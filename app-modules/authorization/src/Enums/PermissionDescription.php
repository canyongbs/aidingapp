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

namespace AidingApp\Authorization\Enums;

enum PermissionDescription: string
{
    case Alert = 'This permission group enables the display and management of the Alert feature in the tertiary navigation group View Contact. Permission options include create, view, update, and delete alerts.';
    case Asset = 'This permission group enables the display and management of the Asset feature in the primary navigation group Service Management. Permission options include create, view, update, and delete assets.';
    case AssetCheckIn = 'This permission group enables the display and management of the Asset Check in feature, a primary navigation group. Permission options include view asset check ins. Note: Users cannot access this feature.';
    case AssetCheckOut = 'This permission group enables the display and management of the Asset Check out feature, a primary navigation group. Permission options include view asset check outs. Note: Users cannot access this feature.';
    case AssetLocation = 'This permission group enables the display and management of the Asset Location feature in the secondary navigation group Asset Management. Permission options include create, view, update, and delete asset locations.';
    case AssetStatus = 'This permission group enables the display and management of the Asset Status feature in the secondary navigation group Asset Management. Permission options include create, view, update, and delete asset statuses.';
    case AssetType = 'This permission group enables the display and management of the Asset Type feature in the secondary navigation group Asset Management. Permission options include create, view, update, and delete asset types.';
    case Audit = 'This permission group enables the display and management of the Audit feature in the secondary navigation group Usage Auditing. Permission options include  view audits.';
    case ChangeRequest = 'This permission group enables the display and management of the Change Request feature in the primary navigation group Service Management. Permission options include create, view, update, and delete change requests.';
    case ChangeRequestStatus = 'This permission group enables the display and management of the Change Request Status feature in the secondary navigation group Service Management. Permission options include create, view, update, and delete change request statuses.';
    case ChangeRequestType = 'This permission group enables the display and management of the Change Request Type feature in the secondary navigation group Service Management. Permission options include create, view, update, and delete change request types.';
    case Contact = 'This permission group enables the display and management of the Contact feature in the primary navigation group Clients. Permission options include create, view, update, and delete contacts.';
    case Contract = 'This permission group enables the display and management of the Contract feature in the primary navigation group Purchasing. Permission options include create, view, update, and delete contracts.';
    case Division = 'This permission group enables the display and management of the Division feature in the primary navigation group User Management. Permission options include create, view, update, and delete divisions.';
    case Engagement = 'This permission group enables the display of the Timeline feature in various navigation groups. Permission options include view timelines.';
    case EngagementFile = 'This permission group enables the display and management of the Engagement File feature in the primary navigation group Engagement Features. Permission options include create, view, update, and delete engagement files. Note: Users cannot access this feature.';
    case EngagementResponse = 'This permission group enables the display of the Engagement Response feature. Permission options include view engagement responses. Note: Users cannot access this feature.';
    case Incident = 'This permission group enables the display and management of the Incident feature in the primary navigtion group Service Management. Permission options include create, view, update, and delete incidents.';
    case IncidentUpdate = 'This permission group enables the display and management of the Incident Update feature in the tertiary navigation group View Incident. Permission options include create, view, update, and delete incident updates.';
    case KnowledgeBaseItem = 'This permission group enables the display and management of the Knowledge Base Item feature in the primary navigation group Service Management. Permission options include create, view, update, and delete knowledge base items.';
    case License = 'This permission group enables the management of the License feature. Permission options include bulk assigning licesnes.';
    case MaintenanceActivity = 'This permission group enables the display and management of the Maintenance Activity feature in the tertiary navigation group View Asset. Permission options include create, view, and update maintenance activity.';
    case MaintenanceProvider = 'This permission group enables the display and management of the Maintenance Provider feature in the secondary navigation group Asset Management. Permission options include create, view, update and delete maintenance providers.';
    case Organization = 'This permission group enables the display and management of the Organization feature in the primary navigation group Clients. Permission options include create, view, update, and delete organizations.';
    case Permission = 'This permission group enables the display and management of the Permission feature in the primary navigation group User Management. Permission options include view permissions.';
    case Product = 'This permission group enables the display and management of the License Management feature in the primary navigation group Purchasing. Permission options include create, view, update, and delete products.';
    case ProductAdmin = 'This permission group enables access of the primary navigation group Settings.';
    case ProductLicense = 'This permission group enables the display and management of the Product License feature in the tertiary navigation group View License. Permission options include create, view, update, and delete product licenses.';
    case Project = 'This permission group enables the display of the Project feature in the primary navigation group Projects. It is coming soon. Permission options will include create, view, update, and delete';
    case RealtimeChat = 'This permission group enables the display of the Realtime Chat feature in the primary navigation group Staff Engagement. Permission options include view realtime chat.';
    case ReportLibrary = 'This permission group enables the display of the Report Library feature in the primary navigation group Report Center. Permission options include view report libraries.';
    case Role = 'This permission group enables the display and management of the Role feature in the primary navigation group User Management. Permission options include create, view, update, and delete roles.';
    case ServiceMonitoring = 'This permission group enables the display and management of the Service Monitoring feature in the primary navigation group Service Management. Permission options include create, view, update, and delete service monitorings.';
    case ServiceRequest = 'This permission group enables the display and management of the Service Request feature in the primary navigation group Service Management. Permission options include create, view, update, and delete service requests.';
    case ServiceRequestAssignment = 'This permission group enables the display of the Service Request Assignment feature in the tertiary navigation group View Service Request. Permission options include create, view, update, and delete service request assignments.';
    case ServiceRequestHistory = 'This permission group enables the display of the Service Request History feature in the tertiary navigation group View Service Request. Permission options include view service history.';
    case ServiceRequestPriority = 'This permission group enables the display and management of the Service Request Priority feature in the tertiary navigation group View Service Request. Permission options include create, view, update, and delete service request priorities.';
    case ServiceRequestUpdate = 'This permission group enables the display of the Service Request Update feature in the tertiary navigation group View Service Request. Permission options include create, view, update, and delete service request updates.';
    case SystemUser = 'This permission group enables the display and management of the Programmatic Users feature in the primary navigation group User Management. Permission options include create, view, update, and delete system users.';
    case Task = 'This permission group enables the display and management of the Task feature in the primary navigation group Project Management. Permission options include create, view, update, and delete tasks.';
    case Team = 'This permission group enables the display and management of the Team feature in the primary navigation group User Management. Permission options include create, view, update, and delete teams.';
    case User = 'This permission group enables the display and management of the User feature in the primary navigation group User Management. Permission options include create, view, update, and delete users.';

    public function getLabel(): string
    {
        return $this->name;
    }
}
