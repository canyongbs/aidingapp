<?php

use App\Models\Media;
use Illuminate\Support\Facades\DB;
use Spatie\LaravelSettings\Migrations\SettingsMigration;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class () extends SettingsMigration {
    public function up(): void
    {
        DB::transaction(function (): void {
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
