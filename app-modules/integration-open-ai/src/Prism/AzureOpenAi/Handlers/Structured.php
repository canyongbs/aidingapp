<?php

namespace AidingApp\IntegrationOpenAi\Prism\AzureOpenAi\Handlers;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Prism\Prism\Providers\OpenAI\Handlers\Structured as BaseStructured;
use Prism\Prism\Providers\OpenAI\Maps\MessageMap;
use Prism\Prism\Structured\Request;

class Structured extends BaseStructured
{
    /**
     * @param  array{type: 'json_schema', name: string, schema: array<mixed>, strict?: bool}|array{type: 'json_object'}  $responseFormat
     */
    protected function sendRequest(Request $request, array $responseFormat): Response
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
                'previous_response_id' => $request->providerOptions('previous_response_id'),
                'truncation' => $request->providerOptions('truncation'),
                'text' => [
                    'format' => $responseFormat,
                ],
                'tools' => $request->providerOptions('tools'),
                'tool_choice' => $request->providerOptions('tool_choice'),
            ]))
        );
    }
}
