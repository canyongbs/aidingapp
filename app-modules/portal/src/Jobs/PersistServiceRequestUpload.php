<?php

namespace AidingApp\Portal\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\Middleware\SkipIfBatchCancelled;
use AidingApp\ServiceManagement\Models\ServiceRequest;

class PersistServiceRequestUpload implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use Batchable;

    public $deleteWhenMissingModels = true;

    public function __construct(
        protected ServiceRequest $serviceRequest,
        protected string $path,
        protected string $originalFileName,
        protected string $collection,
    ) {}

    public function middleware(): array
    {
        return [
            new SkipIfBatchCancelled(),
        ];
    }

    public function handle(): void
    {
        $this->serviceRequest
            ->addMediaFromDisk($this->path)
            ->withCustomProperties([
                'originalFileName' => $this->originalFileName,
            ])
            ->toMediaCollection($this->collection);
    }
}
