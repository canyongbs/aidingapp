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

namespace AidingApp\ServiceManagement\Actions;

use AidingApp\ServiceManagement\Models\ServiceRequestTypeEmailTemplate;
use Illuminate\Support\HtmlString;

class GenerateServiceRequestTypeEmailTemplateContent
{
    /**
     * @param string|array<int, string|array<string, mixed>> $content
     * @param array<string, mixed> $mergeData
     */
    public function __invoke(string|array $content, array $mergeData, ServiceRequestTypeEmailTemplate $template): HtmlString
    {
        $template->body = $content;

        $content = $template->getRichContentAttribute('body')
            ?->mergeTags($mergeData)
            ->toHtml() ?? '';

        // Convert CSS variable-based styles to inline styles for email client compatibility.
        // The RichEditor uses CSS custom properties that email clients don't support.

        // Text colors: style="--color: #hex; --dark-color: #hex" → style="color: #hex"
        $content = preg_replace(
            '/style="--color:\s*([^;]+);\s*--dark-color:\s*[^"]*"/',
            'style="color: $1"',
            $content,
        );

        // Grid layout: style="--cols: repeat(X, ...)" → style="display: table; width: 100%"
        $content = preg_replace(
            '/style="--cols:\s*[^"]*"/',
            'style="display: table; width: 100%; table-layout: fixed;"',
            $content,
        );

        // Grid columns: style="--col-span: span X / span X" → style="display: table-cell; vertical-align: top; padding: 0 8px"
        $content = preg_replace(
            '/style="--col-span:\s*[^"]*"/',
            'style="display: table-cell; vertical-align: top; padding: 0 8px;"',
            $content,
        );

        return str($content)->toHtmlString();
    }
}
