<?php

namespace AidingApp\IntegrationOpenAi\Prism;

use AidingApp\IntegrationOpenAi\Prism\AzureOpenAi\Handlers\Stream;
use AidingApp\IntegrationOpenAi\Prism\AzureOpenAi\Handlers\Structured;
use AidingApp\IntegrationOpenAi\Prism\AzureOpenAi\Handlers\Text;
use Generator;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Arr;
use Override;
use Prism\Prism\Providers\OpenAI\OpenAI;
use Prism\Prism\Structured\Request as StructuredRequest;
use Prism\Prism\Structured\Response as StructuredResponse;
use Prism\Prism\Text\Request as TextRequest;
use Prism\Prism\Text\Response as TextResponse;

class AzureOpenAi extends OpenAI
{
    public function __construct() {}

    #[Override]
    public function stream(TextRequest $request): Generator
    {
        $handler = new Stream($this->client(
            $request->clientOptions(),
            $request->clientRetry()
        ));

        return $handler->handle($request);
    }

    #[Override]
    public function structured(StructuredRequest $request): StructuredResponse
    {
        $handler = new Structured($this->client(
            $request->clientOptions(),
            $request->clientRetry()
        ));

        return $handler->handle($request);
    }

    #[Override]
    public function text(TextRequest $request): TextResponse
    {
        $handler = new Text($this->client(
            $request->clientOptions(),
            $request->clientRetry()
        ));

        return $handler->handle($request);
    }

    /**
     * @param  array<string, mixed>  $options
     * @param  array<mixed>  $retry
     */
    protected function client(array $options = [], array $retry = [], ?string $baseUrl = null): PendingRequest
    {
        return $this->baseClient()
            ->withHeaders([
                'api-key' => $options['apiKey'],
                ...$options['headers'] ?? [],
            ])
            ->withQueryParameters(['api-version' => $options['apiVersion']])
            ->withOptions(Arr::except($options, ['apiKey', 'apiVersion', 'deployment', 'headers']))
            ->when($retry !== [], fn ($client) => $client->retry(...$retry))
            ->baseUrl("{$options['deployment']}/v1")
            ->timeout(500);
    }
}
