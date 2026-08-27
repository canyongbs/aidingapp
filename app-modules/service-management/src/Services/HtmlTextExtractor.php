<?php

namespace AidingApp\ServiceManagement\Services;

use Dom\Element;
use Dom\HTMLDocument;
use Dom\Node;
use Dom\Text;
use Illuminate\Support\Str;

class HtmlTextExtractor
{
    /**
     * @var list<string>
     */
    private const NON_VISIBLE = [
        'head',
        'noscript',
        'script',
        'style',
        'template',
    ];

    /**
     * @var list<string>
     */
    private const BLOCK = [
        'address',
        'article',
        'aside',
        'blockquote',
        'br',
        'dd',
        'details',
        'div',
        'dl',
        'dt',
        'fieldset',
        'figcaption',
        'figure',
        'footer',
        'form',
        'h1',
        'h2',
        'h3',
        'h4',
        'h5',
        'h6',
        'header',
        'hr',
        'li',
        'main',
        'nav',
        'ol',
        'p',
        'pre',
        'section',
        'summary',
        'table',
        'td',
        'th',
        'tr',
        'ul',
    ];

    public function extract(string $html, ?string $encoding = null): string
    {
        $document = HTMLDocument::createFromString(
            "<!doctype html><html><body>{$html}</body></html>",
            LIBXML_NOERROR,
            $encoding,
        );
        $buffer = '';
        $this->walk($document->body ?? $document, $buffer);

        return self::normalizeWhitespace($buffer);
    }

    public static function normalizeWhitespace(string $value): string
    {
        return trim((string) preg_replace('/\s+/u', ' ', str_replace("\u{00A0}", ' ', $value)));
    }

    private function walk(Node $node, string &$buffer): void
    {
        foreach ($node->childNodes as $child) {
            if ($child instanceof Text) {
                $buffer .= $child->textContent;

                continue;
            }

            if (! $child instanceof Element) {
                continue;
            }

            $tag = Str::lower($child->localName);

            if (in_array($tag, self::NON_VISIBLE, true)) {
                continue;
            }

            $isBlock = in_array($tag, self::BLOCK, true);

            if ($isBlock) {
                $buffer .= ' ';
            }

            $this->walk($child, $buffer);

            if ($isBlock) {
                $buffer .= ' ';
            }
        }
    }
}
