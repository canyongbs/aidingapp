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

namespace AidingApp\KnowledgeBase\Filament\Resources\KnowledgeBaseItems\Pages;

use AidingApp\Division\Models\Division;
use AidingApp\KnowledgeBase\Filament\Resources\Actions\DraftKnowledgeBaseItemWithAiAction;
use AidingApp\KnowledgeBase\Filament\Resources\KnowledgeBaseItems\KnowledgeBaseItemResource;
use AidingApp\KnowledgeBase\Models\KnowledgeBaseCategory;
use AidingApp\KnowledgeBase\Models\KnowledgeBaseItem;
use AidingApp\KnowledgeBase\Models\KnowledgeBaseStatus;
use App\Concerns\EditPageRedirection;
use App\Filament\Forms\Components\UserSelect;
use App\Filament\Pages\Concerns\BreadcrumbCharacterLimit;
use App\Models\Scopes\TagsForClass;
use CodeWithDennis\FilamentSelectTree\SelectTree;
use Filament\Actions\Action as BaseAction;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\RichEditor\ToolbarButtonGroup;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Url;

class EditKnowledgeBaseItem extends EditRecord
{
    use BreadcrumbCharacterLimit;
    use EditPageRedirection;

    protected static string $resource = KnowledgeBaseItemResource::class;

    #[Url]
    public ?string $tab = null;

    public function mount(int | string $record): void
    {
        parent::mount($record);

        if (! in_array($this->tab, ['resource', 'properties'])) {
            $this->tab = 'resource';
        }
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make()
                    ->tabs([
                        'resource' => Tab::make('Content')
                            ->label('Resource')
                            ->schema([
                                RichEditor::make('article_details')
                                    ->label('Article Details')
                                    ->hiddenLabel()
                                    ->json()
                                    ->toolbarButtons([
                                        ['bold', 'italic', 'underline', 'strike', 'subscript', 'superscript', 'link'],
                                        [ToolbarButtonGroup::make('Heading', ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'])->textualButtons(), 'blockquote', 'code', 'codeBlock', 'bulletList', 'orderedList', 'horizontalRule'],
                                        ['alignStart', 'alignCenter', 'alignEnd'],
                                        ['textColor', 'highlight', 'lead', 'small'],
                                        ['attachFiles', 'video'],
                                        ['grid', 'table', 'details'],
                                        ['clearFormatting'],
                                        ['undo', 'redo'],
                                    ])
                                    ->resizableImages()
                                    ->columnSpanFull()
                                    ->extraInputAttributes([
                                        'style' => 'min-height: 32rem;',
                                    ]),
                                Actions::make([
                                    DraftKnowledgeBaseItemWithAiAction::make(),
                                ]),
                            ]),
                        'properties' => Tab::make('Properties')
                            ->schema([
                                TextInput::make('title')
                                    ->label('Article Title')
                                    ->required()
                                    ->string()
                                    ->suffixAction(
                                        BaseAction::make('saveArticleTitle')
                                            ->icon('heroicon-o-check')
                                            ->action(function (KnowledgeBaseItem $record, string $state) {
                                                if ($record->title === $state) {
                                                    return;
                                                }

                                                $record->update([
                                                    'title' => $state,
                                                ]);

                                                if ($record->wasChanged('title')) {
                                                    Notification::make()
                                                        ->title("Title successfully updated to '{$record->title}'")
                                                        ->success()
                                                        ->duration(3000)
                                                        ->send();
                                                }
                                            }),
                                    )
                                    ->columnSpanFull(),
                                Grid::make(Division::count() > 1 ? 4 : 3)
                                    ->schema([
                                        Select::make('status_id')
                                            ->label('Status')
                                            ->relationship('status', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->exists((new KnowledgeBaseStatus())->getTable(), (new KnowledgeBaseStatus())->getKeyName()),
                                        SelectTree::make('category_id')
                                            ->label('Category')
                                            ->required()
                                            ->relationship(
                                                'category',
                                                'name',
                                                'parent_id',
                                                modifyQueryUsing: fn (Builder $query) => $query->orderBy('sort'),
                                                modifyChildQueryUsing: fn (Builder $query) => $query->orderBy('sort'),
                                            )
                                            ->enableBranchNode()
                                            ->searchable()
                                            ->exists((new KnowledgeBaseCategory())->getTable(), (new KnowledgeBaseCategory())->getKeyName()),
                                        Select::make('division')
                                            ->label('Division')
                                            ->multiple()
                                            ->relationship('division', 'name')
                                            ->searchable(['name', 'code'])
                                            ->preload()
                                            ->afterStateHydrated(function (array $state, Set $set) {
                                                if (empty($state)) {
                                                    $set('division', [Division::count() === 1 ? Division::query()->first()?->getKey() : null]);
                                                }
                                            })
                                            ->visible(fn (): bool => Division::count() > 1)
                                            ->saveRelationshipsWhenHidden()
                                            ->exists((new Division())->getTable(), (new Division())->getKeyName()),
                                        UserSelect::make('manager_ids')
                                            ->label('Managers')
                                            ->relationship('managers')
                                            ->multiple()
                                            ->exists('users', 'id'),
                                    ]),
                                Grid::make(3)
                                    ->schema([
                                        Toggle::make('public')
                                            ->label('Public')
                                            ->default(false)
                                            ->onColor('success')
                                            ->offColor('gray'),
                                        Toggle::make('is_featured')
                                            ->label('Featured')
                                            ->default(false)
                                            ->onColor('success')
                                            ->offColor('gray'),
                                        Toggle::make('has_table_of_contents')
                                            ->label('Table of Contents')
                                            ->default(false)
                                            ->onColor('success')
                                            ->offColor('gray'),
                                    ]),
                                SpatieMediaLibraryFileUpload::make('article_attachments')
                                    ->label('File Attachments')
                                    ->disk('s3')
                                    ->visibility('private')
                                    ->collection('article_attachments')
                                    ->multiple()
                                    ->preserveFilenames()
                                    ->reorderable()
                                    ->downloadable()
                                    ->previewable(false)
                                    ->columnSpanFull()
                                    ->helperText('Note: Uploaded file attachments are not evaluated or used by the AI Support Assistant at this time.'),
                                Textarea::make('notes')
                                    ->label('Notes')
                                    ->columnSpanFull()
                                    ->extraInputAttributes(['style' => 'min-height: 12rem;']),
                                Select::make('tags')
                                    ->relationship(
                                        'tags',
                                        'name',
                                        fn (Builder $query) => $query->tap(new TagsForClass(new KnowledgeBaseItem()))
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->multiple()
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),
                    ])
                    ->columnSpanFull()
                    ->livewireProperty('tab'),
            ]);
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Article details successfully saved';
    }

    protected function getFormActions(): array
    {
        return [
            $this->getSubmitFormAction()->label('Save'),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            BaseAction::make('save')
                ->action('save')
                ->button()
                ->color('primary')
                ->label('Save'),
        ];
    }

    public function getRedirectUrl(): ?string
    {
        $parameters = ['record' => $this->record];

        if ($this->tab) {
            $parameters['tab'] = $this->tab;
        }

        return KnowledgeBaseItemResource::getUrl('view', $parameters);
    }
}
