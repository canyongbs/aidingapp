<?php

namespace App\Filament\Imports;

use App\Models\User;
use App\Notifications\SetPasswordNotification;
use App\Rules\EmailNotInUseOrSoftDeleted;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class UserImporter extends Importer
{
    protected static ?string $model = User::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->rules(['required', 'max:255'])
                ->requiredMapping()
                ->example('Jonathan Smith'),
            ImportColumn::make('email')
                ->rules(['required', 'email', new EmailNotInUseOrSoftDeleted(), 'max:255'])
                ->requiredMapping()
                ->example('johnsmith@gmail.com'),
            ImportColumn::make('job_title')
                ->rules(['required', 'string', 'max:255'])
                ->requiredMapping()
                ->example('Advisor'),
            ImportColumn::make('is_external')
                ->label('External User')
                ->boolean()
                ->rules(['boolean'])
                ->example('true'),
        ];
    }

    public function resolveRecord(): ?User
    {
        $user = User::where('email', $this->data['email'])->first();

        return $user ?? new User([
            'email' => $this->data['email'],
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your user import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }

    protected function afterFill(): void
    {
        /** @var User $record */
        $record = $this->record;
        $record->is_external ??= true;
    }

    protected function afterCreate(): void
    {
        /** @var User $user */
        $user = $this->getRecord();

        if ($user->is_external) {
            return;
        }

        $user->notify(new SetPasswordNotification());
    }
}
