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

use AidingApp\Form\Models\SubmissibleField;
use AidingApp\ServiceManagement\Actions\SubmitServiceRequestDraft;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestFormSubmission;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use App\Features\PortalAssistantServiceRequestFeature;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

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
        if (! PortalAssistantServiceRequestFeature::active()) {
            return;
        }

        $draft = $this->findDraft();

        if (! $draft) {
            return;
        }

        if ($this->isMissingRequiredFormFields($draft)) {
            return;
        }

        $this->submitDraft($draft);
    }

    protected function findDraft(): ?ServiceRequest
    {
        return ServiceRequest::query()
            ->withoutGlobalScope('excludeDrafts')
            ->where('is_draft', true)
            ->where('id', $this->serviceRequestId)
            ->with(['serviceRequestFormSubmission', 'priority.type.form.fields'])
            ->first();
    }

    protected function isMissingRequiredFormFields(ServiceRequest $draft): bool
    {
        $type = $draft->priority?->type;

        if (! $type?->form) {
            return false;
        }

        $requiredFields = $this->getRequiredFields($type);

        if ($requiredFields->isEmpty()) {
            return false;
        }

        return ! $this->allRequiredFieldsAreFilled($draft, $requiredFields);
    }

    /**
     * @return Collection<int, SubmissibleField>
     */
    protected function getRequiredFields(ServiceRequestType $type): Collection
    {
        return $type->form->fields->where('is_required', true);
    }

    /**
     * @param Collection<int, SubmissibleField> $requiredFields
     */
    protected function allRequiredFieldsAreFilled(ServiceRequest $draft, Collection $requiredFields): bool
    {
        $submission = $draft->serviceRequestFormSubmission;

        if (! $submission) {
            return false;
        }

        $filledFieldIds = $this->getFilledFieldIds($submission);

        return $requiredFields->every(
            fn ($field) => in_array($field->getKey(), $filledFieldIds)
        );
    }

    /**
     * @return array<string>
     */
    protected function getFilledFieldIds(ServiceRequestFormSubmission $submission): array
    {
        return $submission->fields()
            ->wherePivotNotNull('response')
            ->wherePivot('response', '!=', '')
            ->pluck('service_request_form_fields.id')
            ->toArray();
    }

    protected function submitDraft(ServiceRequest $draft): void
    {
        app(SubmitServiceRequestDraft::class)->execute($draft);
    }
}
