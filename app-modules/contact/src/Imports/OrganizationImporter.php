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

namespace AidingApp\Contact\Imports;

use AidingApp\Contact\Models\Organization;
use AidingApp\Contact\Models\OrganizationIndustry;
use AidingApp\Contact\Models\OrganizationType;
use Closure;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class OrganizationImporter extends Importer
{
    protected static ?string $model = Organization::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->rules(['required', 'max:255'])
                ->requiredMapping()
                ->example('College Community College'),
            ImportColumn::make('email')
                ->rules(['nullable', 'email', 'max:255'])
                ->example('admissions@collegecc.edu'),
            ImportColumn::make('phone_number')
                ->rules(['nullable', 'string'])
                ->example('+1 (555) 555-5555'),
            ImportColumn::make('website')
                ->rules(['nullable', 'url', 'max:255'])
                ->example('https://www.collegecc.edu'),
            ImportColumn::make('industry')
                ->relationship(
                    resolveUsing: function (mixed $state): ?OrganizationIndustry {
                        if (blank($state)) {
                            return OrganizationIndustry::query()->where('is_default', true)->first();
                        }

                        return OrganizationIndustry::query()
                            ->where('name', $state)
                            ->first()
                            ?? OrganizationIndustry::query()->where('is_default', true)->first();
                    },
                )
                ->guess(['industry_name'])
                ->example(fn (): ?string => OrganizationIndustry::query()->value('name')),
            ImportColumn::make('type')
                ->relationship(
                    resolveUsing: function (mixed $state): ?OrganizationType {
                        if (blank($state)) {
                            return OrganizationType::query()->where('is_default', true)->first();
                        }

                        return OrganizationType::query()
                            ->where('name', $state)
                            ->first()
                            ?? OrganizationType::query()->where('is_default', true)->first();
                    },
                )
                ->guess(['type_name'])
                ->example(fn (): ?string => OrganizationType::query()->value('name')),
            ImportColumn::make('description')
                ->example('A public community college serving the local community.'),
            ImportColumn::make('number_of_employees')
                ->integer()
                ->rules(['nullable', 'integer', 'min:0'])
                ->example('250'),
            ImportColumn::make('address')
                ->example('123 College Ave.'),
            ImportColumn::make('city')
                ->example('Riverside'),
            ImportColumn::make('state')
                ->example('IL'),
            ImportColumn::make('postalcode')
                ->label('Postal Code')
                ->example('62704'),
            ImportColumn::make('country')
                ->example('United States'),
            ImportColumn::make('linkedin_url')
                ->label('LinkedIn URL')
                ->rules(['nullable', 'url', 'max:255'])
                ->example('https://www.linkedin.com/school/college-community-college'),
            ImportColumn::make('facebook_url')
                ->label('Facebook URL')
                ->rules(['nullable', 'url', 'max:255'])
                ->example('https://www.facebook.com/collegecc'),
            ImportColumn::make('twitter_url')
                ->label('Twitter URL')
                ->rules(['nullable', 'url', 'max:255'])
                ->example('https://twitter.com/collegecc'),
            ImportColumn::make('is_contact_generation_enabled')
                ->label('Automatically generate contact record on login')
                ->boolean()
                ->rules(['boolean'])
                ->example('false'),
            ImportColumn::make('domains')
                ->label('Domains')
                ->rules(['nullable', 'string', function (string $attribute, mixed $value, Closure $fail): void {
                    self::parseDomains($value)->each(function (string $domain) use ($fail): void {
                        if (! preg_match('/^(?!-)([a-zA-Z0-9-]{1,63}\.)+[a-zA-Z]{2,63}$/', $domain)) {
                            $fail("The domain '{$domain}' is not a valid domain.");
                        }
                    });
                }])
                ->fillRecordUsing(function (Organization $record, ?string $state): void {
                    $domains = self::parseDomains($state)
                        ->map(fn (string $domain): array => ['domain' => $domain])
                        ->all();

                    // A blank column leaves the organization's existing domains untouched.
                    if (empty($domains)) {
                        return;
                    }

                    $record->domains = $domains;
                })
                ->example('collegecc.edu|college.edu'),
        ];
    }

    public function resolveRecord(): ?Model
    {
        $name = $this->data['name'];

        $organization = Organization::query()
            ->where('name', $name)
            ->first();

        return $organization ?? new Organization([
            'name' => $name,
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your organization import has completed and ' . number_format($import->successful_rows) . ' ' . Str::plural('row', $import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . Str::plural('row', $failedRowsCount) . ' failed to import.';
        }

        return $body;
    }

    /**
     * Split a comma- or pipe-separated list of domains into a trimmed, de-duplicated collection.
     *
     * @return Collection<int, non-falsy-string>
     */
    protected static function parseDomains(mixed $state): Collection
    {
        return collect(preg_split('/[|,]/', (string) $state) ?: [])
            ->map(fn (string $domain): string => trim($domain))
            ->filter()
            ->unique(fn (string $domain): string => Str::lower($domain))
            ->values();
    }
}
