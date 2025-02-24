<?php

namespace App\Filament\Resources\UserResource\Actions;

use AidingApp\Authorization\Models\Role;
use App\Models\User;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;

class AssignRolesBulkAction extends BulkAction
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->icon('heroicon-s-wrench-screwdriver')
            ->modalWidth(MaxWidth::Small)
            ->fillForm(fn (Collection $records): array => [
                'records' => $records,
            ])
            ->form([
                Checkbox::make('replace')
                    ->label('Replace existing roles?'),
                Select::make('roles')
                    ->label('Roles')
                    ->options(Role::where('guard_name', 'web')->pluck('name', 'name'))
                    ->multiple(),
            ])
            ->action(function (array $data, Collection $records) {
                $records->each(function (User $record) use ($data) {
                    if ($data['replace']) {
                        $record->syncRoles($data['roles']);
                    } else {
                        $record->assignRole($data['roles']);
                    }
                });

                Notification::make()
                    ->title('Assigned Roles')
                    ->success()
                    ->send();
            });
    }

    public static function getDefaultName(): ?string
    {
        return 'assign_roles';
    }
}
