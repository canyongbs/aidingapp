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

namespace App\Filament\Pages;

use AidingApp\Engagement\Filament\Resources\EmailTemplates\EmailTemplateResource;
use AidingApp\Engagement\Models\EmailTemplate;
use App\Enums\CommunicationSettingsTab;
use App\Enums\NavigationGroup;
use App\Settings\NotificationSettings;
use CanyonGBS\Common\Filament\Forms\Components\ColorSelect;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\EmbeddedTable;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Url;
use UnitEnum;

/**
 * @property-read Schema $notificationsForm
 */
class ManageCommunicationSettings extends Page implements HasActions, HasForms, HasTable
{
    use InteractsWithActions;
    use InteractsWithForms;
    use InteractsWithTable;

    protected static string | UnitEnum | null $navigationGroup = NavigationGroup::Settings;

    protected static ?string $navigationLabel = 'Communication Settings';

    protected static ?string $title = 'Communication Settings';

    protected static ?int $navigationSort = 80;

    #[Url(as: 'tab')]
    public string $activeTab = 'notifications';

    /** @var array<string, mixed> */
    public ?array $notificationsData = [];

    public static function canAccess(): bool
    {
        return collect(CommunicationSettingsTab::cases())
            ->contains(fn (CommunicationSettingsTab $tab): bool => $tab->canView());
    }

    public function mount(): void
    {
        if (! CommunicationSettingsTab::tryFrom($this->activeTab)?->canView()) {
            $firstVisibleTab = collect(CommunicationSettingsTab::cases())
                ->first(fn (CommunicationSettingsTab $tab): bool => $tab->canView());

            if ($firstVisibleTab instanceof CommunicationSettingsTab) {
                $this->activeTab = $firstVisibleTab->value;
            }
        }

        $this->fillForms();
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make()
                    ->livewireProperty('activeTab')
                    ->tabs([
                        CommunicationSettingsTab::Notifications->value => Tab::make(CommunicationSettingsTab::Notifications->getLabel())
                            ->visible(fn (): bool => CommunicationSettingsTab::Notifications->canView())
                            ->schema([
                                $this->makeFormContentComponent('notificationsForm', 'saveNotifications'),
                            ]),
                        CommunicationSettingsTab::EmailTemplates->value => Tab::make(CommunicationSettingsTab::EmailTemplates->getLabel())
                            ->visible(fn (): bool => CommunicationSettingsTab::EmailTemplates->canView())
                            ->schema([
                                EmbeddedTable::make(),
                            ]),
                    ]),
            ]);
    }

    public function notificationsForm(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                TextInput::make('from_name')
                    ->string()
                    ->maxLength(150)
                    ->autocomplete(false),
                ColorSelect::make('primary_color'),
                SpatieMediaLibraryFileUpload::make('logo')
                    ->disk('s3-public')
                    ->collection('logo')
                    ->visibility('public')
                    ->model(NotificationSettings::getSettingsPropertyModel('notifications.logo'))
                    ->image()
                    ->maxSize(10240),
            ])
            ->disabled(! $this->canUpdate())
            ->statePath('notificationsData');
    }

    public function saveNotifications(): void
    {
        if (! $this->canUpdate()) {
            return;
        }

        DB::transaction(function (): void {
            $settings = app(NotificationSettings::class);

            $settings->fill($this->notificationsForm->getState());
            $settings->save();
        });

        $this->sendSavedNotification();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(EmailTemplate::query())
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('description'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('New email template')
                    ->url(fn (): string => EmailTemplateResource::getUrl('create'))
                    ->visible(fn (): bool => EmailTemplateResource::canCreate()),
            ])
            ->recordActions([
                EditAction::make()
                    ->url(fn (EmailTemplate $record): string => EmailTemplateResource::getUrl('edit', ['record' => $record]))
                    ->visible(fn (EmailTemplate $record): bool => EmailTemplateResource::canEdit($record)),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->authorizeIndividualRecords('delete')
                        ->visible(fn (): bool => auth()->user()?->can('deleteAny', EmailTemplate::class) ?? false),
                ]),
            ]);
    }

    protected function fillForms(): void
    {
        $this->notificationsForm->fill(app(NotificationSettings::class)->toArray());
    }

    protected function makeFormContentComponent(string $schema, string $submitHandler): Component
    {
        return Form::make([EmbeddedSchema::make($schema)])
            ->id($schema)
            ->livewireSubmitHandler($submitHandler)
            ->footer([
                Actions::make([
                    Action::make($submitHandler)
                        ->label('Save')
                        ->submit($submitHandler)
                        ->visible(fn (): bool => $this->canUpdate()),
                ]),
            ]);
    }

    protected function canUpdate(): bool
    {
        return auth()->user()?->can('settings.*.update') ?? false;
    }

    protected function sendSavedNotification(): void
    {
        Notification::make()
            ->success()
            ->title(__('filament-spatie-laravel-settings-plugin::pages/settings-page.notifications.saved.title'))
            ->send();
    }
}
