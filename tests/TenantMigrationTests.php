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

use AidingApp\ServiceManagement\Models\ServiceRequestTypeEmailTemplate;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

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

test('2026_04_16_095944_tmp_data_migrate_service_request_type_email_templates_to_rich_editor_format transforms tiptapBlock to customBlock in body', function () {
    isolatedMigration(
        '2026_04_16_095944_tmp_data_migrate_service_request_type_email_templates_to_rich_editor_format',
        function () {
            $template = ServiceRequestTypeEmailTemplate::factory()->createQuietly([
                'body' => [
                    'type' => 'doc',
                    'content' => [
                        [
                            'type' => 'paragraph',
                            'content' => [['type' => 'text', 'text' => 'Hello']],
                        ],
                        [
                            'type' => 'tiptapBlock',
                            'attrs' => [
                                'id' => 'some-uuid',
                                'type' => 'serviceRequestTypeEmailTemplateButtonBlock',
                                'data' => [
                                    'label' => 'View Service Request',
                                    'alignment' => 'center',
                                ],
                            ],
                        ],
                    ],
                ],
            ]);

            $migrate = Artisan::call('migrate', ['--path' => 'app-modules/service-management/database/migrations/2026_04_16_095944_tmp_data_migrate_service_request_type_email_templates_to_rich_editor_format.php']);

            expect($migrate)->toBe(Command::SUCCESS);

            $content = json_decode((string) DB::table('service_request_type_email_templates')->where('id', $template->id)->value('body'), associative: true); /** @phpstan-ignore-line */

            /** @phpstan-ignore-next-line */
            expect($content['content'][1]['type'])->toBe('customBlock');
            /** @phpstan-ignore-next-line */
            expect($content['content'][1]['attrs']['id'])->toBe('serviceRequestTypeEmailTemplateButtonBlock');
            /** @phpstan-ignore-next-line */
            expect($content['content'][1]['attrs']['config']['label'])->toBe('View Service Request');
            /** @phpstan-ignore-next-line */
            expect($content['content'][1]['attrs']['config']['alignment'])->toBe('center');
        }
    );
});

test('2026_04_16_095944_tmp_data_migrate_service_request_type_email_templates_to_rich_editor_format transforms surveyResponseEmailTemplateTakeSurveyButtonBlock', function () {
    isolatedMigration(
        '2026_04_16_095944_tmp_data_migrate_service_request_type_email_templates_to_rich_editor_format',
        function () {
            $template = ServiceRequestTypeEmailTemplate::factory()->createQuietly([
                'body' => [
                    'type' => 'doc',
                    'content' => [
                        [
                            'type' => 'tiptapBlock',
                            'attrs' => [
                                'id' => 'survey-uuid',
                                'type' => 'surveyResponseEmailTemplateTakeSurveyButtonBlock',
                                'data' => [
                                    'label' => 'Take Survey',
                                    'alignment' => 'center',
                                ],
                            ],
                        ],
                    ],
                ],
            ]);

            $migrate = Artisan::call('migrate', ['--path' => 'app-modules/service-management/database/migrations/2026_04_16_095944_tmp_data_migrate_service_request_type_email_templates_to_rich_editor_format.php']);

            expect($migrate)->toBe(Command::SUCCESS);

            $content = json_decode((string) DB::table('service_request_type_email_templates')->where('id', $template->id)->value('body'), associative: true); /** @phpstan-ignore-line */

            /** @phpstan-ignore-next-line */
            expect($content['content'][0]['type'])->toBe('customBlock');
            /** @phpstan-ignore-next-line */
            expect($content['content'][0]['attrs']['id'])->toBe('surveyResponseEmailTemplateTakeSurveyButtonBlock');
            /** @phpstan-ignore-next-line */
            expect($content['content'][0]['attrs']['config']['label'])->toBe('Take Survey');
        }
    );
});

test('2026_04_16_095944_tmp_data_migrate_service_request_type_email_templates_to_rich_editor_format transforms textStyle marks to textColor', function () {
    isolatedMigration(
        '2026_04_16_095944_tmp_data_migrate_service_request_type_email_templates_to_rich_editor_format',
        function () {
            $template = ServiceRequestTypeEmailTemplate::factory()->createQuietly([
                'subject' => [
                    'type' => 'doc',
                    'content' => [
                        [
                            'type' => 'paragraph',
                            'content' => [
                                [
                                    'type' => 'text',
                                    'text' => 'Colored text',
                                    'marks' => [
                                        [
                                            'type' => 'textStyle',
                                            'attrs' => ['color' => '#ff0000'],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ]);

            $migrate = Artisan::call('migrate', ['--path' => 'app-modules/service-management/database/migrations/2026_04_16_095944_tmp_data_migrate_service_request_type_email_templates_to_rich_editor_format.php']);

            expect($migrate)->toBe(Command::SUCCESS);

            $content = json_decode((string) DB::table('service_request_type_email_templates')->where('id', $template->id)->value('subject'), associative: true); /** @phpstan-ignore-line */

            /** @phpstan-ignore-next-line */
            expect($content['content'][0]['content'][0]['marks'][0]['type'])->toBe('textColor');
            /** @phpstan-ignore-next-line */
            expect($content['content'][0]['content'][0]['marks'][0]['attrs']['data-color'])->toBe('#ff0000');
            /** @phpstan-ignore-next-line */
            expect($content['content'][0]['content'][0]['marks'][0]['attrs'])->not->toHaveKey('color');
        }
    );
});

test('2026_04_16_095944_tmp_data_migrate_service_request_type_email_templates_to_rich_editor_format transforms grid attributes', function () {
    isolatedMigration(
        '2026_04_16_095944_tmp_data_migrate_service_request_type_email_templates_to_rich_editor_format',
        function () {
            $template = ServiceRequestTypeEmailTemplate::factory()->createQuietly([
                'body' => [
                    'type' => 'doc',
                    'content' => [
                        [
                            'type' => 'grid',
                            'attrs' => ['type' => 'responsive', 'cols' => '2'],
                            'content' => [
                                ['type' => 'gridColumn', 'attrs' => [], 'content' => [['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'Col 1']]]]],
                                ['type' => 'gridColumn', 'attrs' => [], 'content' => [['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'Col 2']]]]],
                            ],
                        ],
                    ],
                ],
            ]);

            $migrate = Artisan::call('migrate', ['--path' => 'app-modules/service-management/database/migrations/2026_04_16_095944_tmp_data_migrate_service_request_type_email_templates_to_rich_editor_format.php']);

            expect($migrate)->toBe(Command::SUCCESS);

            $content = json_decode((string) DB::table('service_request_type_email_templates')->where('id', $template->id)->value('body'), associative: true); /** @phpstan-ignore-line */

            /** @phpstan-ignore-next-line */
            expect($content['content'][0]['attrs']['data-cols'])->toBe('2');
            /** @phpstan-ignore-next-line */
            expect($content['content'][0]['attrs']['data-from-breakpoint'])->toBe('lg');
            /** @phpstan-ignore-next-line */
            expect($content['content'][0]['content'][0]['attrs']['data-col-span'])->toBe('1');
        }
    );
});

test('2026_04_16_095944_tmp_data_migrate_service_request_type_email_templates_to_rich_editor_format does not modify unchanged content', function () {
    isolatedMigration(
        '2026_04_16_095944_tmp_data_migrate_service_request_type_email_templates_to_rich_editor_format',
        function () {
            $originalContent = [
                'type' => 'doc',
                'content' => [
                    [
                        'type' => 'paragraph',
                        'content' => [['type' => 'text', 'text' => 'Simple text']],
                    ],
                ],
            ];

            $template = ServiceRequestTypeEmailTemplate::factory()->createQuietly([
                'subject' => $originalContent,
                'body' => $originalContent,
            ]);

            $migrate = Artisan::call('migrate', ['--path' => 'app-modules/service-management/database/migrations/2026_04_16_095944_tmp_data_migrate_service_request_type_email_templates_to_rich_editor_format.php']);

            expect($migrate)->toBe(Command::SUCCESS);

            $subject = json_decode((string) DB::table('service_request_type_email_templates')->where('id', $template->id)->value('subject'), associative: true); /** @phpstan-ignore-line */
            $body = json_decode((string) DB::table('service_request_type_email_templates')->where('id', $template->id)->value('body'), associative: true); /** @phpstan-ignore-line */
            expect($subject)->toEqual($originalContent);
            expect($body)->toEqual($originalContent);
        }
    );
});

test('2026_04_16_095944_tmp_data_migrate_service_request_type_email_templates_to_rich_editor_format removes unsupported video nodes', function () {
    isolatedMigration(
        '2026_04_16_095944_tmp_data_migrate_service_request_type_email_templates_to_rich_editor_format',
        function () {
            $template = ServiceRequestTypeEmailTemplate::factory()->createQuietly([
                'body' => [
                    'type' => 'doc',
                    'content' => [
                        [
                            'type' => 'youtube',
                            'attrs' => ['src' => 'https://www.youtube.com/embed/dQw4w9WgXcQ'],
                        ],
                        [
                            'type' => 'vimeo',
                            'attrs' => ['src' => 'https://player.vimeo.com/video/123456789'],
                        ],
                        [
                            'type' => 'videoEmbed',
                            'attrs' => ['src' => 'https://example.com/video.mp4', 'type' => 'video'],
                        ],
                        [
                            'type' => 'paragraph',
                            'content' => [['type' => 'text', 'text' => 'Keep me']],
                        ],
                    ],
                ],
            ]);

            $migrate = Artisan::call('migrate', ['--path' => 'app-modules/service-management/database/migrations/2026_04_16_095944_tmp_data_migrate_service_request_type_email_templates_to_rich_editor_format.php']);

            expect($migrate)->toBe(Command::SUCCESS);

            $content = json_decode((string) DB::table('service_request_type_email_templates')->where('id', $template->id)->value('body'), associative: true); /** @phpstan-ignore-line */

            /** @phpstan-ignore-next-line */
            expect(count($content['content']))->toBe(1);
            /** @phpstan-ignore-next-line */
            expect($content['content'][0]['type'])->toBe('paragraph');
            /** @phpstan-ignore-next-line */
            expect($content['content'][0]['content'][0]['text'])->toBe('Keep me');
        }
    );
});

test('2026_04_16_095944_tmp_data_migrate_service_request_type_email_templates_to_rich_editor_format removes hurdles and preserves children', function () {
    isolatedMigration(
        '2026_04_16_095944_tmp_data_migrate_service_request_type_email_templates_to_rich_editor_format',
        function () {
            $template = ServiceRequestTypeEmailTemplate::factory()->createQuietly([
                'body' => [
                    'type' => 'doc',
                    'content' => [
                        [
                            'type' => 'hurdle',
                            'attrs' => ['color' => 'gray_light'],
                            'content' => [
                                ['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'Inside hurdle']]],
                            ],
                        ],
                        [
                            'type' => 'paragraph',
                            'content' => [['type' => 'text', 'text' => 'After hurdle']],
                        ],
                    ],
                ],
            ]);

            $migrate = Artisan::call('migrate', ['--path' => 'app-modules/service-management/database/migrations/2026_04_16_095944_tmp_data_migrate_service_request_type_email_templates_to_rich_editor_format.php']);

            expect($migrate)->toBe(Command::SUCCESS);

            $content = json_decode((string) DB::table('service_request_type_email_templates')->where('id', $template->id)->value('body'), associative: true); /** @phpstan-ignore-line */

            /** @phpstan-ignore-next-line */
            expect($content['content'][0]['type'])->toBe('paragraph');
            /** @phpstan-ignore-next-line */
            expect($content['content'][0]['content'][0]['text'])->toBe('Inside hurdle');
            /** @phpstan-ignore-next-line */
            expect($content['content'][1]['type'])->toBe('paragraph');
            /** @phpstan-ignore-next-line */
            expect($content['content'][1]['content'][0]['text'])->toBe('After hurdle');
        }
    );
});

test('2026_04_16_095944_tmp_data_migrate_service_request_type_email_templates_to_rich_editor_format transforms oversized images', function () {
    isolatedMigration(
        '2026_04_16_095944_tmp_data_migrate_service_request_type_email_templates_to_rich_editor_format',
        function () {
            $template = ServiceRequestTypeEmailTemplate::factory()->createQuietly([
                'body' => [
                    'type' => 'doc',
                    'content' => [
                        [
                            'type' => 'paragraph',
                            'content' => [
                                [
                                    'type' => 'image',
                                    'attrs' => [
                                        'id' => 'test-uuid',
                                        'src' => null,
                                        'width' => 800,
                                        'height' => 600,
                                    ],
                                ],
                            ],
                        ],
                        [
                            'type' => 'paragraph',
                            'content' => [
                                [
                                    'type' => 'image',
                                    'attrs' => [
                                        'id' => 'small-uuid',
                                        'src' => null,
                                        'width' => 300,
                                        'height' => 200,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ]);

            $migrate = Artisan::call('migrate', ['--path' => 'app-modules/service-management/database/migrations/2026_04_16_095944_tmp_data_migrate_service_request_type_email_templates_to_rich_editor_format.php']);

            expect($migrate)->toBe(Command::SUCCESS);

            $content = json_decode((string) DB::table('service_request_type_email_templates')->where('id', $template->id)->value('body'), associative: true); /** @phpstan-ignore-line */

            /** @phpstan-ignore-next-line */
            expect($content['content'][0]['content'][0]['attrs']['width'])->toBeNull();
            /** @phpstan-ignore-next-line */
            expect($content['content'][0]['content'][0]['attrs']['height'])->toBeNull();

            /** @phpstan-ignore-next-line */
            expect($content['content'][1]['content'][0]['attrs']['width'])->toBe(300);
            /** @phpstan-ignore-next-line */
            expect($content['content'][1]['content'][0]['attrs']['height'])->toBe(200);
        }
    );
});

test('2026_04_16_095944_tmp_data_migrate_service_request_type_email_templates_to_rich_editor_format unwraps grids and preserves inner content', function () {
    isolatedMigration(
        '2026_04_16_095944_tmp_data_migrate_service_request_type_email_templates_to_rich_editor_format',
        function () {
            $template = ServiceRequestTypeEmailTemplate::factory()->createQuietly([
                'body' => [
                    'type' => 'doc',
                    'content' => [
                        [
                            'type' => 'grid',
                            'attrs' => ['type' => 'responsive', 'cols' => '2'],
                            'content' => [
                                ['type' => 'gridColumn', 'content' => [['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'Left']]]]],
                                ['type' => 'gridColumn', 'content' => [['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'Right']]]]],
                            ],
                        ],
                    ],
                ],
            ]);

            $migrate = Artisan::call('migrate', ['--path' => 'app-modules/service-management/database/migrations/2026_04_16_095944_tmp_data_migrate_service_request_type_email_templates_to_rich_editor_format.php']);

            expect($migrate)->toBe(Command::SUCCESS);

            $content = json_decode((string) DB::table('service_request_type_email_templates')->where('id', $template->id)->value('body'), associative: true); /** @phpstan-ignore-line */

            /** @phpstan-ignore-next-line */
            expect(count($content['content']))->toBe(2);
            /** @phpstan-ignore-next-line */
            expect($content['content'][0]['type'])->toBe('paragraph');
            /** @phpstan-ignore-next-line */
            expect($content['content'][0]['content'][0]['text'])->toBe('Left');
            /** @phpstan-ignore-next-line */
            expect($content['content'][1]['content'][0]['text'])->toBe('Right');
        }
    );
});

test('2026_04_16_095944_tmp_data_migrate_service_request_type_email_templates_to_rich_editor_format transforms checkedList', function () {
    isolatedMigration(
        '2026_04_16_095944_tmp_data_migrate_service_request_type_email_templates_to_rich_editor_format',
        function () {
            $template = ServiceRequestTypeEmailTemplate::factory()->createQuietly([
                'body' => [
                    'type' => 'doc',
                    'content' => [
                        [
                            'type' => 'checkedList',
                            'content' => [
                                ['type' => 'checkedListItem', 'attrs' => ['checked' => true], 'content' => [['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'Item 1']]]]],
                                ['type' => 'checkedListItem', 'attrs' => ['checked' => false], 'content' => [['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'Item 2']]]]],
                            ],
                        ],
                    ],
                ],
            ]);

            $migrate = Artisan::call('migrate', ['--path' => 'app-modules/service-management/database/migrations/2026_04_16_095944_tmp_data_migrate_service_request_type_email_templates_to_rich_editor_format.php']);

            expect($migrate)->toBe(Command::SUCCESS);

            $content = json_decode((string) DB::table('service_request_type_email_templates')->where('id', $template->id)->value('body'), associative: true); /** @phpstan-ignore-line */

            /** @phpstan-ignore-next-line */
            expect($content['content'][0]['type'])->toBe('bulletList');
            /** @phpstan-ignore-next-line */
            expect($content['content'][0]['content'][0]['type'])->toBe('listItem');
            /** @phpstan-ignore-next-line */
            expect($content['content'][0]['content'][0]['attrs'])->not->toHaveKey('checked');
            /** @phpstan-ignore-next-line */
            expect($content['content'][0]['content'][1]['type'])->toBe('listItem');
        }
    );
});
