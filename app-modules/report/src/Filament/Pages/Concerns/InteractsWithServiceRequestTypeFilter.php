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

namespace AidingApp\Report\Filament\Pages\Concerns;

use AidingApp\ServiceManagement\Filament\Resources\ServiceRequests\Pages\ListServiceRequests;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use App\Models\User;
use CodeWithDennis\FilamentSelectTree\SelectTree;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;

/**
 * Shared "Service Request Types" report filter: the type SelectTree plus the
 * "My Affiliated Types" and "Clear" quick actions.
 * The consuming page must be a Filament dashboard using the `HasFiltersForm`
 * concern so that `updatedFilters()` is available.
 */
trait InteractsWithServiceRequestTypeFilter
{
    /**
     * @return array<int, string>
     */
    public function getAffiliatedServiceRequestTypeIds(): array
    {
        $user = auth()->user();
        assert($user instanceof User);

        $departmentId = $user->department?->getKey();

        return ServiceRequestType::query()
            ->withoutArchived()
            ->where(function (Builder $query) use ($user, $departmentId): void {
                $query->whereHas('managerUsers', fn (Builder $query) => $query->whereKey($user->getKey()))
                    ->orWhereHas('auditorUsers', fn (Builder $query) => $query->whereKey($user->getKey()));

                if (blank($departmentId)) {
                    return;
                }

                $query->orWhereHas('managerDepartments', fn (Builder $query) => $query->whereKey($departmentId))
                    ->orWhereHas('auditorDepartments', fn (Builder $query) => $query->whereKey($departmentId));
            })
            ->orderBy('name')
            ->pluck('id')
            ->all();
    }

    /**
     * The Service Request Types filter components, ready to be placed inside a filters section.
     *
     * Each component spans the full width of its section, so this works inside both single- and
     * multi-column filter sections.
     *
     * @return array<int, Component>
     */
    protected function serviceRequestTypeFilterComponents(): array
    {
        return [
            SelectTree::make('serviceRequestTypes')
                ->label('Service Request Types')
                ->getTreeUsing(fn (): array => ListServiceRequests::buildTypeTreeOptions(withoutArchived: true))
                ->multiple()
                ->searchable()
                ->live()
                ->placeholder('All')
                ->columnSpanFull(),
            Actions::make([
                Action::make('loadAffiliatedServiceRequestTypes')
                    ->label('My Affiliated Types')
                    ->icon(Heroicon::UserGroup)
                    ->action(function (Set $set): void {
                        $affiliatedTypeIds = $this->getAffiliatedServiceRequestTypeIds();

                        if (blank($affiliatedTypeIds)) {
                            Notification::make()
                                ->title('You are not a manager or auditor of any Service Request Types')
                                ->warning()
                                ->send();

                            return;
                        }

                        $set('serviceRequestTypes', $affiliatedTypeIds);

                        $this->updatedFilters();
                    }),
                Action::make('clearServiceRequestTypes')
                    ->label('Clear')
                    ->color('gray')
                    ->icon(Heroicon::XMark)
                    ->disabled(fn (Get $get): bool => blank($get('serviceRequestTypes')))
                    ->action(function (Set $set): void {
                        $set('serviceRequestTypes', []);

                        $this->updatedFilters();
                    }),
            ])
                ->key('serviceRequestTypeActions')
                ->columnSpanFull(),
        ];
    }
}
