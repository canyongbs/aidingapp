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

use App\Models\Media;
use Database\Migrations\Concerns\CanModifyPermissions;
use Illuminate\Support\Facades\DB;
use Spatie\LaravelSettings\Migrations\SettingsMigration;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends SettingsMigration {
    use CanModifyPermissions;

    /**
      * @var array<string, string>
      */
    private array $permissions = [
        'care_team.view-any' => 'Care Team',
        'care_team.create' => 'Care Team',
        'care_team.*.view' => 'Care Team',
        'care_team.*.update' => 'Care Team',
        'care_team.*.delete' => 'Care Team',
        'care_team.*.restore' => 'Care Team',
        'care_team.*.force-delete' => 'Care Team',

        'calendar_event.view-any' => 'Calendar Event',
        'calendar_event.create' => 'Calendar Event',
        'calendar_event.*.view' => 'Calendar Event',
        'calendar_event.*.update' => 'Calendar Event',
        'calendar_event.*.delete' => 'Calendar Event',
        'calendar_event.*.restore' => 'Calendar Event',
        'calendar_event.*.force-delete' => 'Calendar Event',

        'event_attendee.view-any' => 'Event Attendee',
        'event_attendee.create' => 'Event Attendee',
        'event_attendee.*.view' => 'Event Attendee',
        'event_attendee.*.update' => 'Event Attendee',
        'event_attendee.*.delete' => 'Event Attendee',
        'event_attendee.*.restore' => 'Event Attendee',
        'event_attendee.*.force-delete' => 'Event Attendee',

        'event.view-any' => 'Event',
        'event.create' => 'Event',
        'event.*.view' => 'Event',
        'event.*.update' => 'Event',
        'event.*.delete' => 'Event',
        'event.*.restore' => 'Event',
        'event.*.force-delete' => 'Event',

        'analytics_resource_category.view-any' => 'Analytics Resource Category',
        'analytics_resource_category.create' => 'Analytics Resource Category',
        'analytics_resource_category.*.view' => 'Analytics Resource Category',
        'analytics_resource_category.*.update' => 'Analytics Resource Category',
        'analytics_resource_category.*.delete' => 'Analytics Resource Category',
        'analytics_resource_category.*.restore' => 'Analytics Resource Category',
        'analytics_resource_category.*.force-delete' => 'Analytics Resource Category',

        'analytics_resource.view-any' => 'Analytics Resource',
        'analytics_resource.create' => 'Analytics Resource',
        'analytics_resource.*.view' => 'Analytics Resource',
        'analytics_resource.*.update' => 'Analytics Resource',
        'analytics_resource.*.delete' => 'Analytics Resource',
        'analytics_resource.*.restore' => 'Analytics Resource',
        'analytics_resource.*.force-delete' => 'Analytics Resource',

        'analytics_resource_source.view-any' => 'Analytics Resource Source',
        'analytics_resource_source.create' => 'Analytics Resource Source',
        'analytics_resource_source.*.view' => 'Analytics Resource Source',
        'analytics_resource_source.*.update' => 'Analytics Resource Source',
        'analytics_resource_source.*.delete' => 'Analytics Resource Source',
        'analytics_resource_source.*.restore' => 'Analytics Resource Source',
        'analytics_resource_source.*.force-delete' => 'Analytics Resource Source',

        'campaign_action.view-any' => 'Campaign Action',
        'campaign_action.create' => 'Campaign Action',
        'campaign_action.*.view' => 'Campaign Action',
        'campaign_action.*.update' => 'Campaign Action',
        'campaign_action.*.delete' => 'Campaign Action',
        'campaign_action.*.restore' => 'Campaign Action',
        'campaign_action.*.force-delete' => 'Campaign Action',

        'campaign.view-any' => 'Campaign',
        'campaign.create' => 'Campaign',
        'campaign.*.view' => 'Campaign',
        'campaign.*.update' => 'Campaign',
        'campaign.*.delete' => 'Campaign',
        'campaign.*.restore' => 'Campaign',
        'campaign.*.force-delete' => 'Campaign',

        'caseload.view-any' => 'Caseload',
        'caseload.create' => 'Caseload',
        'caseload.*.view' => 'Caseload',
        'caseload.*.update' => 'Caseload',
        'caseload.*.delete' => 'Caseload',
        'caseload.*.restore' => 'Caseload',
        'caseload.*.force-delete' => 'Caseload',

        'application.view-any' => 'Application',
        'application.create' => 'Application',
        'application.*.view' => 'Application',
        'application.*.update' => 'Application',
        'application.*.delete' => 'Application',
        'application.*.restore' => 'Application',
        'application.*.force-delete' => 'Application',

        'application_submission_state.view-any' => 'Application Submission State',
        'application_submission_state.create' => 'Application Submission State',
        'application_submission_state.*.view' => 'Application Submission State',
        'application_submission_state.*.update' => 'Application Submission State',
        'application_submission_state.*.delete' => 'Application Submission State',
        'application_submission_state.*.restore' => 'Application Submission State',
        'application_submission_state.*.force-delete' => 'Application Submission State',

        'interaction_campaign.view-any' => 'Interaction Campaign',
        'interaction_campaign.create' => 'Interaction Campaign',
        'interaction_campaign.*.view' => 'Interaction Campaign',
        'interaction_campaign.*.update' => 'Interaction Campaign',
        'interaction_campaign.*.delete' => 'Interaction Campaign',
        'interaction_campaign.*.restore' => 'Interaction Campaign',
        'interaction_campaign.*.force-delete' => 'Interaction Campaign',
        
        'interaction_driver.view-any' => 'Interaction Driver',
        'interaction_driver.create' => 'Interaction Driver',
        'interaction_driver.*.view' => 'Interaction Driver',
        'interaction_driver.*.update' => 'Interaction Driver',
        'interaction_driver.*.delete' => 'Interaction Driver',
        'interaction_driver.*.restore' => 'Interaction Driver',
        'interaction_driver.*.force-delete' => 'Interaction Driver',
        
        'interaction_outcome.view-any' => 'Interaction Outcome',
        'interaction_outcome.create' => 'Interaction Outcome',
        'interaction_outcome.*.view' => 'Interaction Outcome',
        'interaction_outcome.*.update' => 'Interaction Outcome',
        'interaction_outcome.*.delete' => 'Interaction Outcome',
        'interaction_outcome.*.restore' => 'Interaction Outcome',
        'interaction_outcome.*.force-delete' => 'Interaction Outcome',
        
        'interaction_relation.view-any' => 'Interaction Relation',
        'interaction_relation.create' => 'Interaction Relation',
        'interaction_relation.*.view' => 'Interaction Relation',
        'interaction_relation.*.update' => 'Interaction Relation',
        'interaction_relation.*.delete' => 'Interaction Relation',
        'interaction_relation.*.restore' => 'Interaction Relation',
        'interaction_relation.*.force-delete' => 'Interaction Relation',
        
        'interaction_status.view-any' => 'Interaction Status',
        'interaction_status.create' => 'Interaction Status',
        'interaction_status.*.view' => 'Interaction Status',
        'interaction_status.*.update' => 'Interaction Status',
        'interaction_status.*.delete' => 'Interaction Status',
        'interaction_status.*.restore' => 'Interaction Status',
        'interaction_status.*.force-delete' => 'Interaction Status',
        
        'interaction_type.view-any' => 'Interaction Type',
        'interaction_type.create' => 'Interaction Type',
        'interaction_type.*.view' => 'Interaction Type',
        'interaction_type.*.update' => 'Interaction Type',
        'interaction_type.*.delete' => 'Interaction Type',
        'interaction_type.*.restore' => 'Interaction Type',
        'interaction_type.*.force-delete' => 'Interaction Type',
        
        'interaction.view-any' => 'Interaction',
        'interaction.create' => 'Interaction',
        'interaction.*.view' => 'Interaction',
        'interaction.*.update' => 'Interaction',
        'interaction.*.delete' => 'Interaction',
        'interaction.*.restore' => 'Interaction',
        'interaction.*.force-delete' => 'Interaction',
    ];

    /**
    * @var array<string>
    */
    private array $guards = [
        'web',
        'api',
    ];

    public function up(): void
    {
        DB::transaction(function (): void {
            // Delete permissions
            collect($this->guards)
                ->each(fn (string $guard) => $this->deletePermissions(array_keys($this->permissions), $guard));

            // Drop tables
            Schema::dropIfExists('care_teams');

            Schema::dropIfExists('calendar_events');
            Schema::dropIfExists('calendars');
            Schema::dropIfExists('event_registration_form_field_submission');
            Schema::dropIfExists('event_registration_form_fields');
            Schema::dropIfExists('event_registration_form_steps');
            Schema::dropIfExists('event_registration_form_authentications');
            Schema::dropIfExists('event_registration_form_submissions');
            Schema::dropIfExists('event_registration_forms');
            Schema::dropIfExists('event_attendees_entities');
            Schema::dropIfExists('event_attendees');
            Schema::dropIfExists('events');

            Schema::dropIfExists('analytics_resources');
            Schema::dropIfExists('analytics_resource_sources');
            Schema::dropIfExists('analytics_resource_categories');

            Schema::dropIfExists('campaign_actions');
            Schema::dropIfExists('campaigns');

            Schema::dropIfExists('caseload_subjects');
            Schema::dropIfExists('caseloads');

            Schema::dropIfExists('application_field_submission');
            Schema::dropIfExists('application_fields');
            Schema::dropIfExists('application_steps');
            Schema::dropIfExists('application_submissions');
            Schema::dropIfExists('application_authentications');
            Schema::dropIfExists('applications');
            Schema::dropIfExists('application_submission_states');

            // Drop settings
            $this->migrator->deleteIfExists('azure_calendar.is_enabled');
            $this->migrator->deleteIfExists('azure_calendar.client_id');
            $this->migrator->deleteIfExists('azure_calendar.client_secret');
            $this->migrator->deleteIfExists('azure_calendar.tenant_id');

            $this->migrator->deleteIfExists('google_calendar.is_enabled');
            $this->migrator->deleteIfExists('google_calendar.client_id');
            $this->migrator->deleteIfExists('google_calendar.client_secret');
            $this->migrator->deleteIfExists('google_calendar.tenant_id');

            $this->migrator->deleteIfExists('campaign.action_execution_timezone');

            // Drop entries from audit table
            DB::table('audits')->whereIn('auditable_type', [
                'care_team',
                'calendar',
                'calendar_event',
                'event',
                'event_attendee',
                'event_registration_form',
                'event_registration_form_authentication',
                'event_registration_form_field',
                'event_registration_form_step',
                'event_registration_form_submission',
                'analytics_resource_source',
                'analytics_resource_category',
                'analytics_resource',
                'caseload',
                'caseload_subject',
                'campaign',
                'campaign_action',
                'application',
                'application_field',
                'application_submission',
                'application_step',
                'application_authentication',
                'application_submission_state',
            ])->delete();

            // Drop media
            Media::query()
                ->where('model_type', 'analytics_resource')
                ->each(function (Media $media): void {
                    $media->delete();
                });

            // Drop notifications
            DB::table('notifications')->where('notifiable_type', 'event_attendee')->delete();
            DB::table('push_subscriptions')->where('subscribable_type', 'event_attendee')->delete();
        });
    }

    // The down method of this migration is intentionally left blank
    public function down(): void {}
};
