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

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class () extends Migration {
    public function up(): void
    {
        DB::transaction(function () {
            DB::table('service_request_types')->chunkById(100, function ($serviceRequestTypes) {
                foreach ($serviceRequestTypes as $serviceRequestType) {
                    $roles = ['manager', 'auditor', 'customer'];
                    $templates = ['created', 'assigned', 'update', 'status_change', 'closed'];

                    foreach ($templates as $template) {
                        foreach ($roles as $role) {
                            $subject = $this->getSubject($template, $role);
                            $body = $this->getBody($template, $role);

                            $existing = DB::table('service_request_type_email_templates')
                                ->where('service_request_type_id', $serviceRequestType->id)
                                ->where('type', $template)
                                ->where('role', $role)
                                ->first();

                            if ($existing) {
                                DB::table('service_request_type_email_templates')
                                    ->where('id', $existing->id)
                                    ->update([
                                        'subject' => json_encode($subject),
                                        'body' => json_encode($body),
                                        'updated_at' => now(),
                                    ]);
                            } else {
                                DB::table('service_request_type_email_templates')->insert([
                                    'id' => (string) Str::orderedUuid(),
                                    'service_request_type_id' => $serviceRequestType->id,
                                    'type' => $template,
                                    'role' => $role,
                                    'subject' => json_encode($subject),
                                    'body' => json_encode($body),
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ]);
                            }
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

    /**
     * @param string $template
     * @param string $role
     *
     * @return array<string, mixed>
     */
    private function getSubject(string $template, string $role): array
    {
        $subjects = [
            'created' => [
                'manager' => [
                    'type' => 'doc',
                    'content' => [[
                        'type' => 'paragraph',
                        'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                        'content' => [
                            ['text' => 'Service request ', 'type' => 'text'],
                            ['type' => 'mergeTag', 'attrs' => ['id' => 'service request number']],
                            ['text' => ' created', 'type' => 'text'],
                        ],
                    ]],
                ],
                'auditor' => [
                    'type' => 'doc',
                    'content' => [[
                        'type' => 'paragraph',
                        'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                        'content' => [
                            ['text' => 'Service request ', 'type' => 'text'],
                            ['type' => 'mergeTag', 'attrs' => ['id' => 'service request number']],
                            ['text' => ' created', 'type' => 'text'],
                        ],
                    ]],
                ],
                'customer' => [
                    'type' => 'doc',
                    'content' => [[
                        'type' => 'paragraph',
                        'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                        'content' => [
                            ['type' => 'mergeTag', 'attrs' => ['id' => 'service request number']],
                            ['text' => ' - is now ', 'type' => 'text'],
                            ['type' => 'mergeTag', 'attrs' => ['id' => 'status']],
                            ['text' => ' ', 'type' => 'text'],
                        ],
                    ]],
                ],
            ],
            'assigned' => [
                'manager' => [
                    'type' => 'doc',
                    'content' => [[
                        'type' => 'paragraph',
                        'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                        'content' => [
                            ['text' => 'Service request ', 'type' => 'text'],
                            ['type' => 'mergeTag', 'attrs' => ['id' => 'service request number']],
                            ['text' => ' assigned to agent', 'type' => 'text'],
                        ],
                    ]],
                ],
                'auditor' => [
                    'type' => 'doc',
                    'content' => [[
                        'type' => 'paragraph',
                        'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                        'content' => [
                            ['text' => 'Service request ', 'type' => 'text'],
                            ['type' => 'mergeTag', 'attrs' => ['id' => 'service request number']],
                            ['text' => ' assigned to agent', 'type' => 'text'],
                        ],
                    ]],
                ],
                'customer' => [
                    'type' => 'doc',
                    'content' => [[
                        'type' => 'paragraph',
                        'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                        'content' => [
                            ['text' => 'Your service request ', 'type' => 'text'],
                            ['type' => 'mergeTag', 'attrs' => ['id' => 'service request number']],
                            ['text' => ' has been assigned to agent', 'type' => 'text'],
                        ],
                    ]],
                ],
            ],
            'update' => [
                'manager' => [
                    'type' => 'doc',
                    'content' => [[
                        'type' => 'paragraph',
                        'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                        'content' => [
                            ['text' => 'New update on service request ', 'type' => 'text'],
                            ['type' => 'mergeTag', 'attrs' => ['id' => 'service request number']],
                            ['text' => ' ', 'type' => 'text'],
                        ],
                    ]],
                ],
                'auditor' => [
                    'type' => 'doc',
                    'content' => [[
                        'type' => 'paragraph',
                        'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                        'content' => [
                            ['text' => 'New update on service request ', 'type' => 'text'],
                            ['type' => 'mergeTag', 'attrs' => ['id' => 'service request number']],
                            ['text' => ' ', 'type' => 'text'],
                        ],
                    ]],
                ],
                'customer' => [
                    'type' => 'doc',
                    'content' => [[
                        'type' => 'paragraph',
                        'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                        'content' => [
                            ['text' => "There's an update on your service request ", 'type' => 'text'],
                            ['type' => 'mergeTag', 'attrs' => ['id' => 'service request number']],
                            ['text' => ' ', 'type' => 'text'],
                        ],
                    ]],
                ],
            ],
            'status_change' => [
                'manager' => [
                    'type' => 'doc',
                    'content' => [[
                        'type' => 'paragraph',
                        'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                        'content' => [
                            ['text' => 'Service request ', 'type' => 'text'],
                            ['type' => 'mergeTag', 'attrs' => ['id' => 'service request number']],
                            ['text' => ' status changed to ', 'type' => 'text'],
                            ['type' => 'mergeTag', 'attrs' => ['id' => 'status']],
                            ['text' => ' ', 'type' => 'text'],
                        ],
                    ]],
                ],
                'auditor' => [
                    'type' => 'doc',
                    'content' => [[
                        'type' => 'paragraph',
                        'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                        'content' => [
                            ['text' => 'Service request ', 'type' => 'text'],
                            ['type' => 'mergeTag', 'attrs' => ['id' => 'service request number']],
                            ['text' => ' status changed to ', 'type' => 'text'],
                            ['type' => 'mergeTag', 'attrs' => ['id' => 'status']],
                            ['text' => ' ', 'type' => 'text'],
                        ],
                    ]],
                ],
                'customer' => [
                    'type' => 'doc',
                    'content' => [[
                        'type' => 'paragraph',
                        'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                        'content' => [
                            ['text' => 'Status update: Service request ', 'type' => 'text'],
                            ['type' => 'mergeTag', 'attrs' => ['id' => 'service request number']],
                            ['text' => ' is now ', 'type' => 'text'],
                            ['type' => 'mergeTag', 'attrs' => ['id' => 'status']],
                            ['text' => ' ', 'type' => 'text'],
                        ],
                    ]],
                ],
            ],
            'closed' => [
                'manager' => [
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
                'auditor' => [
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
                'customer' => [
                    'type' => 'doc',
                    'content' => [[
                        'type' => 'paragraph',
                        'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                        'content' => [
                            ['text' => '[Ticket #', 'type' => 'text'],
                            ['type' => 'mergeTag', 'attrs' => ['id' => 'service request number']],
                            ['text' => ']: Your Issue Has Been Resolved ', 'type' => 'text'],
                        ],
                    ]],
                ],
            ],
        ];

        return $subjects[$template][$role] ?? [];
    }

    /**
     * @param string $template
     * @param string $role
     *
     * @return array<string, mixed>
     */
    private function getBody(string $template, string $role): array
    {
        $bodies = [
            'created' => [
                'manager' => [
                    'type' => 'doc',
                    'content' => [
                        [
                            'type' => 'paragraph',
                            'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                            'content' => [['text' => 'Hello!', 'type' => 'text', 'marks' => [['type' => 'bold']]]],
                        ],
                        [
                            'type' => 'paragraph',
                            'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                            'content' => [
                                ['text' => 'The service request ', 'type' => 'text'],
                                ['type' => 'mergeTag', 'attrs' => ['id' => 'service request number']],
                                ['text' => ' has been created.', 'type' => 'text'],
                            ],
                        ],
                        [
                            'type' => 'tiptapBlock',
                            'attrs' => [
                                'data' => ['alignment' => 'center'],
                                'type' => 'serviceRequestTypeEmailTemplateButtonBlock',
                            ],
                        ],
                    ],
                ],
                'auditor' => [
                    'type' => 'doc',
                    'content' => [
                        [
                            'type' => 'paragraph',
                            'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                            'content' => [['text' => 'Hello!', 'type' => 'text', 'marks' => [['type' => 'bold']]]],
                        ],
                        [
                            'type' => 'paragraph',
                            'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                            'content' => [
                                ['text' => 'The service request ', 'type' => 'text'],
                                ['type' => 'mergeTag', 'attrs' => ['id' => 'service request number']],
                                ['text' => ' has been created.', 'type' => 'text'],
                            ],
                        ],
                        [
                            'type' => 'tiptapBlock',
                            'attrs' => [
                                'data' => ['alignment' => 'center'],
                                'type' => 'serviceRequestTypeEmailTemplateButtonBlock',
                            ],
                        ],
                    ],
                ],
                'customer' => [
                    'type' => 'doc',
                    'content' => [
                        [
                            'type' => 'paragraph',
                            'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                            'content' => [
                                ['text' => 'Hello ', 'type' => 'text', 'marks' => [['type' => 'bold']]],
                                ['type' => 'mergeTag', 'attrs' => ['id' => 'assigned to'], 'marks' => [['type' => 'bold']]],
                                ['text' => ',', 'type' => 'text', 'marks' => [['type' => 'bold']]],
                            ],
                        ],
                        [
                            'type' => 'paragraph',
                            'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                            'content' => [
                                ['text' => 'A new ', 'type' => 'text'],
                                ['type' => 'mergeTag', 'attrs' => ['id' => 'type']],
                                ['text' => ' service request has been created and is now in a ', 'type' => 'text'],
                                ['type' => 'mergeTag', 'attrs' => ['id' => 'status']],
                                ['text' => ' status. Your new ticket number is: ', 'type' => 'text'],
                                ['type' => 'mergeTag', 'attrs' => ['id' => 'service request number']],
                                ['text' => '. ', 'type' => 'text'],
                            ],
                        ],
                        [
                            'type' => 'paragraph',
                            'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                            'content' => [['text' => 'The details of your service request are shown below:', 'type' => 'text']],
                        ],
                        [
                            'type' => 'paragraph',
                            'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                            'content' => [
                                ['type' => 'mergeTag', 'attrs' => ['id' => 'description']],
                            ],
                        ],
                    ],
                ],
            ],
            'assigned' => [
                'manager' => [
                    'type' => 'doc',
                    'content' => [
                        [
                            'type' => 'paragraph',
                            'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                            'content' => [['text' => 'Hello!', 'type' => 'text', 'marks' => [['type' => 'bold']]]],
                        ],
                        [
                            'type' => 'paragraph',
                            'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                            'content' => [
                                ['text' => 'The service request ', 'type' => 'text'],
                                ['type' => 'mergeTag', 'attrs' => ['id' => 'service request number']],
                                ['text' => ' has been assigned to an agent.', 'type' => 'text'],
                            ],
                        ],
                        [
                            'type' => 'tiptapBlock',
                            'attrs' => [
                                'data' => ['alignment' => 'center'],
                                'type' => 'serviceRequestTypeEmailTemplateButtonBlock',
                            ],
                        ],
                    ],
                ],
                'auditor' => [
                    'type' => 'doc',
                    'content' => [
                        [
                            'type' => 'paragraph',
                            'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                            'content' => [['text' => 'Hello!', 'type' => 'text', 'marks' => [['type' => 'bold']]]],
                        ],
                        [
                            'type' => 'paragraph',
                            'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                            'content' => [
                                ['text' => 'The service request ', 'type' => 'text'],
                                ['type' => 'mergeTag', 'attrs' => ['id' => 'service request number']],
                                ['text' => ' has been assigned to an agent.', 'type' => 'text'],
                            ],
                        ],
                        [
                            'type' => 'tiptapBlock',
                            'attrs' => [
                                'data' => ['alignment' => 'center'],
                                'type' => 'serviceRequestTypeEmailTemplateButtonBlock',
                            ],
                        ],
                    ],
                ],
                'customer' => [
                    'type' => 'doc',
                    'content' => [
                        [
                            'type' => 'paragraph',
                            'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                            'content' => [
                                ['text' => 'Hello ', 'type' => 'text', 'marks' => [['type' => 'bold']]],
                                ['type' => 'mergeTag', 'attrs' => ['id' => 'assigned to'], 'marks' => [['type' => 'bold']]],
                                ['text' => ',', 'type' => 'text', 'marks' => [['type' => 'bold']]],
                            ],
                        ],
                        [
                            'type' => 'paragraph',
                            'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                            'content' => [
                                ['text' => "We've assigned an agent to your ", 'type' => 'text'],
                                ['type' => 'mergeTag', 'attrs' => ['id' => 'service request number']],
                                ['text' => '. They will review it and follow up shortly.', 'type' => 'text'],
                            ],
                        ],
                        [
                            'type' => 'tiptapBlock',
                            'attrs' => [
                                'data' => ['alignment' => 'center'],
                                'type' => 'serviceRequestTypeEmailTemplateButtonBlock',
                            ],
                        ],
                    ],
                ],
            ],
            'update' => [
                'manager' => [
                    'type' => 'doc',
                    'content' => [
                        [
                            'type' => 'paragraph',
                            'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                            'content' => [['text' => 'Hello!', 'type' => 'text', 'marks' => [['type' => 'bold']]]],
                        ],
                        [
                            'type' => 'paragraph',
                            'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                            'content' => [
                                ['text' => 'The service request ', 'type' => 'text'],
                                ['type' => 'mergeTag', 'attrs' => ['id' => 'service request number']],
                                ['text' => ' has a new update.', 'type' => 'text'],
                            ],
                        ],
                        [
                            'type' => 'tiptapBlock',
                            'attrs' => [
                                'data' => ['alignment' => 'center'],
                                'type' => 'serviceRequestTypeEmailTemplateButtonBlock',
                            ],
                        ],
                    ],
                ],
                'auditor' => [
                    'type' => 'doc',
                    'content' => [
                        [
                            'type' => 'paragraph',
                            'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                            'content' => [['text' => 'Hello!', 'type' => 'text', 'marks' => [['type' => 'bold']]]],
                        ],
                        [
                            'type' => 'paragraph',
                            'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                            'content' => [
                                ['text' => 'The service request ', 'type' => 'text'],
                                ['type' => 'mergeTag', 'attrs' => ['id' => 'service request number']],
                                ['text' => ' has a new update.', 'type' => 'text'],
                            ],
                        ],
                        [
                            'type' => 'tiptapBlock',
                            'attrs' => [
                                'data' => ['alignment' => 'center'],
                                'type' => 'serviceRequestTypeEmailTemplateButtonBlock',
                            ],
                        ],
                        [
                            'type' => 'paragraph',
                            'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                        ],
                    ],
                ],
                'customer' => [
                    'type' => 'doc',
                    'content' => [
                        [
                            'type' => 'paragraph',
                            'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                            'content' => [
                                ['text' => 'Hello ', 'type' => 'text', 'marks' => [['type' => 'bold']]],
                                ['type' => 'mergeTag', 'attrs' => ['id' => 'assigned to'], 'marks' => [['type' => 'bold']]],
                                ['text' => ',', 'type' => 'text', 'marks' => [['type' => 'bold']]],
                            ],
                        ],
                        [
                            'type' => 'paragraph',
                            'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                            'content' => [
                                ['text' => "There's been a new update to your service request ", 'type' => 'text'],
                                ['type' => 'mergeTag', 'attrs' => ['id' => 'service request number']],
                                ['text' => '. Please check the latest details.', 'type' => 'text'],
                            ],
                        ],
                        [
                            'type' => 'tiptapBlock',
                            'attrs' => [
                                'data' => ['alignment' => 'center'],
                                'type' => 'serviceRequestTypeEmailTemplateButtonBlock',
                            ],
                        ],
                    ],
                ],
            ],
            'status_change' => [
                'manager' => [
                    'type' => 'doc',
                    'content' => [
                        [
                            'type' => 'paragraph',
                            'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                            'content' => [['text' => 'Hello!', 'type' => 'text', 'marks' => [['type' => 'bold']]]],
                        ],
                        [
                            'type' => 'paragraph',
                            'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                            'content' => [
                                ['text' => 'The service request ', 'type' => 'text'],
                                ['type' => 'mergeTag', 'attrs' => ['id' => 'service request number']],
                                ['text' => ' status has changed to ', 'type' => 'text'],
                                ['type' => 'mergeTag', 'attrs' => ['id' => 'status']],
                                ['text' => '. ', 'type' => 'text'],
                            ],
                        ],
                        [
                            'type' => 'tiptapBlock',
                            'attrs' => [
                                'data' => ['alignment' => 'center'],
                                'type' => 'serviceRequestTypeEmailTemplateButtonBlock',
                            ],
                        ],
                    ],
                ],
                'auditor' => [
                    'type' => 'doc',
                    'content' => [
                        [
                            'type' => 'paragraph',
                            'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                            'content' => [['text' => 'Hello!', 'type' => 'text', 'marks' => [['type' => 'bold']]]],
                        ],
                        [
                            'type' => 'paragraph',
                            'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                            'content' => [
                                ['text' => 'The service request ', 'type' => 'text'],
                                ['type' => 'mergeTag', 'attrs' => ['id' => 'service request number']],
                                ['text' => ' status has changed to ', 'type' => 'text'],
                                ['type' => 'mergeTag', 'attrs' => ['id' => 'status']],
                                ['text' => '. ', 'type' => 'text'],
                            ],
                        ],
                        [
                            'type' => 'tiptapBlock',
                            'attrs' => [
                                'data' => ['alignment' => 'center'],
                                'type' => 'serviceRequestTypeEmailTemplateButtonBlock',
                            ],
                        ],
                    ],
                ],
                'customer' => [
                    'type' => 'doc',
                    'content' => [
                        [
                            'type' => 'paragraph',
                            'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                            'content' => [
                                ['text' => 'Hello ', 'type' => 'text', 'marks' => [['type' => 'bold']]],
                                ['type' => 'mergeTag', 'attrs' => ['id' => 'assigned to'], 'marks' => [['type' => 'bold']]],
                                ['text' => ',', 'type' => 'text', 'marks' => [['type' => 'bold']]],
                            ],
                        ],
                        [
                            'type' => 'paragraph',
                            'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                            'content' => [
                                ['text' => 'The status of your service request ', 'type' => 'text'],
                                ['type' => 'mergeTag', 'attrs' => ['id' => 'service request number']],
                                ['text' => ' has been updated to: ', 'type' => 'text'],
                                ['type' => 'mergeTag', 'attrs' => ['id' => 'status']],
                                ['text' => '. ', 'type' => 'text'],
                            ],
                        ],
                        [
                            'type' => 'paragraph',
                            'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                            'content' => [['text' => 'You can view your request for more details.', 'type' => 'text']],
                        ],
                        [
                            'type' => 'tiptapBlock',
                            'attrs' => [
                                'data' => ['alignment' => 'center'],
                                'type' => 'serviceRequestTypeEmailTemplateButtonBlock',
                            ],
                        ],
                    ],
                ],
            ],
            'closed' => [
                'manager' => [
                    'type' => 'doc',
                    'content' => [
                        [
                            'type' => 'paragraph',
                            'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                            'content' => [['text' => 'Hello!', 'type' => 'text', 'marks' => [['type' => 'bold']]]],
                        ],
                        [
                            'type' => 'paragraph',
                            'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                            'content' => [
                                ['text' => 'The service request ', 'type' => 'text'],
                                ['type' => 'mergeTag', 'attrs' => ['id' => 'service request number']],
                                ['text' => ' has been closed.', 'type' => 'text'],
                            ],
                        ],
                        [
                            'type' => 'tiptapBlock',
                            'attrs' => [
                                'data' => ['alignment' => 'center'],
                                'type' => 'serviceRequestTypeEmailTemplateButtonBlock',
                            ],
                        ],
                    ],
                ],
                'auditor' => [
                    'type' => 'doc',
                    'content' => [
                        [
                            'type' => 'paragraph',
                            'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                            'content' => [['text' => 'Hello!', 'type' => 'text', 'marks' => [['type' => 'bold']]]],
                        ],
                        [
                            'type' => 'paragraph',
                            'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                            'content' => [
                                ['text' => 'The service request ', 'type' => 'text'],
                                ['type' => 'mergeTag', 'attrs' => ['id' => 'service request number']],
                                ['text' => ' has been closed.', 'type' => 'text'],
                            ],
                        ],
                        [
                            'type' => 'tiptapBlock',
                            'attrs' => [
                                'data' => ['alignment' => 'center'],
                                'type' => 'serviceRequestTypeEmailTemplateButtonBlock',
                            ],
                        ],
                    ],
                ],
                'customer' => [
                    'type' => 'doc',
                    'content' => [
                        [
                            'type' => 'paragraph',
                            'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                            'content' => [
                                ['text' => 'Dear ', 'type' => 'text', 'marks' => [['type' => 'bold']]],
                                ['type' => 'mergeTag', 'attrs' => ['id' => 'assigned to'], 'marks' => [['type' => 'bold']]],
                                ['text' => ',', 'type' => 'text', 'marks' => [['type' => 'bold']]],
                            ],
                        ],
                        [
                            'type' => 'paragraph',
                            'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                            'content' => [
                                ['text' => 'We wanted to update you that the issue you reported in Ticket #', 'type' => 'text'],
                                ['type' => 'mergeTag', 'attrs' => ['id' => 'service request number']],
                                ['text' => ' regarding ', 'type' => 'text'],
                                ['type' => 'mergeTag', 'attrs' => ['id' => 'description']],
                                ['text' => ' has been ', 'type' => 'text'],
                                ['type' => 'mergeTag', 'attrs' => ['id' => 'status']],
                                ['text' => '. ', 'type' => 'text'],
                            ],
                        ],
                        [
                            'type' => 'paragraph',
                            'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                            'content' => [[
                                'text' => 'If you experience any further issues or have additional questions, please do not hesitate to open a new ticket.',
                                'type' => 'text',
                            ]],
                        ],
                        [
                            'type' => 'paragraph',
                            'attrs' => ['class' => null, 'style' => null, 'textAlign' => 'start'],
                            'content' => [[
                                'text' => 'Thank you for giving us a chance to help you with your issue.',
                                'type' => 'text',
                            ]],
                        ],
                    ],
                ],
            ],
        ];

        return $bodies[$template][$role] ?? [];
    }
};
