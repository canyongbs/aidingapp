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

namespace App\Filament\Clusters\GlobalServiceManagementCluster\Pages;

use AidingApp\ServiceManagement\Enums\ServiceRequestEmailTemplateType;
use AidingApp\ServiceManagement\Enums\ServiceRequestTypeEmailTemplateRole;
use AidingApp\ServiceManagement\Filament\Blocks\ServiceRequestTypeEmailTemplateButtonBlock;
use AidingApp\ServiceManagement\Filament\Blocks\SurveyResponseEmailTemplateTakeSurveyButtonBlock;
use AidingApp\ServiceManagement\Models\ServiceRequestNotificationAutomationEmailTemplate;
use AidingApp\ServiceManagement\Models\ServiceRequestTypeEmailTemplate;
use AidingApp\ServiceManagement\Settings\ServiceRequestNotificationAutomationSettings;
use App\Enums\Feature;
use App\Filament\Clusters\GlobalServiceManagementCluster;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\RichEditor\ToolbarButtonGroup;
use Filament\Forms\Components\Textarea;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class ManageServiceRequestBaseTemplates extends SettingsPage
{
    protected static string $settings = ServiceRequestNotificationAutomationSettings::class;

    protected static ?string $cluster = GlobalServiceManagementCluster::class;

    protected static ?string $title = 'Base Templates';

    public static function canAccess(): bool
    {
        $user = auth()->user();
        assert($user instanceof User);

        return Gate::check(Feature::ServiceManagement->getGateName()) && $user->isSuperAdmin();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Event templates')
                    ->persistTab()
                    ->id('event-template-tabs')
                    ->tabs(array_map(
                        fn (ServiceRequestEmailTemplateType $type) => Tab::make($type->getLabel())
                            ->schema([
                                Tabs::make('Role templates')
                                    ->persistTab()
                                    ->id("role-template-tabs-{$type->value}")
                                    ->tabs(array_map(
                                        fn (ServiceRequestTypeEmailTemplateRole $role) => Tab::make($role->getLabel())
                                            ->schema($this->getTemplateFormSchema($type, $role))
                                            ->statePath("templates.{$type->value}.{$role->value}"),
                                        $type === ServiceRequestEmailTemplateType::SurveyResponse
                                            ? [ServiceRequestTypeEmailTemplateRole::Customer]
                                            : ServiceRequestTypeEmailTemplateRole::cases()
                                    ))
                                    ->columnSpanFull(),
                            ]),
                        ServiceRequestEmailTemplateType::cases()
                    ))
                    ->columnSpanFull(),
            ]);
    }

    public function save(): void
    {
        $state = $this->form->getState();

        DB::transaction(function () use ($state): void {
            $templates = $state['templates'] ?? [];

            foreach ($templates as $typeValue => $roles) {
                foreach ($roles as $roleValue => $templateData) {
                    ServiceRequestNotificationAutomationEmailTemplate::updateOrCreate(
                        [
                            'type' => $typeValue,
                            'role' => $roleValue,
                        ],
                        [
                            'subject' => $templateData['subject'] ?? null,
                            'body' => $templateData['body'] ?? null,
                        ],
                    );
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
        if (! auth()->user()->isSuperAdmin()) {
            return [];
        }

        return parent::getFormActions();
    }

    protected function fillForm(): void
    {
        $state = [
            'templates' => [],
        ];

        $templates = ServiceRequestNotificationAutomationEmailTemplate::all();

        foreach ($templates as $template) {
            $state['templates'][$template->type->value][$template->role->value] = [
                'subject' => $template->subject,
                'body' => $template->body,
            ];
        }

        $this->form->fill($state);
    }

    /**
     * @return array<int, RichEditor|Textarea>
     */
    protected function getTemplateFormSchema(ServiceRequestEmailTemplateType $type, ServiceRequestTypeEmailTemplateRole $role): array
    {
        $mergeTags = ServiceRequestTypeEmailTemplate::getMergeTags();

        if ($type !== ServiceRequestEmailTemplateType::Update) {
            unset($mergeTags['recent update']);
        }

        $hasAnyContent = function (Get $get): bool {
            return $this->richEditorHasContent($get('subject'))
                || $this->richEditorHasContent($get('body'));
        };

        return [
            RichEditor::make('subject')
                ->label('Example Subject')
                ->placeholder('Enter the example email subject here...')
                ->extraInputAttributes(['style' => 'min-height: 2rem; overflow-y:none;'])
                ->toolbarButtons([])
                ->mergeTags($mergeTags)
                ->helperText('The example subject template the AI will customize for each service request type.')
                ->required(fn (Get $get): bool => $hasAnyContent($get))
                ->live(onBlur: true)
                ->json(),
            RichEditor::make('body')
                ->label('Example Body')
                ->placeholder('Enter the example email body here...')
                ->extraInputAttributes(['style' => 'min-height: 12rem;'])
                ->toolbarButtons([
                    ['bold', 'italic', 'link'],
                    [ToolbarButtonGroup::make('Heading', ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'])->textualButtons(), 'bulletList', 'orderedList', 'horizontalRule'],
                    ['textColor', 'small'],
                    ['mergeTags', 'customBlocks'],
                    ['clearFormatting'],
                    ['undo', 'redo'],
                ])
                ->mergeTags($mergeTags)
                ->customBlocks([
                    ServiceRequestTypeEmailTemplateButtonBlock::class,
                    SurveyResponseEmailTemplateTakeSurveyButtonBlock::class,
                ])
                ->columnSpanFull()
                ->required(fn (Get $get): bool => $hasAnyContent($get))
                ->live(onBlur: true)
                ->json(),
        ];
    }

    protected function richEditorHasContent(mixed $value): bool
    {
        if (! is_array($value)) {
            return false;
        }

        $isEmpty = (($value['type'] ?? null) === 'doc')
            && (count($value['content'] ?? []) === 1)
            && (($value['content'][0]['type'] ?? null) === 'paragraph')
            && blank($value['content'][0]['content'] ?? []);

        return ! $isEmpty;
    }
}
