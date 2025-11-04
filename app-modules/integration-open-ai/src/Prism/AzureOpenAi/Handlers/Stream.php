<?php

namespace AidingApp\IntegrationOpenAi\Prism\AzureOpenAi\Handlers;

use AidingApp\IntegrationOpenAi\Prism\AzureOpenAi\Maps\MessageMap;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Prism\Prism\Providers\OpenAI\Handlers\Stream as BaseStream;
use Prism\Prism\Providers\OpenAI\Maps\ToolChoiceMap;
use Prism\Prism\Text\Request;

class Stream extends BaseStream
{
    protected function sendRequest(Request $request): Response
    {
        return $this
            ->client
            ->withOptions(['stream' => true])
            ->post(
                'responses',
                array_merge([
                    'stream' => true,
                    'model' => $request->model(),
                    'input' => (new MessageMap($request->messages(), $request->systemPrompts()))(),
                    'max_output_tokens' => $request->maxTokens(),
                ], Arr::whereNotNull([
                    'temperature' => $request->temperature(),
                    'top_p' => $request->topP(),
                    'metadata' => $request->providerOptions('metadata'),
                    // 'tools' => $this->buildTools($request),
                    // 'tool_choice' => ToolChoiceMap::map($request->toolChoice()),
                    'instructions' => $request->providerOptions('instructions'),
                    'previous_response_id' => $request->providerOptions('previous_response_id'),
                    'truncation' => $request->providerOptions('truncation'),
                    'reasoning' => $request->providerOptions('reasoning'),
                    'tools' => $request->providerOptions('tools'),
                    'tool_choice' => $request->providerOptions('tool_choice'),
                ]))
            );
    }
}
