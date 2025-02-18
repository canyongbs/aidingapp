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
