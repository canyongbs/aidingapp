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

use AidingApp\ServiceManagement\Enums\ServiceRequestEmailTemplateType;
use AidingApp\ServiceManagement\Enums\ServiceRequestTypeEmailTemplateRole;
use AidingApp\ServiceManagement\Models\ServiceRequestNotificationAutomationEmailTemplate;
use Filament\Actions\Action;
use Filament\Pages\SettingsPage;

class PreloadBaseTemplatesAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->modalHeading('Override Defaults')
            ->modalDescription('Turning on the override will load the base templates set by the global administrator as the starting point, replacing any templates already entered below.')
            ->modalSubmitActionLabel('Load Base Templates')
            ->modalCloseButton(false)
            ->closeModalByClickingAway(false)
            ->closeModalByEscaping(false)
            ->modalCancelAction(function (Action $action): Action {
                $action->close(false);

                if ($livewire = $this->getLivewire()) {
                    $action->livewire($livewire);
                }

                return $action
                    ->cancelParentActions()
                    ->action(function (): void {
                        $this->revertOverride();
                    });
            })
            ->action(function (): void {
                $this->preloadBaseTemplates();
            });
    }

    public static function getDefaultName(): ?string
    {
        return 'preloadBaseTemplates';
    }

    protected function preloadBaseTemplates(): void
    {
        $livewire = $this->getLivewire();

        if (! $livewire instanceof SettingsPage) {
            return;
        }

        $livewire->form->fill([
            ...($livewire->data ?? []),
            'use_custom_templates' => true,
            'templates' => $this->getBaseTemplates(),
        ]);
    }

    protected function revertOverride(): void
    {
        $livewire = $this->getLivewire();

        if (! $livewire instanceof SettingsPage) {
            return;
        }

        $livewire->form->fill([
            ...($livewire->data ?? []),
            'use_custom_templates' => false,
        ]);
    }

    /**
     * @return array<string, array<string, array{subject: mixed, body: mixed}>>
     */
    protected function getBaseTemplates(): array
    {
        $existingTemplates = ServiceRequestNotificationAutomationEmailTemplate::all()
            ->keyBy(fn ($template) => "{$template->type->value}:{$template->role->value}");

        $templates = [];

        foreach (ServiceRequestEmailTemplateType::cases() as $type) {
            $roles = $type === ServiceRequestEmailTemplateType::SurveyResponse
                ? [ServiceRequestTypeEmailTemplateRole::Customer]
                : ServiceRequestTypeEmailTemplateRole::cases();

            foreach ($roles as $role) {
                $template = $existingTemplates->get("{$type->value}:{$role->value}");

                $templates[$type->value][$role->value] = [
                    'subject' => $template?->subject,
                    'body' => $template?->body,
                ];
            }
        }

        return $templates;
    }
}
