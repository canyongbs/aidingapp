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

namespace AidingApp\Contact\Providers;

use Filament\Panel;
use App\Concerns\ImplementsGraphQL;
use AidingApp\Contact\ContactPlugin;
use AidingApp\Contact\Models\Contact;
use Illuminate\Support\ServiceProvider;
use AidingApp\Contact\Models\Organization;
use AidingApp\Contact\Models\ContactSource;
use AidingApp\Contact\Models\ContactStatus;
use AidingApp\Contact\Models\OrganizationType;
use AidingApp\Contact\Observers\ContactObserver;
use AidingApp\Contact\Models\OrganizationIndustry;
use Illuminate\Database\Eloquent\Relations\Relation;
use AidingApp\Contact\Registries\ContactRbacRegistry;
use AidingApp\Authorization\AuthorizationRoleRegistry;
use AidingApp\Contact\Enums\ContactStatusColorOptions;
use AidingApp\Contact\Enums\SystemContactClassification;
use AidingApp\Contact\Observers\OrganizationTypeObserver;
use AidingApp\Contact\Observers\OrganizationIndustryObserver;
use AidingApp\Contact\Observers\OrganizationObserver;

class ContactServiceProvider extends ServiceProvider
{
    use ImplementsGraphQL;

    public function register(): void
    {
        Panel::configureUsing(fn (Panel $panel) => ($panel->getId() !== 'admin') || $panel->plugin(new ContactPlugin()));
    }

    public function boot(): void
    {
        Relation::morphMap([
            'contact' => Contact::class,
            'contact_source' => ContactSource::class,
            'contact_status' => ContactStatus::class,
            'organization' => Organization::class,
            'organization_industry' => OrganizationIndustry::class,
            'organization_type' => OrganizationType::class,
        ]);
        Contact::observe(ContactObserver::class);
        OrganizationType::observe(OrganizationTypeObserver::class);
        OrganizationIndustry::observe(OrganizationIndustryObserver::class);
        Organization::observe(OrganizationObserver::class);

        $this->discoverSchema(__DIR__ . '/../../graphql/*');
        $this->registerEnum(ContactStatusColorOptions::class);
        $this->registerEnum(SystemContactClassification::class);

        AuthorizationRoleRegistry::register(ContactRbacRegistry::class);
    }
}
