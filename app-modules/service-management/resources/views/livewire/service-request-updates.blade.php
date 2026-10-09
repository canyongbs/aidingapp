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
    use AidingApp\Contact\Filament\Resources\ContactResource;
    use AidingApp\Contact\Models\Contact;
    use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestUpdates\ServiceRequestUpdateResource;
    use AidingApp\ServiceManagement\Models\ServiceRequest;
    use App\Filament\Resources\Users\UserResource;
    use App\Models\SystemUser;
    use App\Models\User;
    use Illuminate\Support\Carbon;
@endphp

<div wire:poll.15s>
    <x-filament::section :contained="false">
        <x-slot name="heading">Service Request Updates</x-slot>

        <x-slot name="afterHeader">
            <x-filament::badge color="gray">
                {{ trans_choice(':count update|:count updates', $updatesCount) }}
            </x-filament::badge>
        </x-slot>

        @if ($hasEarlierUpdates)
            <div class="mb-6 flex justify-center">
                <x-filament::button wire:click="loadEarlierUpdates" color="gray" size="sm" icon="heroicon-m-arrow-up">
                    Show earlier updates
                </x-filament::button>
            </div>
        @endif

        @forelse ($updatesByDate as $date => $updates)
            <div @class(['mt-8' => ! $loop->first])>
                <div class="flex items-center gap-x-3">
                    <div class="h-px flex-1 bg-gray-200 dark:bg-white/10"></div>
                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400">
                        {{ Carbon::parse($date, $timezone)->format('F j, Y · T') }}
                    </span>
                    <div class="h-px flex-1 bg-gray-200 dark:bg-white/10"></div>
                </div>

                <ul class="mt-6 flex flex-col gap-y-6">
                    @foreach ($updates as $update)
                        @php
                            $author = $update->createdBy;

                            [$authorName, $authorRole, $authorUrl] = match (true) {
                                $author instanceof User => [$author->name, 'Service Provider', UserResource::getUrl('view', ['record' => $author])],
                                $author instanceof Contact => [$author->full_name, 'Customer', ContactResource::getUrl('view', ['record' => $author])],
                                $author instanceof SystemUser => [$author->name, 'System', null],
                                $author instanceof ServiceRequest => ['AI Assistant', 'AI', null],
                                filled($update->created_by_id) => ['Deleted user', null, null],
                                default => ['Unknown', null, null],
                            };

                            $isAi = $author instanceof ServiceRequest;
                            $isCustomer = $author instanceof Contact;
                            $createdAt = $update->created_at->copy()->setTimezone($timezone);
                            $uploads = $update->getMedia('uploads');
                        @endphp

                        <li wire:key="service-request-update-{{ $update->getKey() }}" class="flex gap-x-3">
                            @if ($isAi)
                                <x-filament::avatar
                                    :src="$aiAvatarUrl"
                                    alt="AI Assistant"
                                    size="size-9"
                                    class="shrink-0"
                                />
                            @elseif ($author)
                                <x-filament-panels::avatar.user :user="$author" size="size-9" class="shrink-0" />
                            @else
                                <div
                                    class="flex size-9 shrink-0 items-center justify-center rounded-full bg-gray-100 text-gray-400 dark:bg-white/10 dark:text-gray-500"
                                >
                                    <x-filament::icon icon="heroicon-m-user" class="size-5" />
                                </div>
                            @endif

                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-x-2 gap-y-1">
                                    @if ($authorUrl)
                                        <a
                                            href="{{ $authorUrl }}"
                                            class="text-sm font-semibold text-gray-950 hover:underline dark:text-white"
                                        >
                                            {{ $authorName }}
                                        </a>
                                    @else
                                        <span class="text-sm font-semibold text-gray-950 dark:text-white">
                                            {{ $authorName }}
                                        </span>
                                    @endif

                                    @if ($authorRole)
                                        <x-filament::badge size="sm" :color="$isAi ? 'primary' : 'gray'">
                                            {{ $authorRole }}
                                        </x-filament::badge>
                                    @endif

                                    @if ($update->internal)
                                        <x-filament::badge size="sm" color="warning" icon="heroicon-m-lock-closed">
                                            Internal · Staff only
                                        </x-filament::badge>
                                    @endif

                                    @if ($update->update_type)
                                        <x-filament::badge size="sm" color="info">
                                            {{ $update->update_type->getLabel() }}
                                        </x-filament::badge>
                                    @endif

                                    <div class="ms-auto flex items-center gap-x-1">
                                        <time
                                            datetime="{{ $createdAt->toIso8601String() }}"
                                            title="{{ $createdAt->format('M j, Y g:i a (T)') }}"
                                            class="text-xs text-gray-500 dark:text-gray-400"
                                        >
                                            {{ $createdAt->format('g:i a') }}
                                        </time>

                                        @can('view', $update)
                                            <x-filament::icon-button
                                                tag="a"
                                                :href="ServiceRequestUpdateResource::getUrl('view', ['record' => $update, 'service_request' => $update->service_request_id])"
                                                icon="heroicon-m-eye"
                                                color="gray"
                                                size="sm"
                                                label="View update"
                                                tooltip="View"
                                            />
                                        @endcan

                                        {{ ($this->deleteUpdateAction)(['update' => $update->getKey()]) }}
                                    </div>
                                </div>

                                <div
                                    @class([
                                        'mt-2 rounded-xl px-4 py-3 text-sm text-gray-950 ring-1 dark:text-white',
                                        'bg-warning-50 ring-warning-600/20 dark:bg-warning-400/10 dark:ring-warning-400/20' => $update->internal,
                                        'bg-primary-50 ring-primary-600/10 dark:bg-primary-400/10 dark:ring-primary-400/20' => ! $update->internal && $isAi,
                                        'bg-white ring-gray-950/10 dark:bg-gray-900 dark:ring-white/10' => ! $update->internal && $isCustomer,
                                        'bg-gray-50 ring-gray-950/5 dark:bg-white/5 dark:ring-white/10' => ! $update->internal && ! $isAi && ! $isCustomer,
                                    ])
                                >
                                    <p class="break-words whitespace-pre-line">{{ $update->update }}</p>

                                    @if ($uploads->isNotEmpty())
                                        <div class="mt-3 border-t border-gray-950/5 pt-3 dark:border-white/10">
                                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">
                                                Attachments
                                            </p>

                                            <ul class="mt-2 flex flex-col gap-y-1.5">
                                                @foreach ($uploads as $media)
                                                    <li class="flex flex-wrap items-center gap-x-2">
                                                        <x-filament::link
                                                            :href="route('service-request.media.download', ['media' => $media])"
                                                            icon="heroicon-m-paper-clip"
                                                            size="sm"
                                                            target="_blank"
                                                        >
                                                            {{ $media->name }}.{{ $media->extension }}
                                                        </x-filament::link>

                                                        <span class="text-xs text-gray-500 dark:text-gray-400">
                                                            {{ str($media->extension)->upper() }} ·
                                                            {{ $media->human_readable_size }}
                                                        </span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        @empty
            <x-filament::empty-state
                heading="No updates yet"
                description="Updates shared with the customer and internal notes will appear here."
                icon="heroicon-o-chat-bubble-left-right"
                :contained="false"
            />
        @endforelse

        @if ($canCreateUpdates || $isClosed)
            <x-slot name="footer">
                <div class="mt-4 border-t border-gray-200 pt-6 dark:border-white/10">
                    @if ($canCreateUpdates)
                        <form wire:submit="create" class="flex flex-col gap-y-4">
                            {{ $this->form }}

                            <div class="flex">
                                <x-filament::button
                                    type="submit"
                                    :color="($data['internal'] ?? false) ? 'warning' : 'primary'"
                                    icon="heroicon-m-paper-airplane"
                                >
                                    {{ $data['internal'] ?? false ? 'Add internal note' : 'Send update' }}
                                </x-filament::button>
                            </div>
                        </form>
                    @else
                        <x-filament::callout
                            icon="heroicon-o-lock-closed"
                            heading="This service request is closed"
                            description="Updates cannot be added to a closed service request."
                        />
                    @endif
                </div>
            </x-slot>
        @endif
    </x-filament::section>

    <x-filament-actions::modals />
</div>
