<?php

namespace AidingApp\ServiceManagement\Notifications\Concerns;

use AidingApp\ServiceManagement\Actions\GenerateServiceRequestTypeEmailTemplateContent;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestResource;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use Illuminate\Support\HtmlString;

trait HandlesServiceRequestTemplateContent
{
    public function getBody($body): HtmlString
    {
        if (is_array($body)) {
            $body = $this->injectButtonUrlIntoTiptapContent($body);
        }

        return app(GenerateServiceRequestTypeEmailTemplateContent::class)(
            $body,
            $this->getMergeData(),
            $this->serviceRequest,
            'body',
        );
    }

    public function getSubject($subject): HtmlString
    {
        return app(GenerateServiceRequestTypeEmailTemplateContent::class)(
            $subject,
            $this->getMergeData(),
            $this->serviceRequest,
            'subject',
        );
    }

    public function getMergeData(): array
    {
        return [
            'service request number' => $this->serviceRequest->service_request_number,
            'created date' => $this->serviceRequest->created_at->format('d-m-Y H:i'),
            'updated date' => $this->serviceRequest->updated_at->format('d-m-Y H:i'),
            'assigned to' => $this->serviceRequest->respondent->full_name,
            'status' => $this->serviceRequest->status->name,
            'title' => $this->serviceRequest->title,
            'type' => $this->serviceRequest->priority->type->name,
            'description' => $this->serviceRequest->close_details,
        ];
    }

    protected function injectButtonUrlIntoTiptapContent(array $content): array
    {
        if (!isset($content['content']) || !is_array($content['content'])) {
            return $content;
        }

        $content['content'] = array_map(function ($block) {
            if ($block['type'] === 'tiptapBlock' &&
                ($block['attrs']['type'] ?? null) === 'serviceRequestTypeEmailTemplateButtonBlock') {
                
                $block['attrs']['data']['url'] = ServiceRequestResource::getUrl('view', [
                    'record' => $this->serviceRequest,
                ]);
            }

            if (isset($block['content']) && is_array($block['content'])) {
                $block = $this->injectButtonUrlIntoTiptapContent($block);
            }

            return $block;
        }, $content['content']);

        return $content;
    }
}