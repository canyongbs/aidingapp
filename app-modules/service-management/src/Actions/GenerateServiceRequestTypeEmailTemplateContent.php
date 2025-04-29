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
    }
}