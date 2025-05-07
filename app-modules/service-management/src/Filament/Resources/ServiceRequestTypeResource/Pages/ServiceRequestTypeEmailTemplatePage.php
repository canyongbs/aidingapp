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

namespace AidingApp\ServiceManagement\Filament\Resources\ServiceRequestTypeResource\Pages;

use AidingApp\ServiceManagement\Enums\ServiceRequestEmailTemplateType;
use AidingApp\ServiceManagement\Enums\ServiceRequestTypeEmailTemplateRole;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestTypeResource;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use AidingApp\ServiceManagement\Models\ServiceRequestTypeEmailTemplate;
use App\Concerns\EditPageRedirection;
use App\Features\ServiceRequestTypeEmailTemplateTabs;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;
use FilamentTiptapEditor\TiptapEditor;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;

/** @property-read ?ServiceRequestTypeEmailTemplate $template */
class ServiceRequestTypeEmailTemplatePage extends EditRecord
{
    use EditPageRedirection;

    protected static string $resource = ServiceRequestTypeResource::class;

    #[Locked]
    public ServiceRequestEmailTemplateType $type;

    public static ?string $navigationGroup = 'Email Templates';

    public function getRelationManagers(): array
    {
        // Needed to prevent Filament from loading the relation managers on this page.
        return [];
    }

    public function form(Form $form): Form
    {
        if (ServiceRequestTypeEmailTemplateTabs::active()) {
            return $form
                ->schema([
                    Tabs::make('Email template roles')
                        ->persistTab()
                        ->id('email-template-role-tabs')
                        ->tabs(array_map(
                            fn (ServiceRequestTypeEmailTemplateRole $role) => Tab::make($role->getLabel())
                                ->schema($this->getEmailTemplateFormSchema())
                                ->statePath($role->value),
                            ServiceRequestTypeEmailTemplateRole::cases()
                        ))
                        ->columnSpanFull(),
                ]);
        }

        return $form
            ->schema([
                Section::make()
                    ->columns()
                    ->schema([
                        TiptapEditor::make('subject')
                            ->label('Subject')
                            ->placeholder('Enter the email subject here...')
                            ->rules(['required'])
                            ->extraInputAttributes(['style' => 'min-height: 2rem; overflow-y:none;'])
                            ->disableToolbarMenus()
                            ->mergeTags([
                                'created',
                                'updated',
                                'status',
                                'assigned to',
                                'title',
                                'type',
                            ])
                            ->showMergeTagsInBlocksPanel(false)
                            ->helperText('You may use “merge tags” to substitute information about a service request into your subject line. Insert a “{{“ in the subject line field to see a list of available merge tags')
                            ->columnSpanFull(),
                        TiptapEditor::make('body')
                            ->label('Body')
                            ->placeholder('Enter the email body here...')
                            ->rules(['required'])
                            ->extraInputAttributes(['style' => 'min-height: 12rem;'])
                            ->mergeTags([
                                'created',
                                'updated',
                                'status',
                                'assigned to',
                                'title',
                                'type',
                            ])
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public function save(bool $shouldRedirect = true, bool $shouldSendSavedNotification = true): void
    {
        $data = $this->form->getState();

        /** @var ServiceRequestType $record */
        $record = $this->getRecord();

        if (! ServiceRequestTypeEmailTemplateTabs::active()) {
            if ($this->template) {
                $this->template->update($data);
            } else {
                $data['service_request_type_id'] = $record->getKey();
                $data['type'] = $this->type;

                $record->templates()->create($data);

                unset($this->template);
            }
        } else {
            foreach (ServiceRequestTypeEmailTemplateRole::cases() as $role) {
                $templateData = $data[$role->value] ?? null;

                if (
                    ! $templateData ||
                    (blank($templateData['subject']) && blank($templateData['body']))
                ) {
                    continue;
                }

                $template = ServiceRequestTypeEmailTemplate::firstOrNew([
                    'service_request_type_id' => $record->getKey(),
                    'type' => $this->type,
                    'role' => $role,
                ]);

                if (! $template->exists && (blank($templateData['subject']) || blank($templateData['body']))) {
                    continue;
                }

                $template->subject = $templateData['subject'] ?? $template->subject;
                $template->body = $templateData['body'] ?? $template->body;

                $template->save();
            }
        }

        $this->getSavedNotification()->send();
    }

    /** @return array<int, TiptapEditor> */
    protected function getEmailTemplateFormSchema(): array
    {
        return [
            TiptapEditor::make('subject')
                ->label('Subject')
                ->placeholder('Enter the email subject here...')
                ->extraInputAttributes(['style' => 'min-height: 2rem; overflow-y:none;'])
                ->disableToolbarMenus()
                ->mergeTags(['created', 'updated', 'status', 'assigned to', 'title', 'type'])
                ->showMergeTagsInBlocksPanel(false)
                ->helperText('You may use “merge tags” to substitute information about a service request into your subject line. Insert a “{{“ in the subject line field to see a list of available merge tags'),

            TiptapEditor::make('body')
                ->label('Body')
                ->placeholder('Enter the email body here...')
                ->extraInputAttributes(['style' => 'min-height: 12rem;'])
                ->mergeTags(['created', 'updated', 'status', 'assigned to', 'title', 'type'])
                ->columnSpanFull(),
        ];
    }

    protected function fillForm(): void
    {
        if (! ServiceRequestTypeEmailTemplateTabs::active()) {
            $this->form->fill($this->template?->only(['subject', 'body']));
        } else {
            /** @var ServiceRequestType $record */
            $record = $this->getRecord();

            /** @var Collection<int, ServiceRequestTypeEmailTemplate> $templates */
            $templates = $record
                ->templates()
                ->where('type', $this->type)
                ->get();

            /** @var Collection<string, ServiceRequestTypeEmailTemplate> $templates */
            $templates = $templates->keyBy(fn (ServiceRequestTypeEmailTemplate $template) => $template->role->value);

            $state = [];

            foreach (ServiceRequestTypeEmailTemplateRole::cases() as $role) {
                if ($template = $templates[$role->value] ?? null) {
                    $state[$role->value] = $template->only(['subject', 'body']);
                }
            }

            $this->form->fill($state);
        }
    }

    #[Computed]
    protected function template(): ?ServiceRequestTypeEmailTemplate
    {
        /** @var ServiceRequestType $record */
        $record = $this->getRecord();

        return $record->templates()->where('type', $this->type)->first();
    }
}
