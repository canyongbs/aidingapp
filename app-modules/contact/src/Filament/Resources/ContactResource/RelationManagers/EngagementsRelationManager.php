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

namespace AidingApp\Contact\Filament\Resources\ContactResource\RelationManagers;

use AidingApp\Engagement\Actions\RelationManagerSendEngagementAction;
use AidingApp\Engagement\Enums\EngagementDeliveryMethod;
use AidingApp\Engagement\Enums\EngagementDeliveryStatus;
use AidingApp\Engagement\Models\Engagement;
use AidingApp\Engagement\Models\EngagementResponse;
use AidingApp\Notification\Enums\NotificationChannel;
use AidingApp\Timeline\Models\Timeline;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Enums\IconPosition;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;

class EngagementsRelationManager extends RelationManager
{
    protected static string $relationship = 'timeline';

    protected static ?string $title = 'Emails';

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema(fn (Timeline $record) => match ($record->timelineable::class) {
            Engagement::class => [
                TextEntry::make('user.name')
                    ->label('Created By')
                    ->getStateUsing(fn (Timeline $record): string => $record->timelineable->user->name),
                Fieldset::make('Content')
                    ->schema([
                        TextEntry::make('subject')
                            ->getStateUsing(fn (Timeline $record): ?string => $record->timelineable->subject)
                            ->hidden(fn ($state): bool => blank($state))
                            ->columnSpanFull(),
                        TextEntry::make('body')
                            ->getStateUsing(fn (Timeline $record): HtmlString => $record->timelineable->getBody())
                            ->markdown()
                            ->columnSpanFull(),
                    ]),
                Fieldset::make('deliverable')
                    ->label('Delivery Information')
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('deliverable.channel')
                            ->getStateUsing(fn (Timeline $record): EngagementDeliveryMethod => $record->timelineable->deliverable->channel)
                            ->label('Channel'),
                        TextEntry::make('deliverable.delivery_status')
                            ->getStateUsing(fn (Timeline $record): EngagementDeliveryStatus => $record->timelineable->deliverable->delivery_status)
                            ->iconPosition(IconPosition::After)
                            ->icon(fn (EngagementDeliveryStatus $state): string => match ($state) {
                                EngagementDeliveryStatus::Successful => 'heroicon-o-check-circle',
                                EngagementDeliveryStatus::Awaiting, EngagementDeliveryStatus::Dispatched => 'heroicon-o-clock',
                                EngagementDeliveryStatus::Failed, EngagementDeliveryStatus::DispatchFailed, EngagementDeliveryStatus::RateLimited => 'heroicon-o-x-circle',
                            })
                            ->iconColor(fn (EngagementDeliveryStatus $state): string => match ($state) {
                                EngagementDeliveryStatus::Successful => 'success',
                                EngagementDeliveryStatus::Awaiting, EngagementDeliveryStatus::Dispatched => 'info',
                                EngagementDeliveryStatus::Failed, EngagementDeliveryStatus::DispatchFailed, EngagementDeliveryStatus::RateLimited => 'danger',
                            })
                            ->label('Status')
                            ->formatStateUsing(fn (Timeline $record): string => match ($record->timelineable->deliverable->delivery_status) {
                                EngagementDeliveryStatus::Successful => 'Successfully delivered',
                                EngagementDeliveryStatus::Awaiting, EngagementDeliveryStatus::Dispatched => 'Awaiting delivery',
                                EngagementDeliveryStatus::Failed, EngagementDeliveryStatus::DispatchFailed => 'Failed to send',
                                EngagementDeliveryStatus::RateLimited => 'Failed to send due to rate limits',
                            }),
                        TextEntry::make('deliverable.delivered_at')
                            ->label('Delivered At')
                            ->getStateUsing(fn (Timeline $record): string => $record->timelineable->deliverable->delivered_at)
                            ->hidden(fn (Timeline $record): bool => is_null($record->timelineable->deliverable->delivered_at)),
                        TextEntry::make('deliverable.delivery_response')
                            ->label('Error Details')
                            ->getStateUsing(fn (Timeline $record): string => $record->timelineable->deliverable->delivery_response)
                            ->hidden(fn (Timeline $record): bool => is_null($record->timelineable->deliverable->delivery_response)),
                    ])
                    ->columns(2),
            ],
            EngagementResponse::class => [
                TextEntry::make('content')->getStateUsing(fn (Timeline $record): HtmlString => $record->timelineable->getBody()),
                TextEntry::make('sent_at')
                    ->getStateUsing(fn (Timeline $record): string => $record->timelineable->sent_at)
                    ->dateTime('Y-m-d H:i:s'),
            ],
        });
    }

    public function table(Table $table): Table
    {
        $canAccessEngagements = auth()->user()->can('viewAny', Engagement::class);
        $canAccessEngagementResponses = auth()->user()->can('viewAny', EngagementResponse::class);

        return $table
            ->emptyStateHeading('No email.')
            ->emptyStateDescription('Create an email to get started.')
            ->defaultSort('record_sortable_date', 'desc')
            ->modifyQueryUsing(fn (Builder $query) => $query->whereHasMorph('timelineable', [
                ...($canAccessEngagements ? [Engagement::class] : []),
                ...($canAccessEngagements ? [EngagementResponse::class] : []),
            ]))
            ->columns([
                TextColumn::make('direction')
                    ->getStateUsing(fn (Timeline $record) => match ($record->timelineable::class) {
                        Engagement::class => 'Outbound',
                        EngagementResponse::class => 'Inbound',
                    })
                    ->icon(fn (string $state) => match ($state) {
                        'Outbound' => 'heroicon-o-arrow-up-tray',
                        'Inbound' => 'heroicon-o-arrow-down-tray',
                    }),
                TextColumn::make('type')
                    ->getStateUsing(function (Timeline $record) {
                        $timelineable = $record->timelineable?->engagementDeliverable;

                        return $timelineable->channel ?? NotificationChannel::Sms;
                    })
                    ->icon(fn ($state) => $state->getIcon()),
                TextColumn::make('record_sortable_date')
                    ->label('Date')
                    ->sortable(),
            ])
            ->headerActions([
                RelationManagerSendEngagementAction::make(),
            ])
            ->actions([
                ViewAction::make()
                    ->modalHeading(function (Timeline $record) {
                        $timelineable = $record->timelineable?->engagementDeliverable;
                        $deliveryMethod = $timelineable->channel ?? NotificationChannel::Sms;

                        return "View {$deliveryMethod->getLabel()}";
                    }),
            ])
            ->filters([
                SelectFilter::make('direction')
                    ->options([
                        Engagement::class => 'Outbound',
                        EngagementResponse::class => 'Inbound',
                    ])
                    ->modifyQueryUsing(
                        fn (Builder $query, array $data) => $query
                            ->when($data['value'], fn (Builder $query) => $query->whereHasMorph('timelineable', $data['value']))
                    )
                    ->visible($canAccessEngagements && $canAccessEngagementResponses),
                SelectFilter::make('type')
                    ->options(NotificationChannel::class)
                    ->modifyQueryUsing(
                        fn (Builder $query, array $data) => $query
                            ->when(
                                $data['value'] === NotificationChannel::Email->value,
                                fn (Builder $query) => $query
                                    ->whereHasMorph(
                                        'timelineable',
                                        [Engagement::class],
                                        fn (Builder $query, string $type) => match ($type) {
                                            Engagement::class => $query->whereHas('engagementDeliverable', fn (Builder $query) => $query->where('channel', $data['value'])),
                                        }
                                    )
                            )
                            ->when(
                                $data['value'] === NotificationChannel::Sms->value,
                                fn (Builder $query) => $query
                                    ->whereHasMorph(
                                        'timelineable',
                                        [Engagement::class, EngagementResponse::class],
                                        fn (Builder $query, string $type) => match ($type) {
                                            Engagement::class => $query->whereHas('engagementDeliverable', fn (Builder $query) => $query->where('channel', $data['value'])),
                                            EngagementResponse::class => $query,
                                        }
                                    )
                            )
                    ),
            ]);
    }
}
