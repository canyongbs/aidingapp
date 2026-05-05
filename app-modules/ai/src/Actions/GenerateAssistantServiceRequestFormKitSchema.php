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

namespace AidingApp\Ai\Actions;

use AidingApp\Form\Actions\ResolveBlockRegistry;
use AidingApp\Form\Filament\Blocks\FormFieldBlock;
use AidingApp\Form\Models\SubmissibleField;
use AidingApp\Form\Models\SubmissibleStep;
use AidingApp\ServiceManagement\Models\ServiceRequestForm;
use Illuminate\Database\Eloquent\Collection;

class GenerateAssistantServiceRequestFormKitSchema
{
    /**
     * @return array<int, array{label: string, schema: array<int, mixed>}>
     */
    public function __invoke(ServiceRequestForm $form): array
    {
        $form->loadMissing([
            'steps' => [
                'fields',
            ],
        ]);

        $blocks = app(ResolveBlockRegistry::class)($form, true);

        $steps = [];

        foreach ($form->steps as $step) {
            if ($step->label === 'Main') {
                continue;
            }

            $schema = $this->generateStepSchema($blocks, $step);

            if (empty($schema)) {
                continue;
            }

            $steps[] = [
                'label' => $step->label,
                'schema' => $schema,
            ];
        }

        return $steps;
    }

    /**
     * @param  array<string, class-string<FormFieldBlock>>  $blocks
     * @return array<int, mixed>
     */
    protected function generateStepSchema(array $blocks, SubmissibleStep $step): array
    {
        /** @var array<int, mixed> $content */
        $content = $step->content['content'] ?? []; // @phpstan-ignore nullCoalesce.offset
        $fields = $step->fields->keyBy('id');

        return $this->flatContent($blocks, $content, $fields);
    }

    /**
     * @param  array<string, class-string<FormFieldBlock>>  $blocks
     * @param  array<int, mixed>  $content
     * @param  Collection<int, SubmissibleField>|null  $fields
     * @return array<int, mixed>
     */
    protected function flatContent(array $blocks, array $content, ?Collection $fields = null): array
    {
        $result = [];

        foreach ($content as $component) {
            $items = match ($component['type'] ?? null) {
                'grid' => $this->flatContent($blocks, $this->flattenGrid($component), $fields),
                'gridColumn' => $this->flatContent($blocks, $component['content'] ?? [], $fields),
                'customBlock' => $this->resolveField($blocks, $component, $fields),
                'heading' => [['$el' => "h{$component['attrs']['level']}", 'children' => $this->textContent($component['content'] ?? [])]],
                'paragraph' => [['$el' => 'p', 'children' => $this->textContent($component['content'] ?? [])]],
                'horizontalRule' => [['$el' => 'hr']],
                default => [],
            };

            array_push($result, ...$items);
        }

        return $result;
    }

    /**
     * @param  array<string, mixed>  $component
     * @return array<int, mixed>
     */
    protected function flattenGrid(array $component): array
    {
        /** @var array<int, array<string, mixed>> $columns */
        $columns = $component['content'] ?? [];
        $flattened = [];

        foreach ($columns as $column) {
            $flattened = array_merge($flattened, $column['content'] ?? []);
        }

        return $flattened;
    }

    /**
     * @param  array<string, class-string<FormFieldBlock>>  $blocks
     * @param  array<string, mixed>  $component
     * @param  Collection<int, SubmissibleField>|null  $fields
     * @return array<int, mixed>
     */
    protected function resolveField(array $blocks, array $component, ?Collection $fields): array
    {
        $fieldId = $component['attrs']['config']['fieldId'] ?? '';
        $blockType = $component['attrs']['id'] ?? '';

        /** @var SubmissibleField|null $field */
        $field = $fields[$fieldId] ?? null;
        $block = $blocks[$blockType] ?? null;

        if (! $field || ! $block) {
            return [];
        }

        $schema = $block::getFormKitSchema($field);

        if (empty($schema)) {
            return [];
        }

        if ($field->is_required) {
            $schema['classes'] = [
                'outer' => 'formkit-required',
            ];
        }

        if (($schema['$formkit'] ?? null) === 'upload') {
            $schema['uploadUrl'] = route('widgets.assistant.api.service-request.upload-url');
        }

        return [$schema];
    }

    /**
     * @param  array<int, mixed>  $content
     * @return array<int, mixed>|string
     */
    protected function textContent(array $content): array|string
    {
        if (empty($content)) {
            return '';
        }

        if (count($content) === 1 && ($content[0]['type'] ?? null) === 'text' && empty($content[0]['marks'] ?? [])) {
            return $content[0]['text'];
        }

        return array_map(
            fn (array $component): array|string => match ($component['type'] ?? null) {
                'text' => $this->text($component),
                default => '',
            },
            $content,
        );
    }

    /**
     * @param  array<string, mixed>  $component
     * @return array<string, mixed>|string
     */
    protected function text(array $component): array|string
    {
        if (filled($component['marks'] ?? [])) {
            return array_reduce(
                $component['marks'],
                fn (array|string $text, array $mark): array|string => match ($mark['type']) {
                    'bold' => ['$el' => 'strong', 'children' => $component['text']],
                    'italic' => ['$el' => 'em', 'children' => $component['text']],
                    'link' => ['$el' => 'a', 'attrs' => ['href' => $mark['attrs']['href'] ?? null, 'target' => $mark['attrs']['target'] ?? null], 'children' => $component['text']],
                    default => $text,
                },
                $component['text'],
            );
        }

        return $component['text'];
    }
}
