<?php

/*
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
*/

namespace AidingApp\Engagement\Filament\Actions;

use AidingApp\Engagement\Actions\CreateEngagementBatch;
use AidingApp\Engagement\DataTransferObjects\EngagementCreationData;
use AidingApp\Engagement\Filament\Schemas\Components\EngagementBodyInput;
use AidingApp\Engagement\Filament\Schemas\Components\EngagementChannelSelect;
use AidingApp\Engagement\Filament\Schemas\Components\EngagementScheduledAtDateTimePicker;
use AidingApp\Engagement\Filament\Schemas\Components\EngagementSendLaterToggle;
use AidingApp\Engagement\Filament\Schemas\Components\EngagementSubjectInput;
use AidingApp\Engagement\Models\EngagementBatch;
use AidingApp\Notification\Enums\NotificationChannel;
use Filament\Actions\BulkAction;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class BulkEngagementAction
{
    public static function make(string $context): BulkAction
    {
        return BulkAction::make('engage')
            ->icon('heroicon-o-chat-bubble-bottom-center-text')
            ->modalHeading('Send Bulk Engagement')
            ->slideOver()
            ->modalDescription(fn (Collection $records) => "You have selected {$records->count()} {$context} to engage.")
            ->model(EngagementBatch::class)
            ->steps([
                Step::make('Choose your delivery method')
                    ->schema([
                        EngagementChannelSelect::make(),
                    ]),
                Step::make('Engagement Details')
                    ->description("Add the details that will be sent to the selected {$context}")
                    ->schema([
                        EngagementSubjectInput::make(),
                        EngagementBodyInput::make(),
                        Actions::make([
                            BulkDraftWithAiAction::make()
                                ->mergeTags([
                                    'contact full name',
                                    'contact email',
                                ]),
                        ]),
                    ]),
                Step::make('Schedule')
                    ->description('Choose when you would like to send this engagement.')
                    ->schema([
                        EngagementSendLaterToggle::make(),
                        EngagementScheduledAtDateTimePicker::make(),
                    ]),
            ])
            ->action(function (Collection $records, array $data, Schema $schema) {
                $channel = NotificationChannel::parse($data['channel']);

                $data['body'] ??= ['type' => 'doc', 'content' => []];

                app(CreateEngagementBatch::class)->execute(new EngagementCreationData(
                    user: auth()->user(),
                    recipient: $records,
                    channel: $channel,
                    subject: $data['subject'] ?? null,
                    body: $data['body'],
                    scheduledAt: ($data['send_later'] ?? false) ? Carbon::parse($data['scheduled_at'] ?? null) : null,
                    schema: $schema,
                ));
            })
            ->modalSubmitActionLabel('Send')
            ->deselectRecordsAfterCompletion()
            ->modalCloseButton(false)
            ->closeModalByClickingAway(false)
            ->closeModalByEscaping(false);
    }
}
