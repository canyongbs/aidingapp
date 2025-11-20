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
      of the licensor in the software. Any use of the licensor's trademarks is subject
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

namespace AidingApp\ServiceManagement\Actions;

use AidingApp\Form\Actions\ResolveBlockRegistry;
use AidingApp\Form\Models\SubmissibleField;
use AidingApp\Form\Models\SubmissibleStep;
use AidingApp\ServiceManagement\Models\ServiceRequestForm;
use AidingApp\ServiceManagement\Models\ServiceRequestPriority;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\View;
use Filament\Forms\Components\Wizard;
use Illuminate\Database\Eloquent\Collection;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

class GenerateServiceRequestFilamentFormSchema
{
    /**
     * @return array<Component>
     */
    public function __invoke(ServiceRequestForm $form, bool $includePriority = false): array
    {
        $schema = [];

        if ($includePriority) {
            $priorities = $form->type->priorities->map(function (ServiceRequestPriority $priority) {
                return [
                    'label' => $priority->name,
                    'value' => $priority->id,
                ];
            })->pluck('label', 'value')->toArray();

            $schema[] = Select::make('priority')
                ->label('What is the priority of your request?')
                ->options($priorities)
                ->required();
        }

        return [
            ...$schema,
            ...$this->generateContent($form),
        ];
    }

    /**
     * @param  array<string, mixed>  $blocks
     * @param  array<string, mixed>  $content
     * @param  Collection<string, SubmissibleField>|null  $fields
     *
     * @return array<Component>
     */
    public function content(array $blocks, array $content, ?Collection $fields = null): array
    {
        $components = [];

        foreach ($content as $component) {
            $type = $component['type'] ?? null;

            $result = match ($type) {
                'bulletList' => $this->renderList($blocks, $component, $fields, 'ul'),
                'grid' => $this->grid($blocks, $component, $fields),
                'heading' => $this->renderHeading($component),
                'horizontalRule' => $this->renderHorizontalRule(),
                'listItem' => null, // Handled by parent list
                'orderedList' => $this->renderList($blocks, $component, $fields, 'ol'),
                'paragraph' => $this->renderParagraph($blocks, $component, $fields),
                'small' => $this->renderSmall($blocks, $component, $fields),
                'text' => null, // Handled by parent components
                'tiptapBlock' => $this->renderTiptapBlock($blocks, $component, $fields),
                default => null,
            };

            if ($result !== null) {
                $components[] = $result;
            }
        }

        return $components;
    }

    /**
     * @param  array<string, mixed>  $blocks
     * @param  array<string, mixed>  $component
     * @param  Collection<string, SubmissibleField>|null  $fields
     *
     * @return array<Component>
     */
    public function grid(array $blocks, array $component, ?Collection $fields): array
    {
        $columns = match ($component['attrs']['type'] ?? null) {
            'asymetric-left-thirds' => ['md' => 3],
            'asymetric-right-thirds' => ['md' => 3],
            'asymetric-left-fourths' => ['md' => 4],
            'asymetric-right-fourths' => ['md' => 4],
            'fixed', 'responsive' => ['md' => $component['attrs']['cols'] ?? 2],
            default => ['md' => 2],
        };

        return [
            Grid::make($columns)
                ->schema($this->content($blocks, $component['content'] ?? [], $fields)),
        ];
    }

    /**
     * @param  array<string, mixed>  $blocks
     * @param  array<string, mixed>  $component
     * @param  Collection<string, SubmissibleField>|null  $fields
     */
    protected function renderTiptapBlock(array $blocks, array $component, ?Collection $fields): ?Component
    {
        $fieldId = $component['attrs']['id'] ?? null;
        $blockType = $component['attrs']['type'] ?? null;

        if (! $fieldId || ! $blockType) {
            return null;
        }

        $field = $fields[$fieldId] ?? null;

        if (! $field) {
            return null;
        }

        $blockClass = $blocks[$blockType] ?? null;

        if (! $blockClass) {
            return null;
        }

        return $this->getFilamentComponentFromBlock($blockClass, $field);
    }

    /**
     * @param  class-string  $blockClass
     * @param mixed $field
     */
    protected function getFilamentComponentFromBlock(string $blockClass, $field): Component
    {
        // Map form field types to Filament components
        $component = match ($blockClass::type()) {
            'text_input' => TextInput::make($field->getKey())
                ->label($field->label)
                ->required($field->is_required),
            'textarea' => Textarea::make($field->getKey())
                ->label($field->label)
                ->required($field->is_required),
            'email', 'educatable_email' => TextInput::make($field->getKey())
                ->label($field->label)
                ->email()
                ->required($field->is_required),
            'tel' => PhoneInput::make($field->getKey())
                ->label($field->label)
                ->required($field->is_required),
            'number' => TextInput::make($field->getKey())
                ->label($field->label)
                ->numeric()
                ->required($field->is_required),
            'select' => Select::make($field->getKey())
                ->label($field->label)
                ->options($field->config['options'] ?? [])
                ->placeholder($field->config['placeholder'] ?? null)
                ->required($field->is_required),
            'radio' => Radio::make($field->getKey())
                ->label($field->label)
                ->options($field->config['options'] ?? [])
                ->required($field->is_required),
            'checkbox' => Checkbox::make($field->getKey())
                ->label($field->label)
                ->required($field->is_required),
            'date' => DatePicker::make($field->getKey())
                ->label($field->label)
                ->required($field->is_required),
            'time' => TimePicker::make($field->getKey())
                ->label($field->label)
                ->required($field->is_required),
            'url' => TextInput::make($field->getKey())
                ->label($field->label)
                ->url()
                ->required($field->is_required),
            'signature' => TextInput::make($field->getKey())
                ->label($field->label)
                ->required($field->is_required),
            default => TextInput::make($field->getKey())
                ->label($field->label)
                ->required($field->is_required),
        };

        return $component;
    }

    /**
     * @param  array<string, mixed>  $component
     */
    protected function renderHeading(array $component): Component
    {
        $level = $component['attrs']['level'] ?? 2;
        $content = $this->extractTextContent($component['content'] ?? []);

        return View::make('service-management::filament.forms.components.heading')
            ->viewData([
                'level' => $level,
                'content' => $content,
            ]);
    }

    protected function renderHorizontalRule(): Component
    {
        return View::make('service-management::filament.forms.components.horizontal-rule');
    }

    /**
     * @param  array<string, mixed>  $blocks
     * @param  array<string, mixed>  $component
     * @param  Collection<string, SubmissibleField>|null  $fields
     */
    protected function renderParagraph(array $blocks, array $component, ?Collection $fields): Component
    {
        $content = $this->extractTextContent($component['content'] ?? []);

        return View::make('service-management::filament.forms.components.paragraph')
            ->viewData(['content' => $content]);
    }

    /**
     * @param  array<string, mixed>  $blocks
     * @param  array<string, mixed>  $component
     * @param  Collection<string, SubmissibleField>|null  $fields
     */
    protected function renderSmall(array $blocks, array $component, ?Collection $fields): Component
    {
        $content = $this->extractTextContent($component['content'] ?? []);

        return View::make('service-management::filament.forms.components.small')
            ->viewData(['content' => $content]);
    }

    /**
     * @param  array<string, mixed>  $blocks
     * @param  array<string, mixed>  $component
     * @param  Collection<string, SubmissibleField>|null  $fields
     */
    protected function renderList(array $blocks, array $component, ?Collection $fields, string $tag): Component
    {
        /** @var array<int, array<string, mixed>> $content */
        $content = $component['content'] ?? [];
        $items = collect($content)
            ->filter(fn (array $item): bool => ($item['type'] ?? null) === 'listItem')
            ->map(fn (array $item): string => $this->extractTextContent($item['content'] ?? []))
            ->filter()
            ->toArray();

        return View::make('service-management::filament.forms.components.list')
            ->viewData([
                'tag' => $tag,
                'items' => $items,
            ]);
    }

    /**
     * @param  array<string, mixed>  $content
     */
    protected function extractTextContent(array $content): string
    {
        $text = '';

        foreach ($content as $component) {
            $type = $component['type'] ?? null;

            if ($type === 'text') {
                $textContent = $component['text'] ?? '';

                if (filled($component['marks'] ?? [])) {
                    foreach ($component['marks'] as $mark) {
                        $textContent = match ($mark['type'] ?? null) {
                            'bold' => "<strong>{$textContent}</strong>",
                            'italic' => "<em>{$textContent}</em>",
                            'small' => "<small>{$textContent}</small>",
                            'link' => '<a href="' . ($mark['attrs']['href'] ?? '#') . '" target="' . ($mark['attrs']['target'] ?? '_self') . '">' . $textContent . '</a>',
                            default => $textContent,
                        };
                    }
                }

                $text .= $textContent;
            } elseif (isset($component['content'])) {
                $text .= $this->extractTextContent($component['content']);
            }
        }

        return $text;
    }

    /**
     * @return array<Component>
     */
    protected function generateContent(ServiceRequestForm $form): array
    {
        $blocks = app(ResolveBlockRegistry::class)($form, true);

        if ($form->is_wizard) {
            $form->loadMissing([
                'steps' => [
                    'fields',
                ],
            ]);

            return $this->wizardContent($blocks, $form);
        }

        $form->loadMissing([
            'fields',
        ]);

        /** @var array{content?: array<string, mixed>} $content */
        $content = $form->content ?? [];

        return $this->content($blocks, $content['content'] ?? [], $form->fields->keyBy('id'));
    }

    /**
     * @param  array<string, mixed>  $blocks
     *
     * @return array<Component>
     */
    protected function wizardContent(array $blocks, ServiceRequestForm $form): array
    {
        $steps = $form->steps->map(function (SubmissibleStep $step) use ($blocks) {
            /** @var array{content?: array<string, mixed>} $content */
            $content = $step->content ?? [];

            return Wizard\Step::make($step->label)
                ->schema($this->content($blocks, $content['content'] ?? [], $step->fields->keyBy('id')));
        })->toArray();

        return [
            Wizard::make($steps)
                ->columnSpanFull(),
        ];
    }
}
