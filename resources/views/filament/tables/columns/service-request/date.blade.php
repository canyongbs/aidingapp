{{--
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
--}}
@php
    use Carbon\Carbon;
    use Illuminate\Support\Str;

    /** @var \AidingApp\ServiceManagement\Models\ServiceRequest $record */
    $record = $getRecord();

    $format = function ($date): string {
        if (! $date) {
            return 'N/A';
        }

        $date = $date instanceof Carbon ? $date : Carbon::parse($date);
        $days = (int) abs(Carbon::today()->diffInDays($date, false));

        return $date->format('m-d-Y') . ' (' . $days . ' ' . Str::plural('day', $days) . ')';
    };
@endphp

<div class="fi-ta-text grid w-full gap-y-1 text-sm text-gray-950 dark:text-white">
    <div>
        <span class="font-medium">Created:</span>
        <span>{{ $format($record->created_at) }}</span>
    </div>
    <div>
        <span class="font-medium">Updated:</span>
        <span>{{ $record->updated_at ? $format($record->updated_at) : 'N/A' }}</span>
    </div>
</div>
