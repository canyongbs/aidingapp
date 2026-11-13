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

namespace AidingApp\Authorization\Enums;

enum PermissionDescription: string
{
    case Assets = 'Enables display and management of assets and related asset management features. Permissions cover creating, viewing, updating, and deleting assets, asset locations, asset statuses, asset types, and maintenance providers; creating, viewing, and updating maintenance activities; and viewing asset check-ins and check-outs. Existing restrictions on user access to check-in and check-out features continue to apply.';
    case Audit = 'This permission group enables the display and management of the Audit feature in the secondary navigation group Usage Auditing. Permission options include  view audits.';
    case ChangeRequest = 'Enables display and management of change requests and their configuration. Permissions cover creating, viewing, updating, and deleting change requests, change request statuses, and change request types.';
    case Contacts = 'Enables display and management of contacts and associated engagement features. Permissions cover creating, viewing, updating, and deleting contacts and engagement files, viewing timelines, and viewing engagement responses. Existing restrictions on user access to engagement files and engagement responses continue to apply.';
    case Contract = 'This permission group enables the display and management of the Contract feature in the primary navigation group Purchasing. Permission options include create, view, update, and delete contracts.';
    case Group = 'This permission group enables the display of the Group feature in the primary navigation group Users. Permission options include create, view, update, and delete groups.';
    case Advisories = 'Enables display and management of advisories and their updates. Permissions cover the existing supported actions for advisories, together with creating, viewing, updating, and deleting advisory updates.';
    case KnowledgeBaseItem = 'This permission group enables the display and management of the Knowledge Base Item feature in the primary navigation group Service Management. Permission options include create, view, update, and delete knowledge base items.';
    case License = 'This permission group enables the management of the License feature. Permission options include bulk assigning licesnes.';
    case Licenses = 'Enables display and management of License Management features, including products and their licenses. Permissions cover creating, viewing, updating, and deleting products and product licenses.';
    case Organization = 'This permission group enables the display and management of the Organization feature in the primary navigation group Clients. Permission options include create, view, update, and delete organizations.';
    case Permission = 'This permission group enables the display and management of the Permission feature in the primary navigation group User Management. Permission options include view permissions.';
    case Projects = 'Enables display and management of projects and their pipelines. Permissions cover creating, viewing, updating, and deleting projects and pipelines where those features are available.';
    case RealtimeChat = 'This permission group enables the display of the Realtime Chat feature in the primary navigation group Staff Engagement. Permission options include view realtime chat.';
    case Reporting = 'This permission group enables the display and management of the Reporting feature in the primary navigation group User Management. Permission options include managing which users, departments, and groups have access to each report in the Report Library.';
    case Role = 'This permission group enables the display and management of the Role feature in the primary navigation group User Management. Permission options include create, view, update, and delete roles.';
    case ServiceMonitoring = 'This permission group enables the display and management of the Service Monitoring feature in the primary navigation group Service Management. Permission options include create, view, update, and delete service monitorings.';
    case ServiceRequest = 'Enables display and management of service requests and their related features. Permissions cover creating, viewing, updating, and deleting service requests, assignments, priorities, and internal and public updates, as well as viewing service request history.';
    case Settings = 'This permission group enables access of the primary navigation group Settings.';
    case SystemUser = 'This permission group enables the display and management of the Programmatic Users feature in the primary navigation group User Management. Permission options include create, view, update, and delete system users.';
    case Department = 'This permission group enables the display and management of the Department feature in the primary navigation group User Management. Permission options include create, view, update, and delete departments.';
    case User = 'This permission group enables the display and management of the User feature in the primary navigation group User Management. Permission options include create, view, update, and delete users.';

    public function getLabel(): string
    {
        return $this->name;
    }
}
