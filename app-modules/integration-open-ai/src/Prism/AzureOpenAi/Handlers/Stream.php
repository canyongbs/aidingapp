<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

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

namespace AidingApp\IntegrationOpenAi\Prism\AzureOpenAi\Handlers;

use AidingApp\IntegrationOpenAi\Prism\AzureOpenAi\Maps\MessageMap;
use Generator;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Prism\Prism\Enums\ChunkType;
use Prism\Prism\Providers\OpenAI\Handlers\Stream as BaseStream;
use Prism\Prism\Text\Chunk;
use Prism\Prism\Text\Request;
use Prism\Prism\ValueObjects\Messages\AssistantMessage;
use Prism\Prism\ValueObjects\Messages\ToolResultMessage;
use ReflectionClass;

class Stream extends BaseStream
{

    protected function sendRequest(Request $request): Response
    {
        $requestBody = array_merge([
            'stream' => true,
            'model' => $request->model(),
            'input' => (new MessageMap(
                $request->messages(),
                $request->systemPrompts()
            ))(),
            'max_output_tokens' => $request->maxTokens(),
        ], Arr::whereNotNull([
            'temperature' => $request->temperature(),
            'top_p' => $request->topP(),
            'metadata' => $request->providerOptions('metadata'),
            'instructions' => $request->providerOptions('instructions'),
            'previous_response_id' => $request->providerOptions('previous_response_id'),
            'truncation' => $request->providerOptions('truncation'),
            'reasoning' => $request->providerOptions('reasoning'),
            'tools' => [
                ...$request->providerOptions('tools') ?? [],
                ...$this->buildTools($request),
            ],
            'tool_choice' => $request->providerOptions('tool_choice'),
        ]));

        try {
            return $this
                ->client
                ->withOptions(['stream' => true])
                ->post('responses', $requestBody);
        } catch (RequestException $exception) {
            Log::error('[AzureOpenAI Stream] Request failed', [
                'status' => $exception->response->status(),
                'body' => $exception->response->body(),
                'json' => $exception->response->json(),
            ]);

            throw $exception;
        }
    }

    /**
     * Override handleToolCalls to only send newly added messages during tool execution loops.
     *
     * During tool loops, we only want to send the function_call and function_call_output,
     * not the assistant's text content (which was already sent in the previous iteration).
     * This avoids sending duplicate messages on each iteration.
     *
     * Note: previous_response_id is NOT used during tool loops since the response hasn't
     * been saved to the database yet. It's only available between user messages.
     */
    protected function handleToolCalls(
        Request $request,
        string $text,
        array $toolCalls,
        int $depth
    ): Generator {
        $toolCalls = $this->mapToolCalls($toolCalls);

        yield new Chunk(
            text: '',
            toolCalls: $toolCalls,
            chunkType: ChunkType::ToolCall,
        );

        $toolResults = $this->callTools($request->tools(), $toolCalls);

        yield new Chunk(
            text: '',
            toolResults: $toolResults,
            chunkType: ChunkType::ToolResult,
        );

        // Track how many messages exist before we add new ones
        $messageCountBefore = count($request->messages());

        $request->addMessage(new AssistantMessage($text, $toolCalls));
        $request->addMessage(new ToolResultMessage($toolResults));

        $depth++;

        if ($depth < $request->maxSteps()) {
            // Only send the 2 messages we just added, but WITHOUT the assistant's text content
            // The text was already sent in the previous request, we only need the function_call and function_call_output
            $allMessages = $request->messages();
            $newMessages = array_slice($allMessages, $messageCountBefore);

            // Strip text content from AssistantMessage to avoid duplicate assistant messages
            // We only want to send the function_call, not the text
            $newMessages = array_map(function ($message) {
                if ($message instanceof AssistantMessage) {
                    // Create a new AssistantMessage with empty text but same tool calls
                    return new AssistantMessage('', $message->toolCalls);
                }

                return $message;
            }, $newMessages);

            // Temporarily swap messages array to only include new messages
            $reflection = new ReflectionClass($request);
            $messagesProperty = $reflection->getProperty('messages');
            $messagesProperty->setAccessible(true);
            $messagesProperty->setValue($request, $newMessages);

            $nextResponse = $this->sendRequest($request);

            // Restore full message history
            $messagesProperty->setValue($request, $allMessages);

            yield from $this->processStream($nextResponse, $request, $depth);
        }
    }
}
