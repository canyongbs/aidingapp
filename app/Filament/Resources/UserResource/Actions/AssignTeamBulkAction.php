<?php

namespace App\Filament\Resources\UserResource\Actions;

use AidingApp\Team\Models\Team;
use App\Models\User;
use Exception;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;

class AssignTeamBulkAction extends BulkAction
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->icon('heroicon-o-user-group')
            ->modalWidth(MaxWidth::Small)
            ->modalDescription(
                fn (Collection $records): string => 'This bulk action will overwrite any prior team assignments for the selected ' . ((count($records) > 1) ? 'users' : 'user') . '.'
            )
            ->fillForm(fn (Collection $records): array => [
                'records' => $records,
            ])
            ->form([
                Select::make('team')
                    ->label('Team')
                    ->options(Team::pluck('name', 'id'))
                    ->searchable()
                    ->required(),
            ])
            ->action(function (array $data, Collection $records) {
                $success = 0;
                $fail = 0;
                $records->each(function (User $record) use ($data, &$success, &$fail) {
                    try {
                        $record->assignTeam($data['team']);
                        $success++;
                    } catch (Exception $e) {
                        report($e);
                        $fail++;
                    }
                });

                if ($fail > 0) {
                    Notification::make()
                        ->title('Assigned Team')
                        ->body($fail . ' ' . (($fail > 1) ? 'users were' : 'user was') . ' fail to added to the team.')
                        ->success()
                        ->send();
                } else {
                    Notification::make()
                        ->title('Assigned Team')
                        ->body($success . ' ' . (($success > 1) ? 'users were' : 'user was') . ' successfully added to the team.')
                        ->success()
                        ->send();
                }
            });
    }

    public static function getDefaultName(): ?string
    {
        return 'Assign team';
    }
}
