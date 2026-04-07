<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

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

namespace AidingApp\ServiceManagement\Filament\Resources\ServiceRequestForms\Pages\Concerns;

use AidingApp\Form\Enums\Rounding;
use AidingApp\Form\Filament\Blocks\FormFieldBlockRegistry;
use AidingApp\Form\Rules\IsDomain;
use AidingApp\IntegrationGoogleRecaptcha\Settings\GoogleRecaptchaSettings;
use AidingApp\ServiceManagement\Models\ServiceRequestForm;
use AidingApp\ServiceManagement\Models\ServiceRequestFormField;
use AidingApp\ServiceManagement\Models\ServiceRequestFormStep;
use CanyonGBS\Common\Filament\Forms\Components\ColorSelect;
use CanyonGBS\Common\Filament\Support\HideDeletedAndArchivedExceptSelectedFromSelectOptions;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Forms\Components\RichEditor;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

trait HasSharedFormConfiguration
{
    /**
     * @return array<Component>
     */
    public function fields(): array
    {
        return [
            TextInput::make('name')
                ->required()
                ->string()
                ->maxLength(255)
                ->unique(ignoreRecord: true)
                ->autocomplete(false)
                ->columnSpanFull(),
            Textarea::make('description')
                ->string()
                ->columnSpanFull(),
            Select::make('service_request_type_id')
                ->label('Service Request Type')
                ->helperText('This is the type of service request that will be created when this form is submitted.')
                ->relationship('type', 'name', app(HideDeletedAndArchivedExceptSelectedFromSelectOptions::class)(...))
                ->preload()
                ->searchable()
                ->required(),
            Grid::make()
                ->schema([
                    Toggle::make('embed_enabled')
                        ->label('Embed Enabled')
                        ->live()
                        ->helperText('If enabled, this service request form can be embedded on other websites.'),
                    TagsInput::make('allowed_domains')
                        ->label('Allowed Domains')
                        ->helperText('Only these domains will be allowed to embed this service request form.')
                        ->placeholder('example.com')
                        ->hidden(fn (Get $get) => ! $get('embed_enabled'))
                        ->disabled(fn (Get $get) => ! $get('embed_enabled'))
                        ->nestedRecursiveRules(
                            [
                                'string',
                                new IsDomain(),
                            ]
                        ),
                ])
                ->columnSpanFull(),
            Toggle::make('is_wizard')
                ->label('Multi-step service request form')
                ->live()
                ->disabled(fn (?ServiceRequestForm $record) => $record?->submissions()->submitted()->exists()),
            Toggle::make('recaptcha_enabled')
                ->label('Enable reCAPTCHA')
                ->live()
                ->disabled(fn (GoogleRecaptchaSettings $settings) => ! $settings->is_enabled)
                ->helperText(function (GoogleRecaptchaSettings $settings) {
                    if (! $settings->is_enabled) {
                        return 'Enable and configure reCAPTCHA in order to use it on your service request form.';
                    }
                }),
            Section::make('Fields')
                ->schema([
                    $this->fieldBuilder(),
                ])
                ->hidden(fn (Get $get) => $get('is_wizard'))
                ->disabled(fn (?ServiceRequestForm $record) => $record?->submissions()->submitted()->exists()),
            Repeater::make('steps')
                ->schema([
                    TextInput::make('label')
                        ->required()
                        ->string()
                        ->maxLength(255)
                        ->autocomplete(false)
                        ->columnSpanFull()
                        ->lazy(),
                    $this->fieldBuilder(),
                ])
                ->addActionLabel('New step')
                ->itemLabel(fn (array $state): ?string => $state['label'] ?? null)
                ->visible(fn (Get $get) => $get('is_wizard'))
                ->disabled(fn (?ServiceRequestForm $record) => $record?->submissions()->submitted()->exists())
                ->relationship()
                ->reorderable()
                ->orderColumn('sort')
                ->columnSpanFull(),
            Section::make('Appearance')
                ->schema([
                    ColorSelect::make('primary_color'),
                    Select::make('rounding')
                        ->options(Rounding::class),
                ])
                ->columns(),
        ];
    }

    public function fieldBuilder(): RichEditor
    {
        return RichEditor::make('content')
            ->customBlocks(FormFieldBlockRegistry::get())
            ->toolbarButtons([
                ['bold', 'italic', 'small'],
                ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'bulletList', 'orderedList', 'horizontalRule'],
                ['link', 'grid'],
                ['customBlocks'],
            ])
            ->activePanel('customBlocks')
            ->json()
            ->placeholder('Drag blocks here to build your service request form')
            ->hiddenLabel()
            ->saveRelationshipsUsing(function (RichEditor $component, ServiceRequestForm | ServiceRequestFormStep $record) {
                if ($component->isDisabled()) {
                    return;
                }

                $serviceRequestForm = $record instanceof ServiceRequestForm ? $record : $record->submissible;
                $serviceRequestFormStep = $record instanceof ServiceRequestFormStep ? $record : null;

                assert($serviceRequestForm instanceof ServiceRequestForm);

                ServiceRequestFormField::query()
                    ->whereBelongsTo($serviceRequestForm, 'submissible')
                    ->when($serviceRequestFormStep, fn (EloquentBuilder $query) => $query->whereBelongsTo($serviceRequestFormStep, 'step'))
                    ->delete();

                $state = $component->getState();
                $content = is_array($state) ? $state : ['type' => 'doc', 'content' => []];

                $content['content'] = $this->saveFieldsFromComponents(
                    $serviceRequestForm,
                    $content['content'] ?? [],
                    $serviceRequestFormStep,
                );

                $record->content = $content;
                $record->save();
            })
            ->dehydrated(false)
            ->columnSpanFull()
            ->extraInputAttributes(['style' => 'min-height: 12rem;']);
    }

    public function saveFieldsFromComponents(ServiceRequestForm $serviceRequestForm, array $components, ?ServiceRequestFormStep $serviceRequestFormStep): array
    {
        foreach ($components as $componentKey => $component) {
            if (array_key_exists('content', $component)) {
                $components[$componentKey]['content'] = $this->saveFieldsFromComponents($serviceRequestForm, $component['content'], $serviceRequestFormStep);

                continue;
            }

            if ($component['type'] !== 'customBlock') {
                continue;
            }

            $blockType = $component['attrs']['id'] ?? null;
            $config = $component['attrs']['config'] ?? [];
            $fieldId = $config['fieldId'] ?? null;
            $label = $config['label'] ?? $blockType;
            $isRequired = $config['isRequired'] ?? false;

            $fieldConfig = $config;
            unset($fieldConfig['label'], $fieldConfig['isRequired'], $fieldConfig['fieldId']);

            /** @var ServiceRequestFormField $field */
            $field = $serviceRequestForm->fields()->findOrNew($fieldId);
            $field->step()->associate($serviceRequestFormStep);
            $field->label = $label;
            $field->is_required = $isRequired;
            $field->type = $blockType;
            $field->config = $fieldConfig;
            $field->save();

            $components[$componentKey]['attrs']['config']['fieldId'] = $field->id;
        }

        return $components;
    }

    protected function afterCreate(): void
    {
        $this->clearFormContentForWizard();
    }

    protected function afterSave(): void
    {
        $this->clearFormContentForWizard();
    }

    protected function clearFormContentForWizard(): void
    {
        /** @var ServiceRequestForm $record */
        $record = $this->record;

        if ($record->is_wizard) {
            $record->content = null;
            $record->save();

            return;
        }

        $record->steps()->delete();
    }
}
