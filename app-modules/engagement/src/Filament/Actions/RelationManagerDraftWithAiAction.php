<?php

namespace AidingApp\Engagement\Filament\Actions;

use AidingApp\Ai\Actions\CompletePrompt;
use AidingApp\Ai\Exceptions\MessageResponseException;
use AidingApp\Ai\Models\AiAssistant;
use AidingApp\Ai\Settings\AiIntegratedAssistantSettings;
use AidingApp\Authorization\Enums\LicenseType;
use App\Settings\LicenseSettings;
use Closure;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\Facades\Vite;

class RelationManagerDraftWithAiAction extends Action
{
    protected array | Closure $mergeTags = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Draft with AI Assistant')
            ->link()
            ->icon('heroicon-m-pencil')
            ->modalContent(fn (RelationManager $livewire) => view('engagement::filament.actions.draft-with-ai-modal-content', [
                'recordTitle' => $livewire->getOwnerRecord()->getAttribute($livewire->getOwnerRecord()::displayNameKey()),
                'avatarUrl' => AiAssistant::query()->where('is_default', true)->first()
                    ?->getFirstTemporaryUrl(now()->addHour(), 'avatar', 'avatar-height-250px') ?: Vite::asset('resources/images/canyon-ai-headshot.jpg'),
            ]))
            ->modalWidth(MaxWidth::ExtraLarge)
            ->modalSubmitActionLabel('Draft')
            ->form([
                Textarea::make('instructions')
                    ->hiddenLabel()
                    ->rows(4)
                    ->placeholder('What do you want to write about?')
                    ->required(),
            ])
            ->action(function (array $data, Get $get, Set $set, ManageRelatedRecords | RelationManager $livewire) {
                $model = app(AiIntegratedAssistantSettings::class)->getDefaultModel();

                $userName = auth()->user()->name;
                $userJobTitle = auth()->user()->job_title ?? 'staff member';
                $clientName = app(LicenseSettings::class)->data->subscription->clientName;
                $educatableLabel = $livewire->getOwnerRecord()::getLabel();

                $mergeTagsList = collect($this->getMergeTags())
                    ->map(fn (string $tag): string => <<<HTML
                        <span data-type="mergeTag" data-id="{$tag}" contenteditable="false">{$tag}</span>
                    HTML)
                    ->join(', ', ' and ');

                try {
                    $content = app(CompletePrompt::class)->execute(
                        aiModel: $model,
                        prompt: <<<EOL
                            The user's name is {$userName} and they are a {$userJobTitle} at {$clientName}.
                            Please draft an email for a {$educatableLabel} at their college.
                            The user will send a message to you containing instructions for the content.

                            You should only respond with the email content, you should never greet them.
                            The first line should contain the raw subject of the email, with no "Subject: " label at the start.
                            All following lines after the subject are the email body.

                            You should never include a signature in your response, the user will add that themselves.

                            When you answer, it is crucial that you format the email body using rich text in Markdown format.
                            The subject line can not use Markdown formatting, it is plain text.
                            Do not ever mention in your response that the answer is being formatted/rendered in Markdown.

                            You may use merge tags to insert dynamic data about the contact in the body of the email, but these do not work in the subject line:
                            {$mergeTagsList}
                        EOL,
                        content: $data['instructions'],
                    );
                } catch (MessageResponseException $exception) {
                    report($exception);

                    Notification::make()
                        ->title('AI Assistant Error')
                        ->body('There was an issue using the AI assistant. Please try again later.')
                        ->danger()
                        ->send();

                    $this->halt();

                    return;
                }

                $set('subject', (string) str($content)
                    ->before("\n")
                    ->trim());

                $set('body', (string) str($content)->after("\n")->markdown());
            })
            ->visible(
                auth()->user()->hasLicense(LicenseType::RecruitmentCrm)
            );
    }

    public static function getDefaultName(): ?string
    {
        return 'draftWithAi';
    }

    public function mergeTags(array | Closure $tags): static
    {
        $this->mergeTags = $tags;

        return $this;
    }

    public function getMergeTags(): array
    {
        return $this->evaluate($this->mergeTags);
    }
}
