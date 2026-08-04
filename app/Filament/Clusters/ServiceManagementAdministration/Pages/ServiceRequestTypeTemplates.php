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

namespace App\Filament\Clusters\ServiceManagementAdministration\Pages;

use AidingApp\ServiceManagement\Enums\ServiceRequestEmailTemplateType;
use AidingApp\ServiceManagement\Enums\ServiceRequestTypeEmailTemplateRole;
use AidingApp\ServiceManagement\Filament\Actions\ApplyServiceRequestCustomTemplatesAction;
use AidingApp\ServiceManagement\Filament\Concerns\HasServiceRequestTemplateEditorSchema;
use AidingApp\ServiceManagement\Models\ServiceRequestCustomEmailTemplate;
use AidingApp\ServiceManagement\Models\ServiceRequestNotificationAutomationEmailTemplate;
use AidingApp\ServiceManagement\Settings\ServiceRequestNotificationAutomationSettings;
use App\Enums\Feature;
use App\Enums\ServiceManagementAdministrationNavigationGroup;
use App\Features\ProloadServiceRequestTypeFeature;
use App\Filament\Clusters\ServiceManagementAdministration;
use App\Support\RichContentDocument;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\Toggle;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use UnitEnum;

class ServiceRequestTypeTemplates extends SettingsPage
{
    use HasServiceRequestTemplateEditorSchema;

    protected static string $settings = ServiceRequestNotificationAutomationSettings::class;

    protected static string | UnitEnum | null $navigationGroup = ServiceManagementAdministrationNavigationGroup::ServiceRequests;

    protected static ?int $navigationSort = 50;

    protected static ?string $cluster = ServiceManagementAdministration::class;

    protected static ?string $title = 'Templates';

    protected static ?string $navigationLabel = 'Templates';

    protected ?bool $hasUnsavedDataChangesAlert = false;

    public static function canAccess(): bool
    {
        if (! ProloadServiceRequestTypeFeature::active()) {
            return false;
        }

        if (! Gate::check(Feature::ServiceManagement->getGateName())) {
            return false;
        }

        return (bool) auth()->user()?->can('settings.view-any');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Toggle::make('use_custom_templates')
                    ->label('Override Defaults')
                    ->helperText('When enabled, these custom templates are used instead of the default base templates for all service request types.')
                    ->live()
                    ->afterStateUpdated(function (bool $state, Set $set): void {
                        if ($state) {
                            $set('templates', $this->preloadBaseTemplates());
                        } else {
                            $templates = $this->getServiceRequestCustomEmailTemplate()['templates'] ?? [];
                            $set('templates', $templates);
                        }
                    })
                    ->columnSpanFull(),
                Toggle::make('preload_new_service_request_types')
                    ->label('Preload New Service Request Types')
                    ->helperText('When selected, new service request types will inherit the base templates.')
                    ->visible(fn (Get $get) => $get('use_custom_templates'))
                    ->columnSpanFull(),
                Tabs::make('Event templates')
                    ->persistTab()
                    ->id('service-request-type-template-event-tabs')
                    ->tabs(array_map(
                        fn (ServiceRequestEmailTemplateType $type) => Tab::make($type->getLabel())
                            ->schema([
                                Tabs::make('Role templates')
                                    ->persistTab()
                                    ->id("service-request-type-template-role-tabs-{$type->value}")
                                    ->tabs(array_map(
                                        fn (ServiceRequestTypeEmailTemplateRole $role) => Tab::make($role->getLabel())
                                            ->schema($this->getServiceRequestTemplateEditorSchema(
                                                $type,
                                                subjectHelperText: 'You may use “merge tags” to substitute information about a service request into your subject line.',
                                            ))
                                            ->statePath("templates.{$type->value}.{$role->value}"),
                                        $type === ServiceRequestEmailTemplateType::SurveyResponse
                                            ? [ServiceRequestTypeEmailTemplateRole::Customer]
                                            : ServiceRequestTypeEmailTemplateRole::cases()
                                    ))
                                    ->columnSpanFull(),
                            ]),
                        ServiceRequestEmailTemplateType::cases()
                    ))
                    ->visible(fn (Get $get) => $get('use_custom_templates'))
                    ->columnSpanFull(),
            ]);
    }

    public function save(): void
    {
        $state = $this->form->getState();

        DB::transaction(function () use ($state): void {
            $settings = app(ServiceRequestNotificationAutomationSettings::class);
            $settings->use_custom_templates = (bool) ($state['use_custom_templates'] ?? false);
            $settings->preload_new_service_request_types = (bool) ($state['preload_new_service_request_types'] ?? false);
            $settings->save();

            $templates = $state['templates'] ?? [];

            foreach ($templates as $typeValue => $roles) {
                foreach ($roles as $roleValue => $templateData) {
                    $subject = $templateData['subject'] ?? null;
                    $body = $templateData['body'] ?? null;

                    $template = ServiceRequestCustomEmailTemplate::firstOrNew([
                        'type' => $typeValue,
                        'role' => $roleValue,
                    ]);

                    if (! RichContentDocument::hasContent($subject) && ! RichContentDocument::hasContent($body)) {
                        if ($template->exists) {
                            $template->delete();
                        }

                        continue;
                    }

                    $template->subject = $subject;
                    $template->body = $body;
                    $template->save();
                }
            }
        });

        $this->getSavedNotification()?->send();
    }

    /**
     * @return array<Action | ActionGroup>
     */
    public function getFormActions(): array
    {
        if (! (bool) auth()->user()?->can('settings.view-any')) {
            return [];
        }

        return parent::getFormActions();
    }

    public function preloadBaseTemplates()
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

    /**
     * @return array<Action | ActionGroup>
     */
    protected function getHeaderActions(): array
    {
        return [
            ApplyServiceRequestCustomTemplatesAction::make()
                ->visible(fn (): bool => (bool) ($this->data['use_custom_templates'] ?? false)),
        ];
    }

    protected function fillForm(): void
    {
        $settings = app(ServiceRequestNotificationAutomationSettings::class);

        $state = [
            'use_custom_templates' => $settings->use_custom_templates,
            'preload_new_service_request_types' => $settings->preload_new_service_request_types,
            'templates' => [],
        ];
        $state['templates'] = $this->getServiceRequestCustomEmailTemplate()['templates'] ?? [];

        $this->form->fill($state);
    }

    /**
     * @return array<string, array<string, array{subject: mixed, body: mixed}>>
     */
    protected function getServiceRequestCustomEmailTemplate(): array
    {
        $state = ['templates' => []];

        foreach (ServiceRequestCustomEmailTemplate::all() as $template) {
            $state['templates'][$template->type->value][$template->role->value] = [
                'subject' => $template->subject,
                'body' => $template->body,
            ];
        }

        return $state;
    }
}
