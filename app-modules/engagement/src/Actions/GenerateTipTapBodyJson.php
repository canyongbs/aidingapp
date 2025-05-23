<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

    Aiding App™ is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Aiding App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    <https://www.canyongbs.com> or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AidingApp\Engagement\Actions;

class GenerateTipTapBodyJson
{
    public function __invoke(string $body, array $mergeTags = []): array
    {
        return json_decode(tiptap_converter()
            ->getEditor()
            ->setContent($body)
            ->descendants(function ($node) use ($mergeTags) {
                if ($node->type !== 'paragraph') {
                    return;
                }

                $content = collect();

                foreach ($node->content as $item) {
                    preg_match_all('/{{2}[\s\S]*?}{2}|\s*\S+\s*/', $item->text, $tokens);

                    if (blank($tokens)) {
                        continue;
                    }

                    $content->push(
                        collect($tokens[0])
                            ->map(
                                fn ($token) => in_array($token, $mergeTags)
                                    ? (object) $this->mergeTag($token)
                                    : (object) $this->text($token, $item)
                            )
                    );
                }
                $node->content = $content->flatten()->toArray();
            })
            ->getJSON(), true);
    }

    private function mergeTag(string $token): array
    {
        return [
            'type' => 'mergeTag',
            'attrs' => [
                'id' => str($token)->remove(['{{', '}}'])->trim()->toString(),
            ],
        ];
    }

    private function text(string $text, object $item): array
    {
        return [
            'type' => 'text',
            'text' => $text,
            ...collect($item)->except(['type', 'text'])->toArray(),
        ];
    }
}
