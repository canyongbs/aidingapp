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
use Filament\Actions\Imports\Exceptions\RowImportFailedException;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrganizationImporter extends Importer
{
    protected static ?string $model = Organization::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->label('Name')
                ->exampleHeader('Name')
                ->rules(['required', 'max:255'])
                ->requiredMapping()
                ->example('College Community College'),
            ImportColumn::make('email')
                ->label('Email')
                ->exampleHeader('Email')
                ->rules(['nullable', 'email', 'max:255'])
                ->example('admissions@collegecc.edu'),
            ImportColumn::make('phone_number')
                ->label('Phone Number')
                ->exampleHeader('Phone Number')
                ->rules(['nullable', 'string'])
                ->example('+1 (555) 555-5555'),
            ImportColumn::make('website')
                ->label('Website')
                ->exampleHeader('Website')
                ->rules(['nullable', 'url', 'max:255'])
                ->example('https://www.collegecc.edu'),
            ImportColumn::make('industry')
                ->label('Industry')
                ->exampleHeader('Industry')
                ->relationship(resolveUsing: 'name')
                ->guess(['industry_name'])
                ->fillRecordUsing(function (Organization $record, mixed $state): void {
                    if (blank($state)) {
                        // Only assign the default on create; on update keep the organization's existing industry.
                        if (! $record->exists) {
                            $record->industry()->associate(OrganizationIndustry::query()->where('is_default', true)->first());
                        }

                        return;
                    }

                    $record->industry()->associate(OrganizationIndustry::query()->where('name', $state)->first());
                })
                ->example(fn (): ?string => OrganizationIndustry::query()->value('name')),
            ImportColumn::make('type')
                ->label('Type')
                ->exampleHeader('Type')
                ->relationship(resolveUsing: 'name')
                ->guess(['type_name'])
                ->fillRecordUsing(function (Organization $record, mixed $state): void {
                    if (blank($state)) {
                        // Only assign the default on create; on update keep the organization's existing type.
                        if (! $record->exists) {
                            $record->type()->associate(OrganizationType::query()->where('is_default', true)->first());
                        }

                        return;
                    }

                    $record->type()->associate(OrganizationType::query()->where('name', $state)->first());
                })
                ->example(fn (): ?string => OrganizationType::query()->value('name')),
            ImportColumn::make('description')
                ->label('Description')
                ->exampleHeader('Description')
                ->rules(['nullable', 'string'])
                ->example('A public community college serving the local community.'),
            ImportColumn::make('number_of_employees')
                ->label('Number of Employees')
                ->exampleHeader('Number of Employees')
                ->integer()
                ->rules(['nullable', 'integer', 'min:0'])
                ->example('250'),
            ImportColumn::make('address')
                ->label('Address')
                ->exampleHeader('Address')
                ->rules(['nullable', 'string', 'max:255'])
                ->example('123 College Ave.'),
            ImportColumn::make('city')
                ->label('City')
                ->exampleHeader('City')
                ->rules(['nullable', 'string', 'max:255'])
                ->example('Riverside'),
            ImportColumn::make('state')
                ->label('State')
                ->exampleHeader('State')
                ->rules(['nullable', 'string', 'max:255'])
                ->example('IL'),
            ImportColumn::make('postalcode')
                ->label('Postal Code')
                ->exampleHeader('Postal Code')
                ->rules(['nullable', 'string', 'max:255'])
                ->example('62704'),
            ImportColumn::make('country')
                ->label('Country')
                ->exampleHeader('Country')
                ->rules(['nullable', 'string', 'max:255'])
                ->example('United States'),
            ImportColumn::make('linkedin_url')
                ->label('LinkedIn URL')
                ->exampleHeader('LinkedIn URL')
                ->rules(['nullable', 'url', 'max:255'])
                ->example('https://www.linkedin.com/school/college-community-college'),
            ImportColumn::make('facebook_url')
                ->label('Facebook URL')
                ->exampleHeader('Facebook URL')
                ->rules(['nullable', 'url', 'max:255'])
                ->example('https://www.facebook.com/collegecc'),
            ImportColumn::make('twitter_url')
                ->label('Twitter URL')
                ->exampleHeader('Twitter URL')
                ->rules(['nullable', 'url', 'max:255'])
                ->example('https://twitter.com/collegecc'),
            ImportColumn::make('is_contact_generation_enabled')
                ->label('Automatically generate contact record on login')
                ->exampleHeader('Automatically generate contact record on login')
                ->boolean()
                ->rules(['boolean'])
                ->example('false'),
            ImportColumn::make('domains')
                ->label('Domains')
                ->exampleHeader('Domains')
                ->rules(['nullable', 'string', function (string $attribute, mixed $value, Closure $fail): void {
                    $domains = collect(preg_split('/[|,]/', (string) $value) ?: [])
                        ->map(fn (string $domain): string => trim($domain))
                        ->filter();

                    $domains->each(function (string $domain) use ($fail): void {
                        if (! preg_match('/^(?!-)([a-zA-Z0-9-]{1,63}\.)+[a-zA-Z]{2,63}$/', $domain)) {
                            $fail("The domain '{$domain}' is not a valid domain.");
                        }
                    });

                    $domains
                        ->map(fn (string $domain): string => Str::lower($domain))
                        ->duplicates()
                        ->unique()
                        ->each(fn (string $domain) => $fail("The domain '{$domain}' is listed more than once."));
                }])
                ->fillRecordUsing(function (Organization $record, ?string $state): void {
                    $domains = self::parseDomains($state)
                        ->map(fn (string $domain): array => ['domain' => Str::lower($domain)])
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

        // Serialize concurrent chunk workers importing the same name so the unique index cannot abort a chunk transaction.
        DB::selectOne('SELECT pg_advisory_xact_lock(hashtext(?))', [Str::lower(trim((string) $name))]);

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
     * The cache tag under which this import's claimed domains are held so duplicates across rows can be rejected.
     */
    public static function domainClaimCacheTag(int|string|null $importKey): string
    {
        return "{organization-import-domain-claims-{$importKey}}";
    }

    protected function beforeSave(): void
    {
        assert($this->record instanceof Organization);

        // Domain uniqueness only needs enforcing when this row actually sets domains.
        if (! $this->record->isDirty('domains')) {
            return;
        }

        /** @var array<int, array{domain: string}> $domains */
        $domains = $this->record->domains ?? [];

        $ignoreId = $this->record->exists ? $this->record->getKey() : null;

        foreach ($domains as $domain) {
            $usedByAnotherOrganization = Organization::query()
                ->when($ignoreId, fn (Builder $query) => $query->whereKeyNot($ignoreId))
                ->whereRaw("EXISTS (SELECT 1 FROM jsonb_array_elements(domains) AS element WHERE lower(element->>'domain') = ?)", [$domain['domain']])
                ->exists();

            if ($usedByAnotherOrganization) {
                throw new RowImportFailedException("The domain '{$domain['domain']}' is already in use by another organization.");
            }
        }

        $tag = self::domainClaimCacheTag($this->import->getKey());

        // The claim value records which organization won the domain so a chunk retry can re-claim its own domains.
        $claimant = Str::lower((string) $this->record->name);

        foreach ($domains as $domain) {
            // Cache::add is an atomic set-if-absent, so the first organization to claim a domain wins across concurrent workers.
            if (Cache::tags([$tag])->add($domain['domain'], $claimant, now()->addDay())) {
                continue;
            }

            if (Cache::tags([$tag])->get($domain['domain']) !== $claimant) {
                throw new RowImportFailedException("The domain '{$domain['domain']}' was already imported for a different organization in this file.");
            }
        }
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
