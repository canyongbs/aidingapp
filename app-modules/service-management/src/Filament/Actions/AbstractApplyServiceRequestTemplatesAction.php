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

namespace AidingApp\ServiceManagement\Filament\Actions;

use AidingApp\ServiceManagement\Filament\Tables\ServiceRequestTypesTable;
use AidingApp\ServiceManagement\Models\ServiceRequestCustomEmailTemplate;
use AidingApp\ServiceManagement\Models\ServiceRequestNotificationAutomationEmailTemplate;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use AidingApp\ServiceManagement\Models\ServiceRequestTypeEmailTemplate;
use App\Enums\Feature;
use App\Models\User;
use App\Support\RichContentDocument;
use Filament\Actions\Action;
use Filament\Forms\Components\TableSelect;
use Filament\Forms\Components\ToggleButtons;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\Gate;

abstract class AbstractApplyServiceRequestTemplatesAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $sourceLabel = $this->getSourceLabel();

        $this
            ->label('Apply')
            ->icon('heroicon-m-arrow-down-on-square-stack')
            ->authorize(function (): bool {
                if (! Gate::check(Feature::ServiceManagement->getGateName())) {
                    return false;
                }

                $user = auth()->user();
                assert($user instanceof User);

                return $user->isSuperAdmin();
            })
            ->slideOver()
            ->modalHeading("Apply {$sourceLabel}")
            ->modalDescription("Applying {$sourceLabel} will overwrite any existing customized templates for the affected service request type(s). This action cannot be undone.")
            ->modalSubmitActionLabel('Apply')
            ->schema([
                ToggleButtons::make('apply_to')
                    ->label('Apply to')
                    ->options([
                        'all' => 'All',
                        'select' => 'Select',
                    ])
                    ->default('all')
                    ->inline()
                    ->live()
                    ->required(),
                TableSelect::make('service_request_type_ids')
                    ->label('Service Request Types')
                    ->multiple()
                    ->tableConfiguration(ServiceRequestTypesTable::class)
                    ->visible(fn (Get $get): bool => $get('apply_to') === 'select')
                    ->required(fn (Get $get): bool => $get('apply_to') === 'select'),
            ])
            ->action(function (array $data) use ($sourceLabel): void {
                $sourceTemplates = $this->getSourceTemplatesQuery()
                    ->select(['id', 'type', 'role', 'subject', 'body'])
                    ->cursor()
                    ->filter(function (Model $template): bool {
                        /** @var ServiceRequestNotificationAutomationEmailTemplate|ServiceRequestCustomEmailTemplate $template */
                        return RichContentDocument::hasContent($template->subject) && RichContentDocument::hasContent($template->body);
                    })
                    ->collect();

                if ($sourceTemplates->isEmpty()) {
                    Notification::make()
                        ->warning()
                        ->title("No {$sourceLabel} to apply")
                        ->body($this->getNoContentBodyMessage())
                        ->send();

                    return;
                }

                $serviceRequestTypesQuery = $this->getServiceRequestTypesQuery($data);

                if ($serviceRequestTypesQuery === null) {
                    Notification::make()
                        ->warning()
                        ->title('No service request types selected')
                        ->send();

                    return;
                }

                $serviceRequestTypesCount = $serviceRequestTypesQuery->count();

                if ($serviceRequestTypesCount === 0) {
                    Notification::make()
                        ->warning()
                        ->title('No service request types found')
                        ->send();

                    return;
                }

                $this->applyTemplates($serviceRequestTypesQuery, $sourceTemplates);

                Notification::make()
                    ->success()
                    ->title(ucfirst($sourceLabel) . ' applied')
                    ->body("Applied {$sourceLabel} to {$serviceRequestTypesCount} service request type(s).")
                    ->send();
            });
    }

    /** @phpstan-ignore missingType.generics (Intentionally generic-free so concrete subclasses can specify their own model generics without a childReturnType conflict.) */
    abstract protected function getSourceTemplatesQuery(): Builder;

    /**
     * The lowercase label used in messages, e.g. "base templates" or "custom templates".
     */
    abstract protected function getSourceLabel(): string;

    /**
     * The body shown when there are no source templates with content to apply.
     */
    abstract protected function getNoContentBodyMessage(): string;

    /**
     * @param array<string, mixed> $data
     *
     * @return ?Builder<ServiceRequestType>
     */
    protected function getServiceRequestTypesQuery(array $data): ?Builder
    {
        if (($data['apply_to'] ?? 'all') !== 'select') {
            return ServiceRequestType::query();
        }

        $typeIds = array_filter((array) ($data['service_request_type_ids'] ?? []));

        if (blank($typeIds)) {
            return null;
        }

        return ServiceRequestType::query()->whereKey($typeIds);
    }

    /**
     * @param Builder<ServiceRequestType> $serviceRequestTypesQuery
     * @param SupportCollection<int, mixed> $sourceTemplates
     */
    protected function applyTemplates(Builder $serviceRequestTypesQuery, SupportCollection $sourceTemplates): void
    {
        $now = now();

        $sourceTemplates = $sourceTemplates->map(function (mixed $sourceTemplate) {
            return [
                'type' => $sourceTemplate->type->value,
                'role' => $sourceTemplate->role->value,
                'subject' => $this->encodeRichContent($sourceTemplate->subject),
                'body' => $this->encodeRichContent($sourceTemplate->body),
            ];
        });

        $serviceRequestTypesQuery
            ->select('id')
            ->chunkById(100, function (Collection $serviceRequestTypes) use ($sourceTemplates, $now): void {
                $rows = [];

                foreach ($serviceRequestTypes as $serviceRequestType) {
                    foreach ($sourceTemplates as $sourceTemplate) {
                        $rows[] = [
                            'service_request_type_id' => $serviceRequestType->getKey(),
                            'type' => $sourceTemplate['type'],
                            'role' => $sourceTemplate['role'],
                            'subject' => $sourceTemplate['subject'],
                            'body' => $sourceTemplate['body'],
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }
                }

                foreach (array_chunk($rows, 1000) as $chunk) {
                    ServiceRequestTypeEmailTemplate::query()->upsert(
                        $chunk,
                        ['service_request_type_id', 'type', 'role'],
                        ['subject', 'body', 'updated_at'],
                    );
                }
            });
    }

    /**
     * @param array<string, mixed>|null $value
     */
    protected function encodeRichContent(?array $value): ?string
    {
        return is_null($value) ? null : json_encode($value);
    }
}
