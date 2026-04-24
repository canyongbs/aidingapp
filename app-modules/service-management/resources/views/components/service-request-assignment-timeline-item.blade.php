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
    use AidingApp\ServiceManagement\Models\ServiceRequestHistory;
    use App\Filament\Resources\Users\UserResource;

    $serviceRequest = $this->recordModel ?? $record->serviceRequest;
    $serviceRequest->loadMissing(['assignments.user', 'histories']);

    $allAssignments = $serviceRequest->assignments;
    $initialAssignment = $allAssignments->sortBy('assigned_at')->first();
    $isInitial = $initialAssignment && $record->id === $initialAssignment->id;

    $previousAssignment = $allAssignments
        ->reject(fn ($assignment) => $assignment->id === $record->id)
        ->filter(fn ($assignment) => $assignment->assigned_at <= $record->assigned_at)
        ->sortByDesc('assigned_at')
        ->first();

    // If an Unassigned event happened between the previous assignment and this one,
    // the SR was in an unassigned state right before — this is a fresh Assign, not a Reassign.
    $cameFromUnassigned = $previousAssignment
        ? $serviceRequest->histories->contains(fn ($history) =>
            $history->event_type === ServiceRequestHistory::EVENT_UNASSIGNED
            && $history->created_at >= $previousAssignment->assigned_at
            && $history->created_at <= $record->assigned_at
        )
        : false;

    $isAssign = $isInitial || ! $previousAssignment?->user || $cameFromUnassigned;

    $createdAt = $record->assigned_at ?? $record->created_at;
@endphp

<div>
    <div class="flex flex-row justify-between">
        <h3 class="mb-1 flex items-center text-lg font-semibold text-gray-500 dark:text-gray-100">
            <span class="ml-2 flex space-x-2">
                @if ($isAssign)
                    Service Request Assigned
                @else
                    Service Request Reassigned
                @endif
            </span>
        </h3>

        <div>
            {{ $viewRecordIcon }}
        </div>
    </div>

    @include('service-management::components.timeline-time', ['datetime' => $createdAt])

    <div
        class="my-4 rounded-lg border-2 border-gray-200 p-2 text-base font-normal text-gray-500 dark:border-gray-800 dark:text-gray-400"
    >
        @if ($isAssign)
            Assigned to
            <a class="primary font-semibold underline" href="{{ UserResource::getUrl('view', ['record' => $record->user]) }}">
                {{ $record->user->name }}
            </a>
        @else
            Assigned changed from
            <a class="primary font-semibold underline" href="{{ UserResource::getUrl('view', ['record' => $previousAssignment->user]) }}">
                {{ $previousAssignment->user->name }}
            </a>
            to
            <a class="primary font-semibold underline" href="{{ UserResource::getUrl('view', ['record' => $record->user]) }}">
                {{ $record->user->name }}
            </a>
        @endif
    </div>
</div>
