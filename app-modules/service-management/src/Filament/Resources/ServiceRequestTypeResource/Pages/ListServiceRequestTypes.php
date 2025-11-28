<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

    Aiding App™ is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Aiding App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    <https://www.canyongbs.com> or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AidingApp\ServiceManagement\Filament\Resources\ServiceRequestTypeResource\Pages;

use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestTypeResource;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use AidingApp\ServiceManagement\Models\ServiceRequestTypeCategory;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Renderless;

/**
 * @property-read array<string, mixed> $hierarchicalData
 */
class ListServiceRequestTypes extends ListRecords
{
    protected static string $resource = ServiceRequestTypeResource::class;

    protected static string $view = 'service-management::filament.resources.service-request-type-resource.pages.list-service-request-types';

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
        $categories = ServiceRequestTypeCategory::query()
            ->with([/** @phpstan-ignore argument.type */
                'children' => function (HasMany $query) {
                    $query->orderBy('sort')
                        ->with([/** @phpstan-ignore argument.type */
                            'types' => fn (HasMany $typeQuery) => $typeQuery->orderBy('sort')->withCount('serviceRequests'),
                            'children' => function (HasMany $childQuery) {
                                $childQuery->orderBy('sort')
                                    ->with([/** @phpstan-ignore argument.type */
                                        'types' => fn (HasMany $typeQuery) => $typeQuery->orderBy('sort')->withCount('serviceRequests'),
                                    ])
                                    ->withCount('descendantServiceRequests');
                            },
                        ])
                        ->withCount('descendantServiceRequests');
                },
                'types' => fn ($query) => $query->orderBy('sort')->withCount('serviceRequests'),
            ])
            ->withCount('descendantServiceRequests')
            ->whereNull('parent_id')
            ->orderBy('sort')
            ->get();

        $uncategorizedTypes = ServiceRequestType::query()
            ->whereNull('category_id')
            ->orderBy('sort')
            ->withCount('serviceRequests')
            ->get();

        return [
            'categories' => $this->formatCategories($categories),
            'uncategorized_types' => $this->formatTypes($uncategorizedTypes),
        ];
    }

    /**
     * @param array<string, mixed> $treeData
     */
    #[Renderless]
    public function saveChanges(array $treeData): void
    {
        Validator::validate(['treeData' => $treeData], [
            'treeData' => 'required|array',
            'treeData.categories' => 'array',
            'treeData.uncategorized_types' => 'array',
            'treeData.new_categories' => 'array',
            'treeData.new_types' => 'array',
            'treeData.updated_categories' => 'array',
            'treeData.updated_types' => 'array',
            'treeData.deleted_categories' => 'array',
            'treeData.deleted_types' => 'array',
        ]);

        $this->assertMaxCategoryDepth($treeData['categories'] ?? []);

        DB::transaction(function () use ($treeData) {
            $newCategoryIds = [];

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
            if (! empty($treeData['new_types'])) {
                foreach ($treeData['new_types'] as $newType) {
                    $categoryId = null;

                    if (! empty($newType['category_id']) && $newType['category_id'] !== 'temp') {
                        $categoryId = $newCategoryIds[$newType['category_id']]
                            ?? $newType['category_id']; // Could be UUID
                    }

                    $type = ServiceRequestType::create([
                        'name' => trim($newType['name']),
                        'category_id' => $categoryId,
                        'sort' => $newType['sort'],
                    ]);

                    $type->priorities()->createMany([
                        ['name' => 'High', 'order' => 1],
                        ['name' => 'Medium', 'order' => 2],
                        ['name' => 'Low', 'order' => 3],
                    ]);
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
                    ServiceRequestType::where('id', $updatedType['id'])->update([
                        'name' => trim($updatedType['name']),
                    ]);
                }
            }

            // Update existing categories
            $this->updateCategoriesRecursive($treeData['categories'] ?? [], null, $newCategoryIds);

            // Update uncategorized types
            if (! empty($treeData['uncategorized_types'])) {
                foreach ($treeData['uncategorized_types'] as $index => $type) {
                    // Update existing types (non-temp IDs)
                    if (! str_starts_with($type['id'], 'temp_')) {
                        ServiceRequestType::where('id', $type['id'])->update([
                            'category_id' => null,
                            'sort' => $index + 1,
                        ]);
                    }
                }
            }

            $this->handleDeletedTypes($treeData['deleted_types'] ?? []);
            $this->handleDeletedCategories($treeData['deleted_categories'] ?? []);
        });

        // Clear the cached hierarchicalData to force refresh
        unset($this->hierarchicalData);

        Notification::make()
            ->success()
            ->title('Changes saved successfully')
            ->send();
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

            $type->forceDelete();
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
        $category->types()->forceDelete();

        foreach ($category->children as $child) {
            $this->deleteCategoryWithDescendants($child);
        }

        $category->forceDelete();
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
                'types' => $this->formatTypes($category->types),
                'descendant_service_requests_count' => $category->descendant_service_requests_count ?? 0,
            ];
        })->toArray();
    }

    /**
     * @param Collection<int, ServiceRequestType> $types
     *
     * @return array<int, array<string, mixed>>
     */
    protected function formatTypes(Collection $types): array
    {
        return $types->map(function (ServiceRequestType $type) {
            return [
                'id' => $type->id,
                'name' => $type->name,
                'type' => 'type',
                'sort' => $type->sort,
                'category_id' => $type->category_id,
                'service_requests_count' => $type->service_requests_count ?? 0,
                'view_url' => ServiceRequestTypeResource::getUrl('view', ['record' => $type]),
            ];
        })->toArray();
    }

    /**
     * @param array<int, array<string, mixed>> $categories
     * @param array<string, string> $newCategoryIds
     */
    protected function updateCategoriesRecursive(array $categories, ?string $parentId, array $newCategoryIds): void
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
                    // Update existing types (non-temp IDs)
                    if (! str_starts_with($type['id'], 'temp_')) {
                        ServiceRequestType::where('id', $type['id'])->update([
                            'category_id' => $categoryId,
                            'sort' => $typeIndex + 1,
                        ]);
                    }
                }
            }

            // Recursively update children
            if (! empty($category['children'])) {
                $this->updateCategoriesRecursive($category['children'], $categoryId, $newCategoryIds);
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
}
