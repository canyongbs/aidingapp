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
    $record = $getRecord();
    $submittedFields = $record->serviceRequestFormSubmission?->fields ?? collect();
@endphp

<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    <div class="space-y-4">
        @foreach ($record->secrets as $secret)
            @php
                $field = $submittedFields->first(fn ($field): bool => $field->pivot->response === $secret->getKey());
            @endphp

            <div
                x-data="{
                    value: null,
                    isLoading: false,
                    async reveal() {
                        this.isLoading = true
                        this.value = await $wire.revealServiceRequestSecret(
                            '{{ $secret->getKey() }}',
                        )
                        this.isLoading = false
                    },
                }"
                class="flex flex-wrap items-center gap-3"
            >
                <span class="font-medium text-sm text-gray-950 dark:text-white">
                    {{ $field?->label ?? 'Password' }}
                </span>

                <span x-show="value === null" class="text-gray-500">••••••••</span>
                <code
                    x-show="value !== null"
                    x-text="value"
                    class="rounded bg-gray-100 px-2 py-1 text-sm dark:bg-white/10"
                ></code>

                <x-filament::button
                    type="button"
                    size="sm"
                    x-show="value === null"
                    x-bind:disabled="isLoading"
                    x-on:click="reveal()"
                >
                    Reveal
                </x-filament::button>
            </div>
        @endforeach
    </div>
</x-dynamic-component>
