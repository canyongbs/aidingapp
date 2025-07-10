<?php

namespace App\Providers;

use App\Overrides\LaravelSqsExtended\SqsDiskConnector;
use Illuminate\Support\ServiceProvider;

class QueueServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $manager = $this->app->make('queue');
        $manager->addConnector('canyongbs-sqs-disk', fn () => new SqsDiskConnector());
    }
}
