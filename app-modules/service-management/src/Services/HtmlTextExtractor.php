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
