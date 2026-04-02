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
      of the licensor in the software. Any use of the licensor's trademarks is subject
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

// This is a frozen snapshot of all model classes that existed prior to the
// adoption of Laravel's HasUuids (UUIDv7) trait. These models must continue
// using HasVersion4Uuids to avoid mixing UUID versions within the same table.
//
// New models are free to use HasUuids (UUIDv7) and should NOT be added here.

return [
    \AidingApp\Ai\Models\AiAssistant::class,
    \AidingApp\Ai\Models\AiAssistantFile::class,
    \AidingApp\Ai\Models\AiMessage::class,
    \AidingApp\Ai\Models\AiMessageFile::class,
    \AidingApp\Ai\Models\AiThread::class,
    \AidingApp\Ai\Models\LegacyAiMessageLog::class,
    \AidingApp\Ai\Models\PortalAssistantMessage::class,
    \AidingApp\Ai\Models\PortalAssistantThread::class,
    \AidingApp\Ai\Models\Prompt::class,
    \AidingApp\Ai\Models\PromptType::class,
    \AidingApp\Alert\Models\Alert::class,
    \AidingApp\Audit\Models\Audit::class,
    \AidingApp\Authorization\Models\LoginMagicLink::class,
    \AidingApp\Authorization\Models\Permission::class,
    \AidingApp\Authorization\Models\PermissionGroup::class,
    \AidingApp\Authorization\Models\Role::class,
    \AidingApp\Contact\Models\Contact::class,
    \AidingApp\Contact\Models\ContactType::class,
    \AidingApp\Contact\Models\Organization::class,
    \AidingApp\Contact\Models\OrganizationIndustry::class,
    \AidingApp\Contact\Models\OrganizationType::class,
    \AidingApp\ContractManagement\Models\Contract::class,
    \AidingApp\ContractManagement\Models\ContractType::class,
    \AidingApp\Division\Models\Division::class,
    \AidingApp\Engagement\Models\EmailTemplate::class,
    \AidingApp\Engagement\Models\Engagement::class,
    \AidingApp\Engagement\Models\EngagementBatch::class,
    \AidingApp\Engagement\Models\EngagementFile::class,
    \AidingApp\Engagement\Models\EngagementFileEntities::class,
    \AidingApp\Engagement\Models\EngagementResponse::class,
    \AidingApp\Engagement\Models\UnmatchedInboundCommunication::class,
    \AidingApp\IntegrationOpenAi\Models\OpenAiVectorStore::class,
    \AidingApp\InventoryManagement\Models\Asset::class,
    \AidingApp\InventoryManagement\Models\AssetCheckIn::class,
    \AidingApp\InventoryManagement\Models\AssetCheckOut::class,
    \AidingApp\InventoryManagement\Models\AssetLocation::class,
    \AidingApp\InventoryManagement\Models\AssetStatus::class,
    \AidingApp\InventoryManagement\Models\AssetType::class,
    \AidingApp\InventoryManagement\Models\MaintenanceActivity::class,
    \AidingApp\InventoryManagement\Models\MaintenanceProvider::class,
    \AidingApp\KnowledgeBase\Models\KnowledgeBaseCategory::class,
    \AidingApp\KnowledgeBase\Models\KnowledgeBaseItem::class,
    \AidingApp\KnowledgeBase\Models\KnowledgeBaseQuality::class,
    \AidingApp\KnowledgeBase\Models\KnowledgeBaseStatus::class,
    \AidingApp\LicenseManagement\Models\Product::class,
    \AidingApp\LicenseManagement\Models\ProductLicense::class,
    \AidingApp\Notification\Models\DatabaseMessage::class,
    \AidingApp\Notification\Models\EmailMessage::class,
    \AidingApp\Notification\Models\EmailMessageEvent::class,
    \AidingApp\Notification\Models\StoredAnonymousNotifiable::class,
    \AidingApp\Portal\Models\KnowledgeBaseArticleVote::class,
    \AidingApp\Portal\Models\PortalAuthentication::class,
    \AidingApp\Portal\Models\PortalGuest::class,
    \AidingApp\Project\Models\Pipeline::class,
    \AidingApp\Project\Models\PipelineEntry::class,
    \AidingApp\Project\Models\PipelineStage::class,
    \AidingApp\Project\Models\Project::class,
    \AidingApp\Project\Models\ProjectAuditorTeam::class,
    \AidingApp\Project\Models\ProjectAuditorUser::class,
    \AidingApp\Project\Models\ProjectFile::class,
    \AidingApp\Project\Models\ProjectManagerTeam::class,
    \AidingApp\Project\Models\ProjectManagerUser::class,
    \AidingApp\Project\Models\ProjectMilestone::class,
    \AidingApp\Project\Models\ProjectMilestoneStatus::class,
    \AidingApp\ServiceManagement\Models\ChangeRequest::class,
    \AidingApp\ServiceManagement\Models\ChangeRequestResponse::class,
    \AidingApp\ServiceManagement\Models\ChangeRequestStatus::class,
    \AidingApp\ServiceManagement\Models\ChangeRequestType::class,
    \AidingApp\ServiceManagement\Models\HistoricalServiceMonitoring::class,
    \AidingApp\ServiceManagement\Models\Incident::class,
    \AidingApp\ServiceManagement\Models\IncidentSeverity::class,
    \AidingApp\ServiceManagement\Models\IncidentStatus::class,
    \AidingApp\ServiceManagement\Models\IncidentUpdate::class,
    \AidingApp\ServiceManagement\Models\ServiceMonitoringTarget::class,
    \AidingApp\ServiceManagement\Models\ServiceMonitoringTargetTeam::class,
    \AidingApp\ServiceManagement\Models\ServiceMonitoringTargetUser::class,
    \AidingApp\ServiceManagement\Models\ServiceRequest::class,
    \AidingApp\ServiceManagement\Models\ServiceRequestAssignment::class,
    \AidingApp\ServiceManagement\Models\ServiceRequestFeedback::class,
    \AidingApp\ServiceManagement\Models\ServiceRequestForm::class,
    \AidingApp\ServiceManagement\Models\ServiceRequestFormAuthentication::class,
    \AidingApp\ServiceManagement\Models\ServiceRequestFormField::class,
    \AidingApp\ServiceManagement\Models\ServiceRequestFormStep::class,
    \AidingApp\ServiceManagement\Models\ServiceRequestFormSubmission::class,
    \AidingApp\ServiceManagement\Models\ServiceRequestHistory::class,
    \AidingApp\ServiceManagement\Models\ServiceRequestPriority::class,
    \AidingApp\ServiceManagement\Models\ServiceRequestStatus::class,
    \AidingApp\ServiceManagement\Models\ServiceRequestType::class,
    \AidingApp\ServiceManagement\Models\ServiceRequestTypeCategory::class,
    \AidingApp\ServiceManagement\Models\ServiceRequestTypeEmailTemplate::class,
    \AidingApp\ServiceManagement\Models\ServiceRequestTypeTeamAuditor::class,
    \AidingApp\ServiceManagement\Models\ServiceRequestTypeTeamManager::class,
    \AidingApp\ServiceManagement\Models\ServiceRequestTypeUserAuditor::class,
    \AidingApp\ServiceManagement\Models\ServiceRequestTypeUserManager::class,
    \AidingApp\ServiceManagement\Models\ServiceRequestUpdate::class,
    \AidingApp\ServiceManagement\Models\Sla::class,
    \AidingApp\ServiceManagement\Models\TenantServiceRequestTypeDomain::class,
    \AidingApp\Task\Models\ConfidentialTaskTeam::class,
    \AidingApp\Task\Models\ConfidentialTaskUser::class,
    \AidingApp\Task\Models\ConfidentialTasksProjects::class,
    \AidingApp\Task\Models\Task::class,
    \AidingApp\Team\Models\Team::class,
    \AidingApp\Timeline\Models\Timeline::class,
    \AidingApp\Webhook\Models\InboundWebhook::class,
    \AidingApp\Webhook\Models\LandlordInboundWebhook::class,
    \App\Models\Export::class,
    \App\Models\FailedImportRow::class,
    \App\Models\HealthCheckResultHistoryItem::class,
    \App\Models\Import::class,
    \App\Models\LandlordSettingsProperty::class,
    \App\Models\NotificationSetting::class,
    \App\Models\NotificationSettingPivot::class,
    \App\Models\Pronouns::class,
    \App\Models\SettingsProperty::class,
    \App\Models\SystemUser::class,
    \App\Models\Tag::class,
    \App\Models\Tenant::class,
    \App\Models\User::class,
];
