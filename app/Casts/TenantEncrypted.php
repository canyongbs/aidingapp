<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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

namespace App\Casts;

use App\Models\Tenant;
use App\Multitenancy\Exceptions\TenantAppKeyIsNull;
use App\Multitenancy\Exceptions\UnableToResolveTenantForEncryptionKey;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Str;

class TenantEncrypted implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        $appKey = $model instanceof Tenant
            ? (new Encrypter($this->parseKey(app('originalAppKey')), config('app.cipher')))->decrypt($attributes['key'])
            : (
                Tenant::checkCurrent()
                    ? Tenant::current()->key
                    : throw new UnableToResolveTenantForEncryptionKey()
            );

        if (is_null($appKey)) {
            throw new TenantAppKeyIsNull();
        }

        $encrypter = new Encrypter($this->parseKey($appKey), config('app.cipher'));

        return $encrypter->decrypt($value);
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        $appKey = $model instanceof Tenant
            ? (new Encrypter($this->parseKey(app('originalAppKey')), config('app.cipher')))->decrypt($attributes['key'])
            : (
                Tenant::checkCurrent()
                ? Tenant::current()->key
                : throw new UnableToResolveTenantForEncryptionKey()
            );

        if (is_null($appKey)) {
            throw new TenantAppKeyIsNull();
        }

        $encrypter = new Encrypter($this->parseKey($appKey), config('app.cipher'));

        return $encrypter->encrypt($value);
    }

    protected function parseKey(string $configKey): false|string
    {
        if (Str::startsWith($key = $configKey, $prefix = 'base64:')) {
            $key = base64_decode(Str::after($key, $prefix));
        }

        return $key;
    }
}
