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

namespace App\Filament\Pages;

use AidingApp\Authorization\Enums\LicenseType;
use App\Models\User;
use Exception;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Facades\Filament;
use Filament\Forms\Components\Actions\Action as FormAction;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Pages\Page;
use Filament\Support\Enums\Alignment;
use Filament\Support\Exceptions\Halt;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Tapp\FilamentTimezoneField\Forms\Components\TimezoneSelect;

/**
 * @property Form $form
 */
class EditProfile extends Page
{
    use InteractsWithFormActions;

    protected static string $view = 'filament.pages.edit-profile';

    protected static ?string $slug = 'profile';

    protected static bool $shouldRegisterNavigation = false;

    public ?array $data = [];

    //TODO: I feel like a lot of these could be refactored into a settings file instead of adding them directly to the user migration.
    public function form(Form $form): Form
    {
        /** @var User $user */
        $user = auth()->user();

        return $form
            ->schema([
                Section::make('Public Profile')
                    ->aside()
                    ->schema([
                        Toggle::make('has_enabled_public_profile')
                            ->label('Enable public profile')
                            ->live()
                            ->lockedWithoutAnyLicenses(user: auth()->user(), licenses: [LicenseType::RecruitmentCrm]),
                        TextInput::make('public_profile_slug')
                            ->label('Url')
                            ->visible(fn (Get $get) => $get('has_enabled_public_profile'))
                            //TODO: default doesn't work for some reason
                            ->afterStateHydrated(fn (TextInput $component, $state) => $component->state($state ?? str($user->name)->lower()->slug('')))
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->required()
                            //The id doesn't matter because we're just using it to generate a piece of a url
                            ->prefix(str(route('users.profile.view.public', ['user' => -1]))->beforeLast('/')->append('/'))
                            ->suffixAction(
                                FormAction::make('viewPublicProfile')
                                    ->url(fn () => route('users.profile.view.public', ['user' => $user->public_profile_slug]))
                                    ->icon('heroicon-m-arrow-top-right-on-square')
                                    ->openUrlInNewTab()
                                    ->visible(fn () => $user->public_profile_slug),
                            )
                            ->live(),
                    ]),
                Section::make('Profile Information')
                    ->description('This information is visible to other users on your profile page, if you choose to make it visible.')
                    ->aside()
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('avatar')
                            ->label('Avatar')
                            ->visibility('private')
                            ->disk('s3')
                            ->collection('avatar')
                            ->hidden($user->is_external)
                            ->avatar(),
                        Placeholder::make('external_avatar')
                            ->label('Avatar')
                            ->content('Your authentication into this application is managed through single sign on (SSO). Please update your profile picture in your source authentication system and then logout and login here to persist that update into this application.')
                            ->visible($user->is_external),
                        $this->getNameFormComponent()
                            ->disabled($user->is_external),
                        RichEditor::make('bio')
                            ->label('Personal Bio')
                            ->toolbarButtons(['bold', 'italic', 'underline', 'link', 'blockquote', 'bulletList', 'orderedList'])
                            ->hint(fn (Get $get): string => $get('is_bio_visible_on_profile') ? 'Visible on profile' : 'Not visible on profile'),
                        Checkbox::make('is_bio_visible_on_profile')
                            ->label('Show Bio on profile')
                            ->live()
                            ->lockedWithoutAnyLicenses(user: auth()->user(), licenses: [LicenseType::RecruitmentCrm]),
                        TextInput::make('phone_number')
                            ->label('Contact phone number')
                            ->integer()
                            ->hint(fn (Get $get): string => $get('is_phone_number_visible_on_profile') ? 'Visible on profile' : 'Not visible on profile'),
                        Checkbox::make('is_phone_number_visible_on_profile')
                            ->label('Show phone number on profile')
                            ->live()
                            ->lockedWithoutAnyLicenses(user: auth()->user(), licenses: [LicenseType::RecruitmentCrm]),
                        Select::make('pronouns_id')
                            ->relationship('pronouns', 'label')
                            ->hint(fn (Get $get): string => $get('are_pronouns_visible_on_profile') ? 'Visible on profile' : 'Not visible on profile'),
                        Checkbox::make('are_pronouns_visible_on_profile')
                            ->label('Show Pronouns on profile')
                            ->live()
                            ->lockedWithoutAnyLicenses(user: auth()->user(), licenses: [LicenseType::RecruitmentCrm]),
                        Placeholder::make('teams')
                            ->label(str('Team'))
                            ->content($user->team?->name)
                            ->hidden(blank($user->team))
                            ->hint(fn (Get $get): string => $get('are_teams_visible_on_profile') ? 'Visible on profile' : 'Not visible on profile'),
                        //TODO: Right now this is not passed to the frontend
                        Checkbox::make('are_teams_visible_on_profile')
                            ->label('Show ' . str('team')->ucfirst() . ' on profile')
                            ->hidden(blank($user->team))
                            ->live(),
                        Placeholder::make('division')
                            ->content($user->team?->division?->name)
                            ->hidden(! $user->team?->division()->exists())
                            ->hint(fn (Get $get): string => $get('is_division_visible_on_profile') ? 'Visible on profile' : 'Not visible on profile'),
                        //TODO: Right now this is not passed to the frontend
                        Checkbox::make('is_division_visible_on_profile')
                            ->label('Show Division on profile')
                            ->hidden(! $user->team?->first()?->division()->exists())
                            ->live(),
                    ]),
                Section::make('Account Information')
                    ->description("Update your account's information.")
                    ->aside()
                    ->schema([
                        $this->getEmailFormComponent()
                            ->disabled($user->is_external),
                        Checkbox::make('is_email_visible_on_profile')
                            ->label('Show Email on profile')
                            ->live()
                            ->lockedWithoutAnyLicenses(user: auth()->user(), licenses: [LicenseType::RecruitmentCrm]),
                        $this->getPasswordFormComponent()
                            ->hidden($user->is_external),
                        $this->getPasswordConfirmationFormComponent()
                            ->hidden($user->is_external),
                        TimezoneSelect::make('timezone')
                            ->required()
                            ->selectablePlaceholder(false),
                    ]),
                Section::make('Working Hours')
                    ->aside()
                    ->schema([
                        Toggle::make('working_hours_are_enabled')
                            ->label('Set Working Hours')
                            ->live()
                            ->hint(fn (Get $get): string => $get('are_working_hours_visible_on_profile') ? 'Visible on profile' : 'Not visible on profile')
                            ->lockedWithoutAnyLicenses(user: auth()->user(), licenses: [LicenseType::RecruitmentCrm]),
                        Checkbox::make('are_working_hours_visible_on_profile')
                            ->label('Show Working Hours on profile')
                            ->visible(fn (Get $get) => $get('working_hours_are_enabled'))
                            ->live(),
                        Section::make('Days')
                            ->schema($this->getHoursForDays('working_hours'))
                            ->visible(fn (Get $get) => $get('working_hours_are_enabled')),
                    ]),
                Section::make('Office Hours')
                    ->aside()
                    ->schema([
                        Toggle::make('office_hours_are_enabled')
                            ->label('Enable Office Hours')
                            ->live()
                            ->lockedWithoutAnyLicenses(user: auth()->user(), licenses: [LicenseType::RecruitmentCrm]),
                        Section::make('Days')
                            ->schema($this->getHoursForDays('office_hours'))
                            ->visible(fn (Get $get) => $get('office_hours_are_enabled')),
                    ]),
                Section::make('Out of Office')
                    ->aside()
                    ->schema([
                        Grid::make()
                            ->columns([
                                'sm' => 1,
                                'md' => 1,
                                'lg' => 1,
                                'xl' => 2,
                                '2xl' => 2,
                            ])
                            ->schema([
                                Toggle::make('out_of_office_is_enabled')
                                    ->columnSpanFull()
                                    ->label('Enable Out of Office')
                                    ->live(),
                                DateTimePicker::make('out_of_office_starts_at')
                                    ->columnSpan(1)
                                    ->label('Start')
                                    ->required()
                                    ->visible(fn (Get $get) => $get('out_of_office_is_enabled')),
                                DateTimePicker::make('out_of_office_ends_at')
                                    ->columnSpan(1)
                                    ->label('End')
                                    ->required()
                                    ->visible(fn (Get $get) => $get('out_of_office_is_enabled')),
                            ]),
                    ]),
            ]);
    }

    public function mount(): void
    {
        $this->fillForm();
    }

    public function getUser(): Authenticatable|Model
    {
        $user = Filament::auth()->user();

        if (! $user instanceof Model) {
            throw new Exception('The authenticated user object must be an Eloquent model to allow the profile page to update it.');
        }

        return $user;
    }

    public function save(): void
    {
        try {
            $this->callHook('beforeValidate');

            $data = $this->form->getState();

            $this->callHook('afterValidate');

            $data = $this->mutateFormDataBeforeSave($data);

            $this->callHook('beforeSave');

            $this->handleRecordUpdate($this->getUser(), $data);

            $this->callHook('afterSave');
        } catch (Halt $exception) {
            return;
        }

        if (request()->hasSession() && array_key_exists('password', $data)) {
            request()->session()->put([
                'password_hash_' . Filament::getAuthGuard() => $data['password'],
            ]);
        }

        $this->data['password'] = null;
        $this->data['passwordConfirmation'] = null;

        $this->getSavedNotification()?->send();

        if ($redirectUrl = $this->getRedirectUrl()) {
            $this->redirect($redirectUrl);
        }
    }

    public function getFormActionsAlignment(): string
    {
        return Alignment::Start->value;
    }

    protected function fillForm(): void
    {
        $data = $this->getUser()->attributesToArray();

        $this->callHook('beforeFill');

        $data = $this->mutateFormDataBeforeFill($data);

        $this->form->fill($data);

        $this->callHook('afterFill');
    }

    /**
     * @param  array<string, mixed>  $data
     *
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     *
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->update($data);

        return $record;
    }

    protected function getSavedNotification(): ?Notification
    {
        $title = $this->getSavedNotificationTitle();

        if (blank($title)) {
            return null;
        }

        return Notification::make()
            ->success()
            ->title($this->getSavedNotificationTitle());
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return __('filament-panels::pages/auth/edit-profile.notifications.saved.title');
    }

    protected function getRedirectUrl(): ?string
    {
        return null;
    }

    protected function getNameFormComponent(): Component
    {
        return TextInput::make('name')
            ->label(__('filament-panels::pages/auth/edit-profile.form.name.label'))
            ->required()
            ->maxLength(255)
            ->autofocus();
    }

    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('email')
            ->label(__('filament-panels::pages/auth/edit-profile.form.email.label'))
            ->email()
            ->required()
            ->maxLength(255)
            ->unique(ignoreRecord: true)
            ->hint(fn (Get $get): string => $get('is_email_visible_on_profile') ? 'Visible on profile' : 'Not visible on profile');
    }

    protected function getPasswordFormComponent(): Component
    {
        return TextInput::make('password')
            ->label(__('filament-panels::pages/auth/edit-profile.form.password.label'))
            ->password()
            ->rule(Password::default())
            ->autocomplete('new-password')
            ->dehydrated(fn ($state): bool => filled($state))
            ->dehydrateStateUsing(fn ($state): string => Hash::make($state))
            ->live(debounce: 500)
            ->same('passwordConfirmation');
    }

    protected function getPasswordConfirmationFormComponent(): Component
    {
        return TextInput::make('passwordConfirmation')
            ->label(__('filament-panels::pages/auth/edit-profile.form.password_confirmation.label'))
            ->password()
            ->required()
            ->visible(fn (Get $get): bool => filled($get('password')))
            ->dehydrated(false);
    }

    /**
     * @return array<int | string, string | Form>
     */
    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        $this->getNameFormComponent(),
                        $this->getEmailFormComponent(),
                        $this->getPasswordFormComponent(),
                        $this->getPasswordConfirmationFormComponent(),
                    ])
                    ->operation('edit')
                    ->model($this->getUser())
                    ->statePath('data'),
            ),
        ];
    }

    /**
     * @return array<Action | ActionGroup>
     */
    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction(),
            $this->getCancelFormAction(),
        ];
    }

    protected function getCancelFormAction(): Action
    {
        return Action::make('cancel')
            ->label(__('filament-panels::pages/auth/edit-profile.actions.cancel.label'))
            ->url(filament()->getUrl())
            ->color('gray');
    }

    protected function getSaveFormAction(): Action
    {
        return Action::make('save')
            ->label(__('filament-panels::pages/auth/edit-profile.form.actions.save.label'))
            ->submit('save')
            ->keyBindings(['mod+s']);
    }

    protected function hasFullWidthFormActions(): bool
    {
        return false;
    }

    private function getHoursForDays(string $key): array
    {
        return collect([
            'sunday',
            'monday',
            'tuesday',
            'wednesday',
            'thursday',
            'friday',
            'saturday',
        ])->map(
            fn ($day) => Grid::make()
                ->columns([
                    'sm' => 1,
                    'md' => 1,
                    'lg' => 1,
                    'xl' => 3,
                    '2xl' => 3,
                ])
                ->schema([
                    Toggle::make("{$key}.{$day}.enabled")
                        ->label(str($day)->ucfirst())
                        ->inline(false)
                        ->live(),
                    TimePicker::make("{$key}.{$day}.starts_at")
                        ->required()
                        ->visible(fn (Get $get) => $get("{$key}.{$day}.enabled")),
                    TimePicker::make("{$key}.{$day}.ends_at")
                        ->required()
                        ->visible(fn (Get $get) => $get("{$key}.{$day}.enabled")),
                ])
        )->toArray();
    }
}
