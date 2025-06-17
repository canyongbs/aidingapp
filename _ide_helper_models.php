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
 * 
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Export newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Export newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Export onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Export query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Export whereCompletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Export whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Export whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Export whereExporter($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Export whereFileDisk($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Export whereFileName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Export whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Export whereProcessedRows($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Export whereSuccessfulRows($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Export whereTotalRows($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Export whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Export whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Export withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Export withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperExport {}
}

namespace App\Models{
/**
 * 
 *
 * @property string $id
 * @property array<array-key, mixed> $data
 * @property string $import_id
 * @property string|null $validation_error
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Import $import
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FailedImportRow newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FailedImportRow newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FailedImportRow onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FailedImportRow query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FailedImportRow whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FailedImportRow whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FailedImportRow whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FailedImportRow whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FailedImportRow whereImportId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FailedImportRow whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FailedImportRow whereValidationError($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FailedImportRow withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FailedImportRow withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperFailedImportRow {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $check_name
 * @property string $check_label
 * @property string $status
 * @property string|null $notification_message
 * @property string|null $short_summary
 * @property array<array-key, mixed> $meta
 * @property string $ended_at
 * @property string $batch
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HealthCheckResultHistoryItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HealthCheckResultHistoryItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HealthCheckResultHistoryItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HealthCheckResultHistoryItem whereBatch($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HealthCheckResultHistoryItem whereCheckLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HealthCheckResultHistoryItem whereCheckName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HealthCheckResultHistoryItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HealthCheckResultHistoryItem whereEndedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HealthCheckResultHistoryItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HealthCheckResultHistoryItem whereMeta($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HealthCheckResultHistoryItem whereNotificationMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HealthCheckResultHistoryItem whereShortSummary($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HealthCheckResultHistoryItem whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HealthCheckResultHistoryItem whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperHealthCheckResultHistoryItem {}
}

namespace App\Models{
/**
 * 
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Import newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Import newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Import onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Import query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Import whereCompletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Import whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Import whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Import whereFileName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Import whereFilePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Import whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Import whereImporter($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Import whereProcessedRows($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Import whereSuccessfulRows($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Import whereTotalRows($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Import whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Import whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Import withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Import withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperImport {}
}

namespace App\Models{
/**
 * 
 *
 * @property string $id
 * @property string $group
 * @property string $name
 * @property bool $locked
 * @property string $payload
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandlordSettingsProperty newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandlordSettingsProperty newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandlordSettingsProperty query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandlordSettingsProperty whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandlordSettingsProperty whereGroup($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandlordSettingsProperty whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandlordSettingsProperty whereLocked($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandlordSettingsProperty whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandlordSettingsProperty wherePayload($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandlordSettingsProperty whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperLandlordSettingsProperty {}
}

namespace App\Models{
/**
 * 
 *
 * @property string $id
 * @property string $name
 * @property string|null $primary_color
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string|null $from_name
 * @property-read \App\Models\NotificationSettingPivot|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Division\Models\Division> $divisions
 * @property-read int|null $divisions_count
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\NotificationSettingPivot> $settings
 * @property-read int|null $settings_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationSetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationSetting onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationSetting whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationSetting whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationSetting whereFromName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationSetting whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationSetting wherePrimaryColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationSetting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationSetting withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationSetting withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperNotificationSetting {}
}

namespace App\Models{
/**
 * 
 *
 * @property string $id
 * @property string $notification_setting_id
 * @property string $related_to_type
 * @property string $related_to_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model $relatedTo
 * @property-read \App\Models\NotificationSetting $setting
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationSettingPivot newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationSettingPivot newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationSettingPivot query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationSettingPivot whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationSettingPivot whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationSettingPivot whereNotificationSettingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationSettingPivot whereRelatedToId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationSettingPivot whereRelatedToType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationSettingPivot whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperNotificationSettingPivot {}
}

namespace App\Models{
/**
 * 
 *
 * @property string $id
 * @property string $label
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @method static \Database\Factories\PronounsFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pronouns newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pronouns newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pronouns onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pronouns query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pronouns whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pronouns whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pronouns whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pronouns whereLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pronouns whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pronouns withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Pronouns withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperPronouns {}
}

namespace App\Models{
/**
 * 
 *
 * @property string $id
 * @property string $group
 * @property string $name
 * @property bool $locked
 * @property string $payload
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SettingsProperty newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SettingsProperty newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SettingsProperty query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SettingsProperty whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SettingsProperty whereGroup($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SettingsProperty whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SettingsProperty whereLocked($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SettingsProperty whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SettingsProperty wherePayload($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SettingsProperty whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperSettingsProperty {}
}

namespace App\Models{
/**
 * 
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemUser newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemUser onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemUser permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemUser query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemUser role($roles, $guard = null, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemUser whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemUser whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemUser whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemUser whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemUser whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemUser withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemUser withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemUser withoutRole($roles, $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SystemUser withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperSystemUser {}
}

namespace App\Models{
/**
 * 
 *
 * @property string $id
 * @property string $name
 * @property string|null $type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Database\Factories\TagFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperTag {}
}

namespace App\Models{
/**
 * 
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereConfig($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereDomain($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereSetupComplete($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tenant withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperTenant {}
}

namespace App\Models{
/**
 * 
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
 * @property array<array-key, mixed>|null $office_hours
 * @property bool $out_of_office_is_enabled
 * @property \Illuminate\Support\Carbon|null $out_of_office_starts_at
 * @property \Illuminate\Support\Carbon|null $out_of_office_ends_at
 * @property string|null $phone_number
 * @property bool $is_phone_number_visible_on_profile
 * @property bool $working_hours_are_enabled
 * @property bool $are_working_hours_visible_on_profile
 * @property array<array-key, mixed>|null $working_hours
 * @property string|null $job_title
 * @property string|null $pronouns_id
 * @property bool $are_pronouns_visible_on_profile
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string|null $work_number
 * @property int|null $work_extension
 * @property string|null $mobile
 * @property string|null $team_id
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
 * @property-read \AidingApp\InAppCommunication\Models\TwilioConversationUser|null $participant
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\InAppCommunication\Models\TwilioConversation> $conversations
 * @property-read int|null $conversations_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Engagement\Models\EngagementBatch> $engagementBatches
 * @property-read int|null $engagement_batches_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Engagement\Models\Engagement> $engagements
 * @property-read int|null $engagements_count
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
 * @property-read \AidingApp\ServiceManagement\Models\ServiceMonitoringTargetUser|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\ServiceManagement\Models\ServiceMonitoringTarget> $serviceMonitoringTargets
 * @property-read int|null $service_monitoring_targets_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\ServiceManagement\Models\ServiceRequestAssignment> $serviceRequestAssignments
 * @property-read int|null $service_request_assignments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\ServiceManagement\Models\ServiceRequestType> $serviceRequestTypeIndividualAssignment
 * @property-read int|null $service_request_type_individual_assignment_count
 * @property-read \AidingApp\Team\Models\Team|null $team
 * @property-read \Illuminate\Database\Eloquent\Collection|\AidingApp\Authorization\Models\Permission[] $permissionsFromRoles
 * @property-read int|null $permissions_from_roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\AidingApp\ServiceManagement\Models\ServiceRequest[] $serviceRequests
 * @property-read int|null $service_requests_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User advancedFilter(array $data)
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User role($roles, $guard = null, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereArePronounsVisibleOnProfile($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAreTeamsVisibleOnProfile($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAreWorkingHoursVisibleOnProfile($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAvatarUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereBio($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmplid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereHasEnabledPublicProfile($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsBioVisibleOnProfile($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsDivisionVisibleOnProfile($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsEmailVisibleOnProfile($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsExternal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsPhoneNumberVisibleOnProfile($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereJobTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLocale($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereOfficeHours($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereOfficeHoursAreEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereOutOfOfficeEndsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereOutOfOfficeIsEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereOutOfOfficeStartsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePhoneNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePronounsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePublicProfileSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTimezone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereWorkExtension($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereWorkNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereWorkingHours($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereWorkingHoursAreEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutRole($roles, $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperUser {}
}

namespace AidingApp\Alert\Models{
/**
 * 
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alert licensedToEducatable(string $relationship)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alert newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alert newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alert onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alert query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alert status(\AidingApp\Alert\Enums\AlertStatus $status)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alert whereConcernId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alert whereConcernType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alert whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alert whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alert whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alert whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alert whereSeverity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alert whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alert whereSuggestedIntervention($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alert whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alert withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Alert withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperAlert {}
}

namespace AidingApp\Audit\Models{
/**
 * 
 *
 * @property string $id
 * @property string|null $change_agent_type
 * @property string|null $change_agent_id
 * @property string $event
 * @property string $auditable_type
 * @property string $auditable_id
 * @property array<array-key, mixed>|null $old_values
 * @property array<array-key, mixed>|null $new_values
 * @property string|null $url
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property string|null $tags
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $auditable
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent|null $user
 * @method static \AidingApp\Audit\Database\Factories\AuditFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Audit newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Audit newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Audit query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Audit whereAuditableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Audit whereAuditableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Audit whereChangeAgentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Audit whereChangeAgentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Audit whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Audit whereEvent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Audit whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Audit whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Audit whereNewValues($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Audit whereOldValues($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Audit whereTags($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Audit whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Audit whereUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Audit whereUserAgent($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperAudit {}
}

namespace AidingApp\Authorization\Models{
/**
 * 
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|License newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|License newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|License onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|License query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|License whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|License whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|License whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|License whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|License whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|License whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|License withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|License withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperLicense {}
}

namespace AidingApp\Authorization\Models{
/**
 * 
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission api()
 * @method static \AidingApp\Authorization\Database\Factories\PermissionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission role($roles, $guard = null, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission web()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereGuardName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission withoutRole($roles, $guard = null)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperPermission {}
}

namespace AidingApp\Authorization\Models{
/**
 * 
 *
 * @property string $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Authorization\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermissionGroup newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermissionGroup newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermissionGroup onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermissionGroup query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermissionGroup whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermissionGroup whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermissionGroup whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermissionGroup whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermissionGroup whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermissionGroup withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermissionGroup withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperPermissionGroup {}
}

namespace AidingApp\Authorization\Models{
/**
 * 
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role api()
 * @method static \AidingApp\Authorization\Database\Factories\RoleFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role superAdmin()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role web()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereGuardName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role withoutPermission($permissions)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperRole {}
}

namespace AidingApp\Contact\Models{
/**
 * 
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
 * @property-read \AidingApp\Engagement\Models\EngagementFileEntities|null $pivot
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
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Authorization\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\LicenseManagement\Models\ProductLicense> $productLicenses
 * @property-read int|null $product_licenses_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Authorization\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\ServiceManagement\Models\ServiceRequest> $serviceRequests
 * @property-read int|null $service_requests_count
 * @property-read \AidingApp\Contact\Models\ContactSource $source
 * @property-read \AidingApp\Contact\Models\ContactStatus $status
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Task\Models\Task> $tasks
 * @property-read int|null $tasks_count
 * @property-read \AidingApp\Timeline\Models\Timeline|null $timeline
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \AidingApp\Contact\Database\Factories\ContactFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contact newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contact newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contact onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contact permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contact query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contact role($roles, $guard = null, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contact whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contact whereAddress2($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contact whereAddress3($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contact whereAssignedToId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contact whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contact whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contact whereCreatedById($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contact whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contact whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contact whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contact whereEmailBounce($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contact whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contact whereFullName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contact whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contact whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contact whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contact whereOrganizationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contact wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contact wherePostal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contact wherePreferred($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contact whereSmsOptOut($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contact whereSourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contact whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contact whereStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contact whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contact withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contact withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contact withoutRole($roles, $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contact withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperContact {}
}

namespace AidingApp\Contact\Models{
/**
 * 
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactSource newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactSource newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactSource onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactSource query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactSource whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactSource whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactSource whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactSource whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactSource whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactSource withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactSource withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperContactSource {}
}

namespace AidingApp\Contact\Models{
/**
 * 
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactStatus onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactStatus query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactStatus whereClassification($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactStatus whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactStatus whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactStatus whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactStatus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactStatus whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactStatus whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactStatus withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactStatus withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperContactStatus {}
}

namespace AidingApp\Contact\Models{
/**
 * 
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
 * @property array<array-key, mixed>|null $domains
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereDomains($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereFacebookUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereIndustryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereIsContactGenerationEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereLinkedinUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereNumberOfEmployees($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization wherePhoneNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization wherePostalcode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereTwitterUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization whereWebsite($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Organization withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperOrganization {}
}

namespace AidingApp\Contact\Models{
/**
 * 
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrganizationIndustry newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrganizationIndustry newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrganizationIndustry onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrganizationIndustry query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrganizationIndustry whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrganizationIndustry whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrganizationIndustry whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrganizationIndustry whereIsDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrganizationIndustry whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrganizationIndustry whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrganizationIndustry withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrganizationIndustry withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperOrganizationIndustry {}
}

namespace AidingApp\Contact\Models{
/**
 * 
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrganizationType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrganizationType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrganizationType onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrganizationType query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrganizationType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrganizationType whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrganizationType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrganizationType whereIsDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrganizationType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrganizationType whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrganizationType withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrganizationType withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperOrganizationType {}
}

namespace AidingApp\ContractManagement\Models{
/**
 * 
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereContractTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereContractValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract whereVendorName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contract withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperContract {}
}

namespace AidingApp\ContractManagement\Models{
/**
 * 
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContractType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContractType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContractType onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContractType query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContractType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContractType whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContractType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContractType whereIsDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContractType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContractType whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContractType whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContractType withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContractType withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperContractType {}
}

namespace AidingApp\Division\Models{
/**
 * 
 *
 * @property string $id
 * @property string $name
 * @property string|null $description
 * @property string $code
 * @property string|null $created_by_id
 * @property string|null $last_updated_by_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property bool $is_default
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\User|null $createdBy
 * @property-read \App\Models\User|null $lastUpdatedBy
 * @property-read \App\Models\NotificationSettingPivot|null $notificationSetting
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Team\Models\Team> $teams
 * @property-read int|null $teams_count
 * @method static \AidingApp\Division\Database\Factories\DivisionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Division newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Division newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Division onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Division query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Division whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Division whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Division whereCreatedById($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Division whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Division whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Division whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Division whereIsDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Division whereLastUpdatedById($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Division whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Division whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Division withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Division withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperDivision {}
}

namespace AidingApp\Engagement\Models{
/**
 * 
 *
 * @property string $id
 * @property string $name
 * @property string|null $description
 * @property array<array-key, mixed> $content
 * @property string|null $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \App\Models\User|null $user
 * @method static \AidingApp\Engagement\Database\Factories\EmailTemplateFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailTemplate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailTemplate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailTemplate onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailTemplate query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailTemplate whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailTemplate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailTemplate whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailTemplate whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailTemplate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailTemplate whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailTemplate whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailTemplate whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailTemplate withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailTemplate withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperEmailTemplate {}
}

namespace AidingApp\Engagement\Models{
/**
 * 
 *
 * @property-read Educatable $recipient
 * @property string $id
 * @property string|null $user_id
 * @property string|null $engagement_batch_id
 * @property string|null $recipient_id
 * @property string|null $recipient_type
 * @property string|null $subject
 * @property array<array-key, mixed>|null $body
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \AidingApp\Notification\Enums\NotificationChannel $channel
 * @property \Illuminate\Support\Carbon|null $scheduled_at
 * @property \Illuminate\Support\Carbon|null $dispatched_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \AidingApp\Engagement\Models\EngagementBatch|null $batch
 * @property-read \App\Models\User|null $createdBy
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Notification\Models\EmailMessage> $emailMessages
 * @property-read int|null $email_messages_count
 * @property-read \AidingApp\Engagement\Models\EngagementBatch|null $engagementBatch
 * @property-read \AidingApp\Notification\Models\EmailMessage|null $latestEmailMessage
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \AidingApp\Timeline\Models\Timeline|null $timelineRecord
 * @property-read \App\Models\User|null $user
 * @method static \AidingApp\Engagement\Database\Factories\EngagementFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Engagement isNotPartOfABatch()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Engagement licensedToEducatable(string $relationship)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Engagement newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Engagement newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Engagement onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Engagement query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Engagement sentToContact()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Engagement whereBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Engagement whereChannel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Engagement whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Engagement whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Engagement whereDispatchedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Engagement whereEngagementBatchId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Engagement whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Engagement whereRecipientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Engagement whereRecipientType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Engagement whereScheduledAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Engagement whereSubject($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Engagement whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Engagement whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Engagement withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Engagement withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperEngagement {}
}

namespace AidingApp\Engagement\Models{
/**
 * 
 *
 * @property string $id
 * @property string|null $identifier
 * @property string $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \AidingApp\Notification\Enums\NotificationChannel|null $channel
 * @property string|null $subject
 * @property array<array-key, mixed>|null $body
 * @property \Illuminate\Support\Carbon|null $scheduled_at
 * @property int|null $total_engagements
 * @property int|null $processed_engagements
 * @property int|null $successful_engagements
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Engagement\Models\Engagement> $engagements
 * @property-read int|null $engagements_count
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \App\Models\User $user
 * @method static \AidingApp\Engagement\Database\Factories\EngagementBatchFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngagementBatch newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngagementBatch newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngagementBatch query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngagementBatch whereBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngagementBatch whereChannel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngagementBatch whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngagementBatch whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngagementBatch whereIdentifier($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngagementBatch whereProcessedEngagements($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngagementBatch whereScheduledAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngagementBatch whereSubject($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngagementBatch whereSuccessfulEngagements($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngagementBatch whereTotalEngagements($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngagementBatch whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngagementBatch whereUserId($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperEngagementBatch {}
}

namespace AidingApp\Engagement\Models{
/**
 * 
 *
 * @property string $id
 * @property string $description
 * @property string|null $retention_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \AidingApp\Engagement\Models\EngagementFileEntities|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Contact\Models\Contact> $contacts
 * @property-read int|null $contacts_count
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @method static \AidingApp\Engagement\Database\Factories\EngagementFileFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngagementFile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngagementFile newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngagementFile query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngagementFile whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngagementFile whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngagementFile whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngagementFile whereRetentionDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngagementFile whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperEngagementFile {}
}

namespace AidingApp\Engagement\Models{
/**
 * 
 *
 * @property string $engagement_file_id
 * @property string $entity_id
 * @property string $entity_type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \AidingApp\Engagement\Models\EngagementFile $engagementFile
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $entity
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngagementFileEntities newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngagementFileEntities newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngagementFileEntities query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngagementFileEntities whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngagementFileEntities whereEngagementFileId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngagementFileEntities whereEntityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngagementFileEntities whereEntityType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngagementFileEntities whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperEngagementFileEntities {}
}

namespace AidingApp\Engagement\Models{
/**
 * 
 *
 * @property string $id
 * @property string|null $sender_id
 * @property string|null $sender_type
 * @property string|null $content
 * @property \Illuminate\Support\Carbon|null $sent_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string|null $subject
 * @property \AidingApp\Engagement\Enums\EngagementResponseType $type
 * @property string|null $raw
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent|null $sender
 * @property-read \AidingApp\Timeline\Models\Timeline|null $timelineRecord
 * @method static \AidingApp\Engagement\Database\Factories\EngagementResponseFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngagementResponse newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngagementResponse newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngagementResponse onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngagementResponse query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngagementResponse sentByContact()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngagementResponse whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngagementResponse whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngagementResponse whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngagementResponse whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngagementResponse whereRaw($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngagementResponse whereSenderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngagementResponse whereSenderType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngagementResponse whereSentAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngagementResponse whereSubject($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngagementResponse whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngagementResponse whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngagementResponse withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EngagementResponse withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperEngagementResponse {}
}

namespace AidingApp\InAppCommunication\Models{
/**
 * 
 *
 * @property string $sid
 * @property string|null $friendly_name
 * @property \AidingApp\InAppCommunication\Enums\ConversationType $type
 * @property string|null $channel_name
 * @property bool $is_private_channel
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \AidingApp\InAppCommunication\Models\TwilioConversationUser|null $participant
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $managers
 * @property-read int|null $managers_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $participants
 * @property-read int|null $participants_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TwilioConversation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TwilioConversation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TwilioConversation onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TwilioConversation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TwilioConversation whereChannelName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TwilioConversation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TwilioConversation whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TwilioConversation whereFriendlyName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TwilioConversation whereIsPrivateChannel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TwilioConversation whereSid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TwilioConversation whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TwilioConversation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TwilioConversation withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TwilioConversation withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperTwilioConversation {}
}

namespace AidingApp\InAppCommunication\Models{
/**
 * 
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TwilioConversationUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TwilioConversationUser newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TwilioConversationUser query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TwilioConversationUser whereConversationSid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TwilioConversationUser whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TwilioConversationUser whereFirstUnreadMessageAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TwilioConversationUser whereFirstUnreadMessageSid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TwilioConversationUser whereIsChannelManager($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TwilioConversationUser whereIsPinned($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TwilioConversationUser whereLastReadAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TwilioConversationUser whereLastUnreadMessageContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TwilioConversationUser whereNotificationPreference($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TwilioConversationUser whereParticipantSid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TwilioConversationUser whereUnreadMessagesCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TwilioConversationUser whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TwilioConversationUser whereUserId($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperTwilioConversationUser {}
}

namespace AidingApp\InventoryManagement\Models{
/**
 * 
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset wherePurchaseDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereSerialNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperAsset {}
}

namespace AidingApp\InventoryManagement\Models{
/**
 * 
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
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent|null $checkedInBy
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $checkedInFrom
 * @property-read mixed $formatted_checked_in_at
 * @property-read \AidingApp\Timeline\Models\Timeline|null $timelineRecord
 * @method static \AidingApp\InventoryManagement\Database\Factories\AssetCheckInFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetCheckIn newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetCheckIn newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetCheckIn onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetCheckIn query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetCheckIn whereAssetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetCheckIn whereCheckedInAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetCheckIn whereCheckedInById($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetCheckIn whereCheckedInByType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetCheckIn whereCheckedInFromId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetCheckIn whereCheckedInFromType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetCheckIn whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetCheckIn whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetCheckIn whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetCheckIn whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetCheckIn whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetCheckIn withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetCheckIn withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperAssetCheckIn {}
}

namespace AidingApp\InventoryManagement\Models{
/**
 * 
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
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent|null $checkedOutBy
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $checkedOutTo
 * @property-read mixed $formatted_checked_out_at
 * @property-read mixed $status
 * @property-read \AidingApp\Timeline\Models\Timeline|null $timelineRecord
 * @method static \AidingApp\InventoryManagement\Database\Factories\AssetCheckOutFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetCheckOut newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetCheckOut newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetCheckOut onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetCheckOut query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetCheckOut whereAssetCheckInId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetCheckOut whereAssetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetCheckOut whereCheckedOutAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetCheckOut whereCheckedOutById($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetCheckOut whereCheckedOutByType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetCheckOut whereCheckedOutToId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetCheckOut whereCheckedOutToType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetCheckOut whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetCheckOut whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetCheckOut whereExpectedCheckInAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetCheckOut whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetCheckOut whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetCheckOut whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetCheckOut withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetCheckOut withoutReturned()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetCheckOut withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperAssetCheckOut {}
}

namespace AidingApp\InventoryManagement\Models{
/**
 * 
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetLocation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetLocation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetLocation onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetLocation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetLocation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetLocation whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetLocation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetLocation whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetLocation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetLocation withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetLocation withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperAssetLocation {}
}

namespace AidingApp\InventoryManagement\Models{
/**
 * 
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetStatus onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetStatus query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetStatus whereClassification($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetStatus whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetStatus whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetStatus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetStatus whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetStatus whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetStatus withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetStatus withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperAssetStatus {}
}

namespace AidingApp\InventoryManagement\Models{
/**
 * 
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetType onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetType query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetType whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetType whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetType withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetType withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperAssetType {}
}

namespace AidingApp\InventoryManagement\Models{
/**
 * 
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaintenanceActivity newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaintenanceActivity newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaintenanceActivity onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaintenanceActivity query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaintenanceActivity whereAssetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaintenanceActivity whereCompletedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaintenanceActivity whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaintenanceActivity whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaintenanceActivity whereDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaintenanceActivity whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaintenanceActivity whereMaintenanceProviderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaintenanceActivity whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaintenanceActivity whereScheduledDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaintenanceActivity whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaintenanceActivity whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaintenanceActivity withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaintenanceActivity withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperMaintenanceActivity {}
}

namespace AidingApp\InventoryManagement\Models{
/**
 * 
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaintenanceProvider newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaintenanceProvider newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaintenanceProvider onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaintenanceProvider query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaintenanceProvider whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaintenanceProvider whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaintenanceProvider whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaintenanceProvider whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaintenanceProvider whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaintenanceProvider withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MaintenanceProvider withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperMaintenanceProvider {}
}

namespace AidingApp\KnowledgeBase\Models{
/**
 * 
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeBaseCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeBaseCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeBaseCategory onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeBaseCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeBaseCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeBaseCategory whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeBaseCategory whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeBaseCategory whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeBaseCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeBaseCategory whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeBaseCategory whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeBaseCategory whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeBaseCategory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeBaseCategory withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeBaseCategory withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperKnowledgeBaseCategory {}
}

namespace AidingApp\KnowledgeBase\Models{
/**
 * 
 *
 * @property string $id
 * @property bool $public
 * @property string $title
 * @property array<array-key, mixed>|null $article_details
 * @property string|null $notes
 * @property string|null $quality_id
 * @property string|null $status_id
 * @property string|null $category_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int $portal_view_count
 * @property bool $is_featured
 * @property string|null $article_details_fulltext
 * @property string $search_vector
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeBaseItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeBaseItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeBaseItem onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeBaseItem public()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeBaseItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeBaseItem whereArticleDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeBaseItem whereArticleDetailsFulltext($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeBaseItem whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeBaseItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeBaseItem whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeBaseItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeBaseItem whereIsFeatured($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeBaseItem whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeBaseItem wherePortalViewCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeBaseItem wherePublic($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeBaseItem whereQualityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeBaseItem whereSearchVector($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeBaseItem whereStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeBaseItem whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeBaseItem whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeBaseItem withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeBaseItem withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperKnowledgeBaseItem {}
}

namespace AidingApp\KnowledgeBase\Models{
/**
 * 
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeBaseQuality newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeBaseQuality newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeBaseQuality onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeBaseQuality query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeBaseQuality whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeBaseQuality whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeBaseQuality whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeBaseQuality whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeBaseQuality whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeBaseQuality withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeBaseQuality withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperKnowledgeBaseQuality {}
}

namespace AidingApp\KnowledgeBase\Models{
/**
 * 
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeBaseStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeBaseStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeBaseStatus onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeBaseStatus query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeBaseStatus whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeBaseStatus whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeBaseStatus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeBaseStatus whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeBaseStatus whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeBaseStatus withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeBaseStatus withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperKnowledgeBaseStatus {}
}

namespace AidingApp\LicenseManagement\Models{
/**
 * 
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereAdditionalNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereCreatedById($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperProduct {}
}

namespace AidingApp\LicenseManagement\Models{
/**
 * 
 *
 * @property string $formatted_expiration_date
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductLicense newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductLicense newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductLicense onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductLicense query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductLicense whereAssignedTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductLicense whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductLicense whereCreatedById($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductLicense whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductLicense whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductLicense whereExpirationDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductLicense whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductLicense whereLicense($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductLicense whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductLicense whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductLicense whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductLicense withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProductLicense withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperProductLicense {}
}

namespace AidingApp\Notification\Models{
/**
 * 
 *
 * @property string $id
 * @property string $notification_class
 * @property string|null $notification_id
 * @property array<array-key, mixed> $content
 * @property string|null $related_type
 * @property string|null $related_id
 * @property string|null $recipient_id
 * @property string|null $recipient_type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent|null $recipient
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent|null $related
 * @method static \AidingApp\Notification\Database\Factories\DatabaseMessageFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DatabaseMessage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DatabaseMessage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DatabaseMessage query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DatabaseMessage whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DatabaseMessage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DatabaseMessage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DatabaseMessage whereNotificationClass($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DatabaseMessage whereNotificationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DatabaseMessage whereRecipientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DatabaseMessage whereRecipientType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DatabaseMessage whereRelatedId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DatabaseMessage whereRelatedType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DatabaseMessage whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperDatabaseMessage {}
}

namespace AidingApp\Notification\Models{
/**
 * 
 *
 * @property string $id
 * @property string $notification_class
 * @property string|null $external_reference_id
 * @property array<array-key, mixed> $content
 * @property int $quota_usage
 * @property string|null $related_type
 * @property string|null $related_id
 * @property string|null $recipient_id
 * @property string|null $recipient_type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Notification\Models\EmailMessageEvent> $events
 * @property-read int|null $events_count
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent|null $recipient
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent|null $related
 * @method static \AidingApp\Notification\Database\Factories\EmailMessageFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailMessage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailMessage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailMessage query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailMessage whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailMessage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailMessage whereExternalReferenceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailMessage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailMessage whereNotificationClass($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailMessage whereQuotaUsage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailMessage whereRecipientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailMessage whereRecipientType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailMessage whereRelatedId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailMessage whereRelatedType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailMessage whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperEmailMessage {}
}

namespace AidingApp\Notification\Models{
/**
 * 
 *
 * @property string $id
 * @property string $email_message_id
 * @property \AidingApp\Notification\Enums\EmailMessageEventType $type
 * @property array<array-key, mixed> $payload
 * @property \Illuminate\Support\Carbon $occurred_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \AidingApp\Notification\Models\EmailMessage|null $message
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailMessageEvent newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailMessageEvent newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailMessageEvent query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailMessageEvent whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailMessageEvent whereEmailMessageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailMessageEvent whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailMessageEvent whereOccurredAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailMessageEvent wherePayload($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailMessageEvent whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|EmailMessageEvent whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperEmailMessageEvent {}
}

namespace AidingApp\Notification\Models{
/**
 * 
 *
 * @property string $id
 * @property \AidingApp\Notification\Enums\NotificationChannel $type
 * @property string $route
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StoredAnonymousNotifiable newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StoredAnonymousNotifiable newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StoredAnonymousNotifiable query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StoredAnonymousNotifiable whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StoredAnonymousNotifiable whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StoredAnonymousNotifiable whereRoute($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StoredAnonymousNotifiable whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StoredAnonymousNotifiable whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperStoredAnonymousNotifiable {}
}

namespace AidingApp\Portal\Models{
/**
 * 
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeBaseArticleVote newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeBaseArticleVote newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeBaseArticleVote query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeBaseArticleVote whereArticleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeBaseArticleVote whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeBaseArticleVote whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeBaseArticleVote whereIsHelpful($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeBaseArticleVote whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeBaseArticleVote whereVoterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|KnowledgeBaseArticleVote whereVoterType($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperKnowledgeBaseArticleVote {}
}

namespace AidingApp\Portal\Models{
/**
 * 
 *
 * @property Carbon|null $created_at
 * @property string $id
 * @property string|null $educatable_id
 * @property string|null $educatable_type
 * @property string|null $code
 * @property \AidingApp\Portal\Enums\PortalType|null $portal_type
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent|null $educatable
 * @method static \AidingApp\Portal\Database\Factories\PortalAuthenticationFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PortalAuthentication newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PortalAuthentication newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PortalAuthentication query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PortalAuthentication whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PortalAuthentication whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PortalAuthentication whereEducatableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PortalAuthentication whereEducatableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PortalAuthentication whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PortalAuthentication wherePortalType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PortalAuthentication whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperPortalAuthentication {}
}

namespace AidingApp\Portal\Models{
/**
 * 
 *
 * @property string $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Portal\Models\KnowledgeBaseArticleVote> $knowledgeBaseArticleVotes
 * @property-read int|null $knowledge_base_article_votes_count
 * @method static \AidingApp\Portal\Database\Factories\PortalGuestFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PortalGuest newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PortalGuest newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PortalGuest onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PortalGuest query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PortalGuest whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PortalGuest whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PortalGuest whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PortalGuest whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PortalGuest withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PortalGuest withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperPortalGuest {}
}

namespace AidingApp\ServiceManagement\Models{
/**
 * 
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChangeRequest newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChangeRequest newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChangeRequest query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChangeRequest whereBackoutStrategy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChangeRequest whereChangeRequestStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChangeRequest whereChangeRequestTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChangeRequest whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChangeRequest whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChangeRequest whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChangeRequest whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChangeRequest whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChangeRequest whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChangeRequest whereImpact($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChangeRequest whereLikelihood($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChangeRequest whereReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChangeRequest whereRiskScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChangeRequest whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChangeRequest whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChangeRequest whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperChangeRequest {}
}

namespace AidingApp\ServiceManagement\Models{
/**
 * 
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChangeRequestResponse newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChangeRequestResponse newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChangeRequestResponse query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChangeRequestResponse whereApproved($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChangeRequestResponse whereChangeRequestId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChangeRequestResponse whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChangeRequestResponse whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChangeRequestResponse whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChangeRequestResponse whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChangeRequestResponse whereUserId($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperChangeRequestResponse {}
}

namespace AidingApp\ServiceManagement\Models{
/**
 * 
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChangeRequestStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChangeRequestStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChangeRequestStatus onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChangeRequestStatus query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChangeRequestStatus whereClassification($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChangeRequestStatus whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChangeRequestStatus whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChangeRequestStatus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChangeRequestStatus whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChangeRequestStatus whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChangeRequestStatus withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChangeRequestStatus withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperChangeRequestStatus {}
}

namespace AidingApp\ServiceManagement\Models{
/**
 * 
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChangeRequestType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChangeRequestType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChangeRequestType onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChangeRequestType query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChangeRequestType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChangeRequestType whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChangeRequestType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChangeRequestType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChangeRequestType whereNumberOfRequiredApprovals($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChangeRequestType whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChangeRequestType withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChangeRequestType withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperChangeRequestType {}
}

namespace AidingApp\ServiceManagement\Models{
/**
 * 
 *
 * @property string $id
 * @property int $response
 * @property float $response_time
 * @property bool $succeeded
 * @property string $service_monitoring_target_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \AidingApp\ServiceManagement\Models\ServiceMonitoringTarget $serviceMonitoringTarget
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistoricalServiceMonitoring newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistoricalServiceMonitoring newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistoricalServiceMonitoring onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistoricalServiceMonitoring query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistoricalServiceMonitoring whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistoricalServiceMonitoring whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistoricalServiceMonitoring whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistoricalServiceMonitoring whereResponse($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistoricalServiceMonitoring whereResponseTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistoricalServiceMonitoring whereServiceMonitoringTargetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistoricalServiceMonitoring whereSucceeded($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistoricalServiceMonitoring whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistoricalServiceMonitoring withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistoricalServiceMonitoring withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperHistoricalServiceMonitoring {}
}

namespace AidingApp\ServiceManagement\Models{
/**
 * 
 *
 * @property string $id
 * @property string $title
 * @property string $description
 * @property string $severity_id
 * @property string $status_id
 * @property string|null $assigned_team_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \AidingApp\Team\Models\Team|null $assignedTeam
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\ServiceManagement\Models\IncidentUpdate> $incidentUpdates
 * @property-read int|null $incident_updates_count
 * @property-read \AidingApp\ServiceManagement\Models\IncidentSeverity $severity
 * @property-read \AidingApp\ServiceManagement\Models\IncidentStatus $status
 * @method static \AidingApp\ServiceManagement\Database\Factories\IncidentFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Incident newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Incident newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Incident onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Incident query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Incident whereAssignedTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Incident whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Incident whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Incident whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Incident whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Incident whereSeverityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Incident whereStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Incident whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Incident whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Incident withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Incident withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperIncident {}
}

namespace AidingApp\ServiceManagement\Models{
/**
 * 
 *
 * @property string $id
 * @property string $name
 * @property string $color
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\ServiceManagement\Models\Incident> $incidents
 * @property-read int|null $incidents_count
 * @property-read mixed $rgb_color
 * @method static \AidingApp\ServiceManagement\Database\Factories\IncidentSeverityFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IncidentSeverity newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IncidentSeverity newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IncidentSeverity onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IncidentSeverity query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IncidentSeverity whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IncidentSeverity whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IncidentSeverity whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IncidentSeverity whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IncidentSeverity whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IncidentSeverity whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IncidentSeverity withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IncidentSeverity withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperIncidentSeverity {}
}

namespace AidingApp\ServiceManagement\Models{
/**
 * 
 *
 * @property string $id
 * @property string $name
 * @property \AidingApp\ServiceManagement\Enums\SystemIncidentStatusClassification $classification
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\ServiceManagement\Models\Incident> $incidents
 * @property-read int|null $incidents_count
 * @method static \AidingApp\ServiceManagement\Database\Factories\IncidentStatusFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IncidentStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IncidentStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IncidentStatus onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IncidentStatus query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IncidentStatus whereClassification($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IncidentStatus whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IncidentStatus whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IncidentStatus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IncidentStatus whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IncidentStatus whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IncidentStatus withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IncidentStatus withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperIncidentStatus {}
}

namespace AidingApp\ServiceManagement\Models{
/**
 * 
 *
 * @property string $id
 * @property string|null $incident_id
 * @property string $update
 * @property bool $internal
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \AidingApp\ServiceManagement\Models\Incident|null $incident
 * @method static \AidingApp\ServiceManagement\Database\Factories\IncidentUpdateFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IncidentUpdate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IncidentUpdate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IncidentUpdate query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IncidentUpdate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IncidentUpdate whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IncidentUpdate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IncidentUpdate whereIncidentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IncidentUpdate whereInternal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IncidentUpdate whereUpdate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IncidentUpdate whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperIncidentUpdate {}
}

namespace AidingApp\ServiceManagement\Models{
/**
 * 
 *
 * @property string $id
 * @property string $name
 * @property string|null $description
 * @property string $domain
 * @property \AidingApp\ServiceManagement\Enums\ServiceMonitoringFrequency $frequency
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\ServiceManagement\Models\HistoricalServiceMonitoring> $histories
 * @property-read int|null $histories_count
 * @property-read \AidingApp\ServiceManagement\Models\HistoricalServiceMonitoring|null $latestHistory
 * @property-read \AidingApp\ServiceManagement\Models\ServiceMonitoringTargetUser|\AidingApp\ServiceManagement\Models\ServiceMonitoringTargetTeam|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Team\Models\Team> $teams
 * @property-read int|null $teams_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \AidingApp\ServiceManagement\Database\Factories\ServiceMonitoringTargetFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceMonitoringTarget newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceMonitoringTarget newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceMonitoringTarget onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceMonitoringTarget query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceMonitoringTarget whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceMonitoringTarget whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceMonitoringTarget whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceMonitoringTarget whereDomain($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceMonitoringTarget whereFrequency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceMonitoringTarget whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceMonitoringTarget whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceMonitoringTarget whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceMonitoringTarget withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceMonitoringTarget withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperServiceMonitoringTarget {}
}

namespace AidingApp\ServiceManagement\Models{
/**
 * 
 *
 * @property string $id
 * @property string $service_monitoring_target_id
 * @property string $team_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \AidingApp\ServiceManagement\Models\ServiceMonitoringTarget $serviceMonitoringTarget
 * @property-read \AidingApp\Team\Models\Team $team
 * @method static \AidingApp\ServiceManagement\Database\Factories\ServiceMonitoringTargetTeamFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceMonitoringTargetTeam newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceMonitoringTargetTeam newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceMonitoringTargetTeam query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceMonitoringTargetTeam whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceMonitoringTargetTeam whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceMonitoringTargetTeam whereServiceMonitoringTargetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceMonitoringTargetTeam whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceMonitoringTargetTeam whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperServiceMonitoringTargetTeam {}
}

namespace AidingApp\ServiceManagement\Models{
/**
 * 
 *
 * @property string $id
 * @property string $service_monitoring_target_id
 * @property string $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \AidingApp\ServiceManagement\Models\ServiceMonitoringTarget $serviceMonitoringTarget
 * @property-read \App\Models\User $user
 * @method static \AidingApp\ServiceManagement\Database\Factories\ServiceMonitoringTargetUserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceMonitoringTargetUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceMonitoringTargetUser newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceMonitoringTargetUser query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceMonitoringTargetUser whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceMonitoringTargetUser whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceMonitoringTargetUser whereServiceMonitoringTargetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceMonitoringTargetUser whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceMonitoringTargetUser whereUserId($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperServiceMonitoringTargetUser {}
}

namespace AidingApp\ServiceManagement\Models{
/**
 * 
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequest licensedToEducatable(string $relationship)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequest newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequest newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequest onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequest open()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequest query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequest whereCloseDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequest whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequest whereCreatedById($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequest whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequest whereDivisionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequest whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequest wherePriorityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequest whereResDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequest whereRespondentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequest whereRespondentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequest whereServiceRequestFormSubmissionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequest whereServiceRequestNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequest whereStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequest whereStatusUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequest whereTimeToResolution($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequest whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequest whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequest withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequest withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperServiceRequest {}
}

namespace AidingApp\ServiceManagement\Models{
/**
 * 
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestAssignment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestAssignment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestAssignment onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestAssignment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestAssignment whereAssignedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestAssignment whereAssignedById($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestAssignment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestAssignment whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestAssignment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestAssignment whereServiceRequestId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestAssignment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestAssignment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestAssignment whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestAssignment withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestAssignment withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperServiceRequestAssignment {}
}

namespace AidingApp\ServiceManagement\Models{
/**
 * 
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestFeedback newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestFeedback newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestFeedback onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestFeedback query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestFeedback whereContactId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestFeedback whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestFeedback whereCsatAnswer($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestFeedback whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestFeedback whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestFeedback whereNpsAnswer($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestFeedback whereServiceRequestId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestFeedback whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestFeedback withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestFeedback withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperServiceRequestFeedback {}
}

namespace AidingApp\ServiceManagement\Models{
/**
 * 
 *
 * @property string $id
 * @property string|null $service_request_type_id
 * @property string $name
 * @property string|null $description
 * @property bool $embed_enabled
 * @property array<array-key, mixed>|null $allowed_domains
 * @property string|null $primary_color
 * @property \AidingApp\Form\Enums\Rounding|null $rounding
 * @property bool $is_authenticated
 * @property bool $is_wizard
 * @property bool $recaptcha_enabled
 * @property array<array-key, mixed>|null $content
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestForm newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestForm newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestForm query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestForm whereAllowedDomains($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestForm whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestForm whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestForm whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestForm whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestForm whereEmbedEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestForm whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestForm whereIsAuthenticated($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestForm whereIsWizard($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestForm whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestForm wherePrimaryColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestForm whereRecaptchaEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestForm whereRounding($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestForm whereServiceRequestTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestForm whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperServiceRequestForm {}
}

namespace AidingApp\ServiceManagement\Models{
/**
 * 
 *
 * @property string $id
 * @property string|null $author_id
 * @property string|null $author_type
 * @property string|null $code
 * @property string $service_request_form_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent|null $author
 * @property-read \AidingApp\ServiceManagement\Models\ServiceRequestForm $submissible
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestFormAuthentication newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestFormAuthentication newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestFormAuthentication query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestFormAuthentication whereAuthorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestFormAuthentication whereAuthorType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestFormAuthentication whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestFormAuthentication whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestFormAuthentication whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestFormAuthentication whereServiceRequestFormId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestFormAuthentication whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperServiceRequestFormAuthentication {}
}

namespace AidingApp\ServiceManagement\Models{
/**
 * 
 *
 * @property string $id
 * @property string $label
 * @property string $type
 * @property bool $is_required
 * @property array<array-key, mixed> $config
 * @property string $service_request_form_id
 * @property string|null $service_request_form_step_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \AidingApp\ServiceManagement\Models\ServiceRequestFormStep|null $step
 * @property-read \AidingApp\ServiceManagement\Models\ServiceRequestForm $submissible
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestFormField newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestFormField newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestFormField onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestFormField query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestFormField whereConfig($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestFormField whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestFormField whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestFormField whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestFormField whereIsRequired($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestFormField whereLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestFormField whereServiceRequestFormId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestFormField whereServiceRequestFormStepId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestFormField whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestFormField whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestFormField withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestFormField withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperServiceRequestFormField {}
}

namespace AidingApp\ServiceManagement\Models{
/**
 * 
 *
 * @property string $id
 * @property string $label
 * @property array<array-key, mixed>|null $content
 * @property string $service_request_form_id
 * @property int $sort
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\ServiceManagement\Models\ServiceRequestFormField> $fields
 * @property-read int|null $fields_count
 * @property-read \AidingApp\ServiceManagement\Models\ServiceRequestForm $submissible
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestFormStep newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestFormStep newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestFormStep onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestFormStep query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestFormStep whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestFormStep whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestFormStep whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestFormStep whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestFormStep whereLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestFormStep whereServiceRequestFormId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestFormStep whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestFormStep whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestFormStep withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestFormStep withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperServiceRequestFormStep {}
}

namespace AidingApp\ServiceManagement\Models{
/**
 * 
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestFormSubmission canceled()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestFormSubmission licensedToEducatable(string $relationship)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestFormSubmission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestFormSubmission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestFormSubmission notCanceled()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestFormSubmission notSubmitted()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestFormSubmission query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestFormSubmission requested()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestFormSubmission submitted()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestFormSubmission whereAuthorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestFormSubmission whereAuthorType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestFormSubmission whereCanceledAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestFormSubmission whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestFormSubmission whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestFormSubmission whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestFormSubmission whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestFormSubmission whereRequestMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestFormSubmission whereRequestNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestFormSubmission whereRequesterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestFormSubmission whereServiceRequestFormId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestFormSubmission whereServiceRequestPriorityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestFormSubmission whereSubmittedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestFormSubmission whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperServiceRequestFormSubmission {}
}

namespace AidingApp\ServiceManagement\Models{
/**
 * 
 *
 * @property string $id
 * @property string $service_request_id
 * @property array<array-key, mixed> $original_values
 * @property array<array-key, mixed> $new_values
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read mixed $new_values_formatted
 * @property-read mixed $original_values_formatted
 * @property-read \AidingApp\ServiceManagement\Models\ServiceRequest $serviceRequest
 * @method static \AidingApp\ServiceManagement\Database\Factories\ServiceRequestHistoryFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestHistory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestHistory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestHistory onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestHistory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestHistory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestHistory whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestHistory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestHistory whereNewValues($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestHistory whereOriginalValues($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestHistory whereServiceRequestId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestHistory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestHistory withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestHistory withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperServiceRequestHistory {}
}

namespace AidingApp\ServiceManagement\Models{
/**
 * 
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestPriority newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestPriority newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestPriority onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestPriority query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestPriority whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestPriority whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestPriority whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestPriority whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestPriority whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestPriority whereSlaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestPriority whereTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestPriority whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestPriority withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestPriority withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperServiceRequestPriority {}
}

namespace AidingApp\ServiceManagement\Models{
/**
 * 
 *
 * @property string $id
 * @property \AidingApp\ServiceManagement\Enums\SystemServiceRequestClassification $classification
 * @property string $name
 * @property \CanyonGBS\Common\Enums\Color $color
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property bool $is_system_protected
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\ServiceManagement\Models\ServiceRequest> $serviceRequests
 * @property-read int|null $service_requests_count
 * @method static \AidingApp\ServiceManagement\Database\Factories\ServiceRequestStatusFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestStatus onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestStatus query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestStatus whereClassification($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestStatus whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestStatus whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestStatus whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestStatus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestStatus whereIsSystemProtected($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestStatus whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestStatus whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestStatus withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestStatus withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperServiceRequestStatus {}
}

namespace AidingApp\ServiceManagement\Models{
/**
 * 
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
 * @property bool $is_managers_service_request_closed_email_enabled
 * @property bool $is_managers_service_request_closed_notification_enabled
 * @property bool $is_auditors_service_request_created_email_enabled
 * @property bool $is_auditors_service_request_created_notification_enabled
 * @property bool $is_auditors_service_request_assigned_email_enabled
 * @property bool $is_auditors_service_request_assigned_notification_enabled
 * @property bool $is_auditors_service_request_closed_email_enabled
 * @property bool $is_auditors_service_request_closed_notification_enabled
 * @property string|null $last_assigned_id
 * @property bool $is_managers_service_request_update_email_enabled
 * @property bool $is_managers_service_request_update_notification_enabled
 * @property bool $is_managers_service_request_status_change_email_enabled
 * @property bool $is_managers_service_request_status_change_notification_enabled
 * @property bool $is_auditors_service_request_update_email_enabled
 * @property bool $is_auditors_service_request_update_notification_enabled
 * @property bool $is_auditors_service_request_status_change_email_enabled
 * @property bool $is_auditors_service_request_status_change_notification_enabled
 * @property bool $is_customers_service_request_created_email_enabled
 * @property bool $is_customers_service_request_created_notification_enabled
 * @property bool $is_customers_service_request_assigned_email_enabled
 * @property bool $is_customers_service_request_assigned_notification_enabled
 * @property bool $is_customers_service_request_update_email_enabled
 * @property bool $is_customers_service_request_update_notification_enabled
 * @property bool $is_customers_service_request_status_change_email_enabled
 * @property bool $is_customers_service_request_status_change_notification_enabled
 * @property bool $is_customers_service_request_closed_email_enabled
 * @property bool $is_customers_service_request_closed_notification_enabled
 * @property bool $is_customers_survey_response_email_enabled
 * @property-read \App\Models\User|null $assignmentTypeIndividual
 * @property-read \AidingApp\ServiceManagement\Models\ServiceRequestTypeManager|\AidingApp\ServiceManagement\Models\ServiceRequestTypeAuditor|null $pivot
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
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\ServiceManagement\Models\ServiceRequestTypeEmailTemplate> $templates
 * @property-read int|null $templates_count
 * @method static \AidingApp\ServiceManagement\Database\Factories\ServiceRequestTypeFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestType onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestType query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestType whereAssignmentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestType whereAssignmentTypeIndividualId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestType whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestType whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestType whereHasEnabledCsat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestType whereHasEnabledFeedbackCollection($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestType whereHasEnabledNps($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestType whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestType whereIsAuditorsServiceRequestAssignedEmailEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestType whereIsAuditorsServiceRequestAssignedNotificationEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestType whereIsAuditorsServiceRequestClosedEmailEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestType whereIsAuditorsServiceRequestClosedNotificationEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestType whereIsAuditorsServiceRequestCreatedEmailEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestType whereIsAuditorsServiceRequestCreatedNotificationEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestType whereIsAuditorsServiceRequestStatusChangeEmailEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestType whereIsAuditorsServiceRequestStatusChangeNotificationEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestType whereIsAuditorsServiceRequestUpdateEmailEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestType whereIsAuditorsServiceRequestUpdateNotificationEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestType whereIsCustomersServiceRequestAssignedEmailEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestType whereIsCustomersServiceRequestAssignedNotificationEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestType whereIsCustomersServiceRequestClosedEmailEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestType whereIsCustomersServiceRequestClosedNotificationEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestType whereIsCustomersServiceRequestCreatedEmailEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestType whereIsCustomersServiceRequestCreatedNotificationEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestType whereIsCustomersServiceRequestStatusChangeEmailEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestType whereIsCustomersServiceRequestStatusChangeNotificationEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestType whereIsCustomersServiceRequestUpdateEmailEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestType whereIsCustomersServiceRequestUpdateNotificationEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestType whereIsCustomersSurveyResponseEmailEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestType whereIsManagersServiceRequestAssignedEmailEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestType whereIsManagersServiceRequestAssignedNotificationEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestType whereIsManagersServiceRequestClosedEmailEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestType whereIsManagersServiceRequestClosedNotificationEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestType whereIsManagersServiceRequestCreatedEmailEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestType whereIsManagersServiceRequestCreatedNotificationEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestType whereIsManagersServiceRequestStatusChangeEmailEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestType whereIsManagersServiceRequestStatusChangeNotificationEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestType whereIsManagersServiceRequestUpdateEmailEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestType whereIsManagersServiceRequestUpdateNotificationEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestType whereLastAssignedId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestType whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestType withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestType withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperServiceRequestType {}
}

namespace AidingApp\ServiceManagement\Models{
/**
 * 
 *
 * @property string $id
 * @property string $service_request_type_id
 * @property string $team_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \AidingApp\ServiceManagement\Models\ServiceRequestType $serviceRequestType
 * @property-read \AidingApp\Team\Models\Team $team
 * @method static \AidingApp\ServiceManagement\Database\Factories\ServiceRequestTypeAuditorFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestTypeAuditor newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestTypeAuditor newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestTypeAuditor query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestTypeAuditor whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestTypeAuditor whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestTypeAuditor whereServiceRequestTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestTypeAuditor whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestTypeAuditor whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperServiceRequestTypeAuditor {}
}

namespace AidingApp\ServiceManagement\Models{
/**
 * 
 *
 * @property string $id
 * @property string $service_request_type_id
 * @property \AidingApp\ServiceManagement\Enums\ServiceRequestEmailTemplateType $type
 * @property array<array-key, mixed> $subject
 * @property array<array-key, mixed> $body
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \AidingApp\ServiceManagement\Enums\ServiceRequestTypeEmailTemplateRole|null $role
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \AidingApp\ServiceManagement\Models\ServiceRequestType $serviceRequestType
 * @method static \AidingApp\ServiceManagement\Database\Factories\ServiceRequestTypeEmailTemplateFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestTypeEmailTemplate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestTypeEmailTemplate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestTypeEmailTemplate query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestTypeEmailTemplate whereBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestTypeEmailTemplate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestTypeEmailTemplate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestTypeEmailTemplate whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestTypeEmailTemplate whereServiceRequestTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestTypeEmailTemplate whereSubject($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestTypeEmailTemplate whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestTypeEmailTemplate whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperServiceRequestTypeEmailTemplate {}
}

namespace AidingApp\ServiceManagement\Models{
/**
 * 
 *
 * @property string $id
 * @property string $service_request_type_id
 * @property string $team_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \AidingApp\ServiceManagement\Models\ServiceRequestType $serviceRequestType
 * @property-read \AidingApp\Team\Models\Team $team
 * @method static \AidingApp\ServiceManagement\Database\Factories\ServiceRequestTypeManagerFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestTypeManager newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestTypeManager newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestTypeManager query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestTypeManager whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestTypeManager whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestTypeManager whereServiceRequestTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestTypeManager whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestTypeManager whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperServiceRequestTypeManager {}
}

namespace AidingApp\ServiceManagement\Models{
/**
 * 
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestUpdate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestUpdate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestUpdate onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestUpdate query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestUpdate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestUpdate whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestUpdate whereDirection($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestUpdate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestUpdate whereInternal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestUpdate whereServiceRequestId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestUpdate whereUpdate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestUpdate whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestUpdate withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceRequestUpdate withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperServiceRequestUpdate {}
}

namespace AidingApp\ServiceManagement\Models{
/**
 * 
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sla newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sla newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sla onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sla query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sla whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sla whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sla whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sla whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sla whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sla whereResolutionSeconds($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sla whereResponseSeconds($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sla whereTerms($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sla whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sla withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sla withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperSla {}
}

namespace AidingApp\Task\Models{
/**
 * 
 *
 * @property-read Contact $concern
 * @property string $id
 * @property string $title
 * @property string $description
 * @property \AidingApp\Task\Enums\TaskStatus $status
 * @property \Illuminate\Support\Carbon|null $due
 * @property string|null $assigned_to
 * @property string|null $created_by
 * @property string|null $concern_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User|null $assignedTo
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\User|null $createdBy
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task byNextDue()
 * @method static \AidingApp\Task\Database\Factories\TaskFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task open()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereAssignedTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereConcernId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereDue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperTask {}
}

namespace AidingApp\Team\Models{
/**
 * 
 *
 * @property string $id
 * @property string $name
 * @property string|null $description
 * @property string|null $division_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \AidingApp\ServiceManagement\Models\ServiceMonitoringTargetTeam|\AidingApp\ServiceManagement\Models\ServiceRequestTypeManager|\AidingApp\ServiceManagement\Models\ServiceRequestTypeAuditor|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\ServiceManagement\Models\ServiceRequestType> $auditableServiceRequestTypes
 * @property-read int|null $auditable_service_request_types_count
 * @property-read \AidingApp\Division\Models\Division|null $division
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\ServiceManagement\Models\ServiceRequestType> $manageableServiceRequestTypes
 * @property-read int|null $manageable_service_request_types_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AidingApp\ServiceManagement\Models\ServiceMonitoringTarget> $serviceMonitoringTargets
 * @property-read int|null $service_monitoring_targets_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \AidingApp\Team\Database\Factories\TeamFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereDivisionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperTeam {}
}

namespace AidingApp\Timeline\Models{
/**
 * 
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Timeline forEntity(\Illuminate\Database\Eloquent\Model $entity)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Timeline newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Timeline newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Timeline onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Timeline query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Timeline whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Timeline whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Timeline whereEntityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Timeline whereEntityType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Timeline whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Timeline whereRecordSortableDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Timeline whereTimelineableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Timeline whereTimelineableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Timeline whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Timeline withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Timeline withoutTrashed()
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperTimeline {}
}

namespace AidingApp\Webhook\Models{
/**
 * 
 *
 * @property string $id
 * @property \AidingApp\Webhook\Enums\InboundWebhookSource $source
 * @property string $event
 * @property string $url
 * @property string $payload
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InboundWebhook newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InboundWebhook newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InboundWebhook query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InboundWebhook whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InboundWebhook whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InboundWebhook whereEvent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InboundWebhook whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InboundWebhook wherePayload($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InboundWebhook whereSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InboundWebhook whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InboundWebhook whereUrl($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperInboundWebhook {}
}

namespace AidingApp\Webhook\Models{
/**
 * 
 *
 * @property string $id
 * @property \AidingApp\Webhook\Enums\InboundWebhookSource $source
 * @property string $event
 * @property string $url
 * @property string $payload
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandlordInboundWebhook newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandlordInboundWebhook newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandlordInboundWebhook query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandlordInboundWebhook whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandlordInboundWebhook whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandlordInboundWebhook whereEvent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandlordInboundWebhook whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandlordInboundWebhook wherePayload($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandlordInboundWebhook whereSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandlordInboundWebhook whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandlordInboundWebhook whereUrl($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperLandlordInboundWebhook {}
}

