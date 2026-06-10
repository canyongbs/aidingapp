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

namespace AidingApp\ServiceManagement\Settings;

use Spatie\LaravelSettings\Settings;

class ServiceRequestNotificationAutomationSettings extends Settings
{
    public bool $is_enabled = false;

    /**
     * This property is type `array<string, mixed>`, but the settings package throws an error when attempting to parse this.
     *
     * @phpstan-ignore missingType.iterableValue
     */
    public array $ai_prompt = [];

    public static function group(): string
    {
        return 'service-request-notification-automation';
    }

    /**
     * @return array<string, mixed>
     */
    public static function defaultAiPrompt(): array
    {
        return [
            'type' => 'doc',
            'content' => [
                ['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'You are writing an email template for a service request notification system.']]],
                ['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'Context:']]],
                ['type' => 'bulletList', 'content' => [
                    ['type' => 'listItem', 'content' => [['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'Service request type: '], ['type' => 'mergeTag', 'attrs' => ['id' => 'type name']]]]]],
                    ['type' => 'listItem', 'content' => [['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'Type description: '], ['type' => 'mergeTag', 'attrs' => ['id' => 'type description']]]]]],
                    ['type' => 'listItem', 'content' => [['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'Notification event: '], ['type' => 'mergeTag', 'attrs' => ['id' => 'event name']]]]]],
                    ['type' => 'listItem', 'content' => [['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'Recipient group: '], ['type' => 'mergeTag', 'attrs' => ['id' => 'role name']]]]]],
                ]],
                ['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'Example template to customize:']]],
                ['type' => 'bulletList', 'content' => [
                    ['type' => 'listItem', 'content' => [['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'Subject: '], ['type' => 'mergeTag', 'attrs' => ['id' => 'example subject']]]]]],
                    ['type' => 'listItem', 'content' => [['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'Body: '], ['type' => 'mergeTag', 'attrs' => ['id' => 'example body']]]]]],
                ]],
                ['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'Additional instructions from the administrator:']]],
                ['type' => 'bulletList', 'content' => [
                    ['type' => 'listItem', 'content' => [['type' => 'paragraph', 'content' => [['type' => 'mergeTag', 'attrs' => ['id' => 'user instructions']]]]]],
                ]],
                ['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'Rules:']]],
                ['type' => 'bulletList', 'content' => [
                    ['type' => 'listItem', 'content' => [['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'Rewrite the example subject and body to be specific and relevant to this service request type']]]]],
                    ['type' => 'listItem', 'content' => [['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'Maintain a professional tone unless the administrator instructions specify otherwise']]]]],
                    ['type' => 'listItem', 'content' => [['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'You may use the following merge tags in your output — copy the HTML exactly as shown:']]]]],
                ]],
                ['type' => 'bulletList', 'content' => [
                    ['type' => 'listItem', 'content' => [['type' => 'paragraph', 'content' => [['type' => 'mergeTag', 'attrs' => ['id' => 'available merge tags']]]]]],
                ]],
                ['type' => 'bulletList', 'content' => [
                    ['type' => 'listItem', 'content' => [['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'You may use the following custom blocks in your output — copy the HTML exactly as shown:']]]]],
                ]],
                ['type' => 'bulletList', 'content' => [
                    ['type' => 'listItem', 'content' => [['type' => 'paragraph', 'content' => [['type' => 'mergeTag', 'attrs' => ['id' => 'available custom blocks']]]]]],
                ]],
                ['type' => 'bulletList', 'content' => [
                    ['type' => 'listItem', 'content' => [['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'Do not invent merge tags or custom blocks that are not listed above']]]]],
                    ['type' => 'listItem', 'content' => [['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'Keep the subject concise (under 100 characters of visible text)']]]]],
                    ['type' => 'listItem', 'content' => [['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'The body should be informative but not overly long']]]]],
                ]],
                ['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'Output format:']]],
                ['type' => 'bulletList', 'content' => [
                    ['type' => 'listItem', 'content' => [['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'Respond with only a JSON object containing "subject" and "body" keys']]]]],
                    ['type' => 'listItem', 'content' => [['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'Both values must be valid HTML']]]]],
                    ['type' => 'listItem', 'content' => [['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'Example: {"subject": "<p>Your subject here</p>", "body": "<p>Your body here</p>"}']]]]],
                ]],
            ],
        ];
    }
}
