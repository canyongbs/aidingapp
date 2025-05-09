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

namespace App\Enums;

use App\Settings\LicenseSettings;
use Illuminate\Support\Facades\Gate;

enum Feature: string
{
    case OnlineForms = 'online-forms';

    case ServiceManagement = 'service-management';

    case KnowledgeManagement = 'knowledge-management';

    case RealtimeChat = 'realtime-chat';

    case MobileApps = 'mobile-apps';

    case ChangeManagement = 'change-management';

    case AssetManagement = 'asset-management';

    case FeedbackManagement = 'feedback-management';

    case ExperimentalReporting = 'experimental-reporting';

    public function generateGate(): void
    {
        // If features are added that are not based on a License Addon we will need to update this
        Gate::define(
            $this->getGateName(),
            fn () => app(LicenseSettings::class)->data->addons->{str($this->value)->camel()}
        );
    }

    public function getGateName(): string
    {
        return "feature-{$this->value}";
    }
}
