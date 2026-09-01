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

namespace AidingApp\Report\Filament\Pages;

use AidingApp\Report\Enums\ReportAccessKey;
use AidingApp\ServiceManagement\Enums\ServiceRequestEmailTemplateType;
use AidingApp\ServiceManagement\Enums\ServiceRequestNotificationChannel;
use AidingApp\ServiceManagement\Enums\ServiceRequestTypeEmailTemplateRole;
use AidingApp\ServiceManagement\Filament\Blocks\ServiceRequestTypeEmailTemplateButtonBlock;
use AidingApp\ServiceManagement\Filament\Blocks\SurveyResponseEmailTemplateTakeSurveyButtonBlock;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use AidingApp\ServiceManagement\Models\ServiceRequestTypeEmailPreference;
use AidingApp\ServiceManagement\Models\ServiceRequestTypeEmailTemplate;
use App\Enums\Feature;
use App\Enums\ReportLibraryNavigationGroup;
use App\Filament\Clusters\ReportLibrary;
use App\Models\User;
use BackedEnum;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\RichEditor\ToolbarButtonGroup;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\ViewField;
use Filament\Pages\Dashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Gate;
use LogicException;
use UnitEnum;

/**
 * @property-read Schema $emailTemplatesForm
 * @property-read Schema $notificationForm
 */
class RequestCommunications extends Dashboard
{
    use HasFiltersForm;

    /** @var array<string, array<string, array{subject: array<string, mixed>|null, body: array<string, mixed>|null}>> */
    public array $emailTemplates = [];

    /** @var array<string, bool> */
    public array $notificationSettings = [];

    protected static ?string $cluster = ReportLibrary::class;

    protected static string | UnitEnum | null $navigationGroup = ReportLibraryNavigationGroup::ServiceDesk;

    protected static ?string $navigationLabel = 'Request Communications';

    protected static ?string $title = 'Request Communications';

    protected static string $routePath = 'request-communications';

    protected static ?int $navigationSort = 20;

    protected static string | BackedEnum | null $navigationIcon = '';

    protected string $view = 'report::filament.pages.request-communications';

    public static function canAccess(): bool
    {
        if (! Gate::check(Feature::ServiceManagement->getGateName())) {
            return false;
        }

        /** @var User $user */
        $user = auth()->user();

        return ReportAccessKey::fromPageClass(static::class)?->userCanAccess($user) ?? false;
    }

    public function booted(): void
    {
        $this->refreshEmailTemplates();
        $this->refreshNotifications();
    }

    public function filtersForm(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Service Request Type')
                ->schema([
                    Select::make('serviceRequestType')
                        ->label('Service Request Type')
                        ->options(fn (): array => ServiceRequestType::query()
                            ->withoutArchived()
                            ->orderBy('name')
                            ->pluck('name', 'id')
                            ->all())
                        ->searchable()
                        ->live()
                        ->afterStateUpdated(function (): void {
                            $this->refreshEmailTemplates();
                            $this->refreshNotifications();
                        })
                        ->placeholder('Select a service request type'),
                ])
                ->columns(1)
                ->columnSpanFull(),
        ]);
    }

    public function notificationForm(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Notifications and Alerts')
                    ->schema([
                        ViewField::make('settings')
                            ->statePath('notificationSettings')
                            ->rules(['array'])
                            ->disabled(true)
                            ->view('service-management::filament.resources.service-request-type-resource.pages.edit-service-request-type-notifications.matrix'),
                    ])
                    ->extraAttributes(['class' => 'fi-section-no-content-padding']),
            ]);
    }

    public function emailTemplatesForm(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Email Templates')
                ->schema([
                    Tabs::make('Email template types')
                        ->id('email-template-type-tabs')
                        ->tabs(array_map(
                            fn (ServiceRequestEmailTemplateType $type): Tab => Tab::make($type->getLabel())
                                ->schema([
                                    Tabs::make('Email template roles')
                                        ->id("email-template-role-tabs-{$type->value}")
                                        ->tabs(array_map(
                                            fn (ServiceRequestTypeEmailTemplateRole $role): Tab => Tab::make($role->getLabel())
                                                ->schema($this->getEmailTemplateSchema($type, $role))
                                                ->statePath("emailTemplates.{$type->value}.{$role->value}"),
                                            $type === ServiceRequestEmailTemplateType::SurveyResponse
                                                ? [ServiceRequestTypeEmailTemplateRole::Customer]
                                                : ServiceRequestTypeEmailTemplateRole::cases(),
                                        ))
                                        ->columnSpanFull(),
                                ]),
                            ServiceRequestEmailTemplateType::cases(),
                        ))
                        ->columnSpanFull(),
                ])
                ->visible(fn (): bool => filled($this->filters['serviceRequestType'] ?? null)),
        ]);
    }

    public function getSelectedServiceRequestType(): ?ServiceRequestType
    {
        $serviceRequestTypeId = $this->filters['serviceRequestType'] ?? null;

        if (blank($serviceRequestTypeId)) {
            return null;
        }

        return ServiceRequestType::query()
            ->withoutArchived()
            ->with(['emailPreferences', 'templates'])
            ->find($serviceRequestTypeId);
    }

    /**
     * @return array<int, RichEditor>
     */
    protected function getEmailTemplateSchema(
        ServiceRequestEmailTemplateType $type,
        ServiceRequestTypeEmailTemplateRole $role,
    ): array {
        $serviceRequestTypeId = $this->getSelectedServiceRequestType()?->getKey() ?? 'none';

        return [
            RichEditor::make('subject')
                ->key("email-template-subject-{$type->value}-{$role->value}-{$serviceRequestTypeId}")
                ->label('Subject')
                ->placeholder('No email template has been configured for this event and recipient.')
                ->toolbarButtons([])
                ->disabled()
                ->json()
                ->columnSpanFull(),
            RichEditor::make('body')
                ->key("email-template-body-{$type->value}-{$role->value}-{$serviceRequestTypeId}")
                ->label('Body')
                ->placeholder('No email template has been configured for this event and recipient.')
                ->toolbarButtons([
                    ['bold', 'italic', 'link'],
                    [ToolbarButtonGroup::make('Heading', ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'])->textualButtons(), 'bulletList', 'orderedList', 'horizontalRule'],
                    ['textColor', 'small'],
                    ['attachFiles', 'mergeTags', 'customBlocks'],
                    ['clearFormatting'],
                    ['undo', 'redo'],
                ])
                ->mergeTags(ServiceRequestTypeEmailTemplate::getMergeTags())
                ->customBlocks([
                    ServiceRequestTypeEmailTemplateButtonBlock::class,
                    SurveyResponseEmailTemplateTakeSurveyButtonBlock::class,
                ])
                ->fileAttachmentsDisk('s3-public')
                ->resizableImages()
                ->disabled()
                ->json()
                ->columnSpanFull(),
        ];
    }

    /**
     * @return array<string, array<string, array{subject: array<string, mixed>|null, body: array<string, mixed>|null}>>
     */
    protected function getEmailTemplatesState(): array
    {
        $serviceRequestType = $this->getSelectedServiceRequestType();

        if ($serviceRequestType === null) {
            return [];
        }

        $templates = [];
        $templatesByTypeAndRole = $serviceRequestType->templates->keyBy(
            fn (ServiceRequestTypeEmailTemplate $template): string => "{$template->type->value}.{$template->role->value}",
        );

        foreach (ServiceRequestEmailTemplateType::cases() as $type) {
            $roles = $type === ServiceRequestEmailTemplateType::SurveyResponse
                ? [ServiceRequestTypeEmailTemplateRole::Customer]
                : ServiceRequestTypeEmailTemplateRole::cases();

            foreach ($roles as $role) {
                $template = $templatesByTypeAndRole->get("{$type->value}.{$role->value}");

                $templates[$type->value][$role->value] = [
                    'subject' => $template?->subject,
                    'body' => $template?->body,
                ];
            }
        }

        return $templates;
    }

    protected function refreshEmailTemplates(): void
    {
        $this->getEmailTemplatesForm()->fill([
            'emailTemplates' => $this->getEmailTemplatesState(),
        ]);
    }

    protected function getEmailTemplatesForm(): Schema
    {
        $form = $this->getSchema('emailTemplatesForm');

        if (! $form instanceof Schema) {
            throw new LogicException(static::class . ' expected the [emailTemplatesForm] schema to be registered.');
        }

        return $form;
    }

    protected function getEmailTemplate(
        ServiceRequestEmailTemplateType $type,
        ServiceRequestTypeEmailTemplateRole $role,
    ): ?ServiceRequestTypeEmailTemplate {
        return $this->getSelectedServiceRequestType()
            ?->templates
            ->first(
                fn (ServiceRequestTypeEmailTemplate $template): bool => $template->type === $type
                    && $template->role === $role,
            );
    }

    /**
     * @return array<int, string>
     */
    protected function getForms(): array
    {
        return [
            'filtersForm',
            'emailTemplatesForm',
            'notificationForm',
        ];
    }

    /**
     * @return array<string, bool>
     */
    protected function getNotificationState(): array
    {
        $serviceRequestType = $this->getSelectedServiceRequestType();

        if ($serviceRequestType === null) {
            return [];
        }

        $preferences = $serviceRequestType->emailPreferences()->get();

        $settings = [];

        foreach (ServiceRequestEmailTemplateType::cases() as $templateType) {
            foreach (ServiceRequestTypeEmailTemplateRole::cases() as $templateRole) {
                $eventSlug = $templateType->getEventSlug();
                $roleSlug = $templateRole->value . 's';

                foreach (ServiceRequestNotificationChannel::cases() as $channel) {
                    $preference = $preferences->first(
                        fn (ServiceRequestTypeEmailPreference $preference): bool => $preference->service_request_email_template_type === $templateType
                            && $preference->service_request_email_template_role === $templateRole
                            && $preference->notification_channel === $channel,
                    );

                    if ($preference !== null) {
                        $settings["is_{$roleSlug}_{$eventSlug}_{$channel->value}_enabled"] = $preference->is_enabled;
                    }
                }
            }
        }

        return $settings;
    }

    protected function refreshNotifications(): void
    {
        $this->getNotificationForm()->fill([
            'notificationSettings' => $this->getNotificationState(),
        ]);
    }

    protected function getNotificationForm(): Schema
    {
        $form = $this->getSchema('notificationForm');

        if (! $form instanceof Schema) {
            throw new LogicException(static::class . ' expected the [notificationForm] schema to be registered.');
        }

        return $form;
    }
}
