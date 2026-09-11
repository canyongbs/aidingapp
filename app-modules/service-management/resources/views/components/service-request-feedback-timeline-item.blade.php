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
    $contact = $record->contact;
@endphp

<div>
    <div class="flex flex-row justify-between">
        <h3 class="fi-ta-text mb-1 flex items-center text-base font-semibold text-gray-950 dark:text-white">
            <span class="font-medium">
                Feedback Submitted
            </span>
        </h3>

        <div>
            {{ $viewRecordIcon }}
        </div>
    </div>

    @include('service-management::components.timeline-time', ['datetime' => $record->created_at])

    <div
        class="fi-ta-text my-4 rounded-lg border-2 border-gray-200 p-2 text-sm font-normal text-gray-950 dark:border-gray-800 dark:text-white"
    >
        @if ($contact)
            <div>
                Submitted by
                <span class="font-semibold">{{ $contact->full_name }}</span>
            </div>
        @endif

        @if (! is_null($record->csat_answer))
            <div>
                CSAT:
                <span class="font-semibold">{{ $record->csat_answer }}</span>
            </div>
        @endif

        @if (! is_null($record->nps_answer))
            <div>
                NPS:
                <span class="font-semibold">{{ $record->nps_answer }}</span>
            </div>
        @endif
    </div>
</div>
