<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Aiding App® is licensed under the Elastic License 2.0. For more details,
    see <https://github.com/canyongbs/aidingapp/blob/main/LICENSE.>

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Aiding App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    <https://www.canyongbs.com> or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AidingApp\ServiceManagement\Filament\Resources\ServiceRequestTypes\Pages;

use AidingApp\Contact\Models\ContactType;
use AidingApp\ServiceManagement\Enums\ServiceRequestCategory;
use AidingApp\ServiceManagement\Events\ServiceRequestTypeCatalogUpdated;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestTypes\ServiceRequestTypeResource;
use AidingApp\ServiceManagement\Models\ServiceRequestCategoryType;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use AidingApp\ServiceManagement\Models\ServiceRequestTypeCategory;
use App\Models\Tenant;
use Closure;
use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\ToggleButtons;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Renderless;
use Throwable;

/**
 * The service catalog builder. Every change is saved as soon as it is made, and other users viewing
 * the builder are told over a websocket to reload the tree.
 *
 * @property-read array<string, mixed> $hierarchicalData
 */
class ListServiceRequestTypes extends ListRecords
{
    /** Arbitrary PostgreSQL advisory lock key; locks are per database, so per tenant. */
    protected const CATALOG_LOCK_KEY = 7_418_211_903;

    protected static string $resource = ServiceRequestTypeResource::class;

    protected string $view = 'service-management::filament.resources.service-request-type-resource.pages.list-service-request-types';

    #[Computed]
    public function canEdit(): bool
    {
        return auth()->user()->can('updateAny', ServiceRequestType::class);
    }

    /**
     * @return array<string, mixed>
     */
    #[Computed]
    public function hierarchicalData(): array
    {
        return $this->getHierarchicalData();
    }

    /**
     * @return array<string, mixed>
     */
    #[Renderless]
    public function getHierarchicalData(): array
    {
        // Ordering is handled by the `types` relationship itself through the pivot `sort`, so no
        // explicit order is applied here to avoid an ambiguous `sort` column once the pivot exists.
        $loadTypes = fn ($typeQuery) => $typeQuery
            ->withoutArchived()
            ->withCount('serviceRequests')
            ->with('restrictedToContactTypes:id')
            ->with('categories:id');

        $categories = ServiceRequestTypeCategory::query()
            /** @phpstan-ignore argument.type */
            ->with([
                'restrictedToContactTypes:id',
                /** @phpstan-ignore argument.type */
                'children' => function (HasMany $query) use ($loadTypes) {
                    $query->orderBy('sort')
                        // @phpstan-ignore argument.type
                        ->with([
                            'restrictedToContactTypes:id',
                            'types' => $loadTypes,
                            'children' => function (HasMany $childQuery) use ($loadTypes) {
                                $childQuery->orderBy('sort')
                                    ->with([
                                        'restrictedToContactTypes:id',
                                        'types' => $loadTypes,
                                    ])
                                    ->withCount('descendantServiceRequests');
                            },
                        ])
                        ->withCount('descendantServiceRequests');
                },
                'types' => $loadTypes,
            ])
            ->withCount('descendantServiceRequests')
            ->whereNull('parent_id')
            ->orderBy('sort')
            ->get();

        $uncategorizedTypes = ServiceRequestType::query()
            ->withoutArchived()
            ->whereDoesntHave('categories')
            ->orderBy('sort')
            ->withCount('serviceRequests')
            ->with('restrictedToContactTypes:id')
            ->with('categories:id')
            ->get();

        return [
            'categories' => $this->formatCategories($categories),
            'uncategorized_types' => $this->formatTypes($uncategorizedTypes, null),
        ];
    }

    public function getCatalogChannelName(): string
    {
        return ServiceRequestTypeCatalogUpdated::channelName($this->getCurrentTenantId());
    }

    public function manageVisibilityAction(): Action
    {
        return Action::make('manageVisibility')
            ->modalHeading('Visibility Settings')
            ->modalDescription('Restrict which contact types can view and submit this in the portal and assistant widget. Users of the admin panel are unaffected and can always submit service requests on behalf of anyone.')
            ->slideOver()
            ->authorize(fn (): bool => $this->canEdit())
            ->fillForm(function (array $arguments): array {
                $node = $this->resolveVisibilityNode($arguments);

                return [
                    'is_visibility_restricted' => (bool) $node->is_visibility_restricted,
                    'contact_type_ids' => $node->restrictedToContactTypes()->pluck('contact_types.id')->all(),
                ];
            })
            ->schema([
                Checkbox::make('is_visibility_restricted')
                    ->label('Restrict Visibility')
                    ->live()
                    ->helperText('When enabled, only the selected contact types can view and submit this in the portal and assistant widget.'),
                ToggleButtons::make('contact_type_ids')
                    ->label('Visible to contact types')
                    ->multiple()
                    ->inline()
                    ->options(fn (): array => ContactType::query()->orderBy('name')->pluck('name', 'id')->all())
                    ->visible(fn (Get $get): bool => (bool) $get('is_visibility_restricted'))
                    ->required(fn (Get $get): bool => (bool) $get('is_visibility_restricted')),
            ])
            ->action(function (array $data, array $arguments): void {
                $node = $this->resolveVisibilityNode($arguments);

                $isRestricted = (bool) ($data['is_visibility_restricted'] ?? false);
                $contactTypeIds = $isRestricted ? array_values($data['contact_type_ids'] ?? []) : [];

                DB::transaction(function () use ($node, $isRestricted, $contactTypeIds) {
                    $node->is_visibility_restricted = $isRestricted;
                    $node->save();
                    $node->restrictedToContactTypes()->sync($contactTypeIds);
                });

                $this->broadcastCatalogUpdate();

                unset($this->hierarchicalData);

                $this->dispatch(
                    'service-request-type-visibility-updated',
                    nodeType: $arguments['nodeType'],
                    nodeId: $arguments['nodeId'],
                    isRestricted: $isRestricted,
                    contactTypeIds: $contactTypeIds,
                );

                Notification::make()
                    ->success()
                    ->title('Visibility settings saved')
                    ->send();
            });
    }

    /**
     * @return array{saved: bool, tree: array<string, mixed>}
     */
    #[Renderless]
    public function createCategory(string $name, ?string $parentId = null): array
    {
        return $this->mutateCatalog(function () use ($name, $parentId): void {
            $name = $this->validateCategoryName($name);
            $parent = filled($parentId) ? $this->findCategory($parentId) : null;

            if ($parent?->parent_id !== null) {
                $this->throwMaxDepthExceeded();
            }

            $category = new ServiceRequestTypeCategory();
            $category->name = $name;
            $category->parent()->associate($parent);
            $category->save();
        });
    }

    /**
     * @return array{saved: bool, tree: array<string, mixed>}
     */
    #[Renderless]
    public function renameCategory(string $categoryId, string $name): array
    {
        return $this->mutateCatalog(function () use ($categoryId, $name): void {
            $name = $this->validateCategoryName($name);

            $category = $this->findCategory($categoryId);
            $category->name = $name;
            $category->save();
        });
    }

    /**
     * Move an area under a new parent (or to the root), then order its new siblings.
     *
     * @param array<int, mixed> $orderedSiblingIds
     *
     * @return array{saved: bool, tree: array<string, mixed>}
     */
    #[Renderless]
    public function moveCategory(string $categoryId, ?string $parentId, array $orderedSiblingIds): array
    {
        return $this->mutateCatalog(function () use ($categoryId, $parentId, $orderedSiblingIds): void {
            $category = $this->findCategory($categoryId);
            $parent = filled($parentId) ? $this->findCategory($parentId) : null;

            if (
                ($parent !== null)
                && ($parent->is($category) || ($parent->parent_id !== null) || $category->children()->exists())
            ) {
                $this->throwMaxDepthExceeded();
            }

            $category->parent()->associate($parent);
            $category->save();

            $this->reorderCategories($parent, $orderedSiblingIds);
        });
    }

    /**
     * Delete an area and its child areas. Types filed only within them are deleted; types that are
     * also filed elsewhere are just removed from them.
     *
     * @return array{saved: bool, tree: array<string, mixed>}
     */
    #[Renderless]
    public function deleteCategory(string $categoryId): array
    {
        return $this->mutateCatalog(function () use ($categoryId): void {
            $categoryIds = $this->collectCategoryTreeIds($this->findCategory($categoryId));

            $categories = ServiceRequestTypeCategory::query()
                ->whereKey($categoryIds)
                ->withCount('descendantServiceRequests')
                ->get();

            foreach ($categories as $category) {
                if ($category->descendant_service_requests_count > 0) {
                    throw ValidationException::withMessages([
                        'categoryId' => "Cannot delete category {$category->name} while service requests exist in its descendants.",
                    ]);
                }
            }

            $types = ServiceRequestType::query()
                ->whereHas('categories', fn (Builder $query) => $query->whereKey($categoryIds))
                ->with('categories:id')
                ->get();

            foreach ($types as $type) {
                if (array_diff($type->categories->modelKeys(), $categoryIds) === []) {
                    $type->delete();
                }
            }

            foreach ($categories as $category) {
                $category->types()->detach();
                $category->delete();
            }
        });
    }

    /**
     * @return array{saved: bool, tree: array<string, mixed>}
     */
    #[Renderless]
    public function createType(string $name, ?string $categoryId = null): array
    {
        return $this->mutateCatalog(function () use ($name, $categoryId): void {
            $name = $this->validateTypeName($name);
            $category = filled($categoryId) ? $this->findCategory($categoryId) : null;

            $type = new ServiceRequestType();
            $type->name = $name;
            $type->default_category = ServiceRequestCategory::Request;
            $type->save();

            $type->priorities()->createMany([
                ['name' => 'High', 'order' => 1],
                ['name' => 'Medium', 'order' => 2],
                ['name' => 'Low', 'order' => 3],
            ]);

            if ($category !== null) {
                $this->appendTypeToCategory($type, $category);
            }
        });
    }

    /**
     * Unarchive a type, placing it only in the given area (or uncategorized).
     *
     * @return array{saved: bool, tree: array<string, mixed>}
     */
    #[Renderless]
    public function restoreArchivedType(string $typeId, ?string $categoryId = null): array
    {
        return $this->mutateCatalog(function () use ($typeId, $categoryId): void {
            $type = $this->findType($typeId, isArchived: true);
            $category = filled($categoryId) ? $this->findCategory($categoryId) : null;

            $type->unarchive();
            $type->categories()->detach();

            if ($category !== null) {
                $this->appendTypeToCategory($type, $category);

                return;
            }

            $type->sort = $this->getNextUncategorizedTypeSort();
            $type->save();
        });
    }

    /**
     * @return array{saved: bool, tree: array<string, mixed>}
     */
    #[Renderless]
    public function renameType(string $typeId, string $name): array
    {
        return $this->mutateCatalog(function () use ($typeId, $name): void {
            $type = $this->findType($typeId);

            $type->name = $this->validateTypeName($name, ignoreTypeId: $type->getKey());
            $type->save();
        });
    }

    /**
     * Move one placement of a type from an area (or uncategorized) to another, then order the types
     * in its destination.
     *
     * @param array<int, mixed> $orderedTypeIds
     *
     * @return array{saved: bool, tree: array<string, mixed>}
     */
    #[Renderless]
    public function moveType(string $typeId, ?string $fromCategoryId, ?string $toCategoryId, array $orderedTypeIds): array
    {
        return $this->mutateCatalog(function () use ($typeId, $fromCategoryId, $toCategoryId, $orderedTypeIds): void {
            $type = $this->findType($typeId);
            $from = filled($fromCategoryId) ? $this->findCategory($fromCategoryId) : null;
            $to = filled($toCategoryId) ? $this->findCategory($toCategoryId) : null;

            if ($to === null) {
                $isFiledElsewhere = $type->categories()
                    ->pluck('service_request_type_categories.id')
                    ->contains(fn (string $id): bool => $id !== $from?->getKey());

                if ($isFiledElsewhere) {
                    throw ValidationException::withMessages([
                        'toCategoryId' => 'A type filed under several areas cannot be uncategorized.',
                    ]);
                }

                $type->categories()->detach();

                $this->reorderUncategorizedTypes($orderedTypeIds);

                return;
            }

            if (! $to->is($from)) {
                if ($from !== null) {
                    $type->categories()->detach($from);
                }

                if (! $type->categories()->whereKey($to->getKey())->exists()) {
                    $this->appendTypeToCategory($type, $to);
                }
            }

            $this->reorderCategoryTypes($to, $orderedTypeIds);
        });
    }

    /**
     * @return array{saved: bool, tree: array<string, mixed>}
     */
    #[Renderless]
    public function addTypeToCategory(string $typeId, string $categoryId): array
    {
        return $this->mutateCatalog(function () use ($typeId, $categoryId): void {
            $type = $this->findType($typeId);
            $category = $this->findCategory($categoryId);

            if ($type->categories()->whereKey($category->getKey())->exists()) {
                return;
            }

            $this->appendTypeToCategory($type, $category);
        });
    }

    /**
     * @return array{saved: bool, tree: array<string, mixed>}
     */
    #[Renderless]
    public function removeTypeFromCategory(string $typeId, string $categoryId): array
    {
        return $this->mutateCatalog(function () use ($typeId, $categoryId): void {
            $type = $this->findType($typeId);
            $category = $this->findCategory($categoryId);

            if ($type->categories()->whereKeyNot($category->getKey())->doesntExist()) {
                throw ValidationException::withMessages([
                    'categoryId' => "Cannot remove type {$type->name} from its only area. Delete or archive it instead.",
                ]);
            }

            $type->categories()->detach($category);
        });
    }

    /**
     * @return array{saved: bool, tree: array<string, mixed>}
     */
    #[Renderless]
    public function deleteType(string $typeId): array
    {
        return $this->mutateCatalog(function () use ($typeId): void {
            $type = $this->findType($typeId);

            if ($type->serviceRequests()->exists()) {
                throw ValidationException::withMessages([
                    'typeId' => "Cannot delete type {$type->name} while service requests exist.",
                ]);
            }

            $type->delete();
        });
    }

    /**
     * @return array{saved: bool, tree: array<string, mixed>}
     */
    #[Renderless]
    public function archiveType(string $typeId): array
    {
        return $this->mutateCatalog(function () use ($typeId): void {
            $type = $this->findType($typeId);

            if ($type->serviceRequests()->doesntExist()) {
                throw ValidationException::withMessages([
                    'typeId' => "Cannot archive type {$type->name} without service requests. Delete it instead.",
                ]);
            }

            $type->archive();
        });
    }

    /**
     * @return array<string, mixed>|null
     */
    #[Renderless]
    public function checkArchivedTypeName(string $name): ?array
    {
        $type = ServiceRequestType::query()
            ->onlyArchived()
            ->where('name', trim($name))
            ->withCount('serviceRequests')
            ->first();

        if (! $type) {
            return null;
        }

        return [
            'id' => $type->id,
            'name' => $type->name,
            'service_requests_count' => $type->service_requests_count,
            'view_url' => ServiceRequestTypeResource::getUrl('view', ['record' => $type]),
        ];
    }

    /**
     * Save a single builder change, tell other viewers about it, and return the fresh tree.
     *
     * Failures are reported through a notification rather than an exception, and the fresh tree is
     * still returned so the client can drop its optimistic change and show what is really saved.
     *
     * @param Closure(): void $operation
     *
     * @return array{saved: bool, tree: array<string, mixed>}
     */
    protected function mutateCatalog(Closure $operation): array
    {
        Gate::authorize('updateAny', ServiceRequestType::class);

        try {
            DB::transaction(function () use ($operation): void {
                // Serialise builder changes so concurrent checks (such as nesting depth) cannot both pass.
                DB::select('select pg_advisory_xact_lock(?)', [static::CATALOG_LOCK_KEY]);

                $operation();
            });
        } catch (ValidationException $exception) {
            $this->notifyUnableToSave(collect($exception->errors())->flatten()->first() ?? 'Please review your changes and try again.');

            return ['saved' => false, 'tree' => $this->getHierarchicalData()];
        } catch (UniqueConstraintViolationException) {
            $this->notifyUnableToSave('The same name is already in use by another service request type. Please select a different name.');

            return ['saved' => false, 'tree' => $this->getHierarchicalData()];
        } catch (Throwable $exception) {
            report($exception);

            $this->notifyUnableToSave('An unexpected error occurred while saving your change. Please try again.');

            return ['saved' => false, 'tree' => $this->getHierarchicalData()];
        }

        $this->broadcastCatalogUpdate();

        return ['saved' => true, 'tree' => $this->getHierarchicalData()];
    }

    protected function notifyUnableToSave(string $message): void
    {
        Notification::make()
            ->danger()
            ->title('Unable to save changes')
            ->body($message)
            ->send();
    }

    protected function broadcastCatalogUpdate(): void
    {
        $tenantId = $this->getCurrentTenantId();

        // Ably rejects `toOthers()`, so the sender's socket ID is passed along for its client to skip.
        $originSocketId = Broadcast::socket(request());

        // The change is already saved, so a websocket outage must not report it as failed.
        rescue(function () use ($tenantId, $originSocketId): void {
            broadcast(new ServiceRequestTypeCatalogUpdated($tenantId, $originSocketId));
        });
    }

    protected function getCurrentTenantId(): string
    {
        $tenant = Tenant::current();

        assert($tenant instanceof Tenant);

        return $tenant->getKey();
    }

    protected function findCategory(string $categoryId): ServiceRequestTypeCategory
    {
        $category = Str::isUuid($categoryId)
            ? ServiceRequestTypeCategory::query()->find($categoryId)
            : null;

        if (! $category instanceof ServiceRequestTypeCategory) {
            throw ValidationException::withMessages([
                'categoryId' => 'This service request area no longer exists.',
            ]);
        }

        return $category;
    }

    protected function findType(string $typeId, bool $isArchived = false): ServiceRequestType
    {
        $type = Str::isUuid($typeId)
            ? ServiceRequestType::query()
                ->when(
                    $isArchived,
                    fn (Builder $query) => $query->onlyArchived(),
                    fn (Builder $query) => $query->withoutArchived(),
                )
                ->find($typeId)
            : null;

        if (! $type instanceof ServiceRequestType) {
            throw ValidationException::withMessages([
                'typeId' => 'This service request type no longer exists.',
            ]);
        }

        return $type;
    }

    protected function validateCategoryName(string $name): string
    {
        $name = trim($name);

        Validator::validate(['name' => $name], ['name' => ['required', 'string', 'max:255']]);

        return $name;
    }

    protected function validateTypeName(string $name, ?string $ignoreTypeId = null): string
    {
        $name = trim($name);

        Validator::validate(
            ['name' => $name],
            ['name' => [
                'required',
                'string',
                'max:255',
                Rule::unique(ServiceRequestType::class, 'name')->ignore($ignoreTypeId)->withoutTrashed()->whereNull('archived_at'),
            ]],
            ['name.unique' => 'The same name is already in use by another service request type. Please select a different name.'],
        );

        return $name;
    }

    protected function throwMaxDepthExceeded(): never
    {
        throw ValidationException::withMessages([
            'parentId' => 'Categories may only be nested two levels deep.',
        ]);
    }

    protected function appendTypeToCategory(ServiceRequestType $type, ServiceRequestTypeCategory $category): void
    {
        $sort = (int) $category->types()->newPivotQuery()->max('sort') + 1;

        $type->categories()->attach($category, ['sort' => $sort]);
    }

    protected function getNextUncategorizedTypeSort(): int
    {
        return (int) ServiceRequestType::query()
            ->withoutArchived()
            ->whereDoesntHave('categories')
            ->max('sort') + 1;
    }

    /**
     * @param array<int, mixed> $orderedIds
     */
    protected function reorderCategories(?ServiceRequestTypeCategory $parent, array $orderedIds): void
    {
        $currentIds = ServiceRequestTypeCategory::query()
            ->where('parent_id', $parent?->getKey())
            ->orderBy('sort')
            ->pluck('id');

        foreach ($this->resolveOrder($orderedIds, $currentIds) as $index => $categoryId) {
            ServiceRequestTypeCategory::query()->whereKey($categoryId)->update(['sort' => $index + 1]);
        }
    }

    /**
     * @param array<int, mixed> $orderedIds
     */
    protected function reorderCategoryTypes(ServiceRequestTypeCategory $category, array $orderedIds): void
    {
        $currentIds = $category->types()->pluck('service_request_types.id');

        foreach ($this->resolveOrder($orderedIds, $currentIds) as $index => $typeId) {
            $category->types()->updateExistingPivot($typeId, ['sort' => $index + 1]);
        }
    }

    /**
     * @param array<int, mixed> $orderedIds
     */
    protected function reorderUncategorizedTypes(array $orderedIds): void
    {
        $currentIds = ServiceRequestType::query()
            ->withoutArchived()
            ->whereDoesntHave('categories')
            ->orderBy('sort')
            ->pluck('id');

        foreach ($this->resolveOrder($orderedIds, $currentIds) as $index => $typeId) {
            ServiceRequestType::query()->whereKey($typeId)->update(['sort' => $index + 1]);
        }
    }

    /**
     * Order a container's current items: the requested ids first (ignoring any that are no longer in
     * it, such as items another user just moved away), then any remaining items in their current order.
     *
     * @param array<int, mixed> $requestedIds
     * @param Collection<int, string> $currentIds
     *
     * @return array<int, string>
     */
    protected function resolveOrder(array $requestedIds, Collection $currentIds): array
    {
        $requestedIds = collect($requestedIds)
            ->filter(fn (mixed $id): bool => is_string($id))
            ->intersect($currentIds)
            ->unique()
            ->values();

        return $requestedIds->merge($currentIds->diff($requestedIds))->values()->all();
    }

    /**
     * @return array<int, string>
     */
    protected function collectCategoryTreeIds(ServiceRequestTypeCategory $category): array
    {
        return [
            $category->getKey(),
            ...$category->children->flatMap(fn (ServiceRequestTypeCategory $child): array => $this->collectCategoryTreeIds($child))->all(),
        ];
    }

    /**
     * @param array<string, mixed> $arguments
     */
    protected function resolveVisibilityNode(array $arguments): ServiceRequestType | ServiceRequestTypeCategory
    {
        $nodeId = $arguments['nodeId'] ?? null;

        return match ($arguments['nodeType'] ?? null) {
            'type' => ServiceRequestType::query()->findOrFail($nodeId),
            'category' => ServiceRequestTypeCategory::query()->findOrFail($nodeId),
            default => abort(404),
        };
    }

    /**
     * @param Collection<int, ServiceRequestTypeCategory> $categories
     *
     * @return array<int, array<string, mixed>>
     */
    protected function formatCategories(Collection $categories): array
    {
        return $categories->map(function (ServiceRequestTypeCategory $category) {
            return [
                'id' => $category->id,
                'name' => $category->name,
                'type' => 'category',
                'sort' => $category->sort,
                'parent_id' => $category->parent_id,
                'children' => $this->formatCategories($category->children),
                'types' => $this->formatTypes($category->types, $category->id),
                'descendant_service_requests_count' => $category->descendant_service_requests_count ?? 0,
                'is_visibility_restricted' => (bool) $category->is_visibility_restricted,
                'restricted_to_contact_type_ids' => $category->restrictedToContactTypes->pluck('id')->all(),
            ];
        })->toArray();
    }

    /**
     * @param Collection<int, ServiceRequestType> $types
     *
     * @return array<int, array<string, mixed>>
     */
    protected function formatTypes(Collection $types, ?string $contextCategoryId): array
    {
        return $types->map(function (ServiceRequestType $type) use ($contextCategoryId) {
            return [
                'id' => $type->id,
                'name' => $type->name,
                'type' => 'type',
                'sort' => $this->resolveTypeSort($type, $contextCategoryId),
                'service_requests_count' => $type->service_requests_count ?? 0,
                'view_url' => ServiceRequestTypeResource::getUrl('view', ['record' => $type]),
                'is_visibility_restricted' => (bool) $type->is_visibility_restricted,
                'restricted_to_contact_type_ids' => $type->restrictedToContactTypes->pluck('id')->all(),
            ];
        })->toArray();
    }

    /**
     * Resolve the sort to emit for a type node.
     *
     * A categorised type is ordered by its per-area pivot sort, so the same type can sit in a
     * different position under each area. Uncategorised types fall back to the type's global sort.
     */
    protected function resolveTypeSort(ServiceRequestType $type, ?string $contextCategoryId): int
    {
        if ($contextCategoryId !== null) {
            $pivot = $type->getRelationValue('pivot');

            assert($pivot instanceof ServiceRequestCategoryType || is_null($pivot));

            if ($pivot !== null) {
                return (int) $pivot->getAttribute('sort');
            }
        }

        return (int) $type->sort;
    }
}
