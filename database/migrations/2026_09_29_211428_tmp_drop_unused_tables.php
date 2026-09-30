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
