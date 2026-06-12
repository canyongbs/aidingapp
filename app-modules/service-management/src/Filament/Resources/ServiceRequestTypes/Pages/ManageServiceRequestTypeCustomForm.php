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

namespace AidingApp\ServiceManagement\Filament\Resources\ServiceRequestTypes\Pages;

use AidingApp\Form\Filament\Blocks\FormFieldBlockRegistry;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestTypes\ServiceRequestTypeResource;
use AidingApp\ServiceManagement\Models\ServiceRequestForm;
use AidingApp\ServiceManagement\Models\ServiceRequestFormField;
use AidingApp\ServiceManagement\Models\ServiceRequestFormStep;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use App\Concerns\EditPageRedirection;
use App\Enums\Feature;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\RichEditor\ToolbarButtonGroup;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Gate;

class ManageServiceRequestTypeCustomForm extends EditRecord
{
    use EditPageRedirection;

    protected static string $resource = ServiceRequestTypeResource::class;

    protected static ?string $title = 'Custom Form';

    protected static ?string $breadcrumb = 'Custom Form';

    public static function getNavigationLabel(): string
    {
        return 'Custom Form';
    }

    public static function canAccess(array $arguments = []): bool
    {
        return parent::canAccess($arguments)
            && Gate::check(Feature::OnlineForms->getGateName());
    }

    public function getRelationManagers(): array
    {
        // Needed to prevent Filament from loading the relation managers on this page.
        return [];
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Properties')
                    ->schema([
                        Textarea::make('description')
                            ->string()
                            ->columnSpanFull(),
                        Toggle::make('is_wizard')
                            ->label('Multi-step service request form')
                            ->live(),
                    ]),
                Section::make('Form Design')
                    ->schema([
                        $this->fieldBuilder('content'),
                    ])
                    ->hidden(fn (Get $get): bool => (bool) $get('is_wizard')),
                Repeater::make('steps')
                    ->schema([
                        TextInput::make('label')
                            ->required()
                            ->string()
                            ->maxLength(255)
                            ->autocomplete(false)
                            ->columnSpanFull()
                            ->lazy(),
                        $this->fieldBuilder('content'),
                    ])
                    ->addActionLabel('New step')
                    ->itemLabel(fn (array $state): ?string => $state['label'] ?? null)
                    ->visible(fn (Get $get): bool => (bool) $get('is_wizard'))
                    ->reorderable()
                    ->columnSpanFull(),
            ]);
    }

    public function save(bool $shouldRedirect = true, bool $shouldSendSavedNotification = true): void
    {
        $state = $this->form->getState();

        $type = $this->getServiceRequestType();
        $current = $type->form;

        $isWizard = (bool) ($state['is_wizard'] ?? false);

        if ($current && $current->submissions()->exists()) {
            $current->archive();

            $form = new ServiceRequestForm();
            $form->type()->associate($type);
        } elseif ($current) {
            $form = $current;
        } else {
            $form = new ServiceRequestForm();
            $form->type()->associate($type);
        }

        $form->fill([
            'description' => $state['description'] ?? null,
            'is_wizard' => $isWizard,
        ])->save();

        $form->fields()->delete();
        $form->steps()->delete();

        if ($isWizard) {
            $form->fill(['content' => null])->save();

            $sort = 0;

            foreach ($state['steps'] ?? [] as $stepState) {
                $step = $form->steps()->create([
                    'label' => $stepState['label'] ?? '',
                    'sort' => $sort++,
                    'content' => ['type' => 'doc', 'content' => []],
                ]);

                $content = $this->normalizeContent($stepState['content'] ?? null);
                $content['content'] = $this->persistFields($form, $content['content'] ?? [], $step);

                $step->fill(['content' => $content])->save();
            }
        } else {
            $content = $this->normalizeContent($state['content'] ?? null);
            $content['content'] = $this->persistFields($form, $content['content'] ?? [], null);

            $form->fill(['content' => $content])->save();
        }

        $this->getServiceRequestType()->unsetRelation('form');
        $this->fillForm();

        $this->getSavedNotification()?->send();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('preview')
                ->label('Preview')
                ->icon('heroicon-o-eye')
                ->url(fn (): ?string => ($form = $this->getServiceRequestType()->form)
                    ? route('service-request-forms.preview', ['serviceRequestForm' => $form])
                    : null)
                ->openUrlInNewTab()
                ->visible(fn (): bool => (bool) $this->getServiceRequestType()->form),
        ];
    }

    protected function fieldBuilder(string $name): RichEditor
    {
        return RichEditor::make($name)
            ->customBlocks(FormFieldBlockRegistry::get())
            ->toolbarButtons([
                ['bold', 'italic', 'small', 'link'],
                [ToolbarButtonGroup::make('Heading', ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'])->textualButtons(), 'bulletList', 'orderedList', 'horizontalRule'],
                ['grid'],
                ['customBlocks'],
            ])
            ->activePanel('customBlocks')
            ->json()
            ->placeholder('Drag blocks here to build your service request form')
            ->hiddenLabel()
            ->columnSpanFull()
            ->extraInputAttributes(['style' => 'min-height: 12rem;']);
    }

    protected function fillForm(): void
    {
        $form = $this->getServiceRequestType()->form;

        $this->form->fill([
            'description' => $form?->description,
            'is_wizard' => (bool) $form?->is_wizard,
            'content' => $form?->content,
            'steps' => $form
                ? $form->steps()
                    ->orderBy('sort')
                    ->get()
                    ->map(fn (ServiceRequestFormStep $step): array => [
                        'label' => $step->label,
                        'content' => $step->content,
                    ])
                    ->all()
                : [],
        ]);
    }

    /**
     * @param array<int, array<string, mixed>> $components
     *
     * @return array<int, array<string, mixed>>
     */
    protected function persistFields(ServiceRequestForm $form, array $components, ?ServiceRequestFormStep $step): array
    {
        foreach ($components as $key => $component) {
            if (array_key_exists('content', $component)) {
                $components[$key]['content'] = $this->persistFields($form, $component['content'], $step);

                continue;
            }

            if (($component['type'] ?? null) !== 'customBlock') {
                continue;
            }

            $blockType = $component['attrs']['id'] ?? null;
            $config = $component['attrs']['config'] ?? [];
            $label = $config['label'] ?? $blockType;
            $isRequired = $config['isRequired'] ?? false;

            $fieldConfig = $config;
            unset($fieldConfig['label'], $fieldConfig['isRequired'], $fieldConfig['fieldId']);

            $field = new ServiceRequestFormField();
            $field->submissible()->associate($form);
            $field->step()->associate($step);
            $field->fill([
                'label' => $label,
                'is_required' => $isRequired,
                'type' => $blockType,
                'config' => $fieldConfig,
            ]);
            $field->save();

            $components[$key]['attrs']['config']['fieldId'] = $field->id;
        }

        return $components;
    }

    /**
     * @return array<string, mixed>
     */
    protected function normalizeContent(mixed $content): array
    {
        if (is_string($content)) {
            $content = json_decode($content, true);
        }

        if (! is_array($content)) {
            $content = [];
        }

        $content['type'] ??= 'doc';
        $content['content'] ??= [];

        return $content;
    }

    protected function getServiceRequestType(): ServiceRequestType
    {
        /** @var ServiceRequestType $record */
        $record = $this->getRecord();

        return $record;
    }
}
