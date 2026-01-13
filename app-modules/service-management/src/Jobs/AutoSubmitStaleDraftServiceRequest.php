<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

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

namespace AidingApp\ServiceManagement\Jobs;

use AidingApp\ServiceManagement\Enums\SystemServiceRequestClassification;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestStatus;
use AidingApp\ServiceManagement\Services\ServiceRequestNumber\Contracts\ServiceRequestNumberGenerator;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class AutoSubmitStaleDraftServiceRequest implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        protected string $serviceRequestId,
    ) {}

    public function uniqueId(): string
    {
        return $this->serviceRequestId;
    }

    public function handle(): void
    {
        $cutoffTime = now()->subHour();

        $draft = ServiceRequest::query()
            ->withoutGlobalScope('excludeDrafts')
            ->where('is_draft', true)
            ->where('id', $this->serviceRequestId)
            ->with(['serviceRequestFormSubmission', 'priority.type.form.fields'])
            ->first();

        if (! $draft) {
            return;
        }

        if (! $this->isStale($draft, $cutoffTime)) {
            return;
        }

        if (! $this->hasAllRequiredFields($draft)) {
            return;
        }

        $this->submitDraft($draft);
    }

    protected function isStale(ServiceRequest $draft, Carbon $cutoffTime): bool
    {
        // Check service request itself
        if ($draft->updated_at > $cutoffTime) {
            return false;
        }

        // Check form submission
        $submission = $draft->serviceRequestFormSubmission;

        if ($submission && $submission->updated_at > $cutoffTime) {
            return false;
        }

        // Check pivot table (form field submissions)
        if ($submission) {
            $latestFieldUpdate = DB::table('service_request_form_field_submission')
                ->where('service_request_form_submission_id', $submission->id)
                ->max('updated_at');

            if ($latestFieldUpdate && Carbon::parse($latestFieldUpdate) > $cutoffTime) {
                return false;
            }
        }

        // Check service request updates
        $latestUpdateTime = $draft->serviceRequestUpdates()->max('updated_at');

        if ($latestUpdateTime && Carbon::parse($latestUpdateTime) > $cutoffTime) {
            return false;
        }

        return true;
    }

    protected function hasAllRequiredFields(ServiceRequest $draft): bool
    {
        // Check title
        if (empty($draft->title)) {
            return false;
        }

        // Check description
        if (empty($draft->close_details)) {
            return false;
        }

        // Check custom form fields
        $type = $draft->priority?->type;

        if (! $type) {
            return true;
        }

        $form = $type->form;

        if (! $form) {
            return true;
        }

        $requiredFields = $form->fields->where('is_required', true);

        if ($requiredFields->isEmpty()) {
            return true;
        }

        $submission = $draft->serviceRequestFormSubmission;

        if (! $submission) {
            return $requiredFields->isEmpty();
        }

        $filledFieldIds = $submission->fields()
            ->wherePivotNotNull('response')
            ->wherePivot('response', '!=', '')
            ->pluck('service_request_form_fields.id')
            ->toArray();

        foreach ($requiredFields as $field) {
            if (! in_array($field->id, $filledFieldIds)) {
                return false;
            }
        }

        return true;
    }

    protected function submitDraft(ServiceRequest $draft): void
    {
        $status = ServiceRequestStatus::query()
            ->where('classification', SystemServiceRequestClassification::Open)
            ->where('name', 'New')
            ->where('is_system_protected', true)
            ->first();

        if ($status) {
            $draft->status()->associate($status);
            $draft->status_updated_at = CarbonImmutable::now();
        }

        if (! $draft->service_request_number) {
            $draft->service_request_number = app(ServiceRequestNumberGenerator::class)->generate();
        }

        $draft->is_draft = false;
        $draft->save();

        $draft->load('priority.type');
        $assignmentClass = $draft->priority?->type?->assignment_type?->getAssignerClass();

        if ($assignmentClass) {
            $assignmentClass->execute($draft);
        }
    }
}
