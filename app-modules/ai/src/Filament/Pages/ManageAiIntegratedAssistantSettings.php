<?php

namespace AidingApp\Ai\Filament\Pages;

use AidingApp\Ai\Enums\AiModel;
use AidingApp\Ai\Enums\AiModelApplicabilityFeature;
use AidingApp\Ai\Models\AiAssistant;
use AidingApp\Ai\Settings\AiIntegratedAssistantSettings;
use App\Filament\Clusters\GlobalArtificialIntelligence;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use Illuminate\Validation\Rule;

/**
 * @property-read ?AiAssistant $defaultAssistant
 */
class ManageAiIntegratedAssistantSettings extends SettingsPage
{
    protected static string $settings = AiIntegratedAssistantSettings::class;

    protected static ?string $title = 'Integrated Advisor';

    protected static ?string $cluster = GlobalArtificialIntelligence::class;

    protected static ?int $navigationSort = 10;

    public static function canAccess(): bool
    {
        /** @var User $user */
        $user = auth()->user();

        return $user->isSuperAdmin();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('default_model')
                    ->options(fn (AiModel|string|null $state) => array_unique([
                        ...AiModelApplicabilityFeature::IntegratedAdvisor->getModelsAsSelectOptions(),
                        ...match (true) {
                            $state instanceof AiModel => [$state->value => $state->getLabel()],
                            is_string($state) => [$state => AiModel::parse($state)->getLabel()],
                            default => [],
                        },
                    ]))
                    ->rule(Rule::enum(AiModel::class)->only(AiModelApplicabilityFeature::IntegratedAdvisor->getModels()))
                    ->searchable()
                    ->helperText('Used for general purposes like generating content when an assistant is not being used.')
                    ->required(),
            ])
            ->disabled(! auth()->user()->isSuperAdmin());
    }

    public function save(): void
    {
        if (! auth()->user()->isSuperAdmin()) {
            return;
        }

        parent::save();
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

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (filled($data['default_model'] ?? null)) {
            $data['default_model'] = AiModel::parse($data['default_model']);
        }

        return parent::mutateFormDataBeforeSave($data);
    }
}
