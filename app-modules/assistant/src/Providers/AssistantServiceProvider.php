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

namespace AidingApp\Assistant\Providers;

use Filament\Panel;
use Filament\Support\Assets\Js;
use App\Concerns\ImplementsGraphQL;
use Illuminate\Support\Facades\Event;
use AidingApp\Assistant\Models\Prompt;
use Illuminate\Support\ServiceProvider;
use AidingApp\Assistant\AssistantPlugin;
use AidingApp\Assistant\Models\PromptType;
use Filament\Support\Facades\FilamentAsset;
use AidingApp\Assistant\Models\AssistantChat;
use App\Registries\RoleBasedAccessControlRegistry;
use AidingApp\Assistant\Models\AssistantChatFolder;
use AidingApp\Assistant\Models\AssistantChatMessage;
use Illuminate\Database\Eloquent\Relations\Relation;
use AidingApp\IntegrationAI\Events\AIPromptInitiated;
use AidingApp\Assistant\Models\AssistantChatMessageLog;
use AidingApp\Assistant\Registries\AssistantRbacRegistry;
use AidingApp\Assistant\Listeners\LogAssistantChatMessage;
use AidingApp\Assistant\Services\AIInterface\Enums\AIChatMessageFrom;

class AssistantServiceProvider extends ServiceProvider
{
    use ImplementsGraphQL;

    public function register(): void
    {
        Panel::configureUsing(fn (Panel $panel) => ($panel->getId() !== 'admin') || $panel->plugin(new AssistantPlugin()));
    }

    public function boot(): void
    {
        Relation::morphMap([
            'assistant_chat' => AssistantChat::class,
            'assistant_chat_message' => AssistantChatMessage::class,
            'assistant_chat_message_log' => AssistantChatMessageLog::class,
            'assistant_chat_folder' => AssistantChatFolder::class,
            'prompt_type' => PromptType::class,
            'prompt' => Prompt::class,
        ]);

        $this->registerEvents();
        $this->registerAssets();

        RoleBasedAccessControlRegistry::register(AssistantRbacRegistry::class);

        $this->discoverSchema(__DIR__ . '/../../graphql/*');
        $this->registerEnum(AIChatMessageFrom::class);
    }

    public function registerAssets(): void
    {
        FilamentAsset::register([
            Js::make('assistantCurrentResponse', __DIR__ . '/../../resources/js/dist/assistantCurrentResponse.js')->loadedOnRequest(),
        ], 'canyon-gbs/assistant');
    }

    protected function registerEvents(): void
    {
        Event::listen(AIPromptInitiated::class, LogAssistantChatMessage::class);
    }
}
