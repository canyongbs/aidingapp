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

namespace AidingApp\Engagement\Filament\Resources\EngagementResponseResource\Pages;

use AidingApp\Contact\Filament\Resources\ContactResource;
use AidingApp\Contact\Models\Contact;
use AidingApp\Engagement\Filament\Resources\EngagementResponseResource;
use AidingApp\Engagement\Models\EngagementResponse;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewEngagementResponse extends ViewRecord
{
    protected static string $resource = EngagementResponseResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make()
                    ->schema([
                        TextEntry::make('sender')
                            ->label('Sent By')
                            ->color('primary')
                            ->state(function (EngagementResponse $record): string {
                                /** @var Contact $sender */
                                $sender = $record->sender;

                                return match ($sender::class) {
                                    Contact::class => "{$sender->full} (Contact)",
                                };
                            })
                            ->url(function (EngagementResponse $record) {
                                /** @var Contact $sender */
                                $sender = $record->sender;

                                return match ($sender::class) {
                                    Contact::class => ContactResource::getUrl('view', ['record' => $sender->id]),
                                };
                            }),
                        TextEntry::make('content'),
                    ])
                    ->columns(),
            ]);
    }
}
