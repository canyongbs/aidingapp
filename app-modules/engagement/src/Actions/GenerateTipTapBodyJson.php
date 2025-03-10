<?php

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
