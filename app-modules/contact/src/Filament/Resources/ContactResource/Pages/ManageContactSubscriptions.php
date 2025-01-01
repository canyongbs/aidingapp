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

namespace AidingApp\Contact\Filament\Resources\ContactResource\Pages;

use AidingApp\Contact\Filament\Resources\ContactResource;
use AidingApp\Contact\Models\Contact;
use App\Filament\Resources\UserResource;
use App\Filament\Tables\Columns\IdColumn;
use App\Models\Scopes\HasLicense;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DetachAction;
use Filament\Tables\Actions\DetachBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ManageContactSubscriptions extends ManageRelatedRecords
{
    protected static string $resource = ContactResource::class;

    protected static string $relationship = 'subscribedUsers';

    // TODO: Automatically set from Filament based on relationship name
    protected static ?string $navigationLabel = 'Subscriptions';

    // TODO: Automatically set from Filament based on relationship name
    protected static ?string $breadcrumb = 'Subscriptions';

    protected static ?string $navigationIcon = 'heroicon-o-user';

    //TODO: manually override check canAccess for policy

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                IdColumn::make(),
                TextColumn::make('name')
                    ->url(fn ($record) => UserResource::getUrl('view', ['record' => $record]))
                    ->color('primary'),
                TextColumn::make('pivot.created_at')
                    ->label('Subscribed At'),
            ])
            ->headerActions([
                AttachAction::make()
                    ->label('Create Subscription')
                    ->modalHeading(function () {
                        /** @var Contact $contact */
                        $contact = $this->getOwnerRecord();

                        return 'Subscribe a User to ' . $contact->display_name;
                    })
                    ->modalSubmitActionLabel('Subscribe')
                    ->attachAnother(false)
                    ->color('primary')
                    ->recordSelect(
                        fn (Select $select) => $select->placeholder('Select a User'),
                    )
                    ->recordSelectOptionsQuery(
                        fn (Builder $query) => $query->tap(new HasLicense(Contact::getLicenseType())),
                    )
                    ->successNotificationTitle(function (User $record) {
                        /** @var Contact $contact */
                        $contact = $this->getOwnerRecord();

                        return "{$record->name} was subscribed to {$contact->display_name}";
                    }),
            ])
            ->actions([
                DetachAction::make()
                    ->label('Unsubscribe')
                    ->modalHeading(function (User $record) {
                        /** @var Contact $contact */
                        $contact = $this->getOwnerRecord();

                        return "Unsubscribe {$record->name} from {$contact->display_name}";
                    })
                    ->modalSubmitActionLabel('Unsubscribe')
                    ->successNotificationTitle(function (User $record) {
                        /** @var Contact $contact */
                        $contact = $this->getOwnerRecord();

                        return "{$record->name} was unsubscribed from {$contact->display_name}";
                    }),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DetachBulkAction::make()
                        ->label('Unsubscribe selected')
                        ->modalHeading(function () {
                            /** @var Contact $contact */
                            $contact = $this->getOwnerRecord();

                            return "Unsubscribe selected from {$contact->display_name}";
                        })
                        ->modalSubmitActionLabel('Unsubscribe')
                        ->successNotificationTitle(function () {
                            /** @var Contact $contact */
                            $contact = $this->getOwnerRecord();

                            return "All selected were unsubscribed from {$contact->display_name}";
                        }),
                ]),
            ])
            ->emptyStateHeading('No Subscriptions')
            ->inverseRelationship('contactSubscriptions');
    }
}
