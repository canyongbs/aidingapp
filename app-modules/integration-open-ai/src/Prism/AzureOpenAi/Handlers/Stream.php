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

namespace AidingApp\IntegrationOpenAi\Prism\AzureOpenAi\Handlers;

use AidingApp\IntegrationOpenAi\Prism\AzureOpenAi\Maps\MessageMap;
use Generator;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Prism\Prism\Exceptions\PrismException;
use Prism\Prism\Exceptions\PrismRateLimitedException;
use Prism\Prism\Providers\OpenAI\Handlers\Stream as BaseStream;
use Prism\Prism\Streaming\EventID;
use Prism\Prism\Streaming\Events\StepFinishEvent;
use Prism\Prism\Streaming\Events\StepStartEvent;
use Prism\Prism\Streaming\Events\StreamEvent;
use Prism\Prism\Streaming\Events\StreamStartEvent;
use Prism\Prism\Streaming\Events\TextCompleteEvent;
use Prism\Prism\Streaming\Events\TextDeltaEvent;
use Prism\Prism\Streaming\Events\TextStartEvent;
use Prism\Prism\Streaming\Events\ThinkingCompleteEvent;
use Prism\Prism\Streaming\Events\ThinkingEvent;
use Prism\Prism\Streaming\Events\ThinkingStartEvent;
use Prism\Prism\Streaming\Events\ToolCallDeltaEvent;
use Prism\Prism\Streaming\Events\ToolCallEvent;
use Prism\Prism\Text\Request;
use Prism\Prism\ValueObjects\Messages\AssistantMessage;
use Prism\Prism\ValueObjects\Messages\ToolResultMessage;
use Prism\Prism\ValueObjects\ToolCall;
use Prism\Prism\ValueObjects\Usage;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;
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
     * @return Generator<StreamEvent>
     */
    protected function processStream(Response $response, Request $request, int $depth = 0): Generator
    {
        $this->state->reset()->withMessageId(EventID::generate());
        $reasoningItems = [];
        $newResponseId = null;

        assert($response instanceof MessageInterface);

        while (! $response->getBody()->eof()) {
            $data = $this->parseNextDataLine($response->getBody());

            if ($data === null) {
                continue;
            }

            if ($data['type'] === 'error') {
                $code = data_get($data, 'error.code', 'unknown_error');
                $message = data_get($data, 'error.message', 'No error message provided');

                if ($code === 'rate_limit_exceeded') {
                    throw new PrismRateLimitedException([]);
                }

                throw new PrismException(sprintf(
                    'Sending to model %s failed. Code: %s. Message: %s',
                    $request->model(),
                    $code,
                    $message
                ));
            }

            if ($data['type'] === 'response.created' && $this->state->shouldEmitStreamStart()) {
                $newResponseId = $data['response']['id'] ?? $newResponseId;

                yield new StreamStartEvent(
                    id: EventID::generate(),
                    timestamp: time(),
                    model: $data['response']['model'] ?? 'unknown',
                    provider: 'azure_open_ai',
                );

                $this->state->markStreamStarted();

                continue;
            }

            if ($this->state->shouldEmitStepStart()) {
                $this->state->markStepStarted();

                yield new StepStartEvent(
                    id: EventID::generate(),
                    timestamp: time()
                );
            }

            if ($this->hasReasoningSummaryDelta($data)) {
                $reasoningDelta = $this->extractReasoningSummaryDelta($data);

                if ($reasoningDelta !== '') {
                    if ($this->state->reasoningId() === '') {
                        $this->state->withReasoningId(EventID::generate());

                        yield new ThinkingStartEvent(
                            id: EventID::generate(),
                            timestamp: time(),
                            reasoningId: $this->state->reasoningId()
                        );
                    }

                    $this->state->appendThinking($reasoningDelta);

                    yield new ThinkingEvent(
                        id: EventID::generate(),
                        timestamp: time(),
                        delta: $reasoningDelta,
                        reasoningId: $this->state->reasoningId()
                    );
                }

                continue;
            }

            if ($this->hasReasoningItems($data)) {
                $reasoningItems = $this->extractReasoningItems($data, $reasoningItems);

                if ($this->state->reasoningId() !== '') {
                    yield new ThinkingCompleteEvent(
                        id: EventID::generate(),
                        timestamp: time(),
                        reasoningId: $this->state->reasoningId()
                    );
                    $this->state->withReasoningId('');
                }

                continue;
            }

            if ($this->hasToolCalls($data)) {
                $toolCallDeltaEvent = $this->extractToolCalls($data, $reasoningItems);

                if ($toolCallDeltaEvent instanceof ToolCallDeltaEvent) {
                    yield $toolCallDeltaEvent;
                }

                if ($this->isToolCallComplete($data)) {
                    $completedToolCall = $this->getCompletedToolCall($data);

                    if ($completedToolCall instanceof ToolCall) {
                        yield new ToolCallEvent(
                            id: EventID::generate(),
                            timestamp: time(),
                            toolCall: $completedToolCall,
                            messageId: $this->state->messageId()
                        );
                    }
                }

                continue;
            }

            $content = $this->extractOutputTextDelta($data);

            if ($content !== '') {
                if ($this->state->shouldEmitTextStart()) {
                    yield new TextStartEvent(
                        id: EventID::generate(),
                        timestamp: time(),
                        messageId: $this->state->messageId()
                    );
                    $this->state->markTextStarted();
                }

                $this->state->appendText($content);

                yield new TextDeltaEvent(
                    id: EventID::generate(),
                    timestamp: time(),
                    delta: $content,
                    messageId: $this->state->messageId()
                );
            }

            if (data_get($data, 'type') === 'response.output_text.done' && $this->state->hasTextStarted()) {
                $this->state->markTextCompleted();

                yield new TextCompleteEvent(
                    id: EventID::generate(),
                    timestamp: time(),
                    messageId: $this->state->messageId()
                );
            }

            if (data_get($data, 'type') === 'response.completed') {
                $this->state->withFinishReason($this->mapFinishReason($data));
                $this->state->addUsage(new Usage(
                    promptTokens: data_get($data, 'response.usage.input_tokens'),
                    completionTokens: data_get($data, 'response.usage.output_tokens'),
                    cacheReadInputTokens: data_get($data, 'response.usage.input_tokens_details.cached_tokens'),
                    thoughtTokens: data_get($data, 'response.usage.output_tokens_details.reasoning_tokens')
                ));
                $this->state->withMetadata(['response_id' => data_get($data, 'response.id')]);
            }
        }

        if ($this->state->hasToolCalls()) {
            yield from $this->handleToolCalls($request, $depth, $newResponseId);

            return;
        }

        $this->state->markStepFinished();

        yield new StepFinishEvent(
            id: EventID::generate(),
            timestamp: time()
        );

        yield $this->emitStreamEndEvent();
    }

    /**
     * Azure's Responses API supports continuing a run via `previous_response_id`, so unlike the
     * base OpenAI handler we avoid resending the full conversation history: only the assistant's
     * tool calls and the tool results are sent, with the assistant's text stripped since it was
     * already delivered in the previous request.
     *
     * @return Generator<StreamEvent>
     */
    protected function handleToolCalls(Request $request, int $depth, ?string $previousResponseId = null): Generator
    {
        $mappedToolCalls = $this->mapToolCalls($this->state->toolCalls());

        $toolResults = $this->callTools($request->tools(), $mappedToolCalls);

        $this->state->markStepFinished();

        yield new StepFinishEvent(
            id: EventID::generate(),
            timestamp: time()
        );

        $depth++;

        if ($depth >= $request->maxSteps()) {
            yield $this->emitStreamEndEvent();

            return;
        }

        // Track how many messages exist before we add the new ones.
        $messageCountBefore = count($request->messages());

        $request->addMessage(new AssistantMessage($this->state->currentText(), $mappedToolCalls));
        $request->addMessage(new ToolResultMessage($toolResults));

        // Only send the messages we just added, but WITHOUT the assistant's text content.
        // The text was already sent in the previous request, we only need the `function_call`
        // and `function_call_output`.
        $allMessages = $request->messages();
        $newMessages = array_slice($allMessages, $messageCountBefore);

        // Strip text content from `AssistantMessage` to avoid duplicate assistant messages.
        // We only want to send the `function_call`, not the text.
        // @phpstan-ignore argument.type
        $newMessages = array_map(function (AssistantMessage|ToolResultMessage $message) {
            if ($message instanceof AssistantMessage) {
                // Create a new `AssistantMessage` with empty text but same tool calls.
                return new AssistantMessage('', $message->toolCalls);
            }

            return $message;
        }, $newMessages);

        $reflection = new ReflectionClass($request);

        $messagesProperty = $reflection->getProperty('messages');
        $messagesProperty->setValue($request, $newMessages);

        $request->withProviderOptions([
            ...$request->providerOptions(),
            'previous_response_id' => $previousResponseId,
        ]);

        $nextResponse = $this->sendRequest($request);

        // Restore full message history.
        $messagesProperty->setValue($request, $allMessages);

        yield from $this->processStream($nextResponse, $request, $depth);
    }

    /**
     * @return array<string, mixed>|null
     */
    protected function parseNextDataLine(StreamInterface $stream): ?array
    {
        $data = parent::parseNextDataLine($stream);

        // Prism discards the provider's message when it throws for a rate limit, so we capture the retry delay here first.
        if (is_array($data) && data_get($data, 'error.code') === 'rate_limit_exceeded') {
            throw new PrismRateLimitedException([], $this->extractRetryAfterSeconds(data_get($data, 'error.message')));
        }

        return $data;
    }

    private function extractRetryAfterSeconds(mixed $message): ?int
    {
        if (! is_string($message) || blank($message)) {
            return null;
        }

        // Azure phrasing, e.g. "Please retry after 26 seconds."
        if (preg_match('/retry after (\d+)\s*second/i', $message, $matches)) {
            return max(1, (int) $matches[1]);
        }

        // OpenAI phrasing, e.g. "Please try again in 1.5s" or "2m30s" or "200ms".
        if (preg_match('/try again in\s+([0-9hms.\s]+)/i', $message, $matches)) {
            return $this->sumDurationToSeconds($matches[1]);
        }

        return null;
    }

    private function sumDurationToSeconds(string $duration): ?int
    {
        if (preg_match_all('/(\d+(?:\.\d+)?)\s*(ms|h|m|s)/i', $duration, $matches, PREG_SET_ORDER) === 0) {
            return null;
        }

        $seconds = 0.0;

        foreach ($matches as $match) {
            $seconds += match (mb_strtolower($match[2])) {
                'ms' => ((float) $match[1]) / 1000,
                'm' => ((float) $match[1]) * 60,
                'h' => ((float) $match[1]) * 3600,
                default => (float) $match[1],
            };
        }

        return max(1, (int) ceil($seconds));
    }
}
