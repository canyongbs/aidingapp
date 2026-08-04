<?php

namespace AidingApp\ServiceManagement\Filament\Resources\ServiceRequests\Actions;

use AidingApp\ServiceManagement\Enums\ServiceRequestAssignmentStatus;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequests\Schemas\Components\ServiceRequestStatusSelect;
use AidingApp\ServiceManagement\Filament\Tables\ManagersTable;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use Filament\Actions\Action;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TableSelect;
use Filament\Notifications\Notification;
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
            ->visible(fn (): bool => auth()->user()->can('update', $serviceRequest))
            ->schema([
                Placeholder::make('currentAssignment')
                    ->label('Current Assignment')
                    ->content(fn (): string => $serviceRequest->assignedTo?->user?->name ?? 'Unassigned'),
                TableSelect::make('userId')
                    ->label('Reassign')
                    ->tableConfiguration(ManagersTable::class)
                    ->tableArguments([
                        'serviceRequestTypeId' => $serviceRequest->priority->type_id,
                        'excludeUserId' => $serviceRequest->assignedTo?->user_id,
                    ])
                    ->required(),
                ServiceRequestStatusSelect::make($serviceRequest)
                    ->label('Update Status')
                    ->helperText('You may simultaneously update the status along with this change.'),
            ])
            ->action(function (array $data, Component $livewire) use ($serviceRequest): void {
                $isEligibleManager = ManagersTable::query($serviceRequest->priority->type_id)
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

                $serviceRequest->assignments()->create([
                    'user_id' => $data['userId'],
                    'assigned_by_id' => auth()->user()?->getKey(),
                    'assigned_by_type' => auth()->user()?->getMorphClass(),
                    'assigned_at' => now(),
                    'status' => ServiceRequestAssignmentStatus::Active,
                    'service_request_status_id' => $data['status_id'],
                ]);

                $serviceRequest->update(['status_id' => $data['status_id']]);

                $livewire->dispatch('assignment-history-refresh');
            });
    }
}
