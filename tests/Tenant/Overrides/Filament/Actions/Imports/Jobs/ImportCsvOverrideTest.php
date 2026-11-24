<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Aiding App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Aiding App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    <https://www.canyongbs.com> or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

use App\Filament\Imports\UserImporter;
use App\Models\Import;
use App\Models\User;
use App\Overrides\Filament\Actions\Imports\Jobs\ImportCsvOverride;
use Filament\Actions\Imports\Jobs\ImportCsv;
use Illuminate\Container\Container;
use Illuminate\Contracts\Queue\Job as JobContract;
use Illuminate\Queue\Jobs\Job;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SyncQueue;
use Illuminate\Queue\WorkerOptions;
use Illuminate\Support\Facades\Cache;

it('releases a chunk waiting on another chunk of the same import instead of failing it after repeated attempts', function (): void {
    $import = Import::query()->create([
        'user_id' => User::factory()->create()->getKey(),
        'file_name' => 'users.csv',
        'file_path' => 'imports/users.csv',
        'importer' => UserImporter::class,
        'total_rows' => 1,
    ]);

    $importJob = app(ImportCsv::class, [
        'import' => $import,
        'rows' => [],
        'columnMap' => [],
    ]);

    expect($importJob)->toBeInstanceOf(ImportCsvOverride::class);

    $locks = collect($importJob->middleware())
        ->map(function (object $middleware) use ($importJob) {
            assert($middleware instanceof WithoutOverlapping);

            $lock = Cache::lock($middleware->getLockKey($importJob), 600);
            $lock->get();

            return $lock;
        });

    $queue = new class () extends SyncQueue {
        public function payloadFor(ImportCsv $job): string
        {
            return $this->createPayload($job, 'default');
        }
    };
    $queue->setContainer(app());
    $queue->setConnectionName('sync');

    $payload = $queue->payloadFor($importJob);

    $queueJob = new class (app(), $payload) extends Job implements JobContract {
        public function __construct(Container $container, protected string $payload)
        {
            $this->container = $container;
            $this->connectionName = 'sync';
            $this->queue = 'default';
        }

        public function attempts(): int
        {
            return 3;
        }

        public function getJobId(): string
        {
            return 'import-chunk';
        }

        public function getRawBody(): string
        {
            return $this->payload;
        }
    };

    try {
        app('queue.worker')->process('sync', $queueJob, new WorkerOptions(maxTries: 0));
    } finally {
        $locks->each->release();
    }

    expect($queueJob->hasFailed())->toBeFalse()
        ->and($queueJob->isReleased())->toBeTrue();
});
