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

namespace AidingApp\ServiceManagement\Filament\Resources\ServiceRequests\RelationManagers;

use AidingApp\ServiceManagement\Models\ServiceRequestAssignment;
use App\Models\SystemUser;
use App\Models\User;
use App\Settings\DisplaySettings;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Livewire\Attributes\On;

class AssignmentHistoryRelationManager extends RelationManager
{
    protected static string $relationship = 'assignments';

    protected static ?string $title = 'History';

    #[On('assignment-history-refresh')]
    public function onAssignmentHistoryRefresh(): void {}

    public function table(Table $table): Table
    {
        $timezone = app(DisplaySettings::class)->getTimezone();

        $userLine = function (?User $user): ?string {
            if (! $user) {
                return null;
            }

            $jobTitle = $user->job_title;
            $department = $user->department?->name;

            if ($jobTitle && $department) {
                return "{$jobTitle} ({$department})";
            }

            if ($jobTitle) {
                return $jobTitle;
            }

            if ($department) {
                return "({$department})";
            }

            return null;
        };

        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with([
                'user.department',
                'serviceRequestStatus',
                'assignedBy' => function (MorphTo $morphTo) {
                    $morphTo->morphWith([
                        User::class => ['department'],
                    ]);
                },
            ]))
            ->emptyStateHeading('No assignment history')
            ->defaultSort('assigned_at', 'desc')
            ->columns([
                TextColumn::make('user.name')
                    ->label('To')
                    ->description(fn (ServiceRequestAssignment $record): ?string => $userLine($record->user)),
                TextColumn::make('assignedBy.name')
                    ->label('By')
                    ->state(function (ServiceRequestAssignment $record) {
                        /** @var User|SystemUser|null $assignBy */
                        $assignBy = $record->assignedBy;

                        if ($assignBy) {
                            return $assignBy->name;
                        }

                        return 'Auto-Assignment';
                    })
                    ->description(fn (ServiceRequestAssignment $record): ?string => $record->assignedBy instanceof User ? $userLine($record->assignedBy) : null),
                TextColumn::make('serviceRequestStatus.name')
                    ->label('Status')
                    ->badge()
                    ->color(fn (ServiceRequestAssignment $record): ?string => $record->serviceRequestStatus?->color?->value)
                    ->placeholder('—'),
                TextColumn::make('assigned_at')
                    ->label('Date')
                    ->dateTime()
                    ->description(fn (ServiceRequestAssignment $record): string => $record->assigned_at
                        ->copy()
                        ->setTimezone($timezone)
                        ->format('M j, Y g:i a (T)')),
            ]);
    }
}
