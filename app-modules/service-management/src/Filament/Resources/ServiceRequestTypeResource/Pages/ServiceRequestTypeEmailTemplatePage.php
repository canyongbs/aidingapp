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
use AidingApp\ServiceManagement\Models\ServiceRequestTypeEmailTemplate;
use App\Concerns\EditPageRedirection;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Pages\EditRecord;
use FilamentTiptapEditor\TiptapEditor;
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
        return $form
            ->schema([
                Tabs::make('Roles')
                    ->statePath('activeRole')
                    ->persistTab()
                    ->id('role-tabs')
                    ->tabs(array_map(
                        fn (ServiceRequestTypeEmailTemplateRole $role) => Tab::make($role->getLabel())
                            ->schema($this->emailTemplateSchema($role->value))
                            ->statePath($role->value),
                        ServiceRequestTypeEmailTemplateRole::cases()
                    ))
                    ->columnSpanFull(),
            ]);
    }

    public function save(bool $shouldRedirect = true, bool $shouldSendSavedNotification = true): void
    {
        $data = $this->form->getState();

        $filtered = array_filter(
            $data['activeRole'] ?? [],
            fn ($templateData) => ! is_null($templateData['subject']) && ! is_null($templateData['body'])
        );

        foreach (ServiceRequestTypeEmailTemplateRole::cases() as $role) {
            if (! array_key_exists($role->value, $filtered)) {
                continue;
            }

            $templateData = $filtered[$role->value];

            if ($templateData) {
                ServiceRequestTypeEmailTemplate::updateOrCreate(
                    [
                        'service_request_type_id' => $this->getRecord()->id,
                        'type' => $this->type,
                        'role' => $role,
                    ],
                    $templateData,
                );
            }
        }

        $this->getSavedNotification()->send();
    }

    protected function emailTemplateSchema(string $role): array
    {
        return [
            TiptapEditor::make('subject')
                ->label('Subject')
                ->placeholder('Enter the email subject here...')
                ->required(fn (Get $get) => $get('activeRole') === $role)
                ->extraInputAttributes(['style' => 'min-height: 2rem; overflow-y:none;'])
                ->disableToolbarMenus()
                ->mergeTags(['created', 'updated', 'status', 'assigned to', 'title', 'type'])
                ->showMergeTagsInBlocksPanel(false)
                ->helperText('You may use “merge tags” to substitute information about a service request into your subject line. Insert a “{{“ in the subject line field to see a list of available merge tags'),

            TiptapEditor::make('body')
                ->label('Body')
                ->placeholder('Enter the email body here...')
                ->required(fn (Get $get) => $get('activeRole') === $role)
                ->extraInputAttributes(['style' => 'min-height: 12rem;'])
                ->mergeTags(['created', 'updated', 'status', 'assigned to', 'title', 'type'])
                ->columnSpanFull(),
        ];
    }

    protected function fillForm(): void
    {
        $templates = $this->getRecord()
            ?->templates()
            ->where('type', $this->type)
            ->get()
            ->keyBy(fn ($template) => $template->role);

        $state = [];

        foreach (ServiceRequestTypeEmailTemplateRole::cases() as $role) {
            if ($template = $templates[$role->value] ?? null) {
                $state['activeRole'][$role->value] = $template->only(['subject', 'body']);
            }
        }

        $this->form->fill($state);
    }

    #[Computed]
    protected function template(): ?ServiceRequestTypeEmailTemplate
    {
        return $this->getRecord()?->templates()->where('type', $this->type)->first();
    }
}
