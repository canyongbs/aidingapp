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

namespace App\Multitenancy\Tasks;

use App\Models\Tenant;
use Exception;
use Spatie\Multitenancy\Contracts\IsTenant;
use Spatie\Multitenancy\Tasks\SwitchTenantTask;

class SwitchSessionDriver implements SwitchTenantTask
{
    public function __construct(
        protected ?string $originalSessionDriver = null,
        protected ?string $originalSessionConnection = null,
        protected ?string $originalSessionDomain = null,
    ) {
        $this->originalSessionDriver ??= config('session.driver');
        $this->originalSessionConnection ??= config('session.connection');
        $this->originalSessionDomain ??= config('session.domain');
    }

    public function makeCurrent(IsTenant $tenant): void
    {
        throw_if(
            ! $tenant instanceof Tenant,
            new Exception('Tenant is not an instance of Tenant')
        );

        // Not going to switch the session driver in testing, stick with the default array driver
        if (app()->runningUnitTests()) {
            return;
        }

        $this->setSessionConfig(
            driver: config('session.driver'),
            connection: config('session.connection'),
            domain: $tenant->domain,
        );
    }

    public function forgetCurrent(): void
    {
        if (app()->environment('testing')) {
            return;
        }

        $this->setSessionConfig(
            driver: $this->originalSessionDriver,
            connection: $this->originalSessionConnection,
            domain: $this->originalSessionDomain,
        );
    }

    protected function setSessionConfig(string $driver, string $connection, string $domain): void
    {
        config([
            'session.driver' => $driver,
            'session.connection' => $connection,
            'session.domain' => $domain,
            'sanctum.stateful' => [$domain],
        ]);

        app()->forgetInstance('session');
        app()->forgetInstance('session.store');
    }
}
