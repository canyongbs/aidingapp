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

namespace App\Filament\Resources;

use AidingApp\Authorization\Enums\LicenseType;
use AidingApp\Authorization\Models\License;
use App\Filament\Forms\Components\Licenses;
use App\Filament\Resources\UserResource\Actions\AssignLicensesBulkAction;
use App\Filament\Resources\UserResource\Actions\AssignRolesBulkAction;
use App\Filament\Resources\UserResource\Actions\AssignTeamBulkAction;
use App\Filament\Resources\UserResource\Pages\CreateUser;
use App\Filament\Resources\UserResource\Pages\EditUser;
use App\Filament\Resources\UserResource\Pages\ListUsers;
use App\Filament\Resources\UserResource\Pages\ViewUser;
use App\Filament\Resources\UserResource\RelationManagers\PermissionsRelationManager;
use App\Filament\Resources\UserResource\RelationManagers\RolesRelationManager;
use App\Filament\Tables\Columns\IdColumn;
use App\Models\User;
use App\Rules\EmailNotInUseOrSoftDeleted;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use STS\FilamentImpersonate\Tables\Actions\Impersonate;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationGroup = 'User Management';

    protected static ?int $navigationSort = 10;

    protected static ?string $navigationLabel = 'Users';

    protected static ?string $breadcrumb = 'Users';

    protected static ?string $modelLabel = 'User';

    public static function form(Form $form): Form
    {
        return $form
            ->disabled(false)
            ->schema([
                Section::make()
                    ->columns()
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label('Email address')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->rules([
                                new EmailNotInUseOrSoftDeleted(),
                            ]),
                        TextInput::make('job_title')
                            ->string()
                            ->maxLength(255),
                        Toggle::make('is_external')
                            ->label('User can only log in via a social provider.'),
                        TextInput::make('created_at')
                            ->formatStateUsing(fn ($state) => Carbon::parse($state)->format(config('project.datetime_format') ?? 'Y-m-d H:i:s'))
                            ->disabled(),
                        TextInput::make('updated_at')
                            ->formatStateUsing(fn ($state) => Carbon::parse($state)->format(config('project.datetime_format') ?? 'Y-m-d H:i:s'))
                            ->disabled(),
                    ])
                    ->disabled(fn (string $operation) => $operation === 'view'),
                Licenses::make()
                    ->hidden(fn (?User $record) => is_null($record))
                    ->disabled(function () {
                        /** @var User $user */
                        $user = auth()->user();

                        return $user->cannot('create', License::class);
                    }),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                IdColumn::make(),
                TextColumn::make('name'),
                TextColumn::make('email')
                    ->label('Email address'),
                TextColumn::make('job_title'),
                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime(config('project.datetime_format') ?? 'Y-m-d H:i:s')
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->label('Updated At')
                    ->dateTime(config('project.datetime_format') ?? 'Y-m-d H:i:s')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('teams')
                    ->label('Team')
                    ->relationship('teams', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload(),
                SelectFilter::make('licenses')
                    ->label('License')
                    ->options(
                        fn (): array => [
                            '' => [
                                'no_assigned_license' => 'No Assigned License',
                            ],
                            'Licenses' => collect(LicenseType::cases())
                                ->mapWithKeys(fn ($case) => [$case->value => $case->name])
                                ->toArray(),
                        ]
                    )
                    ->getSearchResultsUsing(fn (string $search): array => ['Licenses' => collect(LicenseType::cases())->filter(fn ($case) => str_contains(strtolower($case->name), strtolower($search)))->mapWithKeys(fn ($case) => [$case->value => $case->name])->toArray()])
                    ->query(
                        function (Builder $query, array $data) {
                            if (empty($data['values'])) {
                                return;
                            }

                            $query->when(in_array('no_assigned_license', $data['values']), function ($query) {
                                $query->whereDoesntHave('licenses');
                            })
                                ->{in_array('no_assigned_license', $data['values']) ? 'orWhereHas' : 'whereHas'}('licenses', function ($query) use ($data) {
                                    $query->whereIn('type', array_filter($data['values'], fn ($value) => $value !== 'no_assigned_license'));
                                });
                        }
                    )
                    ->multiple()
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Impersonate::make(),
                ViewAction::make(),
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    AssignTeamBulkAction::make()
                        ->visible(function (User $record): bool {
                            /** @var User $user */
                            $user = auth()->user();

                            return $user->can('update', $record);
                        }),
                    AssignLicensesBulkAction::make()
                        ->visible(fn () => auth()->user()->can('create', License::class)),
                    AssignRolesBulkAction::make()
                        ->visible(fn () => auth()->user()->can('user.*.update', User::class)),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RolesRelationManager::class,
            PermissionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'view' => ViewUser::route('/{record}'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }
}
