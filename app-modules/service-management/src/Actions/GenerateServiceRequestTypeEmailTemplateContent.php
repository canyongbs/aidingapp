<?php

namespace AidingApp\ServiceManagement\Actions;

use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestResource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;

class GenerateServiceRequestTypeEmailTemplateContent
{
    public function __invoke(string|array $content, array $mergeData, Model $record, string $recordAttribute): HtmlString
    {
        $content = tiptap_converter()
            ->mergeTagsMap($mergeData)
            ->record($record, $recordAttribute) 
            ->asHTML($content);

        return str($content)->sanitizeHtml()->toHtmlString();

        // $html = tiptap_converter()
        //     ->mergeTagsMap($mergeData)
        //     ->record($record, $recordAttribute)
        //     ->asHTML($content);

        // \Log::info('HTML Content Before Button Insertion: ' . $html);

        // if (str_contains($html, '[serviceRequestTypeEmailTemplateButtonBlock]')) {
        //     $url = ServiceRequestResource::getUrl('view', ['record' => $record]);
        //     $buttonHtml = <<<HTML
        //     <div style="text-align: right; margin-top: 20px;">
        //         <a href="{$url}" style="display: inline-block; padding: 10px 20px; background-color: #3b82f6; color: white; text-decoration: none; border-radius: 6px; font-weight: bold; text-align: center;" target="_blank">
        //             View Service Request
        //         </a>
        //     </div>
        //     HTML;


        //     \Log::info('Button HTML: ' . $buttonHtml);
        //     $html = str_replace('[serviceRequestTypeEmailTemplateButtonBlock]', $buttonHtml, $html);
        // }

        // \Log::info('Final HTML Content: ' . $html);

        // return str($html)
        //     ->sanitizeHtml([
        //         'allowed_tags' => ['a', 'div', 'span', 'br', 'p', 'strong', 'em', 'b', 'i', 'u'],
        //         'allowed_attributes' => ['href', 'style', 'target', 'rel'],
        //     ])
        //     ->toHtmlString();

    }
}