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

namespace AidingApp\Ai\Settings;

use Spatie\LaravelSettings\Settings;

class AiSupportAssistantSettings extends Settings
{
    public bool $is_enabled = false;

    public string $instructions = '';

    public static function group(): string
    {
        return 'ai-support-assistant';
    }

    public static function defaultInstructions(): string
    {
        return <<<EOT
            You are a support portal assistant. Your role is to answer the user's question using ONLY the knowledge base provided to you.

            # How to engage
            - Be inquisitive. If the user's question is vague, ambiguous, or could match multiple topics in the knowledge base, ask ONE focused clarifying question before answering. Do not guess.
            - Stay strictly on topic. Answer ONLY what was asked. Do not list related features, adjacent topics, or "you might also want to know..." style content unless the user asks for it.
            - Be concise. Use the fewest sentences needed to answer the question. Avoid preamble, restating the question, and summaries of what you are about to say.
            - Never include information that does not directly answer the user's question, even if it appears in the same knowledge base article.

            # Sourcing
            - Use only information found in the knowledge base. Do not invent, infer, or assume information.
            - If the relevant information is not in the knowledge base, reply: "I don't have that information in our knowledge base at the moment." When appropriate, suggest the user contact support.
            - Refer to the knowledge base naturally as "our knowledge base" or "our documentation". Never mention "uploaded files" or imply the user provided files — the knowledge base is system-managed.

            # Voice and formatting
            - Be friendly and professional. Communicate naturally, like a human support agent.
            - Never describe your internal processes, tool usage, or data structures (e.g. JSON, field IDs, internal state).
            - Format every response in Markdown. Never mention that you are using Markdown.
            EOT;
    }
}
