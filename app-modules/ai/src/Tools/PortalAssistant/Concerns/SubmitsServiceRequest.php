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

namespace AidingApp\Ai\Tools\PortalAssistant\Concerns;

use AidingApp\Ai\Settings\AiResolutionSettings;
use AidingApp\ServiceManagement\Enums\ServiceRequestUpdateType;
use AidingApp\ServiceManagement\Enums\SystemServiceRequestClassification;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestStatus;
use AidingApp\ServiceManagement\Services\ServiceRequestNumber\Contracts\ServiceRequestNumberGenerator;
use AidingApp\Timeline\Events\TimelineableRecordCreated;
use Carbon\CarbonImmutable;
use Illuminate\Support\Str;

trait SubmitsServiceRequest
{
    /**
     * Submit service request for human review or close if AI resolution accepted
     *
     * @param ServiceRequest $draft
     * @param bool $resolutionAccepted Whether user accepted the AI resolution
     *
     * @return string The generated service request number
     */
    protected function submitServiceRequest(ServiceRequest $draft, bool $resolutionAccepted = false, ?string $resolutionUpdateUuid = null): string
    {
        $hasAiResolution = $draft->serviceRequestUpdates()
            ->where('update_type', ServiceRequestUpdateType::AiResolutionProposed)
            ->exists();

        if ($hasAiResolution) {
            $this->createResolutionUpdates($draft, $resolutionAccepted, $resolutionUpdateUuid);
        }

        // Determine status based on resolution outcome
        if ($resolutionAccepted) {
            // Resolution accepted - close the ticket
            $status = ServiceRequestStatus::query()
                ->where('classification', SystemServiceRequestClassification::Closed)
                ->first();
        } else {
            // Resolution rejected or not attempted - open for human review
            $status = ServiceRequestStatus::query()
                ->where('classification', SystemServiceRequestClassification::Open)
                ->first();
        }

        if ($status) {
            $draft->status()->associate($status);
            $draft->status_updated_at = CarbonImmutable::now();
        }

        // Only generate number if it doesn't exist (prevents observer error on retry)
        if (! $draft->service_request_number) {
            $draft->service_request_number = app(ServiceRequestNumberGenerator::class)->generate();
        }

        $draft->is_draft = false;
        $draft->save();

        // Only assign to team if resolution was not accepted (needs human review)
        if (! $resolutionAccepted) {
            $this->assignServiceRequest($draft);
        }

        return $draft->service_request_number;
    }

    protected function createResolutionUpdates(ServiceRequest $draft, bool $wasAccepted, ?string $resolutionUpdateUuid = null): void
    {
        $aiResolutionUpdate = $draft->serviceRequestUpdates()
            ->where('update_type', ServiceRequestUpdateType::AiResolutionProposed)
            ->first();

        if (! $aiResolutionUpdate) {
            return;
        }

        $confidenceScore = $draft->ai_resolution_confidence_score ?? 0;
        $aiResolutionSettings = app(AiResolutionSettings::class);
        $threshold = $aiResolutionSettings->confidence_threshold;
        $meetsThreshold = $confidenceScore >= $threshold;

        // Only create internal summary if resolution was rejected or not shown
        if ($wasAccepted) {
            return;
        }

        // Extract the proposed answer from the update (remove wrapper text)
        $updateText = $aiResolutionUpdate->update;
        $proposedAnswer = $updateText;

        // If it contains the wrapper text, extract just the answer
        if (str_contains($updateText, 'Did this resolve your issue?')) {
            preg_match('/here is a potential solution:\n\n(.*?)\n\nDid this resolve your issue\?/s', $updateText, $matches);

            if (isset($matches[1])) {
                $proposedAnswer = $matches[1];
            }
        }

        $resolutionUpdate = $draft->serviceRequestUpdates()->createQuietly([
            'id' => $resolutionUpdateUuid ?? ((string) Str::orderedUuid()),
            'update' => "AI Resolution Attempt (Confidence: {$confidenceScore}%)\n\nProposed Answer:\n{$proposedAnswer}\n\nUser indicated this did not resolve their issue.",
            'update_type' => ServiceRequestUpdateType::AiResolutionSummary,
            'internal' => true,
            'created_by_id' => $draft->getKey(),
            'created_by_type' => $draft->getMorphClass(),
        ]);

        TimelineableRecordCreated::dispatch($draft, $resolutionUpdate);
    }

    protected function assignServiceRequest(ServiceRequest $draft): void
    {
        $draft->load('priority.type');

        $assignmentClass = $draft->priority?->type?->assignment_type?->getAssignerClass();

        if ($assignmentClass) {
            $assignmentClass->execute($draft);
        }
    }
}
