<?php

namespace AidingApp\ServiceManagement\Actions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;

class GenerateServiceRequestTypeEmailTemplateContent
{
    /**
     * @param string|array<int, string|array<string, mixed>> $content
     * @param array<string, mixed> $mergeData
     */
    public function __invoke(string|array $content, array $mergeData, Model $record, string $recordAttribute): HtmlString
    {
        $content = tiptap_converter()
            ->mergeTagsMap($mergeData)
            ->record($record, $recordAttribute)
            ->asHTML($content);

        return str($content)->sanitizeHtml()->toHtmlString();
    }
}
