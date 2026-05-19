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

namespace AidingApp\ServiceManagement\Filament\Widgets;

use AidingApp\Contact\Models\Contact;
use App\Models\Media;
use App\Models\User;
use App\Settings\DisplaySettings;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ServiceRequestMediaTable extends TableWidget
{
    public Model $record;

    public string $collectionName = 'uploads';

    protected static ?string $heading = 'Uploads';

    protected int|string|array $columnSpan = 'full';

    public function mount(Model $record, string $collectionName = 'uploads'): void
    {
        $this->record = $record;
        $this->collectionName = $collectionName;
    }

    public function table(Table $table): Table
    {
        $timezone = app(DisplaySettings::class)->getTimezone();
        $modelType = $this->record->getMorphClass();
        $modelId = $this->record->getKey();
        $collectionName = $this->collectionName;

        return $table
            ->query(
                Media::query()
                    ->where('model_type', $modelType)
                    ->where('model_id', $modelId)
                    ->where('collection_name', $collectionName)
                    ->with('createdBy')
            )
            ->columns([
                TextColumn::make('display_name')
                    ->label('File Name')
                    ->getStateUsing(fn (Media $record): string => $record->name . '.' . pathinfo($record->file_name, PATHINFO_EXTENSION))
                    ->searchable(
                        query: fn (Builder $query, string $search) => $query->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($search) . '%'])
                    )
                    ->wrap(),

                TextColumn::make('created_by_name')
                    ->label('Uploaded By')
                    ->getStateUsing(fn (Media $record): string => $record->created_by_name)
                    ->description(fn (Media $record): ?string => $record->created_by_sub_label)
                    ->searchable(
                        query: fn (Builder $query, string $search) => $query->whereHasMorph(
                            'createdBy',
                            [User::class, Contact::class],
                            function (Builder $subQuery, string $type) use ($search): void {
                                $searchLower = strtolower($search);

                                if ($type === User::class) {
                                    $subQuery->whereRaw('LOWER(name) LIKE ?', ['%' . $searchLower . '%']);
                                } elseif ($type === Contact::class) {
                                    $subQuery->where(function (Builder $query) use ($searchLower): void {
                                        $query->whereRaw('LOWER(first_name) LIKE ?', ['%' . $searchLower . '%'])
                                            ->orWhereRaw('LOWER(last_name) LIKE ?', ['%' . $searchLower . '%'])
                                            ->orWhereRaw('LOWER(full_name) LIKE ?', ['%' . $searchLower . '%']);
                                    });
                                }
                            }
                        )
                    ),

                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('M j, Y')
                    ->description(fn (Media $record): string => $record->created_at !== null
                        ? $record->created_at
                            ->copy()
                            ->setTimezone($timezone)
                            ->format('g:i A (T)')
                        : '')
                    ->sortable(),
            ])
            ->recordActions([
                Action::make('download')
                    ->label('Download')
                    ->icon('heroicon-m-arrow-down-tray')
                    ->color('primary')
                    ->url(fn (Media $record): string => route('service-request.media.download', ['media' => $record->getKey()])),
            ])
            ->emptyStateHeading('No uploads')
            ->defaultSort('created_at', 'desc');
    }
}
