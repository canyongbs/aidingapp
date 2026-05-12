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

namespace App\Filament\Pages;

use App\Features\UserPresence;
use App\Filament\Clusters\DisplaySettings as DisplaySettingsCluster;
use App\Models\User;
use App\Settings\PresenceSettings;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\TextInput;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class ManagePresenceSettings extends SettingsPage
{
    protected static ?string $navigationLabel = 'Presence';

    protected static ?int $navigationSort = 30;

    protected static string $settings = PresenceSettings::class;

    protected static ?string $cluster = DisplaySettingsCluster::class;

    public static function canAccess(): bool
    {
        if (! UserPresence::active()) {
            return false;
        }

        /** @var User $user */
        $user = auth()->user();

        return $user->can('settings.view-any');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('active_threshold')
                    ->label('Active threshold')
                    ->helperText('Minutes of inactivity before a user is considered idle.')
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(fn (Get $get) => ($get('idle_threshold') ?? 2) - 1)
                    ->required()
                    ->live(onBlur: true)
                    ->suffix('minutes'),
                TextInput::make('idle_threshold')
                    ->label('Idle threshold')
                    ->helperText('Minutes of inactivity before a user is considered inactive.')
                    ->numeric()
                    ->minValue(fn (Get $get) => ($get('active_threshold') ?? 0) + 1)
                    ->maxValue(fn (Get $get) => ($get('inactive_threshold') ?? 2) - 1)
                    ->required()
                    ->live(onBlur: true)
                    ->suffix('minutes'),
                TextInput::make('inactive_threshold')
                    ->label('Inactive threshold')
                    ->helperText('Minutes of inactivity before a user is considered offline.')
                    ->numeric()
                    ->minValue(fn (Get $get) => ($get('idle_threshold') ?? 0) + 1)
                    ->required()
                    ->live(onBlur: true)
                    ->suffix('minutes'),
            ])
            ->disabled(! auth()->user()->can('settings.*.update'));
    }

    public function save(): void
    {
        if (! auth()->user()->can('settings.*.update')) {
            return;
        }

        parent::save();
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
