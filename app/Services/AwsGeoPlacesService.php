<?php

namespace App\Services;

use Aws\GeoPlaces\GeoPlacesClient;
use Exception;
use Throwable;

class AwsGeoPlacesService
{
    public function __construct(
        protected GeoPlacesClient $client,
    ) {}

    /**
     * @return array<int, string>
     */
    public function autocomplete(string $query): array
    {
        try {
            $result = $this->client->autocomplete([
                'QueryText' => $query,
                'MaxResults' => 10,
            ]);
        } catch (Throwable $exception) {
            report(new Exception('AWS GeoPlaces autocomplete failed', previous: $exception));

            return [];
        }

        /** @var array<int, array{Address?: array{Label?: string}, Title: string}> $items */
        $items = $result['ResultItems'] ?? [];

        return collect($items)
            ->map(fn (array $item): string => $item['Address']['Label'] ?? $item['Title'])
            ->values()
            ->all();
    }
}