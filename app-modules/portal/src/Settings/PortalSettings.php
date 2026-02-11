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

namespace AidingApp\Portal\Settings;

use AidingApp\Form\Enums\Rounding;
use AidingApp\Portal\Enums\GdprBannerButtonLabel;
use AidingApp\Portal\Enums\GdprDeclineOptions;
use AidingApp\Portal\Settings\SettingsProperties\PortalSettingsProperty;
use App\Settings\SettingsWithMedia;
use CanyonGBS\Common\Enums\Color;

class PortalSettings extends SettingsWithMedia
{
    public null $logo = null;

    public null $favicon = null;

    /**
    * Knowledge Base Portal
    */
    public bool $knowledge_management_portal_enabled = false;

    public bool $knowledge_management_portal_service_management = false;

    public bool $knowledge_management_portal_requires_authentication = false;

    public bool $ai_support_assistant = false;

    public bool $ai_assistant_service_requests = false;

    public bool $embed_assistant = false;

    public array $embed_assistant_allowed_domains = [];

    public ?Color $knowledge_management_portal_primary_color = null;

    public ?Rounding $knowledge_management_portal_rounding = null;

    public string|array $gdpr_banner_text = "We use cookies to personalize content, to provide social media features, and to analyze our traffic. We also share information about your use of our site with our partners who may combine it with other information that you've provided to them or that they've collected from your use of their services.";

    public GdprBannerButtonLabel $gdpr_banner_button_label = GdprBannerButtonLabel::AllowCookies;

    public string $page_title = 'Help Center';

    public bool $gdpr_privacy_policy = false;

    public ?string $gdpr_privacy_policy_url = null;

    public bool $gdpr_terms_of_use = false;

    public ?string $gdpr_terms_of_use_url = null;

    public bool $gdpr_decline = false;

    public GdprDeclineOptions $gdpr_decline_value = GdprDeclineOptions::Decline;

    public static function getSettingsPropertyModelClass(): string
    {
        return PortalSettingsProperty::class;
    }

    public static function group(): string
    {
        return 'portal';
    }
}
