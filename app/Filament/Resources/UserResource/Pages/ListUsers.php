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

namespace App\Filament\Resources\UserResource\Pages;

use AidingApp\Authorization\Enums\LicenseType;
use AidingApp\Authorization\Models\License;
use App\Filament\Imports\UserImporter;
use App\Filament\Resources\UserResource;
use App\Filament\Resources\UserResource\Actions\AssignLicensesBulkAction;
use App\Filament\Resources\UserResource\Actions\AssignRolesBulkAction;
use App\Filament\Resources\UserResource\Actions\AssignTeamBulkAction;
use App\Filament\Tables\Columns\IdColumn;
use App\Models\User;
use Filament\Actions\CreateAction;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use STS\FilamentImpersonate\Tables\Actions\Impersonate;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected ?string $heading = 'Users';

    public function table(Table $table): Table
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

    public function getSubheading(): string | Htmlable | null
    {
        // TODO: Either remove or change to show all possible seats

        //return new HtmlString(view('crm-seats', [
        //    'count' => User::count(),
        //    'max' => app(LicenseSettings::class)->data->limits->crmSeats,
        //])->render());

        return null;
    }

    protected function getHeaderActions(): array
    {
        return [
            ImportAction::make()
                ->importer(UserImporter::class)
                ->authorize('import', User::class),
            CreateAction::make(),
        ];
    }
}
