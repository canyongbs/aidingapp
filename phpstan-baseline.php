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
*/ declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Ai\\\\Providers\\\\AiServiceProvider\\:\\:boot\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/ai/src/Providers/AiServiceProvider.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Ai\\\\Settings\\\\AiSettings\\:\\:encrypted\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/ai/src/Settings/AiSettings.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Alert\\\\Enums\\\\AlertSeverity\\:\\:getLabel\\(\\) never returns null so it can be removed from the return type\\.$#',
	'identifier' => 'return.unusedType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/alert/src/Enums/AlertSeverity.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Alert\\\\Enums\\\\AlertStatus\\:\\:getLabel\\(\\) never returns null so it can be removed from the return type\\.$#',
	'identifier' => 'return.unusedType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/alert/src/Enums/AlertStatus.php',
];
$ignoreErrors[] = [
	'message' => '#^Ternary operator condition is always true\\.$#',
	'identifier' => 'ternary.alwaysTrue',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/alert/src/Filament/Resources/AlertResource/Pages/ListAlerts.php',
];
$ignoreErrors[] = [
	'message' => '#^Using nullsafe property access on non\\-nullable type AidingApp\\\\Contact\\\\Models\\\\Contact\\. Use \\-\\> instead\\.$#',
	'identifier' => 'nullsafe.neverNull',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/alert/src/Filament/Resources/AlertResource/Pages/ListAlerts.php',
];
$ignoreErrors[] = [
	'message' => '#^Using nullsafe method call on non\\-nullable type Illuminate\\\\Database\\\\Eloquent\\\\Collection\\<int, AidingApp\\\\Notification\\\\Models\\\\Subscription\\>\\. Use \\-\\> instead\\.$#',
	'identifier' => 'nullsafe.neverNull',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/alert/src/Listeners/NotifySubscribersOfAlertCreated.php',
];
$ignoreErrors[] = [
	'message' => '#^Instanceof between AidingApp\\\\Contact\\\\Models\\\\Contact and AidingApp\\\\Notification\\\\Models\\\\Contracts\\\\Subscribable will always evaluate to true\\.$#',
	'identifier' => 'instanceof.alwaysTrue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/alert/src/Models/Alert.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Alert\\\\Models\\\\Alert\\:\\:concern\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/alert/src/Models/Alert.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Alert\\\\Models\\\\Alert\\:\\:newBelongsToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/alert/src/Models/Alert.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Alert\\\\Models\\\\Alert\\:\\:newMorphToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/alert/src/Models/Alert.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Alert\\\\Models\\\\Alert\\:\\:scopeLicensedToEducatable\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/alert/src/Models/Alert.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Alert\\\\Models\\\\Alert\\:\\:scopeLicensedToEducatable\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder does not specify its types\\: TModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/alert/src/Models/Alert.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Alert\\\\Models\\\\Alert\\:\\:scopeStatus\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/alert/src/Models/Alert.php',
];
$ignoreErrors[] = [
	'message' => '#^Match expression does not handle remaining value\\: class\\-string\\<AidingApp\\\\Contact\\\\Models\\\\Contact\\>&literal\\-string$#',
	'identifier' => 'match.unhandled',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/alert/src/Notifications/AlertCreatedNotification.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Alert\\\\Notifications\\\\AlertCreatedNotification\\:\\:toDatabase\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/alert/src/Notifications/AlertCreatedNotification.php',
];
$ignoreErrors[] = [
	'message' => '#^Match expression does not handle remaining value\\: string$#',
	'identifier' => 'match.unhandled',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/alert/src/Observers/AlertObserver.php',
];
$ignoreErrors[] = [
	'message' => '#^Using nullsafe method call on non\\-nullable type AidingApp\\\\Contact\\\\Models\\\\Contact\\. Use \\-\\> instead\\.$#',
	'identifier' => 'nullsafe.neverNull',
	'count' => 5,
	'path' => __DIR__ . '/app-modules/alert/src/Policies/AlertPolicy.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Alert\\\\Rules\\\\ConcernIdExistsRule\\:\\:setData\\(\\) has parameter \\$data with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/alert/src/Rules/ConcernIdExistsRule.php',
];
$ignoreErrors[] = [
	'message' => '#^Property AidingApp\\\\Alert\\\\Rules\\\\ConcernIdExistsRule\\:\\:\\$data has no type specified\\.$#',
	'identifier' => 'missingType.property',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/alert/src/Rules/ConcernIdExistsRule.php',
];
$ignoreErrors[] = [
	'message' => '#^Called \'env\' outside of the config directory which returns null when the config is cached, use \'config\'\\.$#',
	'identifier' => 'larastan.noEnvCallsOutsideOfConfig',
	'count' => 7,
	'path' => __DIR__ . '/app-modules/audit/config/audit.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Audit\\\\Actions\\\\Finders\\\\AuditableModels\\:\\:all\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/audit/src/Actions/Finders/AuditableModels.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method OwenIt\\\\Auditing\\\\Contracts\\\\Auditable\\:\\:auditAttach\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/audit/src/Filament/Actions/AuditAttachAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method OwenIt\\\\Auditing\\\\Contracts\\\\Auditable\\:\\:auditDetach\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/audit/src/Filament/Actions/AuditDetachAction.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var for variable \\$relationship contains generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany but does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/audit/src/Filament/Actions/AuditDetachAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Class AidingApp\\\\Audit\\\\Models\\\\Audit uses generic trait Illuminate\\\\Database\\\\Eloquent\\\\Factories\\\\HasFactory but does not specify its types\\: TFactory$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/audit/src/Models/Audit.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Audit\\\\Models\\\\Audit\\:\\:prunable\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder does not specify its types\\: TModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/audit/src/Models/Audit.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property OwenIt\\\\Auditing\\\\Contracts\\\\Auditable\\:\\:\\$auditCustomNew\\.$#',
	'identifier' => 'property.notFound',
	'count' => 4,
	'path' => __DIR__ . '/app-modules/audit/src/Overrides/BelongsToMany.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property OwenIt\\\\Auditing\\\\Contracts\\\\Auditable\\:\\:\\$auditCustomOld\\.$#',
	'identifier' => 'property.notFound',
	'count' => 3,
	'path' => __DIR__ . '/app-modules/audit/src/Overrides/BelongsToMany.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property OwenIt\\\\Auditing\\\\Contracts\\\\Auditable\\:\\:\\$auditEvent\\.$#',
	'identifier' => 'property.notFound',
	'count' => 3,
	'path' => __DIR__ . '/app-modules/audit/src/Overrides/BelongsToMany.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property OwenIt\\\\Auditing\\\\Contracts\\\\Auditable\\:\\:\\$isCustomEvent\\.$#',
	'identifier' => 'property.notFound',
	'count' => 3,
	'path' => __DIR__ . '/app-modules/audit/src/Overrides/BelongsToMany.php',
];
$ignoreErrors[] = [
	'message' => '#^Class AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany extends generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany but does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/audit/src/Overrides/BelongsToMany.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\:\\:attach\\(\\) has parameter \\$attributes with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/audit/src/Overrides/BelongsToMany.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\:\\:isAuditable\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/audit/src/Overrides/BelongsToMany.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\:\\:sync\\(\\) has parameter \\$ids with generic class Illuminate\\\\Support\\\\Collection but does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/audit/src/Overrides/BelongsToMany.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\:\\:sync\\(\\) has parameter \\$ids with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/audit/src/Overrides/BelongsToMany.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\:\\:sync\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 3,
	'path' => __DIR__ . '/app-modules/audit/src/Overrides/BelongsToMany.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property OwenIt\\\\Auditing\\\\Contracts\\\\Auditable\\:\\:\\$auditCustomNew\\.$#',
	'identifier' => 'property.notFound',
	'count' => 4,
	'path' => __DIR__ . '/app-modules/audit/src/Overrides/MorphToMany.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property OwenIt\\\\Auditing\\\\Contracts\\\\Auditable\\:\\:\\$auditCustomOld\\.$#',
	'identifier' => 'property.notFound',
	'count' => 3,
	'path' => __DIR__ . '/app-modules/audit/src/Overrides/MorphToMany.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property OwenIt\\\\Auditing\\\\Contracts\\\\Auditable\\:\\:\\$auditEvent\\.$#',
	'identifier' => 'property.notFound',
	'count' => 3,
	'path' => __DIR__ . '/app-modules/audit/src/Overrides/MorphToMany.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property OwenIt\\\\Auditing\\\\Contracts\\\\Auditable\\:\\:\\$isCustomEvent\\.$#',
	'identifier' => 'property.notFound',
	'count' => 3,
	'path' => __DIR__ . '/app-modules/audit/src/Overrides/MorphToMany.php',
];
$ignoreErrors[] = [
	'message' => '#^Class AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany extends generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany but does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/audit/src/Overrides/MorphToMany.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\:\\:attach\\(\\) has parameter \\$attributes with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/audit/src/Overrides/MorphToMany.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\:\\:isAuditable\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/audit/src/Overrides/MorphToMany.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\:\\:sync\\(\\) has parameter \\$ids with generic class Illuminate\\\\Support\\\\Collection but does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/audit/src/Overrides/MorphToMany.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\:\\:sync\\(\\) has parameter \\$ids with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/audit/src/Overrides/MorphToMany.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\:\\:sync\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 3,
	'path' => __DIR__ . '/app-modules/audit/src/Overrides/MorphToMany.php',
];
$ignoreErrors[] = [
	'message' => '#^Property AidingApp\\\\Audit\\\\Settings\\\\AuditSettings\\:\\:\\$audited_models_exclude type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/audit/src/Settings/AuditSettings.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method Pest\\\\PendingCalls\\\\TestCall\\|Pest\\\\Support\\\\HigherOrderTapProxy\\:\\:expect\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/audit/tests/AuditTraitUsageTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Authorization\\\\Enums\\\\AzureMatchingProperty\\:\\:getLabel\\(\\) never returns null so it can be removed from the return type\\.$#',
	'identifier' => 'return.unusedType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/authorization/src/Enums/AzureMatchingProperty.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Authorization\\\\Enums\\\\LicenseType\\:\\:getLabel\\(\\) never returns null so it can be removed from the return type\\.$#',
	'identifier' => 'return.unusedType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/authorization/src/Enums/LicenseType.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method getMail\\(\\) on an unknown class AidingApp\\\\Authorization\\\\Enums\\\\User\\.$#',
	'identifier' => 'class.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/authorization/src/Enums/SocialiteProvider.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method getPrincipalName\\(\\) on an unknown class AidingApp\\\\Authorization\\\\Enums\\\\User\\.$#',
	'identifier' => 'class.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/authorization/src/Enums/SocialiteProvider.php',
];
$ignoreErrors[] = [
	'message' => '#^Match arm comparison between \'google\' and \'google\' is always true\\.$#',
	'identifier' => 'match.alwaysTrue',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/authorization/src/Enums/SocialiteProvider.php',
];
$ignoreErrors[] = [
	'message' => '#^Match arm comparison between AidingApp\\\\Authorization\\\\Enums\\\\AzureMatchingProperty\\:\\:Mail and AidingApp\\\\Authorization\\\\Enums\\\\AzureMatchingProperty\\:\\:Mail is always true\\.$#',
	'identifier' => 'match.alwaysTrue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/authorization/src/Enums/SocialiteProvider.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var for variable \\$user contains unknown class AidingApp\\\\Authorization\\\\Enums\\\\User\\.$#',
	'identifier' => 'class.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/authorization/src/Enums/SocialiteProvider.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Authorization\\\\Filament\\\\Pages\\\\Auth\\\\Login\\:\\:getSsoFormActions\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/authorization/src/Filament/Pages/Auth/Login.php',
];
$ignoreErrors[] = [
	'message' => '#^Property AidingApp\\\\Authorization\\\\Filament\\\\Pages\\\\Auth\\\\SetPassword\\:\\:\\$data type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/authorization/src/Filament/Pages/Auth/SetPassword.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method isSuperAdmin\\(\\) on an unknown class AidingApp\\\\Authorization\\\\Filament\\\\Pages\\\\User\\.$#',
	'identifier' => 'class.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/authorization/src/Filament/Pages/ManageGoogleSsoSettings.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var for variable \\$user contains unknown class AidingApp\\\\Authorization\\\\Filament\\\\Pages\\\\User\\.$#',
	'identifier' => 'class.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/authorization/src/Filament/Pages/ManageGoogleSsoSettings.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Builder\\:\\:api\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/authorization/src/Filament/Resources/PermissionResource/Pages/ListPermissions.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Builder\\:\\:web\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/authorization/src/Filament/Resources/PermissionResource/Pages/ListPermissions.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Authorization\\\\Filament\\\\Resources\\\\RoleResource\\:\\:getEloquentQuery\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder does not specify its types\\: TModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/authorization/src/Filament/Resources/RoleResource.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var for variable \\$resource contains unresolvable type\\.$#',
	'identifier' => 'varTag.unresolvableType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/authorization/src/Filament/Resources/RoleResource/Pages/EditRole.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var with type mixed is not subtype of native type string\\.$#',
	'identifier' => 'varTag.nativeType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/authorization/src/Filament/Resources/RoleResource/Pages/EditRole.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method Illuminate\\\\Contracts\\\\Database\\\\Eloquent\\\\Builder\\:\\:api\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/authorization/src/Filament/Resources/RoleResource/Pages/ListRoles.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method Illuminate\\\\Contracts\\\\Database\\\\Eloquent\\\\Builder\\:\\:web\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/authorization/src/Filament/Resources/RoleResource/Pages/ListRoles.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method Laravel\\\\Socialite\\\\Contracts\\\\Provider\\|Mockery\\\\MockInterface\\:\\:setConfig\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/authorization/src/Http/Controllers/SocialiteController.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Authorization\\\\Http\\\\Controllers\\\\SocialiteController\\:\\:callback\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/authorization/src/Http/Controllers/SocialiteController.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Authorization\\\\Http\\\\Controllers\\\\SocialiteController\\:\\:redirect\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/authorization/src/Http/Controllers/SocialiteController.php',
];
$ignoreErrors[] = [
	'message' => '#^Using nullsafe property access on non\\-nullable type App\\\\Models\\\\User\\. Use \\-\\> instead\\.$#',
	'identifier' => 'nullsafe.neverNull',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/authorization/src/Http/Controllers/SocialiteController.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method Laravel\\\\Socialite\\\\Contracts\\\\Provider\\|Mockery\\\\MockInterface\\:\\:getLogoutUrl\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/authorization/src/Http/Responses/Auth/SocialiteLogoutResponse.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Authorization\\\\Models\\\\License\\:\\:newBelongsToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/authorization/src/Models/License.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Authorization\\\\Models\\\\License\\:\\:newMorphToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/authorization/src/Models/License.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Authorization\\\\Models\\\\License\\:\\:user\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/authorization/src/Models/License.php',
];
$ignoreErrors[] = [
	'message' => '#^Class AidingApp\\\\Authorization\\\\Models\\\\Permission uses generic trait Illuminate\\\\Database\\\\Eloquent\\\\Factories\\\\HasFactory but does not specify its types\\: TFactory$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/authorization/src/Models/Permission.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Authorization\\\\Models\\\\Permission\\:\\:group\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/authorization/src/Models/Permission.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Authorization\\\\Models\\\\Permission\\:\\:newBelongsToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/authorization/src/Models/Permission.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Authorization\\\\Models\\\\Permission\\:\\:newMorphToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/authorization/src/Models/Permission.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Authorization\\\\Models\\\\Permission\\:\\:systemUsers\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/authorization/src/Models/Permission.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Authorization\\\\Models\\\\PermissionGroup\\:\\:permissions\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/authorization/src/Models/PermissionGroup.php',
];
$ignoreErrors[] = [
	'message' => '#^Class AidingApp\\\\Authorization\\\\Models\\\\Role uses generic trait Illuminate\\\\Database\\\\Eloquent\\\\Factories\\\\HasFactory but does not specify its types\\: TFactory$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/authorization/src/Models/Role.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Authorization\\\\Models\\\\Role\\:\\:newBelongsToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/authorization/src/Models/Role.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Authorization\\\\Models\\\\Role\\:\\:newMorphToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/authorization/src/Models/Role.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Authorization\\\\Models\\\\Role\\:\\:scopeApi\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/authorization/src/Models/Role.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Authorization\\\\Models\\\\Role\\:\\:scopeSuperAdmin\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/authorization/src/Models/Role.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Authorization\\\\Models\\\\Role\\:\\:scopeWeb\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/authorization/src/Models/Role.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Authorization\\\\Models\\\\Role\\:\\:users\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/authorization/src/Models/Role.php',
];
$ignoreErrors[] = [
	'message' => '#^Unable to resolve the template type TRelatedModel in call to method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:morphedByMany\\(\\)$#',
	'identifier' => 'argument.templateType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/authorization/src/Models/Role.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Authorization\\\\Settings\\\\AzureSsoSettings\\:\\:encrypted\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/authorization/src/Settings/AzureSsoSettings.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Authorization\\\\Settings\\\\GoogleSsoSettings\\:\\:encrypted\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/authorization/src/Settings/GoogleSsoSettings.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$view of function view expects view\\-string\\|null, string given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/authorization/src/View/Components/Login.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property Pest\\\\Expectation\\<App\\\\Models\\\\User\\|null\\>\\:\\:\\$password\\.$#',
	'identifier' => 'property.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/authorization/tests/Feature/Filament/Pages/SetPasswordTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method call\\(\\) on an unknown class static\\.$#',
	'identifier' => 'class.notFound',
	'count' => 4,
	'path' => __DIR__ . '/app-modules/authorization/tests/Feature/Filament/Pages/SetPasswordTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method Illuminate\\\\Testing\\\\TestResponse\\:\\:assertActionHidden\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/authorization/tests/Feature/Filament/Resources/UserResources/Pages/EditUserTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method Illuminate\\\\Testing\\\\TestResponse\\:\\:assertActionVisible\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/authorization/tests/Feature/Filament/Resources/UserResources/Pages/EditUserTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method Illuminate\\\\Testing\\\\TestResponse\\:\\:callAction\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/authorization/tests/Feature/Filament/Resources/UserResources/Pages/EditUserTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method assertFormFieldExists\\(\\) on an unknown class static\\.$#',
	'identifier' => 'class.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/authorization/tests/Feature/Filament/Resources/UserResources/Pages/EditUserTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method call\\(\\) on an unknown class static\\.$#',
	'identifier' => 'class.notFound',
	'count' => 4,
	'path' => __DIR__ . '/app-modules/authorization/tests/Feature/Filament/Resources/UserResources/Pages/EditUserTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$key of method Illuminate\\\\Support\\\\Collection\\<string,mixed\\>\\:\\:get\\(\\) expects "\\\\000\\*\\\\000appends"\\|"\\\\000\\*…"\\|"\\\\000\\*\\\\000attributes"\\|"\\\\000\\*\\\\000authPasswordName"\\|"\\\\000\\*\\\\000casts"\\|"\\\\000\\*\\\\000changes"\\|"\\\\000\\*\\\\000classCastCache"\\|"\\\\000\\*\\\\000connection"\\|"\\\\000\\*\\\\000dateFormat"\\|"\\\\000\\*\\\\000dispatchesEvents"\\|"\\\\000\\*\\\\000fillable"\\|"\\\\000\\*\\\\000forceDeleting"\\|"\\\\000\\*\\\\000guarded"\\|"\\\\000\\*\\\\000hidden"\\|"\\\\000\\*\\\\000keyType"\\|"\\\\000\\*\\\\000observables"\\|"\\\\000\\*\\\\000original"\\|"\\\\000\\*\\\\000perPage"\\|"\\\\000\\*\\\\000primaryKey"\\|"\\\\000\\*\\\\000relations"\\|"\\\\000\\*\\\\000rememberTokenName"\\|"\\\\000\\*\\\\000table"\\|"\\\\000\\*\\\\000touches"\\|"\\\\000\\*\\\\000visible"\\|"\\\\000\\*\\\\000with"\\|"\\\\000\\*\\\\000withCount"\\|"\\\\000App\\\\\\\\Models…"\\|\'auditCustomNew\'\\|\'auditCustomOld\'\\|\'auditEvent\'\\|\'exists\'\\|\'filterable\'\\|\'incrementing\'\\|\'isCustomEvent\'\\|\'mediaCollections\'\\|\'mediaConversions\'\\|\'orderable\'\\|\'preloadedResolverDa…\'\\|\'preventsLazyLoading\'\\|\'timestamps\'\\|\'usesUniqueIds\'\\|\'wasRecentlyCreated\', \'email\' given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/authorization/tests/Feature/Filament/Resources/UserResources/Pages/EditUserTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$key of method Illuminate\\\\Support\\\\Collection\\<string,mixed\\>\\:\\:get\\(\\) expects "\\\\000\\*\\\\000appends"\\|"\\\\000\\*…"\\|"\\\\000\\*\\\\000attributes"\\|"\\\\000\\*\\\\000authPasswordName"\\|"\\\\000\\*\\\\000casts"\\|"\\\\000\\*\\\\000changes"\\|"\\\\000\\*\\\\000classCastCache"\\|"\\\\000\\*\\\\000connection"\\|"\\\\000\\*\\\\000dateFormat"\\|"\\\\000\\*\\\\000dispatchesEvents"\\|"\\\\000\\*\\\\000fillable"\\|"\\\\000\\*\\\\000forceDeleting"\\|"\\\\000\\*\\\\000guarded"\\|"\\\\000\\*\\\\000hidden"\\|"\\\\000\\*\\\\000keyType"\\|"\\\\000\\*\\\\000observables"\\|"\\\\000\\*\\\\000original"\\|"\\\\000\\*\\\\000perPage"\\|"\\\\000\\*\\\\000primaryKey"\\|"\\\\000\\*\\\\000relations"\\|"\\\\000\\*\\\\000rememberTokenName"\\|"\\\\000\\*\\\\000table"\\|"\\\\000\\*\\\\000touches"\\|"\\\\000\\*\\\\000visible"\\|"\\\\000\\*\\\\000with"\\|"\\\\000\\*\\\\000withCount"\\|"\\\\000App\\\\\\\\Models…"\\|\'auditCustomNew\'\\|\'auditCustomOld\'\\|\'auditEvent\'\\|\'exists\'\\|\'filterable\'\\|\'incrementing\'\\|\'isCustomEvent\'\\|\'mediaCollections\'\\|\'mediaConversions\'\\|\'orderable\'\\|\'preloadedResolverDa…\'\\|\'preventsLazyLoading\'\\|\'timestamps\'\\|\'usesUniqueIds\'\\|\'wasRecentlyCreated\', \'is_external\' given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/authorization/tests/Feature/Filament/Resources/UserResources/Pages/EditUserTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$key of method Illuminate\\\\Support\\\\Collection\\<string,mixed\\>\\:\\:get\\(\\) expects "\\\\000\\*\\\\000appends"\\|"\\\\000\\*…"\\|"\\\\000\\*\\\\000attributes"\\|"\\\\000\\*\\\\000authPasswordName"\\|"\\\\000\\*\\\\000casts"\\|"\\\\000\\*\\\\000changes"\\|"\\\\000\\*\\\\000classCastCache"\\|"\\\\000\\*\\\\000connection"\\|"\\\\000\\*\\\\000dateFormat"\\|"\\\\000\\*\\\\000dispatchesEvents"\\|"\\\\000\\*\\\\000fillable"\\|"\\\\000\\*\\\\000forceDeleting"\\|"\\\\000\\*\\\\000guarded"\\|"\\\\000\\*\\\\000hidden"\\|"\\\\000\\*\\\\000keyType"\\|"\\\\000\\*\\\\000observables"\\|"\\\\000\\*\\\\000original"\\|"\\\\000\\*\\\\000perPage"\\|"\\\\000\\*\\\\000primaryKey"\\|"\\\\000\\*\\\\000relations"\\|"\\\\000\\*\\\\000rememberTokenName"\\|"\\\\000\\*\\\\000table"\\|"\\\\000\\*\\\\000touches"\\|"\\\\000\\*\\\\000visible"\\|"\\\\000\\*\\\\000with"\\|"\\\\000\\*\\\\000withCount"\\|"\\\\000App\\\\\\\\Models…"\\|\'auditCustomNew\'\\|\'auditCustomOld\'\\|\'auditEvent\'\\|\'exists\'\\|\'filterable\'\\|\'incrementing\'\\|\'isCustomEvent\'\\|\'mediaCollections\'\\|\'mediaConversions\'\\|\'orderable\'\\|\'preloadedResolverDa…\'\\|\'preventsLazyLoading\'\\|\'timestamps\'\\|\'usesUniqueIds\'\\|\'wasRecentlyCreated\', \'name\' given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/authorization/tests/Feature/Filament/Resources/UserResources/Pages/EditUserTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method Illuminate\\\\Testing\\\\TestResponse\\:\\:assertCountTableRecords\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 7,
	'path' => __DIR__ . '/app-modules/authorization/tests/Feature/Filament/Resources/UserResources/Pages/ListUsersTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method filterTable\\(\\) on an unknown class static\\.$#',
	'identifier' => 'class.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/authorization/tests/Feature/Filament/Resources/UserResources/Pages/ListUsersTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method Illuminate\\\\Testing\\\\TestResponse\\:\\:assertActionHidden\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/authorization/tests/Feature/Filament/Resources/UserResources/Pages/ViewUserTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method Illuminate\\\\Testing\\\\TestResponse\\:\\:assertActionVisible\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/authorization/tests/Feature/Filament/Resources/UserResources/Pages/ViewUserTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method Illuminate\\\\Testing\\\\TestResponse\\:\\:callAction\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/authorization/tests/Feature/Filament/Resources/UserResources/Pages/ViewUserTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Function something\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/authorization/tests/Pest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method Faker\\\\Generator\\:\\:secondaryAddress\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/database/factories/ContactFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method Faker\\\\Generator\\:\\:stateAbbr\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/database/factories/ContactFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method Faker\\\\Generator\\:\\:state\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/database/factories/OrganizationFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$guardName of method Illuminate\\\\Database\\\\Migrations\\\\Migration@anonymous/app\\-modules/contact/database/migrations/2024_06_09_122855_data_seed_permissions_for_organization_module\\.php\\:42\\:\\:deletePermissions\\(\\) expects string, array given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/database/migrations/2024_06_09_122855_data_seed_permissions_for_organization_module.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Illuminate\\\\Database\\\\Migrations\\\\Migration@anonymous/app\\-modules/contact/database/migrations/2024_06_09_122855_data_seed_permissions_for_organization_module\\.php\\:42\\:\\:\\$guards type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/database/migrations/2024_06_09_122855_data_seed_permissions_for_organization_module.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Illuminate\\\\Database\\\\Migrations\\\\Migration@anonymous/app\\-modules/contact/database/migrations/2024_06_09_122855_data_seed_permissions_for_organization_module\\.php\\:42\\:\\:\\$permissions type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/database/migrations/2024_06_09_122855_data_seed_permissions_for_organization_module.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$guardName of method Illuminate\\\\Database\\\\Migrations\\\\Migration@anonymous/app\\-modules/contact/database/migrations/2024_06_09_124416_data_seed_permissions_for_organization_type_module\\.php\\:42\\:\\:deletePermissions\\(\\) expects string, array given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/database/migrations/2024_06_09_124416_data_seed_permissions_for_organization_type_module.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Illuminate\\\\Database\\\\Migrations\\\\Migration@anonymous/app\\-modules/contact/database/migrations/2024_06_09_124416_data_seed_permissions_for_organization_type_module\\.php\\:42\\:\\:\\$guards type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/database/migrations/2024_06_09_124416_data_seed_permissions_for_organization_type_module.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Illuminate\\\\Database\\\\Migrations\\\\Migration@anonymous/app\\-modules/contact/database/migrations/2024_06_09_124416_data_seed_permissions_for_organization_type_module\\.php\\:42\\:\\:\\$permissions type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/database/migrations/2024_06_09_124416_data_seed_permissions_for_organization_type_module.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$guardName of method Illuminate\\\\Database\\\\Migrations\\\\Migration@anonymous/app\\-modules/contact/database/migrations/2024_06_09_124444_data_seed_permissions_for_organization_industry_module\\.php\\:42\\:\\:deletePermissions\\(\\) expects string, array given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/database/migrations/2024_06_09_124444_data_seed_permissions_for_organization_industry_module.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Illuminate\\\\Database\\\\Migrations\\\\Migration@anonymous/app\\-modules/contact/database/migrations/2024_06_09_124444_data_seed_permissions_for_organization_industry_module\\.php\\:42\\:\\:\\$guards type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/database/migrations/2024_06_09_124444_data_seed_permissions_for_organization_industry_module.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Illuminate\\\\Database\\\\Migrations\\\\Migration@anonymous/app\\-modules/contact/database/migrations/2024_06_09_124444_data_seed_permissions_for_organization_industry_module\\.php\\:42\\:\\:\\$permissions type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/database/migrations/2024_06_09_124444_data_seed_permissions_for_organization_industry_module.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Contact\\\\Enums\\\\SystemContactClassification\\:\\:getLabel\\(\\) never returns null so it can be removed from the return type\\.$#',
	'identifier' => 'return.unusedType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Enums/SystemContactClassification.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$email\\.$#',
	'identifier' => 'property.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Filament/Resources/ContactResource.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$email_id\\.$#',
	'identifier' => 'property.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Filament/Resources/ContactResource.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$mobile\\.$#',
	'identifier' => 'property.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Filament/Resources/ContactResource.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$otherid\\.$#',
	'identifier' => 'property.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Filament/Resources/ContactResource.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$phone\\.$#',
	'identifier' => 'property.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Filament/Resources/ContactResource.php',
];
$ignoreErrors[] = [
	'message' => '#^Unable to resolve the template type TValue in call to function collect$#',
	'identifier' => 'argument.templateType',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/contact/src/Filament/Resources/ContactResource.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined static method class\\-string\\<Filament\\\\Resources\\\\RelationManagers\\\\RelationManager\\>\\|Filament\\\\Resources\\\\RelationManagers\\\\RelationManagerConfiguration\\:\\:canViewForRecord\\(\\)\\.$#',
	'identifier' => 'staticMethod.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Filament/Resources/ContactResource/Pages/AssetManagement.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Contact\\\\Filament\\\\Resources\\\\ContactResource\\\\Pages\\\\AssetManagement\\:\\:filterRelationManagers\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Filament/Resources/ContactResource/Pages/AssetManagement.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Contact\\\\Filament\\\\Resources\\\\ContactResource\\\\Pages\\\\AssetManagement\\:\\:filterRelationManagers\\(\\) has parameter \\$record with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Filament/Resources/ContactResource/Pages/AssetManagement.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Contact\\\\Filament\\\\Resources\\\\ContactResource\\\\Pages\\\\AssetManagement\\:\\:filterRelationManagers\\(\\) has parameter \\$relationManager with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Filament/Resources/ContactResource/Pages/AssetManagement.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Contact\\\\Filament\\\\Resources\\\\ContactResource\\\\Pages\\\\AssetManagement\\:\\:managers\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Filament/Resources/ContactResource/Pages/AssetManagement.php',
];
$ignoreErrors[] = [
	'message' => '#^Unsafe call to private method AidingApp\\\\Contact\\\\Filament\\\\Resources\\\\ContactResource\\\\Pages\\\\AssetManagement\\:\\:managers\\(\\) through static\\:\\:\\.$#',
	'identifier' => 'staticClassAccess.privateMethod',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/contact/src/Filament/Resources/ContactResource/Pages/AssetManagement.php',
];
$ignoreErrors[] = [
	'message' => '#^Property AidingApp\\\\Contact\\\\Filament\\\\Resources\\\\ContactResource\\\\Pages\\\\ContactEngagementTimeline\\:\\:\\$modelsToTimeline type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Filament/Resources/ContactResource/Pages/ContactEngagementTimeline.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined static method class\\-string\\<Filament\\\\Resources\\\\RelationManagers\\\\RelationManager\\>\\|Filament\\\\Resources\\\\RelationManagers\\\\RelationManagerConfiguration\\:\\:canViewForRecord\\(\\)\\.$#',
	'identifier' => 'staticMethod.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Filament/Resources/ContactResource/Pages/ContactServiceManagement.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Contact\\\\Filament\\\\Resources\\\\ContactResource\\\\Pages\\\\ContactServiceManagement\\:\\:filterRelationManagers\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Filament/Resources/ContactResource/Pages/ContactServiceManagement.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Contact\\\\Filament\\\\Resources\\\\ContactResource\\\\Pages\\\\ContactServiceManagement\\:\\:filterRelationManagers\\(\\) has parameter \\$record with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Filament/Resources/ContactResource/Pages/ContactServiceManagement.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Contact\\\\Filament\\\\Resources\\\\ContactResource\\\\Pages\\\\ContactServiceManagement\\:\\:filterRelationManagers\\(\\) has parameter \\$relationManager with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Filament/Resources/ContactResource/Pages/ContactServiceManagement.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Contact\\\\Filament\\\\Resources\\\\ContactResource\\\\Pages\\\\ContactServiceManagement\\:\\:managers\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Filament/Resources/ContactResource/Pages/ContactServiceManagement.php',
];
$ignoreErrors[] = [
	'message' => '#^Unsafe call to private method AidingApp\\\\Contact\\\\Filament\\\\Resources\\\\ContactResource\\\\Pages\\\\ContactServiceManagement\\:\\:managers\\(\\) through static\\:\\:\\.$#',
	'identifier' => 'staticClassAccess.privateMethod',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/contact/src/Filament/Resources/ContactResource/Pages/ContactServiceManagement.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var for variable \\$resource contains unresolvable type\\.$#',
	'identifier' => 'varTag.unresolvableType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Filament/Resources/ContactResource/Pages/EditContact.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var with type mixed is not subtype of native type string\\.$#',
	'identifier' => 'varTag.nativeType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Filament/Resources/ContactResource/Pages/EditContact.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),Illuminate\\\\Database\\\\Eloquent\\\\Model\\>\\:\\:each\\(\\) expects callable\\(Illuminate\\\\Database\\\\Eloquent\\\\Model, int\\|string\\)\\: mixed, Closure\\(AidingApp\\\\Contact\\\\Models\\\\Contact\\)\\: bool given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Filament/Resources/ContactResource/Pages/ListContacts.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphMany\\:\\:status\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Filament/Resources/ContactResource/Pages/ManageContactAlerts.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$badge of method Filament\\\\Navigation\\\\NavigationItem\\:\\:badge\\(\\) expects Closure\\|string\\|null, int\\<1, max\\>\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Filament/Resources/ContactResource/Pages/ManageContactAlerts.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Contact\\\\Filament\\\\Resources\\\\ContactResource\\\\Pages\\\\ManageContactEngagement\\:\\:managers\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Filament/Resources/ContactResource/Pages/ManageContactEngagement.php',
];
$ignoreErrors[] = [
	'message' => '#^Unsafe call to private method AidingApp\\\\Contact\\\\Filament\\\\Resources\\\\ContactResource\\\\Pages\\\\ManageContactEngagement\\:\\:managers\\(\\) through static\\:\\:\\.$#',
	'identifier' => 'staticClassAccess.privateMethod',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/contact/src/Filament/Resources/ContactResource/Pages/ManageContactEngagement.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Contact\\\\Filament\\\\Resources\\\\ContactResource\\\\Pages\\\\ManageContactFiles\\:\\:managers\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Filament/Resources/ContactResource/Pages/ManageContactFiles.php',
];
$ignoreErrors[] = [
	'message' => '#^Unsafe call to private method AidingApp\\\\Contact\\\\Filament\\\\Resources\\\\ContactResource\\\\Pages\\\\ManageContactFiles\\:\\:managers\\(\\) through static\\:\\:\\.$#',
	'identifier' => 'staticClassAccess.privateMethod',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/contact/src/Filament/Resources/ContactResource/Pages/ManageContactFiles.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Contact\\\\Filament\\\\Resources\\\\ContactResource\\\\RelationManagers\\\\AssetCheckInRelationManager\\:\\:renderInfolist\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Filament/Resources/ContactResource/RelationManagers/AssetCheckInRelationManager.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Contact\\\\Filament\\\\Resources\\\\ContactResource\\\\RelationManagers\\\\AssetCheckOutRelationManager\\:\\:renderInfolist\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Filament/Resources/ContactResource/RelationManagers/AssetCheckOutRelationManager.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$sent_at\\.$#',
	'identifier' => 'property.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Filament/Resources/ContactResource/RelationManagers/EngagementsRelationManager.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$subject\\.$#',
	'identifier' => 'property.notFound',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/contact/src/Filament/Resources/ContactResource/RelationManagers/EngagementsRelationManager.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$user\\.$#',
	'identifier' => 'property.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Filament/Resources/ContactResource/RelationManagers/EngagementsRelationManager.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:getBody\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/contact/src/Filament/Resources/ContactResource/RelationManagers/EngagementsRelationManager.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method getDeliveryMethod\\(\\) on an unknown class AidingApp\\\\Contact\\\\Filament\\\\Resources\\\\ContactResource\\\\RelationManagers\\\\HasDeliveryMethod\\.$#',
	'identifier' => 'class.notFound',
	'count' => 3,
	'path' => __DIR__ . '/app-modules/contact/src/Filament/Resources/ContactResource/RelationManagers/EngagementsRelationManager.php',
];
$ignoreErrors[] = [
	'message' => '#^Match expression does not handle remaining value\\: class\\-string\\<Illuminate\\\\Database\\\\Eloquent\\\\Model\\>&literal\\-string$#',
	'identifier' => 'match.unhandled',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/contact/src/Filament/Resources/ContactResource/RelationManagers/EngagementsRelationManager.php',
];
$ignoreErrors[] = [
	'message' => '#^Match expression does not handle remaining value\\: string$#',
	'identifier' => 'match.unhandled',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/contact/src/Filament/Resources/ContactResource/RelationManagers/EngagementsRelationManager.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var for variable \\$timelineable contains unknown class AidingApp\\\\Contact\\\\Filament\\\\Resources\\\\ContactResource\\\\RelationManagers\\\\HasDeliveryMethod\\.$#',
	'identifier' => 'class.notFound',
	'count' => 3,
	'path' => __DIR__ . '/app-modules/contact/src/Filament/Resources/ContactResource/RelationManagers/EngagementsRelationManager.php',
];
$ignoreErrors[] = [
	'message' => '#^Using nullsafe method call on non\\-nullable type AidingApp\\\\Notification\\\\Enums\\\\EmailMessageEventType\\. Use \\-\\> instead\\.$#',
	'identifier' => 'nullsafe.neverNull',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Filament/Resources/ContactResource/RelationManagers/EngagementsRelationManager.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var for variable \\$resource contains unresolvable type\\.$#',
	'identifier' => 'varTag.unresolvableType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Filament/Resources/ContactSourceResource/Pages/EditContactSource.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var with type mixed is not subtype of native type string\\.$#',
	'identifier' => 'varTag.nativeType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Filament/Resources/ContactSourceResource/Pages/EditContactSource.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var for variable \\$resource contains unresolvable type\\.$#',
	'identifier' => 'varTag.unresolvableType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Filament/Resources/ContactStatusResource/Pages/EditContactStatus.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var with type mixed is not subtype of native type string\\.$#',
	'identifier' => 'varTag.nativeType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Filament/Resources/ContactStatusResource/Pages/EditContactStatus.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var for variable \\$resource contains unresolvable type\\.$#',
	'identifier' => 'varTag.unresolvableType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Filament/Resources/OrganizationIndustryResource/Pages/EditOrganizationIndustry.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var with type mixed is not subtype of native type string\\.$#',
	'identifier' => 'varTag.nativeType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Filament/Resources/OrganizationIndustryResource/Pages/EditOrganizationIndustry.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access property \\$id on Illuminate\\\\Database\\\\Eloquent\\\\Model\\|int\\|string\\.$#',
	'identifier' => 'property.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Filament/Resources/OrganizationResource/Pages/EditOrganization.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var for variable \\$resource contains unresolvable type\\.$#',
	'identifier' => 'varTag.unresolvableType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Filament/Resources/OrganizationResource/Pages/EditOrganization.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var with type mixed is not subtype of native type string\\.$#',
	'identifier' => 'varTag.nativeType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Filament/Resources/OrganizationResource/Pages/EditOrganization.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var for variable \\$resource contains unresolvable type\\.$#',
	'identifier' => 'varTag.unresolvableType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Filament/Resources/OrganizationTypeResource/Pages/EditOrganizationType.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var with type mixed is not subtype of native type string\\.$#',
	'identifier' => 'varTag.nativeType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Filament/Resources/OrganizationTypeResource/Pages/EditOrganizationType.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method Staudenmeir\\\\EloquentHasManyDeep\\\\HasManyDeep\\:\\:status\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Filament/Widgets/ContactStats.php',
];
$ignoreErrors[] = [
	'message' => '#^Class AidingApp\\\\Contact\\\\Models\\\\Contact uses generic trait Illuminate\\\\Database\\\\Eloquent\\\\Factories\\\\HasFactory but does not specify its types\\: TFactory$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Models/Contact.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Contact\\\\Models\\\\Contact\\:\\:alerts\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Models/Contact.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Contact\\\\Models\\\\Contact\\:\\:assetCheckIns\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Models/Contact.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Contact\\\\Models\\\\Contact\\:\\:assetCheckOuts\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Models/Contact.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Contact\\\\Models\\\\Contact\\:\\:assignedTo\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Models/Contact.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Contact\\\\Models\\\\Contact\\:\\:createdBy\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Models/Contact.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Contact\\\\Models\\\\Contact\\:\\:displayName\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Casts\\\\Attribute does not specify its types\\: TGet, TSet$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Models/Contact.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Contact\\\\Models\\\\Contact\\:\\:engagementFiles\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Models/Contact.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Contact\\\\Models\\\\Contact\\:\\:engagementResponses\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Models/Contact.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Contact\\\\Models\\\\Contact\\:\\:engagements\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Models/Contact.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Contact\\\\Models\\\\Contact\\:\\:knowledgeBaseArticleVotes\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Models/Contact.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Contact\\\\Models\\\\Contact\\:\\:newBelongsToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Models/Contact.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Contact\\\\Models\\\\Contact\\:\\:newMorphToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Models/Contact.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Contact\\\\Models\\\\Contact\\:\\:orderedEngagementResponses\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Models/Contact.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Contact\\\\Models\\\\Contact\\:\\:orderedEngagements\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Models/Contact.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Contact\\\\Models\\\\Contact\\:\\:organization\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Models/Contact.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Contact\\\\Models\\\\Contact\\:\\:productLicenses\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Models/Contact.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Contact\\\\Models\\\\Contact\\:\\:serviceRequests\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Models/Contact.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Contact\\\\Models\\\\Contact\\:\\:source\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Models/Contact.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Contact\\\\Models\\\\Contact\\:\\:status\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Models/Contact.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Contact\\\\Models\\\\Contact\\:\\:subscribedUsers\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Models/Contact.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Contact\\\\Models\\\\Contact\\:\\:subscriptions\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Models/Contact.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Contact\\\\Models\\\\Contact\\:\\:tasks\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Models/Contact.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Contact\\\\Models\\\\Contact\\:\\:timeline\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphOne does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Models/Contact.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Contact\\\\Models\\\\ContactSource\\:\\:contacts\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Models/ContactSource.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Contact\\\\Models\\\\ContactSource\\:\\:newBelongsToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Models/ContactSource.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Contact\\\\Models\\\\ContactSource\\:\\:newMorphToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Models/ContactSource.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Contact\\\\Models\\\\ContactStatus\\:\\:contacts\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Models/ContactStatus.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Contact\\\\Models\\\\ContactStatus\\:\\:newBelongsToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Models/ContactStatus.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Contact\\\\Models\\\\ContactStatus\\:\\:newMorphToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Models/ContactStatus.php',
];
$ignoreErrors[] = [
	'message' => '#^Class AidingApp\\\\Contact\\\\Models\\\\Organization uses generic trait Illuminate\\\\Database\\\\Eloquent\\\\Factories\\\\HasFactory but does not specify its types\\: TFactory$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Models/Organization.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Contact\\\\Models\\\\Organization\\:\\:contacts\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Models/Organization.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Contact\\\\Models\\\\Organization\\:\\:industry\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Models/Organization.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Contact\\\\Models\\\\Organization\\:\\:newBelongsToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Models/Organization.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Contact\\\\Models\\\\Organization\\:\\:newMorphToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Models/Organization.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Contact\\\\Models\\\\Organization\\:\\:type\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Models/Organization.php',
];
$ignoreErrors[] = [
	'message' => '#^Class AidingApp\\\\Contact\\\\Models\\\\OrganizationIndustry uses generic trait Illuminate\\\\Database\\\\Eloquent\\\\Factories\\\\HasFactory but does not specify its types\\: TFactory$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Models/OrganizationIndustry.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Contact\\\\Models\\\\OrganizationIndustry\\:\\:newBelongsToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Models/OrganizationIndustry.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Contact\\\\Models\\\\OrganizationIndustry\\:\\:newMorphToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Models/OrganizationIndustry.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Contact\\\\Models\\\\OrganizationIndustry\\:\\:organizations\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Models/OrganizationIndustry.php',
];
$ignoreErrors[] = [
	'message' => '#^Class AidingApp\\\\Contact\\\\Models\\\\OrganizationType uses generic trait Illuminate\\\\Database\\\\Eloquent\\\\Factories\\\\HasFactory but does not specify its types\\: TFactory$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Models/OrganizationType.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Contact\\\\Models\\\\OrganizationType\\:\\:newBelongsToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Models/OrganizationType.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Contact\\\\Models\\\\OrganizationType\\:\\:newMorphToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Models/OrganizationType.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Contact\\\\Models\\\\OrganizationType\\:\\:organizations\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Models/OrganizationType.php',
];
$ignoreErrors[] = [
	'message' => '#^Using nullsafe property access on non\\-nullable type AidingApp\\\\Contact\\\\Models\\\\ContactSource\\. Use \\-\\> instead\\.$#',
	'identifier' => 'nullsafe.neverNull',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Observers/ContactObserver.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Contact\\\\Rules\\\\UniqueOrganizationDomain\\:\\:message\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Rules/UniqueOrganizationDomain.php',
];
$ignoreErrors[] = [
	'message' => '#^Property AidingApp\\\\Contact\\\\Rules\\\\UniqueOrganizationDomain\\:\\:\\$ignoreId has no type specified\\.$#',
	'identifier' => 'missingType.property',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Rules/UniqueOrganizationDomain.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method call\\(\\) on an unknown class static\\.$#',
	'identifier' => 'class.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/tests/Contact/CreateContactTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method call\\(\\) on an unknown class static\\.$#',
	'identifier' => 'class.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/tests/Contact/EditContactTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method assertCountTableRecords\\(\\) on an unknown class static\\.$#',
	'identifier' => 'class.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/tests/Contact/ListContactTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method assertHasNoTableBulkActionErrors\\(\\) on an unknown class static\\.$#',
	'identifier' => 'class.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/tests/Contact/ListContactTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method assertSuccessful\\(\\) on an unknown class static\\.$#',
	'identifier' => 'class.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/tests/Contact/ListContactTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method filterTable\\(\\) on an unknown class static\\.$#',
	'identifier' => 'class.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/tests/Contact/ListContactTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$entity of class AidingApp\\\\Timeline\\\\Events\\\\TimelineableRecordCreated constructor expects Illuminate\\\\Database\\\\Eloquent\\\\Model, AidingApp\\\\Engagement\\\\Models\\\\Educatable given\\.$#',
	'identifier' => 'argument.type',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/contact/tests/Contact/ListContactTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Using nullsafe property access "\\?\\-\\>id" on left side of \\?\\? is unnecessary\\. Use \\-\\> instead\\.$#',
	'identifier' => 'nullsafe.neverNull',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/contact/tests/Contact/RequestFactories/EditContactRequestFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to function PHPUnit\\\\Framework\\\\assertEmpty\\(\\) with Illuminate\\\\Database\\\\Eloquent\\\\Collection\\<int, AidingApp\\\\Contact\\\\Models\\\\ContactSource\\> will always evaluate to false\\.$#',
	'identifier' => 'function.impossibleType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/tests/ContactSource/CreateContactSourceTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method call\\(\\) on an unknown class static\\.$#',
	'identifier' => 'class.notFound',
	'count' => 3,
	'path' => __DIR__ . '/app-modules/contact/tests/ContactSource/CreateContactSourceTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method call\\(\\) on an unknown class static\\.$#',
	'identifier' => 'class.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/tests/ContactSource/EditContactSourceTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method fillForm\\(\\) on an unknown class static\\.$#',
	'identifier' => 'class.notFound',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/contact/tests/ContactSource/EditContactSourceTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method Illuminate\\\\Testing\\\\TestResponse\\:\\:assertCanSeeTableRecords\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/tests/ContactSource/ListContactSourcesTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method assertTableColumnStateSet\\(\\) on an unknown class static\\.$#',
	'identifier' => 'class.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/tests/ContactSource/ListContactSourcesTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to function PHPUnit\\\\Framework\\\\assertEmpty\\(\\) with Illuminate\\\\Database\\\\Eloquent\\\\Collection\\<int, AidingApp\\\\Contact\\\\Models\\\\ContactStatus\\> will always evaluate to false\\.$#',
	'identifier' => 'function.impossibleType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/tests/ContactStatus/CreateContactStatusTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method call\\(\\) on an unknown class static\\.$#',
	'identifier' => 'class.notFound',
	'count' => 3,
	'path' => __DIR__ . '/app-modules/contact/tests/ContactStatus/CreateContactStatusTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method call\\(\\) on an unknown class static\\.$#',
	'identifier' => 'class.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/tests/ContactStatus/EditContactStatusTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method fillForm\\(\\) on an unknown class static\\.$#',
	'identifier' => 'class.notFound',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/contact/tests/ContactStatus/EditContactStatusTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method Illuminate\\\\Testing\\\\TestResponse\\:\\:assertCanSeeTableRecords\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/tests/ContactStatus/ListContactStatusesTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method assertTableColumnStateSet\\(\\) on an unknown class static\\.$#',
	'identifier' => 'class.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/tests/ContactStatus/ListContactStatusesTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method call\\(\\) on an unknown class static\\.$#',
	'identifier' => 'class.notFound',
	'count' => 4,
	'path' => __DIR__ . '/app-modules/contact/tests/Organization/CreateOrganizationTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method call\\(\\) on an unknown class static\\.$#',
	'identifier' => 'class.notFound',
	'count' => 4,
	'path' => __DIR__ . '/app-modules/contact/tests/Organization/EditOrganizationTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method Faker\\\\Generator\\:\\:state\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/tests/Organization/RequestFactories/CreateOrganizationRequestFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method Faker\\\\Generator\\:\\:state\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/tests/Organization/RequestFactories/EditOrganizationRequestFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method call\\(\\) on an unknown class static\\.$#',
	'identifier' => 'class.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/tests/OrganizationIndustry/CreateOrganizationIndustryTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method call\\(\\) on an unknown class static\\.$#',
	'identifier' => 'class.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/tests/OrganizationIndustry/EditOrganizationIndustryTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method call\\(\\) on an unknown class static\\.$#',
	'identifier' => 'class.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/tests/OrganizationType/CreateOrganizationTypeTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method call\\(\\) on an unknown class static\\.$#',
	'identifier' => 'class.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/tests/OrganizationType/EditOrganizationTypeTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Class AidingApp\\\\ContractManagement\\\\Database\\\\Factories\\\\ContractFactory extends generic class Illuminate\\\\Database\\\\Eloquent\\\\Factories\\\\Factory but does not specify its types\\: TModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contract-management/database/factories/ContractFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @extends has invalid value \\(Contract\\>\\)\\: Unexpected token "\\>", expected \'\\<\' at offset 24 on line 2$#',
	'identifier' => 'phpDoc.parseError',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contract-management/database/factories/ContractFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$money of static method Cknow\\\\Money\\\\Money\\:\\:parseByDecimal\\(\\) expects string, int given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contract-management/database/factories/ContractFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Class AidingApp\\\\ContractManagement\\\\Database\\\\Factories\\\\ContractTypeFactory extends generic class Illuminate\\\\Database\\\\Eloquent\\\\Factories\\\\Factory but does not specify its types\\: TModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contract-management/database/factories/ContractTypeFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ContractManagement\\\\Database\\\\Factories\\\\ContractTypeFactory\\:\\:default\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Factories\\\\Factory does not specify its types\\: TModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contract-management/database/factories/ContractTypeFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @extends has invalid value \\(ContractType\\>\\)\\: Unexpected token "\\>", expected \'\\<\' at offset 28 on line 2$#',
	'identifier' => 'phpDoc.parseError',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contract-management/database/factories/ContractTypeFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$guardName of method Illuminate\\\\Database\\\\Migrations\\\\Migration@anonymous/app\\-modules/contract\\-management/database/migrations/2024_11_26_170828_seed_permissions_for_contract\\.php\\:42\\:\\:deletePermissions\\(\\) expects string, array given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contract-management/database/migrations/2024_11_26_170828_seed_permissions_for_contract.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Illuminate\\\\Database\\\\Migrations\\\\Migration@anonymous/app\\-modules/contract\\-management/database/migrations/2024_11_26_170828_seed_permissions_for_contract\\.php\\:42\\:\\:\\$guards type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contract-management/database/migrations/2024_11_26_170828_seed_permissions_for_contract.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Illuminate\\\\Database\\\\Migrations\\\\Migration@anonymous/app\\-modules/contract\\-management/database/migrations/2024_11_26_170828_seed_permissions_for_contract\\.php\\:42\\:\\:\\$permissions type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contract-management/database/migrations/2024_11_26_170828_seed_permissions_for_contract.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ContractManagement\\\\Enums\\\\ContractStatus\\:\\:getStatus\\(\\) has parameter \\$endDate with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contract-management/src/Enums/ContractStatus.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ContractManagement\\\\Enums\\\\ContractStatus\\:\\:getStatus\\(\\) has parameter \\$startDate with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contract-management/src/Enums/ContractStatus.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$contract_value\\.$#',
	'identifier' => 'property.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contract-management/src/Filament/Resources/ContractResource/Pages/EditContract.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var for variable \\$resource contains unresolvable type\\.$#',
	'identifier' => 'varTag.unresolvableType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contract-management/src/Filament/Resources/ContractResource/Pages/EditContract.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var with type mixed is not subtype of native type string\\.$#',
	'identifier' => 'varTag.nativeType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contract-management/src/Filament/Resources/ContractResource/Pages/EditContract.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var for variable \\$resource contains unresolvable type\\.$#',
	'identifier' => 'varTag.unresolvableType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contract-management/src/Filament/Resources/ContractTypeResource/Pages/EditContractType.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var with type mixed is not subtype of native type string\\.$#',
	'identifier' => 'varTag.nativeType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contract-management/src/Filament/Resources/ContractTypeResource/Pages/EditContractType.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ContractManagement\\\\Models\\\\Contract\\:\\:contractType\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contract-management/src/Models/Contract.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ContractManagement\\\\Models\\\\Contract\\:\\:newBelongsToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contract-management/src/Models/Contract.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ContractManagement\\\\Models\\\\Contract\\:\\:newMorphToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contract-management/src/Models/Contract.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ContractManagement\\\\Models\\\\Contract\\:\\:status\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Casts\\\\Attribute does not specify its types\\: TGet, TSet$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contract-management/src/Models/Contract.php',
];
$ignoreErrors[] = [
	'message' => '#^Property AidingApp\\\\ContractManagement\\\\Models\\\\Contract\\:\\:\\$append has no type specified\\.$#',
	'identifier' => 'missingType.property',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contract-management/src/Models/Contract.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ContractManagement\\\\Models\\\\ContractType\\:\\:contracts\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contract-management/src/Models/ContractType.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ContractManagement\\\\Models\\\\ContractType\\:\\:newBelongsToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contract-management/src/Models/ContractType.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ContractManagement\\\\Models\\\\ContractType\\:\\:newMorphToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contract-management/src/Models/ContractType.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ContractManagement\\\\Providers\\\\ContractManagementServiceProvider\\:\\:boot\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contract-management/src/Providers/ContractManagementServiceProvider.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Division\\\\Database\\\\Factories\\\\DivisionFactory\\:\\:default\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Factories\\\\Factory does not specify its types\\: TModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/division/database/factories/DivisionFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var for variable \\$resource contains unresolvable type\\.$#',
	'identifier' => 'varTag.unresolvableType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/division/src/Filament/Resources/DivisionResource/Pages/EditDivision.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var with type mixed is not subtype of native type string\\.$#',
	'identifier' => 'varTag.nativeType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/division/src/Filament/Resources/DivisionResource/Pages/EditDivision.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Division\\\\Models\\\\Division\\:\\:createdBy\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/division/src/Models/Division.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Division\\\\Models\\\\Division\\:\\:lastUpdatedBy\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/division/src/Models/Division.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Division\\\\Models\\\\Division\\:\\:newBelongsToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/division/src/Models/Division.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Division\\\\Models\\\\Division\\:\\:newMorphToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/division/src/Models/Division.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Division\\\\Models\\\\Division\\:\\:notificationSetting\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphOne does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/division/src/Models/Division.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Division\\\\Models\\\\Division\\:\\:teams\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/division/src/Models/Division.php',
];
$ignoreErrors[] = [
	'message' => '#^Class AidingApp\\\\Engagement\\\\Database\\\\Factories\\\\EmailTemplateFactory extends generic class Illuminate\\\\Database\\\\Eloquent\\\\Factories\\\\Factory but does not specify its types\\: TModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/database/factories/EmailTemplateFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Schema\\\\ForeignKeyDefinition\\:\\:unique\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/database/migrations/2023_08_17_183840_create_engagement_deliverables_table.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Schema\\\\ForeignKeyDefinition\\:\\:unique\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/database/migrations/2025_03_06_200002_drop_engagement_deliverables_table.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Illuminate\\\\Database\\\\Migrations\\\\Migration@anonymous/app\\-modules/engagement/database/migrations/2025_03_11_033340_seed_permissions_remove_engagement_deliverable_permissions\\.php\\:40\\:\\:\\$guards type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/database/migrations/2025_03_11_033340_seed_permissions_remove_engagement_deliverable_permissions.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Illuminate\\\\Database\\\\Migrations\\\\Migration@anonymous/app\\-modules/engagement/database/migrations/2025_03_11_033340_seed_permissions_remove_engagement_deliverable_permissions\\.php\\:40\\:\\:\\$permissions type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/database/migrations/2025_03_11_033340_seed_permissions_remove_engagement_deliverable_permissions.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method notify\\(\\) on an unknown class AidingApp\\\\Engagement\\\\Models\\\\Educatable\\.$#',
	'identifier' => 'class.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Actions/CreateEngagement.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$model of method Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphTo\\<Illuminate\\\\Database\\\\Eloquent\\\\Model,Illuminate\\\\Database\\\\Eloquent\\\\Model\\>\\:\\:associate\\(\\) expects Illuminate\\\\Database\\\\Eloquent\\\\Model\\|null, AidingApp\\\\Notification\\\\Models\\\\Contracts\\\\CanBeNotified\\|Illuminate\\\\Database\\\\Eloquent\\\\Collection given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Actions/CreateEngagement.php',
];
$ignoreErrors[] = [
	'message' => '#^Property AidingApp\\\\Engagement\\\\Models\\\\IdeHelperEngagement\\:\\:\\$scheduled_at \\(Illuminate\\\\Support\\\\Carbon\\|null\\) does not accept Carbon\\\\CarbonInterface\\|null\\.$#',
	'identifier' => 'assign.propertyType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Actions/CreateEngagement.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Database\\\\Eloquent\\\\Collection\\<\\(int\\|string\\),Illuminate\\\\Database\\\\Eloquent\\\\Model\\>\\:\\:map\\(\\) expects callable\\(Illuminate\\\\Database\\\\Eloquent\\\\Model, int\\|string\\)\\: AidingApp\\\\Engagement\\\\Jobs\\\\CreateBatchedEngagement, Closure\\(AidingApp\\\\Notification\\\\Models\\\\Contracts\\\\CanBeNotified\\)\\: AidingApp\\\\Engagement\\\\Jobs\\\\CreateBatchedEngagement given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Actions/CreateEngagementBatch.php',
];
$ignoreErrors[] = [
	'message' => '#^Property AidingApp\\\\Engagement\\\\Models\\\\IdeHelperEngagementBatch\\:\\:\\$scheduled_at \\(Illuminate\\\\Support\\\\Carbon\\|null\\) does not accept Carbon\\\\CarbonInterface\\|null\\.$#',
	'identifier' => 'assign.propertyType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Actions/CreateEngagementBatch.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Engagement\\\\Actions\\\\GenerateEngagementBodyContent\\:\\:__invoke\\(\\) has parameter \\$content with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Actions/GenerateEngagementBodyContent.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Engagement\\\\Actions\\\\GenerateEngagementBodyContent\\:\\:__invoke\\(\\) has parameter \\$mergeData with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Actions/GenerateEngagementBodyContent.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Engagement\\\\Actions\\\\GenerateTipTapBodyJson\\:\\:__invoke\\(\\) has parameter \\$mergeTags with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Actions/GenerateTipTapBodyJson.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Engagement\\\\Actions\\\\GenerateTipTapBodyJson\\:\\:__invoke\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Actions/GenerateTipTapBodyJson.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Engagement\\\\Actions\\\\GenerateTipTapBodyJson\\:\\:mergeTag\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Actions/GenerateTipTapBodyJson.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Engagement\\\\Actions\\\\GenerateTipTapBodyJson\\:\\:text\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Actions/GenerateTipTapBodyJson.php',
];
$ignoreErrors[] = [
	'message' => '#^Unable to resolve the template type TKey in call to function collect$#',
	'identifier' => 'argument.templateType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Actions/GenerateTipTapBodyJson.php',
];
$ignoreErrors[] = [
	'message' => '#^Unable to resolve the template type TValue in call to function collect$#',
	'identifier' => 'argument.templateType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Actions/GenerateTipTapBodyJson.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Engagement\\\\DataTransferObjects\\\\EngagementCreationData\\:\\:__construct\\(\\) has parameter \\$body with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/DataTransferObjects/EngagementCreationData.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Engagement\\\\DataTransferObjects\\\\EngagementCreationData\\:\\:__construct\\(\\) has parameter \\$recipient with generic class Illuminate\\\\Database\\\\Eloquent\\\\Collection but does not specify its types\\: TKey, TModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/DataTransferObjects/EngagementCreationData.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Engagement\\\\DataTransferObjects\\\\EngagementCreationData\\:\\:__construct\\(\\) has parameter \\$temporaryBodyImages with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/DataTransferObjects/EngagementCreationData.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Collection\\<int, AidingApp\\\\Team\\\\Models\\\\Team\\>\\:\\:\\$users\\.$#',
	'identifier' => 'property.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Filament/Actions/BulkEngagementAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method Filament\\\\Forms\\\\Components\\\\Field\\:\\:getTemporaryImages\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Filament/Actions/BulkEngagementAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Engagement\\\\Filament\\\\Actions\\\\BulkEngagementAction\\:\\:make\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Filament/Actions/BulkEngagementAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\$recipient of class AidingApp\\\\Engagement\\\\DataTransferObjects\\\\EngagementCreationData constructor expects AidingApp\\\\Notification\\\\Models\\\\Contracts\\\\CanBeNotified\\|Illuminate\\\\Database\\\\Eloquent\\\\Collection, Illuminate\\\\Support\\\\Collection given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Filament/Actions/BulkEngagementAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Collection\\<int, AidingApp\\\\Team\\\\Models\\\\Team\\>\\:\\:\\$users\\.$#',
	'identifier' => 'property.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Filament/Actions/RelationManagerSendEngagementAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method Filament\\\\Forms\\\\Components\\\\Field\\:\\:getTemporaryImages\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Filament/Actions/RelationManagerSendEngagementAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\$recipient of class AidingApp\\\\Engagement\\\\DataTransferObjects\\\\EngagementCreationData constructor expects AidingApp\\\\Notification\\\\Models\\\\Contracts\\\\CanBeNotified\\|Illuminate\\\\Database\\\\Eloquent\\\\Collection, Illuminate\\\\Database\\\\Eloquent\\\\Model given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Filament/Actions/RelationManagerSendEngagementAction.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var for variable \\$resource contains unresolvable type\\.$#',
	'identifier' => 'varTag.unresolvableType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Filament/Resources/EmailTemplateResource/Pages/EditEmailTemplate.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var with type mixed is not subtype of native type string\\.$#',
	'identifier' => 'varTag.nativeType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Filament/Resources/EmailTemplateResource/Pages/EditEmailTemplate.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var for variable \\$resource contains unresolvable type\\.$#',
	'identifier' => 'varTag.unresolvableType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Filament/Resources/EngagementFileResource/Pages/EditEngagementFile.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var with type mixed is not subtype of native type string\\.$#',
	'identifier' => 'varTag.nativeType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Filament/Resources/EngagementFileResource/Pages/EditEngagementFile.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access property \\$id on Illuminate\\\\Database\\\\Eloquent\\\\Model\\|int\\|string\\.$#',
	'identifier' => 'property.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Filament/Resources/EngagementFileResource/Pages/ViewEngagementFile.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property AidingApp\\\\Contact\\\\Models\\\\Contact\\:\\:\\$full\\.$#',
	'identifier' => 'property.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Filament/Resources/EngagementResponseResource/Pages/ViewEngagementResponse.php',
];
$ignoreErrors[] = [
	'message' => '#^Match expression does not handle remaining value\\: class\\-string\\<AidingApp\\\\Contact\\\\Models\\\\Contact\\>&literal\\-string$#',
	'identifier' => 'match.unhandled',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/engagement/src/Filament/Resources/EngagementResponseResource/Pages/ViewEngagementResponse.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Engagement\\\\Http\\\\Controllers\\\\EngagementFileDownloadController\\:\\:__invoke\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Http/Controllers/EngagementFileDownloadController.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Engagement\\\\Http\\\\Requests\\\\EngagementFileDownloadRequest\\:\\:rules\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Http/Requests/EngagementFileDownloadRequest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method notifyNow\\(\\) on an unknown class AidingApp\\\\Engagement\\\\Models\\\\Educatable\\.$#',
	'identifier' => 'class.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Jobs/CreateBatchedEngagement.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$model of method Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphTo\\<Illuminate\\\\Database\\\\Eloquent\\\\Model,Illuminate\\\\Database\\\\Eloquent\\\\Model\\>\\:\\:associate\\(\\) expects Illuminate\\\\Database\\\\Eloquent\\\\Model\\|null, AidingApp\\\\Notification\\\\Models\\\\Contracts\\\\CanBeNotified given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Jobs/CreateBatchedEngagement.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method notify\\(\\) on an unknown class AidingApp\\\\Engagement\\\\Models\\\\Educatable\\.$#',
	'identifier' => 'class.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Jobs/DeliverEngagements.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Engagement\\\\Jobs\\\\DeliverEngagements\\:\\:middleware\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Jobs/DeliverEngagements.php',
];
$ignoreErrors[] = [
	'message' => '#^Property AidingApp\\\\Engagement\\\\Jobs\\\\GatherAndDispatchSesS3InboundEmails\\:\\:\\$uniqueFor has no type specified\\.$#',
	'identifier' => 'missingType.property',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Jobs/GatherAndDispatchSesS3InboundEmails.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method addMediaFromStream\\(\\) on an unknown class AidingApp\\\\Engagement\\\\Jobs\\\\EngagementResponse\\.$#',
	'identifier' => 'class.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Jobs/ProcessSesS3InboundEmail.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var for variable \\$engagementResponse contains unknown class AidingApp\\\\Engagement\\\\Jobs\\\\EngagementResponse\\.$#',
	'identifier' => 'class.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Jobs/ProcessSesS3InboundEmail.php',
];
$ignoreErrors[] = [
	'message' => '#^Property AidingApp\\\\Engagement\\\\Jobs\\\\ProcessSesS3InboundEmail\\:\\:\\$uniqueFor has no type specified\\.$#',
	'identifier' => 'missingType.property',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Jobs/ProcessSesS3InboundEmail.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Engagement\\\\Models\\\\EmailTemplate\\:\\:user\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Models/EmailTemplate.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:orderedEngagements\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Models/Engagement.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method displayEmailKey\\(\\) on an unknown class AidingApp\\\\Engagement\\\\Models\\\\Educatable\\.$#',
	'identifier' => 'class.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Models/Engagement.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method displayNameKey\\(\\) on an unknown class AidingApp\\\\Engagement\\\\Models\\\\Educatable\\.$#',
	'identifier' => 'class.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Models/Engagement.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method getAttribute\\(\\) on an unknown class AidingApp\\\\Engagement\\\\Models\\\\Educatable\\.$#',
	'identifier' => 'class.notFound',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/engagement/src/Models/Engagement.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Engagement\\\\Models\\\\Engagement\\:\\:batch\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Models/Engagement.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Engagement\\\\Models\\\\Engagement\\:\\:createdBy\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Models/Engagement.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Engagement\\\\Models\\\\Engagement\\:\\:emailMessages\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Models/Engagement.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Engagement\\\\Models\\\\Engagement\\:\\:engagementBatch\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Models/Engagement.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Engagement\\\\Models\\\\Engagement\\:\\:getMergeData\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Models/Engagement.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Engagement\\\\Models\\\\Engagement\\:\\:getMergeTags\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Models/Engagement.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Engagement\\\\Models\\\\Engagement\\:\\:getTimelineData\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Models/Engagement.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Engagement\\\\Models\\\\Engagement\\:\\:latestEmailMessage\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphOne does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Models/Engagement.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Engagement\\\\Models\\\\Engagement\\:\\:newBelongsToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Models/Engagement.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Engagement\\\\Models\\\\Engagement\\:\\:newMorphToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Models/Engagement.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Engagement\\\\Models\\\\Engagement\\:\\:recipient\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Models/Engagement.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Engagement\\\\Models\\\\Engagement\\:\\:scopeIsNotPartOfABatch\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Models/Engagement.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Engagement\\\\Models\\\\Engagement\\:\\:scopeLicensedToEducatable\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Models/Engagement.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Engagement\\\\Models\\\\Engagement\\:\\:scopeLicensedToEducatable\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder does not specify its types\\: TModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Models/Engagement.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Engagement\\\\Models\\\\Engagement\\:\\:scopeSentToContact\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Models/Engagement.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Engagement\\\\Models\\\\Engagement\\:\\:timelineRecord\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphOne does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Models/Engagement.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Engagement\\\\Models\\\\Engagement\\:\\:user\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Models/Engagement.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @property\\-read for property AidingApp\\\\Engagement\\\\Models\\\\Engagement\\:\\:\\$recipient contains unknown class AidingApp\\\\Engagement\\\\Models\\\\Educatable\\.$#',
	'identifier' => 'class.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Models/Engagement.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method DOMNode\\:\\:getAttribute\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Models/EngagementBatch.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Engagement\\\\Models\\\\EngagementBatch\\:\\:engagements\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Models/EngagementBatch.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Engagement\\\\Models\\\\EngagementBatch\\:\\:user\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Models/EngagementBatch.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Engagement\\\\Models\\\\EngagementFile\\:\\:contacts\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Models/EngagementFile.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Engagement\\\\Models\\\\EngagementFile\\:\\:newBelongsToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Models/EngagementFile.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Engagement\\\\Models\\\\EngagementFile\\:\\:newMorphToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Models/EngagementFile.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Engagement\\\\Models\\\\EngagementFile\\:\\:prunable\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder does not specify its types\\: TModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Models/EngagementFile.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Engagement\\\\Models\\\\EngagementFileEntities\\:\\:engagementFile\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Models/EngagementFileEntities.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Engagement\\\\Models\\\\EngagementFileEntities\\:\\:entity\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Models/EngagementFileEntities.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:orderedEngagementResponses\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Models/EngagementResponse.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Engagement\\\\Models\\\\EngagementResponse\\:\\:getTimelineData\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Models/EngagementResponse.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Engagement\\\\Models\\\\EngagementResponse\\:\\:newBelongsToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Models/EngagementResponse.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Engagement\\\\Models\\\\EngagementResponse\\:\\:newMorphToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Models/EngagementResponse.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Engagement\\\\Models\\\\EngagementResponse\\:\\:sender\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Models/EngagementResponse.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Engagement\\\\Models\\\\EngagementResponse\\:\\:timelineRecord\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphOne does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Models/EngagementResponse.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$division\\.$#',
	'identifier' => 'property.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Notifications/EngagementBatchFinishedNotification.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Engagement\\\\Notifications\\\\EngagementBatchFinishedNotification\\:\\:toDatabase\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Notifications/EngagementBatchFinishedNotification.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$division\\.$#',
	'identifier' => 'property.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Notifications/EngagementBatchStartedNotification.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Engagement\\\\Notifications\\\\EngagementBatchStartedNotification\\:\\:toDatabase\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Notifications/EngagementBatchStartedNotification.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$division\\.$#',
	'identifier' => 'property.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Notifications/EngagementFailedNotification.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to property \\$display_name on an unknown class AidingApp\\\\Engagement\\\\Models\\\\Educatable\\.$#',
	'identifier' => 'class.notFound',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/engagement/src/Notifications/EngagementFailedNotification.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Engagement\\\\Notifications\\\\EngagementFailedNotification\\:\\:toDatabase\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Notifications/EngagementFailedNotification.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$division\\.$#',
	'identifier' => 'property.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Notifications/EngagementNotification.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to property \\$display_name on an unknown class AidingApp\\\\Engagement\\\\Models\\\\Educatable\\.$#',
	'identifier' => 'class.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Notifications/EngagementNotification.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method AidingApp\\\\Engagement\\\\Models\\\\Engagement\\:\\:getBodyMarkdown\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Notifications/EngagementNotification.php',
];
$ignoreErrors[] = [
	'message' => '#^Match expression does not handle remaining value\\: AidingApp\\\\Notification\\\\Enums\\\\NotificationChannel\\:\\:Database$#',
	'identifier' => 'match.unhandled',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Notifications/EngagementNotification.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to function is_null\\(\\) with string will always evaluate to false\\.$#',
	'identifier' => 'function.impossibleType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Observers/EngagementBatchObserver.php',
];
$ignoreErrors[] = [
	'message' => '#^Result of && is always false\\.$#',
	'identifier' => 'booleanAnd.alwaysFalse',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Observers/EngagementBatchObserver.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method getLicenseType\\(\\) on an unknown class AidingApp\\\\Engagement\\\\Models\\\\Educatable\\.$#',
	'identifier' => 'class.notFound',
	'count' => 5,
	'path' => __DIR__ . '/app-modules/engagement/src/Policies/EngagementPolicy.php',
];
$ignoreErrors[] = [
	'message' => '#^Using nullsafe method call on non\\-nullable type AidingApp\\\\Engagement\\\\Models\\\\Educatable\\. Use \\-\\> instead\\.$#',
	'identifier' => 'nullsafe.neverNull',
	'count' => 5,
	'path' => __DIR__ . '/app-modules/engagement/src/Policies/EngagementPolicy.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property Pest\\\\Expectation\\<AidingApp\\\\Engagement\\\\Models\\\\EngagementBatch\\|null\\>\\:\\:\\$user\\.$#',
	'identifier' => 'property.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/tests/Actions/CreateEngagementBatchTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property Pest\\\\Expectation\\<AidingApp\\\\Engagement\\\\Models\\\\Engagement\\|null\\>\\:\\:\\$user\\.$#',
	'identifier' => 'property.notFound',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/engagement/tests/Actions/CreateEngagementTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property Pest\\\\Expectation\\<AidingApp\\\\Engagement\\\\Models\\\\Engagement\\|null\\>\\:\\:\\$engagementBatch\\.$#',
	'identifier' => 'property.notFound',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/engagement/tests/Jobs/CreateBatchedEngagementTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Form\\\\Actions\\\\GenerateFormKitSchema\\:\\:__invoke\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Actions/GenerateFormKitSchema.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Form\\\\Actions\\\\GenerateFormKitSchema\\:\\:content\\(\\) has parameter \\$blocks with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Actions/GenerateFormKitSchema.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Form\\\\Actions\\\\GenerateFormKitSchema\\:\\:content\\(\\) has parameter \\$content with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Actions/GenerateFormKitSchema.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Form\\\\Actions\\\\GenerateFormKitSchema\\:\\:content\\(\\) has parameter \\$fields with generic class Illuminate\\\\Database\\\\Eloquent\\\\Collection but does not specify its types\\: TKey, TModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Actions/GenerateFormKitSchema.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Form\\\\Actions\\\\GenerateFormKitSchema\\:\\:content\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Actions/GenerateFormKitSchema.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Form\\\\Actions\\\\GenerateFormKitSchema\\:\\:generateContent\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Actions/GenerateFormKitSchema.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Form\\\\Actions\\\\GenerateFormKitSchema\\:\\:grid\\(\\) has parameter \\$blocks with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Actions/GenerateFormKitSchema.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Form\\\\Actions\\\\GenerateFormKitSchema\\:\\:grid\\(\\) has parameter \\$component with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Actions/GenerateFormKitSchema.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Form\\\\Actions\\\\GenerateFormKitSchema\\:\\:grid\\(\\) has parameter \\$fields with generic class Illuminate\\\\Database\\\\Eloquent\\\\Collection but does not specify its types\\: TKey, TModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Actions/GenerateFormKitSchema.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Form\\\\Actions\\\\GenerateFormKitSchema\\:\\:grid\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Actions/GenerateFormKitSchema.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Form\\\\Actions\\\\GenerateFormKitSchema\\:\\:text\\(\\) has parameter \\$component with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Actions/GenerateFormKitSchema.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Form\\\\Actions\\\\GenerateFormKitSchema\\:\\:text\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Actions/GenerateFormKitSchema.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Form\\\\Actions\\\\GenerateFormKitSchema\\:\\:wizardContent\\(\\) has parameter \\$blocks with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Actions/GenerateFormKitSchema.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Form\\\\Actions\\\\GenerateFormKitSchema\\:\\:wizardContent\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Actions/GenerateFormKitSchema.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to function is_null\\(\\) with string will always evaluate to false\\.$#',
	'identifier' => 'function.impossibleType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Actions/GenerateSubmissibleValidation.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Form\\\\Actions\\\\GenerateSubmissibleValidation\\:\\:__invoke\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Actions/GenerateSubmissibleValidation.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Form\\\\Actions\\\\GenerateSubmissibleValidation\\:\\:fields\\(\\) has parameter \\$blocks with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Actions/GenerateSubmissibleValidation.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Form\\\\Actions\\\\GenerateSubmissibleValidation\\:\\:fields\\(\\) has parameter \\$fields with generic class Illuminate\\\\Database\\\\Eloquent\\\\Collection but does not specify its types\\: TKey, TModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Actions/GenerateSubmissibleValidation.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Form\\\\Actions\\\\GenerateSubmissibleValidation\\:\\:fields\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Actions/GenerateSubmissibleValidation.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Form\\\\Actions\\\\GenerateSubmissibleValidation\\:\\:wizardRules\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Actions/GenerateSubmissibleValidation.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Database\\\\Eloquent\\\\Collection\\<\\(int\\|string\\),Illuminate\\\\Database\\\\Eloquent\\\\Model\\>\\:\\:mapWithKeys\\(\\) expects callable\\(Illuminate\\\\Database\\\\Eloquent\\\\Model, int\\|string\\)\\: array\\<array\\>, Closure\\(AidingApp\\\\Form\\\\Models\\\\SubmissibleField\\)\\: array\\<array\\> given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Actions/GenerateSubmissibleValidation.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property AidingApp\\\\Form\\\\Models\\\\SubmissibleField\\:\\:\\$pivot\\.$#',
	'identifier' => 'property.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Actions/InjectSubmissionStateIntoTipTapContent.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Form\\\\Actions\\\\InjectSubmissionStateIntoTipTapContent\\:\\:__invoke\\(\\) has parameter \\$blocks with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Actions/InjectSubmissionStateIntoTipTapContent.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Form\\\\Actions\\\\InjectSubmissionStateIntoTipTapContent\\:\\:__invoke\\(\\) has parameter \\$content with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Actions/InjectSubmissionStateIntoTipTapContent.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Form\\\\Actions\\\\InjectSubmissionStateIntoTipTapContent\\:\\:__invoke\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Actions/InjectSubmissionStateIntoTipTapContent.php',
];
$ignoreErrors[] = [
	'message' => '#^Match expression does not handle remaining value\\: class\\-string\\<AidingApp\\\\Form\\\\Models\\\\Submissible\\>&literal\\-string$#',
	'identifier' => 'match.unhandled',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Actions/ResolveBlockRegistry.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Form\\\\Actions\\\\ResolveBlockRegistry\\:\\:__invoke\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Actions/ResolveBlockRegistry.php',
];
$ignoreErrors[] = [
	'message' => '#^If condition is always true\\.$#',
	'identifier' => 'if.alwaysTrue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Actions/ResolveSubmissionAuthorFromEmail.php',
];
$ignoreErrors[] = [
	'message' => '#^Unreachable statement \\- code above always terminates\\.$#',
	'identifier' => 'deadCode.unreachable',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Actions/ResolveSubmissionAuthorFromEmail.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Form\\\\Enums\\\\FormSubmissionRequestDeliveryMethod\\:\\:getLabel\\(\\) never returns null so it can be removed from the return type\\.$#',
	'identifier' => 'return.unusedType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Enums/FormSubmissionRequestDeliveryMethod.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Form\\\\Enums\\\\FormSubmissionStatus\\:\\:getLabel\\(\\) never returns null so it can be removed from the return type\\.$#',
	'identifier' => 'return.unusedType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Enums/FormSubmissionStatus.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Form\\\\Enums\\\\Rounding\\:\\:getLabel\\(\\) never returns null so it can be removed from the return type\\.$#',
	'identifier' => 'return.unusedType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Enums/Rounding.php',
];
$ignoreErrors[] = [
	'message' => '#^Class AidingApp\\\\Form\\\\Exports\\\\FormSubmissionExport implements generic interface Maatwebsite\\\\Excel\\\\Concerns\\\\WithMapping but does not specify its types\\: RowType$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Exports/FormSubmissionExport.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Form\\\\Exports\\\\FormSubmissionExport\\:\\:__construct\\(\\) has parameter \\$submissions with generic class Illuminate\\\\Support\\\\Collection but does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Exports/FormSubmissionExport.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Form\\\\Exports\\\\FormSubmissionExport\\:\\:collection\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Exports/FormSubmissionExport.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Form\\\\Exports\\\\FormSubmissionExport\\:\\:headings\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Exports/FormSubmissionExport.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Form\\\\Exports\\\\FormSubmissionExport\\:\\:map\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Exports/FormSubmissionExport.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Form\\\\Filament\\\\Blocks\\\\CheckboxFormFieldBlock\\:\\:fields\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/CheckboxFormFieldBlock.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Form\\\\Filament\\\\Blocks\\\\CheckboxFormFieldBlock\\:\\:getFormKitSchema\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/CheckboxFormFieldBlock.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Form\\\\Filament\\\\Blocks\\\\CheckboxFormFieldBlock\\:\\:getValidationRules\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/CheckboxFormFieldBlock.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Form\\\\Filament\\\\Blocks\\\\DateFormFieldBlock\\:\\:fields\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/DateFormFieldBlock.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Form\\\\Filament\\\\Blocks\\\\DateFormFieldBlock\\:\\:getFormKitSchema\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/DateFormFieldBlock.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Form\\\\Filament\\\\Blocks\\\\DateFormFieldBlock\\:\\:getValidationRules\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/DateFormFieldBlock.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Form\\\\Filament\\\\Blocks\\\\EducatableEmailFormFieldBlock\\:\\:getFormKitSchema\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/EducatableEmailFormFieldBlock.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Form\\\\Filament\\\\Blocks\\\\EducatableEmailFormFieldBlock\\:\\:getFormSchema\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/EducatableEmailFormFieldBlock.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Form\\\\Filament\\\\Blocks\\\\EducatableEmailFormFieldBlock\\:\\:getSubmissionState\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/EducatableEmailFormFieldBlock.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Form\\\\Filament\\\\Blocks\\\\EducatableEmailFormFieldBlock\\:\\:getValidationRules\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/EducatableEmailFormFieldBlock.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Form\\\\Filament\\\\Blocks\\\\EmailFormFieldBlock\\:\\:fields\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/EmailFormFieldBlock.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Form\\\\Filament\\\\Blocks\\\\EmailFormFieldBlock\\:\\:getFormKitSchema\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/EmailFormFieldBlock.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Form\\\\Filament\\\\Blocks\\\\EmailFormFieldBlock\\:\\:getValidationRules\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/EmailFormFieldBlock.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Form\\\\Filament\\\\Blocks\\\\FormFieldBlock\\:\\:fields\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/FormFieldBlock.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Form\\\\Filament\\\\Blocks\\\\FormFieldBlock\\:\\:getFormKitSchema\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/FormFieldBlock.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Form\\\\Filament\\\\Blocks\\\\FormFieldBlock\\:\\:getFormSchema\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/FormFieldBlock.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Form\\\\Filament\\\\Blocks\\\\FormFieldBlock\\:\\:getSubmissionState\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/FormFieldBlock.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Form\\\\Filament\\\\Blocks\\\\FormFieldBlock\\:\\:getValidationRules\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/FormFieldBlock.php',
];
$ignoreErrors[] = [
	'message' => '#^Variable \\$block in PHPDoc tag @var does not exist\\.$#',
	'identifier' => 'varTag.variableNotFound',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/FormFieldBlockRegistry.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Form\\\\Filament\\\\Blocks\\\\NumberFormFieldBlock\\:\\:fields\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/NumberFormFieldBlock.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Form\\\\Filament\\\\Blocks\\\\NumberFormFieldBlock\\:\\:getFormKitSchema\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/NumberFormFieldBlock.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Form\\\\Filament\\\\Blocks\\\\NumberFormFieldBlock\\:\\:getValidationRules\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/NumberFormFieldBlock.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Form\\\\Filament\\\\Blocks\\\\PhoneFormFieldBlock\\:\\:fields\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/PhoneFormFieldBlock.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Form\\\\Filament\\\\Blocks\\\\PhoneFormFieldBlock\\:\\:getFormKitSchema\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/PhoneFormFieldBlock.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Form\\\\Filament\\\\Blocks\\\\PhoneFormFieldBlock\\:\\:getValidationRules\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/PhoneFormFieldBlock.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Form\\\\Filament\\\\Blocks\\\\RadioFormFieldBlock\\:\\:fields\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/RadioFormFieldBlock.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Form\\\\Filament\\\\Blocks\\\\RadioFormFieldBlock\\:\\:getFormKitSchema\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/RadioFormFieldBlock.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Form\\\\Filament\\\\Blocks\\\\RadioFormFieldBlock\\:\\:getValidationRules\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/RadioFormFieldBlock.php',
];
$ignoreErrors[] = [
	'message' => '#^Unable to resolve the template type TKey in call to function collect$#',
	'identifier' => 'argument.templateType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/RadioFormFieldBlock.php',
];
$ignoreErrors[] = [
	'message' => '#^Unable to resolve the template type TValue in call to function collect$#',
	'identifier' => 'argument.templateType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/RadioFormFieldBlock.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Form\\\\Filament\\\\Blocks\\\\SelectFormFieldBlock\\:\\:fields\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/SelectFormFieldBlock.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Form\\\\Filament\\\\Blocks\\\\SelectFormFieldBlock\\:\\:getFormKitSchema\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/SelectFormFieldBlock.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Form\\\\Filament\\\\Blocks\\\\SelectFormFieldBlock\\:\\:getValidationRules\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/SelectFormFieldBlock.php',
];
$ignoreErrors[] = [
	'message' => '#^Unable to resolve the template type TKey in call to function collect$#',
	'identifier' => 'argument.templateType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/SelectFormFieldBlock.php',
];
$ignoreErrors[] = [
	'message' => '#^Unable to resolve the template type TValue in call to function collect$#',
	'identifier' => 'argument.templateType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/SelectFormFieldBlock.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Form\\\\Filament\\\\Blocks\\\\SignatureFormFieldBlock\\:\\:fields\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/SignatureFormFieldBlock.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Form\\\\Filament\\\\Blocks\\\\SignatureFormFieldBlock\\:\\:getFormKitSchema\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/SignatureFormFieldBlock.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Form\\\\Filament\\\\Blocks\\\\TextAreaFormFieldBlock\\:\\:fields\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/TextAreaFormFieldBlock.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Form\\\\Filament\\\\Blocks\\\\TextAreaFormFieldBlock\\:\\:getFormKitSchema\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/TextAreaFormFieldBlock.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Form\\\\Filament\\\\Blocks\\\\TextAreaFormFieldBlock\\:\\:getValidationRules\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/TextAreaFormFieldBlock.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Form\\\\Filament\\\\Blocks\\\\TextInputFormFieldBlock\\:\\:fields\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/TextInputFormFieldBlock.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Form\\\\Filament\\\\Blocks\\\\TextInputFormFieldBlock\\:\\:getFormKitSchema\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/TextInputFormFieldBlock.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Form\\\\Filament\\\\Blocks\\\\TextInputFormFieldBlock\\:\\:getValidationRules\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/TextInputFormFieldBlock.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Form\\\\Filament\\\\Blocks\\\\TimeFormFieldBlock\\:\\:fields\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/TimeFormFieldBlock.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Form\\\\Filament\\\\Blocks\\\\TimeFormFieldBlock\\:\\:getFormKitSchema\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/TimeFormFieldBlock.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Form\\\\Filament\\\\Blocks\\\\UploadFormFieldBlock\\:\\:fields\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/UploadFormFieldBlock.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Form\\\\Filament\\\\Blocks\\\\UploadFormFieldBlock\\:\\:getFormKitSchema\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/UploadFormFieldBlock.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Form\\\\Filament\\\\Blocks\\\\UploadFormFieldBlock\\:\\:getValidationRules\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/UploadFormFieldBlock.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Form\\\\Filament\\\\Blocks\\\\UrlFormFieldBlock\\:\\:fields\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/UrlFormFieldBlock.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Form\\\\Filament\\\\Blocks\\\\UrlFormFieldBlock\\:\\:getFormKitSchema\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/UrlFormFieldBlock.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Form\\\\Filament\\\\Blocks\\\\UrlFormFieldBlock\\:\\:getValidationRules\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/UrlFormFieldBlock.php',
];
$ignoreErrors[] = [
	'message' => '#^Class AidingApp\\\\Form\\\\Models\\\\Submissible has PHPDoc tag @property for property \\$allowed_domains with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Models/Submissible.php',
];
$ignoreErrors[] = [
	'message' => '#^Class AidingApp\\\\Form\\\\Models\\\\Submissible has PHPDoc tag @property for property \\$content with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Models/Submissible.php',
];
$ignoreErrors[] = [
	'message' => '#^Class AidingApp\\\\Form\\\\Models\\\\Submissible uses generic trait Illuminate\\\\Database\\\\Eloquent\\\\Factories\\\\HasFactory but does not specify its types\\: TFactory$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Models/Submissible.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Form\\\\Models\\\\Submissible\\:\\:allowedDomains\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Casts\\\\Attribute does not specify its types\\: TGet, TSet$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Models/Submissible.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Form\\\\Models\\\\Submissible\\:\\:content\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Casts\\\\Attribute does not specify its types\\: TGet, TSet$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Models/Submissible.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Form\\\\Models\\\\Submissible\\:\\:embedEnabled\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Casts\\\\Attribute does not specify its types\\: TGet, TSet$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Models/Submissible.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Form\\\\Models\\\\Submissible\\:\\:fields\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Models/Submissible.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Form\\\\Models\\\\Submissible\\:\\:isWizard\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Casts\\\\Attribute does not specify its types\\: TGet, TSet$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Models/Submissible.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Form\\\\Models\\\\Submissible\\:\\:name\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Casts\\\\Attribute does not specify its types\\: TGet, TSet$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Models/Submissible.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Form\\\\Models\\\\Submissible\\:\\:steps\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Models/Submissible.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Form\\\\Models\\\\Submissible\\:\\:submissions\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Models/Submissible.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Form\\\\Models\\\\SubmissibleAuthentication\\:\\:author\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Models/SubmissibleAuthentication.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Form\\\\Models\\\\SubmissibleAuthentication\\:\\:prunable\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder does not specify its types\\: TModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Models/SubmissibleAuthentication.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Form\\\\Models\\\\SubmissibleAuthentication\\:\\:submissible\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Models/SubmissibleAuthentication.php',
];
$ignoreErrors[] = [
	'message' => '#^Class AidingApp\\\\Form\\\\Models\\\\SubmissibleField has PHPDoc tag @property for property \\$config with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Models/SubmissibleField.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Form\\\\Models\\\\SubmissibleField\\:\\:config\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Casts\\\\Attribute does not specify its types\\: TGet, TSet$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Models/SubmissibleField.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Form\\\\Models\\\\SubmissibleField\\:\\:isRequired\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Casts\\\\Attribute does not specify its types\\: TGet, TSet$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Models/SubmissibleField.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Form\\\\Models\\\\SubmissibleField\\:\\:label\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Casts\\\\Attribute does not specify its types\\: TGet, TSet$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Models/SubmissibleField.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Form\\\\Models\\\\SubmissibleField\\:\\:step\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Models/SubmissibleField.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Form\\\\Models\\\\SubmissibleField\\:\\:submissible\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Models/SubmissibleField.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Form\\\\Models\\\\SubmissibleField\\:\\:type\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Casts\\\\Attribute does not specify its types\\: TGet, TSet$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Models/SubmissibleField.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property AidingApp\\\\Form\\\\Models\\\\SubmissibleStep\\:\\:\\$sort\\.$#',
	'identifier' => 'property.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Models/SubmissibleStep.php',
];
$ignoreErrors[] = [
	'message' => '#^Class AidingApp\\\\Form\\\\Models\\\\SubmissibleStep has PHPDoc tag @property for property \\$content with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Models/SubmissibleStep.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Form\\\\Models\\\\SubmissibleStep\\:\\:content\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Casts\\\\Attribute does not specify its types\\: TGet, TSet$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Models/SubmissibleStep.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Form\\\\Models\\\\SubmissibleStep\\:\\:fields\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Models/SubmissibleStep.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Form\\\\Models\\\\SubmissibleStep\\:\\:label\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Casts\\\\Attribute does not specify its types\\: TGet, TSet$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Models/SubmissibleStep.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Form\\\\Models\\\\SubmissibleStep\\:\\:submissible\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Models/SubmissibleStep.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Form\\\\Models\\\\Submission\\:\\:author\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Models/Submission.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Form\\\\Models\\\\Submission\\:\\:fields\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Models/Submission.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Form\\\\Models\\\\Submission\\:\\:scopeLicensedToEducatable\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Models/Submission.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Form\\\\Models\\\\Submission\\:\\:scopeLicensedToEducatable\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder does not specify its types\\: TModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Models/Submission.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Form\\\\Models\\\\Submission\\:\\:submissible\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Models/Submission.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Form\\\\Notifications\\\\AuthenticateFormNotification\\:\\:identifyRecipient\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Notifications/AuthenticateFormNotification.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property AidingApp\\\\Form\\\\Models\\\\Submission\\:\\:\\$request_note\\.$#',
	'identifier' => 'property.notFound',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/form/src/Notifications/FormSubmissionRequestNotification.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property AidingApp\\\\Form\\\\Models\\\\Submission\\:\\:\\$requester\\.$#',
	'identifier' => 'property.notFound',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/form/src/Notifications/FormSubmissionRequestNotification.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\InAppCommunication\\\\Actions\\\\CheckConversationMessageContentForMention\\:\\:__invoke\\(\\) has parameter \\$content with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/in-app-communication/src/Actions/CheckConversationMessageContentForMention.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\InAppCommunication\\\\Actions\\\\ConvertMessageJsonToText\\:\\:__invoke\\(\\) has parameter \\$content with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/in-app-communication/src/Actions/ConvertMessageJsonToText.php',
];
$ignoreErrors[] = [
	'message' => '#^Variable \\$user in PHPDoc tag @var does not match assigned variable \\$creator\\.$#',
	'identifier' => 'varTag.differentVariable',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/in-app-communication/src/Actions/CreateTwilioConversation.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\InAppCommunication\\\\Enums\\\\ConversationNotificationPreference\\:\\:getColor\\(\\) never returns array\\{50\\: string, 100\\: string, 200\\: string, 300\\: string, 400\\: string, 500\\: string, 600\\: string, 700\\: string, \\.\\.\\.\\} so it can be removed from the return type\\.$#',
	'identifier' => 'return.unusedType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/in-app-communication/src/Enums/ConversationNotificationPreference.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\InAppCommunication\\\\Enums\\\\ConversationNotificationPreference\\:\\:getColor\\(\\) never returns null so it can be removed from the return type\\.$#',
	'identifier' => 'return.unusedType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/in-app-communication/src/Enums/ConversationNotificationPreference.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\InAppCommunication\\\\Enums\\\\ConversationNotificationPreference\\:\\:getDescription\\(\\) never returns null so it can be removed from the return type\\.$#',
	'identifier' => 'return.unusedType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/in-app-communication/src/Enums/ConversationNotificationPreference.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\InAppCommunication\\\\Enums\\\\ConversationNotificationPreference\\:\\:getIcon\\(\\) never returns null so it can be removed from the return type\\.$#',
	'identifier' => 'return.unusedType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/in-app-communication/src/Enums/ConversationNotificationPreference.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\InAppCommunication\\\\Enums\\\\ConversationNotificationPreference\\:\\:getLabel\\(\\) never returns null so it can be removed from the return type\\.$#',
	'identifier' => 'return.unusedType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/in-app-communication/src/Enums/ConversationNotificationPreference.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\InAppCommunication\\\\Events\\\\ConversationMessageSent\\:\\:__construct\\(\\) has parameter \\$messageContent with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/in-app-communication/src/Events/ConversationMessageSent.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Collection\\<int, Illuminate\\\\Database\\\\Eloquent\\\\Model\\>\\|Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$participant\\.$#',
	'identifier' => 'property.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/in-app-communication/src/Filament/Pages/UserChat.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$participant\\.$#',
	'identifier' => 'property.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/in-app-communication/src/Filament/Pages/UserChat.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\InAppCommunication\\\\Filament\\\\Pages\\\\UserChat\\:\\:conversations\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Collection does not specify its types\\: TKey, TModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/in-app-communication/src/Filament/Pages/UserChat.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\InAppCommunication\\\\Filament\\\\Pages\\\\UserChat\\:\\:joinChannelsAction\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/in-app-communication/src/Filament/Pages/UserChat.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\InAppCommunication\\\\Filament\\\\Pages\\\\UserChat\\:\\:onMessageSent\\(\\) has parameter \\$messageContent with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/in-app-communication/src/Filament/Pages/UserChat.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @property for property AidingApp\\\\InAppCommunication\\\\Filament\\\\Pages\\\\UserChat\\:\\:\\$conversations contains generic class Illuminate\\\\Database\\\\Eloquent\\\\Collection but does not specify its types\\: TKey, TModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/in-app-communication/src/Filament/Pages/UserChat.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$relations of method Illuminate\\\\Database\\\\Eloquent\\\\Builder\\<Illuminate\\\\Database\\\\Eloquent\\\\Model\\>\\:\\:with\\(\\) expects array\\<array\\|\\(Closure\\(Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\Relation\\<\\*, \\*, \\*\\>\\)\\: mixed\\)\\|string\\>\\|string, array\\{participants\\: Closure\\(Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\)\\: Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\} given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/in-app-communication/src/Filament/Pages/UserChat.php',
];
$ignoreErrors[] = [
	'message' => '#^Unable to resolve the template type TKey in call to function collect$#',
	'identifier' => 'argument.templateType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/in-app-communication/src/Filament/Pages/UserChat.php',
];
$ignoreErrors[] = [
	'message' => '#^Unable to resolve the template type TValue in call to function collect$#',
	'identifier' => 'argument.templateType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/in-app-communication/src/Filament/Pages/UserChat.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$components of method Filament\\\\Panel\\:\\:livewireComponents\\(\\) expects array\\<string, class\\-string\\<Livewire\\\\Component\\>\\>, array\\{\'AidingApp\\\\\\\\InAppCommunication\\\\\\\\Livewire\\\\\\\\ChatNotifications\'\\} given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/in-app-communication/src/InAppCommunicationPlugin.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Collection\\<int, Illuminate\\\\Database\\\\Eloquent\\\\Model\\>\\|Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$participant\\.$#',
	'identifier' => 'property.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/in-app-communication/src/Jobs/NotifyConversationParticipant.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\LazyCollection\\<int,Illuminate\\\\Database\\\\Eloquent\\\\Model\\>\\:\\:each\\(\\) expects callable\\(Illuminate\\\\Database\\\\Eloquent\\\\Model, int\\)\\: mixed, Closure\\(App\\\\Models\\\\User\\)\\: void given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/in-app-communication/src/Jobs/NotifyConversationParticipants.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\InAppCommunication\\\\Livewire\\\\ChatNotifications\\:\\:getNotifications\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Collection does not specify its types\\: TKey, TModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/in-app-communication/src/Livewire/ChatNotifications.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\InAppCommunication\\\\Models\\\\TwilioConversation\\:\\:managers\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/in-app-communication/src/Models/TwilioConversation.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\InAppCommunication\\\\Models\\\\TwilioConversation\\:\\:participants\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/in-app-communication/src/Models/TwilioConversation.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$participant\\.$#',
	'identifier' => 'property.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/in-app-communication/src/Models/TwilioConversationUser.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\InAppCommunication\\\\Models\\\\TwilioConversationUser\\:\\:conversation\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/in-app-communication/src/Models/TwilioConversationUser.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\InAppCommunication\\\\Models\\\\TwilioConversationUser\\:\\:user\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/in-app-communication/src/Models/TwilioConversationUser.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\InAppCommunication\\\\Providers\\\\InAppCommunicationServiceProvider\\:\\:boot\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/in-app-communication/src/Providers/InAppCommunicationServiceProvider.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\IntegrationAwsSesEventHandling\\\\DataTransferObjects\\\\SesBounceData\\:\\:__construct\\(\\) has parameter \\$bouncedRecipients with generic class Spatie\\\\LaravelData\\\\DataCollection but does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/integration-aws-ses-event-handling/src/DataTransferObjects/SesBounceData.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\IntegrationAwsSesEventHandling\\\\DataTransferObjects\\\\SesClickData\\:\\:__construct\\(\\) has parameter \\$linkTags with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/integration-aws-ses-event-handling/src/DataTransferObjects/SesClickData.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\IntegrationAwsSesEventHandling\\\\DataTransferObjects\\\\SesComplaintData\\:\\:__construct\\(\\) has parameter \\$complainedRecipients with generic class Spatie\\\\LaravelData\\\\DataCollection but does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/integration-aws-ses-event-handling/src/DataTransferObjects/SesComplaintData.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\IntegrationAwsSesEventHandling\\\\DataTransferObjects\\\\SesDeliveryData\\:\\:__construct\\(\\) has parameter \\$recipients with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/integration-aws-ses-event-handling/src/DataTransferObjects/SesDeliveryData.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\IntegrationAwsSesEventHandling\\\\DataTransferObjects\\\\SesDeliveryDelayData\\:\\:__construct\\(\\) has parameter \\$delayedRecipients with generic class Spatie\\\\LaravelData\\\\DataCollection but does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/integration-aws-ses-event-handling/src/DataTransferObjects/SesDeliveryDelayData.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\IntegrationAwsSesEventHandling\\\\DataTransferObjects\\\\SesEventData\\:\\:fromRequest\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/integration-aws-ses-event-handling/src/DataTransferObjects/SesEventData.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\IntegrationAwsSesEventHandling\\\\DataTransferObjects\\\\SesMailData\\:\\:__construct\\(\\) has parameter \\$commonHeaders with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/integration-aws-ses-event-handling/src/DataTransferObjects/SesMailData.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\IntegrationAwsSesEventHandling\\\\DataTransferObjects\\\\SesMailData\\:\\:__construct\\(\\) has parameter \\$destination with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/integration-aws-ses-event-handling/src/DataTransferObjects/SesMailData.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\IntegrationAwsSesEventHandling\\\\DataTransferObjects\\\\SesMailData\\:\\:__construct\\(\\) has parameter \\$headers with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/integration-aws-ses-event-handling/src/DataTransferObjects/SesMailData.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\IntegrationAwsSesEventHandling\\\\DataTransferObjects\\\\SesMailData\\:\\:__construct\\(\\) has parameter \\$tags with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/integration-aws-ses-event-handling/src/DataTransferObjects/SesMailData.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\IntegrationAwsSesEventHandling\\\\DataTransferObjects\\\\SesTopicPreferencesData\\:\\:__construct\\(\\) has parameter \\$topicDefaultSubscriptionStatus with generic class Spatie\\\\LaravelData\\\\DataCollection but does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/integration-aws-ses-event-handling/src/DataTransferObjects/SesTopicPreferencesData.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\IntegrationAwsSesEventHandling\\\\DataTransferObjects\\\\SesTopicPreferencesData\\:\\:__construct\\(\\) has parameter \\$topicSubscriptionStatus with generic class Spatie\\\\LaravelData\\\\DataCollection but does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/integration-aws-ses-event-handling/src/DataTransferObjects/SesTopicPreferencesData.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to property \\$mail on an unknown class AidingApp\\\\IntegrationAwsSesEventHandling\\\\Filament\\\\Pages\\\\TenantConfig\\.$#',
	'identifier' => 'class.notFound',
	'count' => 21,
	'path' => __DIR__ . '/app-modules/integration-aws-ses-event-handling/src/Filament/Pages/ManageAmazonSesSettings.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var for variable \\$config contains unknown class AidingApp\\\\IntegrationAwsSesEventHandling\\\\Filament\\\\Pages\\\\TenantConfig\\.$#',
	'identifier' => 'class.notFound',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/integration-aws-ses-event-handling/src/Filament/Pages/ManageAmazonSesSettings.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\IntegrationAwsSesEventHandling\\\\Http\\\\Controllers\\\\AwsSesInboundWebhookController\\:\\:__invoke\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/integration-aws-ses-event-handling/src/Http/Controllers/AwsSesInboundWebhookController.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\IntegrationAwsSesEventHandling\\\\Providers\\\\IntegrationAwsSesEventHandlingServiceProvider\\:\\:boot\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/integration-aws-ses-event-handling/src/Providers/IntegrationAwsSesEventHandlingServiceProvider.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\IntegrationAwsSesEventHandling\\\\Settings\\\\SesSettings\\:\\:encrypted\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/integration-aws-ses-event-handling/src/Settings/SesSettings.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to property \\$mail on an unknown class TenantConfig\\.$#',
	'identifier' => 'class.notFound',
	'count' => 8,
	'path' => __DIR__ . '/app-modules/integration-aws-ses-event-handling/tests/Feature/Filament/ManageAmazonSesSettingsTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method call\\(\\) on an unknown class static\\.$#',
	'identifier' => 'class.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/integration-aws-ses-event-handling/tests/Feature/Filament/ManageAmazonSesSettingsTest.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var for variable \\$config contains unknown class TenantConfig\\.$#',
	'identifier' => 'class.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/integration-aws-ses-event-handling/tests/Feature/Filament/ManageAmazonSesSettingsTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method Pest\\\\PendingCalls\\\\TestCall\\:\\:expectExceptionMessage\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/integration-aws-ses-event-handling/tests/Unit/SesConfigurationSetTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\IntegrationGoogleAnalytics\\\\Providers\\\\IntegrationGoogleAnalyticsServiceProvider\\:\\:boot\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/integration-google-analytics/src/Providers/IntegrationGoogleAnalyticsServiceProvider.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\IntegrationGoogleRecaptcha\\\\Providers\\\\IntegrationGoogleRecaptchaServiceProvider\\:\\:boot\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/integration-google-recaptcha/src/Providers/IntegrationGoogleRecaptchaServiceProvider.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\IntegrationGoogleRecaptcha\\\\Settings\\\\GoogleRecaptchaSettings\\:\\:encrypted\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/integration-google-recaptcha/src/Settings/GoogleRecaptchaSettings.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\IntegrationMicrosoftClarity\\\\Providers\\\\IntegrationMicrosoftClarityServiceProvider\\:\\:boot\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/integration-microsoft-clarity/src/Providers/IntegrationMicrosoftClarityServiceProvider.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\IntegrationTwilio\\\\Settings\\\\TwilioSettings\\:\\:encrypted\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/integration-twilio/src/Settings/TwilioSettings.php',
];
$ignoreErrors[] = [
	'message' => '#^Variable \\$senderModel in PHPDoc tag @var does not match assigned variable \\$checkedInFromModel\\.$#',
	'identifier' => 'varTag.differentVariable',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/database/factories/AssetCheckInFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Variable \\$senderModel in PHPDoc tag @var does not match assigned variable \\$checkedOutToModel\\.$#',
	'identifier' => 'varTag.differentVariable',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/database/factories/AssetCheckOutFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method Faker\\\\Generator\\:\\:catchPhrase\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/database/factories/AssetFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Database\\\\Factories\\\\MaintenanceActivityFactory\\:\\:getStates\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/database/factories/MaintenanceActivityFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Database\\\\Factories\\\\MaintenanceActivityFactory\\:\\:randomizeState\\(\\) has parameter \\$states with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/database/factories/MaintenanceActivityFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Database\\\\Seeders\\\\AssetSeeder\\:\\:metadataSeeders\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/database/seeders/AssetSeeder.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Enums\\\\AssetCheckOutStatus\\:\\:getLabel\\(\\) never returns null so it can be removed from the return type\\.$#',
	'identifier' => 'return.unusedType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Enums/AssetCheckOutStatus.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Enums\\\\MaintenanceActivityStatus\\:\\:getLabel\\(\\) never returns null so it can be removed from the return type\\.$#',
	'identifier' => 'return.unusedType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Enums/MaintenanceActivityStatus.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Enums\\\\SystemAssetStatusClassification\\:\\:getLabel\\(\\) never returns null so it can be removed from the return type\\.$#',
	'identifier' => 'return.unusedType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Enums/SystemAssetStatusClassification.php',
];
$ignoreErrors[] = [
	'message' => '#^Match expression does not handle remaining value\\: mixed$#',
	'identifier' => 'match.unhandled',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Filament/Actions/CheckOutAssetHeaderAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Filament\\\\Resources\\\\AssetCheckInResource\\\\Components\\\\AssetCheckInViewAction\\:\\:renderInfolist\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Filament/Resources/AssetCheckInResource/Components/AssetCheckInViewAction.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var for variable \\$resource contains unresolvable type\\.$#',
	'identifier' => 'varTag.unresolvableType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Filament/Resources/AssetCheckInResource/Pages/EditAssetCheckIn.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var with type mixed is not subtype of native type string\\.$#',
	'identifier' => 'varTag.nativeType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Filament/Resources/AssetCheckInResource/Pages/EditAssetCheckIn.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Filament\\\\Resources\\\\AssetCheckInResource\\\\Pages\\\\ViewAssetCheckIn\\:\\:renderInfolist\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Filament/Resources/AssetCheckInResource/Pages/ViewAssetCheckIn.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Filament\\\\Resources\\\\AssetCheckOutResource\\\\Components\\\\AssetCheckOutViewAction\\:\\:renderInfolist\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Filament/Resources/AssetCheckOutResource/Components/AssetCheckOutViewAction.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var for variable \\$resource contains unresolvable type\\.$#',
	'identifier' => 'varTag.unresolvableType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Filament/Resources/AssetCheckOutResource/Pages/EditAssetCheckOut.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var with type mixed is not subtype of native type string\\.$#',
	'identifier' => 'varTag.nativeType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Filament/Resources/AssetCheckOutResource/Pages/EditAssetCheckOut.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Builder\\:\\:withoutReturned\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Filament/Resources/AssetCheckOutResource/Pages/ListAssetCheckOuts.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Filament\\\\Resources\\\\AssetCheckOutResource\\\\Pages\\\\ViewAssetCheckOut\\:\\:renderInfolist\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Filament/Resources/AssetCheckOutResource/Pages/ViewAssetCheckOut.php',
];
$ignoreErrors[] = [
	'message' => '#^Property AidingApp\\\\InventoryManagement\\\\Filament\\\\Resources\\\\AssetResource\\\\Pages\\\\AssetTimeline\\:\\:\\$modelsToTimeline type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Filament/Resources/AssetResource/Pages/AssetTimeline.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Filament\\\\Resources\\\\AssetResource\\\\Pages\\\\ManageAssetMaintenanceActivity\\:\\:managers\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Filament/Resources/AssetResource/Pages/ManageAssetMaintenanceActivity.php',
];
$ignoreErrors[] = [
	'message' => '#^Unsafe call to private method AidingApp\\\\InventoryManagement\\\\Filament\\\\Resources\\\\AssetResource\\\\Pages\\\\ManageAssetMaintenanceActivity\\:\\:managers\\(\\) through static\\:\\:\\.$#',
	'identifier' => 'staticClassAccess.privateMethod',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Filament/Resources/AssetResource/Pages/ManageAssetMaintenanceActivity.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Filament\\\\Resources\\\\MaintenanceActivityResource\\\\MaintenanceActivityViewAction\\:\\:renderInfolist\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Filament/Resources/MaintenanceActivityResource/MaintenanceActivityViewAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Models\\\\Asset\\:\\:checkIns\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/Asset.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Models\\\\Asset\\:\\:checkOuts\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/Asset.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Models\\\\Asset\\:\\:latestCheckIn\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasOne does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/Asset.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Models\\\\Asset\\:\\:latestCheckOut\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasOne does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/Asset.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Models\\\\Asset\\:\\:location\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/Asset.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Models\\\\Asset\\:\\:maintenanceActivities\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/Asset.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Models\\\\Asset\\:\\:newBelongsToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/Asset.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Models\\\\Asset\\:\\:newMorphToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/Asset.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Models\\\\Asset\\:\\:purchaseAge\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Casts\\\\Attribute does not specify its types\\: TGet, TSet$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/Asset.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Models\\\\Asset\\:\\:status\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/Asset.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Models\\\\Asset\\:\\:type\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/Asset.php',
];
$ignoreErrors[] = [
	'message' => '#^Using nullsafe property access on non\\-nullable type AidingApp\\\\InventoryManagement\\\\Models\\\\AssetCheckOut\\. Use \\-\\> instead\\.$#',
	'identifier' => 'nullsafe.neverNull',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/Asset.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:checkIns\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/AssetCheckIn.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Models\\\\AssetCheckIn\\:\\:asset\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/AssetCheckIn.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Models\\\\AssetCheckIn\\:\\:checkOut\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasOne does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/AssetCheckIn.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Models\\\\AssetCheckIn\\:\\:checkedInBy\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/AssetCheckIn.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Models\\\\AssetCheckIn\\:\\:checkedInFrom\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/AssetCheckIn.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Models\\\\AssetCheckIn\\:\\:getTimelineData\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/AssetCheckIn.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Models\\\\AssetCheckIn\\:\\:newBelongsToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/AssetCheckIn.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Models\\\\AssetCheckIn\\:\\:newMorphToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/AssetCheckIn.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Models\\\\AssetCheckIn\\:\\:timelineRecord\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphOne does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/AssetCheckIn.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:checkOuts\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/AssetCheckOut.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Models\\\\AssetCheckOut\\:\\:asset\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/AssetCheckOut.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Models\\\\AssetCheckOut\\:\\:checkIn\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/AssetCheckOut.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Models\\\\AssetCheckOut\\:\\:checkedOutBy\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/AssetCheckOut.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Models\\\\AssetCheckOut\\:\\:checkedOutTo\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/AssetCheckOut.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Models\\\\AssetCheckOut\\:\\:getTimelineData\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/AssetCheckOut.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Models\\\\AssetCheckOut\\:\\:newBelongsToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/AssetCheckOut.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Models\\\\AssetCheckOut\\:\\:newMorphToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/AssetCheckOut.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Models\\\\AssetCheckOut\\:\\:scopeWithoutReturned\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/AssetCheckOut.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Models\\\\AssetCheckOut\\:\\:scopeWithoutReturned\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder does not specify its types\\: TModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/AssetCheckOut.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Models\\\\AssetCheckOut\\:\\:status\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Casts\\\\Attribute does not specify its types\\: TGet, TSet$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/AssetCheckOut.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Models\\\\AssetCheckOut\\:\\:timelineRecord\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphOne does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/AssetCheckOut.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Models\\\\AssetLocation\\:\\:assets\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/AssetLocation.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Models\\\\AssetLocation\\:\\:newBelongsToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/AssetLocation.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Models\\\\AssetLocation\\:\\:newMorphToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/AssetLocation.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Models\\\\AssetStatus\\:\\:assets\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/AssetStatus.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Models\\\\AssetStatus\\:\\:newBelongsToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/AssetStatus.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Models\\\\AssetStatus\\:\\:newMorphToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/AssetStatus.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Models\\\\AssetType\\:\\:assets\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/AssetType.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Models\\\\AssetType\\:\\:newBelongsToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/AssetType.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Models\\\\AssetType\\:\\:newMorphToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/AssetType.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:maintenanceActivities\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/MaintenanceActivity.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Models\\\\MaintenanceActivity\\:\\:asset\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/MaintenanceActivity.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Models\\\\MaintenanceActivity\\:\\:getTimelineData\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/MaintenanceActivity.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Models\\\\MaintenanceActivity\\:\\:maintenanceProvider\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/MaintenanceActivity.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Models\\\\MaintenanceActivity\\:\\:newBelongsToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/MaintenanceActivity.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Models\\\\MaintenanceActivity\\:\\:newMorphToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/MaintenanceActivity.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Models\\\\MaintenanceActivity\\:\\:timelineRecord\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphOne does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/MaintenanceActivity.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Models\\\\MaintenanceProvider\\:\\:maintenanceActivities\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/MaintenanceProvider.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Models\\\\MaintenanceProvider\\:\\:newBelongsToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/MaintenanceProvider.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Models\\\\MaintenanceProvider\\:\\:newMorphToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/MaintenanceProvider.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Models\\\\Scopes\\\\ClassifiedAs\\:\\:__invoke\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/Scopes/ClassifiedAs.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Policies\\\\AssetCheckInPolicy\\:\\:requiredFeatures\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Policies/AssetCheckInPolicy.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Policies\\\\AssetCheckOutPolicy\\:\\:requiredFeatures\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Policies/AssetCheckOutPolicy.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Policies\\\\AssetLocationPolicy\\:\\:requiredFeatures\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Policies/AssetLocationPolicy.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Policies\\\\AssetPolicy\\:\\:requiredFeatures\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Policies/AssetPolicy.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Policies\\\\AssetStatusPolicy\\:\\:requiredFeatures\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Policies/AssetStatusPolicy.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Policies\\\\AssetTypePolicy\\:\\:requiredFeatures\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Policies/AssetTypePolicy.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Providers\\\\InventoryManagementServiceProvider\\:\\:boot\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Providers/InventoryManagementServiceProvider.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\KnowledgeBase\\\\Database\\\\Factories\\\\KnowledgeBaseCategoryFactory\\:\\:icons\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/database/factories/KnowledgeBaseCategoryFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Using nullsafe property access "\\?\\-\\>id" on left side of \\?\\? is unnecessary\\. Use \\-\\> instead\\.$#',
	'identifier' => 'nullsafe.neverNull',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/database/factories/KnowledgeBaseItemFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var for variable \\$resource contains unresolvable type\\.$#',
	'identifier' => 'varTag.unresolvableType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/src/Filament/Resources/KnowledgeBaseCategoryResource/Pages/EditKnowledgeBaseCategory.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var with type mixed is not subtype of native type string\\.$#',
	'identifier' => 'varTag.nativeType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/src/Filament/Resources/KnowledgeBaseCategoryResource/Pages/EditKnowledgeBaseCategory.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$category\\.$#',
	'identifier' => 'property.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/src/Filament/Resources/KnowledgeBaseItemResource.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$division\\.$#',
	'identifier' => 'property.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/src/Filament/Resources/KnowledgeBaseItemResource.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$quality\\.$#',
	'identifier' => 'property.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/src/Filament/Resources/KnowledgeBaseItemResource.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$status\\.$#',
	'identifier' => 'property.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/src/Filament/Resources/KnowledgeBaseItemResource.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\KnowledgeBase\\\\Filament\\\\Resources\\\\KnowledgeBaseItemResource\\:\\:getGlobalSearchEloquentQuery\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder does not specify its types\\: TModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/src/Filament/Resources/KnowledgeBaseItemResource.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$title\\.$#',
	'identifier' => 'property.notFound',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/knowledge-base/src/Filament/Resources/KnowledgeBaseItemResource/Pages/EditKnowledgeBaseItem.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var for variable \\$resource contains unresolvable type\\.$#',
	'identifier' => 'varTag.unresolvableType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/src/Filament/Resources/KnowledgeBaseItemResource/Pages/EditKnowledgeBaseItem.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var with type mixed is not subtype of native type string\\.$#',
	'identifier' => 'varTag.nativeType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/src/Filament/Resources/KnowledgeBaseItemResource/Pages/EditKnowledgeBaseItem.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\KnowledgeBase\\\\Filament\\\\Resources\\\\KnowledgeBaseItemResource\\\\Pages\\\\EditKnowledgeBaseItemMetadata\\:\\:form\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/src/Filament/Resources/KnowledgeBaseItemResource/Pages/EditKnowledgeBaseItemMetadata.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property App\\\\Models\\\\Tag\\:\\:\\$pivot\\.$#',
	'identifier' => 'property.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/src/Filament/Resources/KnowledgeBaseItemResource/Pages/ListKnowledgeBaseItems.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$notes\\.$#',
	'identifier' => 'property.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/src/Filament/Resources/KnowledgeBaseItemResource/Pages/ListKnowledgeBaseItems.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$public\\.$#',
	'identifier' => 'property.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/src/Filament/Resources/KnowledgeBaseItemResource/Pages/ListKnowledgeBaseItems.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$title\\.$#',
	'identifier' => 'property.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/src/Filament/Resources/KnowledgeBaseItemResource/Pages/ListKnowledgeBaseItems.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var for variable \\$resource contains unresolvable type\\.$#',
	'identifier' => 'varTag.unresolvableType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/src/Filament/Resources/KnowledgeBaseQualityResource/Pages/EditKnowledgeBaseQuality.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var with type mixed is not subtype of native type string\\.$#',
	'identifier' => 'varTag.nativeType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/src/Filament/Resources/KnowledgeBaseQualityResource/Pages/EditKnowledgeBaseQuality.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var for variable \\$resource contains unresolvable type\\.$#',
	'identifier' => 'varTag.unresolvableType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/src/Filament/Resources/KnowledgeBaseStatusResource/Pages/EditKnowledgeBaseStatus.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var with type mixed is not subtype of native type string\\.$#',
	'identifier' => 'varTag.nativeType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/src/Filament/Resources/KnowledgeBaseStatusResource/Pages/EditKnowledgeBaseStatus.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\KnowledgeBase\\\\Jobs\\\\KnowledgeBaseItemDownloadExternalMedia\\:\\:processContentItem\\(\\) has parameter \\$content with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/src/Jobs/KnowledgeBaseItemDownloadExternalMedia.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\KnowledgeBase\\\\Jobs\\\\KnowledgeBaseItemDownloadExternalMedia\\:\\:processContentItem\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/src/Jobs/KnowledgeBaseItemDownloadExternalMedia.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\KnowledgeBase\\\\Models\\\\KnowledgeBaseCategory\\:\\:knowledgeBaseItems\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/src/Models/KnowledgeBaseCategory.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\KnowledgeBase\\\\Models\\\\KnowledgeBaseCategory\\:\\:newBelongsToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/src/Models/KnowledgeBaseCategory.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\KnowledgeBase\\\\Models\\\\KnowledgeBaseCategory\\:\\:newMorphToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/src/Models/KnowledgeBaseCategory.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\KnowledgeBase\\\\Models\\\\KnowledgeBaseCategory\\:\\:parentCategory\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/src/Models/KnowledgeBaseCategory.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\KnowledgeBase\\\\Models\\\\KnowledgeBaseCategory\\:\\:subCategories\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/src/Models/KnowledgeBaseCategory.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\KnowledgeBase\\\\Models\\\\KnowledgeBaseItem\\:\\:category\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/src/Models/KnowledgeBaseItem.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\KnowledgeBase\\\\Models\\\\KnowledgeBaseItem\\:\\:division\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/src/Models/KnowledgeBaseItem.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\KnowledgeBase\\\\Models\\\\KnowledgeBaseItem\\:\\:newBelongsToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/src/Models/KnowledgeBaseItem.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\KnowledgeBase\\\\Models\\\\KnowledgeBaseItem\\:\\:newMorphToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/src/Models/KnowledgeBaseItem.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\KnowledgeBase\\\\Models\\\\KnowledgeBaseItem\\:\\:quality\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/src/Models/KnowledgeBaseItem.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\KnowledgeBase\\\\Models\\\\KnowledgeBaseItem\\:\\:scopePublic\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/src/Models/KnowledgeBaseItem.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\KnowledgeBase\\\\Models\\\\KnowledgeBaseItem\\:\\:scopePublic\\(\\) has parameter \\$query with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/src/Models/KnowledgeBaseItem.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\KnowledgeBase\\\\Models\\\\KnowledgeBaseItem\\:\\:status\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/src/Models/KnowledgeBaseItem.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\KnowledgeBase\\\\Models\\\\KnowledgeBaseItem\\:\\:tags\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/src/Models/KnowledgeBaseItem.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\KnowledgeBase\\\\Models\\\\KnowledgeBaseItem\\:\\:votes\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/src/Models/KnowledgeBaseItem.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\KnowledgeBase\\\\Models\\\\KnowledgeBaseQuality\\:\\:knowledgeBaseItems\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/src/Models/KnowledgeBaseQuality.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\KnowledgeBase\\\\Models\\\\KnowledgeBaseQuality\\:\\:newBelongsToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/src/Models/KnowledgeBaseQuality.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\KnowledgeBase\\\\Models\\\\KnowledgeBaseQuality\\:\\:newMorphToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/src/Models/KnowledgeBaseQuality.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\KnowledgeBase\\\\Models\\\\KnowledgeBaseStatus\\:\\:knowledgeBaseItems\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/src/Models/KnowledgeBaseStatus.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\KnowledgeBase\\\\Models\\\\KnowledgeBaseStatus\\:\\:newBelongsToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/src/Models/KnowledgeBaseStatus.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\KnowledgeBase\\\\Models\\\\KnowledgeBaseStatus\\:\\:newMorphToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/src/Models/KnowledgeBaseStatus.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\KnowledgeBase\\\\Policies\\\\KnowledgeBaseCategoryPolicy\\:\\:hasAnyLicense\\(\\) has parameter \\$licenses with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/src/Policies/KnowledgeBaseCategoryPolicy.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\KnowledgeBase\\\\Policies\\\\KnowledgeBaseCategoryPolicy\\:\\:hasLicenses\\(\\) has parameter \\$licenses with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/src/Policies/KnowledgeBaseCategoryPolicy.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\KnowledgeBase\\\\Policies\\\\KnowledgeBaseItemPolicy\\:\\:hasAnyLicense\\(\\) has parameter \\$licenses with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/src/Policies/KnowledgeBaseItemPolicy.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\KnowledgeBase\\\\Policies\\\\KnowledgeBaseItemPolicy\\:\\:hasLicenses\\(\\) has parameter \\$licenses with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/src/Policies/KnowledgeBaseItemPolicy.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\KnowledgeBase\\\\Policies\\\\KnowledgeBaseQualityPolicy\\:\\:hasAnyLicense\\(\\) has parameter \\$licenses with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/src/Policies/KnowledgeBaseQualityPolicy.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\KnowledgeBase\\\\Policies\\\\KnowledgeBaseQualityPolicy\\:\\:hasLicenses\\(\\) has parameter \\$licenses with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/src/Policies/KnowledgeBaseQualityPolicy.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\KnowledgeBase\\\\Policies\\\\KnowledgeBaseStatusPolicy\\:\\:hasAnyLicense\\(\\) has parameter \\$licenses with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/src/Policies/KnowledgeBaseStatusPolicy.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\KnowledgeBase\\\\Policies\\\\KnowledgeBaseStatusPolicy\\:\\:hasLicenses\\(\\) has parameter \\$licenses with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/src/Policies/KnowledgeBaseStatusPolicy.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method call\\(\\) on an unknown class static\\.$#',
	'identifier' => 'class.notFound',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/knowledge-base/tests/KnowledgeBaseCategory/CreateKnowledgeBaseCategoryTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property Pest\\\\Expectation\\<AidingApp\\\\KnowledgeBase\\\\Models\\\\KnowledgeBaseCategory\\|null\\>\\:\\:\\$subCategories\\.$#',
	'identifier' => 'property.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/tests/KnowledgeBaseCategory/EditKnowledgeBaseCategoryTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method assertFormFieldExists\\(\\) on an unknown class static\\.$#',
	'identifier' => 'class.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/tests/KnowledgeBaseCategory/EditKnowledgeBaseCategoryTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method assertHasNoTableActionErrors\\(\\) on an unknown class static\\.$#',
	'identifier' => 'class.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/tests/KnowledgeBaseCategory/EditKnowledgeBaseCategoryTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method assertSuccessful\\(\\) on an unknown class static\\.$#',
	'identifier' => 'class.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/tests/KnowledgeBaseCategory/EditKnowledgeBaseCategoryTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method call\\(\\) on an unknown class static\\.$#',
	'identifier' => 'class.notFound',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/knowledge-base/tests/KnowledgeBaseCategory/EditKnowledgeBaseCategoryTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method Illuminate\\\\Testing\\\\TestResponse\\:\\:assertActionDisabled\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/tests/KnowledgeBaseItem/CreateKnowledgeBaseItemTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method assertHasNoActionErrors\\(\\) on an unknown class static\\.$#',
	'identifier' => 'class.notFound',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/knowledge-base/tests/KnowledgeBaseItem/CreateKnowledgeBaseItemTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method filterTable\\(\\) on an unknown class static\\.$#',
	'identifier' => 'class.notFound',
	'count' => 6,
	'path' => __DIR__ . '/app-modules/knowledge-base/tests/KnowledgeBaseItem/ListKnowledgeBaseItemsTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Using nullsafe property access "\\?\\-\\>id" on left side of \\?\\? is unnecessary\\. Use \\-\\> instead\\.$#',
	'identifier' => 'nullsafe.neverNull',
	'count' => 4,
	'path' => __DIR__ . '/app-modules/knowledge-base/tests/KnowledgeBaseItem/RequestFactories/CreateKnowledgeBaseItemRequestFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method call\\(\\) on an unknown class static\\.$#',
	'identifier' => 'class.notFound',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/knowledge-base/tests/KnowledgeBaseQuality/CreateKnowledgeBaseQualityTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method call\\(\\) on an unknown class static\\.$#',
	'identifier' => 'class.notFound',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/knowledge-base/tests/KnowledgeBaseQuality/EditKnowledgeBaseCategoryTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method call\\(\\) on an unknown class static\\.$#',
	'identifier' => 'class.notFound',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/knowledge-base/tests/KnowledgeBaseStatus/CreateKnowledgeBaseStatusTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method call\\(\\) on an unknown class static\\.$#',
	'identifier' => 'class.notFound',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/knowledge-base/tests/KnowledgeBaseStatus/EditKnowledgeBaseStatusTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$guardName of method Illuminate\\\\Database\\\\Migrations\\\\Migration@anonymous/app\\-modules/license\\-management/database/migrations/2024_11_21_194048_data_seed_permissions_for_product_license_module\\.php\\:42\\:\\:deletePermissions\\(\\) expects string, array given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/license-management/database/migrations/2024_11_21_194048_data_seed_permissions_for_product_license_module.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Illuminate\\\\Database\\\\Migrations\\\\Migration@anonymous/app\\-modules/license\\-management/database/migrations/2024_11_21_194048_data_seed_permissions_for_product_license_module\\.php\\:42\\:\\:\\$guards type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/license-management/database/migrations/2024_11_21_194048_data_seed_permissions_for_product_license_module.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Illuminate\\\\Database\\\\Migrations\\\\Migration@anonymous/app\\-modules/license\\-management/database/migrations/2024_11_21_194048_data_seed_permissions_for_product_license_module\\.php\\:42\\:\\:\\$permissions type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/license-management/database/migrations/2024_11_21_194048_data_seed_permissions_for_product_license_module.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\LicenseManagement\\\\Enums\\\\ProductLicenseStatus\\:\\:getLabel\\(\\) never returns null so it can be removed from the return type\\.$#',
	'identifier' => 'return.unusedType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/license-management/src/Enums/ProductLicenseStatus.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var for variable \\$resource contains unresolvable type\\.$#',
	'identifier' => 'varTag.unresolvableType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/license-management/src/Filament/Resources/ProductResource/Pages/EditProduct.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var with type mixed is not subtype of native type string\\.$#',
	'identifier' => 'varTag.nativeType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/license-management/src/Filament/Resources/ProductResource/Pages/EditProduct.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\LicenseManagement\\\\Models\\\\Product\\:\\:newBelongsToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/license-management/src/Models/Product.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\LicenseManagement\\\\Models\\\\Product\\:\\:newMorphToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/license-management/src/Models/Product.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\LicenseManagement\\\\Models\\\\Product\\:\\:productLicenses\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/license-management/src/Models/Product.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\LicenseManagement\\\\Models\\\\ProductLicense\\:\\:contact\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/license-management/src/Models/ProductLicense.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\LicenseManagement\\\\Models\\\\ProductLicense\\:\\:newBelongsToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/license-management/src/Models/ProductLicense.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\LicenseManagement\\\\Models\\\\ProductLicense\\:\\:newMorphToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/license-management/src/Models/ProductLicense.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\LicenseManagement\\\\Models\\\\ProductLicense\\:\\:product\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/license-management/src/Models/ProductLicense.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\LicenseManagement\\\\Models\\\\ProductLicense\\:\\:status\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Casts\\\\Attribute does not specify its types\\: TGet, TSet$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/license-management/src/Models/ProductLicense.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc type array of property AidingApp\\\\LicenseManagement\\\\Models\\\\ProductLicense\\:\\:\\$appends is not covariant with PHPDoc type list\\<string\\> of overridden property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$appends\\.$#',
	'identifier' => 'property.phpDocType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/license-management/src/Models/ProductLicense.php',
];
$ignoreErrors[] = [
	'message' => '#^Property AidingApp\\\\LicenseManagement\\\\Models\\\\ProductLicense\\:\\:\\$appends type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/license-management/src/Models/ProductLicense.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\LicenseManagement\\\\Models\\\\Scopes\\\\AuthorizeLicensesScope\\:\\:apply\\(\\) has parameter \\$builder with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/license-management/src/Models/Scopes/AuthorizeLicensesScope.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property AidingApp\\\\LicenseManagement\\\\Models\\\\ProductLicense\\:\\:\\$created_by_id\\.$#',
	'identifier' => 'property.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/license-management/src/Observers/ProductLicenseObserver.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property AidingApp\\\\LicenseManagement\\\\Models\\\\Product\\:\\:\\$created_by_id\\.$#',
	'identifier' => 'property.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/license-management/src/Observers/ProductObserver.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\LicenseManagement\\\\Providers\\\\LicenseManagementServiceProvider\\:\\:boot\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/license-management/src/Providers/LicenseManagementServiceProvider.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property AidingApp\\\\LicenseManagement\\\\Models\\\\ProductLicense\\:\\:\\$status\\.$#',
	'identifier' => 'property.notFound',
	'count' => 4,
	'path' => __DIR__ . '/app-modules/license-management/tests/Unit/ProductLicenseStatusTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Unable to resolve the template type TValue in call to function expect$#',
	'identifier' => 'argument.templateType',
	'count' => 4,
	'path' => __DIR__ . '/app-modules/license-management/tests/Unit/ProductLicenseStatusTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Class AidingApp\\\\Notification\\\\Database\\\\Factories\\\\SubscriptionFactory extends generic class Illuminate\\\\Database\\\\Eloquent\\\\Factories\\\\Factory but does not specify its types\\: TModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/database/factories/SubscriptionFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @extends has invalid value \\(Subscription\\>\\)\\: Unexpected token "\\>", expected \'\\<\' at offset 28 on line 2$#',
	'identifier' => 'phpDoc.parseError',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/database/factories/SubscriptionFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Notification\\\\Actions\\\\SubscriptionToggle\\:\\:handle\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/src/Actions/SubscriptionToggle.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Notification\\\\DataTransferObjects\\\\EmailChannelResultData\\:\\:__construct\\(\\) has parameter \\$recipients with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/src/DataTransferObjects/EmailChannelResultData.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Notification\\\\Enums\\\\EmailMessageEventType\\:\\:getLabel\\(\\) never returns null so it can be removed from the return type\\.$#',
	'identifier' => 'return.unusedType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/src/Enums/EmailMessageEventType.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Notification\\\\Enums\\\\EmailMessageEventType\\:\\:getLabel\\(\\) never returns string so it can be removed from the return type\\.$#',
	'identifier' => 'return.unusedType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/src/Enums/EmailMessageEventType.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Notification\\\\Enums\\\\NotificationChannel\\:\\:getEngagementOptions\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/src/Enums/NotificationChannel.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Notification\\\\Enums\\\\NotificationChannel\\:\\:getIcon\\(\\) never returns null so it can be removed from the return type\\.$#',
	'identifier' => 'return.unusedType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/src/Enums/NotificationChannel.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Notification\\\\Enums\\\\NotificationDeliveryStatus\\:\\:getLabel\\(\\) never returns null so it can be removed from the return type\\.$#',
	'identifier' => 'return.unusedType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/src/Enums/NotificationDeliveryStatus.php',
];
$ignoreErrors[] = [
	'message' => '#^Property AidingApp\\\\Notification\\\\Exceptions\\\\NotificationQuotaExceeded\\:\\:\\$message has no type specified\\.$#',
	'identifier' => 'missingType.property',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/src/Exceptions/NotificationQuotaExceeded.php',
];
$ignoreErrors[] = [
	'message' => '#^Property AidingApp\\\\Notification\\\\Exceptions\\\\SubscriptionAlreadyExistsException\\:\\:\\$message has no type specified\\.$#',
	'identifier' => 'missingType.property',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/src/Exceptions/SubscriptionAlreadyExistsException.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),Illuminate\\\\Database\\\\Eloquent\\\\Model\\>\\:\\:each\\(\\) expects callable\\(Illuminate\\\\Database\\\\Eloquent\\\\Model, int\\|string\\)\\: mixed, Closure\\(AidingApp\\\\Notification\\\\Models\\\\Contracts\\\\Subscribable\\)\\: void given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/src/Filament/Actions/SubscribeBulkAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:displayNameKey\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/src/Listeners/NotifyUserOfSubscriptionCreated.php',
];
$ignoreErrors[] = [
	'message' => '#^Match expression does not handle remaining value\\: class\\-string\\<Illuminate\\\\Database\\\\Eloquent\\\\Model\\>&literal\\-string$#',
	'identifier' => 'match.unhandled',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/src/Listeners/NotifyUserOfSubscriptionCreated.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:displayNameKey\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/src/Listeners/NotifyUserOfSubscriptionDeleted.php',
];
$ignoreErrors[] = [
	'message' => '#^Match expression does not handle remaining value\\: class\\-string\\<Illuminate\\\\Database\\\\Eloquent\\\\Model\\>&literal\\-string$#',
	'identifier' => 'match.unhandled',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/src/Listeners/NotifyUserOfSubscriptionDeleted.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Notification\\\\Models\\\\Contracts\\\\CanBeNotified\\:\\:notifications\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/src/Models/Contracts/CanBeNotified.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Notification\\\\Models\\\\Contracts\\\\CanBeNotified\\:\\:notify\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/src/Models/Contracts/CanBeNotified.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Notification\\\\Models\\\\Contracts\\\\CanBeNotified\\:\\:notify\\(\\) has parameter \\$instance with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/src/Models/Contracts/CanBeNotified.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Notification\\\\Models\\\\Contracts\\\\CanBeNotified\\:\\:notifyNow\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/src/Models/Contracts/CanBeNotified.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Notification\\\\Models\\\\Contracts\\\\CanBeNotified\\:\\:notifyNow\\(\\) has parameter \\$channels with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/src/Models/Contracts/CanBeNotified.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Notification\\\\Models\\\\Contracts\\\\CanBeNotified\\:\\:notifyNow\\(\\) has parameter \\$instance with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/src/Models/Contracts/CanBeNotified.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Notification\\\\Models\\\\Contracts\\\\CanBeNotified\\:\\:readNotifications\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/src/Models/Contracts/CanBeNotified.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Notification\\\\Models\\\\Contracts\\\\CanBeNotified\\:\\:routeNotificationFor\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/src/Models/Contracts/CanBeNotified.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Notification\\\\Models\\\\Contracts\\\\CanBeNotified\\:\\:routeNotificationFor\\(\\) has parameter \\$driver with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/src/Models/Contracts/CanBeNotified.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Notification\\\\Models\\\\Contracts\\\\CanBeNotified\\:\\:routeNotificationFor\\(\\) has parameter \\$notification with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/src/Models/Contracts/CanBeNotified.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Notification\\\\Models\\\\Contracts\\\\CanBeNotified\\:\\:unreadNotifications\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/src/Models/Contracts/CanBeNotified.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Notification\\\\Models\\\\Contracts\\\\Message\\:\\:recipient\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/src/Models/Contracts/Message.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Notification\\\\Models\\\\Contracts\\\\Message\\:\\:related\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/src/Models/Contracts/Message.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Notification\\\\Models\\\\Contracts\\\\Subscribable\\:\\:subscriptions\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/src/Models/Contracts/Subscribable.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Notification\\\\Models\\\\DatabaseMessage\\:\\:recipient\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/src/Models/DatabaseMessage.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Notification\\\\Models\\\\DatabaseMessage\\:\\:related\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/src/Models/DatabaseMessage.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Notification\\\\Models\\\\EmailMessage\\:\\:events\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/src/Models/EmailMessage.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Notification\\\\Models\\\\EmailMessage\\:\\:recipient\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/src/Models/EmailMessage.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Notification\\\\Models\\\\EmailMessage\\:\\:related\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/src/Models/EmailMessage.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Notification\\\\Models\\\\EmailMessageEvent\\:\\:message\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/src/Models/EmailMessageEvent.php',
];
$ignoreErrors[] = [
	'message' => '#^Class AidingApp\\\\Notification\\\\Models\\\\Subscription uses generic trait Illuminate\\\\Database\\\\Eloquent\\\\Factories\\\\HasFactory but does not specify its types\\: TFactory$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/src/Models/Subscription.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Notification\\\\Models\\\\Subscription\\:\\:scopeLicensedToEducatable\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/src/Models/Subscription.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Notification\\\\Models\\\\Subscription\\:\\:scopeLicensedToEducatable\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder does not specify its types\\: TModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/src/Models/Subscription.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Notification\\\\Models\\\\Subscription\\:\\:subscribable\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/src/Models/Subscription.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Notification\\\\Models\\\\Subscription\\:\\:user\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/src/Models/Subscription.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method Illuminate\\\\Notifications\\\\Notification\\:\\:toDatabase\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/src/Notifications/Channels/DatabaseChannel.php',
];
$ignoreErrors[] = [
	'message' => '#^Match expression does not handle remaining value\\: true$#',
	'identifier' => 'match.unhandled',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/src/Notifications/Channels/DatabaseChannel.php',
];
$ignoreErrors[] = [
	'message' => '#^Return type \\(void\\) of method AidingApp\\\\Notification\\\\Notifications\\\\Channels\\\\DatabaseChannel\\:\\:send\\(\\) should be compatible with return type \\(Illuminate\\\\Database\\\\Eloquent\\\\Model\\) of method Illuminate\\\\Notifications\\\\Channels\\\\DatabaseChannel\\:\\:send\\(\\)$#',
	'identifier' => 'method.childReturnType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/src/Notifications/Channels/DatabaseChannel.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method Illuminate\\\\Notifications\\\\Notification\\:\\:toMail\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/notification/src/Notifications/Channels/MailChannel.php',
];
$ignoreErrors[] = [
	'message' => '#^Expression on left side of \\?\\? is not nullable\\.$#',
	'identifier' => 'nullCoalesce.expr',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/src/Notifications/Channels/MailChannel.php',
];
$ignoreErrors[] = [
	'message' => '#^Return type \\(void\\) of method AidingApp\\\\Notification\\\\Notifications\\\\Channels\\\\MailChannel\\:\\:send\\(\\) should be compatible with return type \\(Illuminate\\\\Mail\\\\SentMessage\\|null\\) of method Illuminate\\\\Notifications\\\\Channels\\\\MailChannel\\:\\:send\\(\\)$#',
	'identifier' => 'method.childReturnType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/src/Notifications/Channels/MailChannel.php',
];
$ignoreErrors[] = [
	'message' => '#^Using nullsafe property access "\\?\\-\\>isDemoModeEnabled" on left side of \\?\\? is unnecessary\\. Use \\-\\> instead\\.$#',
	'identifier' => 'nullsafe.neverNull',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/src/Notifications/Channels/MailChannel.php',
];
$ignoreErrors[] = [
	'message' => '#^Using nullsafe property access on non\\-nullable type mixed\\. Use \\-\\> instead\\.$#',
	'identifier' => 'nullsafe.neverNull',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/src/Notifications/Channels/MailChannel.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Notification\\\\Notifications\\\\Contracts\\\\OnDemandNotification\\:\\:identifyRecipient\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/src/Notifications/Contracts/OnDemandNotification.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Notification\\\\Notifications\\\\Messages\\\\MailMessage\\:\\:toArray\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/src/Notifications/Messages/MailMessage.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Illuminate\\\\Notifications\\\\Messages\\\\SimpleMessage\\:\\:\\$actionUrl \\(string\\) on left side of \\?\\? is not nullable\\.$#',
	'identifier' => 'nullCoalesce.property',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/src/Notifications/Messages/MailMessage.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Notification\\\\Notifications\\\\Messages\\\\TwilioMessage\\:\\:toArray\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/src/Notifications/Messages/TwilioMessage.php',
];
$ignoreErrors[] = [
	'message' => '#^Match expression does not handle remaining value\\: string$#',
	'identifier' => 'match.unhandled',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/notification/src/Observers/SubscriptionObserver.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:getLicenseType\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 5,
	'path' => __DIR__ . '/app-modules/notification/src/Policies/SubscriptionPolicy.php',
];
$ignoreErrors[] = [
	'message' => '#^Using nullsafe method call on non\\-nullable type Illuminate\\\\Database\\\\Eloquent\\\\Model\\. Use \\-\\> instead\\.$#',
	'identifier' => 'nullsafe.neverNull',
	'count' => 5,
	'path' => __DIR__ . '/app-modules/notification/src/Policies/SubscriptionPolicy.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Notification\\\\Rules\\\\SubscribableIdExistsRule\\:\\:setData\\(\\) has parameter \\$data with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/src/Rules/SubscribableIdExistsRule.php',
];
$ignoreErrors[] = [
	'message' => '#^Property AidingApp\\\\Notification\\\\Rules\\\\SubscribableIdExistsRule\\:\\:\\$data has no type specified\\.$#',
	'identifier' => 'missingType.property',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/src/Rules/SubscribableIdExistsRule.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Notification\\\\Rules\\\\UniqueSubscriptionRule\\:\\:setData\\(\\) has parameter \\$data with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/src/Rules/UniqueSubscriptionRule.php',
];
$ignoreErrors[] = [
	'message' => '#^Property AidingApp\\\\Notification\\\\Rules\\\\UniqueSubscriptionRule\\:\\:\\$data has no type specified\\.$#',
	'identifier' => 'missingType.property',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/src/Rules/UniqueSubscriptionRule.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Notification\\\\Tests\\\\Fixtures\\\\TestDatabaseNotification\\:\\:toDatabase\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/tests/Fixtures/TestDatabaseNotification.php',
];
$ignoreErrors[] = [
	'message' => '#^Method TestSystemNotification\\:\\:via\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/tests/Notifications/Channels/MailChannelEmailMessageTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Match arm comparison between AidingApp\\\\Portal\\\\Enums\\\\PortalType\\:\\:KnowledgeManagement and AidingApp\\\\Portal\\\\Enums\\\\PortalType\\:\\:KnowledgeManagement is always true\\.$#',
	'identifier' => 'match.alwaysTrue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/portal/src/Actions/GeneratePortalEmbedCode.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Portal\\\\DataTransferObjects\\\\KnowledgeBaseArticleData\\:\\:__construct\\(\\) has parameter \\$tags with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/portal/src/DataTransferObjects/KnowledgeBaseArticleData.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Portal\\\\DataTransferObjects\\\\KnowledgeBaseArticleData\\:\\:__construct\\(\\) has parameter \\$vote with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/portal/src/DataTransferObjects/KnowledgeBaseArticleData.php',
];
$ignoreErrors[] = [
	'message' => '#^Property AidingApp\\\\Portal\\\\DataTransferObjects\\\\KnowledgeManagementSearchData\\:\\:\\$articles with generic class Spatie\\\\LaravelData\\\\PaginatedDataCollection does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/portal/src/DataTransferObjects/KnowledgeManagementSearchData.php',
];
$ignoreErrors[] = [
	'message' => '#^Property AidingApp\\\\Portal\\\\DataTransferObjects\\\\KnowledgeManagementSearchData\\:\\:\\$categories with generic class Spatie\\\\LaravelData\\\\DataCollection does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/portal/src/DataTransferObjects/KnowledgeManagementSearchData.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Portal\\\\Enums\\\\PortalType\\:\\:getLabel\\(\\) never returns null so it can be removed from the return type\\.$#',
	'identifier' => 'return.unusedType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/portal/src/Enums/PortalType.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Portal\\\\Filament\\\\Pages\\\\ManagePortalSettings\\:\\:getFormActions\\(\\) has invalid return type AidingApp\\\\Portal\\\\Filament\\\\Pages\\\\ActionGroup\\.$#',
	'identifier' => 'class.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/portal/src/Filament/Pages/ManagePortalSettings.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Portal\\\\Filament\\\\Pages\\\\ManagePortalSettings\\:\\:getFormActions\\(\\) should return array\\<AidingApp\\\\Portal\\\\Filament\\\\Pages\\\\ActionGroup\\|Filament\\\\Forms\\\\Components\\\\Actions\\\\Action\\> but returns array\\<Filament\\\\Actions\\\\Action\\|Filament\\\\Actions\\\\ActionGroup\\>\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/portal/src/Filament/Pages/ManagePortalSettings.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$author\\.$#',
	'identifier' => 'property.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/portal/src/Http/Controllers/KnowledgeManagementPortal/CreateServiceRequestController.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:priority\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/portal/src/Http/Controllers/KnowledgeManagementPortal/CreateServiceRequestController.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Portal\\\\Http\\\\Controllers\\\\KnowledgeManagementPortal\\\\CreateServiceRequestController\\:\\:addFieldToStep\\(\\) has parameter \\$block with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/portal/src/Http/Controllers/KnowledgeManagementPortal/CreateServiceRequestController.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Portal\\\\Http\\\\Controllers\\\\KnowledgeManagementPortal\\\\CreateServiceRequestController\\:\\:formatBlock\\(\\) has parameter \\$data with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/portal/src/Http/Controllers/KnowledgeManagementPortal/CreateServiceRequestController.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Portal\\\\Http\\\\Controllers\\\\KnowledgeManagementPortal\\\\CreateServiceRequestController\\:\\:formatBlock\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/portal/src/Http/Controllers/KnowledgeManagementPortal/CreateServiceRequestController.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Portal\\\\Http\\\\Controllers\\\\KnowledgeManagementPortal\\\\CreateServiceRequestController\\:\\:formatStep\\(\\) has parameter \\$content with generic class Illuminate\\\\Support\\\\Collection but does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/portal/src/Http/Controllers/KnowledgeManagementPortal/CreateServiceRequestController.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Portal\\\\Http\\\\Controllers\\\\KnowledgeManagementPortal\\\\CreateServiceRequestController\\:\\:processSubmissionField\\(\\) has parameter \\$fields with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/portal/src/Http/Controllers/KnowledgeManagementPortal/CreateServiceRequestController.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$submission of method AidingApp\\\\Portal\\\\Http\\\\Controllers\\\\KnowledgeManagementPortal\\\\CreateServiceRequestController\\:\\:processSubmissionField\\(\\) expects AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestFormSubmission, Illuminate\\\\Database\\\\Eloquent\\\\Model given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/portal/src/Http/Controllers/KnowledgeManagementPortal/CreateServiceRequestController.php',
];
$ignoreErrors[] = [
	'message' => '#^Unable to resolve the template type TKey in call to function collect$#',
	'identifier' => 'argument.templateType',
	'count' => 3,
	'path' => __DIR__ . '/app-modules/portal/src/Http/Controllers/KnowledgeManagementPortal/CreateServiceRequestController.php',
];
$ignoreErrors[] = [
	'message' => '#^Unable to resolve the template type TValue in call to function collect$#',
	'identifier' => 'argument.templateType',
	'count' => 3,
	'path' => __DIR__ . '/app-modules/portal/src/Http/Controllers/KnowledgeManagementPortal/CreateServiceRequestController.php',
];
$ignoreErrors[] = [
	'message' => '#^Using nullsafe method call on non\\-nullable type AidingApp\\\\ServiceManagement\\\\Enums\\\\ServiceRequestTypeAssignmentTypes\\. Use \\-\\> instead\\.$#',
	'identifier' => 'nullsafe.neverNull',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/portal/src/Http/Controllers/KnowledgeManagementPortal/CreateServiceRequestController.php',
];
$ignoreErrors[] = [
	'message' => '#^Using nullsafe property access on non\\-nullable type AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestType\\. Use \\-\\> instead\\.$#',
	'identifier' => 'nullsafe.neverNull',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/portal/src/Http/Controllers/KnowledgeManagementPortal/CreateServiceRequestController.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$updated_at\\.$#',
	'identifier' => 'property.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/portal/src/Http/Controllers/KnowledgeManagementPortal/GetServiceRequestsController.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Database\\\\Eloquent\\\\Collection\\<int,Illuminate\\\\Database\\\\Eloquent\\\\Model\\>\\:\\:map\\(\\) expects callable\\(Illuminate\\\\Database\\\\Eloquent\\\\Model, int\\)\\: AidingApp\\\\Portal\\\\DataTransferObjects\\\\ServiceRequestData, Closure\\(AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequest\\)\\: AidingApp\\\\Portal\\\\DataTransferObjects\\\\ServiceRequestData given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/portal/src/Http/Controllers/KnowledgeManagementPortal/GetServiceRequestsController.php',
];
$ignoreErrors[] = [
	'message' => '#^Using nullsafe property access "\\?\\-\\>description" on left side of \\?\\? is unnecessary\\. Use \\-\\> instead\\.$#',
	'identifier' => 'nullsafe.neverNull',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/portal/src/Http/Controllers/KnowledgeManagementPortal/GetServiceRequestsController.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany\\:\\:public\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/portal/src/Http/Controllers/KnowledgeManagementPortal/KnowledgeManagementPortalCategoryController.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Database\\\\Eloquent\\\\Collection\\<int,Illuminate\\\\Database\\\\Eloquent\\\\Model\\>\\:\\:map\\(\\) expects callable\\(Illuminate\\\\Database\\\\Eloquent\\\\Model, int\\)\\: AidingApp\\\\Portal\\\\DataTransferObjects\\\\KnowledgeBaseCategoryData, Closure\\(AidingApp\\\\KnowledgeBase\\\\Models\\\\KnowledgeBaseCategory\\)\\: AidingApp\\\\Portal\\\\DataTransferObjects\\\\KnowledgeBaseCategoryData given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/portal/src/Http/Controllers/KnowledgeManagementPortal/KnowledgeManagementPortalCategoryController.php',
];
$ignoreErrors[] = [
	'message' => '#^Negated boolean expression is always false\\.$#',
	'identifier' => 'booleanNot.alwaysFalse',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/portal/src/Http/Controllers/KnowledgeManagementPortal/KnowledgeManagementPortalLogoutController.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property Illuminate\\\\Support\\\\ValidatedInput\\:\\:\\$email\\.$#',
	'identifier' => 'property.notFound',
	'count' => 3,
	'path' => __DIR__ . '/app-modules/portal/src/Http/Controllers/KnowledgeManagementPortal/KnowledgeManagementPortalRequestAuthenticationController.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property Illuminate\\\\Support\\\\ValidatedInput\\:\\:\\$isSpa\\.$#',
	'identifier' => 'property.notFound',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/portal/src/Http/Controllers/KnowledgeManagementPortal/KnowledgeManagementPortalRequestAuthenticationController.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$value of static method Illuminate\\\\Support\\\\Facades\\\\Hash\\:\\:make\\(\\) expects string, int\\<100000, 999999\\> given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/portal/src/Http/Controllers/KnowledgeManagementPortal/KnowledgeManagementPortalRequestAuthenticationController.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property AidingApp\\\\KnowledgeBase\\\\Models\\\\KnowledgeBaseItem\\|Illuminate\\\\Database\\\\Eloquent\\\\Collection\\<int, AidingApp\\\\KnowledgeBase\\\\Models\\\\KnowledgeBaseItem\\>\\:\\:\\$helpful_votes_count\\.$#',
	'identifier' => 'property.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/portal/src/Http/Controllers/KnowledgeManagementPortal/StoreKnowledgeBaseArticleVoteController.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$is_helpful\\.$#',
	'identifier' => 'property.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/portal/src/Http/Controllers/KnowledgeManagementPortal/StoreKnowledgeBaseArticleVoteController.php',
];
$ignoreErrors[] = [
	'message' => '#^If condition is always true\\.$#',
	'identifier' => 'if.alwaysTrue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/portal/src/Http/Controllers/KnowledgeManagementPortal/StoreKnowledgeBaseArticleVoteController.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Portal\\\\Http\\\\Requests\\\\GetServiceRequestUploadUrlRequest\\:\\:rules\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/portal/src/Http/Requests/GetServiceRequestUploadUrlRequest.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Portal\\\\Http\\\\Requests\\\\KnowledgeManagementPortalAuthenticationRequest\\:\\:rules\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/portal/src/Http/Requests/KnowledgeManagementPortalAuthenticationRequest.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Portal\\\\Http\\\\Requests\\\\KnowledgeManagementPortalRegisterRequest\\:\\:rules\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/portal/src/Http/Requests/KnowledgeManagementPortalRegisterRequest.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Portal\\\\Http\\\\Routing\\\\ArticleShowMissingHandler\\:\\:__invoke\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/portal/src/Http/Routing/ArticleShowMissingHandler.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Portal\\\\Http\\\\Routing\\\\CategoryShowMissingHandler\\:\\:__invoke\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/portal/src/Http/Routing/CategoryShowMissingHandler.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Portal\\\\Jobs\\\\PersistServiceRequestUpload\\:\\:middleware\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/portal/src/Jobs/PersistServiceRequestUpload.php',
];
$ignoreErrors[] = [
	'message' => '#^Property AidingApp\\\\Portal\\\\Jobs\\\\PersistServiceRequestUpload\\:\\:\\$deleteWhenMissingModels has no type specified\\.$#',
	'identifier' => 'missingType.property',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/portal/src/Jobs/PersistServiceRequestUpload.php',
];
$ignoreErrors[] = [
	'message' => '#^Class AidingApp\\\\Portal\\\\Models\\\\KnowledgeBaseArticleVote uses generic trait Illuminate\\\\Database\\\\Eloquent\\\\Factories\\\\HasFactory but does not specify its types\\: TFactory$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/portal/src/Models/KnowledgeBaseArticleVote.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Portal\\\\Models\\\\KnowledgeBaseArticleVote\\:\\:knowledgeBaseArticle\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/portal/src/Models/KnowledgeBaseArticleVote.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Portal\\\\Models\\\\KnowledgeBaseArticleVote\\:\\:voter\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/portal/src/Models/KnowledgeBaseArticleVote.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Portal\\\\Models\\\\PortalAuthentication\\:\\:educatable\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/portal/src/Models/PortalAuthentication.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Portal\\\\Models\\\\PortalAuthentication\\:\\:prunable\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder does not specify its types\\: TModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/portal/src/Models/PortalAuthentication.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Portal\\\\Models\\\\PortalGuest\\:\\:knowledgeBaseArticleVotes\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/portal/src/Models/PortalGuest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method getKey\\(\\) on an unknown class AidingApp\\\\Portal\\\\Notifications\\\\Contact\\.$#',
	'identifier' => 'class.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/portal/src/Notifications/AuthenticatePortalNotification.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Portal\\\\Notifications\\\\AuthenticatePortalNotification\\:\\:identifyRecipient\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/portal/src/Notifications/AuthenticatePortalNotification.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var for variable \\$contact contains unknown class AidingApp\\\\Portal\\\\Notifications\\\\Contact\\.$#',
	'identifier' => 'class.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/portal/src/Notifications/AuthenticatePortalNotification.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Portal\\\\Providers\\\\PortalServiceProvider\\:\\:boot\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/portal/src/Providers/PortalServiceProvider.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access property \\$code on object\\|string\\.$#',
	'identifier' => 'property.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/portal/src/Rules/PortalAuthenticateCodeValidation.php',
];
$ignoreErrors[] = [
	'message' => '#^Property AidingApp\\\\Portal\\\\Settings\\\\PortalSettings\\:\\:\\$gdpr_banner_text type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/portal/src/Settings/PortalSettings.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method Spatie\\\\Image\\\\Drivers\\\\ImageDriver\\:\\:performOnCollections\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/portal/src/Settings/SettingsProperties/PortalSettingsProperty.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method can\\(\\) on an unknown class AidingApp\\\\Report\\\\Abstract\\\\User\\.$#',
	'identifier' => 'class.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/report/src/Abstract/EngagementReport.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method hasLicense\\(\\) on an unknown class AidingApp\\\\Report\\\\Abstract\\\\User\\.$#',
	'identifier' => 'class.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/report/src/Abstract/EngagementReport.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var for variable \\$user contains unknown class AidingApp\\\\Report\\\\Abstract\\\\User\\.$#',
	'identifier' => 'class.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/report/src/Abstract/EngagementReport.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method can\\(\\) on an unknown class AidingApp\\\\Report\\\\Filament\\\\Pages\\\\User\\.$#',
	'identifier' => 'class.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/report/src/Filament/Pages/ServiceRequests.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var for variable \\$user contains unknown class AidingApp\\\\Report\\\\Filament\\\\Pages\\\\User\\.$#',
	'identifier' => 'class.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/report/src/Filament/Pages/ServiceRequests.php',
];
$ignoreErrors[] = [
	'message' => '#^Property AidingApp\\\\Report\\\\Filament\\\\Pages\\\\ServiceRequests\\:\\:\\$cacheTag has no type specified\\.$#',
	'identifier' => 'missingType.property',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/report/src/Filament/Pages/ServiceRequests.php',
];
$ignoreErrors[] = [
	'message' => '#^Property AidingApp\\\\Report\\\\Filament\\\\Pages\\\\TaskManagement\\:\\:\\$cacheTag has no type specified\\.$#',
	'identifier' => 'missingType.property',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/report/src/Filament/Pages/TaskManagement.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Report\\\\Filament\\\\Widgets\\\\ChartReportWidget\\:\\:mount\\(\\) has parameter \\$cacheTag with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/ChartReportWidget.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Report\\\\Filament\\\\Widgets\\\\ChartReportWidget\\:\\:refreshWidget\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/ChartReportWidget.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Report\\\\Filament\\\\Widgets\\\\LineChartReportWidget\\:\\:mount\\(\\) has parameter \\$cacheTag with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/LineChartReportWidget.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Report\\\\Filament\\\\Widgets\\\\LineChartReportWidget\\:\\:refreshWidget\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/LineChartReportWidget.php',
];
$ignoreErrors[] = [
	'message' => '#^Anonymous function never returns null so it can be removed from the return type\\.$#',
	'identifier' => 'return.unusedType',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/MostRecentTasksTable.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to function is_null\\(\\) with AidingApp\\\\Contact\\\\Models\\\\Contact will always evaluate to false\\.$#',
	'identifier' => 'function.impossibleType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/MostRecentTasksTable.php',
];
$ignoreErrors[] = [
	'message' => '#^Match expression does not handle remaining value\\: class\\-string\\<AidingApp\\\\Contact\\\\Models\\\\Contact\\>&literal\\-string$#',
	'identifier' => 'match.unhandled',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/MostRecentTasksTable.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Report\\\\Filament\\\\Widgets\\\\MostRecentTasksTable\\:\\:mount\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/MostRecentTasksTable.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Report\\\\Filament\\\\Widgets\\\\MostRecentTasksTable\\:\\:refreshWidget\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/MostRecentTasksTable.php',
];
$ignoreErrors[] = [
	'message' => '#^Ternary operator condition is always true\\.$#',
	'identifier' => 'ternary.alwaysTrue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/MostRecentTasksTable.php',
];
$ignoreErrors[] = [
	'message' => '#^Using nullsafe property access "\\?\\-\\>\\(Expression\\)" on left side of \\?\\? is unnecessary\\. Use \\-\\> instead\\.$#',
	'identifier' => 'nullsafe.neverNull',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/MostRecentTasksTable.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Report\\\\Filament\\\\Widgets\\\\RecentServiceRequestsTable\\:\\:mount\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/RecentServiceRequestsTable.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Report\\\\Filament\\\\Widgets\\\\RecentServiceRequestsTable\\:\\:refreshWidget\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/RecentServiceRequestsTable.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Report\\\\Filament\\\\Widgets\\\\RefreshWidget\\:\\:removeWidgetCache\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/RefreshWidget.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Report\\\\Filament\\\\Widgets\\\\RefreshWidget\\:\\:removeWidgetCache\\(\\) has parameter \\$cacheTag with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/RefreshWidget.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method AidingApp\\\\ServiceManagement\\\\Enums\\\\ColumnColorOptions\\:\\:getRgb\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/ServiceRequestStatusDistributionDonutChart.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Report\\\\Filament\\\\Widgets\\\\ServiceRequestStatusDistributionDonutChart\\:\\:getOptions\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/ServiceRequestStatusDistributionDonutChart.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Report\\\\Filament\\\\Widgets\\\\ServiceRequestsOverTimeLineChart\\:\\:getOptions\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/ServiceRequestsOverTimeLineChart.php',
];
$ignoreErrors[] = [
	'message' => '#^If condition is always true\\.$#',
	'identifier' => 'if.alwaysTrue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/ServiceRequestsStats.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Report\\\\Filament\\\\Widgets\\\\ServiceRequestsStats\\:\\:calculateAllServiceRequestStats\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/ServiceRequestsStats.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Report\\\\Filament\\\\Widgets\\\\ServiceRequestsStats\\:\\:calculateAverageServiceResolutionTime\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/ServiceRequestsStats.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Report\\\\Filament\\\\Widgets\\\\ServiceRequestsStats\\:\\:calculateOpenServiceRequestStats\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/ServiceRequestsStats.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Report\\\\Filament\\\\Widgets\\\\ServiceRequestsStats\\:\\:getFormattedPercentageChangeDetails\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/ServiceRequestsStats.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Report\\\\Filament\\\\Widgets\\\\ServiceRequestsStats\\:\\:wasOpenAtIntervalStart\\(\\) has parameter \\$openStatusIds with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/ServiceRequestsStats.php',
];
$ignoreErrors[] = [
	'message' => '#^Unreachable statement \\- code above always terminates\\.$#',
	'identifier' => 'deadCode.unreachable',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/ServiceRequestsStats.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Report\\\\Filament\\\\Widgets\\\\StatsOverviewReportWidget\\:\\:mount\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/StatsOverviewReportWidget.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Report\\\\Filament\\\\Widgets\\\\StatsOverviewReportWidget\\:\\:refreshWidget\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/StatsOverviewReportWidget.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Report\\\\Filament\\\\Widgets\\\\TaskCumulativeCountLineChart\\:\\:getOptions\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/TaskCumulativeCountLineChart.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Report\\\\Filament\\\\Widgets\\\\TopServiceRequestTypesTable\\:\\:mount\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/TopServiceRequestTypesTable.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Report\\\\Filament\\\\Widgets\\\\TopServiceRequestTypesTable\\:\\:refreshWidget\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/TopServiceRequestTypesTable.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Report\\\\Providers\\\\ReportServiceProvider\\:\\:boot\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/report/src/Providers/ReportServiceProvider.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Faker\\\\Generator\\:\\:word\\(\\) invoked with 1 parameter, 0 required\\.$#',
	'identifier' => 'arguments.count',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/database/factories/IncidentFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Faker\\\\Generator\\:\\:word\\(\\) invoked with 1 parameter, 0 required\\.$#',
	'identifier' => 'arguments.count',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/database/factories/IncidentSeverityFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Faker\\\\Generator\\:\\:word\\(\\) invoked with 1 parameter, 0 required\\.$#',
	'identifier' => 'arguments.count',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/database/factories/IncidentStatusFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Faker\\\\Generator\\:\\:word\\(\\) invoked with 1 parameter, 0 required\\.$#',
	'identifier' => 'arguments.count',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/database/factories/ServiceMonitoringTargetFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Using nullsafe property access "\\?\\-\\>id" on left side of \\?\\? is unnecessary\\. Use \\-\\> instead\\.$#',
	'identifier' => 'nullsafe.neverNull',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/database/factories/ServiceRequestFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Database\\\\Factories\\\\ServiceRequestPriorityFactory\\:\\:high\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Factories\\\\Factory does not specify its types\\: TModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/database/factories/ServiceRequestPriorityFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Database\\\\Factories\\\\ServiceRequestPriorityFactory\\:\\:low\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Factories\\\\Factory does not specify its types\\: TModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/database/factories/ServiceRequestPriorityFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Database\\\\Factories\\\\ServiceRequestPriorityFactory\\:\\:medium\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Factories\\\\Factory does not specify its types\\: TModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/database/factories/ServiceRequestPriorityFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Database\\\\Factories\\\\ServiceRequestStatusFactory\\:\\:closed\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Factories\\\\Factory does not specify its types\\: TModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/database/factories/ServiceRequestStatusFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Database\\\\Factories\\\\ServiceRequestStatusFactory\\:\\:in_progress\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Factories\\\\Factory does not specify its types\\: TModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/database/factories/ServiceRequestStatusFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Database\\\\Factories\\\\ServiceRequestStatusFactory\\:\\:open\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Factories\\\\Factory does not specify its types\\: TModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/database/factories/ServiceRequestStatusFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Database\\\\Factories\\\\ServiceRequestTypeFactory\\:\\:icons\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/database/factories/ServiceRequestTypeFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$column of method Illuminate\\\\Database\\\\Schema\\\\Blueprint\\:\\:dropConstrainedForeignId\\(\\) expects string, array\\<int, string\\> given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/database/migrations/2024_10_11_154630_add_assignment_type_columns_to_service_request_types_table.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Illuminate\\\\Database\\\\Migrations\\\\Migration@anonymous/app\\-modules/service\\-management/database/migrations/2025_02_11_095555_seed_permissions_for_incidents\\.php\\:40\\:\\:\\$guards type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/database/migrations/2025_02_11_095555_seed_permissions_for_incidents.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Illuminate\\\\Database\\\\Migrations\\\\Migration@anonymous/app\\-modules/service\\-management/database/migrations/2025_02_11_095555_seed_permissions_for_incidents\\.php\\:40\\:\\:\\$permissions type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/database/migrations/2025_02_11_095555_seed_permissions_for_incidents.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Illuminate\\\\Database\\\\Migrations\\\\Migration@anonymous/app\\-modules/service\\-management/database/migrations/2025_02_24_165721_seed_permissions_add_incident_updates_permissions\\.php\\:40\\:\\:\\$guards type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/database/migrations/2025_02_24_165721_seed_permissions_add_incident_updates_permissions.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Illuminate\\\\Database\\\\Migrations\\\\Migration@anonymous/app\\-modules/service\\-management/database/migrations/2025_02_24_165721_seed_permissions_add_incident_updates_permissions\\.php\\:40\\:\\:\\$permissions type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/database/migrations/2025_02_24_165721_seed_permissions_add_incident_updates_permissions.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Illuminate\\\\Database\\\\Migrations\\\\Migration@anonymous/app\\-modules/service\\-management/database/migrations/2025_03_18_165045_seed_permissions_for_service_monitorings\\.php\\:40\\:\\:\\$guards type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/database/migrations/2025_03_18_165045_seed_permissions_for_service_monitorings.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Illuminate\\\\Database\\\\Migrations\\\\Migration@anonymous/app\\-modules/service\\-management/database/migrations/2025_03_18_165045_seed_permissions_for_service_monitorings\\.php\\:40\\:\\:\\$permissions type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/database/migrations/2025_03_18_165045_seed_permissions_for_service_monitorings.php',
];
$ignoreErrors[] = [
	'message' => '#^If condition is always false\\.$#',
	'identifier' => 'if.alwaysFalse',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Actions/ChangeRequest/ApproveChangeRequest.php',
];
$ignoreErrors[] = [
	'message' => '#^Missing parameter \\$relatedModel\\|newState \\(BackedEnum\\|Illuminate\\\\Database\\\\Eloquent\\\\Model\\|string\\) in call to method App\\\\Models\\\\States\\\\StateMachine\\:\\:transitionTo\\(\\)\\.$#',
	'identifier' => 'argument.missing',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Actions/ChangeRequest/ApproveChangeRequest.php',
];
$ignoreErrors[] = [
	'message' => '#^Unknown parameter \\$newState in call to method App\\\\Models\\\\States\\\\StateMachine\\:\\:transitionTo\\(\\)\\.$#',
	'identifier' => 'argument.unknown',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Actions/ChangeRequest/ApproveChangeRequest.php',
];
$ignoreErrors[] = [
	'message' => '#^Unknown parameter \\$relatedModel in call to method App\\\\Models\\\\States\\\\StateMachine\\:\\:transitionTo\\(\\)\\.$#',
	'identifier' => 'argument.unknown',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Actions/ChangeRequest/ApproveChangeRequest.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Actions\\\\CreateServiceRequestAction\\:\\:execute\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Actions/CreateServiceRequestAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Using nullsafe method call on non\\-nullable type AidingApp\\\\ServiceManagement\\\\Enums\\\\ServiceRequestTypeAssignmentTypes\\. Use \\-\\> instead\\.$#',
	'identifier' => 'nullsafe.neverNull',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Actions/CreateServiceRequestAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Using nullsafe property access on non\\-nullable type AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestType\\. Use \\-\\> instead\\.$#',
	'identifier' => 'nullsafe.neverNull',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Actions/CreateServiceRequestAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Actions\\\\CreateServiceRequestHistory\\:\\:__construct\\(\\) has parameter \\$changes with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Actions/CreateServiceRequestHistory.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Actions\\\\CreateServiceRequestHistory\\:\\:__construct\\(\\) has parameter \\$original with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Actions/CreateServiceRequestHistory.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property AidingApp\\\\Form\\\\Models\\\\Submissible\\:\\:\\$type\\.$#',
	'identifier' => 'property.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Actions/GenerateServiceRequestFormKitSchema.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Actions\\\\GenerateServiceRequestFormKitSchema\\:\\:__invoke\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Actions/GenerateServiceRequestFormKitSchema.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Actions\\\\ResolveUploadsMediaCollectionForServiceRequest\\:\\:__invoke\\(\\) should return AidingApp\\\\ServiceManagement\\\\Models\\\\MediaCollections\\\\UploadsMediaCollection but returns Spatie\\\\MediaLibrary\\\\MediaCollections\\\\MediaCollection\\|null\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Actions/ResolveUploadsMediaCollectionForServiceRequest.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\DataTransferObjects\\\\ServiceRequestDataObject\\:\\:fromData\\(\\) has parameter \\$data with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/DataTransferObjects/ServiceRequestDataObject.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\DataTransferObjects\\\\ServiceRequestDataObject\\:\\:fromData\\(\\) should return static\\(AidingApp\\\\ServiceManagement\\\\DataTransferObjects\\\\ServiceRequestDataObject\\) but returns AidingApp\\\\ServiceManagement\\\\DataTransferObjects\\\\ServiceRequestDataObject\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/DataTransferObjects/ServiceRequestDataObject.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Enums\\\\ServiceRequestAssignmentStatus\\:\\:getLabel\\(\\) never returns null so it can be removed from the return type\\.$#',
	'identifier' => 'return.unusedType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Enums/ServiceRequestAssignmentStatus.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Enums\\\\ServiceRequestUpdateDirection\\:\\:getLabel\\(\\) never returns null so it can be removed from the return type\\.$#',
	'identifier' => 'return.unusedType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Enums/ServiceRequestUpdateDirection.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Enums\\\\SlaComplianceStatus\\:\\:getColor\\(\\) never returns array\\{50\\: string, 100\\: string, 200\\: string, 300\\: string, 400\\: string, 500\\: string, 600\\: string, 700\\: string, \\.\\.\\.\\} so it can be removed from the return type\\.$#',
	'identifier' => 'return.unusedType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Enums/SlaComplianceStatus.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Enums\\\\SlaComplianceStatus\\:\\:getColor\\(\\) never returns null so it can be removed from the return type\\.$#',
	'identifier' => 'return.unusedType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Enums/SlaComplianceStatus.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Enums\\\\SlaComplianceStatus\\:\\:getIcon\\(\\) never returns null so it can be removed from the return type\\.$#',
	'identifier' => 'return.unusedType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Enums/SlaComplianceStatus.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Enums\\\\SlaComplianceStatus\\:\\:getLabel\\(\\) never returns null so it can be removed from the return type\\.$#',
	'identifier' => 'return.unusedType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Enums/SlaComplianceStatus.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Enums\\\\SystemChangeRequestClassification\\:\\:getLabel\\(\\) never returns null so it can be removed from the return type\\.$#',
	'identifier' => 'return.unusedType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Enums/SystemChangeRequestClassification.php',
];
$ignoreErrors[] = [
	'message' => '#^Match arm comparison between \\$this\\(AidingApp\\\\ServiceManagement\\\\Enums\\\\SystemIncidentStatusClassification\\)&AidingApp\\\\ServiceManagement\\\\Enums\\\\SystemIncidentStatusClassification\\:\\:Resolved and AidingApp\\\\ServiceManagement\\\\Enums\\\\SystemIncidentStatusClassification\\:\\:Resolved is always true\\.$#',
	'identifier' => 'match.alwaysTrue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Enums/SystemIncidentStatusClassification.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Enums\\\\SystemServiceRequestClassification\\:\\:getLabel\\(\\) never returns null so it can be removed from the return type\\.$#',
	'identifier' => 'return.unusedType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Enums/SystemServiceRequestClassification.php',
];
$ignoreErrors[] = [
	'message' => '#^Property AidingApp\\\\ServiceManagement\\\\Exceptions\\\\AttemptedToAssignNonManagerToServiceRequest\\:\\:\\$message has no type specified\\.$#',
	'identifier' => 'missingType.property',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Exceptions/AttemptedToAssignNonManagerToServiceRequest.php',
];
$ignoreErrors[] = [
	'message' => '#^Property AidingApp\\\\ServiceManagement\\\\Exceptions\\\\ServiceRequestNumberExceededReRollsException\\:\\:\\$message has no type specified\\.$#',
	'identifier' => 'missingType.property',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Exceptions/ServiceRequestNumberExceededReRollsException.php',
];
$ignoreErrors[] = [
	'message' => '#^Property AidingApp\\\\ServiceManagement\\\\Exceptions\\\\ServiceRequestNumberUpdateAttemptException\\:\\:\\$message has no type specified\\.$#',
	'identifier' => 'missingType.property',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Exceptions/ServiceRequestNumberUpdateAttemptException.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Builder\\:\\:withTrashed\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ChangeRequestResource/Pages/EditChangeRequest.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var for variable \\$resource contains unresolvable type\\.$#',
	'identifier' => 'varTag.unresolvableType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ChangeRequestResource/Pages/EditChangeRequest.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var with type mixed is not subtype of native type string\\.$#',
	'identifier' => 'varTag.nativeType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ChangeRequestResource/Pages/EditChangeRequest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo\\:\\:withTrashed\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ChangeRequestResource/Pages/ListChangeRequests.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo\\:\\:withTrashed\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 4,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ChangeRequestResource/Pages/ViewChangeRequest.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Filament\\\\Resources\\\\ChangeRequestStatusResource\\:\\:getEloquentQuery\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder does not specify its types\\: TModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ChangeRequestStatusResource.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var for variable \\$resource contains unresolvable type\\.$#',
	'identifier' => 'varTag.unresolvableType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ChangeRequestStatusResource/Pages/EditChangeRequestStatus.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var with type mixed is not subtype of native type string\\.$#',
	'identifier' => 'varTag.nativeType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ChangeRequestStatusResource/Pages/EditChangeRequestStatus.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Filament\\\\Resources\\\\ChangeRequestTypeResource\\:\\:getEloquentQuery\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder does not specify its types\\: TModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ChangeRequestTypeResource.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var for variable \\$resource contains unresolvable type\\.$#',
	'identifier' => 'varTag.unresolvableType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ChangeRequestTypeResource/Pages/EditChangeRequestType.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var with type mixed is not subtype of native type string\\.$#',
	'identifier' => 'varTag.nativeType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ChangeRequestTypeResource/Pages/EditChangeRequestType.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var for variable \\$resource contains unresolvable type\\.$#',
	'identifier' => 'varTag.unresolvableType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/IncidentResource/Pages/EditIncident.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var with type mixed is not subtype of native type string\\.$#',
	'identifier' => 'varTag.nativeType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/IncidentResource/Pages/EditIncident.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$status\\.$#',
	'identifier' => 'property.notFound',
	'count' => 3,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/IncidentResource/Pages/ManageIncidentUpdate.php',
];
$ignoreErrors[] = [
	'message' => '#^Unable to resolve the template type TGroupKey in call to method Illuminate\\\\Support\\\\Collection\\<int,AidingApp\\\\ServiceManagement\\\\Models\\\\IncidentStatus\\>\\:\\:groupBy\\(\\)$#',
	'identifier' => 'argument.templateType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/IncidentResource/Pages/ManageIncidentUpdate.php',
];
$ignoreErrors[] = [
	'message' => '#^Using nullsafe property access on non\\-nullable type Illuminate\\\\Database\\\\Eloquent\\\\Model\\. Use \\-\\> instead\\.$#',
	'identifier' => 'nullsafe.neverNull',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/IncidentResource/Pages/ManageIncidentUpdate.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var for variable \\$resource contains unresolvable type\\.$#',
	'identifier' => 'varTag.unresolvableType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/IncidentSeverityResource/Pages/EditIncidentSeverity.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var with type mixed is not subtype of native type string\\.$#',
	'identifier' => 'varTag.nativeType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/IncidentSeverityResource/Pages/EditIncidentSeverity.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$length of method Filament\\\\Forms\\\\Components\\\\TextInput\\:\\:maxLength\\(\\) expects Closure\\|int\\|null, \'255\' given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/IncidentStatusResource/Pages/CreateIncidentStatus.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var for variable \\$resource contains unresolvable type\\.$#',
	'identifier' => 'varTag.unresolvableType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/IncidentStatusResource/Pages/EditIncidentStatus.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var with type mixed is not subtype of native type string\\.$#',
	'identifier' => 'varTag.nativeType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/IncidentStatusResource/Pages/EditIncidentStatus.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$length of method Filament\\\\Forms\\\\Components\\\\TextInput\\:\\:maxLength\\(\\) expects Closure\\|int\\|null, \'255\' given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/IncidentStatusResource/Pages/EditIncidentStatus.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to property \\$name on an unknown class AidingApp\\\\ServiceManagement\\\\Filament\\\\Resources\\\\IncidentStatusResource\\\\Pages\\\\IncidentStatus\\.$#',
	'identifier' => 'class.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/IncidentStatusResource/Pages/ViewIncidentStatus.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var for variable \\$record contains unknown class AidingApp\\\\ServiceManagement\\\\Filament\\\\Resources\\\\IncidentStatusResource\\\\Pages\\\\IncidentStatus\\.$#',
	'identifier' => 'class.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/IncidentStatusResource/Pages/ViewIncidentStatus.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var with type AidingApp\\\\ServiceManagement\\\\Filament\\\\Resources\\\\IncidentStatusResource\\\\Pages\\\\IncidentStatus is not subtype of native type Illuminate\\\\Database\\\\Eloquent\\\\Model\\.$#',
	'identifier' => 'varTag.nativeType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/IncidentStatusResource/Pages/ViewIncidentStatus.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var for variable \\$resource contains unresolvable type\\.$#',
	'identifier' => 'varTag.unresolvableType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/IncidentUpdateResource/Pages/EditIncidentUpdate.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var with type mixed is not subtype of native type string\\.$#',
	'identifier' => 'varTag.nativeType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/IncidentUpdateResource/Pages/EditIncidentUpdate.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceMonitoringTarget\\:\\:\\$name\\.$#',
	'identifier' => 'property.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceMonitoringResource/Pages/EditServiceMonitoring.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var for variable \\$resource contains unresolvable type\\.$#',
	'identifier' => 'varTag.unresolvableType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceMonitoringResource/Pages/EditServiceMonitoring.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var with type mixed is not subtype of native type string\\.$#',
	'identifier' => 'varTag.nativeType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceMonitoringResource/Pages/EditServiceMonitoring.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceMonitoringTarget\\:\\:\\$name\\.$#',
	'identifier' => 'property.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceMonitoringResource/Pages/ViewServiceMonitoring.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:teams\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceMonitoringResource/Pages/ViewServiceMonitoring.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:users\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceMonitoringResource/Pages/ViewServiceMonitoring.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany\\:\\:submitted\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 3,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestFormResource/Pages/CreateServiceRequestForm.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Filament\\\\Resources\\\\ServiceRequestFormResource\\\\Pages\\\\CreateServiceRequestForm\\:\\:fields\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestFormResource/Pages/CreateServiceRequestForm.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Filament\\\\Resources\\\\ServiceRequestFormResource\\\\Pages\\\\CreateServiceRequestForm\\:\\:saveFieldsFromComponents\\(\\) has parameter \\$components with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestFormResource/Pages/CreateServiceRequestForm.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Filament\\\\Resources\\\\ServiceRequestFormResource\\\\Pages\\\\CreateServiceRequestForm\\:\\:saveFieldsFromComponents\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestFormResource/Pages/CreateServiceRequestForm.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$serviceRequestForm of method AidingApp\\\\ServiceManagement\\\\Filament\\\\Resources\\\\ServiceRequestFormResource\\\\Pages\\\\CreateServiceRequestForm\\:\\:saveFieldsFromComponents\\(\\) expects AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestForm, AidingApp\\\\Form\\\\Models\\\\Submissible given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestFormResource/Pages/CreateServiceRequestForm.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany\\:\\:submitted\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 3,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestFormResource/Pages/EditServiceRequestForm.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Filament\\\\Resources\\\\ServiceRequestFormResource\\\\Pages\\\\EditServiceRequestForm\\:\\:fields\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestFormResource/Pages/EditServiceRequestForm.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Filament\\\\Resources\\\\ServiceRequestFormResource\\\\Pages\\\\EditServiceRequestForm\\:\\:saveFieldsFromComponents\\(\\) has parameter \\$components with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestFormResource/Pages/EditServiceRequestForm.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Filament\\\\Resources\\\\ServiceRequestFormResource\\\\Pages\\\\EditServiceRequestForm\\:\\:saveFieldsFromComponents\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestFormResource/Pages/EditServiceRequestForm.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var for variable \\$resource contains unresolvable type\\.$#',
	'identifier' => 'varTag.unresolvableType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestFormResource/Pages/EditServiceRequestForm.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var with type mixed is not subtype of native type string\\.$#',
	'identifier' => 'varTag.nativeType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestFormResource/Pages/EditServiceRequestForm.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$serviceRequestForm of method AidingApp\\\\ServiceManagement\\\\Filament\\\\Resources\\\\ServiceRequestFormResource\\\\Pages\\\\EditServiceRequestForm\\:\\:saveFieldsFromComponents\\(\\) expects AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestForm, AidingApp\\\\Form\\\\Models\\\\Submissible given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestFormResource/Pages/EditServiceRequestForm.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property Filament\\\\Resources\\\\Pages\\\\Page\\:\\:\\$record\\.$#',
	'identifier' => 'property.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestResource.php',
];
$ignoreErrors[] = [
	'message' => '#^Using nullsafe property access on non\\-nullable type AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequest\\. Use \\-\\> instead\\.$#',
	'identifier' => 'nullsafe.neverNull',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestResource/Actions/AddServiceRequestUpdateBulkAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Using nullsafe property access on non\\-nullable type AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequest\\. Use \\-\\> instead\\.$#',
	'identifier' => 'nullsafe.neverNull',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestResource/Actions/ChangeServiceRequestStatusBulkAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$division\\.$#',
	'identifier' => 'property.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestResource/Pages/CreateServiceRequest.php',
];
$ignoreErrors[] = [
	'message' => '#^Unable to resolve the template type TGroupKey in call to method Illuminate\\\\Support\\\\Collection\\<int,AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestStatus\\>\\:\\:groupBy\\(\\)$#',
	'identifier' => 'argument.templateType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestResource/Pages/CreateServiceRequest.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$priority\\.$#',
	'identifier' => 'property.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestResource/Pages/EditServiceRequest.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var for variable \\$resource contains unresolvable type\\.$#',
	'identifier' => 'varTag.unresolvableType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestResource/Pages/EditServiceRequest.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var with type mixed is not subtype of native type string\\.$#',
	'identifier' => 'varTag.nativeType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestResource/Pages/EditServiceRequest.php',
];
$ignoreErrors[] = [
	'message' => '#^Unable to resolve the template type TGroupKey in call to method Illuminate\\\\Support\\\\Collection\\<int,AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestStatus\\>\\:\\:groupBy\\(\\)$#',
	'identifier' => 'argument.templateType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestResource/Pages/EditServiceRequest.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Filament\\\\Resources\\\\ServiceRequestResource\\\\Pages\\\\ManageAssignments\\:\\:bootServiceRequestLocked\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestResource/Pages/ManageAssignments.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Filament\\\\Resources\\\\ServiceRequestResource\\\\Pages\\\\ManageAssignments\\:\\:managers\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestResource/Pages/ManageAssignments.php',
];
$ignoreErrors[] = [
	'message' => '#^Unsafe call to private method AidingApp\\\\ServiceManagement\\\\Filament\\\\Resources\\\\ServiceRequestResource\\\\Pages\\\\ManageAssignments\\:\\:managers\\(\\) through static\\:\\:\\.$#',
	'identifier' => 'staticClassAccess.privateMethod',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestResource/Pages/ManageAssignments.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Filament\\\\Resources\\\\ServiceRequestResource\\\\Pages\\\\ManageServiceRequestFormSubmission\\:\\:managers\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestResource/Pages/ManageServiceRequestFormSubmission.php',
];
$ignoreErrors[] = [
	'message' => '#^Unsafe call to private method AidingApp\\\\ServiceManagement\\\\Filament\\\\Resources\\\\ServiceRequestResource\\\\Pages\\\\ManageServiceRequestFormSubmission\\:\\:managers\\(\\) through static\\:\\:\\.$#',
	'identifier' => 'staticClassAccess.privateMethod',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestResource/Pages/ManageServiceRequestFormSubmission.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Filament\\\\Resources\\\\ServiceRequestResource\\\\Pages\\\\ManageServiceRequestUpdate\\:\\:bootServiceRequestLocked\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestResource/Pages/ManageServiceRequestUpdate.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Filament\\\\Resources\\\\ServiceRequestResource\\\\Pages\\\\ManageServiceRequestUpdate\\:\\:managers\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestResource/Pages/ManageServiceRequestUpdate.php',
];
$ignoreErrors[] = [
	'message' => '#^Unsafe call to private method AidingApp\\\\ServiceManagement\\\\Filament\\\\Resources\\\\ServiceRequestResource\\\\Pages\\\\ManageServiceRequestUpdate\\:\\:managers\\(\\) through static\\:\\:\\.$#',
	'identifier' => 'staticClassAccess.privateMethod',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestResource/Pages/ManageServiceRequestUpdate.php',
];
$ignoreErrors[] = [
	'message' => '#^Property AidingApp\\\\ServiceManagement\\\\Filament\\\\Resources\\\\ServiceRequestResource\\\\Pages\\\\ServiceRequestTimeline\\:\\:\\$modelsToTimeline type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestResource/Pages/ServiceRequestTimeline.php',
];
$ignoreErrors[] = [
	'message' => '#^Anonymous function never returns null so it can be removed from the return type\\.$#',
	'identifier' => 'return.unusedType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestResource/Pages/ViewServiceRequest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo\\:\\:withTrashed\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestResource/Pages/ViewServiceRequest.php',
];
$ignoreErrors[] = [
	'message' => '#^Match expression does not handle remaining value\\: class\\-string\\<AidingApp\\\\Contact\\\\Models\\\\Contact\\>&literal\\-string$#',
	'identifier' => 'match.unhandled',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestResource/Pages/ViewServiceRequest.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Filament\\\\Resources\\\\ServiceRequestResource\\\\Pages\\\\ViewServiceRequest\\:\\:bootServiceRequestLocked\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestResource/Pages/ViewServiceRequest.php',
];
$ignoreErrors[] = [
	'message' => '#^Using nullsafe property access "\\?\\-\\>type_id" on left side of \\?\\? is unnecessary\\. Use \\-\\> instead\\.$#',
	'identifier' => 'nullsafe.neverNull',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestResource/RelationManagers/AssignedToRelationManager.php',
];
$ignoreErrors[] = [
	'message' => '#^Using nullsafe property access on non\\-nullable type AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestType\\. Use \\-\\> instead\\.$#',
	'identifier' => 'nullsafe.neverNull',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestResource/RelationManagers/AssignedToRelationManager.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property AidingApp\\\\Form\\\\Models\\\\Submissible\\:\\:\\$is_authenticated\\.$#',
	'identifier' => 'property.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestResource/RelationManagers/ServiceRequestFormSubmissionRelationManager.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$status\\.$#',
	'identifier' => 'property.notFound',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestResource/RelationManagers/ServiceRequestUpdatesRelationManager.php',
];
$ignoreErrors[] = [
	'message' => '#^Unable to resolve the template type TGroupKey in call to method Illuminate\\\\Support\\\\Collection\\<int,AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestStatus\\>\\:\\:groupBy\\(\\)$#',
	'identifier' => 'argument.templateType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestResource/RelationManagers/ServiceRequestUpdatesRelationManager.php',
];
$ignoreErrors[] = [
	'message' => '#^Using nullsafe property access on non\\-nullable type Illuminate\\\\Database\\\\Eloquent\\\\Model\\. Use \\-\\> instead\\.$#',
	'identifier' => 'nullsafe.neverNull',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestResource/RelationManagers/ServiceRequestUpdatesRelationManager.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Filament\\\\Resources\\\\ServiceRequestStatusResource\\:\\:getEloquentQuery\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder does not specify its types\\: TModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestStatusResource.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var for variable \\$resource contains unresolvable type\\.$#',
	'identifier' => 'varTag.unresolvableType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestStatusResource/Pages/EditServiceRequestStatus.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var with type mixed is not subtype of native type string\\.$#',
	'identifier' => 'varTag.nativeType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestStatusResource/Pages/EditServiceRequestStatus.php',
];
$ignoreErrors[] = [
	'message' => '#^Dead catch \\- Illuminate\\\\Database\\\\QueryException is never thrown in the try block\\.$#',
	'identifier' => 'catch.neverThrown',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestStatusResource/Pages/ListServiceRequestStatuses.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property Filament\\\\Resources\\\\Pages\\\\Page\\:\\:\\$record\\.$#',
	'identifier' => 'property.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestTypeResource.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Filament\\\\Resources\\\\ServiceRequestTypeResource\\:\\:getEloquentQuery\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder does not specify its types\\: TModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestTypeResource.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$needles of static method Illuminate\\\\Support\\\\Str\\:\\:endsWith\\(\\) expects iterable\\<string\\>\\|string, AidingApp\\\\ServiceManagement\\\\Enums\\\\ServiceRequestEmailTemplateType\\:\\:Assigned\\|AidingApp\\\\ServiceManagement\\\\Enums\\\\ServiceRequestEmailTemplateType\\:\\:Closed\\|AidingApp\\\\ServiceManagement\\\\Enums\\\\ServiceRequestEmailTemplateType\\:\\:Created\\|AidingApp\\\\ServiceManagement\\\\Enums\\\\ServiceRequestEmailTemplateType\\:\\:StatusChange\\|AidingApp\\\\ServiceManagement\\\\Enums\\\\ServiceRequestEmailTemplateType\\:\\:SurveyResponse\\|AidingApp\\\\ServiceManagement\\\\Enums\\\\ServiceRequestEmailTemplateType\\:\\:Update given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestTypeResource.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:priorities\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestTypeResource/Pages/CreateServiceRequestType.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var for variable \\$resource contains unresolvable type\\.$#',
	'identifier' => 'varTag.unresolvableType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestTypeResource/Pages/EditServiceRequestType.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var with type mixed is not subtype of native type string\\.$#',
	'identifier' => 'varTag.nativeType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestTypeResource/Pages/EditServiceRequestType.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var for variable \\$resource contains unresolvable type\\.$#',
	'identifier' => 'varTag.unresolvableType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestTypeResource/Pages/EditServiceRequestTypeAssignments.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var with type mixed is not subtype of native type string\\.$#',
	'identifier' => 'varTag.nativeType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestTypeResource/Pages/EditServiceRequestTypeAssignments.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var for variable \\$resource contains unresolvable type\\.$#',
	'identifier' => 'varTag.unresolvableType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestTypeResource/Pages/EditServiceRequestTypeNotifications.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var with type mixed is not subtype of native type string\\.$#',
	'identifier' => 'varTag.nativeType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestTypeResource/Pages/EditServiceRequestTypeNotifications.php',
];
$ignoreErrors[] = [
	'message' => '#^Unable to resolve the template type TKey in call to function collect$#',
	'identifier' => 'argument.templateType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestTypeResource/Pages/EditServiceRequestTypeNotifications.php',
];
$ignoreErrors[] = [
	'message' => '#^Unable to resolve the template type TValue in call to function collect$#',
	'identifier' => 'argument.templateType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestTypeResource/Pages/EditServiceRequestTypeNotifications.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$id\\.$#',
	'identifier' => 'property.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestTypeResource/Pages/ServiceRequestTypeEmailTemplatePage.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:templates\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestTypeResource/Pages/ServiceRequestTypeEmailTemplatePage.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var for variable \\$resource contains unresolvable type\\.$#',
	'identifier' => 'varTag.unresolvableType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestTypeResource/Pages/ServiceRequestTypeEmailTemplatePage.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var with type mixed is not subtype of native type string\\.$#',
	'identifier' => 'varTag.nativeType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestTypeResource/Pages/ServiceRequestTypeEmailTemplatePage.php',
];
$ignoreErrors[] = [
	'message' => '#^Using nullsafe method call on non\\-nullable type Illuminate\\\\Database\\\\Eloquent\\\\Model\\. Use \\-\\> instead\\.$#',
	'identifier' => 'nullsafe.neverNull',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestTypeResource/Pages/ServiceRequestTypeEmailTemplatePage.php',
];
$ignoreErrors[] = [
	'message' => '#^Using nullsafe property access on non\\-nullable type AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestType\\. Use \\-\\> instead\\.$#',
	'identifier' => 'nullsafe.neverNull',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestTypeResource/Pages/ViewServiceRequestType.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Filament\\\\Resources\\\\ServiceRequestUpdateResource\\\\Components\\\\ServiceRequestAssignmentViewAction\\:\\:serviceRequestAssignmentInfolist\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestUpdateResource/Components/ServiceRequestAssignmentViewAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Filament\\\\Resources\\\\ServiceRequestUpdateResource\\\\Components\\\\ServiceRequestHistoryViewAction\\:\\:serviceRequestHistoryInfolist\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestUpdateResource/Components/ServiceRequestHistoryViewAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Filament\\\\Resources\\\\ServiceRequestUpdateResource\\\\Components\\\\ServiceRequestUpdateViewAction\\:\\:serviceRequestUpdateInfolist\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestUpdateResource/Components/ServiceRequestUpdateViewAction.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var for variable \\$resource contains unresolvable type\\.$#',
	'identifier' => 'varTag.unresolvableType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestUpdateResource/Pages/EditServiceRequestUpdate.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var with type mixed is not subtype of native type string\\.$#',
	'identifier' => 'varTag.nativeType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestUpdateResource/Pages/EditServiceRequestUpdate.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var for variable \\$resource contains unresolvable type\\.$#',
	'identifier' => 'varTag.unresolvableType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/SlaResource/Pages/EditSla.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var with type mixed is not subtype of native type string\\.$#',
	'identifier' => 'varTag.nativeType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/SlaResource/Pages/EditSla.php',
];
$ignoreErrors[] = [
	'message' => '#^Using nullsafe method call on non\\-nullable type AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequest\\. Use \\-\\> instead\\.$#',
	'identifier' => 'nullsafe.neverNull',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Http/Controllers/ServiceRequestFeedbackFormWidgetController.php',
];
$ignoreErrors[] = [
	'message' => '#^Using nullsafe property access on non\\-nullable type AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequest\\. Use \\-\\> instead\\.$#',
	'identifier' => 'nullsafe.neverNull',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/service-management/src/Http/Controllers/ServiceRequestFeedbackFormWidgetController.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$author\\.$#',
	'identifier' => 'property.notFound',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/service-management/src/Http/Controllers/ServiceRequestFormWidgetController.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$submitted_at\\.$#',
	'identifier' => 'property.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Http/Controllers/ServiceRequestFormWidgetController.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:author\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 3,
	'path' => __DIR__ . '/app-modules/service-management/src/Http/Controllers/ServiceRequestFormWidgetController.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:fields\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/service-management/src/Http/Controllers/ServiceRequestFormWidgetController.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:priority\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Http/Controllers/ServiceRequestFormWidgetController.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany\\:\\:requested\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Http/Controllers/ServiceRequestFormWidgetController.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$value of static method Illuminate\\\\Support\\\\Facades\\\\Hash\\:\\:check\\(\\) expects string, int given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Http/Controllers/ServiceRequestFormWidgetController.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$value of static method Illuminate\\\\Support\\\\Facades\\\\Hash\\:\\:make\\(\\) expects string, int\\<100000, 999999\\> given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Http/Controllers/ServiceRequestFormWidgetController.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access property \\$priority on object\\|string\\.$#',
	'identifier' => 'property.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Http/Middleware/ServiceRequestTypeFeedbackIsOn.php',
];
$ignoreErrors[] = [
	'message' => '#^Using nullsafe property access on non\\-nullable type object\\|string\\. Use \\-\\> instead\\.$#',
	'identifier' => 'nullsafe.neverNull',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Http/Middleware/ServiceRequestTypeFeedbackIsOn.php',
];
$ignoreErrors[] = [
	'message' => '#^Property AidingApp\\\\ServiceManagement\\\\Livewire\\\\RenderServiceRequestFeedbackForm\\:\\:\\$data type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Livewire/RenderServiceRequestFeedbackForm.php',
];
$ignoreErrors[] = [
	'message' => '#^Property AidingApp\\\\ServiceManagement\\\\Livewire\\\\RenderServiceRequestForm\\:\\:\\$data type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Livewire/RenderServiceRequestForm.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo\\:\\:withTrashed\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 5,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ChangeRequest.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ChangeRequest\\:\\:accessNestedRelations\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ChangeRequest.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ChangeRequest\\:\\:accessNestedRelations\\(\\) has parameter \\$relations with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ChangeRequest.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ChangeRequest\\:\\:approvals\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ChangeRequest.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ChangeRequest\\:\\:dynamicMethodChain\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ChangeRequest.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ChangeRequest\\:\\:dynamicMethodChain\\(\\) has parameter \\$methods with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ChangeRequest.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ChangeRequest\\:\\:getStateMachineFields\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ChangeRequest.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ChangeRequest\\:\\:newBelongsToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ChangeRequest.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ChangeRequest\\:\\:newMorphToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ChangeRequest.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ChangeRequest\\:\\:responses\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ChangeRequest.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ChangeRequest\\:\\:status\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ChangeRequest.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ChangeRequest\\:\\:type\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ChangeRequest.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ChangeRequest\\:\\:user\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ChangeRequest.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ChangeRequestResponse\\:\\:changeRequest\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ChangeRequestResponse.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ChangeRequestResponse\\:\\:newBelongsToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ChangeRequestResponse.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ChangeRequestResponse\\:\\:newMorphToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ChangeRequestResponse.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ChangeRequestResponse\\:\\:user\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ChangeRequestResponse.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ChangeRequestStatus\\:\\:changeRequests\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ChangeRequestStatus.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ChangeRequestStatus\\:\\:newBelongsToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ChangeRequestStatus.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ChangeRequestStatus\\:\\:newMorphToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ChangeRequestStatus.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ChangeRequestType\\:\\:changeRequests\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ChangeRequestType.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ChangeRequestType\\:\\:newBelongsToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ChangeRequestType.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ChangeRequestType\\:\\:newMorphToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ChangeRequestType.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ChangeRequestType\\:\\:userApprovers\\(\\) should return AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany but returns Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<App\\\\Models\\\\User, \\$this\\(AidingApp\\\\ServiceManagement\\\\Models\\\\ChangeRequestType\\)\\>\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ChangeRequestType.php',
];
$ignoreErrors[] = [
	'message' => '#^Trait AidingApp\\\\ServiceManagement\\\\Models\\\\Concerns\\\\HasRelationBasedStateMachine is used zero times and is not analysed\\.$#',
	'identifier' => 'trait.unused',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/Concerns/HasRelationBasedStateMachine.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\Incident\\:\\:assignedTeam\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/Incident.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\Incident\\:\\:incidentUpdates\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/Incident.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\Incident\\:\\:newBelongsToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/Incident.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\Incident\\:\\:newMorphToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/Incident.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\Incident\\:\\:severity\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/Incident.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\Incident\\:\\:status\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/Incident.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\IncidentSeverity\\:\\:incidents\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/IncidentSeverity.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\IncidentSeverity\\:\\:newBelongsToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/IncidentSeverity.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\IncidentSeverity\\:\\:newMorphToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/IncidentSeverity.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\IncidentSeverity\\:\\:rgbColor\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Casts\\\\Attribute does not specify its types\\: TGet, TSet$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/IncidentSeverity.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\IncidentStatus\\:\\:incidents\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/IncidentStatus.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\IncidentStatus\\:\\:newBelongsToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/IncidentStatus.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\IncidentStatus\\:\\:newMorphToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/IncidentStatus.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\IncidentUpdate\\:\\:incident\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/IncidentUpdate.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\IncidentUpdate\\:\\:newBelongsToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/IncidentUpdate.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\IncidentUpdate\\:\\:newMorphToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/IncidentUpdate.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\MediaCollections\\\\UploadsMediaCollection\\:\\:create\\(\\) has parameter \\$name with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/MediaCollections/UploadsMediaCollection.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\MediaCollections\\\\UploadsMediaCollection\\:\\:create\\(\\) should return static\\(AidingApp\\\\ServiceManagement\\\\Models\\\\MediaCollections\\\\UploadsMediaCollection\\) but returns AidingApp\\\\ServiceManagement\\\\Models\\\\MediaCollections\\\\UploadsMediaCollection\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/MediaCollections/UploadsMediaCollection.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\MediaCollections\\\\UploadsMediaCollection\\:\\:getExtensions\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/MediaCollections/UploadsMediaCollection.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\MediaCollections\\\\UploadsMediaCollection\\:\\:getExtensionsFull\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/MediaCollections/UploadsMediaCollection.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\MediaCollections\\\\UploadsMediaCollection\\:\\:getMimes\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/MediaCollections/UploadsMediaCollection.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\MediaCollections\\\\UploadsMediaCollection\\:\\:mimes\\(\\) has parameter \\$mimes with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/MediaCollections/UploadsMediaCollection.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\Scopes\\\\ClassifiedAs\\:\\:__invoke\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/Scopes/ClassifiedAs.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\Scopes\\\\ClassifiedIn\\:\\:__invoke\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/Scopes/ClassifiedIn.php',
];
$ignoreErrors[] = [
	'message' => '#^Class AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceMonitoringTarget uses generic trait Illuminate\\\\Database\\\\Eloquent\\\\Factories\\\\HasFactory but does not specify its types\\: TFactory$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceMonitoringTarget.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceMonitoringTarget\\:\\:newBelongsToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceMonitoringTarget.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceMonitoringTarget\\:\\:newMorphToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceMonitoringTarget.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceMonitoringTarget\\:\\:teams\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceMonitoringTarget.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceMonitoringTarget\\:\\:users\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceMonitoringTarget.php',
];
$ignoreErrors[] = [
	'message' => '#^Class AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceMonitoringTargetTeam uses generic trait Illuminate\\\\Database\\\\Eloquent\\\\Factories\\\\HasFactory but does not specify its types\\: TFactory$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceMonitoringTargetTeam.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceMonitoringTargetTeam\\:\\:serviceMonitoringTarget\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceMonitoringTargetTeam.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceMonitoringTargetTeam\\:\\:team\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceMonitoringTargetTeam.php',
];
$ignoreErrors[] = [
	'message' => '#^Class AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceMonitoringTargetUser uses generic trait Illuminate\\\\Database\\\\Eloquent\\\\Factories\\\\HasFactory but does not specify its types\\: TFactory$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceMonitoringTargetUser.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceMonitoringTargetUser\\:\\:serviceMonitoringTarget\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceMonitoringTargetUser.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceMonitoringTargetUser\\:\\:user\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceMonitoringTargetUser.php',
];
$ignoreErrors[] = [
	'message' => '#^Class AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequest uses generic trait Illuminate\\\\Database\\\\Eloquent\\\\Factories\\\\HasFactory but does not specify its types\\: TFactory$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequest.php',
];
$ignoreErrors[] = [
	'message' => '#^Generic type Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphTo\\<AidingApp\\\\Contact\\\\Models\\\\Contact\\> in PHPDoc tag @return does not specify all template types of class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphTo\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'generics.lessTypes',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequest.php',
];
$ignoreErrors[] = [
	'message' => '#^Instanceof between AidingApp\\\\Contact\\\\Models\\\\Contact and AidingApp\\\\Notification\\\\Models\\\\Contracts\\\\Subscribable will always evaluate to true\\.$#',
	'identifier' => 'instanceof.alwaysTrue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequest.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequest\\:\\:assignedTo\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasOne does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequest.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequest\\:\\:assignments\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequest.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequest\\:\\:createdBy\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequest.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequest\\:\\:division\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequest.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequest\\:\\:feedback\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasOne does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequest.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequest\\:\\:getLatestResponseSeconds\\(\\) should return int but returns float\\.$#',
	'identifier' => 'return.type',
	'count' => 4,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequest.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequest\\:\\:getResolutionSeconds\\(\\) should return int but returns float\\.$#',
	'identifier' => 'return.type',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequest.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequest\\:\\:histories\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequest.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequest\\:\\:initialAssignment\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasOne does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequest.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequest\\:\\:latestInboundServiceRequestUpdate\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasOne does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequest.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequest\\:\\:latestOutboundServiceRequestUpdate\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasOne does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequest.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequest\\:\\:newBelongsToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequest.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequest\\:\\:newMorphToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequest.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequest\\:\\:priority\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequest.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequest\\:\\:respondent\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphTo\\<AidingApp\\\\Contact\\\\Models\\\\Contact\\> but returns Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphTo\\<Illuminate\\\\Database\\\\Eloquent\\\\Model, \\$this\\(AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequest\\)\\>\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequest.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequest\\:\\:scopeLicensedToEducatable\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequest.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequest\\:\\:scopeLicensedToEducatable\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder does not specify its types\\: TModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequest.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequest\\:\\:scopeOpen\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequest.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequest\\:\\:serviceRequestFormSubmission\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequest.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequest\\:\\:serviceRequestUpdates\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequest.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequest\\:\\:status\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequest.php',
];
$ignoreErrors[] = [
	'message' => '#^Negated boolean expression is always true\\.$#',
	'identifier' => 'booleanNot.alwaysTrue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:assignments\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestAssignment.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestAssignment\\:\\:assignedBy\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestAssignment.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestAssignment\\:\\:getTimelineData\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestAssignment.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestAssignment\\:\\:newBelongsToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestAssignment.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestAssignment\\:\\:newMorphToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestAssignment.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestAssignment\\:\\:serviceRequest\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestAssignment.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestAssignment\\:\\:user\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestAssignment.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestFeedback\\:\\:contact\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestFeedback.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestFeedback\\:\\:serviceRequest\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestFeedback.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestForm\\:\\:fields\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestForm.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestForm\\:\\:steps\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestForm.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestForm\\:\\:submissions\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestForm.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestForm\\:\\:type\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestForm.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestFormAuthentication\\:\\:submissible\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestFormAuthentication.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestFormField\\:\\:step\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestFormField.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestFormField\\:\\:submissible\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestFormField.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestFormStep\\:\\:fields\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestFormStep.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestFormStep\\:\\:submissible\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestFormStep.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Builder\\:\\:notSubmitted\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestFormSubmission.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestFormSubmission\\:\\:fields\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestFormSubmission.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestFormSubmission\\:\\:priority\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestFormSubmission.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestFormSubmission\\:\\:requester\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestFormSubmission.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestFormSubmission\\:\\:scopeCanceled\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestFormSubmission.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestFormSubmission\\:\\:scopeCanceled\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder does not specify its types\\: TModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestFormSubmission.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestFormSubmission\\:\\:scopeNotCanceled\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestFormSubmission.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestFormSubmission\\:\\:scopeNotCanceled\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder does not specify its types\\: TModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestFormSubmission.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestFormSubmission\\:\\:scopeNotSubmitted\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestFormSubmission.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestFormSubmission\\:\\:scopeNotSubmitted\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder does not specify its types\\: TModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestFormSubmission.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestFormSubmission\\:\\:scopeRequested\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestFormSubmission.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestFormSubmission\\:\\:scopeRequested\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder does not specify its types\\: TModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestFormSubmission.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestFormSubmission\\:\\:scopeSubmitted\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestFormSubmission.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestFormSubmission\\:\\:scopeSubmitted\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder does not specify its types\\: TModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestFormSubmission.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestFormSubmission\\:\\:serviceRequest\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasOne does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestFormSubmission.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestFormSubmission\\:\\:submissible\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestFormSubmission.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:histories\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestHistory.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestHistory\\:\\:formatValues\\(\\) has parameter \\$value with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestHistory.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestHistory\\:\\:formatValues\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestHistory.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestHistory\\:\\:getTimelineData\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestHistory.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestHistory\\:\\:getUpdates\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestHistory.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestHistory\\:\\:newValuesFormatted\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Casts\\\\Attribute does not specify its types\\: TGet, TSet$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestHistory.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestHistory\\:\\:originalValuesFormatted\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Casts\\\\Attribute does not specify its types\\: TGet, TSet$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestHistory.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestHistory\\:\\:serviceRequest\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestHistory.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestPriority\\:\\:newBelongsToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestPriority.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestPriority\\:\\:newMorphToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestPriority.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestPriority\\:\\:serviceRequests\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestPriority.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestPriority\\:\\:sla\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestPriority.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestPriority\\:\\:type\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestPriority.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestStatus\\:\\:newBelongsToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestStatus.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestStatus\\:\\:newMorphToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestStatus.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestStatus\\:\\:serviceRequests\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestStatus.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestType\\:\\:assignmentTypeIndividual\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestType.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestType\\:\\:auditors\\(\\) should return AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany but returns Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<AidingApp\\\\Team\\\\Models\\\\Team, \\$this\\(AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestType\\)\\>\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestType.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestType\\:\\:form\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasOne does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestType.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestType\\:\\:lastAssignedUser\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestType.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestType\\:\\:managers\\(\\) should return AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany but returns Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<AidingApp\\\\Team\\\\Models\\\\Team, \\$this\\(AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestType\\)\\>\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestType.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestType\\:\\:newBelongsToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestType.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestType\\:\\:newMorphToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestType.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestType\\:\\:priorities\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestType.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestType\\:\\:serviceRequests\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasManyThrough does not specify its types\\: TRelatedModel, TIntermediateModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestType.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestType\\:\\:templates\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestTypeAuditor uses generic trait Illuminate\\\\Database\\\\Eloquent\\\\Factories\\\\HasFactory but does not specify its types\\: TFactory$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestTypeAuditor.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestTypeAuditor\\:\\:serviceRequestType\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestTypeAuditor.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestTypeAuditor\\:\\:team\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestTypeAuditor.php',
];
$ignoreErrors[] = [
	'message' => '#^Class AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestTypeEmailTemplate uses generic trait Illuminate\\\\Database\\\\Eloquent\\\\Factories\\\\HasFactory but does not specify its types\\: TFactory$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestTypeEmailTemplate.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestTypeEmailTemplate\\:\\:newBelongsToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestTypeEmailTemplate.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestTypeEmailTemplate\\:\\:newMorphToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestTypeEmailTemplate.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestTypeEmailTemplate\\:\\:serviceRequestType\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestTypeEmailTemplate.php',
];
$ignoreErrors[] = [
	'message' => '#^Class AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestTypeManager uses generic trait Illuminate\\\\Database\\\\Eloquent\\\\Factories\\\\HasFactory but does not specify its types\\: TFactory$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestTypeManager.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestTypeManager\\:\\:serviceRequestType\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestTypeManager.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestTypeManager\\:\\:team\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestTypeManager.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:serviceRequestUpdates\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestUpdate.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestUpdate\\:\\:getTimelineData\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestUpdate.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestUpdate\\:\\:newBelongsToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestUpdate.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestUpdate\\:\\:newMorphToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestUpdate.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestUpdate\\:\\:serviceRequest\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestUpdate.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\Sla\\:\\:newBelongsToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/Sla.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\Sla\\:\\:newMorphToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/Sla.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\Sla\\:\\:serviceRequestPriorities\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/Sla.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$division\\.$#',
	'identifier' => 'property.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Notifications/ChangeRequestAwaitingApprovalNotification.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Notifications\\\\ChangeRequestAwaitingApprovalNotification\\:\\:toDatabase\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Notifications/ChangeRequestAwaitingApprovalNotification.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property App\\\\Models\\\\Contracts\\\\Educatable\\:\\:\\$first_name\\.$#',
	'identifier' => 'property.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Notifications/SendClosedServiceFeedbackNotification.php',
];
$ignoreErrors[] = [
	'message' => '#^Match expression does not handle remaining value\\: class\\-string&literal\\-string$#',
	'identifier' => 'match.unhandled',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Notifications/SendClosedServiceFeedbackNotification.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property App\\\\Models\\\\Contracts\\\\Educatable\\:\\:\\$first_name\\.$#',
	'identifier' => 'property.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Notifications/SendEducatableServiceRequestOpenedNotification.php',
];
$ignoreErrors[] = [
	'message' => '#^Match expression does not handle remaining value\\: class\\-string&literal\\-string$#',
	'identifier' => 'match.unhandled',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Notifications/SendEducatableServiceRequestOpenedNotification.php',
];
$ignoreErrors[] = [
	'message' => '#^Match expression does not handle remaining value\\: class\\-string$#',
	'identifier' => 'match.unhandled',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Notifications/ServiceRequestAssigned.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Notifications\\\\ServiceRequestAssigned\\:\\:toDatabase\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Notifications/ServiceRequestAssigned.php',
];
$ignoreErrors[] = [
	'message' => '#^Match expression does not handle remaining value\\: class\\-string$#',
	'identifier' => 'match.unhandled',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Notifications/ServiceRequestClosed.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Notifications\\\\ServiceRequestClosed\\:\\:toDatabase\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Notifications/ServiceRequestClosed.php',
];
$ignoreErrors[] = [
	'message' => '#^Match expression does not handle remaining value\\: class\\-string$#',
	'identifier' => 'match.unhandled',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Notifications/ServiceRequestCreated.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Notifications\\\\ServiceRequestCreated\\:\\:toDatabase\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Notifications/ServiceRequestCreated.php',
];
$ignoreErrors[] = [
	'message' => '#^Match expression does not handle remaining value\\: class\\-string$#',
	'identifier' => 'match.unhandled',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Notifications/ServiceRequestStatusChanged.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Notifications\\\\ServiceRequestStatusChanged\\:\\:toDatabase\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Notifications/ServiceRequestStatusChanged.php',
];
$ignoreErrors[] = [
	'message' => '#^Match expression does not handle remaining value\\: class\\-string$#',
	'identifier' => 'match.unhandled',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Notifications/ServiceRequestUpdated.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Notifications\\\\ServiceRequestUpdated\\:\\:toDatabase\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Notifications/ServiceRequestUpdated.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to function is_null\\(\\) with string will always evaluate to false\\.$#',
	'identifier' => 'function.impossibleType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Observers/ChangeRequestObserver.php',
];
$ignoreErrors[] = [
	'message' => '#^Using nullsafe property access on non\\-nullable type AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestType\\. Use \\-\\> instead\\.$#',
	'identifier' => 'nullsafe.neverNull',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Observers/ServiceRequestAssignmentObserver.php',
];
$ignoreErrors[] = [
	'message' => '#^Property AidingApp\\\\ServiceManagement\\\\Models\\\\IdeHelperServiceRequest\\:\\:\\$status_updated_at \\(Carbon\\\\CarbonImmutable\\|null\\) does not accept Illuminate\\\\Support\\\\Carbon\\.$#',
	'identifier' => 'assign.propertyType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Observers/ServiceRequestObserver.php',
];
$ignoreErrors[] = [
	'message' => '#^Property AidingApp\\\\ServiceManagement\\\\Models\\\\IdeHelperServiceRequest\\:\\:\\$time_to_resolution \\(int\\|null\\) does not accept float\\|null\\.$#',
	'identifier' => 'assign.propertyType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Observers/ServiceRequestObserver.php',
];
$ignoreErrors[] = [
	'message' => '#^Using nullsafe method call on non\\-nullable type AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequest\\. Use \\-\\> instead\\.$#',
	'identifier' => 'nullsafe.neverNull',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Observers/ServiceRequestObserver.php',
];
$ignoreErrors[] = [
	'message' => '#^Using nullsafe property access on non\\-nullable type AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequest\\. Use \\-\\> instead\\.$#',
	'identifier' => 'nullsafe.neverNull',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/service-management/src/Observers/ServiceRequestObserver.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany\\:\\:restore\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Observers/ServiceRequestTypeObserver.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Policies\\\\ChangeRequestPolicy\\:\\:hasAnyLicense\\(\\) has parameter \\$licenses with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Policies/ChangeRequestPolicy.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Policies\\\\ChangeRequestPolicy\\:\\:hasLicenses\\(\\) has parameter \\$licenses with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Policies/ChangeRequestPolicy.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Policies\\\\ChangeRequestStatusPolicy\\:\\:hasAnyLicense\\(\\) has parameter \\$licenses with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Policies/ChangeRequestStatusPolicy.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Policies\\\\ChangeRequestStatusPolicy\\:\\:hasLicenses\\(\\) has parameter \\$licenses with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Policies/ChangeRequestStatusPolicy.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Policies\\\\ChangeRequestTypePolicy\\:\\:hasAnyLicense\\(\\) has parameter \\$licenses with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Policies/ChangeRequestTypePolicy.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Policies\\\\ChangeRequestTypePolicy\\:\\:hasLicenses\\(\\) has parameter \\$licenses with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Policies/ChangeRequestTypePolicy.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Policies\\\\IncidentUpdatePolicy\\:\\:requiredFeatures\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Policies/IncidentUpdatePolicy.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Policies\\\\ServiceRequestAssignmentPolicy\\:\\:requiredFeatures\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Policies/ServiceRequestAssignmentPolicy.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Policies\\\\ServiceRequestFormPolicy\\:\\:hasAnyLicense\\(\\) has parameter \\$licenses with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Policies/ServiceRequestFormPolicy.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Policies\\\\ServiceRequestFormPolicy\\:\\:hasLicenses\\(\\) has parameter \\$licenses with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Policies/ServiceRequestFormPolicy.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Policies\\\\ServiceRequestHistoryPolicy\\:\\:requiredFeatures\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Policies/ServiceRequestHistoryPolicy.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:manageableServiceRequestTypes\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Policies/ServiceRequestPolicy.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Policies\\\\ServiceRequestPolicy\\:\\:requiredFeatures\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Policies/ServiceRequestPolicy.php',
];
$ignoreErrors[] = [
	'message' => '#^Using nullsafe property access on non\\-nullable type AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequest\\. Use \\-\\> instead\\.$#',
	'identifier' => 'nullsafe.neverNull',
	'count' => 7,
	'path' => __DIR__ . '/app-modules/service-management/src/Policies/ServiceRequestPolicy.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Policies\\\\ServiceRequestPriorityPolicy\\:\\:requiredFeatures\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Policies/ServiceRequestPriorityPolicy.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Policies\\\\ServiceRequestStatusPolicy\\:\\:requiredFeatures\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Policies/ServiceRequestStatusPolicy.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Policies\\\\ServiceRequestTypePolicy\\:\\:requiredFeatures\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Policies/ServiceRequestTypePolicy.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Policies\\\\ServiceRequestUpdatePolicy\\:\\:requiredFeatures\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Policies/ServiceRequestUpdatePolicy.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Policies\\\\SlaPolicy\\:\\:requiredFeatures\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Policies/SlaPolicy.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @param for parameter \\$fail with type Illuminate\\\\Translation\\\\PotentiallyTranslatedString is incompatible with native type Closure\\.$#',
	'identifier' => 'parameter.phpDocType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Rules/ManagedServiceRequestType.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to private method whereRelation\\(\\) of parent class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<Illuminate\\\\Database\\\\Eloquent\\\\Model,Illuminate\\\\Database\\\\Eloquent\\\\Model\\>\\.$#',
	'identifier' => 'method.private',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Rules/ServiceRequestTypeAssignmentsIndividualUserMustBeAManager.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Services\\\\ServiceRequestNumber\\\\SqidPlusSixServiceRequestNumberGenerator\\:\\:generateRandomString\\(\\) has parameter \\$length with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Services/ServiceRequestNumber/SqidPlusSixServiceRequestNumberGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Using nullsafe property access on non\\-nullable type AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequest\\. Use \\-\\> instead\\.$#',
	'identifier' => 'nullsafe.neverNull',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Services/ServiceRequestType/IndividualAssigner.php',
];
$ignoreErrors[] = [
	'message' => '#^Using nullsafe property access on non\\-nullable type AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestType\\. Use \\-\\> instead\\.$#',
	'identifier' => 'nullsafe.neverNull',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Services/ServiceRequestType/IndividualAssigner.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to function is_null\\(\\) with AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestType will always evaluate to false\\.$#',
	'identifier' => 'function.impossibleType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Services/ServiceRequestType/RoundRobinAssigner.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to function is_null\\(\\) with AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestType will always evaluate to false\\.$#',
	'identifier' => 'function.impossibleType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Services/ServiceRequestType/WorkloadAssigner.php',
];
$ignoreErrors[] = [
	'message' => '#^Using nullsafe property access "\\?\\-\\>service_request_count" on left side of \\?\\? is unnecessary\\. Use \\-\\> instead\\.$#',
	'identifier' => 'nullsafe.neverNull',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Services/ServiceRequestType/WorkloadAssigner.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method Pest\\\\Expectation\\<Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\|null\\>\\:\\:exists\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 4,
	'path' => __DIR__ . '/app-modules/service-management/tests/Filament/ServiceMonitoring/Pages/CreateServiceMonitoringTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method call\\(\\) on an unknown class static\\.$#',
	'identifier' => 'class.notFound',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/service-management/tests/Filament/ServiceMonitoring/Pages/CreateServiceMonitoringTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceMonitoringTarget\\:\\:\\$description\\.$#',
	'identifier' => 'property.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/tests/Filament/ServiceMonitoring/Pages/EditServiceMonitoringTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceMonitoringTarget\\:\\:\\$domain\\.$#',
	'identifier' => 'property.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/tests/Filament/ServiceMonitoring/Pages/EditServiceMonitoringTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceMonitoringTarget\\:\\:\\$frequency\\.$#',
	'identifier' => 'property.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/tests/Filament/ServiceMonitoring/Pages/EditServiceMonitoringTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceMonitoringTarget\\:\\:\\$name\\.$#',
	'identifier' => 'property.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/tests/Filament/ServiceMonitoring/Pages/EditServiceMonitoringTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method call\\(\\) on an unknown class static\\.$#',
	'identifier' => 'class.notFound',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/service-management/tests/Filament/ServiceMonitoring/Pages/EditServiceMonitoringTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Unable to resolve the template type TAndValue in call to method Pest\\\\Expectation\\<mixed\\>\\:\\:and\\(\\)$#',
	'identifier' => 'argument.templateType',
	'count' => 3,
	'path' => __DIR__ . '/app-modules/service-management/tests/Filament/ServiceMonitoring/Pages/EditServiceMonitoringTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Unable to resolve the template type TValue in call to function expect$#',
	'identifier' => 'argument.templateType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/tests/Filament/ServiceMonitoring/Pages/EditServiceMonitoringTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method assertCanSeeTableRecords\\(\\) on an unknown class static\\.$#',
	'identifier' => 'class.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/tests/Filament/ServiceMonitoring/Pages/ListServiceMonitoringsTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceMonitoringTarget\\:\\:\\$description\\.$#',
	'identifier' => 'property.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/tests/Filament/ServiceMonitoring/Pages/ViewServiceMonitoringTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceMonitoringTarget\\:\\:\\$domain\\.$#',
	'identifier' => 'property.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/tests/Filament/ServiceMonitoring/Pages/ViewServiceMonitoringTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceMonitoringTarget\\:\\:\\$frequency\\.$#',
	'identifier' => 'property.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/tests/Filament/ServiceMonitoring/Pages/ViewServiceMonitoringTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceMonitoringTarget\\:\\:\\$name\\.$#',
	'identifier' => 'property.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/tests/Filament/ServiceMonitoring/Pages/ViewServiceMonitoringTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method call\\(\\) on an unknown class static\\.$#',
	'identifier' => 'class.notFound',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/service-management/tests/Incident/CreateIncidentTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method call\\(\\) on an unknown class static\\.$#',
	'identifier' => 'class.notFound',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/service-management/tests/Incident/EditIncidentTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method assertCanSeeTableRecords\\(\\) on an unknown class static\\.$#',
	'identifier' => 'class.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/tests/Incident/ListIncidentsTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method call\\(\\) on an unknown class static\\.$#',
	'identifier' => 'class.notFound',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/service-management/tests/IncidentSeverity/CreateIncidentSeverityTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method call\\(\\) on an unknown class static\\.$#',
	'identifier' => 'class.notFound',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/service-management/tests/IncidentSeverity/EditIncidentSeverityTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method assertCanSeeTableRecords\\(\\) on an unknown class static\\.$#',
	'identifier' => 'class.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/tests/IncidentSeverity/ListIncidentSeveritiesTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method call\\(\\) on an unknown class static\\.$#',
	'identifier' => 'class.notFound',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/service-management/tests/IncidentStatus/CreateIncidentStatusTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method call\\(\\) on an unknown class static\\.$#',
	'identifier' => 'class.notFound',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/service-management/tests/IncidentStatus/EditIncidentStatusTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method assertCanSeeTableRecords\\(\\) on an unknown class static\\.$#',
	'identifier' => 'class.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/tests/IncidentStatus/ListIncidentStatusesTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to function PHPUnit\\\\Framework\\\\assertEmpty\\(\\) with Illuminate\\\\Database\\\\Eloquent\\\\Collection\\<int, AidingApp\\\\ServiceManagement\\\\Models\\\\IncidentUpdate\\> will always evaluate to false\\.$#',
	'identifier' => 'function.impossibleType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/tests/IncidentUpdate/CreateIncidentUpdateTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method call\\(\\) on an unknown class static\\.$#',
	'identifier' => 'class.notFound',
	'count' => 4,
	'path' => __DIR__ . '/app-modules/service-management/tests/IncidentUpdate/CreateIncidentUpdateTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method call\\(\\) on an unknown class static\\.$#',
	'identifier' => 'class.notFound',
	'count' => 4,
	'path' => __DIR__ . '/app-modules/service-management/tests/IncidentUpdate/EditIncidentUpdateTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method Illuminate\\\\Testing\\\\TestResponse\\:\\:assertCanSeeTableRecords\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/tests/IncidentUpdate/ListIncidentUpdatesTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Expression on left side of \\?\\? is not nullable\\.$#',
	'identifier' => 'nullCoalesce.expr',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/tests/RequestFactories/CreateServiceRequestPriorityRequestFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Using nullsafe property access "\\?\\-\\>id" on left side of \\?\\? is unnecessary\\. Use \\-\\> instead\\.$#',
	'identifier' => 'nullsafe.neverNull',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/tests/RequestFactories/CreateServiceRequestRequestFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Using nullsafe property access "\\?\\-\\>id" on left side of \\?\\? is unnecessary\\. Use \\-\\> instead\\.$#',
	'identifier' => 'nullsafe.neverNull',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/tests/RequestFactories/EditServiceRequestRequestFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Faker\\\\Generator\\:\\:word\\(\\) invoked with 1 parameter, 0 required\\.$#',
	'identifier' => 'arguments.count',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/tests/RequestFactories/IncidentRequestFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Faker\\\\Generator\\:\\:word\\(\\) invoked with 1 parameter, 0 required\\.$#',
	'identifier' => 'arguments.count',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/tests/RequestFactories/IncidentSeverityRequestFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Faker\\\\Generator\\:\\:word\\(\\) invoked with 1 parameter, 0 required\\.$#',
	'identifier' => 'arguments.count',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/tests/RequestFactories/IncidentStatusRequestFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Faker\\\\Generator\\:\\:word\\(\\) invoked with 1 parameter, 0 required\\.$#',
	'identifier' => 'arguments.count',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/tests/RequestFactories/ServiceMonitoringTargetRequestFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method Illuminate\\\\Testing\\\\TestResponse\\:\\:assertCountTableRecords\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/service-management/tests/ServiceRequest/Actions/AddServiceRequestUpdateBulkActionTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method assertFormFieldExists\\(\\) on an unknown class static\\.$#',
	'identifier' => 'class.notFound',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/service-management/tests/ServiceRequest/AssignedToRelationManagerTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property Pest\\\\Expectation\\<Illuminate\\\\Database\\\\Eloquent\\\\Model\\|null\\>\\:\\:\\$user\\.$#',
	'identifier' => 'property.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/tests/ServiceRequest/CreateServiceRequestTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method call\\(\\) on an unknown class static\\.$#',
	'identifier' => 'class.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/tests/ServiceRequest/CreateServiceRequestTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method fillForm\\(\\) on an unknown class static\\.$#',
	'identifier' => 'class.notFound',
	'count' => 10,
	'path' => __DIR__ . '/app-modules/service-management/tests/ServiceRequest/CreateServiceRequestTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method call\\(\\) on an unknown class static\\.$#',
	'identifier' => 'class.notFound',
	'count' => 6,
	'path' => __DIR__ . '/app-modules/service-management/tests/ServiceRequest/EditServiceRequestTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method Illuminate\\\\Testing\\\\TestResponse\\:\\:assertCanSeeTableRecords\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/tests/ServiceRequest/ListServiceRequestsTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method assertCanNotSeeTableRecords\\(\\) on an unknown class static\\.$#',
	'identifier' => 'class.notFound',
	'count' => 4,
	'path' => __DIR__ . '/app-modules/service-management/tests/ServiceRequest/ListServiceRequestsTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method assertTableColumnStateSet\\(\\) on an unknown class static\\.$#',
	'identifier' => 'class.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/tests/ServiceRequest/ListServiceRequestsTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method filterTable\\(\\) on an unknown class static\\.$#',
	'identifier' => 'class.notFound',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/service-management/tests/ServiceRequest/ListServiceRequestsTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method Mockery\\\\ExpectationInterface\\|Mockery\\\\HigherOrderMessage\\:\\:twice\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/tests/ServiceRequest/ServiceRequestNumberTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method call\\(\\) on an unknown class static\\.$#',
	'identifier' => 'class.notFound',
	'count' => 4,
	'path' => __DIR__ . '/app-modules/service-management/tests/ServiceRequestStatus/CreateServiceRequestStatusTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method call\\(\\) on an unknown class static\\.$#',
	'identifier' => 'class.notFound',
	'count' => 3,
	'path' => __DIR__ . '/app-modules/service-management/tests/ServiceRequestStatus/EditServiceRequestStatusTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method fillForm\\(\\) on an unknown class static\\.$#',
	'identifier' => 'class.notFound',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/service-management/tests/ServiceRequestStatus/EditServiceRequestStatusTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method Illuminate\\\\Testing\\\\TestResponse\\:\\:assertCanSeeTableRecords\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/tests/ServiceRequestStatus/ListServiceRequestStatusesTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method assertTableColumnStateSet\\(\\) on an unknown class static\\.$#',
	'identifier' => 'class.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/tests/ServiceRequestStatus/ListServiceRequestStatusesTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to function PHPUnit\\\\Framework\\\\assertEmpty\\(\\) with Illuminate\\\\Database\\\\Eloquent\\\\Collection\\<int, AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestType\\> will always evaluate to false\\.$#',
	'identifier' => 'function.impossibleType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/tests/ServiceRequestType/CreateServiceRequestTypeTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method call\\(\\) on an unknown class static\\.$#',
	'identifier' => 'class.notFound',
	'count' => 4,
	'path' => __DIR__ . '/app-modules/service-management/tests/ServiceRequestType/CreateServiceRequestTypeTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method call\\(\\) on an unknown class static\\.$#',
	'identifier' => 'class.notFound',
	'count' => 4,
	'path' => __DIR__ . '/app-modules/service-management/tests/ServiceRequestType/EditServiceRequestTypeAssignmentsTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method fillForm\\(\\) on an unknown class static\\.$#',
	'identifier' => 'class.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/tests/ServiceRequestType/EditServiceRequestTypeAssignmentsTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method call\\(\\) on an unknown class static\\.$#',
	'identifier' => 'class.notFound',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/service-management/tests/ServiceRequestType/EditServiceRequestTypeTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method fillForm\\(\\) on an unknown class static\\.$#',
	'identifier' => 'class.notFound',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/service-management/tests/ServiceRequestType/EditServiceRequestTypeTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method Illuminate\\\\Testing\\\\TestResponse\\:\\:assertCanSeeTableRecords\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/tests/ServiceRequestType/ListServiceRequestTypeTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method assertTableColumnStateSet\\(\\) on an unknown class static\\.$#',
	'identifier' => 'class.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/tests/ServiceRequestType/ListServiceRequestTypeTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property Pest\\\\Expectation\\<AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestType\\|null\\>\\:\\:\\$auditors\\.$#',
	'identifier' => 'property.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/tests/ServiceRequestType/ServiceRequestTypeAuditorsTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method assertSuccessful\\(\\) on an unknown class static\\.$#',
	'identifier' => 'class.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/tests/ServiceRequestType/ServiceRequestTypeAuditorsTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method call\\(\\) on an unknown class static\\.$#',
	'identifier' => 'class.notFound',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/service-management/tests/ServiceRequestType/ServiceRequestTypeEmailTemplatePageTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property Pest\\\\Expectation\\<AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestType\\|null\\>\\:\\:\\$managers\\.$#',
	'identifier' => 'property.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/tests/ServiceRequestType/ServiceRequestTypeManagersTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method assertSuccessful\\(\\) on an unknown class static\\.$#',
	'identifier' => 'class.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/tests/ServiceRequestType/ServiceRequestTypeManagersTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to function PHPUnit\\\\Framework\\\\assertEmpty\\(\\) with Illuminate\\\\Database\\\\Eloquent\\\\Collection\\<int, AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestUpdate\\> will always evaluate to false\\.$#',
	'identifier' => 'function.impossibleType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/tests/ServiceRequestUpdate/CreateServiceRequestUpdateTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method call\\(\\) on an unknown class static\\.$#',
	'identifier' => 'class.notFound',
	'count' => 4,
	'path' => __DIR__ . '/app-modules/service-management/tests/ServiceRequestUpdate/CreateServiceRequestUpdateTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method call\\(\\) on an unknown class static\\.$#',
	'identifier' => 'class.notFound',
	'count' => 4,
	'path' => __DIR__ . '/app-modules/service-management/tests/ServiceRequestUpdate/EditServiceRequestUpdateTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property AidingApp\\\\Contact\\\\Models\\\\Contact\\:\\:\\$full\\.$#',
	'identifier' => 'property.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/tests/ServiceRequestUpdate/ListServiceRequestUpdatesTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method Illuminate\\\\Testing\\\\TestResponse\\:\\:assertCanSeeTableRecords\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/tests/ServiceRequestUpdate/ListServiceRequestUpdatesTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method assertTableColumnStateSet\\(\\) on an unknown class static\\.$#',
	'identifier' => 'class.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/tests/ServiceRequestUpdate/ListServiceRequestUpdatesTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Using nullsafe property access "\\?\\-\\>id" on left side of \\?\\? is unnecessary\\. Use \\-\\> instead\\.$#',
	'identifier' => 'nullsafe.neverNull',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/task/database/factories/TaskFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Task\\\\Filament\\\\Pages\\\\Components\\\\TaskKanbanViewAction\\:\\:taskInfoList\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/task/src/Filament/Pages/Components/TaskKanbanViewAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Ternary operator condition is always true\\.$#',
	'identifier' => 'ternary.alwaysTrue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/task/src/Filament/Pages/Components/TaskKanbanViewAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Using nullsafe method call on non\\-nullable type Illuminate\\\\Auth\\\\AuthManager\\. Use \\-\\> instead\\.$#',
	'identifier' => 'nullsafe.neverNull',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/task/src/Filament/Pages/Components/TaskKanbanViewAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Using nullsafe property access on non\\-nullable type AidingApp\\\\Contact\\\\Models\\\\Contact\\. Use \\-\\> instead\\.$#',
	'identifier' => 'nullsafe.neverNull',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/task/src/Filament/Pages/Components/TaskKanbanViewAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:getLicenseType\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/task/src/Filament/RelationManagers/BaseTaskRelationManager.php',
];
$ignoreErrors[] = [
	'message' => '#^Ternary operator condition is always true\\.$#',
	'identifier' => 'ternary.alwaysTrue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/task/src/Filament/RelationManagers/BaseTaskRelationManager.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Task\\\\Filament\\\\Resources\\\\TaskResource\\\\Components\\\\TaskViewAction\\:\\:taskInfoList\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/task/src/Filament/Resources/TaskResource/Components/TaskViewAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Ternary operator condition is always true\\.$#',
	'identifier' => 'ternary.alwaysTrue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/task/src/Filament/Resources/TaskResource/Components/TaskViewAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Using nullsafe method call on non\\-nullable type Illuminate\\\\Auth\\\\AuthManager\\. Use \\-\\> instead\\.$#',
	'identifier' => 'nullsafe.neverNull',
	'count' => 3,
	'path' => __DIR__ . '/app-modules/task/src/Filament/Resources/TaskResource/Components/TaskViewAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Using nullsafe property access on non\\-nullable type AidingApp\\\\Contact\\\\Models\\\\Contact\\. Use \\-\\> instead\\.$#',
	'identifier' => 'nullsafe.neverNull',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/task/src/Filament/Resources/TaskResource/Components/TaskViewAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Task\\\\Filament\\\\Resources\\\\TaskResource\\\\Components\\\\TaskViewHeaderAction\\:\\:taskInfoList\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/task/src/Filament/Resources/TaskResource/Components/TaskViewHeaderAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Ternary operator condition is always true\\.$#',
	'identifier' => 'ternary.alwaysTrue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/task/src/Filament/Resources/TaskResource/Components/TaskViewHeaderAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Using nullsafe method call on non\\-nullable type Illuminate\\\\Auth\\\\AuthManager\\. Use \\-\\> instead\\.$#',
	'identifier' => 'nullsafe.neverNull',
	'count' => 3,
	'path' => __DIR__ . '/app-modules/task/src/Filament/Resources/TaskResource/Components/TaskViewHeaderAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Using nullsafe property access on non\\-nullable type AidingApp\\\\Contact\\\\Models\\\\Contact\\. Use \\-\\> instead\\.$#',
	'identifier' => 'nullsafe.neverNull',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/task/src/Filament/Resources/TaskResource/Components/TaskViewHeaderAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Match arm comparison between true and false is always false\\.$#',
	'identifier' => 'match.alwaysFalse',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/task/src/Filament/Resources/TaskResource/Pages/CreateTask.php',
];
$ignoreErrors[] = [
	'message' => '#^Match expression does not handle remaining value\\: true$#',
	'identifier' => 'match.unhandled',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/task/src/Filament/Resources/TaskResource/Pages/CreateTask.php',
];
$ignoreErrors[] = [
	'message' => '#^Match arm comparison between true and false is always false\\.$#',
	'identifier' => 'match.alwaysFalse',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/task/src/Filament/Resources/TaskResource/Pages/EditTask.php',
];
$ignoreErrors[] = [
	'message' => '#^Match expression does not handle remaining value\\: true$#',
	'identifier' => 'match.unhandled',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/task/src/Filament/Resources/TaskResource/Pages/EditTask.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Task\\\\Filament\\\\Resources\\\\TaskResource\\\\Pages\\\\EditTask\\:\\:editFormFields\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/task/src/Filament/Resources/TaskResource/Pages/EditTask.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var for variable \\$resource contains unresolvable type\\.$#',
	'identifier' => 'varTag.unresolvableType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/task/src/Filament/Resources/TaskResource/Pages/EditTask.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var with type mixed is not subtype of native type string\\.$#',
	'identifier' => 'varTag.nativeType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/task/src/Filament/Resources/TaskResource/Pages/EditTask.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:users\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/task/src/Filament/Resources/TaskResource/Pages/ListTasks.php',
];
$ignoreErrors[] = [
	'message' => '#^Ternary operator condition is always true\\.$#',
	'identifier' => 'ternary.alwaysTrue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/task/src/Filament/Resources/TaskResource/Pages/ListTasks.php',
];
$ignoreErrors[] = [
	'message' => '#^Using nullsafe property access on non\\-nullable type AidingApp\\\\Contact\\\\Models\\\\Contact\\. Use \\-\\> instead\\.$#',
	'identifier' => 'nullsafe.neverNull',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/task/src/Filament/Resources/TaskResource/Pages/ListTasks.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Builder\\<Illuminate\\\\Database\\\\Eloquent\\\\Model\\>\\:\\:byNextDue\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/task/src/Filament/Widgets/TasksWidget.php',
];
$ignoreErrors[] = [
	'message' => '#^Match expression does not handle remaining value\\: string$#',
	'identifier' => 'match.unhandled',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/task/src/Imports/TaskImporter.php',
];
$ignoreErrors[] = [
	'message' => '#^Using nullsafe property access "\\?\\-\\>email" on left side of \\?\\? is unnecessary\\. Use \\-\\> instead\\.$#',
	'identifier' => 'nullsafe.neverNull',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/task/src/Imports/TaskImporter.php',
];
$ignoreErrors[] = [
	'message' => '#^Class AidingApp\\\\Task\\\\Models\\\\Task uses generic trait Illuminate\\\\Database\\\\Eloquent\\\\Factories\\\\HasFactory but does not specify its types\\: TFactory$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/task/src/Models/Task.php',
];
$ignoreErrors[] = [
	'message' => '#^Instanceof between AidingApp\\\\Contact\\\\Models\\\\Contact and AidingApp\\\\Notification\\\\Models\\\\Contracts\\\\Subscribable will always evaluate to true\\.$#',
	'identifier' => 'instanceof.alwaysTrue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/task/src/Models/Task.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Task\\\\Models\\\\Task\\:\\:assignedTo\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/task/src/Models/Task.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Task\\\\Models\\\\Task\\:\\:concern\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/task/src/Models/Task.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Task\\\\Models\\\\Task\\:\\:createdBy\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/task/src/Models/Task.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Task\\\\Models\\\\Task\\:\\:getStateMachineFields\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/task/src/Models/Task.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Task\\\\Models\\\\Task\\:\\:newBelongsToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/task/src/Models/Task.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Task\\\\Models\\\\Task\\:\\:newMorphToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/task/src/Models/Task.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Task\\\\Models\\\\Task\\:\\:scopeByNextDue\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/task/src/Models/Task.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Task\\\\Models\\\\Task\\:\\:scopeOpen\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/task/src/Models/Task.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$division\\.$#',
	'identifier' => 'property.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/task/src/Notifications/TaskAssignedToUserNotification.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to function is_null\\(\\) with AidingApp\\\\Contact\\\\Models\\\\Contact will always evaluate to false\\.$#',
	'identifier' => 'function.impossibleType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/task/src/Notifications/TaskAssignedToUserNotification.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Task\\\\Notifications\\\\TaskAssignedToUserNotification\\:\\:toDatabase\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/task/src/Notifications/TaskAssignedToUserNotification.php',
];
$ignoreErrors[] = [
	'message' => '#^Using nullsafe property access on non\\-nullable type AidingApp\\\\Contact\\\\Models\\\\Contact\\. Use \\-\\> instead\\.$#',
	'identifier' => 'nullsafe.neverNull',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/task/src/Notifications/TaskAssignedToUserNotification.php',
];
$ignoreErrors[] = [
	'message' => '#^Using nullsafe property access on non\\-nullable type AidingApp\\\\Contact\\\\Models\\\\Organization\\. Use \\-\\> instead\\.$#',
	'identifier' => 'nullsafe.neverNull',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/task/src/Notifications/TaskAssignedToUserNotification.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Task\\\\Policies\\\\TaskPolicy\\:\\:hasAnyLicense\\(\\) has parameter \\$licenses with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/task/src/Policies/TaskPolicy.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Task\\\\Policies\\\\TaskPolicy\\:\\:hasLicenses\\(\\) has parameter \\$licenses with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/task/src/Policies/TaskPolicy.php',
];
$ignoreErrors[] = [
	'message' => '#^Using nullsafe method call on non\\-nullable type AidingApp\\\\Contact\\\\Models\\\\Contact\\. Use \\-\\> instead\\.$#',
	'identifier' => 'nullsafe.neverNull',
	'count' => 5,
	'path' => __DIR__ . '/app-modules/task/src/Policies/TaskPolicy.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method call\\(\\) on an unknown class static\\.$#',
	'identifier' => 'class.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/task/tests/EditTaskTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method assertSuccessful\\(\\) on an unknown class static\\.$#',
	'identifier' => 'class.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/task/tests/ListTasksTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method assertTableColumnFormattedStateSet\\(\\) on an unknown class static\\.$#',
	'identifier' => 'class.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/task/tests/ListTasksTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method callTableAction\\(\\) on an unknown class static\\.$#',
	'identifier' => 'class.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/task/tests/ListTasksTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$id\\.$#',
	'identifier' => 'property.notFound',
	'count' => 5,
	'path' => __DIR__ . '/app-modules/task/tests/TaskAssignmentTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Unable to resolve the template type TAndValue in call to method Pest\\\\Expectation\\<int\\<0, max\\>\\|null\\>\\:\\:and\\(\\)$#',
	'identifier' => 'argument.templateType',
	'count' => 5,
	'path' => __DIR__ . '/app-modules/task/tests/TaskAssignmentTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method Faker\\\\Generator\\:\\:catchPhrase\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/team/database/factories/TeamFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var for variable \\$resource contains unresolvable type\\.$#',
	'identifier' => 'varTag.unresolvableType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/team/src/Filament/Resources/TeamResource/Pages/EditTeam.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var with type mixed is not subtype of native type string\\.$#',
	'identifier' => 'varTag.nativeType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/team/src/Filament/Resources/TeamResource/Pages/EditTeam.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Team\\\\Models\\\\Team\\:\\:auditableServiceRequestTypes\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/team/src/Models/Team.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Team\\\\Models\\\\Team\\:\\:division\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/team/src/Models/Team.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Team\\\\Models\\\\Team\\:\\:manageableServiceRequestTypes\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/team/src/Models/Team.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Team\\\\Models\\\\Team\\:\\:serviceMonitoringTargets\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/team/src/Models/Team.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Team\\\\Models\\\\Team\\:\\:users\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/team/src/Models/Team.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Team\\\\Models\\\\TeamUser\\:\\:team\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/team/src/Models/TeamUser.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Team\\\\Models\\\\TeamUser\\:\\:user\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/team/src/Models/TeamUser.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Team\\\\Observers\\\\TeamUserObserver\\:\\:creating\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/team/src/Observers/TeamUserObserver.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Team\\\\Providers\\\\TeamServiceProvider\\:\\:boot\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/team/src/Providers/TeamServiceProvider.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method call\\(\\) on an unknown class static\\.$#',
	'identifier' => 'class.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/team/tests/Team/CreateTeamTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method call\\(\\) on an unknown class static\\.$#',
	'identifier' => 'class.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/team/tests/Team/EditTeamTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method Spatie\\\\Image\\\\Drivers\\\\ImageDriver\\:\\:keepOriginalImageFormat\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/theme/src/Settings/SettingsProperties/ThemeSettingsProperty.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Timeline\\\\Actions\\\\AggregatesTimelineRecordsForModel\\:\\:handle\\(\\) has parameter \\$modelsToTimeline with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/timeline/src/Actions/AggregatesTimelineRecordsForModel.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Timeline\\\\Actions\\\\AggregatesTimelineRecordsForModel\\:\\:handle\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/timeline/src/Actions/AggregatesTimelineRecordsForModel.php',
];
$ignoreErrors[] = [
	'message' => '#^Property AidingApp\\\\Timeline\\\\Actions\\\\AggregatesTimelineRecordsForModel\\:\\:\\$aggregateRecords with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/timeline/src/Actions/AggregatesTimelineRecordsForModel.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Timeline\\\\Actions\\\\SyncTimelineData\\:\\:now\\(\\) has parameter \\$modelsToTimeline with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/timeline/src/Actions/SyncTimelineData.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:timeline\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/timeline/src/Filament/Pages/TimelinePage.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Timeline\\\\Filament\\\\Pages\\\\TimelinePage\\:\\:mount\\(\\) has parameter \\$record with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/timeline/src/Filament/Pages/TimelinePage.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Timeline\\\\Filament\\\\Pages\\\\TimelinePage\\:\\:viewRecord\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/timeline/src/Filament/Pages/TimelinePage.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Timeline\\\\Filament\\\\Pages\\\\TimelinePage\\:\\:viewRecord\\(\\) has parameter \\$key with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/timeline/src/Filament/Pages/TimelinePage.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Timeline\\\\Filament\\\\Pages\\\\TimelinePage\\:\\:viewRecord\\(\\) has parameter \\$morphReference with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/timeline/src/Filament/Pages/TimelinePage.php',
];
$ignoreErrors[] = [
	'message' => '#^Property AidingApp\\\\Timeline\\\\Filament\\\\Pages\\\\TimelinePage\\:\\:\\$modelsToTimeline type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/timeline/src/Filament/Pages/TimelinePage.php',
];
$ignoreErrors[] = [
	'message' => '#^Property AidingApp\\\\Timeline\\\\Filament\\\\Pages\\\\TimelinePage\\:\\:\\$timelineRecords with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/timeline/src/Filament/Pages/TimelinePage.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:timeline\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/timeline/src/Listeners/AddRecordToTimeline.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Timeline\\\\Models\\\\Contracts\\\\ProvidesATimeline\\:\\:getTimelineData\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/timeline/src/Models/Contracts/ProvidesATimeline.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Timeline\\\\Models\\\\Timeline\\:\\:scopeForEntity\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/timeline/src/Models/Timeline.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Timeline\\\\Models\\\\Timeline\\:\\:scopeForEntity\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/timeline/src/Models/Timeline.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Timeline\\\\Models\\\\Timeline\\:\\:timelineable\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/timeline/src/Models/Timeline.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Timeline\\\\Providers\\\\TimelineServiceProvider\\:\\:boot\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/timeline/src/Providers/TimelineServiceProvider.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:engagementResponses\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/timeline/tests/Listeners/AddRecordToTimelineTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:engagementResponses\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/timeline/tests/Listeners/RemoveRecordFromTimelineTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Webhook\\\\Actions\\\\StoreInboundWebhook\\:\\:handle\\(\\) has parameter \\$payload with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/webhook/src/Actions/StoreInboundWebhook.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Webhook\\\\DataTransferObjects\\\\SnsMessage\\:\\:fromRequest\\(\\) should return static\\(AidingApp\\\\Webhook\\\\DataTransferObjects\\\\SnsMessage\\) but returns AidingApp\\\\Webhook\\\\DataTransferObjects\\\\SnsMessage\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/webhook/src/DataTransferObjects/SnsMessage.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to function is_array\\(\\) with string will always evaluate to false\\.$#',
	'identifier' => 'function.impossibleType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/webhook/src/Http/Middleware/HandleAwsSnsRequest.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property AidingApp\\\\Webhook\\\\Models\\\\LandlordInboundWebhook\\:\\:\\$event\\.$#',
	'identifier' => 'property.notFound',
	'count' => 4,
	'path' => __DIR__ . '/app-modules/webhook/tests/Unit/Http/Middleware/HandleAwsSnsRequestTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property AidingApp\\\\Webhook\\\\Models\\\\LandlordInboundWebhook\\:\\:\\$payload\\.$#',
	'identifier' => 'property.notFound',
	'count' => 4,
	'path' => __DIR__ . '/app-modules/webhook/tests/Unit/Http/Middleware/HandleAwsSnsRequestTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property AidingApp\\\\Webhook\\\\Models\\\\LandlordInboundWebhook\\:\\:\\$url\\.$#',
	'identifier' => 'property.notFound',
	'count' => 4,
	'path' => __DIR__ . '/app-modules/webhook/tests/Unit/Http/Middleware/HandleAwsSnsRequestTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Unable to resolve the template type TAndValue in call to method Pest\\\\Expectation\\<AidingApp\\\\Webhook\\\\Enums\\\\InboundWebhookSource\\>\\:\\:and\\(\\)$#',
	'identifier' => 'argument.templateType',
	'count' => 4,
	'path' => __DIR__ . '/app-modules/webhook/tests/Unit/Http/Middleware/HandleAwsSnsRequestTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Unable to resolve the template type TAndValue in call to method Pest\\\\Expectation\\<mixed\\>\\:\\:and\\(\\)$#',
	'identifier' => 'argument.templateType',
	'count' => 8,
	'path' => __DIR__ . '/app-modules/webhook/tests/Unit/Http/Middleware/HandleAwsSnsRequestTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method Illuminate\\\\Container\\\\Container\\:\\:getNamespace\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/Actions/Finders/ApplicationModels.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Actions\\\\Finders\\\\ApplicationModels\\:\\:all\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app/Actions/Finders/ApplicationModels.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Actions\\\\Finders\\\\ApplicationModules\\:\\:moduleConfig\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app/Actions/Finders/ApplicationModules.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Actions\\\\GetRecordFromMorphAndKey\\:\\:via\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/app/Actions/GetRecordFromMorphAndKey.php',
];
$ignoreErrors[] = [
	'message' => '#^If condition is always true\\.$#',
	'identifier' => 'if.alwaysTrue',
	'count' => 1,
	'path' => __DIR__ . '/app/Actions/ResolveEducatableFromEmail.php',
];
$ignoreErrors[] = [
	'message' => '#^Unreachable statement \\- code above always terminates\\.$#',
	'identifier' => 'deadCode.unreachable',
	'count' => 1,
	'path' => __DIR__ . '/app/Actions/ResolveEducatableFromEmail.php',
];
$ignoreErrors[] = [
	'message' => '#^Class App\\\\Casts\\\\CurrencyCast implements generic interface Illuminate\\\\Contracts\\\\Database\\\\Eloquent\\\\CastsAttributes but does not specify its types\\: TGet, TSet$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app/Casts/CurrencyCast.php',
];
$ignoreErrors[] = [
	'message' => '#^Class App\\\\Casts\\\\Encrypted implements generic interface Illuminate\\\\Contracts\\\\Database\\\\Eloquent\\\\CastsAttributes but does not specify its types\\: TGet, TSet$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app/Casts/Encrypted.php',
];
$ignoreErrors[] = [
	'message' => '#^Class App\\\\Casts\\\\LandlordEncrypted implements generic interface Illuminate\\\\Contracts\\\\Database\\\\Eloquent\\\\CastsAttributes but does not specify its types\\: TGet, TSet$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app/Casts/LandlordEncrypted.php',
];
$ignoreErrors[] = [
	'message' => '#^Class App\\\\Casts\\\\TenantEncrypted implements generic interface Illuminate\\\\Contracts\\\\Database\\\\Eloquent\\\\CastsAttributes but does not specify its types\\: TGet, TSet$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app/Casts/TenantEncrypted.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Console\\\\Commands\\\\BuildAssets\\:\\:handle\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/app/Console/Commands/BuildAssets.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Console\\\\Commands\\\\GenerateLandlordApiKey\\:\\:setKeyInEnvironmentFile\\(\\) has parameter \\$key with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/app/Console/Commands/GenerateLandlordApiKey.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Console\\\\Commands\\\\GenerateLandlordApiKey\\:\\:writeNewEnvironmentFileWith\\(\\) has parameter \\$key with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/app/Console/Commands/GenerateLandlordApiKey.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Console\\\\Commands\\\\ValidateGraphQL\\:\\:checkModel\\(\\) has parameter \\$fail with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/app/Console/Commands/ValidateGraphQL.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Console\\\\Commands\\\\ValidateGraphQL\\:\\:checkModel\\(\\) has parameter \\$models with generic class Illuminate\\\\Support\\\\Collection but does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app/Console/Commands/ValidateGraphQL.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Console\\\\Commands\\\\ValidateGraphQL\\:\\:checkModel\\(\\) has parameter \\$types with generic class Illuminate\\\\Support\\\\Collection but does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app/Console/Commands/ValidateGraphQL.php',
];
$ignoreErrors[] = [
	'message' => '#^Unable to resolve the template type TKey in call to function collect$#',
	'identifier' => 'argument.templateType',
	'count' => 2,
	'path' => __DIR__ . '/app/Console/Commands/ValidateGraphQL.php',
];
$ignoreErrors[] = [
	'message' => '#^Unable to resolve the template type TValue in call to function collect$#',
	'identifier' => 'argument.templateType',
	'count' => 2,
	'path' => __DIR__ . '/app/Console/Commands/ValidateGraphQL.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\DataTransferObjects\\\\Casts\\\\DataCast\\:\\:get\\(\\) has parameter \\$payload with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/app/DataTransferObjects/Casts/DataCast.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\DataTransferObjects\\\\Casts\\\\DataCast\\:\\:set\\(\\) has parameter \\$payload with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/app/DataTransferObjects/Casts/DataCast.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\DataTransferObjects\\\\Casts\\\\DataCast\\:\\:set\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app/DataTransferObjects/Casts/DataCast.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\DataTransferObjects\\\\LicenseManagement\\\\LicenseLimitsData\\:\\:getResetWindow\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app/DataTransferObjects/LicenseManagement/LicenseLimitsData.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Filament\\\\Forms\\\\Components\\\\EducatableSelect\\:\\:getRelationship\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app/Filament/Forms/Components/EducatableSelect.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Filament\\\\Pages\\\\AmazonS3\\:\\:mutateFormDataBeforeFill\\(\\) has parameter \\$data with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app/Filament/Pages/AmazonS3.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Filament\\\\Pages\\\\AmazonS3\\:\\:mutateFormDataBeforeFill\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app/Filament/Pages/AmazonS3.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Filament\\\\Pages\\\\AmazonS3\\:\\:mutateFormDataBeforeSave\\(\\) has parameter \\$data with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app/Filament/Pages/AmazonS3.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Filament\\\\Pages\\\\AmazonS3\\:\\:mutateFormDataBeforeSave\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app/Filament/Pages/AmazonS3.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Filament\\\\Pages\\\\AmazonS3\\:\\:\\$data type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app/Filament/Pages/AmazonS3.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method Filament\\\\Forms\\\\Components\\\\Checkbox\\:\\:lockedWithoutAnyLicenses\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 4,
	'path' => __DIR__ . '/app/Filament/Pages/EditProfile.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method Filament\\\\Forms\\\\Components\\\\Toggle\\:\\:lockedWithoutAnyLicenses\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 3,
	'path' => __DIR__ . '/app/Filament/Pages/EditProfile.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Filament\\\\Pages\\\\EditProfile\\:\\:getHoursForDays\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app/Filament/Pages/EditProfile.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Filament\\\\Pages\\\\EditProfile\\:\\:\\$data type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app/Filament/Pages/EditProfile.php',
];
$ignoreErrors[] = [
	'message' => '#^Using nullsafe method call on non\\-nullable type Illuminate\\\\Database\\\\Eloquent\\\\Collection\\<int, AidingApp\\\\Team\\\\Models\\\\Team\\>\\. Use \\-\\> instead\\.$#',
	'identifier' => 'nullsafe.neverNull',
	'count' => 2,
	'path' => __DIR__ . '/app/Filament/Pages/EditProfile.php',
];
$ignoreErrors[] = [
	'message' => '#^Left side of && is always true\\.$#',
	'identifier' => 'booleanAnd.leftAlwaysTrue',
	'count' => 1,
	'path' => __DIR__ . '/app/Filament/Pages/ManageLicenseSettings.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Filament\\\\Pages\\\\ProductHealth\\:\\:getNavigationBadge\\(\\) should return string\\|null but returns int\\<1, max\\>\\|null\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app/Filament/Pages/ProductHealth.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var for variable \\$resource contains unresolvable type\\.$#',
	'identifier' => 'varTag.unresolvableType',
	'count' => 1,
	'path' => __DIR__ . '/app/Filament/Resources/NotificationSettingResource/Pages/EditNotificationSetting.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var with type mixed is not subtype of native type string\\.$#',
	'identifier' => 'varTag.nativeType',
	'count' => 1,
	'path' => __DIR__ . '/app/Filament/Resources/NotificationSettingResource/Pages/EditNotificationSetting.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var for variable \\$resource contains unresolvable type\\.$#',
	'identifier' => 'varTag.unresolvableType',
	'count' => 1,
	'path' => __DIR__ . '/app/Filament/Resources/SystemUserResource/Pages/EditSystemUser.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var with type mixed is not subtype of native type string\\.$#',
	'identifier' => 'varTag.nativeType',
	'count' => 1,
	'path' => __DIR__ . '/app/Filament/Resources/SystemUserResource/Pages/EditSystemUser.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var for variable \\$resource contains unresolvable type\\.$#',
	'identifier' => 'varTag.unresolvableType',
	'count' => 1,
	'path' => __DIR__ . '/app/Filament/Resources/TagResource/Pages/EditTag.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var with type mixed is not subtype of native type string\\.$#',
	'identifier' => 'varTag.nativeType',
	'count' => 1,
	'path' => __DIR__ . '/app/Filament/Resources/TagResource/Pages/EditTag.php',
];
$ignoreErrors[] = [
	'message' => '#^Negated boolean expression is always true\\.$#',
	'identifier' => 'booleanNot.alwaysTrue',
	'count' => 1,
	'path' => __DIR__ . '/app/Filament/Resources/UserResource/Actions/AssignLicensesBulkAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),Illuminate\\\\Database\\\\Eloquent\\\\Model\\>\\:\\:each\\(\\) expects callable\\(Illuminate\\\\Database\\\\Eloquent\\\\Model, int\\|string\\)\\: mixed, Closure\\(App\\\\Models\\\\User\\)\\: void given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/Filament/Resources/UserResource/Actions/AssignLicensesBulkAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Filament\\\\Resources\\\\UserResource\\\\Actions\\\\AssignLicensesBulkAction\\:\\:\\$licenseTypes type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app/Filament/Resources/UserResource/Actions/AssignLicensesBulkAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),Illuminate\\\\Database\\\\Eloquent\\\\Model\\>\\:\\:each\\(\\) expects callable\\(Illuminate\\\\Database\\\\Eloquent\\\\Model, int\\|string\\)\\: mixed, Closure\\(App\\\\Models\\\\User\\)\\: void given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/Filament/Resources/UserResource/Actions/AssignRolesBulkAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),Illuminate\\\\Database\\\\Eloquent\\\\Model\\>\\:\\:each\\(\\) expects callable\\(Illuminate\\\\Database\\\\Eloquent\\\\Model, int\\|string\\)\\: mixed, Closure\\(App\\\\Models\\\\User\\)\\: void given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/Filament/Resources/UserResource/Actions/AssignTeamBulkAction.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var for variable \\$resource contains unresolvable type\\.$#',
	'identifier' => 'varTag.unresolvableType',
	'count' => 1,
	'path' => __DIR__ . '/app/Filament/Resources/UserResource/Pages/EditUser.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var with type mixed is not subtype of native type string\\.$#',
	'identifier' => 'varTag.nativeType',
	'count' => 1,
	'path' => __DIR__ . '/app/Filament/Resources/UserResource/Pages/EditUser.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method isSuperAdmin\\(\\) on an unknown class App\\\\Filament\\\\Resources\\\\UserResource\\\\RelationManagers\\\\User\\.$#',
	'identifier' => 'class.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/Filament/Resources/UserResource/RelationManagers/RolesRelationManager.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var for variable \\$user contains unknown class App\\\\Filament\\\\Resources\\\\UserResource\\\\RelationManagers\\\\User\\.$#',
	'identifier' => 'class.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/Filament/Resources/UserResource/RelationManagers/RolesRelationManager.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Filament\\\\Widgets\\\\ContactGrowthChart\\:\\:getOptions\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app/Filament/Widgets/ContactGrowthChart.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequest\\:\\:\\$service_request_id\\.$#',
	'identifier' => 'property.notFound',
	'count' => 2,
	'path' => __DIR__ . '/app/Filament/Widgets/MyServiceRequests.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Builder\\<Illuminate\\\\Database\\\\Eloquent\\\\Model\\>\\:\\:byNextDue\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/Filament/Widgets/MyTasks.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Builder\\|Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\Relation\\:\\:unread\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/Filament/Widgets/Notifications.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Filament\\\\Widgets\\\\Notifications\\:\\:getNotifications\\(\\) return type with generic interface Illuminate\\\\Contracts\\\\Pagination\\\\Paginator does not specify its types\\: TItem$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app/Filament/Widgets/Notifications.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Filament\\\\Widgets\\\\Notifications\\:\\:getNotificationsQuery\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder does not specify its types\\: TModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app/Filament/Widgets/Notifications.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Filament\\\\Widgets\\\\Notifications\\:\\:getNotificationsQuery\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\Relation does not specify its types\\: TRelatedModel, TDeclaringModel, TResult$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app/Filament/Widgets/Notifications.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Filament\\\\Widgets\\\\Notifications\\:\\:getUnreadNotificationsQuery\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder does not specify its types\\: TModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app/Filament/Widgets/Notifications.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Filament\\\\Widgets\\\\Notifications\\:\\:getUnreadNotificationsQuery\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\Relation does not specify its types\\: TRelatedModel, TDeclaringModel, TResult$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app/Filament/Widgets/Notifications.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Filament\\\\Widgets\\\\RecentContactsList\\:\\:getColumns\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app/Filament/Widgets/RecentContactsList.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method AidingApp\\\\ServiceManagement\\\\Enums\\\\ColumnColorOptions\\:\\:getRgb\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/Filament/Widgets/ServiceRequestDonutChart.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Filament\\\\Widgets\\\\ServiceRequestDonutChart\\:\\:getOptions\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app/Filament/Widgets/ServiceRequestDonutChart.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Filament\\\\Widgets\\\\ServiceRequestLineChart\\:\\:getOptions\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app/Filament/Widgets/ServiceRequestLineChart.php',
];
$ignoreErrors[] = [
	'message' => '#^If condition is always true\\.$#',
	'identifier' => 'if.alwaysTrue',
	'count' => 1,
	'path' => __DIR__ . '/app/Filament/Widgets/ServiceRequestWidget.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Filament\\\\Widgets\\\\ServiceRequestWidget\\:\\:calculateOpenServiceRequestStats\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app/Filament/Widgets/ServiceRequestWidget.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Filament\\\\Widgets\\\\ServiceRequestWidget\\:\\:calculateUnassignedServiceRequestStats\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app/Filament/Widgets/ServiceRequestWidget.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Filament\\\\Widgets\\\\ServiceRequestWidget\\:\\:getFormattedPercentageChangeDetails\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app/Filament/Widgets/ServiceRequestWidget.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Filament\\\\Widgets\\\\ServiceRequestWidget\\:\\:getPercentageChange\\(\\) has parameter \\$newValue with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/app/Filament/Widgets/ServiceRequestWidget.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Filament\\\\Widgets\\\\ServiceRequestWidget\\:\\:getPercentageChange\\(\\) has parameter \\$oldValue with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/app/Filament/Widgets/ServiceRequestWidget.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Filament\\\\Widgets\\\\ServiceRequestWidget\\:\\:wasOpenAtIntervalStart\\(\\) has parameter \\$openStatusIds with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/app/Filament/Widgets/ServiceRequestWidget.php',
];
$ignoreErrors[] = [
	'message' => '#^Unreachable statement \\- code above always terminates\\.$#',
	'identifier' => 'deadCode.unreachable',
	'count' => 1,
	'path' => __DIR__ . '/app/Filament/Widgets/ServiceRequestWidget.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property GraphQL\\\\Language\\\\AST\\\\TypeExtensionNode\\:\\:\\$directives\\.$#',
	'identifier' => 'property.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/GraphQL/Directives/FeatureDirective.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property GraphQL\\\\Language\\\\AST\\\\TypeExtensionNode\\:\\:\\$fields\\.$#',
	'identifier' => 'property.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/GraphQL/Directives/FeatureDirective.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property GraphQL\\\\Language\\\\AST\\\\TypeExtensionNode\\:\\:\\$interfaces\\.$#',
	'identifier' => 'property.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/GraphQL/Directives/FeatureDirective.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property GraphQL\\\\Language\\\\AST\\\\TypeExtensionNode\\:\\:\\$name\\.$#',
	'identifier' => 'property.notFound',
	'count' => 2,
	'path' => __DIR__ . '/app/GraphQL/Directives/FeatureDirective.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$typeWithFields of static method Nuwave\\\\Lighthouse\\\\Schema\\\\AST\\\\ASTHelper\\:\\:addDirectiveToFields\\(\\) expects GraphQL\\\\Language\\\\AST\\\\InterfaceTypeDefinitionNode\\|GraphQL\\\\Language\\\\AST\\\\InterfaceTypeExtensionNode\\|GraphQL\\\\Language\\\\AST\\\\ObjectTypeDefinitionNode\\|GraphQL\\\\Language\\\\AST\\\\ObjectTypeExtensionNode, GraphQL\\\\Language\\\\AST\\\\Node&GraphQL\\\\Language\\\\AST\\\\TypeDefinitionNode given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/GraphQL/Directives/FeatureDirective.php',
];
$ignoreErrors[] = [
	'message' => '#^Property GraphQL\\\\Language\\\\AST\\\\FieldDefinitionNode\\:\\:\\$directives \\(GraphQL\\\\Language\\\\AST\\\\NodeList\\<GraphQL\\\\Language\\\\AST\\\\DirectiveNode\\>\\) does not accept GraphQL\\\\Language\\\\AST\\\\NodeList\\<GraphQL\\\\Language\\\\AST\\\\Node\\>\\.$#',
	'identifier' => 'assign.propertyType',
	'count' => 1,
	'path' => __DIR__ . '/app/GraphQL/Directives/FeatureDirective.php',
];
$ignoreErrors[] = [
	'message' => '#^Argument of an invalid type Nuwave\\\\Lighthouse\\\\Execution\\\\Arguments\\\\Argument supplied for foreach, only iterables are supported\\.$#',
	'identifier' => 'foreach.nonIterable',
	'count' => 1,
	'path' => __DIR__ . '/app/GraphQL/Directives/MorphToRelationDirective.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\GraphQL\\\\Directives\\\\MorphToRelationDirective\\:\\:build\\(\\) has parameter \\$builder with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app/GraphQL/Directives/MorphToRelationDirective.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\GraphQL\\\\Directives\\\\MorphToRelationDirective\\:\\:build\\(\\) has parameter \\$relationshipTypes with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app/GraphQL/Directives/MorphToRelationDirective.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\GraphQL\\\\Scalars\\\\UUID\\:\\:getTypeDefinition\\(\\) should return class\\-string\\<\\(GraphQL\\\\Type\\\\Definition\\\\NamedType&GraphQL\\\\Type\\\\Definition\\\\Type\\)\\|UnitEnum\\>\\|\\(GraphQL\\\\Language\\\\AST\\\\Node&GraphQL\\\\Language\\\\AST\\\\TypeDefinitionNode\\)\\|null but returns \\$this\\(App\\\\GraphQL\\\\Scalars\\\\UUID\\)\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app/GraphQL/Scalars/UUID.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\GraphQL\\\\Scalars\\\\UUID\\:\\:validateUUID\\(\\) has parameter \\$value with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/app/GraphQL/Scalars/UUID.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property GraphQL\\\\Language\\\\AST\\\\Node&GraphQL\\\\Language\\\\AST\\\\ValueNode\\:\\:\\$value\\.$#',
	'identifier' => 'property.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/GraphQL/Scalars/Year.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Http\\\\Controllers\\\\SetAzureSsoSettingController\\:\\:__invoke\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/app/Http/Controllers/SetAzureSsoSettingController.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Http\\\\Controllers\\\\ViewPublicUserProfileController\\:\\:formatHours\\(\\) has parameter \\$hours with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app/Http/Controllers/ViewPublicUserProfileController.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Http\\\\Controllers\\\\ViewPublicUserProfileController\\:\\:formatHours\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app/Http/Controllers/ViewPublicUserProfileController.php',
];
$ignoreErrors[] = [
	'message' => '#^Empty array passed to foreach\\.$#',
	'identifier' => 'foreach.emptyArray',
	'count' => 2,
	'path' => __DIR__ . '/app/Http/Middleware/AuthGates.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Http\\\\Middleware\\\\AuthGates\\:\\:handle\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/app/Http/Middleware/AuthGates.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Http\\\\Middleware\\\\AuthGates\\:\\:handle\\(\\) has parameter \\$request with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/app/Http/Middleware/AuthGates.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Http\\\\Middleware\\\\SetPreferredLocale\\:\\:handle\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/app/Http/Middleware/SetPreferredLocale.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Http\\\\Middleware\\\\SetPreferredLocale\\:\\:handle\\(\\) has parameter \\$request with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/app/Http/Middleware/SetPreferredLocale.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Http\\\\Requests\\\\SetAzureSsoSettingRequest\\:\\:rules\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app/Http/Requests/SetAzureSsoSettingRequest.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Http\\\\Requests\\\\Tenants\\\\CreateTenantRequest\\:\\:rules\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app/Http/Requests/Tenants/CreateTenantRequest.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Http\\\\Requests\\\\Tenants\\\\SyncTenantRequest\\:\\:rules\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app/Http/Requests/Tenants/SyncTenantRequest.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Jobs\\\\CreateTenantUser\\:\\:middleware\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app/Jobs/CreateTenantUser.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Jobs\\\\DispatchTenantSetupCompleteEvent\\:\\:middleware\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app/Jobs/DispatchTenantSetupCompleteEvent.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Jobs\\\\MigrateTenantDatabase\\:\\:middleware\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app/Jobs/MigrateTenantDatabase.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Jobs\\\\SeedTenantDatabase\\:\\:middleware\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app/Jobs/SeedTenantDatabase.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:getStateMachine\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/Livewire/TaskKanban.php',
];
$ignoreErrors[] = [
	'message' => '#^Match arm comparison between true and false is always false\\.$#',
	'identifier' => 'match.alwaysFalse',
	'count' => 2,
	'path' => __DIR__ . '/app/Livewire/TaskKanban.php',
];
$ignoreErrors[] = [
	'message' => '#^Match expression does not handle remaining value\\: true$#',
	'identifier' => 'match.unhandled',
	'count' => 1,
	'path' => __DIR__ . '/app/Livewire/TaskKanban.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Livewire\\\\TaskKanban\\:\\:editFormFields\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app/Livewire/TaskKanban.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Livewire\\\\TaskKanban\\:\\:getTasks\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app/Livewire/TaskKanban.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Livewire\\\\TaskKanban\\:\\:render\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/app/Livewire/TaskKanban.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Livewire\\\\TaskKanban\\:\\:viewAction\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/app/Livewire/TaskKanban.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Livewire\\\\TaskKanban\\:\\:viewTask\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/app/Livewire/TaskKanban.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Livewire\\\\TaskKanban\\:\\:\\$statuses type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app/Livewire/TaskKanban.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Models\\\\Authenticatable\\:\\:canOrElse\\(\\) has parameter \\$abilities with no value type specified in iterable type iterable\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/Authenticatable.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Models\\\\Authenticatable\\:\\:roles\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/Authenticatable.php',
];
$ignoreErrors[] = [
	'message' => '#^Unable to resolve the template type TRelatedModel in call to method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:morphToMany\\(\\)$#',
	'identifier' => 'argument.templateType',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/Authenticatable.php',
];
$ignoreErrors[] = [
	'message' => '#^Class App\\\\Models\\\\BaseModel uses generic trait Illuminate\\\\Database\\\\Eloquent\\\\Factories\\\\HasFactory but does not specify its types\\: TFactory$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/BaseModel.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Models\\\\Contracts\\\\HasTags\\:\\:tags\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/Contracts/HasTags.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Models\\\\NotificationSetting\\:\\:divisions\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/NotificationSetting.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Models\\\\NotificationSetting\\:\\:settings\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/NotificationSetting.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Models\\\\NotificationSettingPivot\\:\\:relatedTo\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/NotificationSettingPivot.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Models\\\\NotificationSettingPivot\\:\\:setting\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/NotificationSettingPivot.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Models\\\\Pronouns\\:\\:newBelongsToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/Pronouns.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Models\\\\Pronouns\\:\\:newMorphToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/Pronouns.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Models\\\\Scopes\\\\EducatableSearch\\:\\:__invoke\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/Scopes/EducatableSearch.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Models\\\\Scopes\\\\EducatableSort\\:\\:__invoke\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/Scopes/EducatableSort.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Models\\\\Scopes\\\\HasLicense\\:\\:__invoke\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/Scopes/HasLicense.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Models\\\\Scopes\\\\HasLicense\\:\\:__invoke\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\Relation but does not specify its types\\: TRelatedModel, TDeclaringModel, TResult$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/Scopes/HasLicense.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Models\\\\Scopes\\\\LicensedToEducatable\\:\\:__invoke\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/Scopes/LicensedToEducatable.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Models\\\\Scopes\\\\SearchBy\\:\\:__invoke\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/Scopes/SearchBy.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Models\\\\Scopes\\\\SetupIsComplete\\:\\:__invoke\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/Scopes/SetupIsComplete.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Models\\\\Scopes\\\\TagsForClass\\:\\:__invoke\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/Scopes/TagsForClass.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Models\\\\Scopes\\\\TagsForType\\:\\:__invoke\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/Scopes/TagsForType.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Models\\\\States\\\\StateMachine\\:\\:accessNestedRelations\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/States/StateMachine.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Models\\\\States\\\\StateMachine\\:\\:accessNestedRelations\\(\\) has parameter \\$relations with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/States/StateMachine.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Models\\\\States\\\\StateMachine\\:\\:checkValidEnum\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/States/StateMachine.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Models\\\\States\\\\StateMachine\\:\\:dynamicMethodChain\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/States/StateMachine.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Models\\\\States\\\\StateMachine\\:\\:dynamicMethodChain\\(\\) has parameter \\$methods with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/States/StateMachine.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Models\\\\States\\\\StateMachine\\:\\:getAllStates\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/States/StateMachine.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Models\\\\States\\\\StateMachine\\:\\:getStateTransitions\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/States/StateMachine.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Models\\\\States\\\\StateMachine\\:\\:transitionTo\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/States/StateMachine.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Models\\\\States\\\\StateMachine\\:\\:transitionTo\\(\\) has parameter \\$additionalData with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/States/StateMachine.php',
];
$ignoreErrors[] = [
	'message' => '#^Class App\\\\Models\\\\SystemUser uses generic trait Illuminate\\\\Database\\\\Eloquent\\\\Factories\\\\HasFactory but does not specify its types\\: TFactory$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/SystemUser.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Models\\\\SystemUser\\:\\:newBelongsToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/SystemUser.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Models\\\\SystemUser\\:\\:newMorphToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/SystemUser.php',
];
$ignoreErrors[] = [
	'message' => '#^Class App\\\\Models\\\\User uses generic trait Illuminate\\\\Database\\\\Eloquent\\\\Factories\\\\HasFactory but does not specify its types\\: TFactory$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/User.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Models\\\\User\\:\\:allowedOperators\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/User.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Models\\\\User\\:\\:assignTeam\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/User.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Models\\\\User\\:\\:assignTeam\\(\\) has parameter \\$teamId with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/User.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Models\\\\User\\:\\:assignedTasks\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/User.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Models\\\\User\\:\\:changeRequestResponses\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/User.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Models\\\\User\\:\\:changeRequestTypes\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/User.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Models\\\\User\\:\\:changeRequests\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/User.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Models\\\\User\\:\\:contactAlerts\\(\\) return type with generic class Staudenmeir\\\\EloquentHasManyDeep\\\\HasManyDeep does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/User.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Models\\\\User\\:\\:contactSubscriptions\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/User.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Models\\\\User\\:\\:conversations\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/User.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Models\\\\User\\:\\:engagementBatches\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/User.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Models\\\\User\\:\\:engagements\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/User.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Models\\\\User\\:\\:licenses\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/User.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Models\\\\User\\:\\:newBelongsToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/User.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Models\\\\User\\:\\:newMorphToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/User.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Models\\\\User\\:\\:orderableColumns\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/User.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Models\\\\User\\:\\:permissionsFromRoles\\(\\) return type with generic class Staudenmeir\\\\EloquentHasManyDeep\\\\HasManyDeep does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/User.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Models\\\\User\\:\\:processGlobalSearch\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/User.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Models\\\\User\\:\\:processGlobalSearch\\(\\) has parameter \\$data with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/User.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Models\\\\User\\:\\:processQuery\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/User.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Models\\\\User\\:\\:processQuery\\(\\) has parameter \\$data with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/User.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Models\\\\User\\:\\:processQuery\\(\\) has parameter \\$query with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/User.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Models\\\\User\\:\\:pronouns\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/User.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Models\\\\User\\:\\:scopeAdvancedFilter\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/User.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Models\\\\User\\:\\:scopeAdvancedFilter\\(\\) has parameter \\$data with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/User.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Models\\\\User\\:\\:scopeAdvancedFilter\\(\\) has parameter \\$query with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/User.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Models\\\\User\\:\\:serviceMonitoringTargets\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/User.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Models\\\\User\\:\\:serviceRequestAssignments\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/User.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Models\\\\User\\:\\:serviceRequestTypeIndividualAssignment\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/User.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Models\\\\User\\:\\:serviceRequests\\(\\) return type with generic class Staudenmeir\\\\EloquentHasManyDeep\\\\HasManyDeep does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/User.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Models\\\\User\\:\\:subscriptions\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/User.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Models\\\\User\\:\\:teams\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/User.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Models\\\\User\\:\\:whiteListColumns\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/User.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Models\\\\User\\:\\:\\$filterable has no type specified\\.$#',
	'identifier' => 'missingType.property',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/User.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Models\\\\User\\:\\:\\$orderable has no type specified\\.$#',
	'identifier' => 'missingType.property',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/User.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Multitenancy\\\\Exceptions\\\\TenantAppKeyIsNull\\:\\:\\$message has no type specified\\.$#',
	'identifier' => 'missingType.property',
	'count' => 1,
	'path' => __DIR__ . '/app/Multitenancy/Exceptions/TenantAppKeyIsNull.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Multitenancy\\\\Exceptions\\\\UnableToResolveTenantForEncryptionKey\\:\\:\\$message has no type specified\\.$#',
	'identifier' => 'missingType.property',
	'count' => 1,
	'path' => __DIR__ . '/app/Multitenancy/Exceptions/UnableToResolveTenantForEncryptionKey.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Multitenancy\\\\Http\\\\Middleware\\\\NeedsTenant\\:\\:handleInvalidRequest\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/app/Multitenancy/Http/Middleware/NeedsTenant.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property Spatie\\\\Multitenancy\\\\Models\\\\Tenant\\:\\:\\$name\\.$#',
	'identifier' => 'property.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/Multitenancy/Listeners/SetSentryTenantTag.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Multitenancy\\\\Tasks\\\\PrefixCacheTask\\:\\:setCachePrefix\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/app/Multitenancy/Tasks/PrefixCacheTask.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property Spatie\\\\Multitenancy\\\\Models\\\\Tenant\\:\\:\\$key\\.$#',
	'identifier' => 'property.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/Multitenancy/Tasks/SwitchAppKey.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property Spatie\\\\Multitenancy\\\\Models\\\\Tenant\\:\\:\\$domain\\.$#',
	'identifier' => 'property.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/Multitenancy/Tasks/SwitchAppUrl.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Multitenancy\\\\Tasks\\\\SwitchAppUrl\\:\\:setAppUrl\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/app/Multitenancy/Tasks/SwitchAppUrl.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property Spatie\\\\Multitenancy\\\\Models\\\\Tenant\\:\\:\\$config\\.$#',
	'identifier' => 'property.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/Multitenancy/Tasks/SwitchMailTask.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property Spatie\\\\Multitenancy\\\\Models\\\\Tenant\\:\\:\\$domain\\.$#',
	'identifier' => 'property.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/Multitenancy/Tasks/SwitchMailTask.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to property \\$fromName on an unknown class App\\\\Multitenancy\\\\Tasks\\\\TenantMailConfig\\.$#',
	'identifier' => 'class.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/Multitenancy/Tasks/SwitchMailTask.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to property \\$mailer on an unknown class App\\\\Multitenancy\\\\Tasks\\\\TenantMailConfig\\.$#',
	'identifier' => 'class.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/Multitenancy/Tasks/SwitchMailTask.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to property \\$mailers on an unknown class App\\\\Multitenancy\\\\Tasks\\\\TenantMailConfig\\.$#',
	'identifier' => 'class.notFound',
	'count' => 7,
	'path' => __DIR__ . '/app/Multitenancy/Tasks/SwitchMailTask.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var for variable \\$config contains unknown class App\\\\Multitenancy\\\\Tasks\\\\TenantMailConfig\\.$#',
	'identifier' => 'class.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/Multitenancy/Tasks/SwitchMailTask.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property Spatie\\\\Multitenancy\\\\Models\\\\Tenant\\:\\:\\$config\\.$#',
	'identifier' => 'property.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/Multitenancy/Tasks/SwitchS3FilesystemTask.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property Spatie\\\\Multitenancy\\\\Models\\\\Tenant\\:\\:\\$config\\.$#',
	'identifier' => 'property.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/Multitenancy/Tasks/SwitchS3PublicFilesystemTask.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property Spatie\\\\Multitenancy\\\\Models\\\\Tenant\\:\\:\\$domain\\.$#',
	'identifier' => 'property.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/Multitenancy/Tasks/SwitchSessionDriver.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property Spatie\\\\Multitenancy\\\\Models\\\\Tenant\\:\\:\\$config\\.$#',
	'identifier' => 'property.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/Multitenancy/Tasks/SwitchTenantDatabasesTask.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$division\\.$#',
	'identifier' => 'property.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/Notifications/SetPasswordNotification.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method Illuminate\\\\Foundation\\\\Auth\\\\User\\:\\:isSuperAdmin\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/Providers/AuthServiceProvider.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var with type Filament\\\\Forms\\\\Components\\\\Checkbox is not subtype of native type \\$this\\(App\\\\Providers\\\\FilamentServiceProvider\\)\\.$#',
	'identifier' => 'varTag.nativeType',
	'count' => 1,
	'path' => __DIR__ . '/app/Providers/FilamentServiceProvider.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @var with type Filament\\\\Forms\\\\Components\\\\Toggle is not subtype of native type \\$this\\(App\\\\Providers\\\\FilamentServiceProvider\\)\\.$#',
	'identifier' => 'varTag.nativeType',
	'count' => 1,
	'path' => __DIR__ . '/app/Providers/FilamentServiceProvider.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Providers\\\\MultiConnectionParallelTestingServiceProvider\\:\\:\\$parallelConnections type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app/Providers/MultiConnectionParallelTestingServiceProvider.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Providers\\\\RouteServiceProvider\\:\\:configureRateLimiting\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/app/Providers/RouteServiceProvider.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Rules\\\\EmailNotInUseOrSoftDeleted\\:\\:__construct\\(\\) has parameter \\$currentUserId with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/app/Rules/EmailNotInUseOrSoftDeleted.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Rules\\\\EmailNotInUseOrSoftDeleted\\:\\:\\$currentUserId has no type specified\\.$#',
	'identifier' => 'missingType.property',
	'count' => 1,
	'path' => __DIR__ . '/app/Rules/EmailNotInUseOrSoftDeleted.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Rules\\\\EmailNotInUseOrSoftDeleted\\:\\:\\$message has no type specified\\.$#',
	'identifier' => 'missingType.property',
	'count' => 1,
	'path' => __DIR__ . '/app/Rules/EmailNotInUseOrSoftDeleted.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Settings\\\\LicenseSettings\\:\\:encrypted\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app/Settings/LicenseSettings.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Settings\\\\OlympusSettings\\:\\:encrypted\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app/Settings/OlympusSettings.php',
];
$ignoreErrors[] = [
	'message' => '#^Instanceof between Closure and Closure will always evaluate to true\\.$#',
	'identifier' => 'instanceof.alwaysTrue',
	'count' => 1,
	'path' => __DIR__ . '/app/Support/BulkProcessingMachine.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Support\\\\BulkProcessingMachine\\:\\:make\\(\\) has parameter \\$records with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app/Support/BulkProcessingMachine.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Support\\\\BulkProcessingMachine\\:\\:records\\(\\) has parameter \\$records with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app/Support/BulkProcessingMachine.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Support\\\\FilterQueryBuilder\\:\\:apply\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/app/Support/FilterQueryBuilder.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Support\\\\FilterQueryBuilder\\:\\:apply\\(\\) has parameter \\$data with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/app/Support/FilterQueryBuilder.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Support\\\\FilterQueryBuilder\\:\\:apply\\(\\) has parameter \\$query with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/app/Support/FilterQueryBuilder.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Support\\\\FilterQueryBuilder\\:\\:contains\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/app/Support/FilterQueryBuilder.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Support\\\\FilterQueryBuilder\\:\\:contains\\(\\) has parameter \\$filter with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/app/Support/FilterQueryBuilder.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Support\\\\FilterQueryBuilder\\:\\:contains\\(\\) has parameter \\$query with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/app/Support/FilterQueryBuilder.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Support\\\\FilterQueryBuilder\\:\\:isNestedColumn\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/app/Support/FilterQueryBuilder.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Support\\\\FilterQueryBuilder\\:\\:isNestedColumn\\(\\) has parameter \\$column with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/app/Support/FilterQueryBuilder.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Support\\\\FilterQueryBuilder\\:\\:makeFilter\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/app/Support/FilterQueryBuilder.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Support\\\\FilterQueryBuilder\\:\\:makeFilter\\(\\) has parameter \\$filter with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/app/Support/FilterQueryBuilder.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Support\\\\FilterQueryBuilder\\:\\:makeFilter\\(\\) has parameter \\$query with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/app/Support/FilterQueryBuilder.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Support\\\\FilterQueryBuilder\\:\\:makeOrder\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/app/Support/FilterQueryBuilder.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Support\\\\FilterQueryBuilder\\:\\:makeOrder\\(\\) has parameter \\$data with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/app/Support/FilterQueryBuilder.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Support\\\\FilterQueryBuilder\\:\\:makeOrder\\(\\) has parameter \\$query with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/app/Support/FilterQueryBuilder.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Support\\\\FilterQueryBuilder\\:\\:\\$model has no type specified\\.$#',
	'identifier' => 'missingType.property',
	'count' => 1,
	'path' => __DIR__ . '/app/Support/FilterQueryBuilder.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\Support\\\\FilterQueryBuilder\\:\\:\\$table has no type specified\\.$#',
	'identifier' => 'missingType.property',
	'count' => 1,
	'path' => __DIR__ . '/app/Support/FilterQueryBuilder.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$view of function view expects view\\-string\\|null, string given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/View/Components/DatePicker.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$view of function view expects view\\-string\\|null, string given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/View/Components/Dropzone.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @param references unknown parameter\\: \\$id$#',
	'identifier' => 'parameter.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/View/Components/SelectList.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$view of function view expects view\\-string\\|null, string given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app/View/Components/SelectList.php',
];
$ignoreErrors[] = [
	'message' => '#^Property App\\\\View\\\\Components\\\\SelectList\\:\\:\\$options has no type specified\\.$#',
	'identifier' => 'missingType.property',
	'count' => 1,
	'path' => __DIR__ . '/app/View/Components/SelectList.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @extends has invalid type App\\\\Models\\\\Enrollment\\.$#',
	'identifier' => 'class.notFound',
	'count' => 1,
	'path' => __DIR__ . '/database/factories/EnrollmentFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Type App\\\\Models\\\\Enrollment in generic type Illuminate\\\\Database\\\\Eloquent\\\\Factories\\\\Factory\\<App\\\\Models\\\\Enrollment\\> in PHPDoc tag @extends is not subtype of template type TModel of Illuminate\\\\Database\\\\Eloquent\\\\Model of class Illuminate\\\\Database\\\\Eloquent\\\\Factories\\\\Factory\\.$#',
	'identifier' => 'generics.notSubtype',
	'count' => 1,
	'path' => __DIR__ . '/database/factories/EnrollmentFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @extends has invalid type App\\\\Models\\\\Program\\.$#',
	'identifier' => 'class.notFound',
	'count' => 1,
	'path' => __DIR__ . '/database/factories/ProgramFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Type App\\\\Models\\\\Program in generic type Illuminate\\\\Database\\\\Eloquent\\\\Factories\\\\Factory\\<App\\\\Models\\\\Program\\> in PHPDoc tag @extends is not subtype of template type TModel of Illuminate\\\\Database\\\\Eloquent\\\\Model of class Illuminate\\\\Database\\\\Eloquent\\\\Factories\\\\Factory\\.$#',
	'identifier' => 'generics.notSubtype',
	'count' => 1,
	'path' => __DIR__ . '/database/factories/ProgramFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$guardName of method Illuminate\\\\Database\\\\Migrations\\\\Migration@anonymous/database/migrations/2024_05_15_200656_seed_permissions_for_tags\\.php\\:42\\:\\:deletePermissions\\(\\) expects string, array given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/database/migrations/2024_05_15_200656_seed_permissions_for_tags.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Illuminate\\\\Database\\\\Migrations\\\\Migration@anonymous/database/migrations/2024_05_15_200656_seed_permissions_for_tags\\.php\\:42\\:\\:\\$guards type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/database/migrations/2024_05_15_200656_seed_permissions_for_tags.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Illuminate\\\\Database\\\\Migrations\\\\Migration@anonymous/database/migrations/2024_05_15_200656_seed_permissions_for_tags\\.php\\:42\\:\\:\\$permissions type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/database/migrations/2024_05_15_200656_seed_permissions_for_tags.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$guardName of method Illuminate\\\\Database\\\\Migrations\\\\Migration@anonymous/database/migrations/2024_06_07_094732_seed_permissions_add_report_library\\.php\\:42\\:\\:deletePermissions\\(\\) expects string, array given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/database/migrations/2024_06_07_094732_seed_permissions_add_report_library.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Illuminate\\\\Database\\\\Migrations\\\\Migration@anonymous/database/migrations/2024_06_07_094732_seed_permissions_add_report_library\\.php\\:42\\:\\:\\$guards type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/database/migrations/2024_06_07_094732_seed_permissions_add_report_library.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Illuminate\\\\Database\\\\Migrations\\\\Migration@anonymous/database/migrations/2024_06_07_094732_seed_permissions_add_report_library\\.php\\:42\\:\\:\\$permissions type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/database/migrations/2024_06_07_094732_seed_permissions_add_report_library.php',
];
$ignoreErrors[] = [
	'message' => '#^Left side of && is always true\\.$#',
	'identifier' => 'booleanAnd.leftAlwaysTrue',
	'count' => 1,
	'path' => __DIR__ . '/database/migrations/2024_07_10_085735_data_update_value_of_time_to_resolution_in_service_request.php',
];
$ignoreErrors[] = [
	'message' => '#^Right side of && is always true\\.$#',
	'identifier' => 'booleanAnd.rightAlwaysTrue',
	'count' => 1,
	'path' => __DIR__ . '/database/migrations/2024_07_10_085735_data_update_value_of_time_to_resolution_in_service_request.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Illuminate\\\\Database\\\\Migrations\\\\Migration@anonymous/database/migrations/2024_08_01_175631_data_fix_image_ids_in_tiptap_content\\.php\\:40\\:\\:fixTipTapBlock\\(\\) has parameter \\$block with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/database/migrations/2024_08_01_175631_data_fix_image_ids_in_tiptap_content.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Illuminate\\\\Database\\\\Migrations\\\\Migration@anonymous/database/migrations/2024_08_01_175631_data_fix_image_ids_in_tiptap_content\\.php\\:40\\:\\:fixTipTapBlock\\(\\) has parameter \\$mediaUuidMap with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/database/migrations/2024_08_01_175631_data_fix_image_ids_in_tiptap_content.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Illuminate\\\\Database\\\\Migrations\\\\Migration@anonymous/database/migrations/2024_08_01_175631_data_fix_image_ids_in_tiptap_content\\.php\\:40\\:\\:fixTipTapBlock\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/database/migrations/2024_08_01_175631_data_fix_image_ids_in_tiptap_content.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Illuminate\\\\Database\\\\Migrations\\\\Migration@anonymous/database/migrations/2024_08_01_175631_data_fix_image_ids_in_tiptap_content\\.php\\:40\\:\\:fixTipTapContent\\(\\) has parameter \\$mediaUuidMap with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/database/migrations/2024_08_01_175631_data_fix_image_ids_in_tiptap_content.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$guardName of method Illuminate\\\\Database\\\\Migrations\\\\Migration@anonymous/database/migrations/2024_11_19_165344_seed_permissions_for_product_admin\\.php\\:42\\:\\:deletePermissions\\(\\) expects string, array given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/database/migrations/2024_11_19_165344_seed_permissions_for_product_admin.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Illuminate\\\\Database\\\\Migrations\\\\Migration@anonymous/database/migrations/2024_11_19_165344_seed_permissions_for_product_admin\\.php\\:42\\:\\:\\$guards type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/database/migrations/2024_11_19_165344_seed_permissions_for_product_admin.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Illuminate\\\\Database\\\\Migrations\\\\Migration@anonymous/database/migrations/2024_11_19_165344_seed_permissions_for_product_admin\\.php\\:42\\:\\:\\$permissions type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/database/migrations/2024_11_19_165344_seed_permissions_for_product_admin.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Illuminate\\\\Database\\\\Migrations\\\\Migration@anonymous/database/migrations/2024_11_21_002436_seed_permissions_remove_assistant_permissions\\.php\\:42\\:\\:\\$guards type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/database/migrations/2024_11_21_002436_seed_permissions_remove_assistant_permissions.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Illuminate\\\\Database\\\\Migrations\\\\Migration@anonymous/database/migrations/2024_11_21_002436_seed_permissions_remove_assistant_permissions\\.php\\:42\\:\\:\\$permissions type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/database/migrations/2024_11_21_002436_seed_permissions_remove_assistant_permissions.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Illuminate\\\\Database\\\\Migrations\\\\Migration@anonymous/database/migrations/2024_12_03_064748_seed_permissions_remove_specific_global_settings_permissions\\.php\\:40\\:\\:\\$guards type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/database/migrations/2024_12_03_064748_seed_permissions_remove_specific_global_settings_permissions.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Illuminate\\\\Database\\\\Migrations\\\\Migration@anonymous/database/migrations/2024_12_03_064748_seed_permissions_remove_specific_global_settings_permissions\\.php\\:40\\:\\:\\$permissions type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/database/migrations/2024_12_03_064748_seed_permissions_remove_specific_global_settings_permissions.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Illuminate\\\\Database\\\\Migrations\\\\Migration@anonymous/database/migrations/2024_12_09_162908_seed_permissions_delete_unused_product_administration_permissions\\.php\\:42\\:\\:\\$guards type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/database/migrations/2024_12_09_162908_seed_permissions_delete_unused_product_administration_permissions.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Illuminate\\\\Database\\\\Migrations\\\\Migration@anonymous/database/migrations/2024_12_09_162908_seed_permissions_delete_unused_product_administration_permissions\\.php\\:42\\:\\:\\$permissions type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/database/migrations/2024_12_09_162908_seed_permissions_delete_unused_product_administration_permissions.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Illuminate\\\\Database\\\\Migrations\\\\Migration@anonymous/database/migrations/2025_01_21_111815_seed_permissions_delete_view_product_health_dashboard_permissions\\.php\\:40\\:\\:\\$guards type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/database/migrations/2025_01_21_111815_seed_permissions_delete_view_product_health_dashboard_permissions.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Illuminate\\\\Database\\\\Migrations\\\\Migration@anonymous/database/migrations/2025_01_21_111815_seed_permissions_delete_view_product_health_dashboard_permissions\\.php\\:40\\:\\:\\$permissions type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/database/migrations/2025_01_21_111815_seed_permissions_delete_view_product_health_dashboard_permissions.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Illuminate\\\\Database\\\\Migrations\\\\Migration@anonymous/database/migrations/2025_03_19_123414_seed_permission_delete_user_view_email_permission\\.php\\:40\\:\\:\\$guards type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/database/migrations/2025_03_19_123414_seed_permission_delete_user_view_email_permission.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Illuminate\\\\Database\\\\Migrations\\\\Migration@anonymous/database/migrations/2025_03_19_123414_seed_permission_delete_user_view_email_permission\\.php\\:40\\:\\:\\$permissions type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/database/migrations/2025_03_19_123414_seed_permission_delete_user_view_email_permission.php',
];
$ignoreErrors[] = [
	'message' => '#^Trait Database\\\\Migrations\\\\Concerns\\\\CanModifySettings is used zero times and is not analysed\\.$#',
	'identifier' => 'trait.unused',
	'count' => 1,
	'path' => __DIR__ . '/database/migrations/Concerns/CanModifySettings.php',
];
$ignoreErrors[] = [
	'message' => '#^Unable to resolve the template type TKey in call to function collect$#',
	'identifier' => 'argument.templateType',
	'count' => 2,
	'path' => __DIR__ . '/database/seeders/LocalDevelopmentSeeder.php',
];
$ignoreErrors[] = [
	'message' => '#^Unable to resolve the template type TValue in call to function collect$#',
	'identifier' => 'argument.templateType',
	'count' => 2,
	'path' => __DIR__ . '/database/seeders/LocalDevelopmentSeeder.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method call\\(\\) on an unknown class static\\.$#',
	'identifier' => 'class.notFound',
	'count' => 1,
	'path' => __DIR__ . '/tests/Feature/Filament/AmazonS3Test.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method Pest\\\\PendingCalls\\\\TestCall\\|Pest\\\\Support\\\\HigherOrderTapProxy\\:\\:actingAs\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/tests/Helpers.php',
];
$ignoreErrors[] = [
	'message' => '#^Function Tests\\\\Helpers\\\\testResourceRequiresPermissionForAccess\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/tests/Helpers/AccessControl.php',
];
$ignoreErrors[] = [
	'message' => '#^Function Tests\\\\Helpers\\\\testResourceRequiresPermissionForAccess\\(\\) has parameter \\$feature with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/tests/Helpers/AccessControl.php',
];
$ignoreErrors[] = [
	'message' => '#^Function Tests\\\\Helpers\\\\testResourceRequiresPermissionForAccess\\(\\) has parameter \\$permissions with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/tests/Helpers/AccessControl.php',
];
$ignoreErrors[] = [
	'message' => '#^Function Tests\\\\Helpers\\\\Events\\\\testEventIsBeingListenedTo\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/tests/Helpers/Events/EventListener.php',
];
$ignoreErrors[] = [
	'message' => '#^Function user\\(\\) has parameter \\$licenses with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/tests/Pest.php',
];
$ignoreErrors[] = [
	'message' => '#^Function user\\(\\) has parameter \\$permissions with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/tests/Pest.php',
];
$ignoreErrors[] = [
	'message' => '#^Function user\\(\\) has parameter \\$roles with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/tests/Pest.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Tests\\\\TestCase\\:\\:beginDatabaseTransactionOnConnection\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/tests/TestCase.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Tests\\\\TestCase\\:\\:calculateMigrationChecksum\\(\\) has parameter \\$paths with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/tests/TestCase.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Tests\\\\TestCase\\:\\:refreshTestDatabase\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/tests/TestCase.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method Pest\\\\PendingCalls\\\\TestCall\\:\\:expect\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 2,
	'path' => __DIR__ . '/tests/Unit/ArchTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Static method PHPUnit\\\\Framework\\\\Assert\\:\\:assertContains\\(\\) invoked with named argument \\$haystack, but it\'s not allowed because of @no\\-named\\-arguments\\.$#',
	'identifier' => 'argument.named',
	'count' => 1,
	'path' => __DIR__ . '/tests/Unit/ArchTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Static method PHPUnit\\\\Framework\\\\Assert\\:\\:assertContains\\(\\) invoked with named argument \\$message, but it\'s not allowed because of @no\\-named\\-arguments\\.$#',
	'identifier' => 'argument.named',
	'count' => 1,
	'path' => __DIR__ . '/tests/Unit/ArchTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Static method PHPUnit\\\\Framework\\\\Assert\\:\\:assertContains\\(\\) invoked with named argument \\$needle, but it\'s not allowed because of @no\\-named\\-arguments\\.$#',
	'identifier' => 'argument.named',
	'count' => 1,
	'path' => __DIR__ . '/tests/Unit/ArchTest.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
