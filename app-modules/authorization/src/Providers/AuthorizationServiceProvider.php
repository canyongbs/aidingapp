<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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

namespace AidingApp\Authorization\Providers;

use Filament\Panel;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use AidingApp\Authorization\Models\Role;
use AidingApp\Authorization\Models\License;
use AidingApp\Authorization\Models\Permission;
use AidingApp\Authorization\AuthorizationPlugin;
use SocialiteProviders\Azure\AzureExtendSocialite;
use SocialiteProviders\Manager\SocialiteWasCalled;
use Illuminate\Database\Eloquent\Relations\Relation;
use SocialiteProviders\Google\GoogleExtendSocialite;
use AidingApp\Authorization\AuthorizationRoleRegistry;
use AidingApp\Authorization\Observers\LicenseObserver;
use AidingApp\Authorization\Registries\AuthorizationRbacRegistry;
use AidingApp\Authorization\Http\Controllers\Auth\LogoutController;
use Filament\Http\Controllers\Auth\LogoutController as FilamentLogoutController;

class AuthorizationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        Panel::configureUsing(fn (Panel $panel) => ($panel->getId() !== 'admin') || $panel->plugin(new AuthorizationPlugin()));

        $this->app->scoped(AuthorizationRoleRegistry::class, function ($app) {
            return new AuthorizationRoleRegistry();
        });

        $this->app->bind(FilamentLogoutController::class, function ($app) {
            return new LogoutController();
        });

        app('config')->set('permission', require base_path('app-modules/authorization/config/permission.php'));
    }

    public function boot(): void
    {
        Relation::morphMap([
            'role' => Role::class,
            'permission' => Permission::class,
            'license' => License::class,
        ]);

        $this->registerObservers();

        Event::listen(
            events: SocialiteWasCalled::class,
            listener: AzureExtendSocialite::class . '@handle'
        );

        Event::listen(
            events: SocialiteWasCalled::class,
            listener: GoogleExtendSocialite::class . '@handle'
        );

        AuthorizationRoleRegistry::register(AuthorizationRbacRegistry::class);
    }

    public function registerObservers(): void
    {
        License::observe(LicenseObserver::class);
    }
}
