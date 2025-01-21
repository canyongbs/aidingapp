<?php

namespace App\Support;

use Closure;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Throwable;

class BulkProcessingMachine
{
    /** @var array<Model> */
    protected array $records;

    /** @var array<Closure> */
    protected array $checks;

    protected int $successCount;

    protected int $exceptionsCount;

    /** @var array<string> */
    protected array $failureMessages = [];

    public static function make(array $records): static
    {
        $static = app(static::class);
        $static->records($records);

        return $static;
    }

    public function check(Closure $callback): static
    {
        $this->checks[] = $callback;

        return $this;
    }

    public function records(array $records): static
    {
        $this->records = $records;

        return $this;
    }

    public function process(Closure $callback): static
    {
        $this->successCount = 0;
        $this->exceptionsCount = 0;

        $checkFailures = [];

        foreach ($this->records as $record) {
            foreach ($this->checks as $checkIndex => $check) {
                $failureMessageCallback = $check($record);

                if (! ($failureMessageCallback instanceof Closure)) {
                    continue;
                }

                $checkFailures[$checkIndex] ??= ['failureMessageCallback' => $failureMessageCallback, 'count' => 0];
                $checkFailures[$checkIndex]['count']++;

                continue 2;
            }

            try {
                $callback($record);

                $this->successCount++;
            } catch (Throwable $exception) {
                $this->exceptionsCount++;

                report($exception);
            }
        }

        $this->failureMessages = array_map(
            fn (array $failure): string => ($failure['failureMessageCallback'] instanceof Closure)
                ? $failure['failureMessageCallback']($failure['count'])
                : $failure['failureMessageCallback'],
            $checkFailures,
        );

        return $this;
    }

    public function sendNotification(Closure $callback): void
    {
        $failureMessages = $this->failureMessages;

        if ($this->exceptionsCount) {
            $failureMessages[] = ($this->exceptionsCount === 1)
                ? 'There was an unknown failure.'
                : "There were {$this->exceptionsCount} unknown failures.";
        }

        $notification = $callback($this->successCount, $failureMessages);

        if (blank($notification)) {
            return;
        }

        if (is_string($notification)) {
            $notification = Notification::make()->title($notification);

            if ($failureMessages) {
                $notification->body(implode(' ', $failureMessages));
            }
        }

        if (! $this->successCount) {
            $notification->danger();
        } elseif ($failureMessages) {
            $notification->warning();
        } else {
            $notification->success();
        }

        $notification->send();
    }
}
