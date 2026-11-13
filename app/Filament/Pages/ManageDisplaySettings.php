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

use App\Enums\NavigationGroup;
use App\Models\Tenant;
use App\Models\User;
use App\Settings\DisplaySettings;
use App\Settings\EmailSettings;
use App\Settings\PresenceSettings;
use CanyonGBS\Common\Filament\Forms\Components\TimezoneSelect;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Url;
use UnitEnum;

/**
 * @property-read Schema $datesAndTimesForm
 * @property-read Schema $emailForm
 * @property-read Schema $presenceForm
 */
class ManageDisplaySettings extends Page implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    protected static string | UnitEnum | null $navigationGroup = NavigationGroup::Settings;

    protected static ?string $navigationLabel = 'Display Settings';

    protected static ?string $title = 'Display Settings';

    protected static ?int $navigationSort = 110;

    #[Url(as: 'tab')]
    public string $activeTab = 'dates-and-times';

    /** @var array<string, mixed> */
    public ?array $datesAndTimesData = [];

    /** @var array<string, mixed> */
    public ?array $emailData = [];

    /** @var array<string, mixed> */
    public ?array $presenceData = [];

    public static function canAccess(): bool
    {
        $user = auth()->user();

        assert($user instanceof User);

        return $user->can('settings.view-any');
    }

    public function mount(): void
    {
        $this->fillForms();
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make()
                    ->livewireProperty('activeTab')
                    ->tabs([
                        'dates-and-times' => Tab::make('Dates and Times')
                            ->schema([
                                $this->makeFormContentComponent('datesAndTimesForm', 'saveDatesAndTimes'),
                            ]),
                        'email' => Tab::make('Email')
                            ->schema([
                                $this->makeFormContentComponent('emailForm', 'saveEmail'),
                            ]),
                        'presence' => Tab::make('Presence')
                            ->schema([
                                $this->makeFormContentComponent('presenceForm', 'savePresence'),
                            ]),
                    ]),
            ]);
    }

    public function datesAndTimesForm(Schema $schema): Schema
    {
        return $schema
            ->components([
                TimezoneSelect::make('timezone')
                    ->helperText('Default: ' . config('app.timezone')),
            ])
            ->disabled(! $this->canUpdate())
            ->statePath('datesAndTimesData');
    }

    public function emailForm(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('tenant_mail_from_name')
                    ->label('From Name')
                    ->string()
                    ->required()
                    ->maxLength(255),
                SpatieMediaLibraryFileUpload::make('header_logo')
                    ->collection('header_logo')
                    ->disk('s3-public')
                    ->visibility('public')
                    ->image()
                    ->maxSize(10240)
                    ->acceptedFileTypes([
                        'image/png',
                        'image/jpeg',
                        'image/jpg',
                        'image/webp',
                        'image/svg+xml',
                    ])
                    ->model(
                        EmailSettings::getSettingsPropertyModel('email.header_logo'),
                    )
                    ->columnSpanFull(),
                Grid::make(2)
                    ->schema([
                        ColorPicker::make('background_color')
                            ->regex('/^#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})\b$/'),
                        ColorPicker::make('h1_text_color')
                            ->regex('/^#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})\b$/'),
                    ]),
                Grid::make(2)
                    ->schema([
                        ColorPicker::make('h2_text_color')
                            ->regex('/^#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})\b$/'),
                        ColorPicker::make('paragraph_text_color')
                            ->regex('/^#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})\b$/'),
                    ]),
                RichEditor::make('footer')
                    ->label('Footer text')
                    ->columnSpanFull()
                    ->toolbarButtons(['bold', 'italic', 'underline', 'link'])
                    ->json(),
            ])
            ->disabled(! $this->canUpdate())
            ->statePath('emailData');
    }

    public function presenceForm(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('active_threshold')
                    ->label('Active threshold')
                    ->helperText('Minutes of inactivity before a user or contact is considered idle.')
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(fn (Get $get): int|float => ($get('idle_threshold') ?? 2) - 1)
                    ->required()
                    ->live(onBlur: true)
                    ->suffix('minutes'),
                TextInput::make('idle_threshold')
                    ->label('Idle threshold')
                    ->helperText('Minutes of inactivity before a user or contact is considered inactive.')
                    ->numeric()
                    ->minValue(fn (Get $get): int|float => ($get('active_threshold') ?? 0) + 1)
                    ->maxValue(fn (Get $get): int|float => ($get('inactive_threshold') ?? 2) - 1)
                    ->required()
                    ->live(onBlur: true)
                    ->suffix('minutes'),
                TextInput::make('inactive_threshold')
                    ->label('Inactive threshold')
                    ->helperText('Minutes of inactivity before a user or contact is considered offline.')
                    ->numeric()
                    ->minValue(fn (Get $get): int|float => ($get('idle_threshold') ?? 0) + 1)
                    ->required()
                    ->live(onBlur: true)
                    ->suffix('minutes'),
            ])
            ->disabled(! $this->canUpdate())
            ->statePath('presenceData');
    }

    public function saveDatesAndTimes(): void
    {
        if (! $this->canUpdate()) {
            return;
        }

        DB::transaction(function (): void {
            $settings = app(DisplaySettings::class);

            $settings->fill($this->datesAndTimesForm->getState());
            $settings->save();
        });

        $this->sendSavedNotification();
    }

    public function saveEmail(): void
    {
        if (! $this->canUpdate()) {
            return;
        }

        DB::transaction(function (): void {
            $data = $this->mutateEmailDataBeforeSave($this->emailForm->getState());

            $settings = app(EmailSettings::class);

            $settings->fill($data);
            $settings->save();
        });

        $this->sendSavedNotification();
    }

    public function savePresence(): void
    {
        if (! $this->canUpdate()) {
            return;
        }

        DB::transaction(function (): void {
            $settings = app(PresenceSettings::class);

            $settings->fill($this->presenceForm->getState());
            $settings->save();
        });

        $this->sendSavedNotification();
    }

    protected function fillForms(): void
    {
        $this->datesAndTimesForm->fill(app(DisplaySettings::class)->toArray());
        $this->emailForm->fill($this->mutateEmailDataBeforeFill(app(EmailSettings::class)->toArray()));
        $this->presenceForm->fill(app(PresenceSettings::class)->toArray());
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

    /**
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>
     */
    protected function mutateEmailDataBeforeFill(array $data): array
    {
        $tenant = Tenant::current();

        if ($tenant) {
            $data['tenant_mail_from_name'] = $tenant->config->mail->fromName;
        }

        return $data;
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>
     */
    protected function mutateEmailDataBeforeSave(array $data): array
    {
        if (filled($data['tenant_mail_from_name'] ?? null)) {
            $tenant = Tenant::current();

            throw_unless($tenant);

            $tenant->config->mail->fromName = $data['tenant_mail_from_name'];

            $tenant->saveOrFail();
        }

        unset($data['tenant_mail_from_name']);

        return $data;
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
