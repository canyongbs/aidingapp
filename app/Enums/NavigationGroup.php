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
      of the licensor in the software. Any use of the licensor's trademarks is subject
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

namespace App\Enums;

use BackedEnum;
use Filament\Support\Contracts\Collapsible;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;

enum NavigationGroup implements HasLabel, HasIcon, Collapsible
{
    case LiveChat;

    case Clients;

    case Projects;

    case ServiceDesk;

    case Purchasing;

    case Analytics;

    case EngagementFeatures;

    case PremiumFeatures;

    case Users;

    case Settings;

    case GlobalAdmin;

    public function getLabel(): string | Htmlable | null
    {
        return match ($this) {
            self::LiveChat => 'Live Chat',
            self::Clients => 'Clients',
            self::Projects => 'Projects',
            self::ServiceDesk => 'Service Desk',
            self::Purchasing => 'Purchasing',
            self::Analytics => 'Analytics',
            self::EngagementFeatures => 'Engagement Features',
            self::PremiumFeatures => 'Premium Features',
            self::Users => 'Users',
            self::Settings => 'Settings',
            self::GlobalAdmin => 'Global Admin',
        };
    }

    public function getIcon(): string | BackedEnum | Htmlable | null
    {
        return match ($this) {
            self::LiveChat => 'heroicon-o-chat-bubble-left-right',
            self::Clients => 'heroicon-o-building-office-2',
            self::Projects => 'heroicon-o-folder',
            self::ServiceDesk => 'heroicon-o-lifebuoy',
            self::Purchasing => 'heroicon-o-document-text',
            self::Analytics => 'heroicon-o-chart-bar-square',
            self::EngagementFeatures => 'heroicon-o-signal',
            self::PremiumFeatures => 'heroicon-o-rocket-launch',
            self::Users => 'heroicon-o-user-group',
            self::Settings => 'heroicon-o-adjustments-vertical',
            self::GlobalAdmin => 'heroicon-o-shield-check',
        };
    }

    public function isCollapsible(): bool
    {
        return true;
    }

    public function isCollapsed(): bool
    {
        return true;
    }
}
