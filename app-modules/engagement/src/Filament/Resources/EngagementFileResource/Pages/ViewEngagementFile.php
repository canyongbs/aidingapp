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

namespace AidingApp\Engagement\Filament\Resources\EngagementFileResource\Pages;

use AidingApp\Engagement\Filament\Resources\EngagementFileResource;
use AidingApp\Engagement\Models\EngagementFile;
use Filament\Actions;
use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewEngagementFile extends ViewRecord
{
    protected static string $resource = EngagementFileResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        /**
         * @var EngagementFile $record
         */
        $record = $this->record;

        return $infolist
            ->schema([
                Section::make()
                    ->schema([
                        TextEntry::make('description')
                            ->label('Description'),
                        TextEntry::make('retention_date')
                            ->label('Retention Date')
                            ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'The file will be deleted automatically after this date. If left blank, the file will be kept indefinitely.'),
                        SpatieMediaLibraryImageEntry::make('file')
                            ->collection('file')
                            ->hintAction(
                                Action::make('download')
                                    ->label('Download')
                                    ->icon('heroicon-m-arrow-down-tray')
                                    ->color('primary')
                                    ->extraAttributes([
                                        'download' => true,
                                        'target' => '_blank',
                                    ])
                                    ->url(route('engagement-file-download', ['file' => $record->getKey()]))
                            ),
                    ])
                    ->columns(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
