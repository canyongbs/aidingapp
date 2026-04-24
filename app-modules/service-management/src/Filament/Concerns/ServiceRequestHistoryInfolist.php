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

namespace AidingApp\ServiceManagement\Filament\Concerns;

use AidingApp\ServiceManagement\Filament\Resources\ServiceRequests\ServiceRequestResource;
use AidingApp\ServiceManagement\Models\ServiceRequestHistory;
use Filament\Infolists\Components\TextEntry;

// TODO Re-use this trait across other places where infolist is rendered
trait ServiceRequestHistoryInfolist
{
    /**
     * @return array<TextEntry>
     */
    public function serviceRequestHistoryInfolist(): array
    {
        return [
            TextEntry::make('serviceRequest.service_request_number')
                ->label('Service Request')
                ->url(fn (ServiceRequestHistory $serviceRequestHistory): string => ServiceRequestResource::getUrl('view', ['record' => $serviceRequestHistory->serviceRequest]))
                ->color('primary'),
            TextEntry::make('created_by')
                ->label('Created By')
                ->visible(fn (ServiceRequestHistory $record): bool => $record->event_type === ServiceRequestHistory::EVENT_CREATED)
                ->state(fn (ServiceRequestHistory $record): string => $record->actorName()),
            TextEntry::make('snapshot_status')
                ->label('Status')
                ->visible(fn (ServiceRequestHistory $record): bool => $record->event_type === ServiceRequestHistory::EVENT_CREATED)
                ->state(fn (ServiceRequestHistory $record): ?string => $record->snapshotStatus()?->name),
            TextEntry::make('snapshot_priority')
                ->label('Priority')
                ->visible(fn (ServiceRequestHistory $record): bool => $record->event_type === ServiceRequestHistory::EVENT_CREATED)
                ->state(fn (ServiceRequestHistory $record): ?string => $record->snapshotPriority()?->name),
            TextEntry::make('snapshot_type')
                ->label('Type')
                ->visible(fn (ServiceRequestHistory $record): bool => $record->event_type === ServiceRequestHistory::EVENT_CREATED)
                ->state(fn (ServiceRequestHistory $record): ?string => $record->snapshotType()?->name),
            TextEntry::make('snapshot_title')
                ->label('Title')
                ->columnSpanFull()
                ->visible(fn (ServiceRequestHistory $record): bool => $record->event_type === ServiceRequestHistory::EVENT_CREATED)
                ->state(fn (ServiceRequestHistory $record): ?string => $record->new_values['title'] ?? null),
            TextEntry::make('unassigned_from')
                ->label('Unassigned From')
                ->visible(fn (ServiceRequestHistory $record): bool => $record->event_type === ServiceRequestHistory::EVENT_UNASSIGNED)
                ->state(fn (ServiceRequestHistory $record): ?string => $record->original_values['previous_user_name'] ?? null),
            TextEntry::make('getUpdates')
                ->label('Updates')
                ->columnSpanFull()
                ->visible(fn (ServiceRequestHistory $record): bool => $record->event_type === null)
                ->state(function (ServiceRequestHistory $record) {
                    return $record->getUpdates();
                })
                ->view('filament.infolists.components.update-entry'),
        ];
    }
}
