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

namespace App\Providers;

use App\Models\SystemUser;
use App\Models\Tenant;
use App\Overrides\Filament\Actions\Imports\Jobs\ImportCsvOverride;
use App\Overrides\Filament\Actions\Imports\Jobs\PrepareCsvExportOverride;
use App\Overrides\Laravel\PermissionMigrationCreator;
use App\Overrides\LastDragon_ru\LaraASP\GraphQL\SearchBy\Definitions\SearchByDirective as GraphQLSearchByDirectiveOverride;
use App\Overrides\LastDragon_ru\LaraASP\GraphQL\SearchBy\Types\Condition as GraphQLSearchByTypesConditionOverride;
use Filament\Actions\Exports\Jobs\PrepareCsvExport;
use Filament\Actions\Imports\Jobs\ImportCsv;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Laravel\Pennant\Feature;
use LastDragon_ru\LaraASP\GraphQL\SearchBy\Definitions\SearchByDirective as GraphQLSearchByDirectiveAlias;
use LastDragon_ru\LaraASP\GraphQL\SearchBy\Types\Condition\Condition as GraphQLSearchByTypesCondition;

use function Sentry\configureScope;

use Sentry\State\Scope;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton('originalAppKey', fn () => config('app.key'));

        $this->app->bind(GraphQLSearchByTypesCondition::class, GraphQLSearchByTypesConditionOverride::class);
        $this->app->bind(GraphQLSearchByDirectiveAlias::class, GraphQLSearchByDirectiveOverride::class);
        $this->app->bind(ImportCsv::class, ImportCsvOverride::class);
        $this->app->bind(PrepareCsvExport::class, PrepareCsvExportOverride::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Relation::morphMap([
            'system_user' => SystemUser::class,
            'tenant' => Tenant::class,
        ]);

        Feature::resolveScopeUsing(fn ($driver) => null);

        if (config('app.force_https')) {
            URL::forceScheme('https');
            $this->app['request']->server->set('HTTPS', true);
        }

        Queue::looping(function () {
            configureScope(function (Scope $scope): void {
                $scope->removeUser();
            });
        });

        $this->app->singleton(PermissionMigrationCreator::class, function ($app) {
            return new PermissionMigrationCreator($app['files'], $app->basePath('stubs'));
        });

        $this->app->singleton('current-commit', function ($app) {
            $commitProcess = Process::run('git log --pretty="%h" -n1 HEAD');

            if ($commitProcess->successful()) {
                return rtrim($commitProcess->output());
            }

            report($commitProcess->errorOutput());

            return null;
        });

        $this->app->singleton('current-version', function ($app) {
            $gitVersion = Process::run('git describe --tags $(git rev-list --tags --max-count=1)');

            if ($gitVersion->successful()) {
                return rtrim($gitVersion->output());
            }
            report($gitVersion->errorOutput());

            return null;
        });

        Feature::discover();
    }
}
