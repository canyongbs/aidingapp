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

use AidingApp\Engagement\Actions\CreateEngagementDeliverable;
use AidingApp\Engagement\Enums\EngagementDeliveryStatus;
use AidingApp\Engagement\Filament\Resources\EngagementResource\Pages\CreateEngagement;
use AidingApp\Engagement\Models\Engagement;
use App\Filament\Tables\Columns\IdColumn;
use Filament\Forms\Form;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Enums\IconPosition;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class EngagementsRelationManager extends RelationManager
{
    protected static string $relationship = 'engagements';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return 'Outbound';
    }

    public function form(Form $form): Form
    {
        return (resolve(CreateEngagement::class))->form($form);
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('user.name')
                    ->label('Created By'),
                Fieldset::make('Content')
                    ->schema([
                        TextEntry::make('subject')
                            ->columnSpanFull(),
                        TextEntry::make('body')
                            ->getStateUsing(fn (Engagement $engagement): string => $engagement->getBody())
                            ->markdown()
                            ->columnSpanFull(),
                    ]),
                Fieldset::make('deliverable')
                    ->label('Delivery Information')
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('deliverable.channel')
                            ->label('Channel'),
                        TextEntry::make('deliverable.delivery_status')
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
                            ->formatStateUsing(fn (Engagement $engagement): string => match ($engagement->deliverable->delivery_status) {
                                EngagementDeliveryStatus::Successful => 'Successfully delivered',
                                EngagementDeliveryStatus::Awaiting, EngagementDeliveryStatus::Dispatched => 'Awaiting delivery',
                                EngagementDeliveryStatus::Failed, EngagementDeliveryStatus::DispatchFailed => 'Failed to send',
                                EngagementDeliveryStatus::RateLimited => 'Failed to send due to rate limits',
                            }),
                        TextEntry::make('deliverable.delivered_at')
                            ->label('Delivered At')
                            ->hidden(fn (Engagement $engagement): bool => is_null($engagement->deliverable->delivered_at)),
                        TextEntry::make('deliverable.delivery_response')
                            ->label('Error Details')
                            ->hidden(fn (Engagement $engagement): bool => is_null($engagement->deliverable->delivery_response)),
                    ])
                    ->columns(2),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading('Emails')
            ->recordTitleAttribute('id')
            ->columns([
                IdColumn::make(),
                TextColumn::make('subject'),
                TextColumn::make('deliverable.channel')
                    ->label('Delivery Channel'),
                TextColumn::make('created_at')
                    ->dateTime(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->modalHeading('Create new email')
                    ->after(function (Engagement $engagement, array $data) {
                        $this->afterCreate($engagement, $data['delivery_method']);
                    }),
            ])
            ->actions([
                ViewAction::make(),
            ])
            ->bulkActions([
            ])
            ->defaultSort('created_at', 'desc');
    }

    public function afterCreate(Engagement $engagement, string $deliveryMethod): void
    {
        $createEngagementDeliverable = resolve(CreateEngagementDeliverable::class);

        $createEngagementDeliverable($engagement, $deliveryMethod);
    }
}
