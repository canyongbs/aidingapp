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

namespace App\Filament\Resources\Users\Pages;

use AidingApp\Group\Models\Group;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use App\Filament\Exports\UserExporter;
use App\Filament\Imports\UserImporter;
use App\Filament\Resources\Users\Actions\AssignDepartmentBulkAction;
use App\Filament\Resources\Users\Actions\AssignGroupsBulkAction;
use App\Filament\Resources\Users\Actions\AssignRolesBulkAction;
use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ImportAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\VerticalAlignment;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use STS\FilamentImpersonate\Actions\Impersonate;

class ListUsers extends ListRecords
{
    /**
     * A non-blank, invisible marker used as a column's state when its primary value is
     * empty but its description (badge/groups) is not, so Filament renders the
     * description instead of short-circuiting to the placeholder. Formatted away to an
     * empty string before display.
     */
    private const string BLANK_DESCRIPTION_STATE = "\u{200B}";

    protected static string $resource = UserResource::class;

    protected ?string $heading = 'Users';

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with([
                'department.manageableServiceRequestTypes',
                'department.auditableServiceRequestTypes',
                'groups.manageableServiceRequestTypes',
                'groups.auditableServiceRequestTypes',
                'managedContact.type',
                'manageableServiceRequestTypes',
                'auditableServiceRequestTypes',
            ]))
            ->columns([
                TextColumn::make('name')
                    ->label('User')
                    ->weight(FontWeight::Bold)
                    ->searchable(['name', 'email'])
                    ->icon(fn (User $record): string => $record->presenceStatus()->getIcon())
                    ->iconColor(fn (User $record): string => $record->presenceStatus()->getColor())
                    ->tooltip(fn (User $record): string => 'Presence: ' . $record->presenceStatus()->getLabel())
                    ->extraAttributes(fn (User $record): array => [
                        'aria-label' => "{$record->name} (Presence: {$record->presenceStatus()->getLabel()})",
                    ])
                    ->description(fn (User $record): View => view('filament.tables.columns.copyable-description', [
                        'text' => $record->email,
                        'tooltip' => 'Copy Email Address',
                        'copyMessage' => 'Email address copied to clipboard',
                    ])),
                TextColumn::make('job_title')
                    ->label('Service Details')
                    ->state(fn (User $record): ?string => filled($record->job_title)
                        ? $record->job_title
                        : (filled(self::serviceDetailsBadgeLabel($record)) ? self::BLANK_DESCRIPTION_STATE : null))
                    ->formatStateUsing(fn (?string $state): string => $state === self::BLANK_DESCRIPTION_STATE ? '' : (string) $state)
                    ->searchable()
                    ->placeholder('—')
                    ->verticallyAlignCenter(fn (User $record): bool => blank($record->job_title) && filled(self::serviceDetailsBadgeLabel($record)))
                    ->description(fn (User $record): ?View => filled($label = self::serviceDetailsBadgeLabel($record))
                        ? view('filament.tables.columns.badge-description', [
                            'label' => $label,
                            'tooltip' => self::serviceDetailsTooltip($record),
                        ])
                        : null),
                TextColumn::make('department.name')
                    ->label('Associations')
                    ->icon(fn (User $record): ?string => filled($record->department?->name) ? 'heroicon-o-building-office-2' : null)
                    ->state(fn (User $record): ?string => filled($record->department?->name)
                        ? $record->department->name
                        : (filled(self::groupsLabel($record)) ? self::BLANK_DESCRIPTION_STATE : null))
                    ->formatStateUsing(fn (?string $state): string => $state === self::BLANK_DESCRIPTION_STATE ? '' : (string) $state)
                    ->placeholder('—')
                    ->verticallyAlignCenter(fn (User $record): bool => filled($record->department?->name) !== filled(self::groupsLabel($record)))
                    ->tooltip(fn (User $record): ?string => filled($record->department?->name)
                        ? "Department: {$record->department->name}"
                        : null)
                    ->description(fn (User $record): ?View => filled($label = self::groupsLabel($record))
                        ? view('filament.tables.columns.icon-text-description', [
                            'icon' => 'heroicon-o-user-group',
                            'label' => $label,
                            'tooltip' => $record->groups->isNotEmpty()
                                ? 'Group(s): ' . $record->groups->pluck('name')->implode(', ')
                                : null,
                        ])
                        : null),
                IconColumn::make('managed_contact')
                    ->label('Managed Contact')
                    ->state(fn (User $record): bool => $record->managedContact !== null)
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->verticalAlignment(VerticalAlignment::Center)
                    ->tooltip(fn (User $record): ?string => $record->managedContact
                        ? 'Contact Type: ' . ($record->managedContact->type->name ?? '—')
                        : null),
                TextColumn::make('last_logged_in_at')
                    ->label('Last Login')
                    ->dateTime()
                    ->placeholder('Never'),
                TextColumn::make('preferred_name')
                    ->hidden(),
                TextColumn::make('employee_id')
                    ->hidden(),
                TextColumn::make('work_number')
                    ->hidden(),
                TextColumn::make('work_extension')
                    ->hidden(),
                TextColumn::make('mobile')
                    ->hidden(),
                TextColumn::make('student_id')
                    ->hidden(),
            ])
            ->searchable([
                'preferred_name',
                'employee_id',
                'work_number',
                fn (Builder $query, string $search): Builder => $query
                    ->where(new Expression('lower(CAST(work_extension AS TEXT))'), 'like', '%' . Str::lower($search) . '%'),
                'mobile',
                'student_id',
            ])
            ->filters([
                SelectFilter::make('department')
                    ->label('Department')
                    ->relationship('department', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                Impersonate::make(),
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->authorizeIndividualRecords('delete'),
                    AssignDepartmentBulkAction::make()
                        ->visible(function (): bool {
                            /** @var User $user */
                            $user = auth()->user();

                            return $user->can('update', app(User::class));
                        }),
                    AssignRolesBulkAction::make()
                        ->visible(fn () => auth()->user()->can('user.*.update', User::class)),
                    AssignGroupsBulkAction::make()
                        ->authorize(fn (): bool => auth()->user()->can('user.*.update', User::class)),
                ]),
            ]);
    }

    public function getSubheading(): string | Htmlable | null
    {
        // TODO: Either remove or change to show all possible seats

        //return new HtmlString(view('crm-seats', [
        //    'count' => User::count(),
        //    'max' => app(LicenseSettings::class)->data->limits->crmSeats,
        //])->render());

        return null;
    }

    protected function getHeaderActions(): array
    {
        return [
            ImportAction::make()
                ->importer(UserImporter::class)
                ->authorize('import', User::class),
            ExportAction::make()
                ->label('Export')
                ->exporter(UserExporter::class)
                ->authorize('import', User::class),
            CreateAction::make(),
        ];
    }

    private static function serviceDetailsBadgeLabel(User $record): ?string
    {
        $count = self::serviceRequestTypes($record)->count();

        if ($count === 0) {
            return null;
        }

        return "{$count} " . Str::plural('Service Area', $count);
    }

    private static function serviceDetailsTooltip(User $record): ?Htmlable
    {
        $groups = [];

        $manageable = self::manageableServiceRequestTypes($record);
        $auditable = self::auditableServiceRequestTypes($record);

        if ($manageable->isNotEmpty()) {
            $groups[] = 'Manager (Agent): ' . e($manageable->pluck('name')->implode(', '));
        }

        if ($auditable->isNotEmpty()) {
            $groups[] = 'Auditor: ' . e($auditable->pluck('name')->implode(', '));
        }

        if ($groups === []) {
            return null;
        }

        return new HtmlString(implode('<br />', $groups));
    }

    /**
     * @return Collection<int, ServiceRequestType>
     */
    private static function serviceRequestTypes(User $record): Collection
    {
        return self::manageableServiceRequestTypes($record)
            ->merge(self::auditableServiceRequestTypes($record))
            ->unique('id');
    }

    /**
     * Types the user manages directly, through their department, or through their groups.
     *
     * @return Collection<int, ServiceRequestType>
     */
    private static function manageableServiceRequestTypes(User $record): Collection
    {
        return $record->manageableServiceRequestTypes
            ->merge($record->department->manageableServiceRequestTypes ?? [])
            ->merge($record->groups->flatMap(fn (Group $group) => $group->manageableServiceRequestTypes))
            ->unique('id')
            ->values();
    }

    /**
     * Types the user audits directly, through their department, or through their groups.
     *
     * @return Collection<int, ServiceRequestType>
     */
    private static function auditableServiceRequestTypes(User $record): Collection
    {
        return $record->auditableServiceRequestTypes
            ->merge($record->department->auditableServiceRequestTypes ?? [])
            ->merge($record->groups->flatMap(fn (Group $group) => $group->auditableServiceRequestTypes))
            ->unique('id')
            ->values();
    }

    private static function groupsLabel(User $record): ?string
    {
        if ($record->groups->isEmpty()) {
            return null;
        }

        $remaining = $record->groups->count() - 1;

        $label = $record->groups->first()->name;

        return $remaining > 0 ? "{$label} +{$remaining}" : $label;
    }
}
