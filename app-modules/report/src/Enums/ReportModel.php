<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\Report\Enums;

use App\Models\User;
use Filament\Tables\Table;
use App\Models\Authenticatable;
use App\Filament\Tables\UsersTable;
use Filament\Support\Contracts\HasLabel;
use AdvisingApp\Prospect\Models\Prospect;
use Illuminate\Database\Eloquent\Builder;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\Report\Filament\Exports\UserExporter;
use AdvisingApp\Prospect\Filament\Tables\ProspectsTable;
use AdvisingApp\Report\Filament\Exports\StudentExporter;
use AdvisingApp\Report\Filament\Exports\ProspectExporter;
use AdvisingApp\StudentDataModel\Filament\Tables\StudentsTable;

enum ReportModel: string implements HasLabel
{
    case Prospect = 'prospect';

    case Student = 'student';

    case User = 'user';

    public function getLabel(): ?string
    {
        return $this->name;
    }

    public static function default(): static
    {
        return static::Student;
    }

    public function query(): Builder
    {
        return match ($this) {
            static::Student => Student::query(),
            static::Prospect => Prospect::query(),
            static::User => User::query(),
        };
    }

    public function table(Table $table): Table
    {
        return $table->tap(app(match ($this) {
            static::Student => StudentsTable::class,
            static::Prospect => ProspectsTable::class,
            static::User => UsersTable::class,
        }));
    }

    public function class(): string
    {
        return match ($this) {
            static::Student => Student::class,
            static::Prospect => Prospect::class,
            static::User => User::class,
        };
    }

    public function exporter(): string
    {
        return match ($this) {
            static::Student => StudentExporter::class,
            static::Prospect => ProspectExporter::class,
            static::User => UserExporter::class,
        };
    }

    public function canBeAccessed(Authenticatable $user): bool
    {
        return match ($this) {
            static::Student, static::Prospect => $user->hasLicense($this->class()::getLicenseType()),
            static::User => $user->can('viewAny', User::class),
        };
    }

    public static function tryFromCaseOrValue(ReportModel | string $value): ?static
    {
        if ($value instanceof static) {
            return $value;
        }

        return static::tryFrom($value);
    }
}
