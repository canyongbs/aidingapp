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

namespace AidingApp\ServiceManagement\Notifications\Concerns;

use AidingApp\ServiceManagement\Actions\GenerateServiceRequestTypeEmailTemplateContent;
use AidingApp\ServiceManagement\Actions\GenerateServiceRequestTypeEmailTemplateSubject;
use AidingApp\ServiceManagement\Enums\ServiceRequestTypeEmailTemplateRole;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestResource;
use Illuminate\Support\HtmlString;

trait HandlesServiceRequestTemplateContent
{
    /**
     * @param string|array<string, mixed> $body
     * @param ?ServiceRequestTypeEmailTemplateRole $urlType
     * @param ?string $timezone
     */
    public function getBody($body, ?ServiceRequestTypeEmailTemplateRole $urlType = null, ?string $timezone = ''): HtmlString
    {
        if (is_array($body)) {
            $body = $this->injectButtonUrlIntoTiptapContent($body, $urlType);
        }

        return app(GenerateServiceRequestTypeEmailTemplateContent::class)(
            $body,
            $this->getMergeData($timezone),
            $this->serviceRequest,
            'body',
        );
    }

    /**
     * @param string|array<string, mixed> $subject
     * @param ?string $timezone
     */
    public function getSubject($subject, ?string $timezone = ''): HtmlString
    {
        return app(GenerateServiceRequestTypeEmailTemplateSubject::class)(
            $subject,
            $this->getMergeData($timezone),
            $this->serviceRequest,
            'subject',
        );
    }

    /**
     * @param ?string $timezone
     *
     * @return array<string, string>
     */
    public function getMergeData(?string $timezone = ''): array
    {
        return [
            'contact name' => $this->serviceRequest->respondent->{$this->serviceRequest->respondent::displayNameKey()},
            'service request number' => $this->serviceRequest->service_request_number,
            'created date' => $this->serviceRequest->created_at->setTimeZone($timezone)->format('M j, Y \a\t h:i A (T)'),
            'updated date' => $this->serviceRequest->updated_at->setTimeZone($timezone)->format('M j, Y \a\t h:i A (T)'),
            'assigned staff name' => $this->serviceRequest->assignedTo->user->name ?? 'Unassigned',
            'status' => $this->serviceRequest->status->name,
            'title' => $this->serviceRequest->title,
            'type' => $this->serviceRequest->priority->type->name,
            'description' => $this->serviceRequest->close_details,
        ];
    }

    /**
     * @param array<string, mixed> $content
     * @param ?ServiceRequestTypeEmailTemplateRole $urlType
     *
     * @return array<string, mixed>
     */
    protected function injectButtonUrlIntoTiptapContent(array $content, ?ServiceRequestTypeEmailTemplateRole $urlType = null): array
    {
        if (! isset($content['content']) || ! is_array($content['content'])) {
            return $content;
        }

        $content['content'] = array_map(function ($block) use ($urlType) {
            if (
                $block['type'] === 'tiptapBlock' &&
                ($block['attrs']['type'] ?? null) === 'serviceRequestTypeEmailTemplateButtonBlock'
            ) {
                $block['attrs']['data']['url'] = $urlType == ServiceRequestTypeEmailTemplateRole::Customer ? route('portal.service-request.show', $this->serviceRequest) : ServiceRequestResource::getUrl('view', [
                    'record' => $this->serviceRequest,
                ]);
            }

            if (
                $block['type'] === 'tiptapBlock' &&
                ($block['attrs']['type'] ?? null) === 'surveyResponseEmailTemplateTakeSurveyButtonBlock'
            ) {
                $block['attrs']['data']['url'] = route('feedback.service.request', $this->serviceRequest);
            }

            if (isset($block['content']) && is_array($block['content'])) {
                $block = $this->injectButtonUrlIntoTiptapContent($block);
            }

            return $block;
        }, $content['content']);

        return $content;
    }
}
