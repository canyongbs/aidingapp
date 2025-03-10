<?php

namespace AidingApp\Engagement\Actions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;

class GenerateEngagementBodyContent
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
