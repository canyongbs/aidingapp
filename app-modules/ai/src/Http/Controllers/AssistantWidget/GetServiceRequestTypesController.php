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

namespace AidingApp\Ai\Http\Controllers\AssistantWidget;

use AidingApp\Ai\Settings\AiClarificationSettings;
use AidingApp\Ai\Settings\AiResolutionSettings;
use AidingApp\Contact\Models\Contact;
use AidingApp\ServiceManagement\Actions\ResolveUploadsMediaCollectionForServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use AidingApp\ServiceManagement\Models\ServiceRequestTypeCategory;
use App\Features\ServiceRequestTypeLiveChatSettingsFeature;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GetServiceRequestTypesController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $contact = auth('contact')->user() ?? $request->user();

        abort_if(! ($contact instanceof Contact), Response::HTTP_UNAUTHORIZED);

        $categories = ServiceRequestTypeCategory::query()->orderBy('sort')->get();
        $types = ServiceRequestType::query()
            ->withoutArchived()
            ->with(['priorities' => fn ($query) => $query->orderByDesc('order')])
            ->orderBy('sort')
            ->get();

        $aiClarificationGlobalEnabled = app(AiClarificationSettings::class)->is_enabled;
        $aiResolutionGlobalEnabled = app(AiResolutionSettings::class)->is_enabled;
        $liveChatFeatureEnabled = ServiceRequestTypeLiveChatSettingsFeature::active();

        $categoriesById = [];

        foreach ($categories as $category) {
            $categoriesById[$category->id] = [
                'id' => $category->id,
                'name' => $category->name,
                'sort' => $category->sort,
                'parent_id' => $category->parent_id,
                'children' => [],
                'types' => [],
            ];
        }

        $topLevelTypes = [];

        foreach ($types as $type) {
            $payload = [
                'id' => $type->getKey(),
                'name' => $type->name,
                'description' => $type->description,
                'icon' => $type->icon ? svg($type->icon, 'h-5 w-5')->toHtml() : null,
                'sort' => $type->sort,
                'category_id' => $type->category_id,
                'is_ai_clarification_enabled' => $aiClarificationGlobalEnabled && $type->is_ai_clarification_enabled,
                'is_ai_resolution_enabled' => $aiResolutionGlobalEnabled && $type->is_ai_resolution_enabled,
                'is_live_chat_enabled' => $liveChatFeatureEnabled && $type->is_live_chat_enabled,
                'priorities' => $type->priorities->map(fn ($priority) => [
                    'id' => $priority->getKey(),
                    'name' => $priority->name,
                    'order' => $priority->order,
                ])->values()->all(),
            ];

            if ($type->category_id && isset($categoriesById[$type->category_id])) {
                $categoriesById[$type->category_id]['types'][] = $payload;
            } else {
                $topLevelTypes[] = $payload;
            }
        }

        $topLevelCategories = [];

        foreach ($categoriesById as $id => $category) {
            if ($category['parent_id'] && isset($categoriesById[$category['parent_id']])) {
                $categoriesById[$category['parent_id']]['children'][] = &$categoriesById[$id];
            } else {
                $topLevelCategories[] = &$categoriesById[$id];
            }
        }

        $sortRecursive = function (array &$nodes) use (&$sortRecursive) {
            usort($nodes, fn (array $first, array $second) => ($first['sort'] ?? 0) <=> ($second['sort'] ?? 0));

            foreach ($nodes as &$node) {
                if (! empty($node['types'])) {
                    usort($node['types'], fn (array $first, array $second) => ($first['sort'] ?? 0) <=> ($second['sort'] ?? 0));
                }

                if (! empty($node['children'])) {
                    $sortRecursive($node['children']);
                }
            }
        };

        $sortRecursive($topLevelCategories);

        usort($topLevelTypes, fn (array $first, array $second) => ($first['sort'] ?? 0) <=> ($second['sort'] ?? 0));

        return response()->json([
            'categories' => $topLevelCategories,
            'types' => $topLevelTypes,
            'upload_url' => route('widgets.assistant.api.service-request.upload-url'),
            'store_url_base' => route('widgets.assistant.api.service-request.store', ['type' => '__TYPE__']),
            'form_url_base' => route('widgets.assistant.api.service-request-form', ['type' => '__TYPE__']),
            'generate_question_url_base' => route('widgets.assistant.api.service-request.generate-question', ['type' => '__TYPE__']),
            'evaluate_ai_resolution_url_base' => route('widgets.assistant.api.service-request.evaluate-ai-resolution', ['type' => '__TYPE__']),
            'conversation_eligibility_url_base' => route('widgets.assistant.api.service-request.conversation.eligibility', ['serviceRequest' => '__SR__']),
            'conversation_request_url_base' => route('widgets.assistant.api.service-request.conversation.request', ['serviceRequest' => '__SR__']),
            'accepted_mime_types' => ($uploadsCollection = app(ResolveUploadsMediaCollectionForServiceRequest::class)())->getMimes(),
            'max_file_size_mb' => $uploadsCollection->getMaxFileSizeInMB(),
            'max_files' => $uploadsCollection->getMaxNumberOfFiles(),
        ]);
    }
}
