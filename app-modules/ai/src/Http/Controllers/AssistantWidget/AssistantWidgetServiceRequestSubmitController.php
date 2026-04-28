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

use AidingApp\Contact\Models\Contact;
use AidingApp\Portal\Jobs\PersistServiceRequestUpload;
use AidingApp\ServiceManagement\Actions\AssignServiceRequestToTeam;
use AidingApp\ServiceManagement\Actions\ResolveUploadsMediaCollectionForServiceRequest;
use AidingApp\ServiceManagement\Enums\SystemServiceRequestClassification;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestStatus;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use App\Http\Controllers\Controller;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class AssistantWidgetServiceRequestSubmitController extends Controller
{
    public function uploadUrl(Request $request): JsonResponse
    {
        $data = $request->validate([
            'filename' => ['required', 'string'],
        ]);

        $filename = sprintf('%s.%s', Str::uuid(), str($data['filename'])->afterLast('.'));
        $path = "tmp/{$filename}";

        return response()->json([
            'filename' => $filename,
            'path' => $path,
            ...Storage::temporaryUploadUrl($path, now()->addMinute()),
        ]);
    }

    public function store(Request $request, ServiceRequestType $type): JsonResponse
    {
        $contact = auth('contact')->user() ?? $request->user();

        abort_if(! ($contact instanceof Contact), Response::HTTP_UNAUTHORIZED);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'priority_id' => ['required', 'uuid'],
            'attachments' => ['nullable', 'array'],
            'attachments.*.path' => [
                'required_with:attachments',
                'string',
                'regex:/^tmp\/[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}\.[a-zA-Z0-9]+$/i',
            ],
            'attachments.*.original_file_name' => ['required_with:attachments', 'string', 'max:255'],
        ]);

        $priority = $type->priorities()->findOrFail($data['priority_id']);

        $serviceRequestStatus = ServiceRequestStatus::query()
            ->where('classification', SystemServiceRequestClassification::Open)
            ->where('name', 'New')
            ->where('is_system_protected', true)
            ->firstOrFail();

        DB::beginTransaction();

        try {
            $serviceRequest = new ServiceRequest([
                'title' => $data['title'],
                'close_details' => $data['description'],
                'status_id' => $serviceRequestStatus->getKey(),
                'status_updated_at' => CarbonImmutable::now(),
            ]);

            $serviceRequest->respondent()->associate($contact);
            $serviceRequest->priority()->associate($priority);
            $serviceRequest->save();
            $serviceRequest->refresh();

            app(AssignServiceRequestToTeam::class)->execute($serviceRequest);

            $uploadsMediaCollection = app(ResolveUploadsMediaCollectionForServiceRequest::class)();

            Bus::batch(
                array_map(
                    fn (array $file) => new PersistServiceRequestUpload(
                        $serviceRequest,
                        $file['path'],
                        $file['original_file_name'],
                        $uploadsMediaCollection->getName(),
                    ),
                    $data['attachments'] ?? [],
                ),
            )
                ->name("persist-service-request-uploads-{$serviceRequest->getKey()}")
                ->dispatchAfterResponse();

            DB::commit();
        } catch (Throwable $exception) {
            DB::rollBack();

            report($exception);

            return response()->json(
                ['errors' => ['An error occurred while submitting the service request.']],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return response()->json([
            'message' => 'Service request submitted successfully.',
        ]);
    }
}
