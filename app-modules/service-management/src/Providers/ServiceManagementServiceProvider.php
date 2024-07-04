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

namespace AidingApp\ServiceManagement\Providers;

use Filament\Panel;
use App\Concerns\ImplementsGraphQL;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use AidingApp\ServiceManagement\Models\Sla;
use AidingApp\ServiceManagement\Events\UpdateTTR;
use Illuminate\Database\Eloquent\Relations\Relation;
use AidingApp\ServiceManagement\Models\ChangeRequest;
use AidingApp\Authorization\AuthorizationRoleRegistry;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\ServiceManagementPlugin;
use AidingApp\ServiceManagement\Models\ChangeRequestType;
use AidingApp\ServiceManagement\Models\ServiceRequestForm;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use AidingApp\ServiceManagement\Models\ChangeRequestStatus;
use AidingApp\ServiceManagement\Listeners\UpdateTTROnClosed;
use AidingApp\ServiceManagement\Models\ServiceRequestStatus;
use AidingApp\ServiceManagement\Models\ServiceRequestUpdate;
use AidingApp\ServiceManagement\Models\ChangeRequestResponse;
use AidingApp\ServiceManagement\Models\ServiceRequestHistory;
use AidingApp\ServiceManagement\Models\ServiceRequestFormStep;
use AidingApp\ServiceManagement\Models\ServiceRequestPriority;
use AidingApp\ServiceManagement\Models\ServiceRequestFormField;
use AidingApp\ServiceManagement\Models\ServiceRequestAssignment;
use AidingApp\ServiceManagement\Observers\ChangeRequestObserver;
use AidingApp\ServiceManagement\Observers\ServiceRequestObserver;
use AidingApp\ServiceManagement\Models\ServiceRequestFormSubmission;
use AidingApp\ServiceManagement\Observers\ServiceRequestTypeObserver;
use AidingApp\ServiceManagement\Observers\ServiceRequestUpdateObserver;
use AidingApp\ServiceManagement\Models\ServiceRequestFormAuthentication;
use AidingApp\ServiceManagement\Observers\ServiceRequestHistoryObserver;
use AidingApp\ServiceManagement\Registries\ServiceManagementRbacRegistry;
use AidingApp\ServiceManagement\Observers\ServiceRequestAssignmentObserver;
use AidingApp\ServiceManagement\Services\ServiceRequestNumber\Contracts\ServiceRequestNumberGenerator;
use AidingApp\ServiceManagement\Services\ServiceRequestNumber\SqidPlusSixServiceRequestNumberGenerator;

class ServiceManagementServiceProvider extends ServiceProvider
{
  use ImplementsGraphQL;

  public function register(): void
  {
    Panel::configureUsing(fn (Panel $panel) => ($panel->getId() !== 'admin') || $panel->plugin(new ServiceManagementPlugin()));

    $this->app->bind(ServiceRequestNumberGenerator::class, SqidPlusSixServiceRequestNumberGenerator::class);
  }

  public function boot(): void
  {
    Relation::morphMap([
      'change_request_response' => ChangeRequestResponse::class,
      'change_request_status' => ChangeRequestStatus::class,
      'change_request_type' => ChangeRequestType::class,
      'change_request' => ChangeRequest::class,
      'service_request_assignment' => ServiceRequestAssignment::class,
      'service_request_form_authentication' => ServiceRequestFormAuthentication::class,
      'service_request_form_field' => ServiceRequestFormField::class,
      'service_request_form_step' => ServiceRequestFormStep::class,
      'service_request_form_submission' => ServiceRequestFormSubmission::class,
      'service_request_form' => ServiceRequestForm::class,
      'service_request_history' => ServiceRequestHistory::class,
      'service_request_priority' => ServiceRequestPriority::class,
      'service_request_status' => ServiceRequestStatus::class,
      'service_request_type' => ServiceRequestType::class,
      'service_request_update' => ServiceRequestUpdate::class,
      'service_request' => ServiceRequest::class,
      'sla' => Sla::class,
    ]);

    $this->registerObservers();

    $this->discoverSchema(__DIR__ . '/../../graphql/service-management.graphql');

    AuthorizationRoleRegistry::register(ServiceManagementRbacRegistry::class);
  }

  protected function registerObservers(): void
  {
    ChangeRequest::observe(ChangeRequestObserver::class);
    ServiceRequest::observe(ServiceRequestObserver::class);
    ServiceRequestAssignment::observe(ServiceRequestAssignmentObserver::class);
    ServiceRequestHistory::observe(ServiceRequestHistoryObserver::class);
    ServiceRequestUpdate::observe(ServiceRequestUpdateObserver::class);
    ServiceRequestType::observe(ServiceRequestTypeObserver::class);
  }
}
