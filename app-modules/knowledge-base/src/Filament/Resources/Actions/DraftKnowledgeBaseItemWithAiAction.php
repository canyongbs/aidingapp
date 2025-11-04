<?php

namespace AidingApp\KnowledgeBase\Filament\Resources\Actions;

use AidingApp\Ai\Actions\CompletePrompt;
use AidingApp\Ai\Exceptions\MessageResponseException;
use AidingApp\Ai\Models\AiAssistant;
use AidingApp\Ai\Settings\AiIntegratedAssistantSettings;
use AidingApp\Authorization\Enums\LicenseType;
use App\Settings\LicenseSettings;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\Facades\Vite;

class DraftKnowledgeBaseItemWithAiAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Draft with AI Assistant')
            ->link()
            ->icon('heroicon-m-pencil')
            ->modalContent(fn () => view('knowledge-base::filament.actions.draft-with-ai-modal-content-knowledge-base', [
                'recordTitle' => null, // We'll get this from the form state
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
            ->action(function (array $data, Get $get, Set $set) {
                $model = app(AiIntegratedAssistantSettings::class)->getDefaultModel();

                $userName = auth()->user()->name;
                $userJobTitle = auth()->user()->job_title ?? 'staff member';
                $clientName = app(LicenseSettings::class)->data->subscription->clientName;
                $itemTitle = $get('title') ?? 'Untitled';

                try {
                    $content = app(CompletePrompt::class)->execute(
                        aiModel: $model,
                        prompt: <<<EOL
                            My name is {$userName}, and I am a {$userJobTitle} at {$clientName}. I am currently editing an item in the knowledge base of the college. The current title is "{$itemTitle}". 

                            You should only respond with the item content, you should never greet them.
                            The first line should contain the raw title of the item, with no "Title: " label at the start.
                            All following lines after the title are the item body.

                            When you answer, it is crucial that you format the item body using rich text in Markdown format.
                            The title line can not use Markdown formatting, it is plain text.

                            Then, write the item content on the following instructions provided by user.

                            Here are the details:
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

                $set('title', (string) str($content)
                    ->before("\n")
                    ->trim());

                $set('article_details', (string) str($content)->after("\n")->markdown());
            })
            ->visible(
                auth()->user()->hasLicense(LicenseType::RecruitmentCrm)
            );
    }

    public static function getDefaultName(): ?string
    {
        return 'draftWithAi';
    }
}