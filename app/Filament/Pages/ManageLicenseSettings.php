<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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

namespace App\Filament\Pages;

use App\DataTransferObjects\LicenseManagement\LicenseData;
use App\Models\User;
use App\Settings\LicenseSettings;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;

class ManageLicenseSettings extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-key';

    protected static ?string $navigationLabel = 'Subscription';

    protected static ?int $navigationSort = 10;

    protected static string $settings = LicenseSettings::class;

    protected static ?string $navigationGroup = 'Global Administration';

    public static function canAccess(): bool
    {
        /** @var User $user */
        $user = auth()->user();

        return $user->isSuperAdmin() && parent::canAccess();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('license_key')
                    ->label('License Key')
                    ->required()
                    ->disabled()
                    ->dehydrated(),
                Section::make('Subscription Information')
                    ->columns()
                    ->schema(
                        [
                            TextInput::make('data.subscription.clientName')
                                ->label('Client Name')
                                ->required(),
                            TextInput::make('data.subscription.partnerName')
                                ->label('Partner Name')
                                ->required(),
                            TextInput::make('data.subscription.clientPo')
                                ->label('Client PO')
                                ->required(),
                            TextInput::make('data.subscription.partnerPo')
                                ->label('Partner PO')
                                ->required(),
                            DatePicker::make('data.subscription.startDate')
                                ->label('Start Date')
                                ->required()
                                ->format('Y-m-d\TH:i:sP'),
                            DatePicker::make('data.subscription.endDate')
                                ->label('End Date')
                                ->required()
                                ->format('Y-m-d\TH:i:sP'),
                        ]
                    ),
                Section::make('Limits Configuration')
                    ->columns()
                    ->schema(
                        [
                            TextInput::make('data.limits.recruitmentCrmSeats')
                                ->label('Recruitment CRM Seats')
                                ->numeric()
                                ->required(),
                            TextInput::make('data.limits.emails')
                                ->label('Emails')
                                ->numeric()
                                ->required(),
                            TextInput::make('data.limits.sms')
                                ->label('SMS')
                                ->numeric()
                                ->required(),
                            TextInput::make('data.limits.resetDate')
                                ->label('Reset Date')
                                ->required(),
                        ]
                    ),
                Section::make('Enabled Features')
                    ->columns()
                    ->schema(
                        [
                            Toggle::make('data.addons.onlineForms')
                                ->label('Custom Forms'),
                            Toggle::make('data.addons.serviceManagement')
                                ->label('Service Management'),
                            Toggle::make('data.addons.knowledgeManagement')
                                ->label('Knowledge Management'),
                            Toggle::make('data.addons.realtimeChat')
                                ->label('Chat'),
                            Toggle::make('data.addons.mobileApps')
                                ->label('Mobile Apps'),
                            Toggle::make('data.addons.changeManagement')
                                ->label('Change Management'),
                            Toggle::make('data.addons.assetManagement')
                                ->label('Asset Management'),
                            Toggle::make('data.addons.feedbackManagement')
                                ->label('Feedback Management'),
                            Toggle::make('data.addons.experimentalReporting')
                                ->label('Experimental Reporting')
                                ->live()
                                ->afterStateUpdated(fn (Toggle $component, $state) => $state ? $component->state(false) && $this->mountAction('enableExperimentalReporting') : null),
                        ]
                    ),
            ]);
    }

    public function enableExperimentalReporting(): Action
    {
        return Action::make('enableExperimentalReporting')
            ->color('danger')
            ->requiresConfirmation()
            ->modalDescription('Experimental reporting is not currently a reliable method to explore your data, but is representative of a capability in research and development at Canyon GBS to innovatively extend AI to allow you to explore your data through natural language processing. This feature should be used for educational purposes only, and should not be be relied upon for decision making at your institution.')
            ->action(fn () => $this->data['data']['addons']['experimentalReporting'] = true);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return [
            'data' => LicenseData::from(
                [
                    'updatedAt' => now(),
                    ...$data['data'],
                ]
            ),
        ];
    }
}
