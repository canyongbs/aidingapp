<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

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
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Aiding App® are registered trademarks of
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

use AidingApp\Ai\Models\AiAssistant;
use AidingApp\Ai\Models\AiAssistantFile;
use AidingApp\Ai\Models\AiMessage;
use AidingApp\Ai\Models\AiMessageFile;
use AidingApp\Ai\Models\AiThread;
use AidingApp\Ai\Models\LegacyAiMessageLog;
use AidingApp\Ai\Models\PortalAssistantMessage;
use AidingApp\Ai\Models\PortalAssistantThread;
use AidingApp\Ai\Models\Prompt;
use AidingApp\Ai\Models\PromptType;
use AidingApp\Alert\Models\Alert;
use AidingApp\Audit\Models\Audit;
use AidingApp\Authorization\Models\Permission;
use AidingApp\Authorization\Models\PermissionGroup;
use AidingApp\Authorization\Models\Role;
use AidingApp\Contact\Models\Contact;
use AidingApp\Contact\Models\ContactType;
use AidingApp\Contact\Models\Organization;
use AidingApp\Contact\Models\OrganizationIndustry;
use AidingApp\Contact\Models\OrganizationType;
use AidingApp\ContractManagement\Models\Contract;
use AidingApp\ContractManagement\Models\ContractType;
use AidingApp\Division\Models\Division;
use AidingApp\Engagement\Models\EmailTemplate;
use AidingApp\Engagement\Models\Engagement;
use AidingApp\Engagement\Models\EngagementBatch;
use AidingApp\Engagement\Models\EngagementFile;
use AidingApp\Engagement\Models\EngagementFileEntities;
use AidingApp\Engagement\Models\EngagementResponse;
use AidingApp\Engagement\Models\UnmatchedInboundCommunication;
use AidingApp\Form\Models\Submissible;
use AidingApp\Form\Models\SubmissibleAuthentication;
use AidingApp\Form\Models\SubmissibleField;
use AidingApp\Form\Models\SubmissibleStep;
use AidingApp\Form\Models\Submission;
use AidingApp\IntegrationOpenAi\Models\OpenAiVectorStore;
use AidingApp\InventoryManagement\Models\Asset;
use AidingApp\InventoryManagement\Models\AssetCheckIn;
use AidingApp\InventoryManagement\Models\AssetCheckOut;
use AidingApp\InventoryManagement\Models\AssetLocation;
use AidingApp\InventoryManagement\Models\AssetStatus;
use AidingApp\InventoryManagement\Models\AssetType;
use AidingApp\InventoryManagement\Models\MaintenanceActivity;
use AidingApp\InventoryManagement\Models\MaintenanceProvider;
use AidingApp\KnowledgeBase\Models\KnowledgeBaseCategory;
use AidingApp\KnowledgeBase\Models\KnowledgeBaseItem;
use AidingApp\KnowledgeBase\Models\KnowledgeBaseQuality;
use AidingApp\KnowledgeBase\Models\KnowledgeBaseStatus;
use AidingApp\LicenseManagement\Models\Product;
use AidingApp\LicenseManagement\Models\ProductLicense;
use AidingApp\Notification\Models\DatabaseMessage;
use AidingApp\Notification\Models\EmailMessage;
use AidingApp\Notification\Models\EmailMessageEvent;
use AidingApp\Notification\Models\StoredAnonymousNotifiable;
use AidingApp\Portal\Models\KnowledgeBaseArticleVote;
use AidingApp\Portal\Models\PortalAuthentication;
use AidingApp\Portal\Models\PortalGuest;
use AidingApp\Project\Models\Pipeline;
use AidingApp\Project\Models\PipelineEntry;
use AidingApp\Project\Models\PipelineStage;
use AidingApp\Project\Models\Project;
use AidingApp\Project\Models\ProjectAuditorTeam;
use AidingApp\Project\Models\ProjectAuditorUser;
use AidingApp\Project\Models\ProjectFile;
use AidingApp\Project\Models\ProjectManagerTeam;
use AidingApp\Project\Models\ProjectManagerUser;
use AidingApp\Project\Models\ProjectMilestone;
use AidingApp\Project\Models\ProjectMilestoneStatus;
use AidingApp\ServiceManagement\Models\ChangeRequest;
use AidingApp\ServiceManagement\Models\ChangeRequestResponse;
use AidingApp\ServiceManagement\Models\ChangeRequestStatus;
use AidingApp\ServiceManagement\Models\ChangeRequestType;
use AidingApp\ServiceManagement\Models\HistoricalServiceMonitoring;
use AidingApp\ServiceManagement\Models\Incident;
use AidingApp\ServiceManagement\Models\IncidentSeverity;
use AidingApp\ServiceManagement\Models\IncidentStatus;
use AidingApp\ServiceManagement\Models\IncidentUpdate;
use AidingApp\ServiceManagement\Models\ServiceMonitoringTarget;
use AidingApp\ServiceManagement\Models\ServiceMonitoringTargetTeam;
use AidingApp\ServiceManagement\Models\ServiceMonitoringTargetUser;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestAssignment;
use AidingApp\ServiceManagement\Models\ServiceRequestFeedback;
use AidingApp\ServiceManagement\Models\ServiceRequestForm;
use AidingApp\ServiceManagement\Models\ServiceRequestFormAuthentication;
use AidingApp\ServiceManagement\Models\ServiceRequestFormField;
use AidingApp\ServiceManagement\Models\ServiceRequestFormStep;
use AidingApp\ServiceManagement\Models\ServiceRequestFormSubmission;
use AidingApp\ServiceManagement\Models\ServiceRequestHistory;
use AidingApp\ServiceManagement\Models\ServiceRequestPriority;
use AidingApp\ServiceManagement\Models\ServiceRequestStatus;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use AidingApp\ServiceManagement\Models\ServiceRequestTypeCategory;
use AidingApp\ServiceManagement\Models\ServiceRequestTypeEmailTemplate;
use AidingApp\ServiceManagement\Models\ServiceRequestTypeTeamAuditor;
use AidingApp\ServiceManagement\Models\ServiceRequestTypeTeamManager;
use AidingApp\ServiceManagement\Models\ServiceRequestTypeUserAuditor;
use AidingApp\ServiceManagement\Models\ServiceRequestTypeUserManager;
use AidingApp\ServiceManagement\Models\ServiceRequestUpdate;
use AidingApp\ServiceManagement\Models\Sla;
use AidingApp\ServiceManagement\Models\TenantServiceRequestTypeDomain;
use AidingApp\Task\Models\ConfidentialTasksProjects;
use AidingApp\Task\Models\ConfidentialTaskTeam;
use AidingApp\Task\Models\ConfidentialTaskUser;
use AidingApp\Task\Models\Task;
use AidingApp\Team\Models\Team;
use AidingApp\Timeline\Models\Timeline;
use AidingApp\Webhook\Models\InboundWebhook;
use AidingApp\Webhook\Models\LandlordInboundWebhook;
use App\Models\BaseModel;
use App\Models\Export;
use App\Models\FailedImportRow;
use App\Models\HealthCheckResultHistoryItem;
use App\Models\Import;
use App\Models\LandlordSettingsProperty;
use App\Models\NotificationSetting;
use App\Models\NotificationSettingPivot;
use App\Models\Pronouns;
use App\Models\SettingsProperty;
use App\Models\SystemUser;
use App\Models\Tag;
use App\Models\Tenant;
use App\Models\User;

// This is a frozen snapshot of all model classes that existed prior to the
// adoption of Laravel's HasUuids (UUIDv7) trait. These models must continue
// using HasVersion4Uuids to avoid mixing UUID versions within the same table.
//
// New models are free to use HasUuids (UUIDv7) and should NOT be added here.

return [
    AiAssistant::class,
    AiAssistantFile::class,
    AiMessage::class,
    AiMessageFile::class,
    AiThread::class,
    LegacyAiMessageLog::class,
    PortalAssistantMessage::class,
    PortalAssistantThread::class,
    Prompt::class,
    PromptType::class,
    Alert::class,
    Audit::class,
    Permission::class,
    PermissionGroup::class,
    Role::class,
    Contact::class,
    ContactType::class,
    Organization::class,
    OrganizationIndustry::class,
    OrganizationType::class,
    Contract::class,
    ContractType::class,
    Division::class,
    EmailTemplate::class,
    Engagement::class,
    EngagementBatch::class,
    EngagementFile::class,
    EngagementFileEntities::class,
    EngagementResponse::class,
    UnmatchedInboundCommunication::class,
    OpenAiVectorStore::class,
    Asset::class,
    AssetCheckIn::class,
    AssetCheckOut::class,
    AssetLocation::class,
    AssetStatus::class,
    AssetType::class,
    MaintenanceActivity::class,
    MaintenanceProvider::class,
    KnowledgeBaseCategory::class,
    KnowledgeBaseItem::class,
    KnowledgeBaseQuality::class,
    KnowledgeBaseStatus::class,
    Product::class,
    ProductLicense::class,
    DatabaseMessage::class,
    EmailMessage::class,
    EmailMessageEvent::class,
    StoredAnonymousNotifiable::class,
    KnowledgeBaseArticleVote::class,
    PortalAuthentication::class,
    PortalGuest::class,
    Pipeline::class,
    PipelineEntry::class,
    PipelineStage::class,
    Project::class,
    ProjectAuditorTeam::class,
    ProjectAuditorUser::class,
    ProjectFile::class,
    ProjectManagerTeam::class,
    ProjectManagerUser::class,
    ProjectMilestone::class,
    ProjectMilestoneStatus::class,
    ChangeRequest::class,
    ChangeRequestResponse::class,
    ChangeRequestStatus::class,
    ChangeRequestType::class,
    HistoricalServiceMonitoring::class,
    Incident::class,
    IncidentSeverity::class,
    IncidentStatus::class,
    IncidentUpdate::class,
    ServiceMonitoringTarget::class,
    ServiceMonitoringTargetTeam::class,
    ServiceMonitoringTargetUser::class,
    ServiceRequest::class,
    ServiceRequestAssignment::class,
    ServiceRequestFeedback::class,
    ServiceRequestForm::class,
    ServiceRequestFormAuthentication::class,
    ServiceRequestFormField::class,
    ServiceRequestFormStep::class,
    ServiceRequestFormSubmission::class,
    ServiceRequestHistory::class,
    ServiceRequestPriority::class,
    ServiceRequestStatus::class,
    ServiceRequestType::class,
    ServiceRequestTypeCategory::class,
    ServiceRequestTypeEmailTemplate::class,
    ServiceRequestTypeTeamAuditor::class,
    ServiceRequestTypeTeamManager::class,
    ServiceRequestTypeUserAuditor::class,
    ServiceRequestTypeUserManager::class,
    ServiceRequestUpdate::class,
    Sla::class,
    TenantServiceRequestTypeDomain::class,
    ConfidentialTaskTeam::class,
    ConfidentialTaskUser::class,
    ConfidentialTasksProjects::class,
    Task::class,
    Team::class,
    Timeline::class,
    InboundWebhook::class,
    LandlordInboundWebhook::class,
    Export::class,
    FailedImportRow::class,
    HealthCheckResultHistoryItem::class,
    Import::class,
    LandlordSettingsProperty::class,
    NotificationSetting::class,
    NotificationSettingPivot::class,
    Pronouns::class,
    SettingsProperty::class,
    SystemUser::class,
    Tag::class,
    Tenant::class,
    User::class,
    BaseModel::class,
    Submissible::class,
    SubmissibleField::class,
    Submission::class,
    SubmissibleAuthentication::class,
    SubmissibleStep::class,
];
