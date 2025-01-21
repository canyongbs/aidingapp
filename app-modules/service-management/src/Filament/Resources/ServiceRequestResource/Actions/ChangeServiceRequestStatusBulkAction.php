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

namespace AidingApp\ServiceManagement\Filament\Resources\ServiceRequestResource\Actions;

use AidingApp\ServiceManagement\Enums\SystemServiceRequestClassification;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestStatus;
use App\Models\Authenticatable;
use App\Support\BulkProcessingMachine;
use Closure;
use Filament\Forms\Components\Select;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;

class ChangeServiceRequestStatusBulkAction
{
    public static function make(): BulkAction
    {
        return BulkAction::make('changeServiceRequestStatus')
            ->label('Change status')
            ->icon('heroicon-m-pencil-square')
            ->modalHeading('Bulk change service request statuses')
            ->modalWidth('md')
            ->form([
                Select::make('statusId')
                    ->label('New status')
                    ->options(ServiceRequestStatus::query()->pluck('name', 'id'))
                    ->exists(ServiceRequestStatus::class, 'id')
                    ->required(),
            ])
            ->action(function (array $data, Collection $records) {
                $records->loadMissing(['priority.type.managers', 'respondent', 'status']);

                $user = auth()->user();
                $isUserSuperAdmin = $user->hasRole(Authenticatable::SUPER_ADMIN_ROLE);
                $canUserUpdateServiceRequest = $user->can('service_request.*.update');

                BulkProcessingMachine::make($records->all())
                    ->check(function (ServiceRequest $serviceRequest) use ($user, $canUserUpdateServiceRequest): ?Closure {
                        $user = auth()->user();

                        if (
                            $user->hasLicense($serviceRequest->respondent->getLicenseType()) &&
                            $canUserUpdateServiceRequest
                        ) {
                            return null;
                        }

                        return function (int $count): string {
                            if ($count === 1) {
                                return '1 service request cannot be updated because you do not have permission.';
                            }

                            return "{$count} service requests cannot be updated because you do not have permission.";
                        };
                    })
                    ->check(function (ServiceRequest $serviceRequest): ?Closure {
                        if ($serviceRequest?->status?->classification !== SystemServiceRequestClassification::Closed) {
                            return null;
                        }

                        return function (int $count): string {
                            if ($count === 1) {
                                return '1 service request is closed and cannot be edited.';
                            }

                            return "{$count} service requests are closed and cannot be edited.";
                        };
                    })
                    ->check(function (ServiceRequest $serviceRequest) use ($user, $isUserSuperAdmin): ?Closure {
                        if ($isUserSuperAdmin) {
                            return null;
                        }

                        $team = $user->teams->first();

                        if ($serviceRequest?->priority?->type?->managers?->contains('id', $team?->getKey())) {
                            return null;
                        }

                        return function (int $count): string {
                            if ($count === 1) {
                                return "You don't have permission to update 1 service request because you're not a manager of its type.";
                            }

                            return "You don't have permission to update {$count} service requests because you're not a manager of their types.";
                        };
                    })
                    ->process(function (ServiceRequest $serviceRequest) use ($data) {
                        $serviceRequest->status()->associate($data['statusId']);
                        $serviceRequest->save();
                    })
                    ->sendNotification(function (int $successCount): string {
                        if (! $successCount) {
                            return 'No service request statuses have been updated.';
                        }

                        if ($successCount === 1) {
                            return '1 service request status has been updated.';
                        }

                        return "{$successCount} service request statuses have been updated.";
                    });
            });
    }
}
