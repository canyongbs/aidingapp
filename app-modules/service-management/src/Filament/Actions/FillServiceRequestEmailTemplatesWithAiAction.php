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

namespace AidingApp\ServiceManagement\Filament\Actions;

use AidingApp\Ai\Actions\CompletePrompt;
use AidingApp\Ai\Exceptions\MessageResponseException;
use AidingApp\Ai\Settings\AiIntegratedAssistantSettings;
use AidingApp\ServiceManagement\Enums\ServiceRequestEmailTemplateType;
use AidingApp\ServiceManagement\Enums\ServiceRequestTypeEmailTemplateRole;
use AidingApp\ServiceManagement\Filament\Blocks\ServiceRequestTypeEmailTemplateButtonBlock;
use AidingApp\ServiceManagement\Filament\Blocks\SurveyResponseEmailTemplateTakeSurveyButtonBlock;
use AidingApp\ServiceManagement\Models\ServiceRequestNotificationAutomationEmailTemplate;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use AidingApp\ServiceManagement\Models\ServiceRequestTypeEmailTemplate;
use AidingApp\ServiceManagement\Settings\ServiceRequestNotificationAutomationSettings;
use App\Enums\Feature;
use App\Features\ServiceRequestNotificationAutomationFeature;
use Filament\Actions\Action;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\RichEditor\RichContentRenderer;
use Filament\Notifications\Notification;
use Filament\Support\Enums\Width;
use Illuminate\Support\Facades\Gate;
use Tiptap\Editor;

class FillServiceRequestEmailTemplatesWithAiAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Fill with AI')
            ->icon('heroicon-m-sparkles')
            ->visible(function (): bool {
                if (! ServiceRequestNotificationAutomationFeature::active()) {
                    return false;
                }

                if (! Gate::check(Feature::ServiceManagement->getGateName())) {
                    return false;
                }

                return app(ServiceRequestNotificationAutomationSettings::class)->is_enabled;
            })
            ->disabled(fn (): bool => count($this->getConfiguredRoles()) === 0)
            ->tooltip(fn (): ?string => count($this->getConfiguredRoles()) === 0
                ? 'No AI templates have been configured. Contact your administrator to set up communication automation.'
                : null)
            ->modalSubmitActionLabel('Generate')
            ->modalWidth(Width::Medium)
            ->schema(function (): array {
                $allRoles = $this->getAllRolesForEventType();
                $configuredRoles = $this->getConfiguredRoles();

                return [
                    CheckboxList::make('roles')
                        ->label('Select roles to generate templates for')
                        ->options(
                            collect($allRoles)
                                ->mapWithKeys(fn (ServiceRequestTypeEmailTemplateRole $role) => [$role->value => $role->getLabel()])
                                ->all()
                        )
                        ->descriptions(
                            collect($allRoles)
                                ->filter(fn (ServiceRequestTypeEmailTemplateRole $role) => ! in_array($role, $configuredRoles, true))
                                ->mapWithKeys(fn (ServiceRequestTypeEmailTemplateRole $role) => [$role->value => 'Not configured. Contact your administrator to set up this template.'])
                                ->all()
                        )
                        ->disableOptionWhen(fn (string $value): bool => ! in_array(
                            ServiceRequestTypeEmailTemplateRole::from($value),
                            $configuredRoles,
                            true,
                        ))
                        ->default(
                            collect($configuredRoles)
                                ->map(fn (ServiceRequestTypeEmailTemplateRole $role) => $role->value)
                                ->all()
                        )
                        ->required(),
                ];
            })
            ->action(function (array $data): void {
                $livewire = $this->getLivewire();

                /** @var array<int, string> $roles */
                $roles = $data['roles'];

                $selectedRoles = collect($roles)
                    ->map(fn (string $value) => ServiceRequestTypeEmailTemplateRole::from($value));

                $settings = app(ServiceRequestNotificationAutomationSettings::class);
                $model = app(AiIntegratedAssistantSettings::class)->getDefaultModel();
                $eventType = $livewire->type;

                /** @var ServiceRequestType $serviceRequestType */
                $serviceRequestType = $livewire->getRecord();

                $failedRoles = [];
                $generatedResults = [];

                foreach ($selectedRoles as $role) {
                    try {
                        $result = $this->generateForRole(
                            settings: $settings,
                            eventType: $eventType,
                            role: $role,
                            serviceRequestType: $serviceRequestType,
                            model: $model,
                        );

                        if ($result === null) {
                            $failedRoles[] = $role->getLabel();

                            continue;
                        }

                        $generatedResults[$role->value] = $result;
                    } catch (MessageResponseException $exception) {
                        report($exception);
                        $failedRoles[] = $role->getLabel();
                    }
                }

                if (count($generatedResults) > 0) {
                    $state = $livewire->form->getRawState();

                    foreach ($generatedResults as $roleValue => $result) {
                        $state[$roleValue]['subject'] = $result['subject'];
                        $state[$roleValue]['body'] = $result['body'];
                    }

                    $livewire->form->fill($state);
                }

                if (count($failedRoles) > 0) {
                    Notification::make()
                        ->title('Some templates could not be generated')
                        ->body('Failed roles: ' . implode(', ', $failedRoles))
                        ->warning()
                        ->send();
                }

                if (count($failedRoles) < $selectedRoles->count()) {
                    Notification::make()
                        ->title('Templates generated successfully. Please review and save the templates to apply the changes.')
                        ->success()
                        ->send();
                }
            });
    }

    public static function getDefaultName(): ?string
    {
        return 'fillWithAi';
    }

    /**
     * @return array<string, mixed>|null
     */
    protected function generateForRole(
        ServiceRequestNotificationAutomationSettings $settings,
        ServiceRequestEmailTemplateType $eventType,
        ServiceRequestTypeEmailTemplateRole $role,
        ServiceRequestType $serviceRequestType,
        mixed $model,
    ): ?array {
        $baseTemplate = ServiceRequestNotificationAutomationEmailTemplate::where([
            'type' => $eventType,
            'role' => $role,
        ])->first();

        $baseSubjectHtml = '';
        $baseBodyHtml = '';
        $userInstructions = '';

        if ($baseTemplate) {
            $baseSubjectHtml = $baseTemplate->subject
                ? RichContentRenderer::make($baseTemplate->subject)->toUnsafeHtml()
                : '';
            $baseBodyHtml = $baseTemplate->body
                ? RichContentRenderer::make($baseTemplate->body)->toUnsafeHtml()
                : '';
            $userInstructions = $baseTemplate->ai_instructions ?? '';
        }

        $availableMergeTags = collect(array_keys(ServiceRequestTypeEmailTemplate::getMergeTags()))
            ->map(fn (string $tag): string => '<span data-type="mergeTag" data-id="' . $tag . '">' . $tag . '</span>')
            ->join(', ');

        $availableCustomBlocks = collect([
            [ServiceRequestTypeEmailTemplateButtonBlock::getId(), '{"label":"View Service Request","alignment":"center"}', 'A button that links to the service request in the portal. Use this so the recipient can quickly view the service request.'],
            [SurveyResponseEmailTemplateTakeSurveyButtonBlock::getId(), '{"label":"Take Survey","alignment":"center"}', 'A button that links to a satisfaction survey. Only use this for survey response emails sent to customers.'],
        ])
            ->map(fn (array $block): string => '<div data-type="customBlock" data-id="' . $block[0] . '" data-config="' . e($block[1]) . '"></div> — ' . $block[2])
            ->join("\n");

        $mergeValues = [
            'example subject' => $baseSubjectHtml,
            'example body' => $baseBodyHtml,
            'user instructions' => $userInstructions,
            'available merge tags' => $availableMergeTags,
            'available custom blocks' => $availableCustomBlocks,
            'type name' => $serviceRequestType->name,
            'type description' => $serviceRequestType->description ?? '',
            'event name' => $eventType->getLabel(),
            'role name' => $role->getLabel(),
        ];

        $resolvedPrompt = html_entity_decode(
            RichContentRenderer::make($settings->ai_prompt)
                ->mergeTags($mergeValues)
                ->toText(),
            ENT_QUOTES,
            'UTF-8',
        );

        $aiResponse = app(CompletePrompt::class)->execute(
            aiModel: $model,
            prompt: $resolvedPrompt,
            content: '',
        );

        return $this->parseAiResponse($aiResponse);
    }

    /**
     * @return array{subject: array<string, mixed>, body: array<string, mixed>}|null
     */
    protected function parseAiResponse(string $response): ?array
    {
        $response = trim($response);

        if (preg_match('/```(?:json)?\s*(.*?)\s*```/s', $response, $matches)) {
            $response = $matches[1];
        }

        $decoded = json_decode($response, true);

        if (! is_array($decoded) || ! isset($decoded['subject']) || ! isset($decoded['body'])) {
            return null;
        }

        $renderer = RichContentRenderer::make();
        $extensions = $renderer->getTipTapPhpExtensions();

        $subjectEditor = new Editor(['extensions' => $extensions]);
        $subjectEditor->setContent($decoded['subject']);
        $subjectJson = $subjectEditor->getDocument();

        $bodyEditor = new Editor(['extensions' => $extensions]);
        $bodyEditor->setContent($decoded['body']);
        $bodyJson = $bodyEditor->getDocument();

        return [
            'subject' => $subjectJson,
            'body' => $bodyJson,
        ];
    }

    /**
     * @return array<int, ServiceRequestTypeEmailTemplateRole>
     */
    protected function getAllRolesForEventType(): array
    {
        $eventType = $this->getLivewire()->type;

        if ($eventType === ServiceRequestEmailTemplateType::SurveyResponse) {
            return [ServiceRequestTypeEmailTemplateRole::Customer];
        }

        return ServiceRequestTypeEmailTemplateRole::cases();
    }

    /**
     * @return array<int, ServiceRequestTypeEmailTemplateRole>
     */
    protected function getConfiguredRoles(): array
    {
        $eventType = $this->getLivewire()->type;

        $existingRoles = ServiceRequestNotificationAutomationEmailTemplate::where('type', $eventType)
            ->pluck('role')
            ->all();

        return array_values(array_filter(
            $this->getAllRolesForEventType(),
            fn (ServiceRequestTypeEmailTemplateRole $role) => in_array($role, $existingRoles),
        ));
    }
}
