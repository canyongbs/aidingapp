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

namespace AidingApp\ServiceManagement\Filament\Tables;

use AidingApp\ServiceManagement\Models\Scopes\ManagesServiceRequestType;
use App\Models\User;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ManagersTable
{
    /**
     * @return Builder<User>
     */
    public static function query(string $serviceRequestTypeId, ?string $excludeUserId = null): Builder
    {
        return User::query()
            ->tap(new ManagesServiceRequestType($serviceRequestTypeId))
            ->when($excludeUserId, function (Builder $query) use ($excludeUserId): void {
                $query->whereKeyNot($excludeUserId);
            });
    }

    public static function configure(Table $table): Table
    {
        return $table
            ->query(function () use ($table): Builder {
                $arguments = $table->getArguments();

                return static::query(
                    $arguments['serviceRequestTypeId'],
                    $arguments['excludeUserId'] ?? null,
                );
            })
            ->columns([
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->icon(fn (User $record) => $record->presenceStatus()->getIcon())
                    ->iconColor(fn (User $record) => $record->presenceStatus()->getColor())
                    ->tooltip(fn (User $record) => $record->presenceStatus()->getLabel())
                    ->extraAttributes(fn (User $record): array => ['aria-label' => $record->name . ' (' . $record->presenceStatus()->getLabel() . ')']),
                TextColumn::make('department.name')
                    ->label('Department')
                    ->searchable(),
                TextColumn::make('job_title')
                    ->label('Job Title')
                    ->searchable(),
            ])
            ->filters([
                SelectFilter::make('department')
                    ->label('Department')
                    ->relationship('department', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload(),
            ])
            ->paginationPageOptions([5])
            ->defaultPaginationPageOption(5);
    }
}
