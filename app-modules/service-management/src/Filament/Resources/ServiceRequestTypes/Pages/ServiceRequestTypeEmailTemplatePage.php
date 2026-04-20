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

namespace AidingApp\ServiceManagement\Filament\Resources\ServiceRequestTypes\Pages;

use AidingApp\ServiceManagement\Enums\ServiceRequestEmailTemplateType;
use AidingApp\ServiceManagement\Enums\ServiceRequestTypeEmailTemplateRole;
use AidingApp\ServiceManagement\Filament\Blocks\ServiceRequestTypeEmailTemplateButtonBlock;
use AidingApp\ServiceManagement\Filament\Blocks\SurveyResponseEmailTemplateTakeSurveyButtonBlock;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestTypes\ServiceRequestTypeResource;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use AidingApp\ServiceManagement\Models\ServiceRequestTypeEmailTemplate;
use App\Concerns\EditPageRedirection;
use CanyonGBS\Common\Filament\Forms\RichContentPlugins\VideoRichContentPlugin;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\RichEditor\ToolbarButtonGroup;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use UnitEnum;

/** @property-read ?ServiceRequestTypeEmailTemplate $template */
class ServiceRequestTypeEmailTemplatePage extends EditRecord
{
    use EditPageRedirection;

    protected static string $resource = ServiceRequestTypeResource::class;

    #[Locked]
    public ServiceRequestEmailTemplateType $type;

    public static string | UnitEnum | null $navigationGroup = 'Email Templates';

    public function getRelationManagers(): array
    {
        // Needed to prevent Filament from loading the relation managers on this page.
        return [];
    }

    public function form(Schema $schema): Schema
    {
        $roles = ServiceRequestTypeEmailTemplateRole::cases();

        if ($this->type === ServiceRequestEmailTemplateType::SurveyResponse) {
            $roles = [ServiceRequestTypeEmailTemplateRole::Customer];
        }

        /** @var ServiceRequestType $record */
        $record = $this->getRecord();

        return $schema
            ->components([
                Tabs::make('Email template roles')
                    ->persistTab()
                    ->id('email-template-role-tabs')
                    ->tabs(array_map(
                        function (ServiceRequestTypeEmailTemplateRole $role) use ($record): Tab {
                            $template = ServiceRequestTypeEmailTemplate::where([
                                'service_request_type_id' => $record->getKey(),
                                'type' => $this->type,
                                'role' => $role,
                            ])->first();

                            return Tab::make($role->getLabel())
                                ->schema($this->getEmailTemplateFormSchema($role))
                                ->statePath($role->value)
                                ->model($template ?? ServiceRequestTypeEmailTemplate::class);
                        },
                        $roles
                    ))
                    ->columnSpanFull(),
            ]);
    }

    public function save(bool $shouldRedirect = true, bool $shouldSendSavedNotification = true): void
    {
        $data = $this->form->getState();
        $rawData = $this->form->getRawState();

        /** @var ServiceRequestType $record */
        $record = $this->getRecord();

        foreach (ServiceRequestTypeEmailTemplateRole::cases() as $role) {
            $templateData = $data[$role->value] ?? null;
            $rawTemplateData = $rawData[$role->value] ?? null;

            $subject = $templateData['subject'] ?? null;
            $body = $templateData['body'] ?? $this->normalizeRichContent($rawTemplateData['body'] ?? null);

            $template = ServiceRequestTypeEmailTemplate::firstOrNew([
                'service_request_type_id' => $record->getKey(),
                'type' => $this->type,
                'role' => $role,
            ]);

            if (blank($subject) && blank($body)) {
                if ($template->exists) {
                    $template->delete();
                }

                continue;
            }

            if (! $template->exists && blank($subject)) {
                continue;
            }

            $wasNew = ! $template->exists;

            $template->subject = $subject;
            $template->body = $body;

            $template->save();

            if ($wasNew) {
                $this->form->getComponent("email-template-body-{$role->value}")
                    ?->model($template)
                    ->saveRelationships();
            }
        }

        $this->getSavedNotification()->send();
    }

    /**
     * Returns null when the given rich content is an empty document (no text, images, or custom blocks).
     *
     * @param mixed $content
     *
     * @return mixed
     */
    protected function normalizeRichContent($content)
    {
        if (! is_array($content)) {
            return $content;
        }

        $hasContent = false;

        $walk = function (array $node) use (&$walk, &$hasContent): void {
            if (! empty($node['text'])) {
                $hasContent = true;

                return;
            }

            if (in_array($node['type'] ?? null, ['image', 'customBlock', 'videoEmbed', 'horizontalRule', 'hardBreak'], true)) {
                $hasContent = true;

                return;
            }

            foreach ($node['content'] ?? [] as $child) {
                if (is_array($child)) {
                    $walk($child);
                }
            }
        };

        $walk($content);

        return $hasContent ? $content : null;
    }

    /** @return array<int, RichEditor> */
    protected function getEmailTemplateFormSchema(ServiceRequestTypeEmailTemplateRole $role): array
    {
        $mergeTags = array_keys(ServiceRequestTypeEmailTemplate::getMergeTags());

        if ($this->type !== ServiceRequestEmailTemplateType::Update) {
            $mergeTags = array_values(array_diff($mergeTags, ['recent update']));
        }

        $normalizeEmptyContent = fn ($state) => $this->normalizeRichContent($state);

        return [
            RichEditor::make('subject')
                ->label('Subject')
                ->placeholder('Enter the email subject here...')
                ->extraInputAttributes(['style' => 'min-height: 2rem; overflow-y:none;'])
                ->toolbarButtons([])
                ->mergeTags($mergeTags)
                ->helperText('You may use “merge tags” to substitute information about a service request into your subject line. Insert a “{{“ in the subject line field to see a list of available merge tags')
                ->dehydrateStateUsing($normalizeEmptyContent)
                ->json(),
            RichEditor::make('body')
                ->key("email-template-body-{$role->value}")
                ->label('Body')
                ->placeholder('Enter the email body here...')
                ->extraInputAttributes(['style' => 'min-height: 12rem;'])
                ->toolbarButtons([
                    ['bold', 'italic', 'underline', 'strike', 'superscript', 'subscript', 'link'],
                    [ToolbarButtonGroup::make('Heading', ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'])->textualButtons(), 'blockquote', 'code', 'codeBlock', 'bulletList', 'orderedList', 'horizontalRule'],
                    ['alignStart', 'alignCenter', 'alignEnd'],
                    ['textColor', 'highlight', 'lead', 'small'],
                    ['attachFiles', 'video'],
                    ['grid', 'table', 'details'],
                    ['mergeTags', 'customBlocks'],
                    ['clearFormatting'],
                    ['undo', 'redo'],
                ])
                ->mergeTags($mergeTags)
                ->activePanel('mergeTags')
                ->customBlocks([
                    ServiceRequestTypeEmailTemplateButtonBlock::class,
                    SurveyResponseEmailTemplateTakeSurveyButtonBlock::class,
                ])
                ->plugins([
                    VideoRichContentPlugin::make(),
                ])
                ->fileAttachmentsDisk('s3-public')
                ->resizableImages()
                ->columnSpanFull()
                ->dehydrateStateUsing($normalizeEmptyContent)
                ->json(),
        ];
    }

    protected function fillForm(): void
    {
        /** @var ServiceRequestType $record */
        $record = $this->getRecord();

        /** @var Collection<int, ServiceRequestTypeEmailTemplate> $templates */
        $templates = $record
            ->templates()
            ->where('type', $this->type)
            ->get();

        /** @var Collection<string, ServiceRequestTypeEmailTemplate> $templates */
        $templates = $templates->keyBy(fn (ServiceRequestTypeEmailTemplate $template) => $template->role->value);

        $state = [];

        foreach (ServiceRequestTypeEmailTemplateRole::cases() as $role) {
            if ($template = $templates[$role->value] ?? null) {
                $state[$role->value] = $template->only(['subject', 'body']);
            }
        }

        $this->form->fill($state);
    }

    #[Computed]
    protected function template(): ?ServiceRequestTypeEmailTemplate
    {
        /** @var ServiceRequestType $record */
        $record = $this->getRecord();

        return $record->templates()->where('type', $this->type)->first();
    }
}
