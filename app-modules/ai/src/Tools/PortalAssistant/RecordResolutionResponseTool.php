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
      of the licensor in the software. Any use of the licensor's trademarks is subject
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

namespace AidingApp\Ai\Tools\PortalAssistant;

use AidingApp\Ai\Models\PortalAssistantThread;
use AidingApp\Contact\Models\Contact;
use AidingApp\ServiceManagement\Enums\SystemServiceRequestClassification;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestStatus;
use AidingApp\ServiceManagement\Services\ServiceRequestNumber\Contracts\ServiceRequestNumberGenerator;
use AidingApp\Timeline\Events\TimelineableRecordCreated;
use Carbon\CarbonImmutable;
use Illuminate\Support\Str;
use Prism\Prism\Tool;

class RecordResolutionResponseTool extends Tool
{
    public function __construct(
        protected PortalAssistantThread $thread,
    ) {
        $this
            ->as('record_resolution_response')
            ->for('Records the user\'s response to your resolution attempt. Call this after presenting a resolution and getting explicit yes/no feedback.')
            ->withBooleanParameter('accepted', 'Whether the user said the resolution solved their problem')
            ->using($this);
    }

    public function __invoke(bool $accepted): string
    {
        $draft = $this->thread->draftServiceRequest;

        if (! $draft) {
            return json_encode([
                'success' => false,
                'error' => 'No draft exists.',
            ]);
        }

        if ($draft->workflow_phase !== 'resolution') {
            return json_encode([
                'success' => false,
                'error' => 'Not in resolution phase. Current phase: ' . $draft->workflow_phase,
            ]);
        }

        $aiResolution = $draft->ai_resolution ?? [];
        $aiResolution['user_accepted'] = $accepted;
        $draft->ai_resolution = $aiResolution;
        $draft->is_ai_resolution_attempted = true;
        $draft->is_ai_resolution_successful = $accepted;

        $this->finalize($draft, $accepted);

        $instruction = $accepted
            ? "Tell the user their issue has been marked as resolved. Provide the request number ({$draft->service_request_number}) for their records."
            : "Tell the user their request has been submitted for human review. Provide the request number ({$draft->service_request_number}) and let them know a team member will follow up.";

        return json_encode([
            'success' => true,
            'request_number' => $draft->service_request_number,
            'resolution_accepted' => $accepted,
            'status' => $accepted ? 'resolved' : 'open',
            'instruction' => $instruction,
        ]);
    }

    protected function finalize(ServiceRequest $draft, bool $resolved): void
    {
        $this->createClarifyingQuestionUpdates($draft);
        $this->createResolutionUpdates($draft);

        if ($resolved) {
            $status = ServiceRequestStatus::query()
                ->where('classification', SystemServiceRequestClassification::Closed)
                ->first();
        } else {
            $status = ServiceRequestStatus::query()
                ->where('classification', SystemServiceRequestClassification::Open)
                ->where('name', 'New')
                ->where('is_system_protected', true)
                ->first();
        }

        if ($status) {
            $draft->status()->associate($status);
            $draft->status_updated_at = CarbonImmutable::now();
        }

        $draft->service_request_number = app(ServiceRequestNumberGenerator::class)->generate();
        $draft->is_draft = false;
        $draft->workflow_phase = null;
        $draft->save();

        $this->assignServiceRequest($draft);
    }

    protected function createClarifyingQuestionUpdates(ServiceRequest $draft): void
    {
        $clarifyingQuestions = $draft->clarifying_questions ?? [];
        $contact = $draft->respondent;

        foreach ($clarifyingQuestions as $qa) {
            $question = $qa['question'] ?? '';
            $answer = $qa['answer'] ?? '';

            if (empty($question) || empty($answer)) {
                continue;
            }

            $questionUpdate = $draft->serviceRequestUpdates()->createQuietly([
                'id' => (string) Str::orderedUuid(),
                'update' => $question,
                'internal' => false,
                'created_by_id' => $draft->getKey(),
                'created_by_type' => $draft->getMorphClass(),
            ]);

            TimelineableRecordCreated::dispatch($draft, $questionUpdate);

            if ($contact instanceof Contact) {
                $answerUpdate = $draft->serviceRequestUpdates()->createQuietly([
                    'id' => (string) Str::orderedUuid(),
                    'update' => $answer,
                    'internal' => false,
                    'created_by_id' => $contact->getKey(),
                    'created_by_type' => $contact->getMorphClass(),
                ]);

                TimelineableRecordCreated::dispatch($draft, $answerUpdate);
            }
        }
    }

    protected function createResolutionUpdates(ServiceRequest $draft): void
    {
        $aiResolution = $draft->ai_resolution ?? [];

        if (empty($aiResolution['proposed_answer'])) {
            return;
        }

        $resolutionUpdate = $draft->serviceRequestUpdates()->createQuietly([
            'id' => (string) Str::orderedUuid(),
            'update' => "AI Resolution Attempt (Confidence: {$aiResolution['confidence_score']}%)\n\n{$aiResolution['proposed_answer']}",
            'internal' => true,
            'created_by_id' => $draft->getKey(),
            'created_by_type' => $draft->getMorphClass(),
        ]);

        TimelineableRecordCreated::dispatch($draft, $resolutionUpdate);

        $accepted = $aiResolution['user_accepted'] ?? false;
        $outcomeUpdate = $draft->serviceRequestUpdates()->createQuietly([
            'id' => (string) Str::orderedUuid(),
            'update' => $accepted
                ? 'User confirmed the AI resolution solved their problem.'
                : 'User indicated the AI resolution did not solve their problem.',
            'internal' => true,
            'created_by_id' => $draft->getKey(),
            'created_by_type' => $draft->getMorphClass(),
        ]);

        TimelineableRecordCreated::dispatch($draft, $outcomeUpdate);
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
