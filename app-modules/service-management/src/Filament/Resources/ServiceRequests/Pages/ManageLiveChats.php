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

namespace AidingApp\ServiceManagement\Filament\Resources\ServiceRequests\Pages;

use AidingApp\ServiceManagement\Filament\Resources\ServiceRequests\ServiceRequestResource;
use AidingApp\ServiceManagement\Models\ServiceRequestConversation;
use App\Enums\Feature;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Gate;

class ManageLiveChats extends ManageRelatedRecords
{
    protected static string $resource = ServiceRequestResource::class;

    protected static string $relationship = 'conversations';

    protected static ?string $navigationLabel = 'Live Chats';

    protected static ?string $breadcrumb = 'Live Chats';

    protected static ?string $title = 'Live Chats';

    public static function canAccess(array $arguments = []): bool
    {
        return Gate::check(Feature::RealtimeChat->getGateName());
    }

    public static function getNavigationItemActiveRoutePattern(): string | array
    {
        return [
            static::getRouteName(),
            static::getResource()::getRouteBaseName() . '.view-live-chat-transcript',
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query
                ->whereNotNull('finished_at')
                ->whereNotNull('conversation_id'))
            ->columns([
                TextColumn::make('contact.full_name')
                    ->label('Contact'),
                TextColumn::make('user.name')
                    ->label('Agent'),
                TextColumn::make('finished_at')
                    ->label('Finished')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('finished_reason')
                    ->label('Finish Reason')
                    ->badge(),
            ])
            ->defaultSort('finished_at', 'desc')
            ->recordUrl(fn (ServiceRequestConversation $record): string => ServiceRequestResource::getUrl('view-live-chat-transcript', [
                'record' => $this->getRecord(),
                'conversation' => $record,
            ]));
    }

    protected function authorizeAccess(): void
    {
        parent::authorizeAccess();

        Gate::authorize('view', $this->getRecord());
    }
}
