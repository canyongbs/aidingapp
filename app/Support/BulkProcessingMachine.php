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
