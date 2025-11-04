<?php

namespace AidingApp\IntegrationOpenAi\Prism\AzureOpenAi\Handlers;

use Illuminate\Http\Client\Response as ClientResponse;
use Illuminate\Support\Arr;
use Prism\Prism\Providers\OpenAI\Handlers\Text as BaseText;
use Prism\Prism\Providers\OpenAI\Maps\MessageMap;
use Prism\Prism\Providers\OpenAI\Maps\ToolCallMap;
use Prism\Prism\Providers\OpenAI\Maps\ToolChoiceMap;
use Prism\Prism\Text\Request;
use Prism\Prism\Text\Step;
use Prism\Prism\ValueObjects\Meta;
use Prism\Prism\ValueObjects\ToolResult;
use Prism\Prism\ValueObjects\Usage;

class Text extends BaseText
{
    /**
     * @param  array<string, mixed>  $data
     * @param  ToolResult[]  $toolResults
     */
    protected function addStep(array $data, Request $request, ClientResponse $clientResponse, array $toolResults = []): void
    {
        $this->responseBuilder->addStep(new Step(
            text: data_get($data, 'output.{last}.content.0.text') ?? '',
            finishReason: $this->mapFinishReason($data),
            toolCalls: ToolCallMap::map(array_filter(data_get($data, 'output', []), fn (array $output): bool => $output['type'] === 'function_call')),
            toolResults: $toolResults,
            usage: new Usage(
                promptTokens: data_get($data, 'usage.input_tokens', 0) - data_get($data, 'usage.input_tokens_details.cached_tokens', 0),
                completionTokens: data_get($data, 'usage.output_tokens'),
                cacheReadInputTokens: data_get($data, 'usage.input_tokens_details.cached_tokens'),
                thoughtTokens: data_get($data, 'usage.output_token_details.reasoning_tokens'),
            ),
            meta: new Meta(
                id: data_get($data, 'id'),
                model: data_get($data, 'model'),
            ),
            messages: $request->messages(),
            additionalContent: [
                'generated_images' => array_filter($data['output'] ?? [], fn (array $output): bool => $output['type'] === 'image_generation_call'),
            ],
            systemPrompts: $request->systemPrompts(),
        ));
    }

    protected function sendRequest(Request $request): ClientResponse
    {
        return $this->client->post(
            'responses',
            array_merge([
                'model' => $request->model(),
                'input' => (new MessageMap($request->messages(), $request->systemPrompts()))(),
                'max_output_tokens' => $request->maxTokens(),
            ], Arr::whereNotNull([
                'temperature' => $request->temperature(),
                'top_p' => $request->topP(),
                'metadata' => $request->providerOptions('metadata'),
                // 'tools' => $this->buildTools($request),
                // 'tool_choice' => ToolChoiceMap::map($request->toolChoice()),
                'previous_response_id' => $request->providerOptions('previous_response_id'),
                'truncation' => $request->providerOptions('truncation'),
                'tools' => $request->providerOptions('tools'),
                'tool_choice' => $request->providerOptions('tool_choice'),
            ]))
        );
    }
}
