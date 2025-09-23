<?php

namespace AidingApp\Authorization\Database\Factories;

use AidingApp\Authorization\Models\LoginMagicLink;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<LoginMagicLink>
 */
class LoginMagicLinkFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => Hash::make(Str::random()),
            'user_id' => User::factory(),
        ];
    }

    /**
     * @return Factory<LoginMagicLink>
     */
    public function withCode(string $code): Factory
    {
        return $this->state(function (array $attributes) use ($code) {
            return [
                'code' => Hash::make($code),
            ];
        });
    }

    /**
     * @return Factory<LoginMagicLink>
     */
    public function used(?Carbon $when = null): Factory
    {
        return $this->state(function (array $attributes) use ($when) {
            return [
                'used_at' => $when ?? now(),
            ];
        });
    }
}
