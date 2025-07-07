<?php

namespace Tests;

use App\Models\Tenant;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Testing\RefreshDatabaseState;
use Illuminate\Support\Facades\Event;
use Spatie\Multitenancy\Events\MadeTenantCurrentEvent;

abstract class TenantTestCase extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Event::listen(MadeTenantCurrentEvent::class, function () {
            $this->beginDatabaseTransactionOnConnection($this->tenantDatabaseConnectionName());
        });

        Tenant::first()->makeCurrent();
    }

    protected function refreshTestDatabase()
    {
        if (! RefreshDatabaseState::$migrated) {
            $this->createLandlordTestingEnvironment();

            $this->app[Kernel::class]->setArtisan(null);

            RefreshDatabaseState::$migrated = true;
        }

        $beginLandlordTransaction = function () {
            $this->beginDatabaseTransactionOnConnection($this->landlordDatabaseConnectionName());
        };

        if (! in_array($beginLandlordTransaction, $this->afterApplicationCreatedCallbacks)) {
            $this->afterApplicationCreated($beginLandlordTransaction);
        }
    }
}
