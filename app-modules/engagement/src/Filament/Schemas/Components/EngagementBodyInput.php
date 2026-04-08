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
      of the licensor in the software. Any use of the licensor's trademarks is subject
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

namespace AidingApp\Engagement\Filament\Schemas\Components;

use AidingApp\Engagement\Models\EmailTemplate;
use AidingApp\Engagement\Models\Engagement;
use AidingApp\Engagement\Models\EngagementBatch;
use Exception;
use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class EngagementBodyInput
{
    public static function make(): RichEditor
    {
        return RichEditor::make('body')
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
                                ->when(
                                    $get('onlyMyTeamTemplates'),
                                    fn (Builder $query) => $query->whereIn('user_id', auth()->user()->team->users->pluck('id'))
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
                    Checkbox::make('onlyMyTeamTemplates')
                        ->label("Only show my team's templates")
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
            ->saveFileAttachmentFromAnotherRecordUsing(function (mixed $file, EngagementBatch|Engagement $record): ?string {
                return Media::query()
                    ->where('uuid', $file)
                    ->where('model_type', (new EmailTemplate())->getMorphClass())
                    ->first()
                    ?->copy($record, 'body', 's3-public')
                    ->uuid;
            })
            ->helperText('You can insert recipient or your information by typing {{ and choosing a merge value to insert.')
            ->columnSpanFull()
            ->json();
    }
}
