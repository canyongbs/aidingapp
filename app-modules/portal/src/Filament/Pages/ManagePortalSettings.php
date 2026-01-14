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

namespace AidingApp\Portal\Filament\Pages;

use AidingApp\Ai\Jobs\PrepareKnowledgeBaseVectorStore;
use AidingApp\Form\Enums\Rounding;
use AidingApp\Portal\Enums\GdprBannerButtonLabel;
use AidingApp\Portal\Enums\GdprDeclineOptions;
use AidingApp\Portal\Settings\PortalSettings;
use App\Enums\Feature;
use App\Features\PortalAssistantServiceRequestFeature;
use App\Models\User;
use App\Rules\ValidUrl;
use CanyonGBS\Common\Filament\Forms\Components\ColorSelect;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Pages\SettingsPage;
use FilamentTiptapEditor\TiptapEditor;
use Illuminate\Support\Facades\Gate;

class ManagePortalSettings extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'Client Portal';

    protected static ?int $navigationSort = 90;

    protected static string $settings = PortalSettings::class;

    protected static ?string $title = 'Client Portal';

    protected static ?string $navigationGroup = 'Settings';

    public static function canAccess(): bool
    {
        /** @var User $user */
        $user = auth()->user();

        return $user->can(['settings.view-any']);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Client Portal')
                    ->schema([
                        Toggle::make('knowledge_management_portal_enabled')
                            ->label('Knowledge Management')
                            ->disabled(! Gate::check(Feature::KnowledgeManagement->getGateName()))
                            ->hintIcon(fn (Toggle $component) => $component->isDisabled() ? 'heroicon-m-lock-closed' : null)
                            ->hintIconTooltip('Knowledge Management is not a part of your current subscription.')
                            ->live()
                            ->columnSpanFull(),
                        ColorSelect::make('knowledge_management_portal_primary_color')
                            ->label('Primary Color')
                            ->visible(fn (Get $get) => $get('knowledge_management_portal_enabled'))
                            ->disabled(! Gate::check(Feature::KnowledgeManagement->getGateName()))
                            ->hintIcon(fn (Select $component) => $component->isDisabled() ? 'heroicon-m-lock-closed' : null)
                            ->hintIconTooltip('Knowledge Management is not a part of your current subscription.')
                            ->columnSpan(1),
                        Select::make('knowledge_management_portal_rounding')
                            ->label('Rounding')
                            ->options(Rounding::class)
                            ->visible(fn (Get $get) => $get('knowledge_management_portal_enabled'))
                            ->disabled(! Gate::check(Feature::KnowledgeManagement->getGateName()))
                            ->hintIcon(fn (Select $component) => $component->isDisabled() ? 'heroicon-m-lock-closed' : null)
                            ->hintIconTooltip('Knowledge Management is not a part of your current subscription.')
                            ->columnSpan(1),
                        Toggle::make('knowledge_management_portal_requires_authentication')
                            ->label('Require Authentication')
                            ->visible(fn (Get $get) => $get('knowledge_management_portal_enabled'))
                            ->disabled(! Gate::check(Feature::ServiceManagement->getGateName()))
                            ->hintIcon(fn (Toggle $component) => $component->isDisabled() ? 'heroicon-m-lock-closed' : null)
                            ->columnSpanFull(),
                        Toggle::make('knowledge_management_portal_service_management')
                            ->label('Service Management')
                            ->visible(fn (Get $get) => $get('knowledge_management_portal_enabled'))
                            ->disabled(! Gate::check(Feature::ServiceManagement->getGateName()))
                            ->hintIcon(fn (Toggle $component) => $component->isDisabled() ? 'heroicon-m-lock-closed' : null)
                            ->columnSpanFull(),
                        Toggle::make('ai_support_assistant')
                            ->label('AI Support Assistant')
                            ->columnSpanFull(),
                        Toggle::make('ai_assistant_service_requests')
                            ->label('AI Assistant Service Requests')
                            ->helperText('Allow the AI Support Assistant to help users submit service requests.')
                            ->visible(fn (Get $get) => $get('ai_support_assistant'))
                            ->hidden(! PortalAssistantServiceRequestFeature::active())
                            ->columnSpanFull(),
                        Grid::make()->schema([
                            TextInput::make('page_title')
                                ->label('Page Title')
                                ->maxLength(255)
                                ->columns(1)
                                ->required()
                                ->visible(fn (Get $get) => $get('knowledge_management_portal_enabled'))
                                ->disabled(! Gate::check(Feature::KnowledgeManagement->getGateName()))
                                ->hintIcon(fn (TextInput $component) => $component->isDisabled() ? 'heroicon-m-lock-closed' : null),
                        ])->columns(2),
                        SpatieMediaLibraryFileUpload::make('favicon')
                            ->collection('portal_favicon')
                            ->visibility('private')
                            ->visible(fn (Get $get) => $get('knowledge_management_portal_enabled'))
                            ->acceptedFileTypes([
                                'image/png',
                                'image/jpeg',
                                'image/ico',
                                'image/webp',
                                'image/jpg',
                                'image/svg',
                            ])
                            ->model(
                                PortalSettings::getSettingsPropertyModel('portal.favicon'),
                            ),
                        SpatieMediaLibraryFileUpload::make('logo')
                            ->collection('logo')
                            ->visibility('private')
                            ->image()
                            ->model(
                                PortalSettings::getSettingsPropertyModel('portal.logo'),
                            )
                            ->visible(fn (Get $get) => $get('knowledge_management_portal_enabled')),
                        Actions::make([
                            Action::make('view')
                                ->url(fn () => route('portal.show'))
                                ->icon('heroicon-m-arrow-top-right-on-square')
                                ->disabled(! Gate::check(Feature::KnowledgeManagement->getGateName()))
                                ->openUrlInNewTab(),
                        ])
                            ->visible(
                                fn (Get $get) => $get('knowledge_management_portal_enabled') &&
                                    ! is_null($get('knowledge_management_portal_primary_color')) &&
                                    ! is_null($get('knowledge_management_portal_rounding'))
                            )
                            ->columnSpanFull(),
                    ])->columns(2),
                Section::make('GDPR Banner Notice')
                    ->schema([
                        TiptapEditor::make('gdpr_banner_text')
                            ->label('GDPR Banner Text')
                            ->required()
                            ->tools(['link'])
                            ->columnSpanFull(),
                        Toggle::make('gdpr_privacy_policy')
                            ->label('Privacy Policy')
                            ->live()
                            ->columnSpanFull(),
                        TextInput::make('gdpr_privacy_policy_url')
                            ->label('Privacy Policy URL')
                            ->visible(fn (Get $get) => $get('gdpr_privacy_policy'))
                            ->maxLength(255)
                            ->rules([new ValidUrl()]),
                        Toggle::make('gdpr_terms_of_use')
                            ->label('Terms of Use')
                            ->live()
                            ->columnSpanFull(),
                        TextInput::make('gdpr_terms_of_use_url')
                            ->label('Terms of Use URL')
                            ->visible(fn (Get $get) => $get('gdpr_terms_of_use'))
                            ->maxLength(255)
                            ->rules([new ValidUrl()]),
                        Select::make('gdpr_banner_button_label')
                            ->options(GdprBannerButtonLabel::class)
                            ->enum(GdprBannerButtonLabel::class)
                            ->required()
                            ->label('GDPR Button Label'),
                        Toggle::make('gdpr_decline')
                            ->label('GDPR Decline')
                            ->live()
                            ->columnSpanFull(),
                        Select::make('gdpr_decline_value')
                            ->options(GdprDeclineOptions::class)
                            ->enum(GdprDeclineOptions::class)
                            ->visible(fn (Get $get) => $get('gdpr_decline'))
                            ->label('GDPR Decline Option'),
                    ])
                    ->visible(fn (Get $get) => $get('knowledge_management_portal_enabled')),
            ])
            ->disabled(! auth()->user()->can('settings.*.update'));
    }

    public function save(): void
    {
        if (! auth()->user()->can('settings.*.update')) {
            return;
        }

        parent::save();

        if (app(PortalSettings::class)->ai_support_assistant) {
            PrepareKnowledgeBaseVectorStore::dispatch();
        }
    }

    /**
     * @return array<Action | ActionGroup>
     */
    public function getFormActions(): array
    {
        if (! auth()->user()->can('settings.*.update')) {
            return [];
        }

        return parent::getFormActions();
    }
}
