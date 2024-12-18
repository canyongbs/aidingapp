<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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

namespace AidingApp\Engagement\Filament\Resources\EngagementResource\Fields;

use AidingApp\Engagement\Enums\EngagementDeliveryMethod;
use AidingApp\Engagement\Models\Engagement;
use AidingApp\Engagement\Models\SmsTemplate;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\RelationManagers\RelationManager;
use FilamentTiptapEditor\TiptapEditor;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Expression;

class EngagementSmsBodyField
{
    public static function make(string $context, ?Form $form = null)
    {
        // TODO Implement length validation (320 characters max)
        // https://www.twilio.com/docs/glossary/what-sms-character-limit#:~:text=Twilio's%20platform%20supports%20long%20messages,best%20deliverability%20and%20user%20experience.
        return TiptapEditor::make('body')
            ->label('Body')
            ->mergeTags([
                'contact full name',
                'contact email',
            ])
            ->showMergeTagsInBlocksPanel(is_null($form) ? false : ! ($form->getLivewire() instanceof RelationManager))
            ->profile('sms')
            ->required()
            ->hintAction(fn (TiptapEditor $component) => Action::make('loadSmsTemplate')
                ->form([
                    Select::make('smsTemplate')
                        ->searchable()
                        ->options(function (Get $get): array {
                            return SmsTemplate::query()
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
                            return SmsTemplate::query()
                                ->when(
                                    $get('onlyMyTemplates'),
                                    fn (Builder $query) => $query->whereBelongsTo(auth()->user())
                                )
                                ->when(
                                    $get('onlyMyTeamTemplates'),
                                    fn (Builder $query) => $query->whereIn('user_id', auth()->user()->teams->users->pluck('id'))
                                )
                                ->where(new Expression('lower(name)'), 'like', "%{$search}%")
                                ->orderBy('name')
                                ->limit(50)
                                ->pluck('name', 'id')
                                ->toArray();
                        }),
                    Checkbox::make('onlyMyTemplates')
                        ->label('Only show my templates')
                        ->live()
                        ->afterStateUpdated(fn (Set $set) => $set('smsTemplate', null)),
                    Checkbox::make('onlyMyTeamTemplates')
                        ->label("Only show my team's templates")
                        ->live()
                        ->afterStateUpdated(fn (Set $set) => $set('smsTemplate', null)),
                ])
                ->action(function (array $data) use ($component) {
                    $template = SmsTemplate::find($data['smsTemplate']);

                    if (! $template) {
                        return;
                    }

                    $component->state(
                        $component->generateImageUrls($template->content),
                    );
                }))
            ->when($context === 'create', function (Field $field) {
                $field->hidden(fn (Get $get): bool => $get('delivery_method') === EngagementDeliveryMethod::Email->value);
            })
            ->when($context === 'edit', function (Field $field) {
                $field->visible(fn (Engagement $record): bool => $record->deliverable->channel === EngagementDeliveryMethod::Sms);
            })
            ->helperText('You can insert contact information by typing {{ and choosing a tag to insert.')
            ->columnSpanFull();
    }
}
