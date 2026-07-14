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

namespace App\Filament\Resources\Users\Pages;

use AidingApp\Contact\Models\ContactType;
use AidingApp\Contact\Services\ManagedContactService;
use App\Features\ManagedContactFeature;
use App\Filament\Resources\Users\UserResource;
use App\Models\Authenticatable;
use App\Models\User;
use App\Notifications\SetPasswordNotification;
use App\Rules\EmailNotInUseOrSoftDeleted;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use STS\FilamentImpersonate\Actions\Impersonate;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected bool $isManagedContact = false;

    protected ?string $managedContactTypeId = null;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->columns()
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->disabled(fn (User $record) => $record->isAdmin()),
                        TextInput::make('email')
                            ->label('Email address')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->rules([
                                new EmailNotInUseOrSoftDeleted($this->getRecord()->getKey()),
                            ])
                            ->disabled(fn (User $record) => $record->isAdmin()),
                        TextInput::make('job_title')
                            ->string()
                            ->maxLength(255)
                            ->disabled(fn (User $record) => $record->isAdmin()),
                        PhoneInput::make('work_number')
                            ->label('Work Number')
                            ->nullable(),
                        TextInput::make('work_extension')
                            ->label('Work Extension')
                            ->nullable()
                            ->numeric(),
                        PhoneInput::make('mobile')
                            ->nullable(),
                        Grid::make(2)
                            ->visible(fn (): bool => ManagedContactFeature::active())
                            ->schema([
                                Toggle::make('is_managed_contact')
                                    ->label('Managed Contact')
                                    ->helperText('Creates a linked, read-only contact record for the self-service portal that stays in sync with this user.')
                                    ->live(),
                                Select::make('managed_contact_type_id')
                                    ->label('Contact Type')
                                    ->options(fn (): array => ContactType::query()->pluck('name', 'id')->all())
                                    ->searchable()
                                    ->preload()
                                    ->required(fn (Get $get): bool => (bool) $get('is_managed_contact'))
                                    ->visible(fn (Get $get): bool => (bool) $get('is_managed_contact')),
                            ])
                            ->columnSpanFull(),
                        Toggle::make('is_external')
                            ->label('User can only log in via a social provider.')
                            ->columnSpanFull()
                            ->disabled(fn (User $record) => $record->isAdmin()),
                        TextInput::make('created_at')
                            ->formatStateUsing(fn ($state) => Carbon::parse($state)->format('M j, Y g:i a (T)'))
                            ->disabled(),
                        TextInput::make('updated_at')
                            ->formatStateUsing(fn ($state) => Carbon::parse($state)->format('M j, Y g:i a (T)'))
                            ->disabled(),
                    ]),
                Section::make('Department')
                    ->schema([
                        Select::make('department_id')
                            ->hiddenLabel()
                            ->relationship('department', 'name'),
                    ])
                    ->hidden(fn (?User $record) => (bool) $record?->hasRole(Authenticatable::SUPER_ADMIN_ROLE)),
            ]);
    }

    /**
     * @param  array<string, mixed>  $data
     *
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        if (! ManagedContactFeature::active()) {
            return $data;
        }

        /** @var User $user */
        $user = $this->getRecord();

        $managedContact = $user->managedContact()->first();

        $data['is_managed_contact'] = ! is_null($managedContact);
        $data['managed_contact_type_id'] = $managedContact?->type_id;

        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     *
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->isManagedContact = (bool) ($data['is_managed_contact'] ?? false);
        $this->managedContactTypeId = $data['managed_contact_type_id'] ?? null;

        unset($data['is_managed_contact'], $data['managed_contact_type_id']);

        return $data;
    }

    protected function afterSave(): void
    {
        if (! ManagedContactFeature::active()) {
            return;
        }

        /** @var User $user */
        $user = $this->getRecord();

        if ($this->isManagedContact && filled($this->managedContactTypeId)) {
            app(ManagedContactService::class)->enable($user, $this->managedContactTypeId);

            return;
        }

        app(ManagedContactService::class)->disable($user);
    }

    protected function getHeaderActions(): array
    {
        /** @var User $user */
        $user = $this->getRecord();

        return [
            Impersonate::make()
                ->record($user),
            Action::make('resetPassword')
                ->color('gray')
                ->requiresConfirmation()
                ->modalDescription('This will remove the user\'s current password and send them an email with a link to set a new password.')
                ->hidden($user->is_external)
                ->action(function () use ($user) {
                    $user->password = null;
                    $user->save();

                    $user->notify(new SetPasswordNotification());

                    Notification::make()
                        ->title('The password has been reset')
                        ->success()
                        ->send();
                }),
            DeleteAction::make(),
        ];
    }
}
