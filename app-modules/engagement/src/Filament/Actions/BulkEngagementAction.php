<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

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

namespace AidingApp\Engagement\Filament\Actions;

use AidingApp\Engagement\Actions\CreateEngagementBatch;
use AidingApp\Engagement\DataTransferObjects\EngagementCreationData;
use AidingApp\Engagement\Models\EmailTemplate;
use AidingApp\Engagement\Models\EngagementBatch;
use AidingApp\Notification\Enums\NotificationChannel;
use Exception;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class BulkEngagementAction
{
    public static function make(string $context): BulkAction
    {
        return BulkAction::make('engage')
            ->icon('heroicon-o-chat-bubble-bottom-center-text')
            ->modalHeading('Send Bulk Engagement')
            ->modalDescription(fn (Collection $records) => "You have selected {$records->count()} {$context} to engage.")
            ->steps([
                Step::make('Choose your delivery method')
                    ->schema([
                        Select::make('channel')
                            ->label('How would you like to send this engagement?')
                            ->options(NotificationChannel::getEngagementOptions())
                            ->default(NotificationChannel::Email->value)
                            // ->disableOptionWhen(fn (string $value): bool => NotificationChannel::tryFrom($value)?->getCaseDisabled())
                            ->selectablePlaceholder(false)
                            ->live(),
                    ]),
                Step::make('Engagement Details')
                    ->description("Add the details that will be sent to the selected {$context}")
                    ->schema([
                        RichEditor::make('subject')
                            ->label('Subject')
                            ->toolbarButtons([])
                            ->helperText('You may use "merge tags" to substitute information about a recipient into your subject line. Insert a "{{" in the subject line field to see a list of available merge tags')
                            ->required()
                            ->live()
                            ->placeholder('Enter the email subject here...')
                            ->columnSpanFull()
                            ->json(),
                        RichEditor::make('body')
                            ->label('Body')
                            ->toolbarButtons([['bold', 'italic', 'small', 'link'], ['h1', 'h2', 'h3', 'bulletList', 'orderedList', 'horizontalRule', 'attachFiles'], ['mergeTags']])
                            ->activePanel('mergeTags')
                            ->resizableImages()
                            ->required()
                            ->hintAction(fn (RichEditor $component) => Action::make('loadEmailTemplate')
                                ->schema([
                                    Select::make('emailTemplate')
                                        ->searchable()
                                        ->options(function (Get $get): array {
                                            return EmailTemplate::query()
                                                ->when(
                                                    $get('onlyMyTemplates'),
                                                    fn (Builder $query) => $query->whereBelongsTo(auth()->user())
                                                )
                                                ->orderBy('name')
                                                ->limit(50)
                                                ->pluck('name', 'id')
                                                ->toArray();
                                        })
                                        ->getSearchResultsUsing(function (Get $get, string $search): array {
                                            $search = Str::lower($search);

                                            return EmailTemplate::query()
                                                ->when(
                                                    $get('onlyMyTemplates'),
                                                    fn (Builder $query) => $query->whereBelongsTo(auth()->user())
                                                )
                                                ->where(new Expression('lower(name)'), 'like', "%{$search}%")
                                                ->orderBy('name')
                                                ->limit(50)
                                                ->pluck('name', 'id')
                                                ->toArray();
                                        })
                                        ->getOptionLabelUsing(fn (string $value): ?string => EmailTemplate::find($value)?->name),
                                    Checkbox::make('onlyMyTemplates')
                                        ->label('Only show my templates')
                                        ->live()
                                        ->afterStateUpdated(fn (Set $set) => $set('emailTemplate', null)),
                                ])
                                ->action(function (array $data) use ($component) {
                                    $template = EmailTemplate::find($data['emailTemplate']);

                                    if (! $template instanceof EmailTemplate) {
                                        throw new Exception('template is not instance of EmailTemplate');
                                    }

                                    $component->state($template->content);
                                }))
                            ->getFileAttachmentUrlFromAnotherRecordUsing(function (mixed $file): ?string {
                                return Media::query()
                                    ->where('uuid', $file)
                                    ->where('model_type', (new EmailTemplate())->getMorphClass())
                                    ->first()
                                    ?->getUrl();
                            })
                            ->saveFileAttachmentFromAnotherRecordUsing(function (mixed $file, EngagementBatch $record): ?string {
                                return Media::query()
                                    ->where('uuid', $file)
                                    ->where('model_type', (new EmailTemplate())->getMorphClass())
                                    ->first()
                                    ?->copy($record, 'body', 's3-public')
                                    ->uuid;
                            })
                            ->helperText('You can insert recipient or your information by typing {{ and choosing a merge value to insert.')
                            ->columnSpanFull()
                            ->json(),
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
                        Toggle::make('send_later')
                            ->reactive()
                            ->helperText('By default, this email or text will send as soon as it is created unless you schedule it to send later.'),
                        DateTimePicker::make('scheduled_at')
                            ->required()
                            ->visible(fn (Get $get) => $get('send_later')),
                    ]),
            ])
            ->action(function (Collection $records, array $data, Schema $schema) {
                $channel = NotificationChannel::parse($data['channel']);

                $data['subject'] ??= ['type' => 'doc', 'content' => []];
                $data['body'] ??= ['type' => 'doc', 'content' => []];

                app(CreateEngagementBatch::class)->execute(new EngagementCreationData(
                    user: auth()->user(),
                    recipient: $records,
                    channel: $channel,
                    subject: $data['subject'],
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
