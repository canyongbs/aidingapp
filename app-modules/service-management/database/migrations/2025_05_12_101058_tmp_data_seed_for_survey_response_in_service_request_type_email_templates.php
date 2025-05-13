<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class () extends Migration {
    public function up(): void
    {
        DB::transaction(function () {
            $table = 'service_request_type_email_templates';
            $chunkSize = 100;
            DB::table($table)
                ->where('type', 'survey_response')
                ->whereIn('role', ['auditor', 'manager'])
                ->orderBy('id')
                ->chunkById($chunkSize, function ($records) use ($table) {
                    foreach ($records as $record) {
                        DB::table($table)->where('id', $record->id)->delete();
                    }
                });
        });

        $subject = [
            'type' => 'doc',
            'content' => [
                [
                    'type' => 'paragraph',
                    'attrs' => [
                        'class' => null,
                        'style' => null,
                        'textAlign' => 'start',
                    ],
                    'content' => [
                        [
                            'text' => 'Feedback survey for ',
                            'type' => 'text',
                        ],
                        [
                            'type' => 'mergeTag',
                            'attrs' => ['id' => 'service request number'],
                        ],
                    ],
                ],
            ],
        ];

        $body = [
            'type' => 'doc',
            'content' => [
                [
                    'type' => 'paragraph',
                    'attrs' => [
                        'class' => null,
                        'style' => null,
                        'textAlign' => 'start',
                    ],
                    'content' => [
                        [
                            'text' => 'Hi ',
                            'type' => 'text',
                            'marks' => [
                                ['type' => 'bold'],
                            ],
                        ],
                        [
                            'type' => 'mergeTag',
                            'attrs' => ['id' => 'assigned to'],
                            'marks' => [
                                ['type' => 'bold'],
                            ],
                        ],
                        [
                            'text' => ',',
                            'type' => 'text',
                            'marks' => [
                                ['type' => 'bold'],
                            ],
                        ],
                    ],
                ],
                [
                    'type' => 'paragraph',
                    'attrs' => [
                        'class' => null,
                        'style' => null,
                        'textAlign' => 'start',
                    ],
                    'content' => [
                        [
                            'text' => "To help us serve you better in the future, we'd love to hear about your experience with our support team.",
                            'type' => 'text',
                        ],
                    ],
                ],
                [
                    'type' => 'tiptapBlock',
                    'attrs' => [
                        'data' => [
                            'alignment' => 'center',
                            'take_survey' => 'Take Survey',
                        ],
                        'type' => 'surveyResponseEmailTemplateTakeSurveyButtonBlock',
                    ],
                ],
                [
                    'type' => 'paragraph',
                    'attrs' => [
                        'class' => null,
                        'style' => null,
                        'textAlign' => 'start',
                    ],
                    'content' => [
                        ['type' => 'hardBreak'],
                        [
                            'text' => 'We appreciate your time and we value your feedback!',
                            'type' => 'text',
                        ],
                        ['type' => 'hardBreak'],
                        [
                            'text' => 'Thank You.',
                            'type' => 'text',
                        ],
                        ['type' => 'hardBreak'],
                    ],
                ],
            ],
        ];

        $subjectJson = json_encode($subject);
        $bodyJson = json_encode($body);

        DB::transaction(function () use ($subjectJson, $bodyJson) {
            DB::table('service_request_types')->orderBy('id')->chunkById(100, function ($types) use ($subjectJson, $bodyJson) {
                foreach ($types as $type) {
                    $existing = DB::table('service_request_type_email_templates')->where([
                        'service_request_type_id' => $type->id,
                        'type' => 'survey_response',
                        'role' => 'customer',
                    ])->first();

                    if ($existing) {
                        DB::table('service_request_type_email_templates')->where('id', $existing->id)->update([
                            'subject' => $subjectJson,
                            'body' => $bodyJson,
                            'updated_at' => now(),
                        ]);
                    } else {
                        DB::table('service_request_type_email_templates')->insert([
                            'id' => (string) Str::orderedUuid(),
                            'service_request_type_id' => $type->id,
                            'type' => 'survey_response',
                            'role' => 'customer',
                            'subject' => $subjectJson,
                            'body' => $bodyJson,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            });
        });
    }

    public function down(): void
    {
        DB::table('service_request_type_email_templates')
            ->where('type', 'survey_response')
            ->where('role', 'customer')
            ->delete();
    }
};
