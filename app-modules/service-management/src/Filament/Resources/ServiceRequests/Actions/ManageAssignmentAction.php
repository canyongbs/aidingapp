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

namespace AidingApp\ServiceManagement\Filament\Resources\ServiceRequests\Actions;

use AidingApp\ServiceManagement\Enums\ServiceRequestAssignmentStatus;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequests\Schemas\Components\ServiceRequestStatusSelect;
use AidingApp\ServiceManagement\Filament\Tables\ManagersTable;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use Filament\Actions\Action;
use Filament\Forms\Components\TableSelect;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ManageAssignmentAction
{
    public static function make(ServiceRequest $serviceRequest): Action
    {
        return Action::make('manageAssignment')
            ->label('Manage Assignment')
            ->color('gray')
            ->slideOver()
            ->modalHeading('Manage Assignment')
            ->modalSubmitActionLabel('Submit')
            ->visible(fn (): bool => $serviceRequest->priority?->type_id !== null
                && auth()->user()->can('update', $serviceRequest))
            ->schema([
                TextEntry::make('currentAssignment')
                    ->label('Current Assignment')
                    ->state(function () use ($serviceRequest): string {
                        $assignedUser = $serviceRequest->assignedTo?->user;

                        return $assignedUser === null ? 'Unassigned' : $assignedUser->name;
                    }),
                TableSelect::make('userId')
                    ->label('Reassign')
                    ->tableConfiguration(ManagersTable::class)
                    ->tableArguments([
                        'serviceRequestTypeId' => $serviceRequest->priority?->type_id,
                        'excludeUserId' => $serviceRequest->assignedTo?->user_id,
                    ])
                    ->required(),
                ServiceRequestStatusSelect::make()
                    ->default($serviceRequest->status_id)
                    ->label('Update Status')
                    ->required()
                    ->helperText('You may simultaneously update the status along with this change.'),
            ])
            ->action(function (array $data, Component $livewire) use ($serviceRequest): void {
                $serviceRequestTypeId = $serviceRequest->priority?->type_id;

                $isEligibleManager = $serviceRequestTypeId !== null
                    && ManagersTable::query($serviceRequestTypeId, $serviceRequest->assignedTo?->user_id)
                        ->whereKey($data['userId'])
                        ->exists();

                if (! $isEligibleManager) {
                    Notification::make()
                        ->danger()
                        ->title('Invalid assignment')
                        ->body('The selected user is not a manager of this service request type.')
                        ->send();

                    return;
                }

                DB::transaction(function () use ($serviceRequest, $data): void {
                    $serviceRequest->assignments()->create([
                        'user_id' => $data['userId'],
                        'assigned_by_id' => auth()->user()?->getKey(),
                        'assigned_by_type' => auth()->user()?->getMorphClass(),
                        'assigned_at' => now(),
                        'status' => ServiceRequestAssignmentStatus::Active,
                        'service_request_status_id' => $data['status_id'],
                    ]);

                    $serviceRequest->status_id = $data['status_id'];
                    $serviceRequest->unsetRelation('status');
                    $serviceRequest->save();
                });

                $livewire->dispatch('assignment-history-refresh');
            });
    }
}
