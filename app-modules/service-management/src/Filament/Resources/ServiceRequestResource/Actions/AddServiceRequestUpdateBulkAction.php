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

use AidingApp\ServiceManagement\Enums\ServiceRequestUpdateDirection;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestUpdate;
use App\Features\ServiceRequestUpdateCreatedByFeature;
use App\Support\BulkProcessingMachine;
use Closure;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;

class AddServiceRequestUpdateBulkAction
{
    public static function make(): BulkAction
    {
        return BulkAction::make('addServiceRequestUpdate')
            ->label('Add Request Update')
            ->icon('heroicon-m-pencil-square')
            ->modalHeading('Bulk add service request update')
            ->modalWidth('md')
            ->modalSubmitActionLabel('Add Update')
            ->form([
                Textarea::make('update')
                    ->label('Update')
                    ->rows(3)
                    ->columnSpan('full')
                    ->required()
                    ->string(),
                Toggle::make('internal')
                    ->label('Internal')
                    ->rule(['boolean']),
            ])
            ->action(function (array $data, Collection $records) {
                $records->loadMissing(['priority.type.managers', 'serviceRequestUpdates']);

                $user = auth()->user();

                BulkProcessingMachine::make($records->all())
                    ->check(function (ServiceRequest $serviceRequest) use ($user): ?Closure {
                        $user = auth()->user();

                        if (
                            $user->hasLicense($serviceRequest->respondent->getLicenseType()) &&
                            $user->can('service_request.*.update') && $user->can('service_request_update.create')
                        ) {
                            return null;
                        }

                        return function (int $count): string {
                            if ($count === 1) {
                                return 'Service request update cannot be created for 1 service request because you do not have permission.';
                            }

                            return "Service request update cannot be created for {$count} service requests because you do not have permission.";
                        };
                    })
                    ->check(function (ServiceRequest $serviceRequest) use ($user): ?Closure {
                        if ($user->isSuperAdmin()) {
                            return null;
                        }

                        $team = $user->team;

                        if ($serviceRequest?->priority?->type?->managers?->contains('id', $team?->getKey())) {
                            return null;
                        }

                        return function (int $count): string {
                            if ($count === 1) {
                                return "You don't have permission to add service request update to 1 service request because you're not a manager of its type.";
                            }

                            return "You don't have permission to add service request update to {$count} service requests because you're not a manager of their types.";
                        };
                    })
                    ->process(function (ServiceRequest $serviceRequest) use ($data) {
                        $update = new ServiceRequestUpdate([
                            'service_request_id' => $serviceRequest->getKey(),
                            'update' => $data['update'],
                            'internal' => $data['internal'],
                        ]);

                        if (ServiceRequestUpdateCreatedByFeature::active()) {
                            $update->createdBy()->associate(auth()->user());
                        } else {
                            $update->direction = ServiceRequestUpdateDirection::Outbound;
                        }

                        $update->saveOrFail();
                    })
                    ->sendNotification(function (int $successCount): string {
                        if (! $successCount) {
                            return 'No service request update have been created for any service requests.';
                        }

                        if ($successCount === 1) {
                            return 'Service request update has been created for 1 service request.';
                        }

                        return "Service request update has been created for {$successCount} service requests.";
                    });
            });
    }
}
