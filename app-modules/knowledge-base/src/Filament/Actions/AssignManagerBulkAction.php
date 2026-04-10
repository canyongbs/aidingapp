<?php

namespace AidingApp\KnowledgeBase\Filament\Actions;

use AidingApp\KnowledgeBase\Models\KnowledgeBaseItem;
use App\Models\User;
use Exception;
use Filament\Actions\BulkAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AssignManagerBulkAction
{
    public static function make(): BulkAction
    {
        return BulkAction::make('bulkManagers')
            ->label('Assign Managers')
            ->icon('heroicon-s-user-group')
            ->modalHeading('Bulk Assign Managers')
            ->modalDescription(fn (Collection $records) => "You have selected {$records->count()} " . Str::plural('item', $records->count()) . ' to assign manager(s).')
            ->schema([
                Select::make('manager_ids')
                    ->label('Managers')
                    ->options(fn (): array => User::query()->limit(50)->pluck('name', 'id')->all())
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->exists('users', 'id'),
                Toggle::make('remove_prior')
                    ->label('Remove all previous assigned managers?')
                    ->default(false)
                    ->hintIconTooltip('If selected, all prior managers will be removed.'),
            ])
            ->action(function (array $data, Collection $records) {
                try {
                    DB::beginTransaction();

                    $records->each(function (KnowledgeBaseItem $record) use ($data) {
                        if (! empty($data['manager_ids'])) {
                            $record->managers()->sync($data['manager_ids'], $data['remove_prior']);
                        }
                    });

                    Notification::make()
                        ->title('Managers assigned successfully.')
                        ->success()
                        ->send();

                    DB::commit();
                } catch (Exception $excetpion) {
                    DB::rollBack();

                    Notification::make()
                        ->title('Something went wrong')
                        ->body('We failed to assign these managers. Please try again later.')
                        ->danger()
                        ->send();
                }
            })
            ->deselectRecordsAfterCompletion();
    }
}
