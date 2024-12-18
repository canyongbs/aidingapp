<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * App\Models\Export
 *
 * @property string $id
 * @property int|null $completed_at
 * @property string $file_disk
 * @property string|null $file_name
 * @property string $exporter
 * @property int $processed_rows
 * @property int $total_rows
 * @property int $successful_rows
 * @property string $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $user
 * @method static \Illuminate\Database\Eloquent\Builder|Export newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Export newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Export onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Export query()
 * @method static \Illuminate\Database\Eloquent\Builder|Export whereCompletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Export whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Export whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Export whereExporter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Export whereFileDisk($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Export whereFileName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Export whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Export whereProcessedRows($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Export whereSuccessfulRows($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Export whereTotalRows($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Export whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Export whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Export withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Export withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperExport {}
}

namespace App\Models{
/**
 * App\Models\FailedImportRow
 *
 * @property string $id
 * @property array $data
 * @property string $import_id
 * @property string|null $validation_error
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Import $import
 * @method static \Illuminate\Database\Eloquent\Builder|FailedImportRow newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FailedImportRow newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FailedImportRow onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|FailedImportRow query()
 * @method static \Illuminate\Database\Eloquent\Builder|FailedImportRow whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FailedImportRow whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FailedImportRow whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FailedImportRow whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FailedImportRow whereImportId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FailedImportRow whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FailedImportRow whereValidationError($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FailedImportRow withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|FailedImportRow withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperFailedImportRow {}
}

namespace App\Models{
/**
 * App\Models\HealthCheckResultHistoryItem
 *
 * @property int $id
 * @property string $check_name
 * @property string $check_label
 * @property string $status
 * @property string|null $notification_message
 * @property string|null $short_summary
 * @property array $meta
 * @property string $ended_at
 * @property string $batch
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|HealthCheckResultHistoryItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|HealthCheckResultHistoryItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|HealthCheckResultHistoryItem query()
 * @method static \Illuminate\Database\Eloquent\Builder|HealthCheckResultHistoryItem whereBatch($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HealthCheckResultHistoryItem whereCheckLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HealthCheckResultHistoryItem whereCheckName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HealthCheckResultHistoryItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HealthCheckResultHistoryItem whereEndedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HealthCheckResultHistoryItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HealthCheckResultHistoryItem whereMeta($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HealthCheckResultHistoryItem whereNotificationMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HealthCheckResultHistoryItem whereShortSummary($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HealthCheckResultHistoryItem whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HealthCheckResultHistoryItem whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperHealthCheckResultHistoryItem {}
}

namespace App\Models{
/**
 * App\Models\Import
 *
 * @property string $id
 * @property int|null $completed_at
 * @property string $file_name
 * @property string $file_path
 * @property string $importer
 * @property int $processed_rows
 * @property int $total_rows
 * @property int $successful_rows
 * @property string $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\FailedImportRow> $failedRows
 * @property-read int|null $failed_rows_count
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $user
 * @method static \Illuminate\Database\Eloquent\Builder|Import newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Import newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Import onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Import query()
 * @method static \Illuminate\Database\Eloquent\Builder|Import whereCompletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Import whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Import whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Import whereFileName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Import whereFilePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Import whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Import whereImporter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Import whereProcessedRows($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Import whereSuccessfulRows($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Import whereTotalRows($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Import whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Import whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Import withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Import withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperImport {}
}

namespace App\Models{
/**
 * App\Models\LandlordSettingsProperty
 *
 * @property string $id
 * @property string $group
 * @property string $name
 * @property bool $locked
 * @property mixed $payload
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|LandlordSettingsProperty newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LandlordSettingsProperty newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LandlordSettingsProperty query()
 * @method static \Illuminate\Database\Eloquent\Builder|LandlordSettingsProperty whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LandlordSettingsProperty whereGroup($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LandlordSettingsProperty whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LandlordSettingsProperty whereLocked($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LandlordSettingsProperty whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LandlordSettingsProperty wherePayload($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LandlordSettingsProperty whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperLandlordSettingsProperty {}
}

namespace App\Models{
/**
 * App\Models\NotificationSetting
 *
 * @property string $id
 * @property string $name
 * @property string|null $primary_color
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string|null $from_name
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Division\Models\Division> $divisions
 * @property-read int|null $divisions_count
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\NotificationSettingPivot> $settings
 * @property-read int|null $settings_count
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationSetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationSetting onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationSetting whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationSetting whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationSetting whereFromName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationSetting whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationSetting wherePrimaryColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationSetting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationSetting withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationSetting withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperNotificationSetting {}
}

namespace App\Models{
/**
 * App\Models\NotificationSettingPivot
 *
 * @property string $id
 * @property string $notification_setting_id
 * @property string $related_to_type
 * @property string $related_to_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $relatedTo
 * @property-read \App\Models\NotificationSetting $setting
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationSettingPivot newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationSettingPivot newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationSettingPivot query()
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationSettingPivot whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationSettingPivot whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationSettingPivot whereNotificationSettingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationSettingPivot whereRelatedToId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationSettingPivot whereRelatedToType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NotificationSettingPivot whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperNotificationSettingPivot {}
}

namespace App\Models{
/**
 * App\Models\Pronouns
 *
 * @property string $id
 * @property string $label
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @method static \Database\Factories\PronounsFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Pronouns newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Pronouns newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Pronouns onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Pronouns query()
 * @method static \Illuminate\Database\Eloquent\Builder|Pronouns whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Pronouns whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Pronouns whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Pronouns whereLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Pronouns whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Pronouns withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Pronouns withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperPronouns {}
}

namespace App\Models{
/**
 * App\Models\SettingsProperty
 *
 * @property string $id
 * @property string $group
 * @property string $name
 * @property bool $locked
 * @property mixed $payload
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|SettingsProperty newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SettingsProperty newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SettingsProperty query()
 * @method static \Illuminate\Database\Eloquent\Builder|SettingsProperty whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SettingsProperty whereGroup($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SettingsProperty whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SettingsProperty whereLocked($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SettingsProperty whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SettingsProperty wherePayload($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SettingsProperty whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperSettingsProperty {}
}

namespace App\Models{
/**
 * App\Models\SystemUser
 *
 * @property string $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Authorization\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Authorization\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder|SystemUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemUser newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemUser onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Authenticatable permission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemUser query()
 * @method static \Illuminate\Database\Eloquent\Builder|Authenticatable role($roles, $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemUser whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemUser whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemUser whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemUser whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemUser whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SystemUser withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|SystemUser withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperSystemUser {}
}

namespace App\Models{
/**
 * App\Models\Tag
 *
 * @property string $id
 * @property string $name
 * @property string|null $type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Database\Factories\TagFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Tag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Tag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Tag query()
 * @method static \Illuminate\Database\Eloquent\Builder|Tag whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tag whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tag whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tag whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tag whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperTag {}
}

namespace App\Models{
/**
 * App\Models\Tenant
 *
 * @property string $id
 * @property string $name
 * @property string $domain
 * @property mixed $key
 * @property mixed $config
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property bool $setup_complete
 * @method static \Spatie\Multitenancy\TenantCollection<int, static> all($columns = ['*'])
 * @method static \Spatie\Multitenancy\TenantCollection<int, static> get($columns = ['*'])
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant query()
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant whereConfig($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant whereDomain($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant whereSetupComplete($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Tenant withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperTenant {}
}

namespace App\Models{
/**
 * App\Models\User
 *
 * @property string $id
 * @property string|null $emplid
 * @property string|null $name
 * @property string|null $email
 * @property bool $is_email_visible_on_profile
 * @property string|null $password
 * @property string|null $remember_token
 * @property string|null $locale
 * @property string|null $type
 * @property bool $is_external
 * @property string|null $bio
 * @property bool $is_bio_visible_on_profile
 * @property string|null $avatar_url
 * @property bool $are_teams_visible_on_profile
 * @property bool $is_division_visible_on_profile
 * @property string $timezone
 * @property bool $has_enabled_public_profile
 * @property string|null $public_profile_slug
 * @property bool $office_hours_are_enabled
 * @property array|null $office_hours
 * @property bool $out_of_office_is_enabled
 * @property \Illuminate\Support\Carbon|null $out_of_office_starts_at
 * @property \Illuminate\Support\Carbon|null $out_of_office_ends_at
 * @property string|null $phone_number
 * @property bool $is_phone_number_visible_on_profile
 * @property bool $working_hours_are_enabled
 * @property bool $are_working_hours_visible_on_profile
 * @property array|null $working_hours
 * @property string|null $job_title
 * @property string|null $pronouns_id
 * @property bool $are_pronouns_visible_on_profile
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Task\Models\Task> $assignedTasks
 * @property-read int|null $assigned_tasks_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\ServiceManagement\Models\ChangeRequestResponse> $changeRequestResponses
 * @property-read int|null $change_request_responses_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\ServiceManagement\Models\ChangeRequestType> $changeRequestTypes
 * @property-read int|null $change_request_types_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\ServiceManagement\Models\ChangeRequest> $changeRequests
 * @property-read int|null $change_requests_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Contact\Models\Contact> $contactSubscriptions
 * @property-read int|null $contact_subscriptions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\InAppCommunication\Models\TwilioConversation> $conversations
 * @property-read int|null $conversations_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Engagement\Models\EngagementBatch> $engagementBatches
 * @property-read int|null $engagement_batches_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Engagement\Models\Engagement> $engagements
 * @property-read int|null $engagements_count
 * @property-read mixed $is_admin
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Authorization\Models\License> $licenses
 * @property-read int|null $licenses_count
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Authorization\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \App\Models\Pronouns|null $pronouns
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Authorization\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\ServiceManagement\Models\ServiceRequestAssignment> $serviceRequestAssignments
 * @property-read int|null $service_request_assignments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\ServiceManagement\Models\ServiceRequestType> $serviceRequestTypeIndividualAssignment
 * @property-read int|null $service_request_type_individual_assignment_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Notification\Models\Subscription> $subscriptions
 * @property-read int|null $subscriptions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Team\Models\Team> $teams
 * @property-read int|null $teams_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\AidingApp\Alert\Models\Alert[] $contactAlerts
 * @property-read int|null $contact_alerts_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\AidingApp\Authorization\Models\Permission[] $permissionsFromRoles
 * @property-read int|null $permissions_from_roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\AidingApp\ServiceManagement\Models\ServiceRequest[] $serviceRequests
 * @property-read int|null $service_requests_count
 * @method static \Illuminate\Database\Eloquent\Builder|User admins()
 * @method static \Illuminate\Database\Eloquent\Builder|User advancedFilter($data)
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Authenticatable permission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|Authenticatable role($roles, $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereArePronounsVisibleOnProfile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAreTeamsVisibleOnProfile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAreWorkingHoursVisibleOnProfile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAvatarUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereBio($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmplid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereHasEnabledPublicProfile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereIsBioVisibleOnProfile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereIsDivisionVisibleOnProfile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereIsEmailVisibleOnProfile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereIsExternal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereIsPhoneNumberVisibleOnProfile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereJobTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLocale($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereOfficeHours($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereOfficeHoursAreEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereOutOfOfficeEndsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereOutOfOfficeIsEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereOutOfOfficeStartsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePhoneNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePronounsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePublicProfileSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereTimezone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereWorkingHours($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereWorkingHoursAreEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|User withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperUser {}
}

namespace AidingApp\Alert\Models{
/**
 * AidingApp\Alert\Models\Alert
 *
 * @property-read Contact $concern
 * @property string $id
 * @property string $concern_type
 * @property string $concern_id
 * @property string $description
 * @property \AidingApp\Alert\Enums\AlertSeverity $severity
 * @property \AidingApp\Alert\Enums\AlertStatus $status
 * @property string $suggested_intervention
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @method static \AidingApp\Alert\Database\Factories\AlertFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Alert licensedToEducatable(string $relationship)
 * @method static \Illuminate\Database\Eloquent\Builder|Alert newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Alert newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Alert onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Alert query()
 * @method static \Illuminate\Database\Eloquent\Builder|Alert status(\AidingApp\Alert\Enums\AlertStatus $status)
 * @method static \Illuminate\Database\Eloquent\Builder|Alert whereConcernId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Alert whereConcernType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Alert whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Alert whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Alert whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Alert whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Alert whereSeverity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Alert whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Alert whereSuggestedIntervention($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Alert whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Alert withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Alert withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperAlert {}
}

namespace AidingApp\Audit\Models{
/**
 * AidingApp\Audit\Models\Audit
 *
 * @property string $id
 * @property string|null $change_agent_type
 * @property string|null $change_agent_id
 * @property string $event
 * @property string $auditable_type
 * @property string $auditable_id
 * @property array|null $old_values
 * @property array|null $new_values
 * @property string|null $url
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property string|null $tags
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $auditable
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $user
 * @method static \AidingApp\Audit\Database\Factories\AuditFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Audit newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Audit newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Audit query()
 * @method static \Illuminate\Database\Eloquent\Builder|Audit whereAuditableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Audit whereAuditableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Audit whereChangeAgentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Audit whereChangeAgentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Audit whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Audit whereEvent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Audit whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Audit whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Audit whereNewValues($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Audit whereOldValues($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Audit whereTags($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Audit whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Audit whereUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Audit whereUserAgent($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperAudit {}
}

namespace AidingApp\Authorization\Models{
/**
 * AidingApp\Authorization\Models\License
 *
 * @property string $id
 * @property string $user_id
 * @property \AidingApp\Authorization\Enums\LicenseType $type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|License newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|License newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|License onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|License query()
 * @method static \Illuminate\Database\Eloquent\Builder|License whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|License whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|License whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|License whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|License whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|License whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|License withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|License withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperLicense {}
}

namespace AidingApp\Authorization\Models{
/**
 * AidingApp\Authorization\Models\Permission
 *
 * @property string $id
 * @property string $name
 * @property string $guard_name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $group_id
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \AidingApp\Authorization\Models\PermissionGroup $group
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Authorization\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SystemUser> $systemUsers
 * @property-read int|null $system_users_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder|Permission api()
 * @method static \AidingApp\Authorization\Database\Factories\PermissionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Permission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Permission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Permission permission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder|Permission query()
 * @method static \Illuminate\Database\Eloquent\Builder|Permission role($roles, $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder|Permission web()
 * @method static \Illuminate\Database\Eloquent\Builder|Permission whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Permission whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Permission whereGuardName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Permission whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Permission whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Permission whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperPermission {}
}

namespace AidingApp\Authorization\Models{
/**
 * AidingApp\Authorization\Models\PermissionGroup
 *
 * @property string $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Authorization\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @method static \Illuminate\Database\Eloquent\Builder|PermissionGroup newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PermissionGroup newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PermissionGroup onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|PermissionGroup query()
 * @method static \Illuminate\Database\Eloquent\Builder|PermissionGroup whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PermissionGroup whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PermissionGroup whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PermissionGroup whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PermissionGroup whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PermissionGroup withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|PermissionGroup withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperPermissionGroup {}
}

namespace AidingApp\Authorization\Models{
/**
 * AidingApp\Authorization\Models\Role
 *
 * @property string $id
 * @property string $name
 * @property string $guard_name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $description
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Authorization\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder|Role api()
 * @method static \AidingApp\Authorization\Database\Factories\RoleFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Role newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Role newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Role permission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder|Role query()
 * @method static \Illuminate\Database\Eloquent\Builder|Role superAdmin()
 * @method static \Illuminate\Database\Eloquent\Builder|Role web()
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereGuardName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperRole {}
}

namespace AidingApp\Contact\Models{
/**
 * AidingApp\Contact\Models\Contact
 *
 * @property string $display_name
 * @property string $id
 * @property string $status_id
 * @property string $source_id
 * @property string $first_name
 * @property string $last_name
 * @property string $full_name
 * @property string|null $preferred
 * @property string|null $description
 * @property string|null $email
 * @property string|null $mobile
 * @property bool $sms_opt_out
 * @property bool $email_bounce
 * @property string|null $phone
 * @property string|null $address
 * @property string|null $address_2
 * @property string|null $address_3
 * @property string|null $city
 * @property string|null $state
 * @property string|null $postal
 * @property string|null $assigned_to_id
 * @property string|null $created_by_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string|null $organization_id
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Alert\Models\Alert> $alerts
 * @property-read int|null $alerts_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\InventoryManagement\Models\AssetCheckIn> $assetCheckIns
 * @property-read int|null $asset_check_ins_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\InventoryManagement\Models\AssetCheckOut> $assetCheckOuts
 * @property-read int|null $asset_check_outs_count
 * @property-read \App\Models\User|null $assignedTo
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\User|null $createdBy
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Engagement\Models\EngagementFile> $engagementFiles
 * @property-read int|null $engagement_files_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Engagement\Models\EngagementResponse> $engagementResponses
 * @property-read int|null $engagement_responses_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Engagement\Models\Engagement> $engagements
 * @property-read int|null $engagements_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Portal\Models\KnowledgeBaseArticleVote> $knowledgeBaseArticleVotes
 * @property-read int|null $knowledge_base_article_votes_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Engagement\Models\EngagementResponse> $orderedEngagementResponses
 * @property-read int|null $ordered_engagement_responses_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Engagement\Models\Engagement> $orderedEngagements
 * @property-read int|null $ordered_engagements_count
 * @property-read \AidingApp\Contact\Models\Organization|null $organization
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\LicenseManagement\Models\ProductLicense> $productLicenses
 * @property-read int|null $product_licenses_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\ServiceManagement\Models\ServiceRequest> $serviceRequests
 * @property-read int|null $service_requests_count
 * @property-read \AidingApp\Contact\Models\ContactSource $source
 * @property-read \AidingApp\Contact\Models\ContactStatus $status
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $subscribedUsers
 * @property-read int|null $subscribed_users_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Notification\Models\Subscription> $subscriptions
 * @property-read int|null $subscriptions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Task\Models\Task> $tasks
 * @property-read int|null $tasks_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \AidingApp\Contact\Database\Factories\ContactFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Contact newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Contact newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Contact onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Contact query()
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereAddress2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereAddress3($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereAssignedToId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereCreatedById($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereEmailBounce($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereFullName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereOrganizationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact wherePostal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact wherePreferred($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereSmsOptOut($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereSourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contact withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Contact withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperContact {}
}

namespace AidingApp\Contact\Models{
/**
 * AidingApp\Contact\Models\ContactSource
 *
 * @property string $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Contact\Models\Contact> $contacts
 * @property-read int|null $contacts_count
 * @method static \AidingApp\Contact\Database\Factories\ContactSourceFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|ContactSource newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ContactSource newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ContactSource onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ContactSource query()
 * @method static \Illuminate\Database\Eloquent\Builder|ContactSource whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContactSource whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContactSource whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContactSource whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContactSource whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContactSource withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ContactSource withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperContactSource {}
}

namespace AidingApp\Contact\Models{
/**
 * AidingApp\Contact\Models\ContactStatus
 *
 * @property string $id
 * @property \AidingApp\Contact\Enums\SystemContactClassification $classification
 * @property string $name
 * @property \AidingApp\Contact\Enums\ContactStatusColorOptions $color
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Contact\Models\Contact> $contacts
 * @property-read int|null $contacts_count
 * @method static \AidingApp\Contact\Database\Factories\ContactStatusFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|ContactStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ContactStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ContactStatus onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ContactStatus query()
 * @method static \Illuminate\Database\Eloquent\Builder|ContactStatus whereClassification($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContactStatus whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContactStatus whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContactStatus whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContactStatus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContactStatus whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContactStatus whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContactStatus withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ContactStatus withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperContactStatus {}
}

namespace AidingApp\Contact\Models{
/**
 * AidingApp\Contact\Models\Organization
 *
 * @property string $id
 * @property string $name
 * @property string|null $email
 * @property string|null $phone_number
 * @property string|null $website
 * @property string|null $industry_id
 * @property string|null $type_id
 * @property string|null $description
 * @property int|null $number_of_employees
 * @property string|null $address
 * @property string|null $city
 * @property string|null $state
 * @property string|null $postalcode
 * @property string|null $country
 * @property string|null $linkedin_url
 * @property string|null $facebook_url
 * @property string|null $twitter_url
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property array|null $domains
 * @property bool $is_contact_generation_enabled
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Contact\Models\Contact> $contacts
 * @property-read int|null $contacts_count
 * @property-read \AidingApp\Contact\Models\OrganizationIndustry|null $industry
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \AidingApp\Contact\Models\OrganizationType|null $type
 * @method static \AidingApp\Contact\Database\Factories\OrganizationFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Organization newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Organization newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Organization onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Organization query()
 * @method static \Illuminate\Database\Eloquent\Builder|Organization whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organization whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organization whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organization whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organization whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organization whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organization whereDomains($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organization whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organization whereFacebookUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organization whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organization whereIndustryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organization whereIsContactGenerationEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organization whereLinkedinUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organization whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organization whereNumberOfEmployees($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organization wherePhoneNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organization wherePostalcode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organization whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organization whereTwitterUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organization whereTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organization whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organization whereWebsite($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Organization withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Organization withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperOrganization {}
}

namespace AidingApp\Contact\Models{
/**
 * AidingApp\Contact\Models\OrganizationIndustry
 *
 * @property string $id
 * @property string $name
 * @property bool $is_default
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Contact\Models\Organization> $organizations
 * @property-read int|null $organizations_count
 * @method static \AidingApp\Contact\Database\Factories\OrganizationIndustryFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizationIndustry newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizationIndustry newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizationIndustry onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizationIndustry query()
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizationIndustry whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizationIndustry whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizationIndustry whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizationIndustry whereIsDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizationIndustry whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizationIndustry whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizationIndustry withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizationIndustry withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperOrganizationIndustry {}
}

namespace AidingApp\Contact\Models{
/**
 * AidingApp\Contact\Models\OrganizationType
 *
 * @property string $id
 * @property string $name
 * @property bool $is_default
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Contact\Models\Organization> $organizations
 * @property-read int|null $organizations_count
 * @method static \AidingApp\Contact\Database\Factories\OrganizationTypeFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizationType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizationType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizationType onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizationType query()
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizationType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizationType whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizationType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizationType whereIsDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizationType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizationType whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizationType withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|OrganizationType withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperOrganizationType {}
}

namespace AidingApp\ContractManagement\Models{
/**
 * AidingApp\ContractManagement\Models\Contract
 *
 * @property string $id
 * @property string $name
 * @property string|null $description
 * @property string $contract_type_id
 * @property string $vendor_name
 * @property string $start_date
 * @property string $end_date
 * @property mixed|null $contract_value
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \AidingApp\ContractManagement\Models\ContractType $contractType
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read mixed $status
 * @method static \AidingApp\ContractManagement\Database\Factories\ContractFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Contract newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Contract newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Contract onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Contract query()
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereContractTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereContractValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract whereVendorName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Contract withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Contract withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperContract {}
}

namespace AidingApp\ContractManagement\Models{
/**
 * AidingApp\ContractManagement\Models\ContractType
 *
 * @property string $id
 * @property string $name
 * @property bool $is_default
 * @property int $order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\ContractManagement\Models\Contract> $contracts
 * @property-read int|null $contracts_count
 * @method static \AidingApp\ContractManagement\Database\Factories\ContractTypeFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|ContractType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ContractType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ContractType onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ContractType query()
 * @method static \Illuminate\Database\Eloquent\Builder|ContractType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractType whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractType whereIsDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractType whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractType whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ContractType withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ContractType withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperContractType {}
}

namespace AidingApp\Division\Models{
/**
 * AidingApp\Division\Models\Division
 *
 * @property string $id
 * @property string $name
 * @property string|null $description
 * @property string $code
 * @property string|null $header
 * @property string|null $footer
 * @property string|null $created_by_id
 * @property string|null $last_updated_by_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\User|null $createdBy
 * @property-read \App\Models\User|null $lastUpdatedBy
 * @property-read \App\Models\NotificationSettingPivot|null $notificationSetting
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Team\Models\Team> $teams
 * @property-read int|null $teams_count
 * @method static \AidingApp\Division\Database\Factories\DivisionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Division newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Division newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Division onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Division query()
 * @method static \Illuminate\Database\Eloquent\Builder|Division whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Division whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Division whereCreatedById($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Division whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Division whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Division whereFooter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Division whereHeader($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Division whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Division whereLastUpdatedById($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Division whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Division whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Division withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Division withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperDivision {}
}

namespace AidingApp\Engagement\Models{
/**
 * AidingApp\Engagement\Models\EmailTemplate
 *
 * @property string $id
 * @property string $name
 * @property string|null $description
 * @property array $content
 * @property string|null $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \App\Models\User|null $user
 * @method static \AidingApp\Engagement\Database\Factories\EmailTemplateFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|EmailTemplate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EmailTemplate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EmailTemplate onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EmailTemplate query()
 * @method static \Illuminate\Database\Eloquent\Builder|EmailTemplate whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EmailTemplate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EmailTemplate whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EmailTemplate whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EmailTemplate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EmailTemplate whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EmailTemplate whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EmailTemplate whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EmailTemplate withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EmailTemplate withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperEmailTemplate {}
}

namespace AidingApp\Engagement\Models{
/**
 * AidingApp\Engagement\Models\Engagement
 *
 * @property-read Educatable $recipient
 * @property string $id
 * @property string|null $user_id
 * @property string|null $engagement_batch_id
 * @property string|null $recipient_id
 * @property string|null $recipient_type
 * @property string|null $subject
 * @property array|null $body
 * @property bool $scheduled
 * @property \Illuminate\Support\Carbon $deliver_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \AidingApp\Engagement\Models\EngagementBatch|null $batch
 * @property-read \App\Models\User|null $createdBy
 * @property-read \AidingApp\Engagement\Models\EngagementDeliverable|null $deliverable
 * @property-read \AidingApp\Engagement\Models\EngagementBatch|null $engagementBatch
 * @property-read \AidingApp\Engagement\Models\EngagementDeliverable|null $engagementDeliverable
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \AidingApp\Timeline\Models\Timeline|null $timelineRecord
 * @property-read \App\Models\User|null $user
 * @method static \AidingApp\Engagement\Database\Factories\EngagementFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Engagement hasBeenDelivered()
 * @method static \Illuminate\Database\Eloquent\Builder|Engagement hasNotBeenDelivered()
 * @method static \Illuminate\Database\Eloquent\Builder|Engagement isAwaitingDelivery()
 * @method static \Illuminate\Database\Eloquent\Builder|Engagement isNotPartOfABatch()
 * @method static \Illuminate\Database\Eloquent\Builder|Engagement isScheduled()
 * @method static \Illuminate\Database\Eloquent\Builder|Engagement licensedToEducatable(string $relationship)
 * @method static \Illuminate\Database\Eloquent\Builder|Engagement newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Engagement newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Engagement onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Engagement query()
 * @method static \Illuminate\Database\Eloquent\Builder|Engagement sentToContact()
 * @method static \Illuminate\Database\Eloquent\Builder|Engagement whereBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Engagement whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Engagement whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Engagement whereDeliverAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Engagement whereEngagementBatchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Engagement whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Engagement whereRecipientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Engagement whereRecipientType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Engagement whereScheduled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Engagement whereSubject($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Engagement whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Engagement whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Engagement withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Engagement withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperEngagement {}
}

namespace AidingApp\Engagement\Models{
/**
 * AidingApp\Engagement\Models\EngagementBatch
 *
 * @property string $id
 * @property string|null $identifier
 * @property string $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Engagement\Models\Engagement> $engagements
 * @property-read int|null $engagements_count
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \App\Models\User $user
 * @method static \AidingApp\Engagement\Database\Factories\EngagementBatchFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementBatch newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementBatch newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementBatch query()
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementBatch whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementBatch whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementBatch whereIdentifier($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementBatch whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementBatch whereUserId($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperEngagementBatch {}
}

namespace AidingApp\Engagement\Models{
/**
 * AidingApp\Engagement\Models\EngagementDeliverable
 *
 * @property string $id
 * @property string $engagement_id
 * @property \AidingApp\Engagement\Enums\EngagementDeliveryMethod $channel
 * @property string|null $external_reference_id
 * @property string|null $external_status
 * @property \AidingApp\Engagement\Enums\EngagementDeliveryStatus $delivery_status
 * @property \Illuminate\Support\Carbon|null $delivered_at
 * @property \Illuminate\Support\Carbon|null $last_delivery_attempt
 * @property string|null $delivery_response
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \AidingApp\Engagement\Models\Engagement $engagement
 * @method static \AidingApp\Engagement\Database\Factories\EngagementDeliverableFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementDeliverable newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementDeliverable newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementDeliverable onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementDeliverable query()
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementDeliverable whereChannel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementDeliverable whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementDeliverable whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementDeliverable whereDeliveredAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementDeliverable whereDeliveryResponse($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementDeliverable whereDeliveryStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementDeliverable whereEngagementId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementDeliverable whereExternalReferenceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementDeliverable whereExternalStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementDeliverable whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementDeliverable whereLastDeliveryAttempt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementDeliverable whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementDeliverable withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementDeliverable withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperEngagementDeliverable {}
}

namespace AidingApp\Engagement\Models{
/**
 * AidingApp\Engagement\Models\EngagementFile
 *
 * @property string $id
 * @property string $description
 * @property string|null $retention_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Contact\Models\Contact> $contacts
 * @property-read int|null $contacts_count
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @method static \AidingApp\Engagement\Database\Factories\EngagementFileFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementFile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementFile newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementFile query()
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementFile whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementFile whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementFile whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementFile whereRetentionDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementFile whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperEngagementFile {}
}

namespace AidingApp\Engagement\Models{
/**
 * AidingApp\Engagement\Models\EngagementFileEntities
 *
 * @property string $engagement_file_id
 * @property string $entity_id
 * @property string $entity_type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \AidingApp\Engagement\Models\EngagementFile $engagementFile
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $entity
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementFileEntities newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementFileEntities newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementFileEntities query()
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementFileEntities whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementFileEntities whereEngagementFileId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementFileEntities whereEntityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementFileEntities whereEntityType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementFileEntities whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperEngagementFileEntities {}
}

namespace AidingApp\Engagement\Models{
/**
 * AidingApp\Engagement\Models\EngagementResponse
 *
 * @property string $id
 * @property string|null $sender_id
 * @property string|null $sender_type
 * @property string|null $content
 * @property \Illuminate\Support\Carbon|null $sent_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $sender
 * @property-read \AidingApp\Timeline\Models\Timeline|null $timelineRecord
 * @method static \AidingApp\Engagement\Database\Factories\EngagementResponseFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementResponse newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementResponse newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementResponse onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementResponse query()
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementResponse sentByContact()
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementResponse whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementResponse whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementResponse whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementResponse whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementResponse whereSenderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementResponse whereSenderType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementResponse whereSentAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementResponse whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementResponse withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementResponse withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperEngagementResponse {}
}

namespace AidingApp\Engagement\Models{
/**
 * AidingApp\Engagement\Models\SmsTemplate
 *
 * @property string $id
 * @property string $name
 * @property string|null $description
 * @property array $content
 * @property string|null $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User|null $user
 * @method static \AidingApp\Engagement\Database\Factories\SmsTemplateFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|SmsTemplate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SmsTemplate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SmsTemplate onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|SmsTemplate query()
 * @method static \Illuminate\Database\Eloquent\Builder|SmsTemplate whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SmsTemplate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SmsTemplate whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SmsTemplate whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SmsTemplate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SmsTemplate whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SmsTemplate whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SmsTemplate whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SmsTemplate withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|SmsTemplate withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperSmsTemplate {}
}

namespace AidingApp\InAppCommunication\Models{
/**
 * AidingApp\InAppCommunication\Models\TwilioConversation
 *
 * @property string $sid
 * @property string|null $friendly_name
 * @property \AidingApp\InAppCommunication\Enums\ConversationType $type
 * @property string|null $channel_name
 * @property bool $is_private_channel
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $managers
 * @property-read int|null $managers_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $participants
 * @property-read int|null $participants_count
 * @method static \Illuminate\Database\Eloquent\Builder|TwilioConversation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TwilioConversation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TwilioConversation onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|TwilioConversation query()
 * @method static \Illuminate\Database\Eloquent\Builder|TwilioConversation whereChannelName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TwilioConversation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TwilioConversation whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TwilioConversation whereFriendlyName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TwilioConversation whereIsPrivateChannel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TwilioConversation whereSid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TwilioConversation whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TwilioConversation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TwilioConversation withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|TwilioConversation withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperTwilioConversation {}
}

namespace AidingApp\InAppCommunication\Models{
/**
 * AidingApp\InAppCommunication\Models\TwilioConversationUser
 *
 * @property string $conversation_sid
 * @property string $user_id
 * @property string $participant_sid
 * @property bool $is_channel_manager
 * @property bool $is_pinned
 * @property \AidingApp\InAppCommunication\Enums\ConversationNotificationPreference $notification_preference
 * @property string|null $first_unread_message_sid
 * @property \Carbon\CarbonImmutable|null $first_unread_message_at
 * @property string|null $last_unread_message_content
 * @property \Carbon\CarbonImmutable|null $last_read_at
 * @property int $unread_messages_count
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \AidingApp\InAppCommunication\Models\TwilioConversation $conversation
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|TwilioConversationUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TwilioConversationUser newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TwilioConversationUser query()
 * @method static \Illuminate\Database\Eloquent\Builder|TwilioConversationUser whereConversationSid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TwilioConversationUser whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TwilioConversationUser whereFirstUnreadMessageAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TwilioConversationUser whereFirstUnreadMessageSid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TwilioConversationUser whereIsChannelManager($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TwilioConversationUser whereIsPinned($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TwilioConversationUser whereLastReadAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TwilioConversationUser whereLastUnreadMessageContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TwilioConversationUser whereNotificationPreference($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TwilioConversationUser whereParticipantSid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TwilioConversationUser whereUnreadMessagesCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TwilioConversationUser whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TwilioConversationUser whereUserId($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperTwilioConversationUser {}
}

namespace AidingApp\InventoryManagement\Models{
/**
 * AidingApp\InventoryManagement\Models\Asset
 *
 * @property-read string $purchase_age
 * @property string $id
 * @property string $serial_number
 * @property string $name
 * @property string $description
 * @property string $type_id
 * @property string $status_id
 * @property string $location_id
 * @property \Illuminate\Support\Carbon $purchase_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\InventoryManagement\Models\AssetCheckIn> $checkIns
 * @property-read int|null $check_ins_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\InventoryManagement\Models\AssetCheckOut> $checkOuts
 * @property-read int|null $check_outs_count
 * @property-read \AidingApp\InventoryManagement\Models\AssetCheckIn|null $latestCheckIn
 * @property-read \AidingApp\InventoryManagement\Models\AssetCheckOut|null $latestCheckOut
 * @property-read \AidingApp\InventoryManagement\Models\AssetLocation $location
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\InventoryManagement\Models\MaintenanceActivity> $maintenanceActivities
 * @property-read int|null $maintenance_activities_count
 * @property-read \AidingApp\InventoryManagement\Models\AssetStatus $status
 * @property-read \AidingApp\InventoryManagement\Models\AssetType $type
 * @method static \AidingApp\InventoryManagement\Database\Factories\AssetFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Asset newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Asset newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Asset onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Asset query()
 * @method static \Illuminate\Database\Eloquent\Builder|Asset whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Asset whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Asset whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Asset whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Asset whereLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Asset whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Asset wherePurchaseDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Asset whereSerialNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Asset whereStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Asset whereTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Asset whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Asset withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Asset withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperAsset {}
}

namespace AidingApp\InventoryManagement\Models{
/**
 * AidingApp\InventoryManagement\Models\AssetCheckIn
 *
 * @property string $id
 * @property string $asset_id
 * @property string|null $checked_in_by_type
 * @property string|null $checked_in_by_id
 * @property string $checked_in_from_type
 * @property string $checked_in_from_id
 * @property \Illuminate\Support\Carbon $checked_in_at
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \AidingApp\InventoryManagement\Models\Asset $asset
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \AidingApp\InventoryManagement\Models\AssetCheckOut|null $checkOut
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $checkedInBy
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $checkedInFrom
 * @property-read \AidingApp\Timeline\Models\Timeline|null $timelineRecord
 * @method static \AidingApp\InventoryManagement\Database\Factories\AssetCheckInFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|AssetCheckIn newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AssetCheckIn newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AssetCheckIn onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AssetCheckIn query()
 * @method static \Illuminate\Database\Eloquent\Builder|AssetCheckIn whereAssetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssetCheckIn whereCheckedInAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssetCheckIn whereCheckedInById($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssetCheckIn whereCheckedInByType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssetCheckIn whereCheckedInFromId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssetCheckIn whereCheckedInFromType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssetCheckIn whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssetCheckIn whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssetCheckIn whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssetCheckIn whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssetCheckIn whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssetCheckIn withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AssetCheckIn withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperAssetCheckIn {}
}

namespace AidingApp\InventoryManagement\Models{
/**
 * AidingApp\InventoryManagement\Models\AssetCheckOut
 *
 * @property string $id
 * @property string $asset_id
 * @property string|null $asset_check_in_id
 * @property string|null $checked_out_by_type
 * @property string|null $checked_out_by_id
 * @property string $checked_out_to_type
 * @property string $checked_out_to_id
 * @property \Illuminate\Support\Carbon $checked_out_at
 * @property \Illuminate\Support\Carbon|null $expected_check_in_at
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \AidingApp\InventoryManagement\Models\Asset $asset
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \AidingApp\InventoryManagement\Models\AssetCheckIn|null $checkIn
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $checkedOutBy
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $checkedOutTo
 * @property-read mixed $status
 * @property-read \AidingApp\Timeline\Models\Timeline|null $timelineRecord
 * @method static \AidingApp\InventoryManagement\Database\Factories\AssetCheckOutFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|AssetCheckOut newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AssetCheckOut newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AssetCheckOut onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AssetCheckOut query()
 * @method static \Illuminate\Database\Eloquent\Builder|AssetCheckOut whereAssetCheckInId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssetCheckOut whereAssetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssetCheckOut whereCheckedOutAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssetCheckOut whereCheckedOutById($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssetCheckOut whereCheckedOutByType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssetCheckOut whereCheckedOutToId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssetCheckOut whereCheckedOutToType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssetCheckOut whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssetCheckOut whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssetCheckOut whereExpectedCheckInAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssetCheckOut whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssetCheckOut whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssetCheckOut whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssetCheckOut withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AssetCheckOut withoutReturned()
 * @method static \Illuminate\Database\Eloquent\Builder|AssetCheckOut withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperAssetCheckOut {}
}

namespace AidingApp\InventoryManagement\Models{
/**
 * AidingApp\InventoryManagement\Models\AssetLocation
 *
 * @property string $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\InventoryManagement\Models\Asset> $assets
 * @property-read int|null $assets_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @method static \AidingApp\InventoryManagement\Database\Factories\AssetLocationFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|AssetLocation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AssetLocation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AssetLocation onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AssetLocation query()
 * @method static \Illuminate\Database\Eloquent\Builder|AssetLocation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssetLocation whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssetLocation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssetLocation whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssetLocation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssetLocation withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AssetLocation withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperAssetLocation {}
}

namespace AidingApp\InventoryManagement\Models{
/**
 * AidingApp\InventoryManagement\Models\AssetStatus
 *
 * @property string $id
 * @property \AidingApp\InventoryManagement\Enums\SystemAssetStatusClassification $classification
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\InventoryManagement\Models\Asset> $assets
 * @property-read int|null $assets_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @method static \AidingApp\InventoryManagement\Database\Factories\AssetStatusFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|AssetStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AssetStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AssetStatus onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AssetStatus query()
 * @method static \Illuminate\Database\Eloquent\Builder|AssetStatus whereClassification($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssetStatus whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssetStatus whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssetStatus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssetStatus whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssetStatus whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssetStatus withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AssetStatus withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperAssetStatus {}
}

namespace AidingApp\InventoryManagement\Models{
/**
 * AidingApp\InventoryManagement\Models\AssetType
 *
 * @property string $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\InventoryManagement\Models\Asset> $assets
 * @property-read int|null $assets_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @method static \AidingApp\InventoryManagement\Database\Factories\AssetTypeFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|AssetType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AssetType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AssetType onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AssetType query()
 * @method static \Illuminate\Database\Eloquent\Builder|AssetType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssetType whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssetType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssetType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssetType whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AssetType withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AssetType withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperAssetType {}
}

namespace AidingApp\InventoryManagement\Models{
/**
 * AidingApp\InventoryManagement\Models\MaintenanceActivity
 *
 * @property string $id
 * @property string $asset_id
 * @property string|null $maintenance_provider_id
 * @property string $details
 * @property \Illuminate\Support\Carbon|null $scheduled_date
 * @property \Illuminate\Support\Carbon|null $completed_date
 * @property \AidingApp\InventoryManagement\Enums\MaintenanceActivityStatus $status
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \AidingApp\InventoryManagement\Models\Asset $asset
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \AidingApp\InventoryManagement\Models\MaintenanceProvider|null $maintenanceProvider
 * @property-read \AidingApp\Timeline\Models\Timeline|null $timelineRecord
 * @method static \AidingApp\InventoryManagement\Database\Factories\MaintenanceActivityFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|MaintenanceActivity newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MaintenanceActivity newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MaintenanceActivity onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|MaintenanceActivity query()
 * @method static \Illuminate\Database\Eloquent\Builder|MaintenanceActivity whereAssetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MaintenanceActivity whereCompletedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MaintenanceActivity whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MaintenanceActivity whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MaintenanceActivity whereDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MaintenanceActivity whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MaintenanceActivity whereMaintenanceProviderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MaintenanceActivity whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MaintenanceActivity whereScheduledDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MaintenanceActivity whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MaintenanceActivity whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MaintenanceActivity withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|MaintenanceActivity withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperMaintenanceActivity {}
}

namespace AidingApp\InventoryManagement\Models{
/**
 * AidingApp\InventoryManagement\Models\MaintenanceProvider
 *
 * @property string $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\InventoryManagement\Models\MaintenanceActivity> $maintenanceActivities
 * @property-read int|null $maintenance_activities_count
 * @method static \AidingApp\InventoryManagement\Database\Factories\MaintenanceProviderFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|MaintenanceProvider newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MaintenanceProvider newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MaintenanceProvider onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|MaintenanceProvider query()
 * @method static \Illuminate\Database\Eloquent\Builder|MaintenanceProvider whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MaintenanceProvider whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MaintenanceProvider whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MaintenanceProvider whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MaintenanceProvider whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MaintenanceProvider withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|MaintenanceProvider withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperMaintenanceProvider {}
}

namespace AidingApp\KnowledgeBase\Models{
/**
 * AidingApp\KnowledgeBase\Models\KnowledgeBaseCategory
 *
 * @property string $id
 * @property string $name
 * @property string|null $description
 * @property string|null $icon
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string $slug
 * @property string|null $parent_id
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\KnowledgeBase\Models\KnowledgeBaseItem> $knowledgeBaseItems
 * @property-read int|null $knowledge_base_items_count
 * @property-read KnowledgeBaseCategory|null $parentCategory
 * @property-read \Illuminate\Database\Eloquent\Collection<int, KnowledgeBaseCategory> $subCategories
 * @property-read int|null $sub_categories_count
 * @method static \AidingApp\KnowledgeBase\Database\Factories\KnowledgeBaseCategoryFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseCategory onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseCategory whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseCategory whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseCategory whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseCategory whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseCategory whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseCategory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseCategory withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseCategory withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperKnowledgeBaseCategory {}
}

namespace AidingApp\KnowledgeBase\Models{
/**
 * AidingApp\KnowledgeBase\Models\KnowledgeBaseItem
 *
 * @property string $id
 * @property bool $public
 * @property string $title
 * @property array|null $article_details
 * @property string|null $notes
 * @property string|null $quality_id
 * @property string|null $status_id
 * @property string|null $category_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int $portal_view_count
 * @property bool $is_featured
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \AidingApp\KnowledgeBase\Models\KnowledgeBaseCategory|null $category
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Division\Models\Division> $division
 * @property-read int|null $division_count
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \AidingApp\KnowledgeBase\Models\KnowledgeBaseQuality|null $quality
 * @property-read \AidingApp\KnowledgeBase\Models\KnowledgeBaseStatus|null $status
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tag> $tags
 * @property-read int|null $tags_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Portal\Models\KnowledgeBaseArticleVote> $votes
 * @property-read int|null $votes_count
 * @method static \AidingApp\KnowledgeBase\Database\Factories\KnowledgeBaseItemFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseItem onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseItem public()
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseItem query()
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseItem whereArticleDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseItem whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseItem whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseItem whereIsFeatured($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseItem whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseItem wherePortalViewCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseItem wherePublic($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseItem whereQualityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseItem whereStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseItem whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseItem whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseItem withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseItem withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperKnowledgeBaseItem {}
}

namespace AidingApp\KnowledgeBase\Models{
/**
 * AidingApp\KnowledgeBase\Models\KnowledgeBaseQuality
 *
 * @property string $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\KnowledgeBase\Models\KnowledgeBaseItem> $knowledgeBaseItems
 * @property-read int|null $knowledge_base_items_count
 * @method static \AidingApp\KnowledgeBase\Database\Factories\KnowledgeBaseQualityFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseQuality newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseQuality newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseQuality onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseQuality query()
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseQuality whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseQuality whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseQuality whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseQuality whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseQuality whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseQuality withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseQuality withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperKnowledgeBaseQuality {}
}

namespace AidingApp\KnowledgeBase\Models{
/**
 * AidingApp\KnowledgeBase\Models\KnowledgeBaseStatus
 *
 * @property string $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\KnowledgeBase\Models\KnowledgeBaseItem> $knowledgeBaseItems
 * @property-read int|null $knowledge_base_items_count
 * @method static \AidingApp\KnowledgeBase\Database\Factories\KnowledgeBaseStatusFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseStatus onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseStatus query()
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseStatus whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseStatus whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseStatus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseStatus whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseStatus whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseStatus withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseStatus withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperKnowledgeBaseStatus {}
}

namespace AidingApp\LicenseManagement\Models{
/**
 * AidingApp\LicenseManagement\Models\Product
 *
 * @property string $id
 * @property string $name
 * @property string|null $url
 * @property string|null $description
 * @property string|null $version
 * @property string|null $additional_notes
 * @property string|null $created_by_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\LicenseManagement\Models\ProductLicense> $productLicenses
 * @property-read int|null $product_licenses_count
 * @method static \AidingApp\LicenseManagement\Database\Factories\ProductFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Product newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Product newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Product onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Product query()
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereAdditionalNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereCreatedById($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Product withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperProduct {}
}

namespace AidingApp\LicenseManagement\Models{
/**
 * AidingApp\LicenseManagement\Models\ProductLicense
 *
 * @property string $id
 * @property string $product_id
 * @property mixed $license
 * @property string|null $description
 * @property string|null $assigned_to
 * @property \Illuminate\Support\Carbon $start_date
 * @property \Illuminate\Support\Carbon|null $expiration_date
 * @property string|null $created_by_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \AidingApp\Contact\Models\Contact|null $contact
 * @property-read \AidingApp\LicenseManagement\Models\Product $product
 * @property-read \AidingApp\LicenseManagement\Enums\ProductLicenseStatus $status
 * @method static \AidingApp\LicenseManagement\Database\Factories\ProductLicenseFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|ProductLicense newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductLicense newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductLicense onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductLicense query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductLicense whereAssignedTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductLicense whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductLicense whereCreatedById($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductLicense whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductLicense whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductLicense whereExpirationDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductLicense whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductLicense whereLicense($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductLicense whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductLicense whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductLicense whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProductLicense withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ProductLicense withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperProductLicense {}
}

namespace AidingApp\Notification\Models{
/**
 * AidingApp\Notification\Models\OutboundDeliverable
 *
 * @property string $id
 * @property \AidingApp\Notification\Enums\NotificationChannel $channel
 * @property string $notification_class
 * @property string|null $external_reference_id
 * @property string|null $external_status
 * @property array|null $content
 * @property \AidingApp\Notification\Enums\NotificationDeliveryStatus $delivery_status
 * @property string|null $delivery_response
 * @property int $quota_usage
 * @property string|null $related_id
 * @property string|null $related_type
 * @property string|null $recipient_id
 * @property string|null $recipient_type
 * @property \Illuminate\Support\Carbon|null $delivered_at
 * @property \Illuminate\Support\Carbon|null $last_delivery_attempt
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $recipient
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $related
 * @method static \AidingApp\Notification\Database\Factories\OutboundDeliverableFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|OutboundDeliverable newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OutboundDeliverable newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OutboundDeliverable onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|OutboundDeliverable query()
 * @method static \Illuminate\Database\Eloquent\Builder|OutboundDeliverable whereChannel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutboundDeliverable whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutboundDeliverable whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutboundDeliverable whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutboundDeliverable whereDeliveredAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutboundDeliverable whereDeliveryResponse($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutboundDeliverable whereDeliveryStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutboundDeliverable whereExternalReferenceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutboundDeliverable whereExternalStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutboundDeliverable whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutboundDeliverable whereLastDeliveryAttempt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutboundDeliverable whereNotificationClass($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutboundDeliverable whereQuotaUsage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutboundDeliverable whereRecipientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutboundDeliverable whereRecipientType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutboundDeliverable whereRelatedId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutboundDeliverable whereRelatedType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutboundDeliverable whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OutboundDeliverable withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|OutboundDeliverable withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperOutboundDeliverable {}
}

namespace AidingApp\Notification\Models{
/**
 * AidingApp\Notification\Models\Subscription
 *
 * @property string $id
 * @property string $user_id
 * @property string $subscribable_id
 * @property string $subscribable_type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $subscribable
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription licensedToEducatable(string $relationship)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription query()
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereSubscribableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereSubscribableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription whereUserId($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperSubscription {}
}

namespace AidingApp\Portal\Models{
/**
 * AidingApp\Portal\Models\KnowledgeBaseArticleVote
 *
 * @property string $id
 * @property bool $is_helpful
 * @property string $voter_type
 * @property string $voter_id
 * @property string $article_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \AidingApp\KnowledgeBase\Models\KnowledgeBaseItem $knowledgeBaseArticle
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $voter
 * @method static \AidingApp\Portal\Database\Factories\KnowledgeBaseArticleVoteFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseArticleVote newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseArticleVote newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseArticleVote query()
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseArticleVote whereArticleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseArticleVote whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseArticleVote whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseArticleVote whereIsHelpful($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseArticleVote whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseArticleVote whereVoterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|KnowledgeBaseArticleVote whereVoterType($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperKnowledgeBaseArticleVote {}
}

namespace AidingApp\Portal\Models{
/**
 * AidingApp\Portal\Models\PortalAuthentication
 *
 * @property Carbon|null $created_at
 * @property string $id
 * @property string|null $educatable_id
 * @property string|null $educatable_type
 * @property string|null $code
 * @property \AidingApp\Portal\Enums\PortalType|null $portal_type
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $educatable
 * @method static \AidingApp\Portal\Database\Factories\PortalAuthenticationFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|PortalAuthentication newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PortalAuthentication newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PortalAuthentication query()
 * @method static \Illuminate\Database\Eloquent\Builder|PortalAuthentication whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PortalAuthentication whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PortalAuthentication whereEducatableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PortalAuthentication whereEducatableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PortalAuthentication whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PortalAuthentication wherePortalType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PortalAuthentication whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperPortalAuthentication {}
}

namespace AidingApp\Portal\Models{
/**
 * AidingApp\Portal\Models\PortalGuest
 *
 * @property string $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Portal\Models\KnowledgeBaseArticleVote> $knowledgeBaseArticleVotes
 * @property-read int|null $knowledge_base_article_votes_count
 * @method static \AidingApp\Portal\Database\Factories\PortalGuestFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|PortalGuest newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PortalGuest newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PortalGuest onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|PortalGuest query()
 * @method static \Illuminate\Database\Eloquent\Builder|PortalGuest whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PortalGuest whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PortalGuest whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PortalGuest whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PortalGuest withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|PortalGuest withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperPortalGuest {}
}

namespace AidingApp\ServiceManagement\Models{
/**
 * AidingApp\ServiceManagement\Models\ChangeRequest
 *
 * @property string $id
 * @property string|null $created_by
 * @property string $change_request_type_id
 * @property string $change_request_status_id
 * @property string $title
 * @property string $description
 * @property string $reason
 * @property string $backout_strategy
 * @property int $impact
 * @property int $likelihood
 * @property int $risk_score
 * @property \Illuminate\Support\Carbon $start_time
 * @property \Illuminate\Support\Carbon $end_time
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\ServiceManagement\Models\ChangeRequestResponse> $approvals
 * @property-read int|null $approvals_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\ServiceManagement\Models\ChangeRequestResponse> $responses
 * @property-read int|null $responses_count
 * @property-read \AidingApp\ServiceManagement\Models\ChangeRequestStatus $status
 * @property-read \AidingApp\ServiceManagement\Models\ChangeRequestType $type
 * @property-read \App\Models\User|null $user
 * @method static \AidingApp\ServiceManagement\Database\Factories\ChangeRequestFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|ChangeRequest newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ChangeRequest newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ChangeRequest query()
 * @method static \Illuminate\Database\Eloquent\Builder|ChangeRequest whereBackoutStrategy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChangeRequest whereChangeRequestStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChangeRequest whereChangeRequestTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChangeRequest whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChangeRequest whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChangeRequest whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChangeRequest whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChangeRequest whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChangeRequest whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChangeRequest whereImpact($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChangeRequest whereLikelihood($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChangeRequest whereReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChangeRequest whereRiskScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChangeRequest whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChangeRequest whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChangeRequest whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperChangeRequest {}
}

namespace AidingApp\ServiceManagement\Models{
/**
 * AidingApp\ServiceManagement\Models\ChangeRequestResponse
 *
 * @property string $id
 * @property string $change_request_id
 * @property string $user_id
 * @property bool $approved
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \AidingApp\ServiceManagement\Models\ChangeRequest $changeRequest
 * @property-read \App\Models\User $user
 * @method static \AidingApp\ServiceManagement\Database\Factories\ChangeRequestResponseFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|ChangeRequestResponse newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ChangeRequestResponse newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ChangeRequestResponse query()
 * @method static \Illuminate\Database\Eloquent\Builder|ChangeRequestResponse whereApproved($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChangeRequestResponse whereChangeRequestId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChangeRequestResponse whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChangeRequestResponse whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChangeRequestResponse whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChangeRequestResponse whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChangeRequestResponse whereUserId($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperChangeRequestResponse {}
}

namespace AidingApp\ServiceManagement\Models{
/**
 * AidingApp\ServiceManagement\Models\ChangeRequestStatus
 *
 * @property string $id
 * @property string $name
 * @property \AidingApp\ServiceManagement\Enums\SystemChangeRequestClassification $classification
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\ServiceManagement\Models\ChangeRequest> $changeRequests
 * @property-read int|null $change_requests_count
 * @method static \AidingApp\ServiceManagement\Database\Factories\ChangeRequestStatusFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|ChangeRequestStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ChangeRequestStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ChangeRequestStatus onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ChangeRequestStatus query()
 * @method static \Illuminate\Database\Eloquent\Builder|ChangeRequestStatus whereClassification($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChangeRequestStatus whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChangeRequestStatus whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChangeRequestStatus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChangeRequestStatus whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChangeRequestStatus whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChangeRequestStatus withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ChangeRequestStatus withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperChangeRequestStatus {}
}

namespace AidingApp\ServiceManagement\Models{
/**
 * AidingApp\ServiceManagement\Models\ChangeRequestType
 *
 * @property string $id
 * @property string $name
 * @property int $number_of_required_approvals
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\ServiceManagement\Models\ChangeRequest> $changeRequests
 * @property-read int|null $change_requests_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $userApprovers
 * @property-read int|null $user_approvers_count
 * @method static \AidingApp\ServiceManagement\Database\Factories\ChangeRequestTypeFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|ChangeRequestType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ChangeRequestType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ChangeRequestType onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ChangeRequestType query()
 * @method static \Illuminate\Database\Eloquent\Builder|ChangeRequestType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChangeRequestType whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChangeRequestType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChangeRequestType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChangeRequestType whereNumberOfRequiredApprovals($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChangeRequestType whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChangeRequestType withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ChangeRequestType withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperChangeRequestType {}
}

namespace AidingApp\ServiceManagement\Models{
/**
 * AidingApp\ServiceManagement\Models\ServiceRequest
 *
 * @property-read Contact $respondent
 * @property string $id
 * @property string $service_request_number
 * @property string|null $title
 * @property string $respondent_type
 * @property string $respondent_id
 * @property string|null $close_details
 * @property string|null $res_details
 * @property string|null $service_request_form_submission_id
 * @property string|null $division_id
 * @property string|null $status_id
 * @property string|null $priority_id
 * @property string|null $created_by_id
 * @property \Carbon\CarbonImmutable|null $status_updated_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $time_to_resolution
 * @property-read \AidingApp\ServiceManagement\Models\ServiceRequestAssignment|null $assignedTo
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\ServiceManagement\Models\ServiceRequestAssignment> $assignments
 * @property-read int|null $assignments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\User|null $createdBy
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Notification\Models\OutboundDeliverable> $deliverables
 * @property-read int|null $deliverables_count
 * @property-read \AidingApp\Division\Models\Division|null $division
 * @property-read \AidingApp\ServiceManagement\Models\ServiceRequestFeedback|null $feedback
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\ServiceManagement\Models\ServiceRequestHistory> $histories
 * @property-read int|null $histories_count
 * @property-read \AidingApp\ServiceManagement\Models\ServiceRequestAssignment|null $initialAssignment
 * @property-read \AidingApp\ServiceManagement\Models\ServiceRequestUpdate|null $latestInboundServiceRequestUpdate
 * @property-read \AidingApp\ServiceManagement\Models\ServiceRequestUpdate|null $latestOutboundServiceRequestUpdate
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \AidingApp\ServiceManagement\Models\ServiceRequestPriority|null $priority
 * @property-read \AidingApp\ServiceManagement\Models\ServiceRequestFormSubmission|null $serviceRequestFormSubmission
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\ServiceManagement\Models\ServiceRequestUpdate> $serviceRequestUpdates
 * @property-read int|null $service_request_updates_count
 * @property-read \AidingApp\ServiceManagement\Models\ServiceRequestStatus|null $status
 * @method static \AidingApp\ServiceManagement\Database\Factories\ServiceRequestFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequest licensedToEducatable(string $relationship)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequest newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequest newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequest onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequest open()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequest query()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequest whereCloseDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequest whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequest whereCreatedById($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequest whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequest whereDivisionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequest whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequest wherePriorityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequest whereResDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequest whereRespondentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequest whereRespondentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequest whereServiceRequestFormSubmissionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequest whereServiceRequestNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequest whereStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequest whereStatusUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequest whereTimeToResolution($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequest whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequest whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequest withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequest withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperServiceRequest {}
}

namespace AidingApp\ServiceManagement\Models{
/**
 * AidingApp\ServiceManagement\Models\ServiceRequestAssignment
 *
 * @property string $id
 * @property string $service_request_id
 * @property string $user_id
 * @property string|null $assigned_by_id
 * @property \Illuminate\Support\Carbon $assigned_at
 * @property \AidingApp\ServiceManagement\Enums\ServiceRequestAssignmentStatus $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User|null $assignedBy
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \AidingApp\ServiceManagement\Models\ServiceRequest $serviceRequest
 * @property-read \App\Models\User $user
 * @method static \AidingApp\ServiceManagement\Database\Factories\ServiceRequestAssignmentFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestAssignment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestAssignment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestAssignment onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestAssignment query()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestAssignment whereAssignedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestAssignment whereAssignedById($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestAssignment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestAssignment whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestAssignment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestAssignment whereServiceRequestId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestAssignment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestAssignment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestAssignment whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestAssignment withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestAssignment withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperServiceRequestAssignment {}
}

namespace AidingApp\ServiceManagement\Models{
/**
 * AidingApp\ServiceManagement\Models\ServiceRequestFeedback
 *
 * @property string $id
 * @property string $service_request_id
 * @property string $contact_id
 * @property int|null $csat_answer
 * @property int|null $nps_answer
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \AidingApp\Contact\Models\Contact $contact
 * @property-read \AidingApp\ServiceManagement\Models\ServiceRequest $serviceRequest
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestFeedback newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestFeedback newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestFeedback onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestFeedback query()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestFeedback whereContactId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestFeedback whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestFeedback whereCsatAnswer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestFeedback whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestFeedback whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestFeedback whereNpsAnswer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestFeedback whereServiceRequestId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestFeedback whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestFeedback withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestFeedback withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperServiceRequestFeedback {}
}

namespace AidingApp\ServiceManagement\Models{
/**
 * AidingApp\ServiceManagement\Models\ServiceRequestForm
 *
 * @property string $id
 * @property string|null $service_request_type_id
 * @property-read string $name
 * @property string|null $description
 * @property-read bool $embed_enabled
 * @property-read array|null $allowed_domains
 * @property string|null $primary_color
 * @property \AidingApp\Form\Enums\Rounding|null $rounding
 * @property bool $is_authenticated
 * @property-read bool $is_wizard
 * @property bool $recaptcha_enabled
 * @property-read array|null $content
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\ServiceManagement\Models\ServiceRequestFormField> $fields
 * @property-read int|null $fields_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\ServiceManagement\Models\ServiceRequestFormStep> $steps
 * @property-read int|null $steps_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\ServiceManagement\Models\ServiceRequestFormSubmission> $submissions
 * @property-read int|null $submissions_count
 * @property-read \AidingApp\ServiceManagement\Models\ServiceRequestType|null $type
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestForm newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestForm newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestForm query()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestForm whereAllowedDomains($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestForm whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestForm whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestForm whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestForm whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestForm whereEmbedEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestForm whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestForm whereIsAuthenticated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestForm whereIsWizard($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestForm whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestForm wherePrimaryColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestForm whereRecaptchaEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestForm whereRounding($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestForm whereServiceRequestTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestForm whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperServiceRequestForm {}
}

namespace AidingApp\ServiceManagement\Models{
/**
 * AidingApp\ServiceManagement\Models\ServiceRequestFormAuthentication
 *
 * @property string $id
 * @property string|null $author_id
 * @property string|null $author_type
 * @property string|null $code
 * @property string $service_request_form_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $author
 * @property-read \AidingApp\ServiceManagement\Models\ServiceRequestForm $submissible
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestFormAuthentication newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestFormAuthentication newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestFormAuthentication query()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestFormAuthentication whereAuthorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestFormAuthentication whereAuthorType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestFormAuthentication whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestFormAuthentication whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestFormAuthentication whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestFormAuthentication whereServiceRequestFormId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestFormAuthentication whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperServiceRequestFormAuthentication {}
}

namespace AidingApp\ServiceManagement\Models{
/**
 * AidingApp\ServiceManagement\Models\ServiceRequestFormField
 *
 * @property string $id
 * @property-read string $label
 * @property-read string $type
 * @property-read bool $is_required
 * @property-read array $config
 * @property string $service_request_form_id
 * @property string|null $service_request_form_step_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \AidingApp\ServiceManagement\Models\ServiceRequestFormStep|null $step
 * @property-read \AidingApp\ServiceManagement\Models\ServiceRequestForm $submissible
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestFormField newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestFormField newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestFormField onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestFormField query()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestFormField whereConfig($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestFormField whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestFormField whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestFormField whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestFormField whereIsRequired($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestFormField whereLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestFormField whereServiceRequestFormId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestFormField whereServiceRequestFormStepId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestFormField whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestFormField whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestFormField withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestFormField withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperServiceRequestFormField {}
}

namespace AidingApp\ServiceManagement\Models{
/**
 * AidingApp\ServiceManagement\Models\ServiceRequestFormStep
 *
 * @property string $id
 * @property-read string $label
 * @property-read array|null $content
 * @property string $service_request_form_id
 * @property int $sort
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\ServiceManagement\Models\ServiceRequestFormField> $fields
 * @property-read int|null $fields_count
 * @property-read \AidingApp\ServiceManagement\Models\ServiceRequestForm $submissible
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestFormStep newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestFormStep newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestFormStep onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestFormStep query()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestFormStep whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestFormStep whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestFormStep whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestFormStep whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestFormStep whereLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestFormStep whereServiceRequestFormId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestFormStep whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestFormStep whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestFormStep withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestFormStep withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperServiceRequestFormStep {}
}

namespace AidingApp\ServiceManagement\Models{
/**
 * AidingApp\ServiceManagement\Models\ServiceRequestFormSubmission
 *
 * @property Contact|null $author
 * @property string $id
 * @property string $service_request_form_id
 * @property string|null $service_request_priority_id
 * @property string|null $author_id
 * @property string|null $author_type
 * @property string|null $description
 * @property \Carbon\CarbonImmutable|null $submitted_at
 * @property \Carbon\CarbonImmutable|null $canceled_at
 * @property \AidingApp\Form\Enums\FormSubmissionRequestDeliveryMethod|null $request_method
 * @property string|null $request_note
 * @property string|null $requester_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\ServiceManagement\Models\ServiceRequestFormField> $fields
 * @property-read int|null $fields_count
 * @property-read \AidingApp\ServiceManagement\Models\ServiceRequestPriority|null $priority
 * @property-read \App\Models\User|null $requester
 * @property-read \AidingApp\ServiceManagement\Models\ServiceRequest|null $serviceRequest
 * @property-read \AidingApp\ServiceManagement\Models\ServiceRequestForm $submissible
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestFormSubmission canceled()
 * @method static \Illuminate\Database\Eloquent\Builder|Submission licensedToEducatable(string $relationship)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestFormSubmission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestFormSubmission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestFormSubmission notCanceled()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestFormSubmission notSubmitted()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestFormSubmission query()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestFormSubmission requested()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestFormSubmission submitted()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestFormSubmission whereAuthorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestFormSubmission whereAuthorType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestFormSubmission whereCanceledAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestFormSubmission whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestFormSubmission whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestFormSubmission whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestFormSubmission whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestFormSubmission whereRequestMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestFormSubmission whereRequestNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestFormSubmission whereRequesterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestFormSubmission whereServiceRequestFormId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestFormSubmission whereServiceRequestPriorityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestFormSubmission whereSubmittedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestFormSubmission whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperServiceRequestFormSubmission {}
}

namespace AidingApp\ServiceManagement\Models{
/**
 * AidingApp\ServiceManagement\Models\ServiceRequestHistory
 *
 * @property string $id
 * @property string $service_request_id
 * @property array $original_values
 * @property array $new_values
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read mixed $new_values_formatted
 * @property-read mixed $original_values_formatted
 * @property-read \AidingApp\ServiceManagement\Models\ServiceRequest $serviceRequest
 * @method static \AidingApp\ServiceManagement\Database\Factories\ServiceRequestHistoryFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestHistory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestHistory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestHistory onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestHistory query()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestHistory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestHistory whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestHistory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestHistory whereNewValues($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestHistory whereOriginalValues($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestHistory whereServiceRequestId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestHistory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestHistory withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestHistory withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperServiceRequestHistory {}
}

namespace AidingApp\ServiceManagement\Models{
/**
 * AidingApp\ServiceManagement\Models\ServiceRequestPriority
 *
 * @property string $id
 * @property string $name
 * @property int $order
 * @property string|null $sla_id
 * @property string $type_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\ServiceManagement\Models\ServiceRequest> $serviceRequests
 * @property-read int|null $service_requests_count
 * @property-read \AidingApp\ServiceManagement\Models\Sla|null $sla
 * @property-read \AidingApp\ServiceManagement\Models\ServiceRequestType $type
 * @method static \AidingApp\ServiceManagement\Database\Factories\ServiceRequestPriorityFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestPriority newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestPriority newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestPriority onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestPriority query()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestPriority whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestPriority whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestPriority whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestPriority whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestPriority whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestPriority whereSlaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestPriority whereTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestPriority whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestPriority withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestPriority withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperServiceRequestPriority {}
}

namespace AidingApp\ServiceManagement\Models{
/**
 * AidingApp\ServiceManagement\Models\ServiceRequestStatus
 *
 * @property string $id
 * @property \AidingApp\ServiceManagement\Enums\SystemServiceRequestClassification $classification
 * @property string $name
 * @property \AidingApp\ServiceManagement\Enums\ColumnColorOptions $color
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property bool $is_system_protected
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\ServiceManagement\Models\ServiceRequest> $serviceRequests
 * @property-read int|null $service_requests_count
 * @method static \AidingApp\ServiceManagement\Database\Factories\ServiceRequestStatusFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestStatus onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestStatus query()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestStatus whereClassification($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestStatus whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestStatus whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestStatus whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestStatus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestStatus whereIsSystemProtected($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestStatus whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestStatus whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestStatus withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestStatus withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperServiceRequestStatus {}
}

namespace AidingApp\ServiceManagement\Models{
/**
 * AidingApp\ServiceManagement\Models\ServiceRequestType
 *
 * @property string $id
 * @property string $name
 * @property string|null $description
 * @property string|null $icon
 * @property bool $has_enabled_feedback_collection
 * @property bool $has_enabled_csat
 * @property bool $has_enabled_nps
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \AidingApp\ServiceManagement\Enums\ServiceRequestTypeAssignmentTypes $assignment_type
 * @property string|null $assignment_type_individual_id
 * @property bool $is_managers_service_request_created_email_enabled
 * @property bool $is_managers_service_request_created_notification_enabled
 * @property bool $is_managers_service_request_assigned_email_enabled
 * @property bool $is_managers_service_request_assigned_notification_enabled
 * @property bool $is_managers_service_request_resolved_email_enabled
 * @property bool $is_managers_service_request_resolved_notification_enabled
 * @property bool $is_auditors_service_request_created_email_enabled
 * @property bool $is_auditors_service_request_created_notification_enabled
 * @property bool $is_auditors_service_request_assigned_email_enabled
 * @property bool $is_auditors_service_request_assigned_notification_enabled
 * @property bool $is_auditors_service_request_resolved_email_enabled
 * @property bool $is_auditors_service_request_resolved_notification_enabled
 * @property string|null $last_assigned_id
 * @property-read \App\Models\User|null $assignmentTypeIndividual
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Team\Models\Team> $auditors
 * @property-read int|null $auditors_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \AidingApp\ServiceManagement\Models\ServiceRequestForm|null $form
 * @property-read \App\Models\User|null $lastAssignedUser
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Team\Models\Team> $managers
 * @property-read int|null $managers_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\ServiceManagement\Models\ServiceRequestPriority> $priorities
 * @property-read int|null $priorities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\ServiceManagement\Models\ServiceRequest> $serviceRequests
 * @property-read int|null $service_requests_count
 * @method static \AidingApp\ServiceManagement\Database\Factories\ServiceRequestTypeFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestType onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestType query()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestType whereAssignmentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestType whereAssignmentTypeIndividualId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestType whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestType whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestType whereHasEnabledCsat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestType whereHasEnabledFeedbackCollection($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestType whereHasEnabledNps($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestType whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestType whereIsAuditorsServiceRequestAssignedEmailEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestType whereIsAuditorsServiceRequestAssignedNotificationEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestType whereIsAuditorsServiceRequestCreatedEmailEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestType whereIsAuditorsServiceRequestCreatedNotificationEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestType whereIsAuditorsServiceRequestResolvedEmailEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestType whereIsAuditorsServiceRequestResolvedNotificationEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestType whereIsManagersServiceRequestAssignedEmailEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestType whereIsManagersServiceRequestAssignedNotificationEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestType whereIsManagersServiceRequestCreatedEmailEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestType whereIsManagersServiceRequestCreatedNotificationEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestType whereIsManagersServiceRequestResolvedEmailEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestType whereIsManagersServiceRequestResolvedNotificationEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestType whereLastAssignedId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestType whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestType withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestType withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperServiceRequestType {}
}

namespace AidingApp\ServiceManagement\Models{
/**
 * AidingApp\ServiceManagement\Models\ServiceRequestTypeAuditor
 *
 * @property string $id
 * @property string $service_request_type_id
 * @property string $team_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \AidingApp\ServiceManagement\Models\ServiceRequestType $serviceRequestType
 * @property-read \AidingApp\Team\Models\Team $team
 * @method static \AidingApp\ServiceManagement\Database\Factories\ServiceRequestTypeAuditorFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestTypeAuditor newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestTypeAuditor newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestTypeAuditor query()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestTypeAuditor whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestTypeAuditor whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestTypeAuditor whereServiceRequestTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestTypeAuditor whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestTypeAuditor whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperServiceRequestTypeAuditor {}
}

namespace AidingApp\ServiceManagement\Models{
/**
 * AidingApp\ServiceManagement\Models\ServiceRequestTypeManager
 *
 * @property string $id
 * @property string $service_request_type_id
 * @property string $team_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \AidingApp\ServiceManagement\Models\ServiceRequestType $serviceRequestType
 * @property-read \AidingApp\Team\Models\Team $team
 * @method static \AidingApp\ServiceManagement\Database\Factories\ServiceRequestTypeManagerFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestTypeManager newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestTypeManager newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestTypeManager query()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestTypeManager whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestTypeManager whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestTypeManager whereServiceRequestTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestTypeManager whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestTypeManager whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperServiceRequestTypeManager {}
}

namespace AidingApp\ServiceManagement\Models{
/**
 * AidingApp\ServiceManagement\Models\ServiceRequestUpdate
 *
 * @property string $id
 * @property string|null $service_request_id
 * @property string $update
 * @property bool $internal
 * @property \AidingApp\ServiceManagement\Enums\ServiceRequestUpdateDirection $direction
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \AidingApp\ServiceManagement\Models\ServiceRequest|null $serviceRequest
 * @method static \AidingApp\ServiceManagement\Database\Factories\ServiceRequestUpdateFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestUpdate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestUpdate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestUpdate onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestUpdate query()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestUpdate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestUpdate whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestUpdate whereDirection($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestUpdate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestUpdate whereInternal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestUpdate whereServiceRequestId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestUpdate whereUpdate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestUpdate whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestUpdate withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|ServiceRequestUpdate withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperServiceRequestUpdate {}
}

namespace AidingApp\ServiceManagement\Models{
/**
 * AidingApp\ServiceManagement\Models\Sla
 *
 * @property string $id
 * @property string $name
 * @property string|null $description
 * @property int|null $response_seconds
 * @property int|null $resolution_seconds
 * @property string|null $terms
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\ServiceManagement\Models\ServiceRequestPriority> $serviceRequestPriorities
 * @property-read int|null $service_request_priorities_count
 * @method static \Illuminate\Database\Eloquent\Builder|Sla newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Sla newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Sla onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Sla query()
 * @method static \Illuminate\Database\Eloquent\Builder|Sla whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sla whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sla whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sla whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sla whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sla whereResolutionSeconds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sla whereResponseSeconds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sla whereTerms($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sla whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sla withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Sla withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperSla {}
}

namespace AidingApp\Task\Models{
/**
 * AidingApp\Task\Models\Task
 *
 * @property-read Contact $concern
 * @property string $id
 * @property string $title
 * @property string $description
 * @property \AidingApp\Task\Enums\TaskStatus $status
 * @property \Illuminate\Support\Carbon|null $due
 * @property string|null $assigned_to
 * @property string|null $created_by
 * @property string|null $concern_type
 * @property string|null $concern_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User|null $assignedTo
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\User|null $createdBy
 * @method static \Illuminate\Database\Eloquent\Builder|Task byNextDue()
 * @method static \AidingApp\Task\Database\Factories\TaskFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Task newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Task newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Task onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Task open()
 * @method static \Illuminate\Database\Eloquent\Builder|Task query()
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereAssignedTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereConcernId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereConcernType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereDue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Task withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Task withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperTask {}
}

namespace AidingApp\Team\Models{
/**
 * AidingApp\Team\Models\Team
 *
 * @property string $id
 * @property string $name
 * @property string|null $description
 * @property string|null $division_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\ServiceManagement\Models\ServiceRequestType> $auditableServiceRequestTypes
 * @property-read int|null $auditable_service_request_types_count
 * @property-read \AidingApp\Division\Models\Division|null $division
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\ServiceManagement\Models\ServiceRequestType> $managableServiceRequestTypes
 * @property-read int|null $managable_service_request_types_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \AidingApp\Team\Database\Factories\TeamFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Team newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Team newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Team query()
 * @method static \Illuminate\Database\Eloquent\Builder|Team whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Team whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Team whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Team whereDivisionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Team whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Team whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Team whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperTeam {}
}

namespace AidingApp\Team\Models{
/**
 * AidingApp\Team\Models\TeamUser
 *
 * @property string $id
 * @property string $team_id
 * @property string $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \AidingApp\Team\Models\Team $team
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|TeamUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TeamUser newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TeamUser query()
 * @method static \Illuminate\Database\Eloquent\Builder|TeamUser whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TeamUser whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TeamUser whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TeamUser whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TeamUser whereUserId($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperTeamUser {}
}

namespace AidingApp\Timeline\Models{
/**
 * AidingApp\Timeline\Models\Timeline
 *
 * @property string $id
 * @property string $entity_type
 * @property string $entity_id
 * @property string $timelineable_type
 * @property string $timelineable_id
 * @property string $record_sortable_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $timelineable
 * @method static \Illuminate\Database\Eloquent\Builder|Timeline forEntity(\Illuminate\Database\Eloquent\Model $entity)
 * @method static \Illuminate\Database\Eloquent\Builder|Timeline newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Timeline newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Timeline onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Timeline query()
 * @method static \Illuminate\Database\Eloquent\Builder|Timeline whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Timeline whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Timeline whereEntityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Timeline whereEntityType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Timeline whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Timeline whereRecordSortableDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Timeline whereTimelineableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Timeline whereTimelineableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Timeline whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Timeline withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Timeline withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperTimeline {}
}

namespace AidingApp\Webhook\Models{
/**
 * AidingApp\Webhook\Models\InboundWebhook
 *
 * @property string $id
 * @property \AidingApp\Webhook\Enums\InboundWebhookSource $source
 * @property string $event
 * @property string $url
 * @property string $payload
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|InboundWebhook newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InboundWebhook newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InboundWebhook query()
 * @method static \Illuminate\Database\Eloquent\Builder|InboundWebhook whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InboundWebhook whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InboundWebhook whereEvent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InboundWebhook whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InboundWebhook wherePayload($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InboundWebhook whereSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InboundWebhook whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InboundWebhook whereUrl($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperInboundWebhook {}
}

