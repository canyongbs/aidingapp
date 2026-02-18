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

namespace AidingApp\ServiceManagement\Filament\Actions;

use AidingApp\Ai\Actions\CompletePrompt;
use AidingApp\Ai\Exceptions\MessageResponseException;
use AidingApp\Ai\Models\AiAssistant;
use AidingApp\Ai\Settings\AiIntegratedAssistantSettings;
use AidingApp\Contact\Models\Contact;
use AidingApp\KnowledgeBase\Models\KnowledgeBaseItem;
use AidingApp\KnowledgeBase\Models\Scopes\KnowledgeBasePortalAssistantItem;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestUpdate;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Support\Enums\Width;
use Illuminate\Support\Facades\Vite;

class DraftServiceRequestUpdateWithAiAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Draft with AI Assistant')
            ->link()
            ->icon('heroicon-m-pencil')
            ->modalContent(function (RelationManager $livewire) {
                /** @var ServiceRequest $serviceRequest */
                $serviceRequest = $livewire->getOwnerRecord();

                return view('service-management::filament.actions.draft-with-ai-modal-content-service-request-update', [
                    'contactName' => $serviceRequest->respondent->full_name,
                    'serviceRequestNumber' => $serviceRequest->service_request_number,
                    'avatarUrl' => AiAssistant::query()->where('is_default', true)->first()
                        ?->getFirstTemporaryUrl(now()->addHour(), 'avatar', 'avatar-height-250px') ?: Vite::asset('resources/images/canyon-ai-headshot.jpg'),
                ]);
            })
            ->modalWidth(Width::ExtraLarge)
            ->modalSubmitActionLabel('Draft')
            ->form([
                Textarea::make('instructions')
                    ->hiddenLabel()
                    ->rows(4)
                    ->placeholder('Describe the update you wish to share, and I will help you draft it. I already have all the context of this service request, and your knowledge base, so no need to share those details.')
                    ->required(),
            ])
            ->action(function (array $data, Get $get, Set $set, RelationManager $livewire) {
                $model = app(AiIntegratedAssistantSettings::class)->getDefaultModel();

                /** @var ServiceRequest $serviceRequest */
                $serviceRequest = $livewire->getOwnerRecord();

                $serviceRequestNumber = $serviceRequest->service_request_number;
                $serviceRequestTitle = $serviceRequest->title;
                $serviceRequestDescription = $serviceRequest->close_details;
                $serviceRequestTypeName = $serviceRequest->priority->type->name ?? 'Unknown';
                $serviceRequestTypeDescription = $serviceRequest->priority?->type?->description;

                $updateHistory = $serviceRequest->serviceRequestUpdates()
                    ->with('createdBy')
                    ->orderBy('created_at', 'asc')
                    ->get()
                    ->map(function (ServiceRequestUpdate $update) {
                        $createdByName = match ($update->createdBy::class) {
                            User::class => $update->createdBy->name,
                            Contact::class => $update->createdBy->full_name,
                            ServiceRequest::class => 'AI',
                            default => 'Unknown',
                        };

                        return "- {$update->created_at->format('Y-m-d H:i:s')} - {$createdByName}: {$update->update}";
                    })
                    ->join("\n");

                $typeDescriptionText = $serviceRequestTypeDescription
                    ? " with the description of \"{$serviceRequestTypeDescription}\""
                    : '';

                $updateHistoryText = $updateHistory
                    ? "\n\nThe full update history to date includes:\n{$updateHistory}\n"
                    : '';

                $prompt = "You are being asked to assist a service provider in the service to an end customer. The customer opened the issue in the service request type {$serviceRequestTypeName}{$typeDescriptionText}. The service request (#{$serviceRequestNumber}) has a title of \"{$serviceRequestTitle}\" and description of \"{$serviceRequestDescription}\".{$updateHistoryText}\n" .
                    "The service agent will provide you instructions below on how you should assist them with the update that needs to be drafted.\n\n" .
                    "You should only respond with the update content. Do not include any greetings or signatures.\n" .
                    "Do NOT use formatting in Markdown, plain text only.\n\n" .
                    "Here are the service agent's instructions:";

                try {
                    $content = app(CompletePrompt::class)->execute(
                        aiModel: $model,
                        prompt: $prompt,
                        content: $data['instructions'],
                        files: KnowledgeBaseItem::query()->tap(app(KnowledgeBasePortalAssistantItem::class))->get(['id'])->all(),
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

                $set('update', $content);
            });
    }

    public static function getDefaultName(): ?string
    {
        return 'draftWithAi';
    }
}
