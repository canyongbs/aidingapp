{{--
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
--}}
@php
    use AidingApp\InAppCommunication\Enums\ConversationType;
    use AidingApp\InAppCommunication\Filament\Pages\UserChat;

    $notifications = $this->getNotifications();
    $totalUnread = $this->getTotalUnreadCount();
@endphp

<div
    class="flex"
    wire:poll.10s
>
    <x-filament::icon-button
        label="Open chat notifications"
        icon="heroicon-o-chat-bubble-oval-left-ellipsis"
        color="gray"
        :badge="$totalUnread ?: null"
        x-on:click="$dispatch('open-modal', { id: 'chat-notifications' })"
    />

    <x-filament::modal
        id="chat-notifications"
        slide-over
    >
        <x-slot name="heading">
            Chat
        </x-slot>

        @if (count($notifications))
            <div
                class="chat-notifications -mx-6 divide-y divide-gray-200 border-y border-gray-200 dark:divide-white/10 dark:border-white/10">
                @foreach ($notifications as $notification)
                    <a
                        class="relative block px-6 py-4 before:absolute before:start-0 before:top-0 before:h-full before:w-0.5 before:bg-primary-600 hover:bg-gray-50 dark:before:bg-primary-500 dark:hover:bg-white/5"
                        href="{{ $notification['url'] }}"
                    >
                        <div class="flex items-start gap-3">
                            @if ($notification['type'] === ConversationType::Channel)
                                <div
                                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-primary-100 dark:bg-primary-900/30">
                                    <x-heroicon-o-hashtag class="h-5 w-5 text-primary-600 dark:text-primary-400" />
                                </div>
                            @else
                                <img
                                    class="h-10 w-10 shrink-0 rounded-full object-cover"
                                    src="{{ $notification['avatar_url'] }}"
                                    alt="{{ $notification['display_name'] }}"
                                />
                            @endif

                            <div class="min-w-0 flex-1">
                                <div class="flex items-center justify-between gap-2">
                                    <p class="truncate font-medium text-gray-950 dark:text-white">
                                        {{ $notification['display_name'] }}
                                    </p>
                                    @if ($notification['unread_count'] > 0)
                                        <span
                                            class="inline-flex items-center justify-center rounded-full bg-primary-600 px-2 py-0.5 text-xs font-medium text-white"
                                        >
                                            {{ $notification['unread_count'] > 99 ? '99+' : $notification['unread_count'] }}
                                        </span>
                                    @endif
                                </div>

                                @if ($notification['message_preview'])
                                    <p class="mt-1 truncate text-sm text-gray-500 dark:text-gray-400">
                                        @if ($notification['author_name'] && $notification['type'] === ConversationType::Channel)
                                            <span class="font-medium">{{ $notification['author_name'] }}:</span>
                                        @endif
                                        {{ $notification['message_preview'] }}
                                    </p>
                                @endif

                                @if ($notification['created_at'])
                                    <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">
                                        {{ $notification['created_at'] }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div class="flex flex-col px-6">
                <div class="mb-5 flex items-center justify-center">
                    <div class="rounded-full bg-gray-100 p-3 dark:bg-gray-500/20">
                        <x-heroicon-o-chat-bubble-oval-left-ellipsis class="h-6 w-6 text-gray-500 dark:text-gray-400" />
                    </div>
                </div>

                <div class="text-center">
                    <h2 class="text-base font-semibold leading-6 text-gray-950 dark:text-white">
                        No new messages
                    </h2>

                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                        Please check again later.
                    </p>
                </div>
            </div>
        @endif

        <x-slot name="footerActions">
            <x-filament::button
                :href="UserChat::getUrl()"
                tag="a"
            >
                Open chat
            </x-filament::button>
        </x-slot>
    </x-filament::modal>
</div>
