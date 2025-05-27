<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        DB::table('knowledge_base_articles')->orderBy('id')->chunk(100, function ($articles) {
            foreach ($articles as $article) {
                $json = json_decode($article->article_details, true);

                if (! $json || ! isset($json['content'])) {
                    continue;
                }

                $html = self::convertToHtml($json['content']);

                $fulltext = strip_tags($html);

                DB::table('knowledge_base_articles')
                    ->where('id', $article->id)
                    ->update(['article_details_fulltext' => $fulltext]);
            }
        });
    }

    public function down(): void
    {
        Schema::table('knowledge_base_articles', function (Blueprint $table) {
            DB::table('knowledge_base_articles')->update([
                'article_details_fulltext' => null,
            ]);
        });
    }

    /**
     * @param array<int, array<string, mixed>> $content.
     *
     * @return string
     */
    private static function convertToHtml(array $content): string
    {
        $html = '';

        foreach ($content as $node) {
            $type = $node['type'];
            $attrs = $node['attrs'] ?? [];
            $children = $node['content'] ?? [];

            switch ($type) {
                case 'heading':
                    $level = $attrs['level'] ?? 2;
                    $style = self::buildStyle($attrs);
                    $html .= "<h{$level}{$style}>" . self::convertToHtml($children) . "</h{$level}> ";

                    break;
                case 'paragraph':
                    $style = self::buildStyle($attrs);
                    $html .= "<p{$style}>" . self::convertToHtml($children) . '</p> ';

                    break;
                case 'bulletList':
                    $style = self::buildStyle($attrs);
                    $html .= "<ul{$style}>" . self::convertToHtml($children) . '</ul> ';

                    break;
                case 'orderedList':
                    $style = self::buildStyle($attrs);
                    $start = isset($attrs['start']) && $attrs['start'] != 1 ? ' start="' . intval($attrs['start']) . '"' : '';
                    $html .= "<ol{$start}{$style}>" . self::convertToHtml($children) . '</ol> ';

                    break;
                case 'checkedList':
                    $style = self::buildStyle($attrs);
                    $html .= "<ul class='checked-list'{$style}>" . self::convertToHtml($children) . '</ul> ';

                    break;
                case 'listItem':
                    $style = self::buildStyle($attrs);
                    $html .= "<li{$style}>" . self::convertToHtml($children) . '</li> ';

                    break;
                case 'blockquote':
                    $html .= '<blockquote>' . self::convertToHtml($children) . '</blockquote> ';

                    break;
                case 'hardBreak':
                    $html .= '<br/>';

                    break;
                case 'text':
                    $html .= self::applyMarks($node);

                    break;
                case 'table':
                    $style = self::buildStyle($attrs);
                    $html .= "<table{$style}>" . self::convertToHtml($children) . '</table> ';

                    break;
                case 'tableRow':
                    $style = self::buildStyle($attrs);
                    $html .= "<tr{$style}>" . self::convertToHtml($children) . '</tr> ';

                    break;
                case 'tableHeader':
                    $style = self::buildStyle($attrs);
                    $colspan = isset($attrs['colspan']) && $attrs['colspan'] > 1 ? ' colspan="' . intval($attrs['colspan']) . '"' : '';
                    $rowspan = isset($attrs['rowspan']) && $attrs['rowspan'] > 1 ? ' rowspan="' . intval($attrs['rowspan']) . '"' : '';
                    $html .= "<th{$colspan}{$rowspan}{$style}>" . self::convertToHtml($children) . '</th> ';

                    break;
                case 'tableCell':
                    $style = self::buildStyle($attrs);
                    $colspan = isset($attrs['colspan']) && $attrs['colspan'] > 1 ? ' colspan="' . intval($attrs['colspan']) . '"' : '';
                    $rowspan = isset($attrs['rowspan']) && $attrs['rowspan'] > 1 ? ' rowspan="' . intval($attrs['rowspan']) . '"' : '';
                    $html .= "<td{$colspan}{$rowspan}{$style}>" . self::convertToHtml($children) . '</td> ';

                    break;
                case 'grid':
                    $cols = $attrs['cols'] ?? 1;
                    $type = $attrs['type'] ?? 'default';
                    $html .= "<div class='grid grid-{$type}' style='grid-template-columns: repeat({$cols}, 1fr);'>" . self::convertToHtml($children) . '</div>';

                    break;
                case 'gridColumn':
                    $html .= "<div class='grid-column'>" . self::convertToHtml($children) . '</div> ';

                    break;
                case 'details':
                    $html .= '<details>' . self::convertToHtml($children) . '</details> ';

                    break;
                case 'detailsSummary':
                    $html .= '<summary>' . self::convertToHtml($children) . '</summary> ';

                    break;
                case 'detailsContent':
                    $html .= "<div class='details-content'>" . self::convertToHtml($children) . '</div> ';

                    break;
                case 'hurdle':
                    $color = $attrs['color'] ?? 'gray';
                    $html .= "<div class='hurdle hurdle-{$color}'>" . self::convertToHtml($children) . '</div> ';

                    break;
                case 'codeBlock':
                    $lang = $attrs['language'] ?? '';
                    $langClass = $lang ? " class='language-{$lang}'" : '';
                    $text = '';

                    foreach ($children as $child) {
                        if ($child['type'] === 'text') {
                            $text .= e($child['text']);
                        }
                    }
                    $html .= "<pre><code{$langClass}>{$text}</code></pre> ";

                    break;

                default:
                    if (! empty($children)) {
                        $html .= self::convertToHtml($children);
                    }

                    break;
            }
        }

        return $html;
    }

    /**
     * @param array<string, mixed> $attrs.
     *
     * @return string
     */
    private static function buildStyle(array $attrs): string
    {
        $styleString = '';
        $classString = '';

        if (! empty($attrs['style'])) {
            $styleString = ' style="' . e($attrs['style']) . '"';
        }

        if (! empty($attrs['class'])) {
            $classString = ' class="' . e($attrs['class']) . '"';
        }

        if (isset($attrs['textAlign'])) {
            $align = $attrs['textAlign'];

            if (in_array($align, ['start', 'center', 'end'])) {
                $styleString .= ' style="text-align:' . e($align === 'start' ? 'left' : ($align === 'end' ? 'right' : 'center')) . ';"';
            }
        }

        return $classString . $styleString;
    }

    /**
     * @param array<string, mixed> $node.
     *
     * @return string
     */
    private static function applyMarks(array $node): string
    {
        $text = e($node['text'] ?? '');

        if (! empty($node['marks'])) {
            foreach ($node['marks'] as $mark) {
                switch ($mark['type']) {
                    case 'bold':
                        $text = "<strong>{$text}</strong>";

                        break;
                    case 'italic':
                        $text = "<em>{$text}</em>";

                        break;
                    case 'underline':
                        $text = "<u>{$text}</u>";

                        break;
                    case 'strike':
                        $text = "<s>{$text}</s>";

                        break;
                    case 'code':
                        $text = "<code>{$text}</code>";

                        break;
                    case 'link':
                        $attrs = $mark['attrs'] ?? [];
                        $href = e($attrs['href'] ?? '#');
                        $target = e($attrs['target'] ?? '_blank');
                        $rel = e($attrs['rel'] ?? 'noopener noreferrer');
                        $class = e($attrs['class'] ?? '');
                        $style = e($attrs['style'] ?? '');
                        $classAttr = $class ? " class=\"{$class}\"" : '';
                        $styleAttr = $style ? " style=\"{$style}\"" : '';
                        $text = "<a href=\"{$href}\" target=\"{$target}\" rel=\"{$rel}\"{$classAttr}{$styleAttr}>{$text}</a>";

                        break;
                    case 'superscript':
                        $text = "<sup>{$text}</sup>";

                        break;
                    case 'subscript':
                        $text = "<sub>{$text}</sub>";

                        break;
                    case 'small':
                        $text = "<small>{$text}</small>";

                        break;
                    case 'highlight':
                        $text = "<mark>{$text}</mark>";

                        break;
                    case 'textStyle':
                        $styleAttrs = [];

                        if (! empty($mark['attrs'])) {
                            foreach ($mark['attrs'] as $k => $v) {
                                if ($v !== null) {
                                    $styleAttrs[] = "{$k}: {$v}";
                                }
                            }
                        }
                        $styleStr = implode('; ', $styleAttrs);
                        $text = '<span style="' . e($styleStr) . "\">{$text}</span>";

                        break;
                }
            }
        }

        return $text;
    }
};
