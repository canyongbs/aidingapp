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

namespace AidingApp\Interaction\Imports;

use App\Models\User;
use Illuminate\Support\Str;
use AidingApp\Contact\Models\Contact;
use Filament\Actions\Imports\Importer;
use AidingApp\Division\Models\Division;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Models\Import;
use AidingApp\Interaction\Models\Interaction;
use AidingApp\Interaction\Models\InteractionType;
use AidingApp\Interaction\Models\InteractionDriver;
use AidingApp\Interaction\Models\InteractionStatus;
use AidingApp\Interaction\Models\InteractionOutcome;
use AidingApp\Interaction\Models\InteractionCampaign;
use AidingApp\Interaction\Models\InteractionRelation;

class InteractionsImporter extends Importer
{
    /**
     * @inheritDoc
     */
    public static function getColumns(): array
    {
        return [
            ImportColumn::make('interactable')
                ->relationship(
                    resolveUsing: function (InteractionsImporter $importer, mixed $state): ?Model {
                        $resolveFromModel = fn (string $model, string $identifier): ?Model => $model::query()
                            ->when(
                                str($identifier)->isUuid(),
                                fn (Builder $query) => $query->whereKey($identifier),
                                fn (Builder $query) => $query->where('email', $identifier),
                            )
                            ->first();

                        if (str($state)->contains(':')) {
                            return $resolveFromModel(match ((string) str($state)->before(':')) {
                                'contact' => Contact::class,
                            }, (string) str($state)->after(':'));
                        }

                        $user = $importer->getImport()->user;

                        $model = match (true) {
                            $user->hasLicense(Contact::getLicenseType()) => Contact::class,
                            default => null,
                        };

                        return filled($model) ? $resolveFromModel($model, $state) : null;
                    },
                )
                ->requiredMapping()
                ->rules(function (InteractionsImporter $importer) {
                    if (! $importer->getImport()->user->hasLicense([Contact::getLicenseType()])) {
                        return [];
                    }

                    return ['starts_with:contact:'];
                })
                ->example(function () {
                    if (auth()->user()?->hasLicense([Contact::getLicenseType()]) ?? true) {
                        return 'contact:johnsmith@gmail.com';
                    }

                    return 'johnsmith@gmail.com';
                }),
            ImportColumn::make('type')
                ->relationship(
                    resolveUsing: fn (mixed $state) => InteractionType::query()
                        ->when(
                            str($state)->isUuid(),
                            fn (Builder $query) => $query->whereKey($state),
                            fn (Builder $query) => $query->whereRaw('lower(name) = ?', [strtolower($state)]),
                        )
                        ->first(),
                )
                ->requiredMapping()
                ->example(fn (): ?string => InteractionType::query()->value('name')),
            ImportColumn::make('relation')
                ->relationship(
                    resolveUsing: fn (mixed $state) => InteractionRelation::query()
                        ->when(
                            str($state)->isUuid(),
                            fn (Builder $query) => $query->whereKey($state),
                            fn (Builder $query) => $query->whereRaw('lower(name) = ?', [strtolower($state)]),
                        )
                        ->first(),
                )
                ->requiredMapping()
                ->example(fn (): ?string => InteractionRelation::query()->value('name')),
            ImportColumn::make('campaign')
                ->relationship(
                    resolveUsing: fn (mixed $state) => InteractionCampaign::query()
                        ->when(
                            str($state)->isUuid(),
                            fn (Builder $query) => $query->whereKey($state),
                            fn (Builder $query) => $query->whereRaw('lower(name) = ?', [strtolower($state)]),
                        )
                        ->first(),
                )
                ->requiredMapping()
                ->example(fn (): ?string => InteractionCampaign::query()->value('name')),
            ImportColumn::make('driver')
                ->relationship(
                    resolveUsing: fn (mixed $state) => InteractionDriver::query()
                        ->when(
                            str($state)->isUuid(),
                            fn (Builder $query) => $query->whereKey($state),
                            fn (Builder $query) => $query->whereRaw('lower(name) = ?', [strtolower($state)]),
                        )
                        ->first(),
                )
                ->requiredMapping()
                ->example(fn (): ?string => InteractionDriver::query()->value('name')),
            ImportColumn::make('status')
                ->relationship(
                    resolveUsing: fn (mixed $state) => InteractionStatus::query()
                        ->when(
                            str($state)->isUuid(),
                            fn (Builder $query) => $query->whereKey($state),
                            fn (Builder $query) => $query->whereRaw('lower(name) = ?', [strtolower($state)]),
                        )
                        ->first(),
                )
                ->requiredMapping()
                ->example(fn (): ?string => InteractionStatus::query()->value('name')),
            ImportColumn::make('outcome')
                ->relationship(
                    resolveUsing: fn (mixed $state) => InteractionOutcome::query()
                        ->when(
                            str($state)->isUuid(),
                            fn (Builder $query) => $query->whereKey($state),
                            fn (Builder $query) => $query->whereRaw('lower(name) = ?', [strtolower($state)]),
                        )
                        ->first(),
                )
                ->requiredMapping()
                ->example(fn (): ?string => InteractionOutcome::query()->value('name')),
            ImportColumn::make('division')
                ->relationship(
                    resolveUsing: fn (mixed $state) => Division::query()
                        ->when(
                            str($state)->isUuid(),
                            fn (Builder $query) => $query->whereKey($state),
                            fn (Builder $query) => $query->whereRaw('lower(name) = ?', [strtolower($state)]),
                        )
                        ->first(),
                )
                ->requiredMapping()
                ->example(fn (): ?string => Division::query()->value('name')),
            ImportColumn::make('start_datetime')
                ->rules(['date'])
                ->example('2023-09-28 16:52:50'),
            ImportColumn::make('end_datetime')
                ->rules(['date'])
                ->example('2023-09-28 17:00:00'),
            ImportColumn::make('subject')
                ->example('Subject of the interaction.'),
            ImportColumn::make('description')
                ->example('A description of the interaction.'),
        ];
    }

    public function resolveRecord(): Interaction
    {
        return new Interaction();
    }

    public function afterFill(): void
    {
        /** @var Interaction $record */
        $record = $this->record;

        $query = Interaction::query();

        foreach ($record->getAttributes() as $key => $value) {
            if (in_array($key, ['id', 'created_at', 'updated_at'])) {
                continue;
            }

            $query->where($key, $value);
        }

        $existingRecord = $query->first();

        if ($existingRecord) {
            $this->record = $existingRecord;
        }
    }

    public function beforeCreate(): void
    {
        /** @var Interaction $record */
        $record = $this->record;

        /** @var User $user */
        $user = $this->import->user;

        $record['user_id'] = $user->id;
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your interactions import has completed and ' . number_format($import->successful_rows) . ' ' . Str::plural('row', $import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . Str::plural('row', $failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
