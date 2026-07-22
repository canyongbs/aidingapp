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

namespace AidingApp\Portal\Http\Controllers\KnowledgeManagementPortal;

use AidingApp\Ai\Settings\AiClarificationSettings;
use AidingApp\Form\Actions\GenerateFormKitSchema;
use AidingApp\Portal\Actions\GenerateServiceRequestForm;
use AidingApp\ServiceManagement\Actions\ResolveUploadsMediaCollectionForServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use AidingApp\ServiceManagement\Models\ServiceRequestTypeCategory;
use App\Features\ServiceRequestTypeMultipleCategoriesFeature;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GetServiceRequestFormController extends Controller
{
    public function __invoke(Request $request, ServiceRequestType $type): JsonResponse
    {
        abort_unless($type->isVisibleToContactType(auth('contact')->user()?->type_id), 404);

        $uploadsMediaCollection = app(ResolveUploadsMediaCollectionForServiceRequest::class)();
        $form = app(GenerateServiceRequestForm::class)->execute($type, $uploadsMediaCollection);

        return response()->json([
            'schema' => app(GenerateFormKitSchema::class)($form),
            'category' => $this->breadcrumbCategory($request, $type),
            'number_of_clarifying_questions' => AiClarificationSettings::NUMBER_OF_QUESTIONS,
        ]);
    }

    /**
     * Resolve the category to show in the breadcrumb trail.
     *
     * A type can belong to many categories, so when the feature is active the breadcrumb follows the
     * category the contact navigated under (supplied by the frontend) rather than one derived from
     * the type. This is display-only context and is not persisted with the submitted request.
     *
     * @return array<string, mixed>|null
     */
    private function breadcrumbCategory(Request $request, ServiceRequestType $type): ?array
    {
        if (ServiceRequestTypeMultipleCategoriesFeature::active()) {
            $categoryId = $request->query('category');

            if (! is_string($categoryId) || $categoryId === '') {
                return null;
            }

            // Only follow a category the type actually belongs to, so an arbitrary id in the query
            // string cannot surface an unrelated category's name or ancestry.
            /** @var ServiceRequestTypeCategory|null $category */
            $category = $type->categories()->whereKey($categoryId)->first();

            // Never expose a category the contact is not allowed to see, even if the type is
            // reachable through another (visible) area.
            if (
                $category !== null
                && ! $category->isVisibleToContactType(auth('contact')->user()?->type_id)
            ) {
                return null;
            }
        } else {
            $type->load('category');

            $category = $type->category;
        }

        if ($category === null) {
            return null;
        }

        $ancestors = [];
        $currentCategory = $category;

        while ($currentCategory->parent_id) {
            if (! $currentCategory->relationLoaded('parent')) {
                $currentCategory->load('parent');
            }

            $currentCategory = $currentCategory->parent;

            if ($currentCategory) {
                $ancestors[] = [
                    'id' => $currentCategory->id,
                    'name' => $currentCategory->name,
                    'parent_id' => $currentCategory->parent_id,
                ];
            }
        }

        return [
            'id' => $category->id,
            'name' => $category->name,
            'parent_id' => $category->parent_id,
            'ancestors' => array_reverse($ancestors),
        ];
    }
}
