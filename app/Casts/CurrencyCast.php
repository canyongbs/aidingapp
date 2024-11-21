<?php

namespace App\Casts;

use Cknow\Money\Money;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class CurrencyCast implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $payload, array $attributes): mixed
    {
        $payload = json_decode($payload);

        if (blank($payload)) {
            return null;
        }

        $value = $payload->value ?? null;
        $currency = $payload->currency ?? null;

        if (blank($value) || blank($currency)) {
            return null;
        }

        return Money::parse($value, $currency);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $payload, array $attributes): mixed
    {
        if (blank($payload) || ! ($payload instanceof Money)) {
            return null;
        }

        return json_encode([
            'value' => $payload->getAmount(),
            'currency' => $payload->getCurrency()->getCode(),
        ]);
    }
}
