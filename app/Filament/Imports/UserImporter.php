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

namespace App\Filament\Imports;

use AidingApp\Authorization\Models\Role;
use AidingApp\Department\Models\Department;
use App\Features\FullNameFeature;
use App\Models\User;
use App\Notifications\SetPasswordNotification;
use App\Rules\DepartmentExists;
use App\Rules\EmailNotArchived;
use App\Rules\RolesExist;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class UserImporter extends Importer
{
    protected static ?string $model = User::class;

    public static function getColumns(): array
    {
        $columns = [
            ImportColumn::make('first_name')
                ->label('First Name')
                ->exampleHeader('First Name')
                ->rules(['required', 'string', 'max:255'])
                ->requiredMapping()
                ->example('Jonathan'),
            ImportColumn::make('last_name')
                ->label('Last Name')
                ->exampleHeader('Last Name')
                ->rules(['required', 'string', 'max:255'])
                ->requiredMapping()
                ->example('Smith'),
            ImportColumn::make('preferred_name')
                ->label('Preferred Name')
                ->exampleHeader('Preferred Name')
                ->rules(['nullable', 'string', 'max:255'])
                ->example('Jon'),
            ImportColumn::make('email')
                ->label('Email address')
                ->exampleHeader('Email address')
                ->rules(['required', 'email', new EmailNotArchived(), 'max:255'])
                ->requiredMapping()
                ->example('johnsmith@gmail.com'),
            ImportColumn::make('job_title')
                ->label('Job title')
                ->exampleHeader('Job title')
                ->rules(['required', 'string', 'max:255'])
                ->requiredMapping()
                ->example('Advisor'),
            ImportColumn::make('is_external')
                ->label('External User')
                ->exampleHeader('External User')
                ->boolean()
                ->rules(['boolean'])
                ->example('true'),
            ImportColumn::make('employee_id')
                ->label('Employee ID')
                ->exampleHeader('Employee ID')
                ->rules(['nullable', 'string', 'max:255'])
                ->example('EMP-1001'),
            ImportColumn::make('work_number')
                ->label('Work Number')
                ->exampleHeader('Work Number')
                ->rules(['nullable', 'string', 'max:255'])
                ->example('+1 555 123 4567'),
            ImportColumn::make('work_extension')
                ->label('Work Extension')
                ->exampleHeader('Work Extension')
                ->integer()
                ->rules(['nullable', 'integer', 'min:0'])
                ->example('123'),
            ImportColumn::make('student_id')
                ->label('Student ID')
                ->exampleHeader('Student ID')
                ->rules(['nullable', 'string', 'max:255'])
                ->example('STU-1001'),
            ImportColumn::make('school')
                ->label('School')
                ->exampleHeader('School')
                ->rules(['nullable', 'string', 'max:255'])
                ->example('College of Engineering'),
            ImportColumn::make('academic_department')
                ->label('Academic Department')
                ->exampleHeader('Academic Department')
                ->rules(['nullable', 'string', 'max:255'])
                ->example('Computer Science'),
            ImportColumn::make('program')
                ->label('Program')
                ->exampleHeader('Program')
                ->rules(['nullable', 'string', 'max:255'])
                ->example('Bachelor of Science'),
            ImportColumn::make('mobile')
                ->label('Mobile number')
                ->exampleHeader('Mobile number')
                ->rules(['nullable', 'string', 'max:255'])
                ->example('+1 555 987 6543'),
            ImportColumn::make('address')
                ->label('Address')
                ->exampleHeader('Address')
                ->rules(['nullable', 'string', 'max:255'])
                ->example('123 Main St'),
            ImportColumn::make('address_2')
                ->label('Address 2')
                ->exampleHeader('Address 2')
                ->rules(['nullable', 'string', 'max:255'])
                ->example('Suite 100'),
            ImportColumn::make('city')
                ->label('City')
                ->exampleHeader('City')
                ->rules(['nullable', 'string', 'max:255'])
                ->example('Springfield'),
            ImportColumn::make('state')
                ->label('State')
                ->exampleHeader('State')
                ->rules(['nullable', 'string', 'max:255'])
                ->example('IL'),
            ImportColumn::make('postal_code')
                ->label('Postal')
                ->exampleHeader('Postal')
                ->rules(['nullable', 'string', 'max:255'])
                ->example('62701'),
            ImportColumn::make('country')
                ->label('Country')
                ->exampleHeader('Country')
                ->rules(['nullable', 'string', 'max:255'])
                ->example('United States'),
            ImportColumn::make('department')
                ->label('Department')
                ->exampleHeader('Department')
                ->fillRecordUsing(function (User $record, ?string $state): void {
                    if (blank($state)) {
                        return;
                    }

                    $department = Department::query()
                        ->where('name', $state)
                        ->first();

                    if ($department) {
                        $record->department()->associate($department);
                    }
                })
                ->rules([new DepartmentExists()])
                ->example(fn (): ?string => Department::query()->value('name')),
            ImportColumn::make('roles')
                ->label('Assigned Role')
                ->exampleHeader('Assigned Role')
                ->array('|')
                // Roles are a many-to-many relationship synced in afterSave(); this column must not
                // attempt to write a "roles" attribute onto the user record.
                ->fillRecordUsing(function (): void {})
                ->rules([new RolesExist()])
                ->example('Authorization Admin|Super Admin'),
        ];

        if (FullNameFeature::active()) {
            return $columns;
        }

        return array_values(array_filter(
            $columns,
            fn (ImportColumn $column): bool => ! in_array($column->getName(), static::newDemographicColumnNames(), true),
        ));
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

    /**
     * @return array<string>
     */
    protected static function newDemographicColumnNames(): array
    {
        return [
            'first_name',
            'last_name',
            'preferred_name',
            'employee_id',
            'student_id',
            'school',
            'academic_department',
            'program',
            'address',
            'address_2',
            'city',
            'state',
            'postal_code',
            'country',
        ];
    }

    protected function afterFill(): void
    {
        /** @var User $record */
        $record = $this->record;
        $record->is_external ??= true;

        if (FullNameFeature::active()) {
            $record->name = trim("{$record->first_name} {$record->last_name}");
        }
    }

    protected function afterSave(): void
    {
        $roleNames = collect((array) ($this->data['roles'] ?? []))
            ->map(fn (mixed $name): string => trim((string) $name))
            ->filter()
            ->values();

        // A blank "Assigned Role" column leaves the user's existing roles untouched.
        if ($roleNames->isEmpty()) {
            return;
        }

        /** @var User $user */
        $user = $this->getRecord();

        $roles = Role::query()
            ->where('guard_name', 'web')
            ->whereIn('name', $roleNames->all())
            ->get();

        $user->syncRoles($roles);
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
