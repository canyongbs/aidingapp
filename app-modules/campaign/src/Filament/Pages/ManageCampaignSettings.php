<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\Campaign\Filament\Pages;

use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use AdvisingApp\Campaign\Settings\CampaignSettings;
use Tapp\FilamentTimezoneField\Forms\Components\TimezoneSelect;

class ManageCampaignSettings extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'Campaign Settings';

    protected static ?int $navigationSort = 140;

    protected static bool $shouldRegisterNavigation = false;

    protected static string $settings = CampaignSettings::class;

    protected static ?string $title = 'Campaign Settings';

    public function mount(): void
    {
        $this->authorize('campaign.view_campaign_settings');

        parent::mount();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TimezoneSelect::make('action_execution_timezone')
                    ->label('Journey step execution timezone')
                    ->placeholder(fn (TimezoneSelect $component): string => $component->getOptions()[config('app.timezone')]),
            ]);
    }
}
