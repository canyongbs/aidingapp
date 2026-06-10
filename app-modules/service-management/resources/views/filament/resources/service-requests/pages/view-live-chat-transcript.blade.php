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
    use AidingApp\Contact\Models\Contact;
    use App\Models\User;
    use Filament\Forms\Components\RichEditor\RichContentRenderer;

    $messages = $this->getMessages();
    $previousAuthorKey = null;
@endphp

<x-filament-panels::page>
    {{ $this->infolist }}

    <div
        class="space-y-1 rounded-xl border border-gray-200 bg-gradient-to-b from-gray-50 to-white p-6 dark:border-gray-700 dark:from-gray-800 dark:to-gray-900"
    >
        @forelse ($messages as $index => $message)
            @php
                $isOwn = $message->author instanceof User;
                $authorName =
                    $message->author instanceof User
                        ? $message->author->name
                        : ($message->author instanceof Contact
                            ? $message->author->full_name
                            : 'Unknown');
                $authorKey = ($isOwn ? 'agent' : 'contact') . ':' . $authorName;
                $showAuthor = $previousAuthorKey !== $authorKey;

                $nextMessage = $messages->get($index + 1);
                $showTimestamp =
                    ! $nextMessage ||
                    $nextMessage->author instanceof User !== $isOwn ||
                    $message->created_at->diffInMinutes($nextMessage->created_at) > 2;

                $previousAuthorKey = $authorKey;
            @endphp

            <div
                @class([
                    'mb-4' => $showTimestamp,
                    'mb-1' => ! $showTimestamp,
                    'text-right' => $isOwn,
                ])
            >
                @if ($showAuthor)
                    <p @class(['mb-1 text-xs font-medium text-gray-600 dark:text-gray-400', 'pr-11' => $isOwn])>
                        {{ $authorName }}
                    </p>
                @endif

                <div
                    @class([
                        'flex gap-3',
                        'justify-end' => $isOwn,
                    ])
                >
                    <div
                        @class([
                            'max-w-[80%]',
                            'flex flex-col items-end' => $isOwn,
                        ])
                    >
                        <div
                            @class([
                                'w-fit max-w-full border px-4 py-2 shadow-sm',
                                'rounded-lg rounded-tr-sm bg-primary-50 border-primary-200 dark:bg-primary-900/30 dark:border-primary-800' => $isOwn,
                                'rounded-lg rounded-tl-sm bg-white border-gray-200 dark:bg-gray-800 dark:border-gray-700' => ! $isOwn,
                            ])
                        >
                            <div
                                class="prose prose-sm max-w-none leading-relaxed text-gray-800 prose-p:my-0 prose-p:first:mt-0 prose-p:last:mb-0 prose-ul:my-1 prose-ol:my-1 prose-li:my-0 dark:prose-invert dark:text-gray-200"
                            >
                                {!! RichContentRenderer::make($message->content)->toHtml() !!}
                            </div>
                        </div>

                        @if ($showTimestamp)
                            <div class="mt-0.5 text-xs">
                                <span class="text-gray-500 dark:text-gray-500">
                                    {{ $message->created_at->format('g:i A') }}
                                </span>
                            </div>
                        @endif
                    </div>

                    @if ($isOwn)
                        <div class="shrink-0 w-8">
                            @if ($showAuthor)
                                <x-filament-panels::avatar.user :user="$message->author" class="!h-8 !w-8" />
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="py-8 text-center text-gray-500 dark:text-gray-400">No messages in this conversation.</div>
        @endforelse
    </div>
</x-filament-panels::page>
