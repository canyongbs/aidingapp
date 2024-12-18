<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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

use Database\Migrations\Concerns\CanModifyPermissions;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    use CanModifyPermissions;

    private array $permissions = [
        'assistant_chat.view-any' => 'Assistant Chat',
        'assistant_chat.create' => 'Assistant Chat',
        'assistant_chat.*.view' => 'Assistant Chat',
        'assistant_chat.*.update' => 'Assistant Chat',
        'assistant_chat.*.delete' => 'Assistant Chat',
        'assistant_chat.*.restore' => 'Assistant Chat',
        'assistant_chat.*.force-delete' => 'Assistant Chat',
        'assistant_chat_message.view-any' => 'Assistant Chat Message',
        'assistant_chat_message.create' => 'Assistant Chat Message',
        'assistant_chat_message.*.view' => 'Assistant Chat Message',
        'assistant_chat_message.*.update' => 'Assistant Chat Message',
        'assistant_chat_message.*.delete' => 'Assistant Chat Message',
        'assistant_chat_message.*.restore' => 'Assistant Chat Message',
        'assistant_chat_message.*.force-delete' => 'Assistant Chat Message',
        'assistant_chat_message_log.view-any' => 'Assistant Chat Message Log',
        'assistant_chat_message_log.*.view' => 'Assistant Chat Message Log',
        'assistant_chat_folder.view-any' => 'Assistant Chat Folder',
        'assistant_chat_folder.create' => 'Assistant Chat Folder',
        'assistant_chat_folder.*.view' => 'Assistant Chat Folder',
        'assistant_chat_folder.*.update' => 'Assistant Chat Folder',
        'assistant_chat_folder.*.delete' => 'Assistant Chat Folder',
        'assistant_chat_folder.*.restore' => 'Assistant Chat Folder',
        'assistant_chat_folder.*.force-delete' => 'Assistant Chat Folder',
        'prompt_type.view-any' => 'Prompt Type',
        'prompt_type.create' => 'Prompt Type',
        'prompt_type.*.view' => 'Prompt Type',
        'prompt_type.*.update' => 'Prompt Type',
        'prompt_type.*.delete' => 'Prompt Type',
        'prompt_type.*.restore' => 'Prompt Type',
        'prompt_type.*.force-delete' => 'Prompt Type',
        'prompt.view-any' => 'Prompt',
        'prompt.create' => 'Prompt',
        'prompt.*.view' => 'Prompt',
        'prompt.*.update' => 'Prompt',
        'prompt.*.delete' => 'Prompt',
        'prompt.*.restore' => 'Prompt',
        'prompt.*.force-delete' => 'Prompt',
        'assistant.access' => 'Assistant',
        'assistant.access_ai_settings' => 'Assistant',
        'report.access_assistant' => 'Report',
        'report.access_assistant_settings' => 'Report',
    ];

    private array $guards = [
        'web',
        'api',
    ];

    public function up(): void
    {
        $assistantChatGroup = DB::table('permission_groups')->where('name', 'Assistant Chat')->first();

        if ($assistantChatGroup) {
            DB::table('permissions')->where('group_id', $assistantChatGroup->id)->delete();
            DB::table('permission_groups')->where('id', $assistantChatGroup->id)->delete();
        }

        $assistantChatMessageGroup = DB::table('permission_groups')->where('name', 'Assistant Chat Message')->first();

        if ($assistantChatMessageGroup) {
            DB::table('permissions')->where('group_id', $assistantChatMessageGroup->id)->delete();
            DB::table('permission_groups')->where('id', $assistantChatMessageGroup->id)->delete();
        }

        $assistantChatMessageLogGroup = DB::table('permission_groups')->where('name', 'Assistant Chat Message Log')->first();

        if ($assistantChatMessageLogGroup) {
            DB::table('permissions')->where('group_id', $assistantChatMessageLogGroup->id)->delete();
            DB::table('permission_groups')->where('id', $assistantChatMessageLogGroup->id)->delete();
        }

        $assistantChatFolderGroup = DB::table('permission_groups')->where('name', 'Assistant Chat Folder')->first();

        if ($assistantChatFolderGroup) {
            DB::table('permissions')->where('group_id', $assistantChatFolderGroup->id)->delete();
            DB::table('permission_groups')->where('id', $assistantChatFolderGroup->id)->delete();
        }

        $promptTypeGroup = DB::table('permission_groups')->where('name', 'Prompt Type')->first();

        if ($promptTypeGroup) {
            DB::table('permissions')->where('group_id', $promptTypeGroup->id)->delete();
            DB::table('permission_groups')->where('id', $promptTypeGroup->id)->delete();
        }

        $promptGroup = DB::table('permission_groups')->where('name', 'Prompt')->first();

        if ($promptGroup) {
            DB::table('permissions')->where('group_id', $promptGroup->id)->delete();
            DB::table('permission_groups')->where('id', $promptGroup->id)->delete();
        }

        $assistantGroup = DB::table('permission_groups')->where('name', 'Assistant')->first();

        if ($assistantGroup) {
            DB::table('permissions')->where('group_id', $assistantGroup->id)->delete();
            DB::table('permission_groups')->where('id', $assistantGroup->id)->delete();
        }

        $reportGroup = DB::table('permission_groups')->where('name', 'Report')->first();

        if ($reportGroup) {
            DB::table('permissions')->where('group_id', $reportGroup->id)->delete();
            DB::table('permission_groups')->where('id', $reportGroup->id)->delete();
        }
    }

    public function down(): void
    {
        collect($this->guards)
            ->each(function (string $guard) {
                $permissions = Arr::except($this->permissions, keys: DB::table('permissions')
                    ->where('guard_name', $guard)
                    ->pluck('name')
                    ->all());

                $this->createPermissions($permissions, $guard);
            });
    }
};
