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

namespace AidingApp\KnowledgeBase\Filament\Pages;

use AidingApp\KnowledgeBase\Enums\KnowledgeBaseCategoryTabOrder;
use AidingApp\KnowledgeBase\Settings\KnowledgeBasePortalSettings;
use App\Enums\Feature;
use App\Filament\Clusters\KnowledgeManagement;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\Select;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Gate;

class ManageKnowledgeBasePortalSettings extends SettingsPage
{
    protected static ?string $navigationLabel = 'Portal';

    protected static ?int $navigationSort = 50;

    protected static string $settings = KnowledgeBasePortalSettings::class;

    protected static ?string $title = 'Portal';

    protected static ?string $cluster = KnowledgeManagement::class;

    public static function canAccess(): bool
    {
        if (! Gate::check(Feature::KnowledgeManagement->getGateName())) {
            return false;
        }

        /** @var User $user */
        $user = auth()->user();

        return $user->can(['settings.view-any']);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Configuration Options')
                    ->schema([
                        Select::make('category_tab_order')
                            ->label('Category Tabs')
                            ->aboveContent('When customers browse a category or subcategory in the self-service portal, tabs organize the available articles. Use the options below to choose the order in which these tabs appear. The first tab in the selected order will be displayed by default when the page loads.')
                            ->options(KnowledgeBaseCategoryTabOrder::class)
                            ->enum(KnowledgeBaseCategoryTabOrder::class)
                            ->required(),
                    ]),
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
     * @return array<Action|ActionGroup>
     */
    public function getFormActions(): array
    {
        if (! auth()->user()->can('settings.*.update')) {
            return [];
        }

        return parent::getFormActions();
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
