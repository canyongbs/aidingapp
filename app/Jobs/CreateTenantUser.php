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

namespace App\Jobs;

use AidingApp\Authorization\Enums\LicenseType;
use AidingApp\Authorization\Models\Role;
use App\Models\Authenticatable;
use App\Models\Tenant;
use App\Models\User;
use App\Multitenancy\DataTransferObjects\TenantUser;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\SkipIfBatchCancelled;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Spatie\Multitenancy\Jobs\NotTenantAware;

class CreateTenantUser implements ShouldQueue, NotTenantAware
{
    use Batchable;
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $timeout = 1200;

    public function __construct(
        public Tenant $tenant,
        public TenantUser $data,
    ) {}

    /**
      * @return array<int, SkipIfBatchCancelled>
      */
    public function middleware(): array
    {
        return [new SkipIfBatchCancelled()];
    }

    public function handle(): void
    {
        $this->tenant->execute(function () {
            $user = User::create($this->data->toArray());

            foreach (Arr::wrap(LicenseType::cases()) as $licenseType) {
                /** @var LicenseType $licenseType */
                if ($licenseType->hasAvailableLicenses()) {
                    $user->licenses()->create(['type' => $licenseType]);
                }
            }

            $user->roles()->sync(Role::where('name', Authenticatable::SUPER_ADMIN_ROLE)->firstOrFail());
        });
    }
}
