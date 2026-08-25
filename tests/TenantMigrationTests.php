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

use AidingApp\Engagement\Models\EmailTemplate;
use AidingApp\Engagement\Models\Engagement;
use AidingApp\Engagement\Models\EngagementBatch;
use AidingApp\ServiceManagement\Models\ServiceRequestCustomEmailTemplate;
use AidingApp\ServiceManagement\Models\ServiceRequestNotificationAutomationEmailTemplate;
use AidingApp\ServiceManagement\Models\ServiceRequestTypeEmailTemplate;
use AidingApp\Theme\Settings\ThemeSettings;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Artisan;

use function Tests\richContentText;
use function Tests\richContentWith;

if (! function_exists('plantLiteralMergeTagContent')) {
    function plantLiteralMergeTagContent(Model $model, string $attribute, string $text): void
    {
        $model->setAttribute($attribute, richContentText($text));
        $model->saveQuietly();
    }
}

describe('2026_07_30_182335_tmp_data_convert_literal_merge_tags_in_engagement_rich_content', function () {
    $migrationName = '2026_07_30_182335_tmp_data_convert_literal_merge_tags_in_engagement_rich_content';
    $migrationPath = "app-modules/engagement/database/migrations/{$migrationName}.php";

    it('converts literal merge tags across the engagement tables', function () use ($migrationName, $migrationPath) {
        isolatedMigration($migrationName, function () use ($migrationPath) {
            $engagement = Engagement::factory()->create();
            $batch = EngagementBatch::factory()->create();
            $emailTemplate = EmailTemplate::factory()->create();

            plantLiteralMergeTagContent($engagement, 'body', 'Hello {{ contact full name }}!');
            plantLiteralMergeTagContent($batch, 'body', 'Hello {{ contact full name }}!');
            plantLiteralMergeTagContent($emailTemplate, 'content', 'Reach you at {{ contact email }}?');

            $migrate = Artisan::call('migrate', ['--path' => $migrationPath]);

            expect($migrate)->toBe(Command::SUCCESS);

            $expectedGreeting = richContentWith([
                ['type' => 'text', 'text' => 'Hello '],
                ['type' => 'mergeTag', 'attrs' => ['id' => 'contact full name']],
                ['type' => 'text', 'text' => '!'],
            ]);

            expect($engagement->refresh()->body)->toEqual($expectedGreeting)
                ->and($batch->refresh()->body)->toEqual($expectedGreeting)
                ->and($emailTemplate->refresh()->content)->toEqual(richContentWith([
                    ['type' => 'text', 'text' => 'Reach you at '],
                    ['type' => 'mergeTag', 'attrs' => ['id' => 'contact email']],
                    ['type' => 'text', 'text' => '?'],
                ]));
        });
    });

    it('leaves content without a recognised merge tag untouched', function () use ($migrationName, $migrationPath) {
        isolatedMigration($migrationName, function () use ($migrationPath) {
            $engagement = Engagement::factory()->create();

            plantLiteralMergeTagContent($engagement, 'body', 'Hello {{ not a merge tag }}!');

            $migrate = Artisan::call('migrate', ['--path' => $migrationPath]);

            expect($migrate)->toBe(Command::SUCCESS);

            expect($engagement->refresh()->body)->toEqual(richContentText('Hello {{ not a merge tag }}!'));
        });
    });
});

describe('2026_07_30_182417_tmp_data_convert_literal_merge_tags_in_service_request_email_templates', function () {
    $migrationName = '2026_07_30_182417_tmp_data_convert_literal_merge_tags_in_service_request_email_templates';
    $migrationPath = "app-modules/service-management/database/migrations/{$migrationName}.php";

    it('converts literal merge tags in the subject and body of every template table', function () use ($migrationName, $migrationPath) {
        isolatedMigration($migrationName, function () use ($migrationPath) {
            $typeTemplate = ServiceRequestTypeEmailTemplate::factory()->create();
            $automationTemplate = ServiceRequestNotificationAutomationEmailTemplate::factory()->create();
            $customTemplate = ServiceRequestCustomEmailTemplate::factory()->create();

            plantLiteralMergeTagContent($typeTemplate, 'subject', '{{ title }}');
            plantLiteralMergeTagContent($typeTemplate, 'body', "Hello {{ recipient's name }}!");
            plantLiteralMergeTagContent($automationTemplate, 'subject', '{{ title }}');
            plantLiteralMergeTagContent($automationTemplate, 'body', 'Assigned to {{ assigned manager }}');
            plantLiteralMergeTagContent($customTemplate, 'subject', '{{ title }}');
            plantLiteralMergeTagContent($customTemplate, 'body', "Hi {{ contact's name }}!");

            $migrate = Artisan::call('migrate', ['--path' => $migrationPath]);

            expect($migrate)->toBe(Command::SUCCESS);

            $expectedSubject = richContentWith([['type' => 'mergeTag', 'attrs' => ['id' => 'title']]]);

            expect($typeTemplate->refresh()->subject)->toEqual($expectedSubject)
                ->and($typeTemplate->body)->toEqual(richContentWith([
                    ['type' => 'text', 'text' => 'Hello '],
                    ['type' => 'mergeTag', 'attrs' => ['id' => 'recipient name']],
                    ['type' => 'text', 'text' => '!'],
                ]))
                ->and($automationTemplate->refresh()->subject)->toEqual($expectedSubject)
                ->and($automationTemplate->body)->toEqual(richContentWith([
                    ['type' => 'text', 'text' => 'Assigned to '],
                    ['type' => 'mergeTag', 'attrs' => ['id' => 'assigned staff name']],
                ]))
                ->and($customTemplate->refresh()->subject)->toEqual($expectedSubject)
                ->and($customTemplate->body)->toEqual(richContentWith([
                    ['type' => 'text', 'text' => 'Hi '],
                    ['type' => 'mergeTag', 'attrs' => ['id' => 'contact name']],
                    ['type' => 'text', 'text' => '!'],
                ]));
        });
    });

    it('leaves content without a recognised merge tag untouched', function () use ($migrationName, $migrationPath) {
        isolatedMigration($migrationName, function () use ($migrationPath) {
            $typeTemplate = ServiceRequestTypeEmailTemplate::factory()->create();

            plantLiteralMergeTagContent($typeTemplate, 'body', 'Hello {{ not a merge tag }}!');

            $migrate = Artisan::call('migrate', ['--path' => $migrationPath]);

            expect($migrate)->toBe(Command::SUCCESS);

            expect($typeTemplate->refresh()->body)->toEqual(richContentText('Hello {{ not a merge tag }}!'));
        });
    });
});

describe('2026_08_18_130641_tmp_seed_profile_menu_and_login_home_theme_settings_for_existing_tenants', function () {
    $migrationName = '2026_08_18_130641_tmp_seed_profile_menu_and_login_home_theme_settings_for_existing_tenants';
    $migrationPath = "database/migrations/{$migrationName}.php";

    it('seeds the default profile menu and login and home target settings for existing tenants', function () use ($migrationName, $migrationPath) {
        isolatedMigration($migrationName, function () use ($migrationPath) {
            $settings = app(ThemeSettings::class);
            $settings->refresh();

            expect($settings->is_support_url_enabled)->toBeFalse()
                ->and($settings->support_url)->toBeNull()
                ->and($settings->is_recent_updates_url_enabled)->toBeFalse()
                ->and($settings->recent_updates_url)->toBeNull()
                ->and($settings->changelog_url)->toBeNull()
                ->and($settings->product_resource_hub_url)->toBeNull();

            $migrate = Artisan::call('migrate', ['--path' => $migrationPath]);

            expect($migrate)->toBe(Command::SUCCESS);

            $settings->refresh();

            expect($settings->is_support_url_enabled)->toBeTrue()
                ->and($settings->support_url)->toBe(ThemeSettings::DEFAULT_SUPPORT_URL)
                ->and($settings->is_recent_updates_url_enabled)->toBeTrue()
                ->and($settings->recent_updates_url)->toBe(ThemeSettings::DEFAULT_RECENT_UPDATES_URL)
                ->and($settings->changelog_url)->toBe(ThemeSettings::DEFAULT_CHANGELOG_URL)
                ->and($settings->product_resource_hub_url)->toBe(ThemeSettings::DEFAULT_PRODUCT_RESOURCE_HUB_URL);
        });
    });

    it('preserves urls an admin has already configured', function () use ($migrationName, $migrationPath) {
        isolatedMigration($migrationName, function () use ($migrationPath) {
            $settings = app(ThemeSettings::class);
            $settings->refresh();
            $settings->support_url = 'https://example.com/custom-support';
            $settings->recent_updates_url = 'https://example.com/custom-updates';
            $settings->changelog_url = 'https://example.com/custom-changelog';
            $settings->product_resource_hub_url = 'https://example.com/custom-hub';
            $settings->save();

            $migrate = Artisan::call('migrate', ['--path' => $migrationPath]);

            expect($migrate)->toBe(Command::SUCCESS);

            $settings->refresh();

            expect($settings->support_url)->toBe('https://example.com/custom-support')
                ->and($settings->recent_updates_url)->toBe('https://example.com/custom-updates')
                ->and($settings->changelog_url)->toBe('https://example.com/custom-changelog')
                ->and($settings->product_resource_hub_url)->toBe('https://example.com/custom-hub')
                ->and($settings->is_support_url_enabled)->toBeTrue()
                ->and($settings->is_recent_updates_url_enabled)->toBeTrue();
        });
    });
});

// Example migration test, leave commented out for future use as a template/example
//describe('2025_01_01_165527_tmp_data_do_a_thing', function () {
//    it('properly changed the data', function () {
//        isolatedMigration(
//            '2025_01_01_165527_tmp_data_do_a_thing',
//            function () {
//                // Setup data before migration
//
//                // Run the migration
//                $migrate = Artisan::call('migrate', ['--path' => 'app/database/migrations/2025_01_01_165527_tmp_data_do_a_thing.php']);
//                // Confirm migration ran successfully
//                expect($migrate)->toBe(Command::SUCCESS);
//
//                // Add any assertions to verify the migration's effects
//            }
//        );
//    });
//});
