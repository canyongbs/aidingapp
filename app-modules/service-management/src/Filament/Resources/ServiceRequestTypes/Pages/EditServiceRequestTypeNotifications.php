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

namespace AidingApp\ServiceManagement\Filament\Resources\ServiceRequestTypes\Pages;

use AidingApp\ServiceManagement\Enums\ServiceRequestEmailTemplateType;
use AidingApp\ServiceManagement\Enums\ServiceRequestNotificationChannel;
use AidingApp\ServiceManagement\Enums\ServiceRequestTypeEmailTemplateRole;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestTypes\ServiceRequestTypeResource;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use AidingApp\ServiceManagement\Models\ServiceRequestTypeEmailPreference;
use App\Concerns\EditPageRedirection;
use App\Features\ServiceRequestTypeEmailPreferenceFeature;
use Filament\Forms\Components\ViewField;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EditServiceRequestTypeNotifications extends EditRecord
{
    use EditPageRedirection;

    protected static string $resource = ServiceRequestTypeResource::class;

    protected static ?string $title = 'Notifications';

    protected static ?string $breadcrumb = 'Notifications';

    protected static ?string $navigationLabel = 'Notifications';

    /** @var list<array<string, mixed>> */
    private array $preferencesToUpsert = [];

    public function getRelationManagers(): array
    {
        // Needed to prevent Filament from loading the relation managers on this page.
        return [];
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Notifications and Alerts')
                    ->description('This page is used to configure notifications and alerts for this service request type.')
                    ->schema([
                        ViewField::make('settings')
                            ->rules(['array'])
                            ->view('service-management::filament.resources.service-request-type-resource.pages.edit-service-request-type-notifications.matrix'),
                    ])
                    ->extraAttributes(['class' => 'fi-section-no-content-padding']),
            ]);
    }

    /**
     * @param  array<string, mixed>  $data
     *
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $record = $this->getRecord();
        assert($record instanceof ServiceRequestType);

        // TODO: Remove this block when ServiceRequestTypeEmailPreferenceFeature is removed.
        if (! ServiceRequestTypeEmailPreferenceFeature::active()) {
            $data['settings'] = $record->only($this->generateLegacySettingsAttributeList());

            return $data;
        }

        // Eagerly load all preferences (both channels) for this service request type.
        $preferences = $record->emailPreferences()->get();

        $settings = [];

        foreach (ServiceRequestEmailTemplateType::cases() as $templateType) {
            foreach (ServiceRequestTypeEmailTemplateRole::cases() as $templateRole) {
                $eventSlug = $this->getEventSlug($templateType);
                $roleSlug = $templateRole->value . 's';

                foreach (ServiceRequestNotificationChannel::cases() as $channel) {
                    $preference = $preferences->first(
                        fn (ServiceRequestTypeEmailPreference $preference): bool => $preference->service_request_email_template_type === $templateType
                            && $preference->service_request_email_template_role === $templateRole
                            && $preference->notification_channel === $channel,
                    );

                    // Only set the key when a pivot row actually exists for this combination.
                    // Missing combinations (e.g. survey_response + notification) are simply
                    // absent from the settings array so the blade view can skip them naturally.
                    if ($preference !== null) {
                        $settings["is_{$roleSlug}_{$eventSlug}_{$channel->value}_enabled"] = $preference->is_enabled;
                    }
                }
            }
        }

        $data['settings'] = $settings;

        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     *
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // TODO: Remove this block when ServiceRequestTypeEmailPreferenceFeature is removed.
        if (! ServiceRequestTypeEmailPreferenceFeature::active()) {
            $data = [
                ...$data,
                ...collect($data['settings'])
                    ->only($this->generateLegacySettingsAttributeList())
                    ->filter(fn (mixed $value): bool => is_bool($value))
                    ->all(),
            ];

            unset($data['settings']);

            return $data;
        }

        $settings = $data['settings'] ?? [];
        $record = $this->getRecord();
        assert($record instanceof ServiceRequestType);

        $this->preferencesToUpsert = [];

        foreach (ServiceRequestEmailTemplateType::cases() as $templateType) {
            foreach (ServiceRequestTypeEmailTemplateRole::cases() as $templateRole) {
                $eventSlug = $this->getEventSlug($templateType);
                $roleSlug = $templateRole->value . 's';

                foreach (ServiceRequestNotificationChannel::cases() as $channel) {
                    $key = "is_{$roleSlug}_{$eventSlug}_{$channel->value}_enabled";

                    // Skip combinations that have no checkbox in the matrix
                    // (e.g. survey_response + notification for any role).
                    if (! array_key_exists($key, $settings)) {
                        continue;
                    }

                    $this->preferencesToUpsert[] = [
                        'service_request_type_id' => $record->id,
                        'service_request_email_template_type' => $templateType->value,
                        'service_request_email_template_role' => $templateRole->value,
                        'notification_channel' => $channel->value,
                        'is_enabled' => (bool) ($settings[$key] ?? false),
                    ];
                }
            }
        }

        // Nothing from the notification/email checkboxes is written to the model anymore;
        // all preferences live in the pivot table.
        unset($data['settings']);

        return $data;
    }

    protected function afterSave(): void
    {
        // TODO: Remove this guard when ServiceRequestTypeEmailPreferenceFeature is removed.
        if (! ServiceRequestTypeEmailPreferenceFeature::active()) {
            return;
        }

        ServiceRequestTypeEmailPreference::upsert(
            $this->preferencesToUpsert,
            uniqueBy: ['service_request_type_id', 'service_request_email_template_type', 'service_request_email_template_role', 'notification_channel'],
            update: ['is_enabled'],
        );
    }

    /**
     * Returns the event slug for the given template type.
     * Delegates to the enum method to avoid duplication.
     */
    private function getEventSlug(ServiceRequestEmailTemplateType $type): string
    {
        return $type->getEventSlug();
    }

    /**
     * Returns the combined list of all email + notification boolean attribute names
     * that are stored directly on the service_request_types table (legacy behaviour).
     *
     * TODO: Remove this method when ServiceRequestTypeEmailPreferenceFeature is removed.
     *
     * @return array<int, string>
     */
    private function generateLegacySettingsAttributeList(): array
    {
        $attributes = [];

        foreach (['managers', 'auditors', 'customers'] as $role) {
            foreach (
                [
                    'service_request_created',
                    'service_request_assigned',
                    'service_request_update',
                    'service_request_status_change',
                    'service_request_closed',
                    'survey_response',
                ] as $event
            ) {
                $attributes[] = "is_{$role}_{$event}_email_enabled";
                $attributes[] = "is_{$role}_{$event}_notification_enabled";
            }
        }

        return $attributes;
    }
}
