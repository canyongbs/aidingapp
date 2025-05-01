<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class () extends Migration {
    public function up(): void
    {
        DB::transaction(function () {
            DB::table('service_request_type_email_templates')->truncate();

            $types = [
                'created' => [
                    'subject' => [
                        'type' => 'doc',
                        'content' => [[
                            'type' => 'paragraph',
                            'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                            'content' => [
                                ['text' => 'Service request ', 'type' => 'text'],
                                ['type' => 'mergeTag', 'attrs' => ['id' => 'service request number']],
                                ['text' => ' ', 'type' => 'text'],
                                ['type' => 'mergeTag', 'attrs' => ['id' => 'created']],
                            ],
                        ]],
                    ],
                    'body' => [
                        'type' => 'doc',
                        'content' => [
                            ['type' => 'paragraph', 'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'], 'content' => [['text' => 'Hello!', 'type' => 'text', 'marks' => [['type' => 'bold']]]]],
                            ['type' => 'paragraph', 'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'], 'content' => [
                                ['text' => 'The Service request ', 'type' => 'text'],
                                ['type' => 'mergeTag', 'attrs' => ['id' => 'service request number']],
                                ['text' => ' has been created.', 'type' => 'text'],
                            ]],
                        ],
                    ],
                ],
                'assigned' => [
                    'subject' => [
                        'type' => 'doc',
                        'content' => [[
                            'type' => 'paragraph',
                            'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                            'content' => [
                                ['text' => 'Service Request ', 'type' => 'text'],
                                ['type' => 'mergeTag', 'attrs' => ['id' => 'service request number']],
                                ['text' => ' ', 'type' => 'text'],
                                ['type' => 'mergeTag', 'attrs' => ['id' => 'assigned to']],
                                ['text' => ' agent', 'type' => 'text'],
                            ],
                        ]],
                    ],
                    'body' => [
                        'type' => 'doc',
                        'content' => [
                            ['type' => 'paragraph', 'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'], 'content' => [['text' => 'Hello!', 'type' => 'text']]],
                            ['type' => 'paragraph', 'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'], 'content' => [
                                ['text' => 'The service request ', 'type' => 'text'],
                                ['type' => 'mergeTag', 'attrs' => ['id' => 'service request number']],
                                ['text' => ' has been ', 'type' => 'text'],
                                ['type' => 'mergeTag', 'attrs' => ['id' => 'assigned to']],
                                ['text' => ' an agent.', 'type' => 'text'],
                            ]],
                        ],
                    ],
                ],
                'update' => [
                    'subject' => [
                        'type' => 'doc',
                        'content' => [[
                            'type' => 'paragraph',
                            'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                            'content' => [
                                ['text' => 'New ', 'type' => 'text'],
                                ['type' => 'mergeTag', 'attrs' => ['id' => 'updated']],
                                ['text' => ' on service request ', 'type' => 'text'],
                                ['type' => 'mergeTag', 'attrs' => ['id' => 'service request number']],
                                ['text' => ' ', 'type' => 'text'],
                            ],
                        ]],
                    ],
                    'body' => [
                        'type' => 'doc',
                        'content' => [
                            ['type' => 'paragraph', 'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'], 'content' => [['text' => 'Hello!', 'type' => 'text', 'marks' => [['type' => 'bold']]]]],
                            ['type' => 'paragraph', 'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'], 'content' => [
                                ['text' => 'The service request ', 'type' => 'text'],
                                ['type' => 'mergeTag', 'attrs' => ['id' => 'service request number']],
                                ['text' => ' has a new ', 'type' => 'text'],
                                ['type' => 'mergeTag', 'attrs' => ['id' => 'updated']],
                                ['text' => '.', 'type' => 'text'],
                            ]],
                        ],
                    ],
                ],
                'status_change' => [
                    'subject' => [
                        'type' => 'doc',
                        'content' => [[
                            'type' => 'paragraph',
                            'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                            'content' => [
                                ['text' => 'Service request ', 'type' => 'text'],
                                ['type' => 'mergeTag', 'attrs' => ['id' => 'service request number']],
                                ['text' => ' status changed to ', 'type' => 'text'],
                                ['type' => 'mergeTag', 'attrs' => ['id' => 'status']],
                            ],
                        ]],
                    ],
                    'body' => [
                        'type' => 'doc',
                        'content' => [
                            ['type' => 'paragraph', 'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'], 'content' => [['text' => 'Hello!', 'type' => 'text', 'marks' => [['type' => 'bold']]]]],
                            ['type' => 'paragraph', 'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'], 'content' => [
                                ['text' => 'The service request ', 'type' => 'text'],
                                ['type' => 'mergeTag', 'attrs' => ['id' => 'service request number']],
                                ['text' => ' status has changed to ', 'type' => 'text'],
                                ['type' => 'mergeTag', 'attrs' => ['id' => 'status']],
                                ['text' => '.', 'type' => 'text'],
                            ]],
                        ],
                    ],
                ],
                'closed' => [
                    'subject' => [
                        'type' => 'doc',
                        'content' => [[
                            'type' => 'paragraph',
                            'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                            'content' => [
                                ['text' => 'Service request ', 'type' => 'text'],
                                ['type' => 'mergeTag', 'attrs' => ['id' => 'service request number']],
                                ['text' => ' closed', 'type' => 'text'],
                            ],
                        ]],
                    ],
                    'body' => [
                        'type' => 'doc',
                        'content' => [
                            ['type' => 'paragraph', 'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'], 'content' => [['text' => 'Hello!', 'type' => 'text', 'marks' => [['type' => 'bold']]]]],
                            ['type' => 'paragraph', 'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'], 'content' => [
                                ['text' => 'The service request ', 'type' => 'text'],
                                ['type' => 'mergeTag', 'attrs' => ['id' => 'service request number']],
                                ['text' => ' has been closed.', 'type' => 'text'],
                            ]],
                        ],
                    ],
                ],
            ];

            $roles = ['manager', 'auditor', 'customer'];

            DB::table('service_request_types')
                ->select('id')
                ->orderBy('id')
                ->chunkById(100, function ($typesChunk) use ($types, $roles) {
                    $now = now();

                    foreach ($typesChunk as $type) {
                        foreach ($types as $emailType => $content) {
                            foreach ($roles as $role) {
                                DB::table('service_request_type_email_templates')->insert([
                                    'id' => Str::uuid(),
                                    'service_request_type_id' => $type->id,
                                    'type' => $emailType,
                                    'role' => $role,
                                    'subject' => json_encode($content['subject']),
                                    'body' => json_encode($content['body']),
                                    'created_at' => $now,
                                    'updated_at' => $now,
                                ]);
                            }
                        }
                    }
                });
        });
    }

    public function down(): void
    {
        DB::table('service_request_type_email_templates')->truncate();
    }
};
