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

namespace AidingApp\Report\Client;

use Closure;
use Throwable;
use Illuminate\Support\Arr;
use AidingApp\Task\Models\Task;
use AidingApp\Alert\Models\Alert;
use Illuminate\Support\Facades\DB;
use AidingApp\Contact\Models\Contact;
use Illuminate\Support\Facades\Schema;
use AidingApp\Contact\Models\ContactSource;
use AidingApp\Contact\Models\ContactStatus;
use AidingApp\IntegrationAI\Client\AzureOpenAI;
use AidingApp\Notification\Models\Subscription;
use Illuminate\Auth\Access\AuthorizationException;
use AidingApp\Report\Settings\ReportAssistantSettings;
use AidingApp\Assistant\Services\AIInterface\Enums\AIChatMessageFrom;
use AidingApp\Assistant\Services\AIInterface\DataTransferObjects\Chat;

class AIReportChatClient extends AzureOpenAI
{
    public function ask(Chat $chat, ?Closure $callback, int $attempt = 1): string
    {
        if (is_null($this->systemContext)) {
            $this->setSystemContext();
        }

        if ($chat->messages->last()->message && ($attempt === 1)) {
            $this->dispatchPromptInitiatedEvent($chat);
        }

        try {
            $response = $this->client->chat()->create([
                'messages' => $this->formatMessagesFromChat($chat),
                'functions' => [
                    [
                        'name' => 'sql',
                        'description' => 'Get the results of a SQL query',
                        'parameters' => [
                            'type' => 'object',
                            'properties' => [
                                'query' => [
                                    'type' => 'string',
                                    'description' => 'The SQL query to execute.',
                                ],
                            ],
                            'required' => ['query'],
                        ],
                    ],
                ],
            ]);
        } catch (Throwable $exception) {
            if ($attempt >= 3) {
                return "An error occurred while processing this request: {$exception->getMessage()}";
            }

            return $this->ask($chat, $callback, $attempt + 1);
        }

        $response = Arr::first($response->choices);

        if (
            ($response->finishReason === 'function_call') &&
            ($response->message->functionCall->name === 'sql')
        ) {
            $query = json_decode($response->message->functionCall->arguments, associative: true)['query'] ?? null;

            if (blank($query)) {
                return 'An error occurred while processing this request.';
            }

            try {
                $this->canQuery($query, $this->getAuthorizedTables()) || throw new AuthorizationException();

                $results = DB::select($query);
            } catch (AuthorizationException $exception) {
                if ($attempt >= 2) {
                    return 'So sorry, I do not have the data I need to answer that question.';
                }

                return $this->ask($chat, $callback, $attempt + 1);
            } catch (Throwable $exception) {
                if ($attempt >= 3) {
                    return 'You do not appear to have access to the information required to process this request.';
                }

                return $this->ask($chat, $callback, $attempt + 1);
            }

            $chat->messages[] = [
                'from' => AIChatMessageFrom::from($response->message->role),
                'functionCall' => [
                    'name' => $response->message->functionCall->name,
                    'arguments' => $response->message->functionCall->arguments,
                ],
            ];

            $chat->messages[] = [
                'from' => AIChatMessageFrom::Function,
                'name' => 'sql',
                'message' => json_encode($results, JSON_PRETTY_PRINT),
            ];

            return $this->ask($chat, $callback);
        }

        return $response->message->content;
    }

    protected function getAuthorizedTables(): array
    {
        return collect([
            Alert::class,
            Contact::class, ContactSource::class, ContactStatus::class,
            Subscription::class,
            Task::class,
        ])
            ->filter(fn (string $model) => auth()->user()->can('viewAny', $model))
            ->map(fn (string $model): string => app($model)->getTable())
            ->all();
    }

    protected function setSystemContext(): void
    {
        $schema = collect(Schema::getTables())
            ->keyBy('name')
            ->only($this->getAuthorizedTables())
            ->map(fn (array $table): string => "{$table['name']}: " . PHP_EOL . collect(Schema::getColumns($table['name']))
                ->map(fn (array $column): string => "{$table['name']}.{$column['name']} ({$column['type_name']}" . ($column['nullable'] ? ', nullable' : '') . ')')
                ->join(PHP_EOL))
            ->implode(PHP_EOL . PHP_EOL);

        $this->systemContext = (string) str(
            app(ReportAssistantSettings::class)
                ->prompt_system_context,
        )->replace('{{ schema }}', $schema);
    }

    protected function canQuery(string $query, array $authorizedTables): bool
    {
        return collect($this->getConsumedTablesFromQuery($query))
            ->diff($authorizedTables)
            ->isEmpty();
    }

    protected function getConsumedTablesFromQuery(string $query): array
    {
        $tables = [];

        $explain = DB::select("EXPLAIN {$query}");

        foreach ($explain as $explainRow) {
            preg_match_all(
                '/(?<=Scan\son\s)(\w+)\s*?|Scan\susing\s\w+\son\s\K(\w+)\s*?/',
                $explainRow->{'QUERY PLAN'},
                $tableMatches,
                PREG_SET_ORDER,
            );

            if (! empty($tableMatches)) {
                $tables[] = $tableMatches[0][0];
            }
        }

        return collect($tables)->unique()->values()->all();
    }
}
