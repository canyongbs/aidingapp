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
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestTypes\ServiceRequestTypeResource;
use AidingApp\ServiceManagement\Models\ServiceRequestCategoryType;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use AidingApp\ServiceManagement\Models\ServiceRequestTypeCategory;
use App\Features\ServiceRequestTypeMultipleCategoriesFeature;
use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\ToggleButtons;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Renderless;
use Throwable;

/**
 * @property-read array<string, mixed> $hierarchicalData
 */
class ListServiceRequestTypes extends ListRecords
{
    protected static string $resource = ServiceRequestTypeResource::class;

    protected string $view = 'service-management::filament.resources.service-request-type-resource.pages.list-service-request-types';

    /**
     * Category placements accumulated while walking the tree during a save, keyed by type id then
     * category id, with the per-area sort as the value. Applied in bulk once the whole tree has been
     * walked so a type filed under several categories keeps every membership instead of only the
     * last one written. Only used while the multiple categories feature is active.
     *
     * @var array<string, array<string, int>>
     */
    protected array $pendingTypePlacements = [];

    /**
     * Desired `sort` values for types that end up uncategorised during a save, keyed by type id.
     * Only used while the multiple categories feature is active.
     *
     * @var array<string, int>
     */
    protected array $pendingUncategorizedTypeSorts = [];

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
    public function getHierarchicalData(): array
    {
        $multipleCategoriesEnabled = ServiceRequestTypeMultipleCategoriesFeature::active();

        // Ordering is handled by the `types` relationship itself (by `sort` under the legacy
        // single-category path, by the pivot `sort` under the multiple categories path), so no
        // explicit order is applied here to avoid an ambiguous `sort` column once the pivot exists.
        $loadTypes = fn ($typeQuery) => $typeQuery
            ->withoutArchived()
            ->withCount('serviceRequests')
            ->with('restrictedToContactTypes:id')
            ->when(
                $multipleCategoriesEnabled,
                fn ($query) => $query->with('categories:id'),
            );

        $categories = ServiceRequestTypeCategory::query()
            /** @phpstan-ignore argument.type */
            ->with([
                'restrictedToContactTypes:id',
                /** @phpstan-ignore argument.type */
                'children' => function (HasMany $query) use ($loadTypes) {
                    $query->orderBy('sort')
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
            ->when(
                $multipleCategoriesEnabled,
                fn ($query) => $query->whereDoesntHave('categories'),
                fn ($query) => $query->whereNull('category_id'),
            )
            ->orderBy('sort')
            ->withCount('serviceRequests')
            ->with('restrictedToContactTypes:id')
            ->when(
                $multipleCategoriesEnabled,
                fn ($query) => $query->with('categories:id'),
            )
            ->get();

        return [
            'categories' => $this->formatCategories($categories),
            'uncategorized_types' => $this->formatTypes($uncategorizedTypes, null),
        ];
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
     * Persist the tree changes, returning whether they were saved.
     *
     * Any failure is reported to the user through a notification and results in `false` rather than
     * an exception bubbling to the client, so the front-end never gets stuck in a "saving" state and
     * the user's unsaved work is preserved instead of being wiped by a reload.
     *
     * @param array<string, mixed> $treeData
     */
    public function saveChanges(array $treeData): bool
    {
        try {
            $this->persistChanges($treeData);
        } catch (ValidationException $exception) {
            Notification::make()
                ->danger()
                ->title('Unable to save changes')
                ->body(collect($exception->errors())->flatten()->first() ?? 'Please review your changes and try again.')
                ->send();

            return false;
        } catch (Throwable $exception) {
            report($exception);

            Notification::make()
                ->danger()
                ->title('Unable to save changes')
                ->body('An unexpected error occurred while saving your changes. Your work has not been lost — please try again.')
                ->send();

            return false;
        }

        return true;
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
     * @param array<string, mixed> $treeData
     */
    protected function persistChanges(array $treeData): void
    {
        Validator::validate(['treeData' => $treeData], [
            'treeData' => 'required|array',
            'treeData.categories' => 'array',
            'treeData.uncategorized_types' => 'array',
            'treeData.new_categories' => 'array',
            'treeData.new_types' => 'array',
            'treeData.new_types.*.name' => ['required', 'string', Rule::unique('service_request_types', 'name')->withoutTrashed()->whereNull('archived_at')],
            'treeData.updated_categories' => 'array',
            'treeData.updated_types' => 'array',
            'treeData.deleted_categories' => 'array',
            'treeData.deleted_types' => 'array',
            'treeData.archived_types' => 'array',
            'treeData.restore_types' => 'array',
        ]);

        $this->assertNoDuplicateTypeNames($treeData);

        $this->assertMaxCategoryDepth($treeData['categories'] ?? []);

        DB::transaction(function () use ($treeData) {
            $newCategoryIds = [];

            $this->pendingTypePlacements = [];
            $this->pendingUncategorizedTypeSorts = [];

            $this->handleRestoredTypes($treeData['restore_types'] ?? []);

            if (! empty($treeData['new_categories'])) {
                foreach ($treeData['new_categories'] as $newCategory) {
                    $parentId = null;

                    if (! empty($newCategory['parent_id']) && $newCategory['parent_id'] !== 'temp') {
                        $parentId = $newCategoryIds[$newCategory['parent_id']]
                            ?? $newCategory['parent_id']; // Could be UUID
                    }

                    $category = ServiceRequestTypeCategory::create([
                        'name' => trim($newCategory['name']),
                        'parent_id' => $parentId,
                        'sort' => $newCategory['sort'],
                    ]);
                    $newCategoryIds[$newCategory['temp_id']] = $category->id;
                }
            }

            // Create new types
            $newTypeIds = [];

            if (! empty($treeData['new_types'])) {
                foreach ($treeData['new_types'] as $newType) {
                    // A brand-new type may appear in several areas of the tree. It is only created
                    // once here (keyed by its temp id); every placement is applied from the tree
                    // walk below so it can sit under multiple categories.
                    if (isset($newTypeIds[$newType['temp_id']])) {
                        continue;
                    }

                    $categoryId = null;

                    if (! empty($newType['category_id']) && $newType['category_id'] !== 'temp') {
                        $categoryId = $newCategoryIds[$newType['category_id']]
                            ?? $newType['category_id']; // Could be UUID
                    }

                    $type = ServiceRequestType::create([
                        'name' => trim($newType['name']),
                        'sort' => $newType['sort'],
                        'default_category' => ServiceRequestCategory::Request,
                    ]);

                    $type->priorities()->createMany([
                        ['name' => 'High', 'order' => 1],
                        ['name' => 'Medium', 'order' => 2],
                        ['name' => 'Low', 'order' => 3],
                    ]);

                    $newTypeIds[$newType['temp_id']] = $type->id;

                    // Under the legacy single-category path the placement is written immediately.
                    // Under the multiple categories path it is deferred to the aggregation pass so a
                    // new type can be filed under several areas at once.
                    if (! ServiceRequestTypeMultipleCategoriesFeature::active()) {
                        $this->syncTypeCategory($type, $categoryId, (int) $newType['sort']);
                    }
                }
            }

            // Update renamed categories
            if (! empty($treeData['updated_categories'])) {
                foreach ($treeData['updated_categories'] as $updatedCategory) {
                    ServiceRequestTypeCategory::where('id', $updatedCategory['id'])->update([
                        'name' => trim($updatedCategory['name']),
                    ]);
                }
            }

            // Update renamed types
            if (! empty($treeData['updated_types'])) {
                foreach ($treeData['updated_types'] as $updatedType) {
                    Validator::validate(
                        ['name' => trim($updatedType['name'])],
                        ['name' => ['required', 'string', Rule::unique('service_request_types', 'name')->ignore($updatedType['id'])->withoutTrashed()->whereNull('archived_at')]],
                    );

                    ServiceRequestType::where('id', $updatedType['id'])->update([
                        'name' => trim($updatedType['name']),
                    ]);
                }
            }

            // Update existing categories
            $this->updateCategoriesRecursive($treeData['categories'] ?? [], null, $newCategoryIds, $newTypeIds);

            // Update uncategorized types
            if (! empty($treeData['uncategorized_types'])) {
                foreach ($treeData['uncategorized_types'] as $index => $type) {
                    $typeId = $this->resolveSaveTypeId($type['id'], $newTypeIds);

                    if ($typeId !== null) {
                        $this->assignTypeToCategory($typeId, null, $index + 1);
                    }
                }
            }

            // Apply the accumulated category placements in one pass so multi-category types keep
            // every membership. No-op while the multiple categories feature is inactive.
            $this->applyPendingTypePlacements();

            $this->handleDeletedTypes($treeData['deleted_types'] ?? []);
            $this->handleDeletedCategories($treeData['deleted_categories'] ?? []);
            $this->handleArchivedTypes($treeData['archived_types'] ?? []);
        });

        // Clear the cached hierarchicalData to force refresh
        unset($this->hierarchicalData);

        Notification::make()
            ->success()
            ->title('Changes saved successfully')
            ->send();
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
     * @param array<int, string> $typeIds
     */
    protected function handleDeletedTypes(array $typeIds): void
    {
        if (empty($typeIds)) {
            return;
        }

        $types = ServiceRequestType::query()
            ->whereIn('id', $typeIds)
            ->withCount('serviceRequests')
            ->get();

        foreach ($types as $type) {
            if ($type->service_requests_count > 0) {
                throw ValidationException::withMessages([
                    'treeData.deleted_types' => "Cannot delete type {$type->name} while service requests exist.",
                ]);
            }

            $type->delete();
        }
    }

    /**
     * @param array<int, string> $categoryIds
     */
    protected function handleDeletedCategories(array $categoryIds): void
    {
        if (empty($categoryIds)) {
            return;
        }

        $categories = ServiceRequestTypeCategory::query()
            ->whereIn('id', $categoryIds)
            ->withCount('descendantServiceRequests')
            ->get();

        foreach ($categories as $category) {
            if ($category->descendant_service_requests_count > 0) {
                throw ValidationException::withMessages([
                    'treeData.deleted_categories' => "Cannot delete category {$category->name} while service requests exist in its descendants.",
                ]);
            }

            $this->deleteCategoryWithDescendants($category);
        }
    }

    protected function deleteCategoryWithDescendants(ServiceRequestTypeCategory $category): void
    {
        // While the multiple categories feature is active `types()` is a many-to-many relationship,
        // so the types are only detached from this category (they may live under others). Under the
        // legacy single-category path each type belongs solely to this category and is deleted.
        if (ServiceRequestTypeMultipleCategoriesFeature::active()) {
            $category->types()->detach();
        } else {
            $category->types()->delete();
        }

        foreach ($category->children as $child) {
            $this->deleteCategoryWithDescendants($child);
        }

        $category->delete();
    }

    /**
     * @param array<int, string> $typeIds
     */
    protected function handleRestoredTypes(array $typeIds): void
    {
        if (empty($typeIds)) {
            return;
        }

        $types = ServiceRequestType::query()
            ->onlyArchived()
            ->whereIn('id', $typeIds)
            ->get();

        foreach ($types as $type) {
            $type->unarchive();
        }
    }

    /**
     * @param array<int, string> $typeIds
     */
    protected function handleArchivedTypes(array $typeIds): void
    {
        if (empty($typeIds)) {
            return;
        }

        $types = ServiceRequestType::query()
            ->whereIn('id', $typeIds)
            ->withCount('serviceRequests')
            ->get();

        foreach ($types as $type) {
            if ($type->service_requests_count === 0) {
                throw ValidationException::withMessages([
                    'treeData.archived_types' => "Cannot archive type {$type->name} without service requests. Delete it instead.",
                ]);
            }

            $type->archive();
        }
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
                'category_id' => $contextCategoryId,
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
     * While the multiple categories feature is active a categorised type is ordered by its per-area
     * pivot sort, so the same type can sit in a different position under each area. Uncategorised
     * types (and the legacy single-category path) fall back to the type's global sort.
     */
    protected function resolveTypeSort(ServiceRequestType $type, ?string $contextCategoryId): int
    {
        if ($contextCategoryId !== null && ServiceRequestTypeMultipleCategoriesFeature::active()) {
            $pivot = $type->getRelationValue('pivot');

            assert($pivot instanceof ServiceRequestCategoryType || is_null($pivot));

            if ($pivot !== null) {
                return (int) $pivot->getAttribute('sort');
            }
        }

        return (int) $type->sort;
    }

    /**
     * Resolve the id to persist for a type node in the saved tree.
     *
     * Existing types return their own id. A brand-new type (temp id) is only placed through this
     * path while the multiple categories feature is active — its temp id is mapped to the real id
     * created earlier in the save. Under the legacy single-category path new types are placed at
     * creation time, so they are skipped here (null).
     *
     * @param array<string, string> $newTypeIds
     */
    protected function resolveSaveTypeId(string $treeTypeId, array $newTypeIds): ?string
    {
        if (! str_starts_with($treeTypeId, 'temp_')) {
            return $treeTypeId;
        }

        if (! ServiceRequestTypeMultipleCategoriesFeature::active()) {
            return null;
        }

        return $newTypeIds[$treeTypeId] ?? null;
    }

    /**
     * Record where a service request type sits in the tree and its sort order within that spot.
     *
     * While the multiple categories feature is active the placement is accumulated and applied in
     * bulk by {@see applyPendingTypePlacements()} so a type filed under several categories keeps
     * every membership. Otherwise the legacy single `category_id` column is written immediately.
     */
    protected function assignTypeToCategory(string $typeId, ?string $categoryId, int $sort): void
    {
        if (ServiceRequestTypeMultipleCategoriesFeature::active()) {
            $this->pendingTypePlacements[$typeId] ??= [];

            if ($categoryId !== null) {
                $this->pendingTypePlacements[$typeId][$categoryId] = $sort;

                return;
            }

            $this->pendingUncategorizedTypeSorts[$typeId] = $sort;

            return;
        }

        $type = ServiceRequestType::find($typeId);

        if ($type === null) {
            return;
        }

        $type->sort = $sort;
        $type->save();

        $this->syncTypeCategory($type, $categoryId);
    }

    /**
     * Apply every category placement accumulated during the tree walk in a single pass.
     *
     * Each type is synced to the full set of categories it appears under, deduplicated by category
     * (the pivot's unique constraint is the backstop), with the per-area sort stored on the pivot.
     * Types that end up without any category are detached and keep their global `sort`. No-op while
     * the multiple categories feature is inactive.
     */
    protected function applyPendingTypePlacements(): void
    {
        if (! ServiceRequestTypeMultipleCategoriesFeature::active()) {
            return;
        }

        $typeIds = array_unique([
            ...array_keys($this->pendingTypePlacements),
            ...array_keys($this->pendingUncategorizedTypeSorts),
        ]);

        foreach ($typeIds as $typeId) {
            $type = ServiceRequestType::find($typeId);

            if ($type === null) {
                continue;
            }

            $placements = $this->pendingTypePlacements[$typeId] ?? [];

            if ($placements === []) {
                $sort = $this->pendingUncategorizedTypeSorts[$typeId] ?? $type->sort;

                if ($type->sort !== $sort) {
                    $type->sort = $sort;
                    $type->save();
                }

                $type->categories()->sync([]);

                continue;
            }

            $syncData = [];

            foreach ($placements as $categoryId => $sort) {
                $syncData[$categoryId] = ['sort' => $sort];
            }

            $type->categories()->sync($syncData);
        }
    }

    /**
     * Store the single category a type is filed under (legacy new-type creation path).
     *
     * When the multiple categories feature is active the assignment is stored in the `categories`
     * pivot with its per-area sort; otherwise the legacy `category_id` column is written.
     */
    protected function syncTypeCategory(ServiceRequestType $type, ?string $categoryId, ?int $sort = null): void
    {
        if (ServiceRequestTypeMultipleCategoriesFeature::active()) {
            $type->categories()->sync(
                $categoryId !== null ? [$categoryId => ['sort' => $sort ?? 0]] : [],
            );

            return;
        }

        ServiceRequestType::whereKey($type->getKey())->update(['category_id' => $categoryId]);
    }

    /**
     * @param array<int, array<string, mixed>> $categories
     * @param array<string, string> $newCategoryIds
     * @param array<string, string> $newTypeIds
     */
    protected function updateCategoriesRecursive(array $categories, ?string $parentId, array $newCategoryIds, array $newTypeIds = []): void
    {
        foreach ($categories as $index => $category) {
            $originalCategoryId = $category['id'];
            $categoryId = $originalCategoryId;

            // Handle new categories by mapping temp IDs to real IDs
            if (isset($newCategoryIds[$originalCategoryId])) {
                $categoryId = $newCategoryIds[$originalCategoryId];
            }

            // If this is a new category that hasn't been created yet (no mapping), skip it.
            // Otherwise (mapping exists) fall through and update the created category so
            // moved types get their category_id updated and parent/sort can be set.
            if (str_starts_with($originalCategoryId, 'temp_') && ! isset($newCategoryIds[$originalCategoryId])) {
                // For truly new-but-not-yet-created categories we can't do anything here.
                // Still attempt to recurse into children using the (unknown) category id would be wrong,
                // so skip.
                continue;
            }

            // Update existing or newly-created categories (UUIDs or any non-temp IDs)
            $actualParentId = $parentId;

            // If parent is a new category, map its temp ID to real ID
            if ($actualParentId && isset($newCategoryIds[$actualParentId])) {
                $actualParentId = $newCategoryIds[$actualParentId];
            }

            ServiceRequestTypeCategory::where('id', $categoryId)->update([
                'parent_id' => $actualParentId,
                'sort' => $index + 1,
            ]);

            // Update types in this category
            if (! empty($category['types'])) {
                foreach ($category['types'] as $typeIndex => $type) {
                    $typeId = $this->resolveSaveTypeId($type['id'], $newTypeIds);

                    if ($typeId !== null) {
                        $this->assignTypeToCategory($typeId, $categoryId, $typeIndex + 1);
                    }
                }
            }

            // Recursively update children
            if (! empty($category['children'])) {
                $this->updateCategoriesRecursive($category['children'], $categoryId, $newCategoryIds, $newTypeIds);
            }
        }
    }

    /**
     * @param array<int, array<string, mixed>> $categories
     */
    protected function assertMaxCategoryDepth(array $categories, int $depth = 0): void
    {
        if ($depth > 1) {
            throw ValidationException::withMessages([
                'treeData.categories' => 'Categories may only be nested two levels deep.',
            ]);
        }

        foreach ($categories as $category) {
            $children = $category['children'] ?? [];

            if (! empty($children)) {
                $this->assertMaxCategoryDepth($children, $depth + 1);
            }
        }
    }

    /**
     * Guard against two types being saved with the same name within the same request.
     *
     * The per-row `unique` rule only compares each name against the database, so it cannot catch
     * two brand-new types (or a new type and a rename) sharing a name in the same payload. Names
     * must be unique across every area, so any collision here is rejected before writing.
     *
     * @param array<string, mixed> $treeData
     */
    protected function assertNoDuplicateTypeNames(array $treeData): void
    {
        $names = [];

        // A brand-new type may be placed in several areas, appearing once per placement under the
        // same temp id; count it a single time.
        $seenTempIds = [];

        foreach ($treeData['new_types'] ?? [] as $newType) {
            $tempId = $newType['temp_id'] ?? null;

            if ($tempId !== null && isset($seenTempIds[$tempId])) {
                continue;
            }

            $seenTempIds[$tempId] = true;
            $names[] = mb_strtolower(trim((string) ($newType['name'] ?? '')));
        }

        $seenIds = [];

        foreach ($treeData['updated_types'] ?? [] as $updatedType) {
            $id = $updatedType['id'] ?? null;

            if ($id !== null && isset($seenIds[$id])) {
                continue;
            }

            $seenIds[$id] = true;
            $names[] = mb_strtolower(trim((string) ($updatedType['name'] ?? '')));
        }

        $names = array_filter($names, fn (string $name): bool => $name !== '');

        $hasDuplicates = count($names) !== count(array_unique($names));

        if ($hasDuplicates) {
            throw ValidationException::withMessages([
                'treeData.new_types' => 'The same name is already in use by another service request type. Please select a different name.',
            ]);
        }
    }
}
