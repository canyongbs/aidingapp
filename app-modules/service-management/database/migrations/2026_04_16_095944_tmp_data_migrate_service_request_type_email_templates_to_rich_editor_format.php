<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Aiding App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Aiding App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    <https://www.canyongbs.com> or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    private const KNOWN_BLOCK_TYPES = [
        'serviceRequestTypeEmailTemplateButtonBlock',
        'surveyResponseEmailTemplateTakeSurveyButtonBlock',
    ];

    public function up(): void
    {
        $columns = ['subject', 'body'];

        foreach ($columns as $column) {
            DB::table('service_request_type_email_templates')
                ->whereNotNull($column)
                ->eachById(function (object $record) use ($column) {
                    $content = json_decode($record->{$column}, associative: true);

                    if (! is_array($content)) {
                        return;
                    }

                    $changed = false;

                    /** @var array<string, mixed> $content */
                    $this->processNodes($content, $changed);

                    if (! $changed) {
                        return;
                    }

                    DB::table('service_request_type_email_templates')
                        ->where('id', $record->id)
                        ->update([$column => json_encode($content)]);
                }, 100);
        }
    }

    public function down(): void {}

    /**
     * @param  array<string, mixed>  $node
     */
    protected function processNodes(array &$node, bool &$changed): void
    {
        $type = $node['type'] ?? null;

        match ($type) {
            'tiptapBlock' => $this->transformTiptapBlock($node, $changed),
            'grid' => $this->transformGrid($node, $changed),
            'hurdle' => $this->removeNode($node, $changed),
            'youtube' => $this->transformYoutube($node, $changed),
            'vimeo' => $this->transformVimeo($node, $changed),
            'video' => $this->transformVideo($node, $changed),
            'image' => $this->transformImage($node, $changed),
            'gridBuilder' => $this->transformGridBuilder($node, $changed),
            'checkedList' => $this->transformCheckedList($node, $changed),
            default => null,
        };

        if (isset($node['marks']) && is_array($node['marks'])) {
            /** @var array<int, array<string, mixed>> $marks */
            $marks = &$node['marks'];
            $this->processMarks($marks, $changed);
        }

        if (isset($node['content']) && is_array($node['content'])) {
            /** @var array<int, array<string, mixed>> $content */
            $content = $node['content'];

            $node['content'] = $this->unwrapRemovedNodes($content);

            foreach ($node['content'] as &$child) {
                $this->processNodes($child, $changed);
            }

            /** @var array<int, array<string, mixed>> $contentAfter */
            $contentAfter = $node['content'];

            $node['content'] = $this->unwrapRemovedNodes($contentAfter);
        }
    }

    /**
     * @param  array<string, mixed>  $node
     */
    protected function transformTiptapBlock(array &$node, bool &$changed): void
    {
        $oldAttrs = $node['attrs'] ?? [];
        $blockType = $oldAttrs['type'] ?? null;

        if (in_array($blockType, self::KNOWN_BLOCK_TYPES, true)) {
            $node['type'] = 'customBlock';
            $node['attrs'] = [
                'id' => $blockType,
                'config' => $oldAttrs['data'] ?? [],
            ];
        } else {
            $this->removeNode($node, $changed);
        }

        $changed = true;
    }

    /**
     * @param  array<string, mixed>  $node
     */
    protected function transformGrid(array &$node, bool &$changed): void
    {
        /** @var array<string, mixed> $attrs */
        $attrs = $node['attrs'] ?? [];
        $oldType = is_string($attrs['type'] ?? null) ? $attrs['type'] : 'responsive';
        $oldCols = is_string($attrs['cols'] ?? null) ? $attrs['cols'] : '2';

        [$dataCols, $fromBreakpoint, $colSpans] = $this->mapGridType($oldType, $oldCols);

        $node['attrs'] = [
            'data-cols' => (string) $dataCols,
            'data-from-breakpoint' => $fromBreakpoint,
        ];

        $changed = true;

        if (! isset($node['content']) || ! is_array($node['content'])) {
            return;
        }

        foreach ($node['content'] as $index => &$child) {
            if (is_array($child) && ($child['type'] ?? null) === 'gridColumn') {
                $child['attrs'] = $child['attrs'] ?? [];
                $child['attrs']['data-col-span'] = (string) ($colSpans[$index] ?? 1);
            }
        }
    }

    /**
     * @return array{int, string, array<int>}
     */
    protected function mapGridType(string $type, string $cols): array
    {
        $fromBreakpoint = ($type === 'fixed') ? 'sm' : 'lg';

        return match ($type) {
            'asymetric-left-thirds' => [3, $fromBreakpoint, [2, 1]],
            'asymetric-right-thirds' => [3, $fromBreakpoint, [1, 2]],
            'asymetric-left-fourths' => [4, $fromBreakpoint, [3, 1]],
            'asymetric-right-fourths' => [4, $fromBreakpoint, [1, 3]],
            default => [(int) $cols, $fromBreakpoint, array_fill(0, (int) $cols, 1)],
        };
    }

    /**
     * @param  array<string, mixed>  $node
     */
    protected function removeNode(array &$node, bool &$changed): void
    {
        $node['type'] = '__removed__';

        $changed = true;
    }

    /**
     * @param  array<int, array<string, mixed>>  $content
     *
     * @return array<int, array<string, mixed>>
     */
    protected function unwrapRemovedNodes(array $content): array
    {
        $result = [];

        foreach ($content as $child) {
            if (($child['type'] ?? null) === '__removed__' && ! empty($child['content'])) {
                /** @var array<int, array<string, mixed>> $grandchildren */
                $grandchildren = $child['content'];

                foreach ($grandchildren as $grandchild) {
                    $result[] = $grandchild;
                }
            } elseif (($child['type'] ?? null) !== '__removed__') {
                $result[] = $child;
            }
        }

        return $result;
    }

    /**
     * @param  array<string, mixed>  $node
     */
    protected function transformYoutube(array &$node, bool &$changed): void
    {
        $node['type'] = 'videoEmbed';

        /** @var array<string, mixed> $attrs */
        $attrs = $node['attrs'] ?? [];
        $src = is_string($attrs['src'] ?? null) ? $attrs['src'] : '';

        $node['attrs'] = [
            'src' => $src,
            'type' => 'youtube',
            'width' => null,
            'height' => null,
        ];

        $changed = true;
    }

    /**
     * @param  array<string, mixed>  $node
     */
    protected function transformVimeo(array &$node, bool &$changed): void
    {
        $node['type'] = 'videoEmbed';

        /** @var array<string, mixed> $attrs */
        $attrs = $node['attrs'] ?? [];
        $src = is_string($attrs['src'] ?? null) ? $attrs['src'] : '';

        $node['attrs'] = [
            'src' => $src,
            'type' => 'vimeo',
            'width' => null,
            'height' => null,
        ];

        $changed = true;
    }

    /**
     * @param  array<string, mixed>  $node
     */
    protected function transformVideo(array &$node, bool &$changed): void
    {
        $node['type'] = 'videoEmbed';

        /** @var array<string, mixed> $attrs */
        $attrs = $node['attrs'] ?? [];
        $src = is_string($attrs['src'] ?? null) ? $attrs['src'] : '';

        $node['attrs'] = [
            'src' => $src,
            'type' => 'video',
            'width' => null,
            'height' => null,
        ];

        $changed = true;
    }

    /**
     * @param  array<string, mixed>  $node
     */
    protected function transformImage(array &$node, bool &$changed): void
    {
        /** @var array<string, mixed> $attrs */
        $attrs = $node['attrs'] ?? [];
        $width = $attrs['width'] ?? null;

        if (is_numeric($width) && $width > 500) {
            $attrs['width'] = null;
            $attrs['height'] = null;
            $node['attrs'] = $attrs;
            $changed = true;
        }
    }

    /**
     * @param  array<string, mixed>  $node
     */
    protected function transformGridBuilder(array &$node, bool &$changed): void
    {
        $node['type'] = 'grid';

        /** @var array<string, mixed> $attrs */
        $attrs = $node['attrs'] ?? [];
        $rawCols = $attrs['data-cols'] ?? $attrs['cols'] ?? null;
        $oldCols = is_string($rawCols) || is_int($rawCols) ? $rawCols : '2';
        $colCount = (int) $oldCols;
        $stackAt = is_string($attrs['data-stack-at'] ?? null) ? $attrs['data-stack-at'] : 'lg';

        $node['attrs'] = [
            'data-cols' => (string) $colCount,
            'data-from-breakpoint' => $stackAt,
        ];

        $changed = true;

        if (! isset($node['content']) || ! is_array($node['content'])) {
            return;
        }

        foreach ($node['content'] as &$child) {
            if (is_array($child) && ($child['type'] ?? null) === 'gridBuilderColumn') {
                $child['type'] = 'gridColumn';

                /** @var array<string, mixed> $childAttrs */
                $childAttrs = $child['attrs'] ?? [];
                $colSpan = $childAttrs['data-col-span'] ?? $childAttrs['span'] ?? 1;

                $child['attrs'] = [
                    'data-col-span' => is_scalar($colSpan) ? (string) ($colSpan ?: 1) : '1',
                ];
            }
        }
    }

    /**
     * @param  array<string, mixed>  $node
     */
    protected function transformCheckedList(array &$node, bool &$changed): void
    {
        $node['type'] = 'bulletList';

        $changed = true;

        if (! isset($node['content']) || ! is_array($node['content'])) {
            return;
        }

        foreach ($node['content'] as &$child) {
            if (is_array($child) && in_array($child['type'] ?? null, ['checkedListItem', 'taskItem', 'listItem'])) {
                $child['type'] = 'listItem';

                unset($child['attrs']['checked']);
            }
        }
    }

    /**
     * @param  array<int, array<string, mixed>>  $marks
     */
    protected function processMarks(array &$marks, bool &$changed): void
    {
        foreach ($marks as &$mark) {
            if (($mark['type'] ?? null) === 'textStyle') {
                /** @var array<string, mixed> $markAttrs */
                $markAttrs = $mark['attrs'] ?? [];

                $mark['type'] = 'textColor';
                $mark['attrs'] = [
                    'data-color' => $markAttrs['color'] ?? null,
                ];
                $changed = true;
            }
        }
    }
};
