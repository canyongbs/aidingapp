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
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Renderless;

class ListServiceRequestTypes extends ListRecords
{
    protected static string $resource = ServiceRequestTypeResource::class;

    protected static string $view = 'service-management::filament.resources.service-request-type-resource.pages.list-service-request-types';

    #[Computed]
    public function hierarchicalData(): array
    {
        $categories = ServiceRequestTypeCategory::query()
            ->with(['children', 'types'])
            ->whereNull('parent_id')
            ->orderBy('sort')
            ->get();

        $uncategorizedTypes = ServiceRequestType::query()
            ->whereNull('category_id')
            ->orderBy('sort')
            ->get();

        return [
            'categories' => $this->formatCategories($categories),
            'uncategorized_types' => $this->formatTypes($uncategorizedTypes),
        ];
    }

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
            ];
        })->toArray();
    }

    protected function formatTypes(Collection $types): array
    {
        return $types->map(function (ServiceRequestType $type) {
            return [
                'id' => $type->id,
                'name' => $type->name,
                'type' => 'type',
                'sort' => $type->sort,
                'category_id' => $type->category_id,
            ];
        })->toArray();
    }

    #[Renderless]
    public function createCategory(?string $parentId, string $name): void
    {
        Validator::validate(['name' => $name], [
            'name' => 'required|string|max:255',
        ]);

        ServiceRequestTypeCategory::create([
            'name' => trim($name),
            'parent_id' => $parentId,
        ]);

        unset($this->hierarchicalData);

        Notification::make()
            ->success()
            ->title('Category created')
            ->send();
    }

    #[Renderless]
    public function createType(?string $categoryId, string $name): void
    {
        Validator::validate(['name' => $name], [
            'name' => 'required|string|max:255',
        ]);

        $type = ServiceRequestType::create([
            'name' => trim($name),
            'category_id' => $categoryId,
        ]);

        $type->priorities()->createMany([
            ['name' => 'High', 'order' => 1],
            ['name' => 'Medium', 'order' => 2],
            ['name' => 'Low', 'order' => 3],
        ]);

        unset($this->hierarchicalData);

        Notification::make()
            ->success()
            ->title('Type created')
            ->send();
    }

    #[Renderless]
    public function updateCategoriesOrder(array $orderedIds, ?string $parentId): void
    {
        if (empty($orderedIds)) {
            return;
        }

        DB::transaction(function () use ($orderedIds, $parentId) {
            foreach ($orderedIds as $index => $categoryId) {
                ServiceRequestTypeCategory::where('id', $categoryId)->update([
                    'parent_id' => $parentId,
                    'sort' => $index + 1,
                ]);
            }
        });

        unset($this->hierarchicalData);
    }

    #[Renderless]
    public function updateTypesOrder(array $orderedIds, ?string $categoryId): void
    {
        if (empty($orderedIds)) {
            return;
        }

        DB::transaction(function () use ($orderedIds, $categoryId) {
            foreach ($orderedIds as $index => $typeId) {
                ServiceRequestType::where('id', $typeId)->update([
                    'category_id' => $categoryId,
                    'sort' => $index + 1,
                ]);
            }
        });

        unset($this->hierarchicalData);
    }
}
