<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method AidingApp\\\\Ai\\\\Providers\\\\AiServiceProvider\\:\\:boot\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/ai/src/Providers/AiServiceProvider.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Ai\\\\Settings\\\\AiSettings\\:\\:encrypted\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/ai/src/Settings/AiSettings.php',
];
$ignoreErrors[] = [
	// identifier: ternary.alwaysTrue
	'message' => '#^Ternary operator condition is always true\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/alert/src/Filament/Resources/AlertResource/Pages/ListAlerts.php',
];
$ignoreErrors[] = [
	// identifier: nullsafe.neverNull
	'message' => '#^Using nullsafe property access on non\\-nullable type AidingApp\\\\Contact\\\\Models\\\\Contact\\. Use \\-\\> instead\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/alert/src/Filament/Resources/AlertResource/Pages/ListAlerts.php',
];
$ignoreErrors[] = [
	// identifier: nullsafe.neverNull
	'message' => '#^Using nullsafe method call on non\\-nullable type Illuminate\\\\Database\\\\Eloquent\\\\Collection\\<int, AidingApp\\\\Notification\\\\Models\\\\Subscription\\>\\. Use \\-\\> instead\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/alert/src/Listeners/NotifySubscribersOfAlertCreated.php',
];
$ignoreErrors[] = [
	// identifier: ternary.elseUnreachable
	'message' => '#^Else branch is unreachable because ternary operator condition is always true\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/alert/src/Models/Alert.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Alert\\\\Models\\\\Alert\\:\\:concern\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/alert/src/Models/Alert.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\Alert\\\\Models\\\\Alert\\:\\:newBelongsToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/alert/src/Models/Alert.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\Alert\\\\Models\\\\Alert\\:\\:newMorphToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/alert/src/Models/Alert.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Alert\\\\Models\\\\Alert\\:\\:scopeLicensedToEducatable\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/alert/src/Models/Alert.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Alert\\\\Models\\\\Alert\\:\\:scopeLicensedToEducatable\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder does not specify its types\\: TModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/alert/src/Models/Alert.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Alert\\\\Models\\\\Alert\\:\\:scopeStatus\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/alert/src/Models/Alert.php',
];
$ignoreErrors[] = [
	// identifier: match.unhandled
	'message' => '#^Match expression does not handle remaining value\\: class\\-string\\<AidingApp\\\\Contact\\\\Models\\\\Contact\\>&literal\\-string$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/alert/src/Notifications/AlertCreatedNotification.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Alert\\\\Notifications\\\\AlertCreatedNotification\\:\\:toDatabase\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/alert/src/Notifications/AlertCreatedNotification.php',
];
$ignoreErrors[] = [
	// identifier: match.unhandled
	'message' => '#^Match expression does not handle remaining value\\: string$#',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/alert/src/Observers/AlertObserver.php',
];
$ignoreErrors[] = [
	// identifier: nullsafe.neverNull
	'message' => '#^Using nullsafe method call on non\\-nullable type AidingApp\\\\Contact\\\\Models\\\\Contact\\. Use \\-\\> instead\\.$#',
	'count' => 5,
	'path' => __DIR__ . '/app-modules/alert/src/Policies/AlertPolicy.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Alert\\\\Rules\\\\ConcernIdExistsRule\\:\\:setData\\(\\) has parameter \\$data with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/alert/src/Rules/ConcernIdExistsRule.php',
];
$ignoreErrors[] = [
	// identifier: missingType.property
	'message' => '#^Property AidingApp\\\\Alert\\\\Rules\\\\ConcernIdExistsRule\\:\\:\\$data has no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/alert/src/Rules/ConcernIdExistsRule.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Audit\\\\Actions\\\\Finders\\\\AuditableModels\\:\\:all\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/audit/src/Actions/Finders/AuditableModels.php',
];
$ignoreErrors[] = [
	// identifier: method.notFound
	'message' => '#^Call to an undefined method OwenIt\\\\Auditing\\\\Contracts\\\\Auditable\\:\\:auditAttach\\(\\)\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/audit/src/Filament/Actions/AuditAttachAction.php',
];
$ignoreErrors[] = [
	// identifier: method.notFound
	'message' => '#^Call to an undefined method OwenIt\\\\Auditing\\\\Contracts\\\\Auditable\\:\\:auditDetach\\(\\)\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/audit/src/Filament/Actions/AuditDetachAction.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^PHPDoc tag @var for variable \\$relationship contains generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany but does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/audit/src/Filament/Actions/AuditDetachAction.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Class AidingApp\\\\Audit\\\\Models\\\\Audit uses generic trait Illuminate\\\\Database\\\\Eloquent\\\\Factories\\\\HasFactory but does not specify its types\\: TFactory$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/audit/src/Models/Audit.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Audit\\\\Models\\\\Audit\\:\\:prunable\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder does not specify its types\\: TModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/audit/src/Models/Audit.php',
];
$ignoreErrors[] = [
	// identifier: property.notFound
	'message' => '#^Access to an undefined property OwenIt\\\\Auditing\\\\Contracts\\\\Auditable\\:\\:\\$auditCustomNew\\.$#',
	'count' => 4,
	'path' => __DIR__ . '/app-modules/audit/src/Overrides/BelongsToMany.php',
];
$ignoreErrors[] = [
	// identifier: property.notFound
	'message' => '#^Access to an undefined property OwenIt\\\\Auditing\\\\Contracts\\\\Auditable\\:\\:\\$auditCustomOld\\.$#',
	'count' => 3,
	'path' => __DIR__ . '/app-modules/audit/src/Overrides/BelongsToMany.php',
];
$ignoreErrors[] = [
	// identifier: property.notFound
	'message' => '#^Access to an undefined property OwenIt\\\\Auditing\\\\Contracts\\\\Auditable\\:\\:\\$auditEvent\\.$#',
	'count' => 3,
	'path' => __DIR__ . '/app-modules/audit/src/Overrides/BelongsToMany.php',
];
$ignoreErrors[] = [
	// identifier: property.notFound
	'message' => '#^Access to an undefined property OwenIt\\\\Auditing\\\\Contracts\\\\Auditable\\:\\:\\$isCustomEvent\\.$#',
	'count' => 3,
	'path' => __DIR__ . '/app-modules/audit/src/Overrides/BelongsToMany.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Class AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany extends generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany but does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/audit/src/Overrides/BelongsToMany.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\:\\:attach\\(\\) has parameter \\$attributes with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/audit/src/Overrides/BelongsToMany.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\:\\:isAuditable\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/audit/src/Overrides/BelongsToMany.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\:\\:sync\\(\\) has parameter \\$ids with generic class Illuminate\\\\Support\\\\Collection but does not specify its types\\: TKey, TValue$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/audit/src/Overrides/BelongsToMany.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\:\\:sync\\(\\) has parameter \\$ids with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/audit/src/Overrides/BelongsToMany.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\:\\:sync\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 3,
	'path' => __DIR__ . '/app-modules/audit/src/Overrides/BelongsToMany.php',
];
$ignoreErrors[] = [
	// identifier: property.notFound
	'message' => '#^Access to an undefined property OwenIt\\\\Auditing\\\\Contracts\\\\Auditable\\:\\:\\$auditCustomNew\\.$#',
	'count' => 4,
	'path' => __DIR__ . '/app-modules/audit/src/Overrides/MorphToMany.php',
];
$ignoreErrors[] = [
	// identifier: property.notFound
	'message' => '#^Access to an undefined property OwenIt\\\\Auditing\\\\Contracts\\\\Auditable\\:\\:\\$auditCustomOld\\.$#',
	'count' => 3,
	'path' => __DIR__ . '/app-modules/audit/src/Overrides/MorphToMany.php',
];
$ignoreErrors[] = [
	// identifier: property.notFound
	'message' => '#^Access to an undefined property OwenIt\\\\Auditing\\\\Contracts\\\\Auditable\\:\\:\\$auditEvent\\.$#',
	'count' => 3,
	'path' => __DIR__ . '/app-modules/audit/src/Overrides/MorphToMany.php',
];
$ignoreErrors[] = [
	// identifier: property.notFound
	'message' => '#^Access to an undefined property OwenIt\\\\Auditing\\\\Contracts\\\\Auditable\\:\\:\\$isCustomEvent\\.$#',
	'count' => 3,
	'path' => __DIR__ . '/app-modules/audit/src/Overrides/MorphToMany.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Class AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany extends generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany but does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/audit/src/Overrides/MorphToMany.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\:\\:attach\\(\\) has parameter \\$attributes with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/audit/src/Overrides/MorphToMany.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\:\\:isAuditable\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/audit/src/Overrides/MorphToMany.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\:\\:sync\\(\\) has parameter \\$ids with generic class Illuminate\\\\Support\\\\Collection but does not specify its types\\: TKey, TValue$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/audit/src/Overrides/MorphToMany.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\:\\:sync\\(\\) has parameter \\$ids with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/audit/src/Overrides/MorphToMany.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\:\\:sync\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 3,
	'path' => __DIR__ . '/app-modules/audit/src/Overrides/MorphToMany.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Property AidingApp\\\\Audit\\\\Settings\\\\AuditSettings\\:\\:\\$audited_models_exclude type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/audit/src/Settings/AuditSettings.php',
];
$ignoreErrors[] = [
	// identifier: method.notFound
	'message' => '#^Call to an undefined method Pest\\\\PendingCalls\\\\TestCall\\|Pest\\\\Support\\\\HigherOrderTapProxy\\:\\:expect\\(\\)\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/audit/tests/AuditTraitUsageTest.php',
];
$ignoreErrors[] = [
	// identifier: class.notFound
	'message' => '#^Call to method getMail\\(\\) on an unknown class AidingApp\\\\Authorization\\\\Enums\\\\User\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/authorization/src/Enums/SocialiteProvider.php',
];
$ignoreErrors[] = [
	// identifier: class.notFound
	'message' => '#^Call to method getPrincipalName\\(\\) on an unknown class AidingApp\\\\Authorization\\\\Enums\\\\User\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/authorization/src/Enums/SocialiteProvider.php',
];
$ignoreErrors[] = [
	// identifier: match.unreachable
	'message' => '#^Match arm is unreachable because previous comparison is always true\\.$#',
	'count' => 3,
	'path' => __DIR__ . '/app-modules/authorization/src/Enums/SocialiteProvider.php',
];
$ignoreErrors[] = [
	// identifier: class.notFound
	'message' => '#^PHPDoc tag @var for variable \\$user contains unknown class AidingApp\\\\Authorization\\\\Enums\\\\User\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/authorization/src/Enums/SocialiteProvider.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Authorization\\\\Filament\\\\Pages\\\\Auth\\\\Login\\:\\:getSsoFormActions\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/authorization/src/Filament/Pages/Auth/Login.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Property AidingApp\\\\Authorization\\\\Filament\\\\Pages\\\\Auth\\\\SetPassword\\:\\:\\$data type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/authorization/src/Filament/Pages/Auth/SetPassword.php',
];
$ignoreErrors[] = [
	// identifier: class.notFound
	'message' => '#^Call to method isSuperAdmin\\(\\) on an unknown class AidingApp\\\\Authorization\\\\Filament\\\\Pages\\\\User\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/authorization/src/Filament/Pages/ManageGoogleSsoSettings.php',
];
$ignoreErrors[] = [
	// identifier: class.notFound
	'message' => '#^PHPDoc tag @var for variable \\$user contains unknown class AidingApp\\\\Authorization\\\\Filament\\\\Pages\\\\User\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/authorization/src/Filament/Pages/ManageGoogleSsoSettings.php',
];
$ignoreErrors[] = [
	// identifier: method.notFound
	'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Builder\\:\\:api\\(\\)\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/authorization/src/Filament/Resources/PermissionResource/Pages/ListPermissions.php',
];
$ignoreErrors[] = [
	// identifier: method.notFound
	'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Builder\\:\\:web\\(\\)\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/authorization/src/Filament/Resources/PermissionResource/Pages/ListPermissions.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Authorization\\\\Filament\\\\Resources\\\\RoleResource\\:\\:getEloquentQuery\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder does not specify its types\\: TModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/authorization/src/Filament/Resources/RoleResource.php',
];
$ignoreErrors[] = [
	// identifier: varTag.unresolvableType
	'message' => '#^PHPDoc tag @var for variable \\$resource contains unresolvable type\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/authorization/src/Filament/Resources/RoleResource/Pages/EditRole.php',
];
$ignoreErrors[] = [
	// identifier: method.notFound
	'message' => '#^Call to an undefined method Illuminate\\\\Contracts\\\\Database\\\\Eloquent\\\\Builder\\:\\:api\\(\\)\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/authorization/src/Filament/Resources/RoleResource/Pages/ListRoles.php',
];
$ignoreErrors[] = [
	// identifier: method.notFound
	'message' => '#^Call to an undefined method Illuminate\\\\Contracts\\\\Database\\\\Eloquent\\\\Builder\\:\\:web\\(\\)\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/authorization/src/Filament/Resources/RoleResource/Pages/ListRoles.php',
];
$ignoreErrors[] = [
	// identifier: method.notFound
	'message' => '#^Call to an undefined method Laravel\\\\Socialite\\\\Contracts\\\\Provider\\|Mockery\\\\MockInterface\\:\\:setConfig\\(\\)\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/authorization/src/Http/Controllers/SocialiteController.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method AidingApp\\\\Authorization\\\\Http\\\\Controllers\\\\SocialiteController\\:\\:callback\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/authorization/src/Http/Controllers/SocialiteController.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method AidingApp\\\\Authorization\\\\Http\\\\Controllers\\\\SocialiteController\\:\\:redirect\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/authorization/src/Http/Controllers/SocialiteController.php',
];
$ignoreErrors[] = [
	// identifier: nullsafe.neverNull
	'message' => '#^Using nullsafe property access on non\\-nullable type App\\\\Models\\\\User\\. Use \\-\\> instead\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/authorization/src/Http/Controllers/SocialiteController.php',
];
$ignoreErrors[] = [
	// identifier: method.notFound
	'message' => '#^Call to an undefined method Laravel\\\\Socialite\\\\Contracts\\\\Provider\\|Mockery\\\\MockInterface\\:\\:getLogoutUrl\\(\\)\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/authorization/src/Http/Responses/Auth/SocialiteLogoutResponse.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\Authorization\\\\Models\\\\License\\:\\:newBelongsToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/authorization/src/Models/License.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\Authorization\\\\Models\\\\License\\:\\:newMorphToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/authorization/src/Models/License.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Authorization\\\\Models\\\\License\\:\\:user\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/authorization/src/Models/License.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Class AidingApp\\\\Authorization\\\\Models\\\\Permission uses generic trait Illuminate\\\\Database\\\\Eloquent\\\\Factories\\\\HasFactory but does not specify its types\\: TFactory$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/authorization/src/Models/Permission.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Authorization\\\\Models\\\\Permission\\:\\:group\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/authorization/src/Models/Permission.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\Authorization\\\\Models\\\\Permission\\:\\:newBelongsToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/authorization/src/Models/Permission.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\Authorization\\\\Models\\\\Permission\\:\\:newMorphToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/authorization/src/Models/Permission.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method AidingApp\\\\Authorization\\\\Models\\\\Permission\\:\\:systemUsers\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/authorization/src/Models/Permission.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Authorization\\\\Models\\\\PermissionGroup\\:\\:permissions\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/authorization/src/Models/PermissionGroup.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Class AidingApp\\\\Authorization\\\\Models\\\\Role uses generic trait Illuminate\\\\Database\\\\Eloquent\\\\Factories\\\\HasFactory but does not specify its types\\: TFactory$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/authorization/src/Models/Role.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\Authorization\\\\Models\\\\Role\\:\\:newBelongsToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/authorization/src/Models/Role.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\Authorization\\\\Models\\\\Role\\:\\:newMorphToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/authorization/src/Models/Role.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Authorization\\\\Models\\\\Role\\:\\:scopeApi\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/authorization/src/Models/Role.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Authorization\\\\Models\\\\Role\\:\\:scopeSuperAdmin\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/authorization/src/Models/Role.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Authorization\\\\Models\\\\Role\\:\\:scopeWeb\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/authorization/src/Models/Role.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Authorization\\\\Models\\\\Role\\:\\:users\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/authorization/src/Models/Role.php',
];
$ignoreErrors[] = [
	// identifier: argument.templateType
	'message' => '#^Unable to resolve the template type TRelatedModel in call to method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:morphedByMany\\(\\)$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/authorization/src/Models/Role.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Authorization\\\\Settings\\\\AzureSsoSettings\\:\\:encrypted\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/authorization/src/Settings/AzureSsoSettings.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Authorization\\\\Settings\\\\GoogleSsoSettings\\:\\:encrypted\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/authorization/src/Settings/GoogleSsoSettings.php',
];
$ignoreErrors[] = [
	// identifier: property.notFound
	'message' => '#^Access to an undefined property Pest\\\\Expectation\\<App\\\\Models\\\\User\\|null\\>\\:\\:\\$password\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/authorization/tests/Feature/Filament/Pages/SetPasswordTest.php',
];
$ignoreErrors[] = [
	// identifier: method.notFound
	'message' => '#^Call to an undefined method Illuminate\\\\Testing\\\\TestResponse\\:\\:assertActionHidden\\(\\)\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/authorization/tests/Feature/Filament/Resources/UserResources/Pages/EditUserTest.php',
];
$ignoreErrors[] = [
	// identifier: method.notFound
	'message' => '#^Call to an undefined method Illuminate\\\\Testing\\\\TestResponse\\:\\:assertActionVisible\\(\\)\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/authorization/tests/Feature/Filament/Resources/UserResources/Pages/EditUserTest.php',
];
$ignoreErrors[] = [
	// identifier: method.notFound
	'message' => '#^Call to an undefined method Illuminate\\\\Testing\\\\TestResponse\\:\\:callAction\\(\\)\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/authorization/tests/Feature/Filament/Resources/UserResources/Pages/EditUserTest.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$key of method Illuminate\\\\Support\\\\Collection\\<string,mixed\\>\\:\\:get\\(\\) expects "\\\\000\\*\\\\000appends"\\|"\\\\000\\*…"\\|"\\\\000\\*\\\\000attributes"\\|"\\\\000\\*\\\\000authPasswordName"\\|"\\\\000\\*\\\\000casts"\\|"\\\\000\\*\\\\000changes"\\|"\\\\000\\*\\\\000classCastCache"\\|"\\\\000\\*\\\\000connection"\\|"\\\\000\\*\\\\000dateFormat"\\|"\\\\000\\*\\\\000dispatchesEvents"\\|"\\\\000\\*\\\\000fillable"\\|"\\\\000\\*\\\\000forceDeleting"\\|"\\\\000\\*\\\\000guarded"\\|"\\\\000\\*\\\\000hidden"\\|"\\\\000\\*\\\\000keyType"\\|"\\\\000\\*\\\\000observables"\\|"\\\\000\\*\\\\000original"\\|"\\\\000\\*\\\\000perPage"\\|"\\\\000\\*\\\\000primaryKey"\\|"\\\\000\\*\\\\000relations"\\|"\\\\000\\*\\\\000rememberTokenName"\\|"\\\\000\\*\\\\000table"\\|"\\\\000\\*\\\\000touches"\\|"\\\\000\\*\\\\000visible"\\|"\\\\000\\*\\\\000with"\\|"\\\\000\\*\\\\000withCount"\\|"\\\\000App\\\\\\\\Models…"\\|\'auditCustomNew\'\\|\'auditCustomOld\'\\|\'auditEvent\'\\|\'exists\'\\|\'filterable\'\\|\'incrementing\'\\|\'isCustomEvent\'\\|\'mediaCollections\'\\|\'mediaConversions\'\\|\'orderable\'\\|\'preloadedResolverDa…\'\\|\'preventsLazyLoading\'\\|\'timestamps\'\\|\'usesUniqueIds\'\\|\'wasRecentlyCreated\', \'email\' given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/authorization/tests/Feature/Filament/Resources/UserResources/Pages/EditUserTest.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$key of method Illuminate\\\\Support\\\\Collection\\<string,mixed\\>\\:\\:get\\(\\) expects "\\\\000\\*\\\\000appends"\\|"\\\\000\\*…"\\|"\\\\000\\*\\\\000attributes"\\|"\\\\000\\*\\\\000authPasswordName"\\|"\\\\000\\*\\\\000casts"\\|"\\\\000\\*\\\\000changes"\\|"\\\\000\\*\\\\000classCastCache"\\|"\\\\000\\*\\\\000connection"\\|"\\\\000\\*\\\\000dateFormat"\\|"\\\\000\\*\\\\000dispatchesEvents"\\|"\\\\000\\*\\\\000fillable"\\|"\\\\000\\*\\\\000forceDeleting"\\|"\\\\000\\*\\\\000guarded"\\|"\\\\000\\*\\\\000hidden"\\|"\\\\000\\*\\\\000keyType"\\|"\\\\000\\*\\\\000observables"\\|"\\\\000\\*\\\\000original"\\|"\\\\000\\*\\\\000perPage"\\|"\\\\000\\*\\\\000primaryKey"\\|"\\\\000\\*\\\\000relations"\\|"\\\\000\\*\\\\000rememberTokenName"\\|"\\\\000\\*\\\\000table"\\|"\\\\000\\*\\\\000touches"\\|"\\\\000\\*\\\\000visible"\\|"\\\\000\\*\\\\000with"\\|"\\\\000\\*\\\\000withCount"\\|"\\\\000App\\\\\\\\Models…"\\|\'auditCustomNew\'\\|\'auditCustomOld\'\\|\'auditEvent\'\\|\'exists\'\\|\'filterable\'\\|\'incrementing\'\\|\'isCustomEvent\'\\|\'mediaCollections\'\\|\'mediaConversions\'\\|\'orderable\'\\|\'preloadedResolverDa…\'\\|\'preventsLazyLoading\'\\|\'timestamps\'\\|\'usesUniqueIds\'\\|\'wasRecentlyCreated\', \'is_external\' given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/authorization/tests/Feature/Filament/Resources/UserResources/Pages/EditUserTest.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$key of method Illuminate\\\\Support\\\\Collection\\<string,mixed\\>\\:\\:get\\(\\) expects "\\\\000\\*\\\\000appends"\\|"\\\\000\\*…"\\|"\\\\000\\*\\\\000attributes"\\|"\\\\000\\*\\\\000authPasswordName"\\|"\\\\000\\*\\\\000casts"\\|"\\\\000\\*\\\\000changes"\\|"\\\\000\\*\\\\000classCastCache"\\|"\\\\000\\*\\\\000connection"\\|"\\\\000\\*\\\\000dateFormat"\\|"\\\\000\\*\\\\000dispatchesEvents"\\|"\\\\000\\*\\\\000fillable"\\|"\\\\000\\*\\\\000forceDeleting"\\|"\\\\000\\*\\\\000guarded"\\|"\\\\000\\*\\\\000hidden"\\|"\\\\000\\*\\\\000keyType"\\|"\\\\000\\*\\\\000observables"\\|"\\\\000\\*\\\\000original"\\|"\\\\000\\*\\\\000perPage"\\|"\\\\000\\*\\\\000primaryKey"\\|"\\\\000\\*\\\\000relations"\\|"\\\\000\\*\\\\000rememberTokenName"\\|"\\\\000\\*\\\\000table"\\|"\\\\000\\*\\\\000touches"\\|"\\\\000\\*\\\\000visible"\\|"\\\\000\\*\\\\000with"\\|"\\\\000\\*\\\\000withCount"\\|"\\\\000App\\\\\\\\Models…"\\|\'auditCustomNew\'\\|\'auditCustomOld\'\\|\'auditEvent\'\\|\'exists\'\\|\'filterable\'\\|\'incrementing\'\\|\'isCustomEvent\'\\|\'mediaCollections\'\\|\'mediaConversions\'\\|\'orderable\'\\|\'preloadedResolverDa…\'\\|\'preventsLazyLoading\'\\|\'timestamps\'\\|\'usesUniqueIds\'\\|\'wasRecentlyCreated\', \'name\' given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/authorization/tests/Feature/Filament/Resources/UserResources/Pages/EditUserTest.php',
];
$ignoreErrors[] = [
	// identifier: method.notFound
	'message' => '#^Call to an undefined method Illuminate\\\\Testing\\\\TestResponse\\:\\:assertCountTableRecords\\(\\)\\.$#',
	'count' => 7,
	'path' => __DIR__ . '/app-modules/authorization/tests/Feature/Filament/Resources/UserResources/Pages/ListUsersTest.php',
];
$ignoreErrors[] = [
	// identifier: method.protected
	'message' => '#^Call to protected method assignRole\\(\\) of class App\\\\Models\\\\Authenticatable\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/authorization/tests/Feature/Filament/Resources/UserResources/Pages/ListUsersTest.php',
];
$ignoreErrors[] = [
	// identifier: method.notFound
	'message' => '#^Call to an undefined method Illuminate\\\\Testing\\\\TestResponse\\:\\:assertActionHidden\\(\\)\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/authorization/tests/Feature/Filament/Resources/UserResources/Pages/ViewUserTest.php',
];
$ignoreErrors[] = [
	// identifier: method.notFound
	'message' => '#^Call to an undefined method Illuminate\\\\Testing\\\\TestResponse\\:\\:assertActionVisible\\(\\)\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/authorization/tests/Feature/Filament/Resources/UserResources/Pages/ViewUserTest.php',
];
$ignoreErrors[] = [
	// identifier: method.notFound
	'message' => '#^Call to an undefined method Illuminate\\\\Testing\\\\TestResponse\\:\\:callAction\\(\\)\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/authorization/tests/Feature/Filament/Resources/UserResources/Pages/ViewUserTest.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Function something\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/authorization/tests/Pest.php',
];
$ignoreErrors[] = [
	// identifier: method.notFound
	'message' => '#^Call to an undefined method Faker\\\\Generator\\:\\:secondaryAddress\\(\\)\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/database/factories/ContactFactory.php',
];
$ignoreErrors[] = [
	// identifier: method.notFound
	'message' => '#^Call to an undefined method Faker\\\\Generator\\:\\:stateAbbr\\(\\)\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/database/factories/ContactFactory.php',
];
$ignoreErrors[] = [
	// identifier: method.notFound
	'message' => '#^Call to an undefined method Faker\\\\Generator\\:\\:state\\(\\)\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/database/factories/OrganizationFactory.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#2 \\$guardName of method class@anonymous/app\\-modules/contact/database/migrations/2024_06_09_122855_data_seed_permissions_for_organization_module\\.php\\:42\\:\\:deletePermissions\\(\\) expects string, array given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/database/migrations/2024_06_09_122855_data_seed_permissions_for_organization_module.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Property class@anonymous/app\\-modules/contact/database/migrations/2024_06_09_122855_data_seed_permissions_for_organization_module\\.php\\:42\\:\\:\\$guards type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/database/migrations/2024_06_09_122855_data_seed_permissions_for_organization_module.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Property class@anonymous/app\\-modules/contact/database/migrations/2024_06_09_122855_data_seed_permissions_for_organization_module\\.php\\:42\\:\\:\\$permissions type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/database/migrations/2024_06_09_122855_data_seed_permissions_for_organization_module.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#2 \\$guardName of method class@anonymous/app\\-modules/contact/database/migrations/2024_06_09_124416_data_seed_permissions_for_organization_type_module\\.php\\:42\\:\\:deletePermissions\\(\\) expects string, array given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/database/migrations/2024_06_09_124416_data_seed_permissions_for_organization_type_module.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Property class@anonymous/app\\-modules/contact/database/migrations/2024_06_09_124416_data_seed_permissions_for_organization_type_module\\.php\\:42\\:\\:\\$guards type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/database/migrations/2024_06_09_124416_data_seed_permissions_for_organization_type_module.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Property class@anonymous/app\\-modules/contact/database/migrations/2024_06_09_124416_data_seed_permissions_for_organization_type_module\\.php\\:42\\:\\:\\$permissions type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/database/migrations/2024_06_09_124416_data_seed_permissions_for_organization_type_module.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#2 \\$guardName of method class@anonymous/app\\-modules/contact/database/migrations/2024_06_09_124444_data_seed_permissions_for_organization_industry_module\\.php\\:42\\:\\:deletePermissions\\(\\) expects string, array given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/database/migrations/2024_06_09_124444_data_seed_permissions_for_organization_industry_module.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Property class@anonymous/app\\-modules/contact/database/migrations/2024_06_09_124444_data_seed_permissions_for_organization_industry_module\\.php\\:42\\:\\:\\$guards type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/database/migrations/2024_06_09_124444_data_seed_permissions_for_organization_industry_module.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Property class@anonymous/app\\-modules/contact/database/migrations/2024_06_09_124444_data_seed_permissions_for_organization_industry_module\\.php\\:42\\:\\:\\$permissions type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/database/migrations/2024_06_09_124444_data_seed_permissions_for_organization_industry_module.php',
];
$ignoreErrors[] = [
	// identifier: property.notFound
	'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$email\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Filament/Resources/ContactResource.php',
];
$ignoreErrors[] = [
	// identifier: property.notFound
	'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$email_id\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Filament/Resources/ContactResource.php',
];
$ignoreErrors[] = [
	// identifier: property.notFound
	'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$mobile\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Filament/Resources/ContactResource.php',
];
$ignoreErrors[] = [
	// identifier: property.notFound
	'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$otherid\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Filament/Resources/ContactResource.php',
];
$ignoreErrors[] = [
	// identifier: property.notFound
	'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$phone\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Filament/Resources/ContactResource.php',
];
$ignoreErrors[] = [
	// identifier: argument.templateType
	'message' => '#^Unable to resolve the template type TValue in call to function collect$#',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/contact/src/Filament/Resources/ContactResource.php',
];
$ignoreErrors[] = [
	// identifier: staticMethod.notFound
	'message' => '#^Call to an undefined static method class\\-string\\<Filament\\\\Resources\\\\RelationManagers\\\\RelationManager\\>\\|Filament\\\\Resources\\\\RelationManagers\\\\RelationManagerConfiguration\\:\\:canViewForRecord\\(\\)\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Filament/Resources/ContactResource/Pages/AssetManagement.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method AidingApp\\\\Contact\\\\Filament\\\\Resources\\\\ContactResource\\\\Pages\\\\AssetManagement\\:\\:filterRelationManagers\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Filament/Resources/ContactResource/Pages/AssetManagement.php',
];
$ignoreErrors[] = [
	// identifier: missingType.parameter
	'message' => '#^Method AidingApp\\\\Contact\\\\Filament\\\\Resources\\\\ContactResource\\\\Pages\\\\AssetManagement\\:\\:filterRelationManagers\\(\\) has parameter \\$record with no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Filament/Resources/ContactResource/Pages/AssetManagement.php',
];
$ignoreErrors[] = [
	// identifier: missingType.parameter
	'message' => '#^Method AidingApp\\\\Contact\\\\Filament\\\\Resources\\\\ContactResource\\\\Pages\\\\AssetManagement\\:\\:filterRelationManagers\\(\\) has parameter \\$relationManager with no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Filament/Resources/ContactResource/Pages/AssetManagement.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Contact\\\\Filament\\\\Resources\\\\ContactResource\\\\Pages\\\\AssetManagement\\:\\:managers\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Filament/Resources/ContactResource/Pages/AssetManagement.php',
];
$ignoreErrors[] = [
	// identifier: staticClassAccess.privateMethod
	'message' => '#^Unsafe call to private method AidingApp\\\\Contact\\\\Filament\\\\Resources\\\\ContactResource\\\\Pages\\\\AssetManagement\\:\\:managers\\(\\) through static\\:\\:\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/contact/src/Filament/Resources/ContactResource/Pages/AssetManagement.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Property AidingApp\\\\Contact\\\\Filament\\\\Resources\\\\ContactResource\\\\Pages\\\\ContactEngagementTimeline\\:\\:\\$modelsToTimeline type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Filament/Resources/ContactResource/Pages/ContactEngagementTimeline.php',
];
$ignoreErrors[] = [
	// identifier: staticMethod.notFound
	'message' => '#^Call to an undefined static method class\\-string\\<Filament\\\\Resources\\\\RelationManagers\\\\RelationManager\\>\\|Filament\\\\Resources\\\\RelationManagers\\\\RelationManagerConfiguration\\:\\:canViewForRecord\\(\\)\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Filament/Resources/ContactResource/Pages/ContactServiceManagement.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method AidingApp\\\\Contact\\\\Filament\\\\Resources\\\\ContactResource\\\\Pages\\\\ContactServiceManagement\\:\\:filterRelationManagers\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Filament/Resources/ContactResource/Pages/ContactServiceManagement.php',
];
$ignoreErrors[] = [
	// identifier: missingType.parameter
	'message' => '#^Method AidingApp\\\\Contact\\\\Filament\\\\Resources\\\\ContactResource\\\\Pages\\\\ContactServiceManagement\\:\\:filterRelationManagers\\(\\) has parameter \\$record with no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Filament/Resources/ContactResource/Pages/ContactServiceManagement.php',
];
$ignoreErrors[] = [
	// identifier: missingType.parameter
	'message' => '#^Method AidingApp\\\\Contact\\\\Filament\\\\Resources\\\\ContactResource\\\\Pages\\\\ContactServiceManagement\\:\\:filterRelationManagers\\(\\) has parameter \\$relationManager with no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Filament/Resources/ContactResource/Pages/ContactServiceManagement.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Contact\\\\Filament\\\\Resources\\\\ContactResource\\\\Pages\\\\ContactServiceManagement\\:\\:managers\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Filament/Resources/ContactResource/Pages/ContactServiceManagement.php',
];
$ignoreErrors[] = [
	// identifier: staticClassAccess.privateMethod
	'message' => '#^Unsafe call to private method AidingApp\\\\Contact\\\\Filament\\\\Resources\\\\ContactResource\\\\Pages\\\\ContactServiceManagement\\:\\:managers\\(\\) through static\\:\\:\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/contact/src/Filament/Resources/ContactResource/Pages/ContactServiceManagement.php',
];
$ignoreErrors[] = [
	// identifier: varTag.unresolvableType
	'message' => '#^PHPDoc tag @var for variable \\$resource contains unresolvable type\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Filament/Resources/ContactResource/Pages/EditContact.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),Illuminate\\\\Database\\\\Eloquent\\\\Model\\>\\:\\:each\\(\\) expects callable\\(Illuminate\\\\Database\\\\Eloquent\\\\Model, int\\|string\\)\\: mixed, Closure\\(AidingApp\\\\Contact\\\\Models\\\\Contact\\)\\: bool given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Filament/Resources/ContactResource/Pages/ListContacts.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$badge of method Filament\\\\Navigation\\\\NavigationItem\\:\\:badge\\(\\) expects Closure\\|string\\|null, int\\<1, max\\>\\|null given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Filament/Resources/ContactResource/Pages/ManageContactAlerts.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Contact\\\\Filament\\\\Resources\\\\ContactResource\\\\Pages\\\\ManageContactEngagement\\:\\:managers\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Filament/Resources/ContactResource/Pages/ManageContactEngagement.php',
];
$ignoreErrors[] = [
	// identifier: staticClassAccess.privateMethod
	'message' => '#^Unsafe call to private method AidingApp\\\\Contact\\\\Filament\\\\Resources\\\\ContactResource\\\\Pages\\\\ManageContactEngagement\\:\\:managers\\(\\) through static\\:\\:\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/contact/src/Filament/Resources/ContactResource/Pages/ManageContactEngagement.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Contact\\\\Filament\\\\Resources\\\\ContactResource\\\\Pages\\\\ManageContactFiles\\:\\:managers\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Filament/Resources/ContactResource/Pages/ManageContactFiles.php',
];
$ignoreErrors[] = [
	// identifier: staticClassAccess.privateMethod
	'message' => '#^Unsafe call to private method AidingApp\\\\Contact\\\\Filament\\\\Resources\\\\ContactResource\\\\Pages\\\\ManageContactFiles\\:\\:managers\\(\\) through static\\:\\:\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/contact/src/Filament/Resources/ContactResource/Pages/ManageContactFiles.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Contact\\\\Filament\\\\Resources\\\\ContactResource\\\\RelationManagers\\\\AssetCheckInRelationManager\\:\\:renderInfolist\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Filament/Resources/ContactResource/RelationManagers/AssetCheckInRelationManager.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Contact\\\\Filament\\\\Resources\\\\ContactResource\\\\RelationManagers\\\\AssetCheckOutRelationManager\\:\\:renderInfolist\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Filament/Resources/ContactResource/RelationManagers/AssetCheckOutRelationManager.php',
];
$ignoreErrors[] = [
	// identifier: property.notFound
	'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$sent_at\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Filament/Resources/ContactResource/RelationManagers/EngagementsRelationManager.php',
];
$ignoreErrors[] = [
	// identifier: property.notFound
	'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$subject\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/contact/src/Filament/Resources/ContactResource/RelationManagers/EngagementsRelationManager.php',
];
$ignoreErrors[] = [
	// identifier: property.notFound
	'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$user\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Filament/Resources/ContactResource/RelationManagers/EngagementsRelationManager.php',
];
$ignoreErrors[] = [
	// identifier: method.notFound
	'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:getBody\\(\\)\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/contact/src/Filament/Resources/ContactResource/RelationManagers/EngagementsRelationManager.php',
];
$ignoreErrors[] = [
	// identifier: class.notFound
	'message' => '#^Call to method getDeliveryMethod\\(\\) on an unknown class AidingApp\\\\Contact\\\\Filament\\\\Resources\\\\ContactResource\\\\RelationManagers\\\\HasDeliveryMethod\\.$#',
	'count' => 3,
	'path' => __DIR__ . '/app-modules/contact/src/Filament/Resources/ContactResource/RelationManagers/EngagementsRelationManager.php',
];
$ignoreErrors[] = [
	// identifier: match.unhandled
	'message' => '#^Match expression does not handle remaining value\\: class\\-string\\<Illuminate\\\\Database\\\\Eloquent\\\\Model\\>&literal\\-string$#',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/contact/src/Filament/Resources/ContactResource/RelationManagers/EngagementsRelationManager.php',
];
$ignoreErrors[] = [
	// identifier: match.unhandled
	'message' => '#^Match expression does not handle remaining value\\: string$#',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/contact/src/Filament/Resources/ContactResource/RelationManagers/EngagementsRelationManager.php',
];
$ignoreErrors[] = [
	// identifier: class.notFound
	'message' => '#^PHPDoc tag @var for variable \\$timelineable contains unknown class AidingApp\\\\Contact\\\\Filament\\\\Resources\\\\ContactResource\\\\RelationManagers\\\\HasDeliveryMethod\\.$#',
	'count' => 3,
	'path' => __DIR__ . '/app-modules/contact/src/Filament/Resources/ContactResource/RelationManagers/EngagementsRelationManager.php',
];
$ignoreErrors[] = [
	// identifier: nullsafe.neverNull
	'message' => '#^Using nullsafe method call on non\\-nullable type AidingApp\\\\Notification\\\\Enums\\\\EmailMessageEventType\\. Use \\-\\> instead\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Filament/Resources/ContactResource/RelationManagers/EngagementsRelationManager.php',
];
$ignoreErrors[] = [
	// identifier: varTag.unresolvableType
	'message' => '#^PHPDoc tag @var for variable \\$resource contains unresolvable type\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Filament/Resources/ContactSourceResource/Pages/EditContactSource.php',
];
$ignoreErrors[] = [
	// identifier: varTag.unresolvableType
	'message' => '#^PHPDoc tag @var for variable \\$resource contains unresolvable type\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Filament/Resources/ContactStatusResource/Pages/EditContactStatus.php',
];
$ignoreErrors[] = [
	// identifier: varTag.unresolvableType
	'message' => '#^PHPDoc tag @var for variable \\$resource contains unresolvable type\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Filament/Resources/OrganizationIndustryResource/Pages/EditOrganizationIndustry.php',
];
$ignoreErrors[] = [
	// identifier: property.nonObject
	'message' => '#^Cannot access property \\$id on Illuminate\\\\Database\\\\Eloquent\\\\Model\\|int\\|string\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Filament/Resources/OrganizationResource/Pages/EditOrganization.php',
];
$ignoreErrors[] = [
	// identifier: varTag.unresolvableType
	'message' => '#^PHPDoc tag @var for variable \\$resource contains unresolvable type\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Filament/Resources/OrganizationResource/Pages/EditOrganization.php',
];
$ignoreErrors[] = [
	// identifier: varTag.unresolvableType
	'message' => '#^PHPDoc tag @var for variable \\$resource contains unresolvable type\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Filament/Resources/OrganizationTypeResource/Pages/EditOrganizationType.php',
];
$ignoreErrors[] = [
	// identifier: method.notFound
	'message' => '#^Call to an undefined method Staudenmeir\\\\EloquentHasManyDeep\\\\HasManyDeep\\<Illuminate\\\\Database\\\\Eloquent\\\\Model, App\\\\Models\\\\User\\>\\:\\:status\\(\\)\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Filament/Widgets/ContactStats.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Class AidingApp\\\\Contact\\\\Models\\\\Contact uses generic trait Illuminate\\\\Database\\\\Eloquent\\\\Factories\\\\HasFactory but does not specify its types\\: TFactory$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Models/Contact.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Contact\\\\Models\\\\Contact\\:\\:alerts\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Models/Contact.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Contact\\\\Models\\\\Contact\\:\\:assetCheckIns\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Models/Contact.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Contact\\\\Models\\\\Contact\\:\\:assetCheckOuts\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Models/Contact.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Contact\\\\Models\\\\Contact\\:\\:assignedTo\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Models/Contact.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Contact\\\\Models\\\\Contact\\:\\:createdBy\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Models/Contact.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Contact\\\\Models\\\\Contact\\:\\:displayName\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Casts\\\\Attribute does not specify its types\\: TGet, TSet$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Models/Contact.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Contact\\\\Models\\\\Contact\\:\\:engagementFiles\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Models/Contact.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Contact\\\\Models\\\\Contact\\:\\:engagementResponses\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Models/Contact.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Contact\\\\Models\\\\Contact\\:\\:engagements\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Models/Contact.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Contact\\\\Models\\\\Contact\\:\\:knowledgeBaseArticleVotes\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Models/Contact.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\Contact\\\\Models\\\\Contact\\:\\:newBelongsToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Models/Contact.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\Contact\\\\Models\\\\Contact\\:\\:newMorphToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Models/Contact.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Contact\\\\Models\\\\Contact\\:\\:orderedEngagementResponses\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Models/Contact.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Contact\\\\Models\\\\Contact\\:\\:orderedEngagements\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Models/Contact.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Contact\\\\Models\\\\Contact\\:\\:organization\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Models/Contact.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Contact\\\\Models\\\\Contact\\:\\:productLicenses\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Models/Contact.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Contact\\\\Models\\\\Contact\\:\\:serviceRequests\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Models/Contact.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Contact\\\\Models\\\\Contact\\:\\:source\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Models/Contact.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Contact\\\\Models\\\\Contact\\:\\:status\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Models/Contact.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Contact\\\\Models\\\\Contact\\:\\:subscribedUsers\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Models/Contact.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Contact\\\\Models\\\\Contact\\:\\:subscriptions\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Models/Contact.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Contact\\\\Models\\\\Contact\\:\\:tasks\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Models/Contact.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Contact\\\\Models\\\\Contact\\:\\:timeline\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphOne does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Models/Contact.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Contact\\\\Models\\\\ContactSource\\:\\:contacts\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Models/ContactSource.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\Contact\\\\Models\\\\ContactSource\\:\\:newBelongsToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Models/ContactSource.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\Contact\\\\Models\\\\ContactSource\\:\\:newMorphToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Models/ContactSource.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Contact\\\\Models\\\\ContactStatus\\:\\:contacts\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Models/ContactStatus.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\Contact\\\\Models\\\\ContactStatus\\:\\:newBelongsToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Models/ContactStatus.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\Contact\\\\Models\\\\ContactStatus\\:\\:newMorphToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Models/ContactStatus.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Class AidingApp\\\\Contact\\\\Models\\\\Organization uses generic trait Illuminate\\\\Database\\\\Eloquent\\\\Factories\\\\HasFactory but does not specify its types\\: TFactory$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Models/Organization.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Contact\\\\Models\\\\Organization\\:\\:contacts\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Models/Organization.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Contact\\\\Models\\\\Organization\\:\\:industry\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Models/Organization.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\Contact\\\\Models\\\\Organization\\:\\:newBelongsToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Models/Organization.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\Contact\\\\Models\\\\Organization\\:\\:newMorphToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Models/Organization.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Contact\\\\Models\\\\Organization\\:\\:type\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Models/Organization.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Class AidingApp\\\\Contact\\\\Models\\\\OrganizationIndustry uses generic trait Illuminate\\\\Database\\\\Eloquent\\\\Factories\\\\HasFactory but does not specify its types\\: TFactory$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Models/OrganizationIndustry.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\Contact\\\\Models\\\\OrganizationIndustry\\:\\:newBelongsToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Models/OrganizationIndustry.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\Contact\\\\Models\\\\OrganizationIndustry\\:\\:newMorphToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Models/OrganizationIndustry.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Contact\\\\Models\\\\OrganizationIndustry\\:\\:organizations\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Models/OrganizationIndustry.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Class AidingApp\\\\Contact\\\\Models\\\\OrganizationType uses generic trait Illuminate\\\\Database\\\\Eloquent\\\\Factories\\\\HasFactory but does not specify its types\\: TFactory$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Models/OrganizationType.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\Contact\\\\Models\\\\OrganizationType\\:\\:newBelongsToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Models/OrganizationType.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\Contact\\\\Models\\\\OrganizationType\\:\\:newMorphToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Models/OrganizationType.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Contact\\\\Models\\\\OrganizationType\\:\\:organizations\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Models/OrganizationType.php',
];
$ignoreErrors[] = [
	// identifier: nullsafe.neverNull
	'message' => '#^Using nullsafe property access on non\\-nullable type AidingApp\\\\Contact\\\\Models\\\\ContactSource\\. Use \\-\\> instead\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Observers/ContactObserver.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method AidingApp\\\\Contact\\\\Rules\\\\UniqueOrganizationDomain\\:\\:message\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Rules/UniqueOrganizationDomain.php',
];
$ignoreErrors[] = [
	// identifier: missingType.property
	'message' => '#^Property AidingApp\\\\Contact\\\\Rules\\\\UniqueOrganizationDomain\\:\\:\\$ignoreId has no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Rules/UniqueOrganizationDomain.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$entity of class AidingApp\\\\Timeline\\\\Events\\\\TimelineableRecordCreated constructor expects Illuminate\\\\Database\\\\Eloquent\\\\Model, AidingApp\\\\Engagement\\\\Models\\\\Educatable given\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/contact/tests/Contact/ListContactTest.php',
];
$ignoreErrors[] = [
	// identifier: method.notFound
	'message' => '#^Call to an undefined method Illuminate\\\\Testing\\\\TestResponse\\:\\:assertCanSeeTableRecords\\(\\)\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/tests/ContactSource/ListContactSourcesTest.php',
];
$ignoreErrors[] = [
	// identifier: method.notFound
	'message' => '#^Call to an undefined method Illuminate\\\\Testing\\\\TestResponse\\:\\:assertCanSeeTableRecords\\(\\)\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/tests/ContactStatus/ListContactStatusesTest.php',
];
$ignoreErrors[] = [
	// identifier: method.notFound
	'message' => '#^Call to an undefined method Faker\\\\Generator\\:\\:state\\(\\)\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/tests/Organization/RequestFactories/CreateOrganizationRequestFactory.php',
];
$ignoreErrors[] = [
	// identifier: method.notFound
	'message' => '#^Call to an undefined method Faker\\\\Generator\\:\\:state\\(\\)\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/tests/Organization/RequestFactories/EditOrganizationRequestFactory.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Class AidingApp\\\\ContractManagement\\\\Database\\\\Factories\\\\ContractFactory extends generic class Illuminate\\\\Database\\\\Eloquent\\\\Factories\\\\Factory but does not specify its types\\: TModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contract-management/database/factories/ContractFactory.php',
];
$ignoreErrors[] = [
	// identifier: phpDoc.parseError
	'message' => '#^PHPDoc tag @extends has invalid value \\(Contract\\>\\)\\: Unexpected token "\\>", expected \'\\<\' at offset 24$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contract-management/database/factories/ContractFactory.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$money of static method Cknow\\\\Money\\\\Money\\:\\:parseByDecimal\\(\\) expects string, int given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contract-management/database/factories/ContractFactory.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Class AidingApp\\\\ContractManagement\\\\Database\\\\Factories\\\\ContractTypeFactory extends generic class Illuminate\\\\Database\\\\Eloquent\\\\Factories\\\\Factory but does not specify its types\\: TModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contract-management/database/factories/ContractTypeFactory.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ContractManagement\\\\Database\\\\Factories\\\\ContractTypeFactory\\:\\:default\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Factories\\\\Factory does not specify its types\\: TModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contract-management/database/factories/ContractTypeFactory.php',
];
$ignoreErrors[] = [
	// identifier: phpDoc.parseError
	'message' => '#^PHPDoc tag @extends has invalid value \\(ContractType\\>\\)\\: Unexpected token "\\>", expected \'\\<\' at offset 28$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contract-management/database/factories/ContractTypeFactory.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#2 \\$guardName of method class@anonymous/app\\-modules/contract\\-management/database/migrations/2024_11_26_170828_seed_permissions_for_contract\\.php\\:42\\:\\:deletePermissions\\(\\) expects string, array given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contract-management/database/migrations/2024_11_26_170828_seed_permissions_for_contract.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Property class@anonymous/app\\-modules/contract\\-management/database/migrations/2024_11_26_170828_seed_permissions_for_contract\\.php\\:42\\:\\:\\$guards type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contract-management/database/migrations/2024_11_26_170828_seed_permissions_for_contract.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Property class@anonymous/app\\-modules/contract\\-management/database/migrations/2024_11_26_170828_seed_permissions_for_contract\\.php\\:42\\:\\:\\$permissions type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contract-management/database/migrations/2024_11_26_170828_seed_permissions_for_contract.php',
];
$ignoreErrors[] = [
	// identifier: missingType.parameter
	'message' => '#^Method AidingApp\\\\ContractManagement\\\\Enums\\\\ContractStatus\\:\\:getStatus\\(\\) has parameter \\$endDate with no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contract-management/src/Enums/ContractStatus.php',
];
$ignoreErrors[] = [
	// identifier: missingType.parameter
	'message' => '#^Method AidingApp\\\\ContractManagement\\\\Enums\\\\ContractStatus\\:\\:getStatus\\(\\) has parameter \\$startDate with no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contract-management/src/Enums/ContractStatus.php',
];
$ignoreErrors[] = [
	// identifier: property.notFound
	'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$contract_value\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contract-management/src/Filament/Resources/ContractResource/Pages/EditContract.php',
];
$ignoreErrors[] = [
	// identifier: varTag.unresolvableType
	'message' => '#^PHPDoc tag @var for variable \\$resource contains unresolvable type\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contract-management/src/Filament/Resources/ContractResource/Pages/EditContract.php',
];
$ignoreErrors[] = [
	// identifier: varTag.unresolvableType
	'message' => '#^PHPDoc tag @var for variable \\$resource contains unresolvable type\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contract-management/src/Filament/Resources/ContractTypeResource/Pages/EditContractType.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ContractManagement\\\\Models\\\\Contract\\:\\:contractType\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contract-management/src/Models/Contract.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\ContractManagement\\\\Models\\\\Contract\\:\\:newBelongsToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contract-management/src/Models/Contract.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\ContractManagement\\\\Models\\\\Contract\\:\\:newMorphToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contract-management/src/Models/Contract.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ContractManagement\\\\Models\\\\Contract\\:\\:status\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Casts\\\\Attribute does not specify its types\\: TGet, TSet$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contract-management/src/Models/Contract.php',
];
$ignoreErrors[] = [
	// identifier: missingType.property
	'message' => '#^Property AidingApp\\\\ContractManagement\\\\Models\\\\Contract\\:\\:\\$append has no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contract-management/src/Models/Contract.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ContractManagement\\\\Models\\\\ContractType\\:\\:contracts\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contract-management/src/Models/ContractType.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\ContractManagement\\\\Models\\\\ContractType\\:\\:newBelongsToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contract-management/src/Models/ContractType.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\ContractManagement\\\\Models\\\\ContractType\\:\\:newMorphToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contract-management/src/Models/ContractType.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method AidingApp\\\\ContractManagement\\\\Providers\\\\ContractManagementServiceProvider\\:\\:boot\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contract-management/src/Providers/ContractManagementServiceProvider.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Division\\\\Database\\\\Factories\\\\DivisionFactory\\:\\:default\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Factories\\\\Factory does not specify its types\\: TModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/division/database/factories/DivisionFactory.php',
];
$ignoreErrors[] = [
	// identifier: varTag.unresolvableType
	'message' => '#^PHPDoc tag @var for variable \\$resource contains unresolvable type\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/division/src/Filament/Resources/DivisionResource/Pages/EditDivision.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Division\\\\Models\\\\Division\\:\\:createdBy\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/division/src/Models/Division.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Division\\\\Models\\\\Division\\:\\:lastUpdatedBy\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/division/src/Models/Division.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\Division\\\\Models\\\\Division\\:\\:newBelongsToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/division/src/Models/Division.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\Division\\\\Models\\\\Division\\:\\:newMorphToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/division/src/Models/Division.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Division\\\\Models\\\\Division\\:\\:notificationSetting\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphOne does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/division/src/Models/Division.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Division\\\\Models\\\\Division\\:\\:teams\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/division/src/Models/Division.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Class AidingApp\\\\Engagement\\\\Database\\\\Factories\\\\EmailTemplateFactory extends generic class Illuminate\\\\Database\\\\Eloquent\\\\Factories\\\\Factory but does not specify its types\\: TModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/database/factories/EmailTemplateFactory.php',
];
$ignoreErrors[] = [
	// identifier: method.notFound
	'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Schema\\\\ForeignKeyDefinition\\:\\:unique\\(\\)\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/database/migrations/2023_08_17_183840_create_engagement_deliverables_table.php',
];
$ignoreErrors[] = [
	// identifier: method.notFound
	'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Schema\\\\ForeignKeyDefinition\\:\\:unique\\(\\)\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/database/migrations/2025_03_06_200002_drop_engagement_deliverables_table.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Property class@anonymous/app\\-modules/engagement/database/migrations/2025_03_11_033340_seed_permissions_remove_engagement_deliverable_permissions\\.php\\:40\\:\\:\\$guards type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/database/migrations/2025_03_11_033340_seed_permissions_remove_engagement_deliverable_permissions.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Property class@anonymous/app\\-modules/engagement/database/migrations/2025_03_11_033340_seed_permissions_remove_engagement_deliverable_permissions\\.php\\:40\\:\\:\\$permissions type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/database/migrations/2025_03_11_033340_seed_permissions_remove_engagement_deliverable_permissions.php',
];
$ignoreErrors[] = [
	// identifier: class.notFound
	'message' => '#^Call to method notify\\(\\) on an unknown class AidingApp\\\\Engagement\\\\Models\\\\Educatable\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Actions/CreateEngagement.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$model of method Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphTo\\<Illuminate\\\\Database\\\\Eloquent\\\\Model,AidingApp\\\\Engagement\\\\Models\\\\Engagement\\>\\:\\:associate\\(\\) expects Illuminate\\\\Database\\\\Eloquent\\\\Model\\|null, AidingApp\\\\Notification\\\\Models\\\\Contracts\\\\CanBeNotified\\|Illuminate\\\\Database\\\\Eloquent\\\\Collection given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Actions/CreateEngagement.php',
];
$ignoreErrors[] = [
	// identifier: assign.propertyType
	'message' => '#^Property AidingApp\\\\Engagement\\\\Models\\\\IdeHelperEngagement\\:\\:\\$scheduled_at \\(Illuminate\\\\Support\\\\Carbon\\|null\\) does not accept Carbon\\\\CarbonInterface\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Actions/CreateEngagement.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Database\\\\Eloquent\\\\Collection\\<\\(int\\|string\\),Illuminate\\\\Database\\\\Eloquent\\\\Model\\>\\:\\:map\\(\\) expects callable\\(Illuminate\\\\Database\\\\Eloquent\\\\Model, int\\|string\\)\\: AidingApp\\\\Engagement\\\\Jobs\\\\CreateBatchedEngagement, Closure\\(AidingApp\\\\Notification\\\\Models\\\\Contracts\\\\CanBeNotified\\)\\: AidingApp\\\\Engagement\\\\Jobs\\\\CreateBatchedEngagement given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Actions/CreateEngagementBatch.php',
];
$ignoreErrors[] = [
	// identifier: assign.propertyType
	'message' => '#^Property AidingApp\\\\Engagement\\\\Models\\\\IdeHelperEngagementBatch\\:\\:\\$scheduled_at \\(Illuminate\\\\Support\\\\Carbon\\|null\\) does not accept Carbon\\\\CarbonInterface\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Actions/CreateEngagementBatch.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Engagement\\\\Actions\\\\GenerateEngagementBodyContent\\:\\:__invoke\\(\\) has parameter \\$content with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Actions/GenerateEngagementBodyContent.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Engagement\\\\Actions\\\\GenerateEngagementBodyContent\\:\\:__invoke\\(\\) has parameter \\$mergeData with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Actions/GenerateEngagementBodyContent.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Engagement\\\\Actions\\\\GenerateTipTapBodyJson\\:\\:__invoke\\(\\) has parameter \\$mergeTags with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Actions/GenerateTipTapBodyJson.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Engagement\\\\Actions\\\\GenerateTipTapBodyJson\\:\\:__invoke\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Actions/GenerateTipTapBodyJson.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Engagement\\\\Actions\\\\GenerateTipTapBodyJson\\:\\:mergeTag\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Actions/GenerateTipTapBodyJson.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Engagement\\\\Actions\\\\GenerateTipTapBodyJson\\:\\:text\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Actions/GenerateTipTapBodyJson.php',
];
$ignoreErrors[] = [
	// identifier: argument.templateType
	'message' => '#^Unable to resolve the template type TKey in call to function collect$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Actions/GenerateTipTapBodyJson.php',
];
$ignoreErrors[] = [
	// identifier: argument.templateType
	'message' => '#^Unable to resolve the template type TValue in call to function collect$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Actions/GenerateTipTapBodyJson.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Engagement\\\\DataTransferObjects\\\\EngagementCreationData\\:\\:__construct\\(\\) has parameter \\$body with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/DataTransferObjects/EngagementCreationData.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Engagement\\\\DataTransferObjects\\\\EngagementCreationData\\:\\:__construct\\(\\) has parameter \\$recipient with generic class Illuminate\\\\Database\\\\Eloquent\\\\Collection but does not specify its types\\: TKey, TModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/DataTransferObjects/EngagementCreationData.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Engagement\\\\DataTransferObjects\\\\EngagementCreationData\\:\\:__construct\\(\\) has parameter \\$temporaryBodyImages with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/DataTransferObjects/EngagementCreationData.php',
];
$ignoreErrors[] = [
	// identifier: property.notFound
	'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Collection\\<int, AidingApp\\\\Team\\\\Models\\\\Team\\>\\:\\:\\$users\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Filament/Actions/BulkEngagementAction.php',
];
$ignoreErrors[] = [
	// identifier: method.notFound
	'message' => '#^Call to an undefined method Filament\\\\Forms\\\\Components\\\\Field\\:\\:getTemporaryImages\\(\\)\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Filament/Actions/BulkEngagementAction.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method AidingApp\\\\Engagement\\\\Filament\\\\Actions\\\\BulkEngagementAction\\:\\:make\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Filament/Actions/BulkEngagementAction.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\$recipient of class AidingApp\\\\Engagement\\\\DataTransferObjects\\\\EngagementCreationData constructor expects AidingApp\\\\Notification\\\\Models\\\\Contracts\\\\CanBeNotified\\|Illuminate\\\\Database\\\\Eloquent\\\\Collection, Illuminate\\\\Support\\\\Collection given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Filament/Actions/BulkEngagementAction.php',
];
$ignoreErrors[] = [
	// identifier: property.notFound
	'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Collection\\<int, AidingApp\\\\Team\\\\Models\\\\Team\\>\\:\\:\\$users\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Filament/Actions/RelationManagerSendEngagementAction.php',
];
$ignoreErrors[] = [
	// identifier: method.notFound
	'message' => '#^Call to an undefined method Filament\\\\Forms\\\\Components\\\\Field\\:\\:getTemporaryImages\\(\\)\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Filament/Actions/RelationManagerSendEngagementAction.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\$recipient of class AidingApp\\\\Engagement\\\\DataTransferObjects\\\\EngagementCreationData constructor expects AidingApp\\\\Notification\\\\Models\\\\Contracts\\\\CanBeNotified\\|Illuminate\\\\Database\\\\Eloquent\\\\Collection, Illuminate\\\\Database\\\\Eloquent\\\\Model given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Filament/Actions/RelationManagerSendEngagementAction.php',
];
$ignoreErrors[] = [
	// identifier: varTag.unresolvableType
	'message' => '#^PHPDoc tag @var for variable \\$resource contains unresolvable type\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Filament/Resources/EmailTemplateResource/Pages/EditEmailTemplate.php',
];
$ignoreErrors[] = [
	// identifier: varTag.unresolvableType
	'message' => '#^PHPDoc tag @var for variable \\$resource contains unresolvable type\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Filament/Resources/EngagementFileResource/Pages/EditEngagementFile.php',
];
$ignoreErrors[] = [
	// identifier: property.nonObject
	'message' => '#^Cannot access property \\$id on Illuminate\\\\Database\\\\Eloquent\\\\Model\\|int\\|string\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Filament/Resources/EngagementFileResource/Pages/ViewEngagementFile.php',
];
$ignoreErrors[] = [
	// identifier: property.notFound
	'message' => '#^Access to an undefined property AidingApp\\\\Contact\\\\Models\\\\Contact\\:\\:\\$full\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Filament/Resources/EngagementResponseResource/Pages/ViewEngagementResponse.php',
];
$ignoreErrors[] = [
	// identifier: match.unhandled
	'message' => '#^Match expression does not handle remaining value\\: class\\-string\\<AidingApp\\\\Contact\\\\Models\\\\Contact\\>&literal\\-string$#',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/engagement/src/Filament/Resources/EngagementResponseResource/Pages/ViewEngagementResponse.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method AidingApp\\\\Engagement\\\\Http\\\\Controllers\\\\EngagementFileDownloadController\\:\\:__invoke\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Http/Controllers/EngagementFileDownloadController.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Engagement\\\\Http\\\\Requests\\\\EngagementFileDownloadRequest\\:\\:rules\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Http/Requests/EngagementFileDownloadRequest.php',
];
$ignoreErrors[] = [
	// identifier: class.notFound
	'message' => '#^Call to method notifyNow\\(\\) on an unknown class AidingApp\\\\Engagement\\\\Models\\\\Educatable\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Jobs/CreateBatchedEngagement.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$model of method Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphTo\\<Illuminate\\\\Database\\\\Eloquent\\\\Model,AidingApp\\\\Engagement\\\\Models\\\\Engagement\\>\\:\\:associate\\(\\) expects Illuminate\\\\Database\\\\Eloquent\\\\Model\\|null, AidingApp\\\\Notification\\\\Models\\\\Contracts\\\\CanBeNotified given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Jobs/CreateBatchedEngagement.php',
];
$ignoreErrors[] = [
	// identifier: class.notFound
	'message' => '#^Call to method notify\\(\\) on an unknown class AidingApp\\\\Engagement\\\\Models\\\\Educatable\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Jobs/DeliverEngagements.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Engagement\\\\Jobs\\\\DeliverEngagements\\:\\:middleware\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Jobs/DeliverEngagements.php',
];
$ignoreErrors[] = [
	// identifier: missingType.property
	'message' => '#^Property AidingApp\\\\Engagement\\\\Jobs\\\\GatherAndDispatchSesS3InboundEmails\\:\\:\\$uniqueFor has no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Jobs/GatherAndDispatchSesS3InboundEmails.php',
];
$ignoreErrors[] = [
	// identifier: class.notFound
	'message' => '#^Call to method addMediaFromStream\\(\\) on an unknown class AidingApp\\\\Engagement\\\\Jobs\\\\EngagementResponse\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Jobs/ProcessSesS3InboundEmail.php',
];
$ignoreErrors[] = [
	// identifier: class.notFound
	'message' => '#^PHPDoc tag @var for variable \\$engagementResponse contains unknown class AidingApp\\\\Engagement\\\\Jobs\\\\EngagementResponse\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Jobs/ProcessSesS3InboundEmail.php',
];
$ignoreErrors[] = [
	// identifier: missingType.property
	'message' => '#^Property AidingApp\\\\Engagement\\\\Jobs\\\\ProcessSesS3InboundEmail\\:\\:\\$uniqueFor has no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Jobs/ProcessSesS3InboundEmail.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Engagement\\\\Models\\\\EmailTemplate\\:\\:user\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Models/EmailTemplate.php',
];
$ignoreErrors[] = [
	// identifier: method.notFound
	'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:orderedEngagements\\(\\)\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Models/Engagement.php',
];
$ignoreErrors[] = [
	// identifier: class.notFound
	'message' => '#^Call to method displayEmailKey\\(\\) on an unknown class AidingApp\\\\Engagement\\\\Models\\\\Educatable\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Models/Engagement.php',
];
$ignoreErrors[] = [
	// identifier: class.notFound
	'message' => '#^Call to method displayNameKey\\(\\) on an unknown class AidingApp\\\\Engagement\\\\Models\\\\Educatable\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Models/Engagement.php',
];
$ignoreErrors[] = [
	// identifier: class.notFound
	'message' => '#^Call to method getAttribute\\(\\) on an unknown class AidingApp\\\\Engagement\\\\Models\\\\Educatable\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/engagement/src/Models/Engagement.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Engagement\\\\Models\\\\Engagement\\:\\:batch\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Models/Engagement.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Engagement\\\\Models\\\\Engagement\\:\\:createdBy\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Models/Engagement.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Engagement\\\\Models\\\\Engagement\\:\\:emailMessages\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Models/Engagement.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Engagement\\\\Models\\\\Engagement\\:\\:engagementBatch\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Models/Engagement.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Engagement\\\\Models\\\\Engagement\\:\\:getMergeData\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Models/Engagement.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Engagement\\\\Models\\\\Engagement\\:\\:getMergeTags\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Models/Engagement.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Engagement\\\\Models\\\\Engagement\\:\\:getTimelineData\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Models/Engagement.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Engagement\\\\Models\\\\Engagement\\:\\:latestEmailMessage\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphOne does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Models/Engagement.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\Engagement\\\\Models\\\\Engagement\\:\\:newBelongsToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Models/Engagement.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\Engagement\\\\Models\\\\Engagement\\:\\:newMorphToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Models/Engagement.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Engagement\\\\Models\\\\Engagement\\:\\:recipient\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Models/Engagement.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Engagement\\\\Models\\\\Engagement\\:\\:scopeIsNotPartOfABatch\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Models/Engagement.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Engagement\\\\Models\\\\Engagement\\:\\:scopeLicensedToEducatable\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Models/Engagement.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Engagement\\\\Models\\\\Engagement\\:\\:scopeLicensedToEducatable\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder does not specify its types\\: TModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Models/Engagement.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Engagement\\\\Models\\\\Engagement\\:\\:scopeSentToContact\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Models/Engagement.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Engagement\\\\Models\\\\Engagement\\:\\:timelineRecord\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphOne does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Models/Engagement.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Engagement\\\\Models\\\\Engagement\\:\\:user\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Models/Engagement.php',
];
$ignoreErrors[] = [
	// identifier: method.notFound
	'message' => '#^Call to an undefined method DOMNode\\:\\:getAttribute\\(\\)\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Models/EngagementBatch.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Engagement\\\\Models\\\\EngagementBatch\\:\\:engagements\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Models/EngagementBatch.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Engagement\\\\Models\\\\EngagementBatch\\:\\:user\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Models/EngagementBatch.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Engagement\\\\Models\\\\EngagementFile\\:\\:contacts\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Models/EngagementFile.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\Engagement\\\\Models\\\\EngagementFile\\:\\:newBelongsToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Models/EngagementFile.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\Engagement\\\\Models\\\\EngagementFile\\:\\:newMorphToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Models/EngagementFile.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Engagement\\\\Models\\\\EngagementFile\\:\\:prunable\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder does not specify its types\\: TModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Models/EngagementFile.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Engagement\\\\Models\\\\EngagementFileEntities\\:\\:engagementFile\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Models/EngagementFileEntities.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Engagement\\\\Models\\\\EngagementFileEntities\\:\\:entity\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Models/EngagementFileEntities.php',
];
$ignoreErrors[] = [
	// identifier: method.notFound
	'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:orderedEngagementResponses\\(\\)\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Models/EngagementResponse.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Engagement\\\\Models\\\\EngagementResponse\\:\\:getTimelineData\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Models/EngagementResponse.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\Engagement\\\\Models\\\\EngagementResponse\\:\\:newBelongsToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Models/EngagementResponse.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\Engagement\\\\Models\\\\EngagementResponse\\:\\:newMorphToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Models/EngagementResponse.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Engagement\\\\Models\\\\EngagementResponse\\:\\:sender\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Models/EngagementResponse.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Engagement\\\\Models\\\\EngagementResponse\\:\\:timelineRecord\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphOne does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Models/EngagementResponse.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Engagement\\\\Notifications\\\\EngagementBatchFinishedNotification\\:\\:toDatabase\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Notifications/EngagementBatchFinishedNotification.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Engagement\\\\Notifications\\\\EngagementBatchStartedNotification\\:\\:toDatabase\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Notifications/EngagementBatchStartedNotification.php',
];
$ignoreErrors[] = [
	// identifier: class.notFound
	'message' => '#^Access to property \\$display_name on an unknown class AidingApp\\\\Engagement\\\\Models\\\\Educatable\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/engagement/src/Notifications/EngagementFailedNotification.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Engagement\\\\Notifications\\\\EngagementFailedNotification\\:\\:toDatabase\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Notifications/EngagementFailedNotification.php',
];
$ignoreErrors[] = [
	// identifier: class.notFound
	'message' => '#^Access to property \\$display_name on an unknown class AidingApp\\\\Engagement\\\\Models\\\\Educatable\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Notifications/EngagementNotification.php',
];
$ignoreErrors[] = [
	// identifier: method.notFound
	'message' => '#^Call to an undefined method AidingApp\\\\Engagement\\\\Models\\\\Engagement\\:\\:getBodyMarkdown\\(\\)\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Notifications/EngagementNotification.php',
];
$ignoreErrors[] = [
	// identifier: match.unhandled
	'message' => '#^Match expression does not handle remaining value\\: AidingApp\\\\Notification\\\\Enums\\\\NotificationChannel\\:\\:Database$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Notifications/EngagementNotification.php',
];
$ignoreErrors[] = [
	// identifier: function.impossibleType
	'message' => '#^Call to function is_null\\(\\) with string will always evaluate to false\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Observers/EngagementBatchObserver.php',
];
$ignoreErrors[] = [
	// identifier: booleanAnd.alwaysFalse
	'message' => '#^Result of && is always false\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Observers/EngagementBatchObserver.php',
];
$ignoreErrors[] = [
	// identifier: class.notFound
	'message' => '#^Call to method getLicenseType\\(\\) on an unknown class AidingApp\\\\Engagement\\\\Models\\\\Educatable\\.$#',
	'count' => 5,
	'path' => __DIR__ . '/app-modules/engagement/src/Policies/EngagementPolicy.php',
];
$ignoreErrors[] = [
	// identifier: nullsafe.neverNull
	'message' => '#^Using nullsafe method call on non\\-nullable type AidingApp\\\\Engagement\\\\Models\\\\Educatable\\. Use \\-\\> instead\\.$#',
	'count' => 5,
	'path' => __DIR__ . '/app-modules/engagement/src/Policies/EngagementPolicy.php',
];
$ignoreErrors[] = [
	// identifier: property.notFound
	'message' => '#^Access to an undefined property Pest\\\\Expectation\\<AidingApp\\\\Engagement\\\\Models\\\\EngagementBatch\\|null\\>\\:\\:\\$user\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/tests/Actions/CreateEngagementBatchTest.php',
];
$ignoreErrors[] = [
	// identifier: property.notFound
	'message' => '#^Access to an undefined property Pest\\\\Expectation\\<AidingApp\\\\Engagement\\\\Models\\\\Engagement\\|null\\>\\:\\:\\$user\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/engagement/tests/Actions/CreateEngagementTest.php',
];
$ignoreErrors[] = [
	// identifier: property.notFound
	'message' => '#^Access to an undefined property Pest\\\\Expectation\\<AidingApp\\\\Engagement\\\\Models\\\\Engagement\\|null\\>\\:\\:\\$engagementBatch\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/engagement/tests/Jobs/CreateBatchedEngagementTest.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Form\\\\Actions\\\\GenerateFormKitSchema\\:\\:__invoke\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Actions/GenerateFormKitSchema.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Form\\\\Actions\\\\GenerateFormKitSchema\\:\\:content\\(\\) has parameter \\$blocks with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Actions/GenerateFormKitSchema.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Form\\\\Actions\\\\GenerateFormKitSchema\\:\\:content\\(\\) has parameter \\$content with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Actions/GenerateFormKitSchema.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Form\\\\Actions\\\\GenerateFormKitSchema\\:\\:content\\(\\) has parameter \\$fields with generic class Illuminate\\\\Database\\\\Eloquent\\\\Collection but does not specify its types\\: TKey, TModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Actions/GenerateFormKitSchema.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Form\\\\Actions\\\\GenerateFormKitSchema\\:\\:content\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Actions/GenerateFormKitSchema.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Form\\\\Actions\\\\GenerateFormKitSchema\\:\\:generateContent\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Actions/GenerateFormKitSchema.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Form\\\\Actions\\\\GenerateFormKitSchema\\:\\:grid\\(\\) has parameter \\$blocks with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Actions/GenerateFormKitSchema.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Form\\\\Actions\\\\GenerateFormKitSchema\\:\\:grid\\(\\) has parameter \\$component with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Actions/GenerateFormKitSchema.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Form\\\\Actions\\\\GenerateFormKitSchema\\:\\:grid\\(\\) has parameter \\$fields with generic class Illuminate\\\\Database\\\\Eloquent\\\\Collection but does not specify its types\\: TKey, TModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Actions/GenerateFormKitSchema.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Form\\\\Actions\\\\GenerateFormKitSchema\\:\\:grid\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Actions/GenerateFormKitSchema.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Form\\\\Actions\\\\GenerateFormKitSchema\\:\\:text\\(\\) has parameter \\$component with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Actions/GenerateFormKitSchema.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Form\\\\Actions\\\\GenerateFormKitSchema\\:\\:text\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Actions/GenerateFormKitSchema.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Form\\\\Actions\\\\GenerateFormKitSchema\\:\\:wizardContent\\(\\) has parameter \\$blocks with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Actions/GenerateFormKitSchema.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Form\\\\Actions\\\\GenerateFormKitSchema\\:\\:wizardContent\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Actions/GenerateFormKitSchema.php',
];
$ignoreErrors[] = [
	// identifier: function.impossibleType
	'message' => '#^Call to function is_null\\(\\) with string will always evaluate to false\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Actions/GenerateSubmissibleValidation.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Form\\\\Actions\\\\GenerateSubmissibleValidation\\:\\:__invoke\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Actions/GenerateSubmissibleValidation.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Form\\\\Actions\\\\GenerateSubmissibleValidation\\:\\:fields\\(\\) has parameter \\$blocks with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Actions/GenerateSubmissibleValidation.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Form\\\\Actions\\\\GenerateSubmissibleValidation\\:\\:fields\\(\\) has parameter \\$fields with generic class Illuminate\\\\Database\\\\Eloquent\\\\Collection but does not specify its types\\: TKey, TModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Actions/GenerateSubmissibleValidation.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Form\\\\Actions\\\\GenerateSubmissibleValidation\\:\\:fields\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Actions/GenerateSubmissibleValidation.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Form\\\\Actions\\\\GenerateSubmissibleValidation\\:\\:wizardRules\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Actions/GenerateSubmissibleValidation.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Database\\\\Eloquent\\\\Collection\\<\\(int\\|string\\),Illuminate\\\\Database\\\\Eloquent\\\\Model\\>\\:\\:mapWithKeys\\(\\) expects callable\\(Illuminate\\\\Database\\\\Eloquent\\\\Model, int\\|string\\)\\: array\\<array\\>, Closure\\(AidingApp\\\\Form\\\\Models\\\\SubmissibleField\\)\\: array\\<array\\> given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Actions/GenerateSubmissibleValidation.php',
];
$ignoreErrors[] = [
	// identifier: property.notFound
	'message' => '#^Access to an undefined property AidingApp\\\\Form\\\\Models\\\\SubmissibleField\\:\\:\\$pivot\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Actions/InjectSubmissionStateIntoTipTapContent.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Form\\\\Actions\\\\InjectSubmissionStateIntoTipTapContent\\:\\:__invoke\\(\\) has parameter \\$blocks with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Actions/InjectSubmissionStateIntoTipTapContent.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Form\\\\Actions\\\\InjectSubmissionStateIntoTipTapContent\\:\\:__invoke\\(\\) has parameter \\$content with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Actions/InjectSubmissionStateIntoTipTapContent.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Form\\\\Actions\\\\InjectSubmissionStateIntoTipTapContent\\:\\:__invoke\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Actions/InjectSubmissionStateIntoTipTapContent.php',
];
$ignoreErrors[] = [
	// identifier: match.unhandled
	'message' => '#^Match expression does not handle remaining value\\: class\\-string\\<AidingApp\\\\Form\\\\Models\\\\Submissible\\>&literal\\-string$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Actions/ResolveBlockRegistry.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Form\\\\Actions\\\\ResolveBlockRegistry\\:\\:__invoke\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Actions/ResolveBlockRegistry.php',
];
$ignoreErrors[] = [
	// identifier: if.alwaysTrue
	'message' => '#^If condition is always true\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Actions/ResolveSubmissionAuthorFromEmail.php',
];
$ignoreErrors[] = [
	// identifier: deadCode.unreachable
	'message' => '#^Unreachable statement \\- code above always terminates\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Actions/ResolveSubmissionAuthorFromEmail.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Class AidingApp\\\\Form\\\\Exports\\\\FormSubmissionExport implements generic interface Maatwebsite\\\\Excel\\\\Concerns\\\\WithMapping but does not specify its types\\: RowType$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Exports/FormSubmissionExport.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Form\\\\Exports\\\\FormSubmissionExport\\:\\:__construct\\(\\) has parameter \\$submissions with generic class Illuminate\\\\Support\\\\Collection but does not specify its types\\: TKey, TValue$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Exports/FormSubmissionExport.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Form\\\\Exports\\\\FormSubmissionExport\\:\\:collection\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Exports/FormSubmissionExport.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Form\\\\Exports\\\\FormSubmissionExport\\:\\:headings\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Exports/FormSubmissionExport.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Form\\\\Exports\\\\FormSubmissionExport\\:\\:map\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Exports/FormSubmissionExport.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Form\\\\Filament\\\\Blocks\\\\CheckboxFormFieldBlock\\:\\:fields\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/CheckboxFormFieldBlock.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Form\\\\Filament\\\\Blocks\\\\CheckboxFormFieldBlock\\:\\:getFormKitSchema\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/CheckboxFormFieldBlock.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Form\\\\Filament\\\\Blocks\\\\CheckboxFormFieldBlock\\:\\:getValidationRules\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/CheckboxFormFieldBlock.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Form\\\\Filament\\\\Blocks\\\\DateFormFieldBlock\\:\\:fields\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/DateFormFieldBlock.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Form\\\\Filament\\\\Blocks\\\\DateFormFieldBlock\\:\\:getFormKitSchema\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/DateFormFieldBlock.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Form\\\\Filament\\\\Blocks\\\\DateFormFieldBlock\\:\\:getValidationRules\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/DateFormFieldBlock.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Form\\\\Filament\\\\Blocks\\\\EducatableEmailFormFieldBlock\\:\\:getFormKitSchema\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/EducatableEmailFormFieldBlock.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Form\\\\Filament\\\\Blocks\\\\EducatableEmailFormFieldBlock\\:\\:getFormSchema\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/EducatableEmailFormFieldBlock.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Form\\\\Filament\\\\Blocks\\\\EducatableEmailFormFieldBlock\\:\\:getSubmissionState\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/EducatableEmailFormFieldBlock.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Form\\\\Filament\\\\Blocks\\\\EducatableEmailFormFieldBlock\\:\\:getValidationRules\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/EducatableEmailFormFieldBlock.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Form\\\\Filament\\\\Blocks\\\\EmailFormFieldBlock\\:\\:fields\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/EmailFormFieldBlock.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Form\\\\Filament\\\\Blocks\\\\EmailFormFieldBlock\\:\\:getFormKitSchema\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/EmailFormFieldBlock.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Form\\\\Filament\\\\Blocks\\\\EmailFormFieldBlock\\:\\:getValidationRules\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/EmailFormFieldBlock.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Form\\\\Filament\\\\Blocks\\\\FormFieldBlock\\:\\:fields\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/FormFieldBlock.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Form\\\\Filament\\\\Blocks\\\\FormFieldBlock\\:\\:getFormKitSchema\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/FormFieldBlock.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Form\\\\Filament\\\\Blocks\\\\FormFieldBlock\\:\\:getFormSchema\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/FormFieldBlock.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Form\\\\Filament\\\\Blocks\\\\FormFieldBlock\\:\\:getSubmissionState\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/FormFieldBlock.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Form\\\\Filament\\\\Blocks\\\\FormFieldBlock\\:\\:getValidationRules\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/FormFieldBlock.php',
];
$ignoreErrors[] = [
	// identifier: varTag.variableNotFound
	'message' => '#^Variable \\$block in PHPDoc tag @var does not exist\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/FormFieldBlockRegistry.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Form\\\\Filament\\\\Blocks\\\\NumberFormFieldBlock\\:\\:fields\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/NumberFormFieldBlock.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Form\\\\Filament\\\\Blocks\\\\NumberFormFieldBlock\\:\\:getFormKitSchema\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/NumberFormFieldBlock.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Form\\\\Filament\\\\Blocks\\\\NumberFormFieldBlock\\:\\:getValidationRules\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/NumberFormFieldBlock.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Form\\\\Filament\\\\Blocks\\\\PhoneFormFieldBlock\\:\\:fields\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/PhoneFormFieldBlock.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Form\\\\Filament\\\\Blocks\\\\PhoneFormFieldBlock\\:\\:getFormKitSchema\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/PhoneFormFieldBlock.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Form\\\\Filament\\\\Blocks\\\\PhoneFormFieldBlock\\:\\:getValidationRules\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/PhoneFormFieldBlock.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Form\\\\Filament\\\\Blocks\\\\RadioFormFieldBlock\\:\\:fields\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/RadioFormFieldBlock.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Form\\\\Filament\\\\Blocks\\\\RadioFormFieldBlock\\:\\:getFormKitSchema\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/RadioFormFieldBlock.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Form\\\\Filament\\\\Blocks\\\\RadioFormFieldBlock\\:\\:getValidationRules\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/RadioFormFieldBlock.php',
];
$ignoreErrors[] = [
	// identifier: argument.templateType
	'message' => '#^Unable to resolve the template type TKey in call to function collect$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/RadioFormFieldBlock.php',
];
$ignoreErrors[] = [
	// identifier: argument.templateType
	'message' => '#^Unable to resolve the template type TValue in call to function collect$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/RadioFormFieldBlock.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Form\\\\Filament\\\\Blocks\\\\SelectFormFieldBlock\\:\\:fields\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/SelectFormFieldBlock.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Form\\\\Filament\\\\Blocks\\\\SelectFormFieldBlock\\:\\:getFormKitSchema\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/SelectFormFieldBlock.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Form\\\\Filament\\\\Blocks\\\\SelectFormFieldBlock\\:\\:getValidationRules\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/SelectFormFieldBlock.php',
];
$ignoreErrors[] = [
	// identifier: argument.templateType
	'message' => '#^Unable to resolve the template type TKey in call to function collect$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/SelectFormFieldBlock.php',
];
$ignoreErrors[] = [
	// identifier: argument.templateType
	'message' => '#^Unable to resolve the template type TValue in call to function collect$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/SelectFormFieldBlock.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Form\\\\Filament\\\\Blocks\\\\SignatureFormFieldBlock\\:\\:fields\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/SignatureFormFieldBlock.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Form\\\\Filament\\\\Blocks\\\\SignatureFormFieldBlock\\:\\:getFormKitSchema\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/SignatureFormFieldBlock.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Form\\\\Filament\\\\Blocks\\\\TextAreaFormFieldBlock\\:\\:fields\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/TextAreaFormFieldBlock.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Form\\\\Filament\\\\Blocks\\\\TextAreaFormFieldBlock\\:\\:getFormKitSchema\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/TextAreaFormFieldBlock.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Form\\\\Filament\\\\Blocks\\\\TextAreaFormFieldBlock\\:\\:getValidationRules\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/TextAreaFormFieldBlock.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Form\\\\Filament\\\\Blocks\\\\TextInputFormFieldBlock\\:\\:fields\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/TextInputFormFieldBlock.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Form\\\\Filament\\\\Blocks\\\\TextInputFormFieldBlock\\:\\:getFormKitSchema\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/TextInputFormFieldBlock.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Form\\\\Filament\\\\Blocks\\\\TextInputFormFieldBlock\\:\\:getValidationRules\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/TextInputFormFieldBlock.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Form\\\\Filament\\\\Blocks\\\\TimeFormFieldBlock\\:\\:fields\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/TimeFormFieldBlock.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Form\\\\Filament\\\\Blocks\\\\TimeFormFieldBlock\\:\\:getFormKitSchema\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/TimeFormFieldBlock.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Form\\\\Filament\\\\Blocks\\\\UploadFormFieldBlock\\:\\:fields\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/UploadFormFieldBlock.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Form\\\\Filament\\\\Blocks\\\\UploadFormFieldBlock\\:\\:getFormKitSchema\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/UploadFormFieldBlock.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Form\\\\Filament\\\\Blocks\\\\UploadFormFieldBlock\\:\\:getValidationRules\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/UploadFormFieldBlock.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Form\\\\Filament\\\\Blocks\\\\UrlFormFieldBlock\\:\\:fields\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/UrlFormFieldBlock.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Form\\\\Filament\\\\Blocks\\\\UrlFormFieldBlock\\:\\:getFormKitSchema\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/UrlFormFieldBlock.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Form\\\\Filament\\\\Blocks\\\\UrlFormFieldBlock\\:\\:getValidationRules\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/UrlFormFieldBlock.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Class AidingApp\\\\Form\\\\Models\\\\Submissible uses generic trait Illuminate\\\\Database\\\\Eloquent\\\\Factories\\\\HasFactory but does not specify its types\\: TFactory$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Models/Submissible.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Form\\\\Models\\\\Submissible\\:\\:allowedDomains\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Casts\\\\Attribute does not specify its types\\: TGet, TSet$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Models/Submissible.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Form\\\\Models\\\\Submissible\\:\\:content\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Casts\\\\Attribute does not specify its types\\: TGet, TSet$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Models/Submissible.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Form\\\\Models\\\\Submissible\\:\\:embedEnabled\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Casts\\\\Attribute does not specify its types\\: TGet, TSet$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Models/Submissible.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Form\\\\Models\\\\Submissible\\:\\:fields\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Models/Submissible.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Form\\\\Models\\\\Submissible\\:\\:isWizard\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Casts\\\\Attribute does not specify its types\\: TGet, TSet$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Models/Submissible.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Form\\\\Models\\\\Submissible\\:\\:name\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Casts\\\\Attribute does not specify its types\\: TGet, TSet$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Models/Submissible.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Form\\\\Models\\\\Submissible\\:\\:steps\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Models/Submissible.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Form\\\\Models\\\\Submissible\\:\\:submissions\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Models/Submissible.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Form\\\\Models\\\\SubmissibleAuthentication\\:\\:author\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Models/SubmissibleAuthentication.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Form\\\\Models\\\\SubmissibleAuthentication\\:\\:prunable\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder does not specify its types\\: TModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Models/SubmissibleAuthentication.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Form\\\\Models\\\\SubmissibleAuthentication\\:\\:submissible\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Models/SubmissibleAuthentication.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Form\\\\Models\\\\SubmissibleField\\:\\:config\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Casts\\\\Attribute does not specify its types\\: TGet, TSet$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Models/SubmissibleField.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Form\\\\Models\\\\SubmissibleField\\:\\:isRequired\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Casts\\\\Attribute does not specify its types\\: TGet, TSet$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Models/SubmissibleField.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Form\\\\Models\\\\SubmissibleField\\:\\:label\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Casts\\\\Attribute does not specify its types\\: TGet, TSet$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Models/SubmissibleField.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Form\\\\Models\\\\SubmissibleField\\:\\:step\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Models/SubmissibleField.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Form\\\\Models\\\\SubmissibleField\\:\\:submissible\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Models/SubmissibleField.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Form\\\\Models\\\\SubmissibleField\\:\\:type\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Casts\\\\Attribute does not specify its types\\: TGet, TSet$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Models/SubmissibleField.php',
];
$ignoreErrors[] = [
	// identifier: property.notFound
	'message' => '#^Access to an undefined property AidingApp\\\\Form\\\\Models\\\\SubmissibleStep\\:\\:\\$sort\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Models/SubmissibleStep.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Form\\\\Models\\\\SubmissibleStep\\:\\:content\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Casts\\\\Attribute does not specify its types\\: TGet, TSet$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Models/SubmissibleStep.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Form\\\\Models\\\\SubmissibleStep\\:\\:fields\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Models/SubmissibleStep.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Form\\\\Models\\\\SubmissibleStep\\:\\:label\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Casts\\\\Attribute does not specify its types\\: TGet, TSet$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Models/SubmissibleStep.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Form\\\\Models\\\\SubmissibleStep\\:\\:submissible\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Models/SubmissibleStep.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Form\\\\Models\\\\Submission\\:\\:author\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Models/Submission.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Form\\\\Models\\\\Submission\\:\\:fields\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Models/Submission.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Form\\\\Models\\\\Submission\\:\\:scopeLicensedToEducatable\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Models/Submission.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Form\\\\Models\\\\Submission\\:\\:scopeLicensedToEducatable\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder does not specify its types\\: TModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Models/Submission.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Form\\\\Models\\\\Submission\\:\\:submissible\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Models/Submission.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Form\\\\Notifications\\\\AuthenticateFormNotification\\:\\:identifyRecipient\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Notifications/AuthenticateFormNotification.php',
];
$ignoreErrors[] = [
	// identifier: property.notFound
	'message' => '#^Access to an undefined property AidingApp\\\\Form\\\\Models\\\\Submission\\:\\:\\$request_note\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/form/src/Notifications/FormSubmissionRequestNotification.php',
];
$ignoreErrors[] = [
	// identifier: property.notFound
	'message' => '#^Access to an undefined property AidingApp\\\\Form\\\\Models\\\\Submission\\:\\:\\$requester\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/form/src/Notifications/FormSubmissionRequestNotification.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\InAppCommunication\\\\Actions\\\\CheckConversationMessageContentForMention\\:\\:__invoke\\(\\) has parameter \\$content with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/in-app-communication/src/Actions/CheckConversationMessageContentForMention.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\InAppCommunication\\\\Actions\\\\ConvertMessageJsonToText\\:\\:__invoke\\(\\) has parameter \\$content with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/in-app-communication/src/Actions/ConvertMessageJsonToText.php',
];
$ignoreErrors[] = [
	// identifier: varTag.differentVariable
	'message' => '#^Variable \\$user in PHPDoc tag @var does not match assigned variable \\$creator\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/in-app-communication/src/Actions/CreateTwilioConversation.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\InAppCommunication\\\\Events\\\\ConversationMessageSent\\:\\:__construct\\(\\) has parameter \\$messageContent with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/in-app-communication/src/Events/ConversationMessageSent.php',
];
$ignoreErrors[] = [
	// identifier: if.alwaysTrue
	'message' => '#^If condition is always true\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/in-app-communication/src/Filament/Pages/UserChat.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\InAppCommunication\\\\Filament\\\\Pages\\\\UserChat\\:\\:conversations\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Collection does not specify its types\\: TKey, TModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/in-app-communication/src/Filament/Pages/UserChat.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method AidingApp\\\\InAppCommunication\\\\Filament\\\\Pages\\\\UserChat\\:\\:joinChannelsAction\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/in-app-communication/src/Filament/Pages/UserChat.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\InAppCommunication\\\\Filament\\\\Pages\\\\UserChat\\:\\:onMessageSent\\(\\) has parameter \\$messageContent with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/in-app-communication/src/Filament/Pages/UserChat.php',
];
$ignoreErrors[] = [
	// identifier: booleanNot.alwaysFalse
	'message' => '#^Negated boolean expression is always false\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/in-app-communication/src/Filament/Pages/UserChat.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$relations of method Illuminate\\\\Database\\\\Eloquent\\\\Builder\\<AidingApp\\\\InAppCommunication\\\\Models\\\\TwilioConversation\\>\\:\\:with\\(\\) expects array\\<array\\|\\(Closure\\(Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\Relation\\<\\*, \\*, \\*\\>\\)\\: mixed\\)\\|string\\>\\|string, array\\{participants\\: Closure\\(Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\)\\: Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\} given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/in-app-communication/src/Filament/Pages/UserChat.php',
];
$ignoreErrors[] = [
	// identifier: assign.propertyType
	'message' => '#^Property AidingApp\\\\InAppCommunication\\\\Models\\\\IdeHelperTwilioConversationUser\\:\\:\\$last_read_at \\(Carbon\\\\CarbonImmutable\\|null\\) does not accept Illuminate\\\\Support\\\\Carbon\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/in-app-communication/src/Filament/Pages/UserChat.php',
];
$ignoreErrors[] = [
	// identifier: argument.templateType
	'message' => '#^Unable to resolve the template type TKey in call to function collect$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/in-app-communication/src/Filament/Pages/UserChat.php',
];
$ignoreErrors[] = [
	// identifier: argument.templateType
	'message' => '#^Unable to resolve the template type TValue in call to function collect$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/in-app-communication/src/Filament/Pages/UserChat.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$components of method Filament\\\\Panel\\:\\:livewireComponents\\(\\) expects array\\<string, class\\-string\\<Livewire\\\\Component\\>\\>, array\\{\'AidingApp\\\\\\\\InAppCommunication\\\\\\\\Livewire\\\\\\\\ChatNotifications\'\\} given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/in-app-communication/src/InAppCommunicationPlugin.php',
];
$ignoreErrors[] = [
	// identifier: property.notFound
	'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Collection\\<int, App\\\\Models\\\\User\\>\\:\\:\\$participant\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/in-app-communication/src/Jobs/NotifyConversationParticipant.php',
];
$ignoreErrors[] = [
	// identifier: nullsafe.neverNull
	'message' => '#^Using nullsafe property access on non\\-nullable type Illuminate\\\\Database\\\\Eloquent\\\\Collection\\<int, App\\\\Models\\\\User\\>\\. Use \\-\\> instead\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/in-app-communication/src/Jobs/NotifyConversationParticipant.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\InAppCommunication\\\\Livewire\\\\ChatNotifications\\:\\:getNotifications\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Collection does not specify its types\\: TKey, TModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/in-app-communication/src/Livewire/ChatNotifications.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\InAppCommunication\\\\Models\\\\TwilioConversation\\:\\:managers\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/in-app-communication/src/Models/TwilioConversation.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\InAppCommunication\\\\Models\\\\TwilioConversation\\:\\:participants\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/in-app-communication/src/Models/TwilioConversation.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\InAppCommunication\\\\Models\\\\TwilioConversationUser\\:\\:conversation\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/in-app-communication/src/Models/TwilioConversationUser.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\InAppCommunication\\\\Models\\\\TwilioConversationUser\\:\\:user\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/in-app-communication/src/Models/TwilioConversationUser.php',
];
$ignoreErrors[] = [
	// identifier: assign.propertyType
	'message' => '#^Property AidingApp\\\\InAppCommunication\\\\Models\\\\IdeHelperTwilioConversationUser\\:\\:\\$last_read_at \\(Carbon\\\\CarbonImmutable\\|null\\) does not accept Illuminate\\\\Support\\\\Carbon\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/in-app-communication/src/Models/TwilioConversationUser.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method AidingApp\\\\InAppCommunication\\\\Providers\\\\InAppCommunicationServiceProvider\\:\\:boot\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/in-app-communication/src/Providers/InAppCommunicationServiceProvider.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\IntegrationAwsSesEventHandling\\\\DataTransferObjects\\\\SesBounceData\\:\\:__construct\\(\\) has parameter \\$bouncedRecipients with generic class Spatie\\\\LaravelData\\\\DataCollection but does not specify its types\\: TKey, TValue$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/integration-aws-ses-event-handling/src/DataTransferObjects/SesBounceData.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\IntegrationAwsSesEventHandling\\\\DataTransferObjects\\\\SesClickData\\:\\:__construct\\(\\) has parameter \\$linkTags with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/integration-aws-ses-event-handling/src/DataTransferObjects/SesClickData.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\IntegrationAwsSesEventHandling\\\\DataTransferObjects\\\\SesComplaintData\\:\\:__construct\\(\\) has parameter \\$complainedRecipients with generic class Spatie\\\\LaravelData\\\\DataCollection but does not specify its types\\: TKey, TValue$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/integration-aws-ses-event-handling/src/DataTransferObjects/SesComplaintData.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\IntegrationAwsSesEventHandling\\\\DataTransferObjects\\\\SesDeliveryData\\:\\:__construct\\(\\) has parameter \\$recipients with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/integration-aws-ses-event-handling/src/DataTransferObjects/SesDeliveryData.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\IntegrationAwsSesEventHandling\\\\DataTransferObjects\\\\SesDeliveryDelayData\\:\\:__construct\\(\\) has parameter \\$delayedRecipients with generic class Spatie\\\\LaravelData\\\\DataCollection but does not specify its types\\: TKey, TValue$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/integration-aws-ses-event-handling/src/DataTransferObjects/SesDeliveryDelayData.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method AidingApp\\\\IntegrationAwsSesEventHandling\\\\DataTransferObjects\\\\SesEventData\\:\\:fromRequest\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/integration-aws-ses-event-handling/src/DataTransferObjects/SesEventData.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\IntegrationAwsSesEventHandling\\\\DataTransferObjects\\\\SesMailData\\:\\:__construct\\(\\) has parameter \\$commonHeaders with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/integration-aws-ses-event-handling/src/DataTransferObjects/SesMailData.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\IntegrationAwsSesEventHandling\\\\DataTransferObjects\\\\SesMailData\\:\\:__construct\\(\\) has parameter \\$destination with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/integration-aws-ses-event-handling/src/DataTransferObjects/SesMailData.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\IntegrationAwsSesEventHandling\\\\DataTransferObjects\\\\SesMailData\\:\\:__construct\\(\\) has parameter \\$headers with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/integration-aws-ses-event-handling/src/DataTransferObjects/SesMailData.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\IntegrationAwsSesEventHandling\\\\DataTransferObjects\\\\SesMailData\\:\\:__construct\\(\\) has parameter \\$tags with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/integration-aws-ses-event-handling/src/DataTransferObjects/SesMailData.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\IntegrationAwsSesEventHandling\\\\DataTransferObjects\\\\SesTopicPreferencesData\\:\\:__construct\\(\\) has parameter \\$topicDefaultSubscriptionStatus with generic class Spatie\\\\LaravelData\\\\DataCollection but does not specify its types\\: TKey, TValue$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/integration-aws-ses-event-handling/src/DataTransferObjects/SesTopicPreferencesData.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\IntegrationAwsSesEventHandling\\\\DataTransferObjects\\\\SesTopicPreferencesData\\:\\:__construct\\(\\) has parameter \\$topicSubscriptionStatus with generic class Spatie\\\\LaravelData\\\\DataCollection but does not specify its types\\: TKey, TValue$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/integration-aws-ses-event-handling/src/DataTransferObjects/SesTopicPreferencesData.php',
];
$ignoreErrors[] = [
	// identifier: class.notFound
	'message' => '#^Access to property \\$mail on an unknown class AidingApp\\\\IntegrationAwsSesEventHandling\\\\Filament\\\\Pages\\\\TenantConfig\\.$#',
	'count' => 21,
	'path' => __DIR__ . '/app-modules/integration-aws-ses-event-handling/src/Filament/Pages/ManageAmazonSesSettings.php',
];
$ignoreErrors[] = [
	// identifier: class.notFound
	'message' => '#^PHPDoc tag @var for variable \\$config contains unknown class AidingApp\\\\IntegrationAwsSesEventHandling\\\\Filament\\\\Pages\\\\TenantConfig\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/integration-aws-ses-event-handling/src/Filament/Pages/ManageAmazonSesSettings.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method AidingApp\\\\IntegrationAwsSesEventHandling\\\\Http\\\\Controllers\\\\AwsSesInboundWebhookController\\:\\:__invoke\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/integration-aws-ses-event-handling/src/Http/Controllers/AwsSesInboundWebhookController.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method AidingApp\\\\IntegrationAwsSesEventHandling\\\\Providers\\\\IntegrationAwsSesEventHandlingServiceProvider\\:\\:boot\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/integration-aws-ses-event-handling/src/Providers/IntegrationAwsSesEventHandlingServiceProvider.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\IntegrationAwsSesEventHandling\\\\Settings\\\\SesSettings\\:\\:encrypted\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/integration-aws-ses-event-handling/src/Settings/SesSettings.php',
];
$ignoreErrors[] = [
	// identifier: class.notFound
	'message' => '#^Access to property \\$mail on an unknown class TenantConfig\\.$#',
	'count' => 8,
	'path' => __DIR__ . '/app-modules/integration-aws-ses-event-handling/tests/Feature/Filament/ManageAmazonSesSettingsTest.php',
];
$ignoreErrors[] = [
	// identifier: class.notFound
	'message' => '#^PHPDoc tag @var for variable \\$config contains unknown class TenantConfig\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/integration-aws-ses-event-handling/tests/Feature/Filament/ManageAmazonSesSettingsTest.php',
];
$ignoreErrors[] = [
	// identifier: method.notFound
	'message' => '#^Call to an undefined method Pest\\\\PendingCalls\\\\TestCall\\:\\:expectExceptionMessage\\(\\)\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/integration-aws-ses-event-handling/tests/Unit/SesConfigurationSetTest.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method AidingApp\\\\IntegrationGoogleAnalytics\\\\Providers\\\\IntegrationGoogleAnalyticsServiceProvider\\:\\:boot\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/integration-google-analytics/src/Providers/IntegrationGoogleAnalyticsServiceProvider.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method AidingApp\\\\IntegrationGoogleRecaptcha\\\\Providers\\\\IntegrationGoogleRecaptchaServiceProvider\\:\\:boot\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/integration-google-recaptcha/src/Providers/IntegrationGoogleRecaptchaServiceProvider.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\IntegrationGoogleRecaptcha\\\\Settings\\\\GoogleRecaptchaSettings\\:\\:encrypted\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/integration-google-recaptcha/src/Settings/GoogleRecaptchaSettings.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method AidingApp\\\\IntegrationMicrosoftClarity\\\\Providers\\\\IntegrationMicrosoftClarityServiceProvider\\:\\:boot\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/integration-microsoft-clarity/src/Providers/IntegrationMicrosoftClarityServiceProvider.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\IntegrationTwilio\\\\Settings\\\\TwilioSettings\\:\\:encrypted\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/integration-twilio/src/Settings/TwilioSettings.php',
];
$ignoreErrors[] = [
	// identifier: varTag.differentVariable
	'message' => '#^Variable \\$senderModel in PHPDoc tag @var does not match assigned variable \\$checkedInFromModel\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/database/factories/AssetCheckInFactory.php',
];
$ignoreErrors[] = [
	// identifier: varTag.differentVariable
	'message' => '#^Variable \\$senderModel in PHPDoc tag @var does not match assigned variable \\$checkedOutToModel\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/database/factories/AssetCheckOutFactory.php',
];
$ignoreErrors[] = [
	// identifier: method.notFound
	'message' => '#^Call to an undefined method Faker\\\\Generator\\:\\:catchPhrase\\(\\)\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/database/factories/AssetFactory.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Database\\\\Factories\\\\MaintenanceActivityFactory\\:\\:getStates\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/database/factories/MaintenanceActivityFactory.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Database\\\\Factories\\\\MaintenanceActivityFactory\\:\\:randomizeState\\(\\) has parameter \\$states with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/database/factories/MaintenanceActivityFactory.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Database\\\\Seeders\\\\AssetSeeder\\:\\:metadataSeeders\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/database/seeders/AssetSeeder.php',
];
$ignoreErrors[] = [
	// identifier: match.unhandled
	'message' => '#^Match expression does not handle remaining value\\: mixed$#',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Filament/Actions/CheckOutAssetHeaderAction.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Filament\\\\Resources\\\\AssetCheckInResource\\\\Components\\\\AssetCheckInViewAction\\:\\:renderInfolist\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Filament/Resources/AssetCheckInResource/Components/AssetCheckInViewAction.php',
];
$ignoreErrors[] = [
	// identifier: varTag.unresolvableType
	'message' => '#^PHPDoc tag @var for variable \\$resource contains unresolvable type\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Filament/Resources/AssetCheckInResource/Pages/EditAssetCheckIn.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Filament\\\\Resources\\\\AssetCheckInResource\\\\Pages\\\\ViewAssetCheckIn\\:\\:renderInfolist\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Filament/Resources/AssetCheckInResource/Pages/ViewAssetCheckIn.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Filament\\\\Resources\\\\AssetCheckOutResource\\\\Components\\\\AssetCheckOutViewAction\\:\\:renderInfolist\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Filament/Resources/AssetCheckOutResource/Components/AssetCheckOutViewAction.php',
];
$ignoreErrors[] = [
	// identifier: varTag.unresolvableType
	'message' => '#^PHPDoc tag @var for variable \\$resource contains unresolvable type\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Filament/Resources/AssetCheckOutResource/Pages/EditAssetCheckOut.php',
];
$ignoreErrors[] = [
	// identifier: method.notFound
	'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Builder\\:\\:withoutReturned\\(\\)\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Filament/Resources/AssetCheckOutResource/Pages/ListAssetCheckOuts.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Filament\\\\Resources\\\\AssetCheckOutResource\\\\Pages\\\\ViewAssetCheckOut\\:\\:renderInfolist\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Filament/Resources/AssetCheckOutResource/Pages/ViewAssetCheckOut.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Property AidingApp\\\\InventoryManagement\\\\Filament\\\\Resources\\\\AssetResource\\\\Pages\\\\AssetTimeline\\:\\:\\$modelsToTimeline type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Filament/Resources/AssetResource/Pages/AssetTimeline.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Filament\\\\Resources\\\\AssetResource\\\\Pages\\\\ManageAssetMaintenanceActivity\\:\\:managers\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Filament/Resources/AssetResource/Pages/ManageAssetMaintenanceActivity.php',
];
$ignoreErrors[] = [
	// identifier: staticClassAccess.privateMethod
	'message' => '#^Unsafe call to private method AidingApp\\\\InventoryManagement\\\\Filament\\\\Resources\\\\AssetResource\\\\Pages\\\\ManageAssetMaintenanceActivity\\:\\:managers\\(\\) through static\\:\\:\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Filament/Resources/AssetResource/Pages/ManageAssetMaintenanceActivity.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Filament\\\\Resources\\\\MaintenanceActivityResource\\\\MaintenanceActivityViewAction\\:\\:renderInfolist\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Filament/Resources/MaintenanceActivityResource/MaintenanceActivityViewAction.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Models\\\\Asset\\:\\:checkIns\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/Asset.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Models\\\\Asset\\:\\:checkOuts\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/Asset.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Models\\\\Asset\\:\\:latestCheckIn\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasOne does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/Asset.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Models\\\\Asset\\:\\:latestCheckOut\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasOne does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/Asset.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Models\\\\Asset\\:\\:location\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/Asset.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Models\\\\Asset\\:\\:maintenanceActivities\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/Asset.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Models\\\\Asset\\:\\:newBelongsToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/Asset.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Models\\\\Asset\\:\\:newMorphToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/Asset.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Models\\\\Asset\\:\\:purchaseAge\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Casts\\\\Attribute does not specify its types\\: TGet, TSet$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/Asset.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Models\\\\Asset\\:\\:status\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/Asset.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Models\\\\Asset\\:\\:type\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/Asset.php',
];
$ignoreErrors[] = [
	// identifier: nullsafe.neverNull
	'message' => '#^Using nullsafe property access on non\\-nullable type AidingApp\\\\InventoryManagement\\\\Models\\\\AssetCheckOut\\. Use \\-\\> instead\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/Asset.php',
];
$ignoreErrors[] = [
	// identifier: method.notFound
	'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:checkIns\\(\\)\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/AssetCheckIn.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Models\\\\AssetCheckIn\\:\\:asset\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/AssetCheckIn.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Models\\\\AssetCheckIn\\:\\:checkOut\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasOne does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/AssetCheckIn.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Models\\\\AssetCheckIn\\:\\:checkedInBy\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/AssetCheckIn.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Models\\\\AssetCheckIn\\:\\:checkedInFrom\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/AssetCheckIn.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Models\\\\AssetCheckIn\\:\\:getTimelineData\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/AssetCheckIn.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Models\\\\AssetCheckIn\\:\\:newBelongsToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/AssetCheckIn.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Models\\\\AssetCheckIn\\:\\:newMorphToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/AssetCheckIn.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Models\\\\AssetCheckIn\\:\\:timelineRecord\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphOne does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/AssetCheckIn.php',
];
$ignoreErrors[] = [
	// identifier: method.notFound
	'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:checkOuts\\(\\)\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/AssetCheckOut.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Models\\\\AssetCheckOut\\:\\:asset\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/AssetCheckOut.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Models\\\\AssetCheckOut\\:\\:checkIn\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/AssetCheckOut.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Models\\\\AssetCheckOut\\:\\:checkedOutBy\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/AssetCheckOut.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Models\\\\AssetCheckOut\\:\\:checkedOutTo\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/AssetCheckOut.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Models\\\\AssetCheckOut\\:\\:getTimelineData\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/AssetCheckOut.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Models\\\\AssetCheckOut\\:\\:newBelongsToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/AssetCheckOut.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Models\\\\AssetCheckOut\\:\\:newMorphToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/AssetCheckOut.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Models\\\\AssetCheckOut\\:\\:scopeWithoutReturned\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/AssetCheckOut.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Models\\\\AssetCheckOut\\:\\:scopeWithoutReturned\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder does not specify its types\\: TModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/AssetCheckOut.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Models\\\\AssetCheckOut\\:\\:status\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Casts\\\\Attribute does not specify its types\\: TGet, TSet$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/AssetCheckOut.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Models\\\\AssetCheckOut\\:\\:timelineRecord\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphOne does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/AssetCheckOut.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Models\\\\AssetLocation\\:\\:assets\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/AssetLocation.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Models\\\\AssetLocation\\:\\:newBelongsToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/AssetLocation.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Models\\\\AssetLocation\\:\\:newMorphToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/AssetLocation.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Models\\\\AssetStatus\\:\\:assets\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/AssetStatus.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Models\\\\AssetStatus\\:\\:newBelongsToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/AssetStatus.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Models\\\\AssetStatus\\:\\:newMorphToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/AssetStatus.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Models\\\\AssetType\\:\\:assets\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/AssetType.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Models\\\\AssetType\\:\\:newBelongsToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/AssetType.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Models\\\\AssetType\\:\\:newMorphToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/AssetType.php',
];
$ignoreErrors[] = [
	// identifier: method.notFound
	'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:maintenanceActivities\\(\\)\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/MaintenanceActivity.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Models\\\\MaintenanceActivity\\:\\:asset\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/MaintenanceActivity.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Models\\\\MaintenanceActivity\\:\\:getTimelineData\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/MaintenanceActivity.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Models\\\\MaintenanceActivity\\:\\:maintenanceProvider\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/MaintenanceActivity.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Models\\\\MaintenanceActivity\\:\\:newBelongsToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/MaintenanceActivity.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Models\\\\MaintenanceActivity\\:\\:newMorphToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/MaintenanceActivity.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Models\\\\MaintenanceActivity\\:\\:timelineRecord\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphOne does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/MaintenanceActivity.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Models\\\\MaintenanceProvider\\:\\:maintenanceActivities\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/MaintenanceProvider.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Models\\\\MaintenanceProvider\\:\\:newBelongsToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/MaintenanceProvider.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Models\\\\MaintenanceProvider\\:\\:newMorphToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/MaintenanceProvider.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Models\\\\Scopes\\\\ClassifiedAs\\:\\:__invoke\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/Scopes/ClassifiedAs.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Policies\\\\AssetCheckInPolicy\\:\\:requiredFeatures\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Policies/AssetCheckInPolicy.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Policies\\\\AssetCheckOutPolicy\\:\\:requiredFeatures\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Policies/AssetCheckOutPolicy.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Policies\\\\AssetLocationPolicy\\:\\:requiredFeatures\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Policies/AssetLocationPolicy.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Policies\\\\AssetPolicy\\:\\:requiredFeatures\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Policies/AssetPolicy.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Policies\\\\AssetStatusPolicy\\:\\:requiredFeatures\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Policies/AssetStatusPolicy.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Policies\\\\AssetTypePolicy\\:\\:requiredFeatures\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Policies/AssetTypePolicy.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Providers\\\\InventoryManagementServiceProvider\\:\\:boot\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Providers/InventoryManagementServiceProvider.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\KnowledgeBase\\\\Database\\\\Factories\\\\KnowledgeBaseCategoryFactory\\:\\:icons\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/database/factories/KnowledgeBaseCategoryFactory.php',
];
$ignoreErrors[] = [
	// identifier: varTag.unresolvableType
	'message' => '#^PHPDoc tag @var for variable \\$resource contains unresolvable type\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/src/Filament/Resources/KnowledgeBaseCategoryResource/Pages/EditKnowledgeBaseCategory.php',
];
$ignoreErrors[] = [
	// identifier: property.notFound
	'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$category\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/src/Filament/Resources/KnowledgeBaseItemResource.php',
];
$ignoreErrors[] = [
	// identifier: property.notFound
	'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$division\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/src/Filament/Resources/KnowledgeBaseItemResource.php',
];
$ignoreErrors[] = [
	// identifier: property.notFound
	'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$quality\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/src/Filament/Resources/KnowledgeBaseItemResource.php',
];
$ignoreErrors[] = [
	// identifier: property.notFound
	'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$status\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/src/Filament/Resources/KnowledgeBaseItemResource.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\KnowledgeBase\\\\Filament\\\\Resources\\\\KnowledgeBaseItemResource\\:\\:getGlobalSearchEloquentQuery\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder does not specify its types\\: TModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/src/Filament/Resources/KnowledgeBaseItemResource.php',
];
$ignoreErrors[] = [
	// identifier: property.notFound
	'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$title\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/knowledge-base/src/Filament/Resources/KnowledgeBaseItemResource/Pages/EditKnowledgeBaseItem.php',
];
$ignoreErrors[] = [
	// identifier: varTag.unresolvableType
	'message' => '#^PHPDoc tag @var for variable \\$resource contains unresolvable type\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/src/Filament/Resources/KnowledgeBaseItemResource/Pages/EditKnowledgeBaseItem.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\KnowledgeBase\\\\Filament\\\\Resources\\\\KnowledgeBaseItemResource\\\\Pages\\\\EditKnowledgeBaseItemMetadata\\:\\:form\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/src/Filament/Resources/KnowledgeBaseItemResource/Pages/EditKnowledgeBaseItemMetadata.php',
];
$ignoreErrors[] = [
	// identifier: property.notFound
	'message' => '#^Access to an undefined property App\\\\Models\\\\Tag\\:\\:\\$pivot\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/src/Filament/Resources/KnowledgeBaseItemResource/Pages/ListKnowledgeBaseItems.php',
];
$ignoreErrors[] = [
	// identifier: property.notFound
	'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$notes\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/src/Filament/Resources/KnowledgeBaseItemResource/Pages/ListKnowledgeBaseItems.php',
];
$ignoreErrors[] = [
	// identifier: property.notFound
	'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$public\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/src/Filament/Resources/KnowledgeBaseItemResource/Pages/ListKnowledgeBaseItems.php',
];
$ignoreErrors[] = [
	// identifier: property.notFound
	'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$title\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/src/Filament/Resources/KnowledgeBaseItemResource/Pages/ListKnowledgeBaseItems.php',
];
$ignoreErrors[] = [
	// identifier: varTag.unresolvableType
	'message' => '#^PHPDoc tag @var for variable \\$resource contains unresolvable type\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/src/Filament/Resources/KnowledgeBaseQualityResource/Pages/EditKnowledgeBaseQuality.php',
];
$ignoreErrors[] = [
	// identifier: varTag.unresolvableType
	'message' => '#^PHPDoc tag @var for variable \\$resource contains unresolvable type\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/src/Filament/Resources/KnowledgeBaseStatusResource/Pages/EditKnowledgeBaseStatus.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\KnowledgeBase\\\\Jobs\\\\KnowledgeBaseItemDownloadExternalMedia\\:\\:processContentItem\\(\\) has parameter \\$content with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/src/Jobs/KnowledgeBaseItemDownloadExternalMedia.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\KnowledgeBase\\\\Jobs\\\\KnowledgeBaseItemDownloadExternalMedia\\:\\:processContentItem\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/src/Jobs/KnowledgeBaseItemDownloadExternalMedia.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\KnowledgeBase\\\\Models\\\\KnowledgeBaseCategory\\:\\:knowledgeBaseItems\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/src/Models/KnowledgeBaseCategory.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\KnowledgeBase\\\\Models\\\\KnowledgeBaseCategory\\:\\:newBelongsToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/src/Models/KnowledgeBaseCategory.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\KnowledgeBase\\\\Models\\\\KnowledgeBaseCategory\\:\\:newMorphToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/src/Models/KnowledgeBaseCategory.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\KnowledgeBase\\\\Models\\\\KnowledgeBaseCategory\\:\\:parentCategory\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/src/Models/KnowledgeBaseCategory.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\KnowledgeBase\\\\Models\\\\KnowledgeBaseCategory\\:\\:subCategories\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/src/Models/KnowledgeBaseCategory.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\KnowledgeBase\\\\Models\\\\KnowledgeBaseItem\\:\\:category\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/src/Models/KnowledgeBaseItem.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\KnowledgeBase\\\\Models\\\\KnowledgeBaseItem\\:\\:division\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/src/Models/KnowledgeBaseItem.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\KnowledgeBase\\\\Models\\\\KnowledgeBaseItem\\:\\:newBelongsToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/src/Models/KnowledgeBaseItem.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\KnowledgeBase\\\\Models\\\\KnowledgeBaseItem\\:\\:newMorphToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/src/Models/KnowledgeBaseItem.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\KnowledgeBase\\\\Models\\\\KnowledgeBaseItem\\:\\:quality\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/src/Models/KnowledgeBaseItem.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method AidingApp\\\\KnowledgeBase\\\\Models\\\\KnowledgeBaseItem\\:\\:scopePublic\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/src/Models/KnowledgeBaseItem.php',
];
$ignoreErrors[] = [
	// identifier: missingType.parameter
	'message' => '#^Method AidingApp\\\\KnowledgeBase\\\\Models\\\\KnowledgeBaseItem\\:\\:scopePublic\\(\\) has parameter \\$query with no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/src/Models/KnowledgeBaseItem.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\KnowledgeBase\\\\Models\\\\KnowledgeBaseItem\\:\\:status\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/src/Models/KnowledgeBaseItem.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\KnowledgeBase\\\\Models\\\\KnowledgeBaseItem\\:\\:tags\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/src/Models/KnowledgeBaseItem.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\KnowledgeBase\\\\Models\\\\KnowledgeBaseItem\\:\\:votes\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/src/Models/KnowledgeBaseItem.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\KnowledgeBase\\\\Models\\\\KnowledgeBaseQuality\\:\\:knowledgeBaseItems\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/src/Models/KnowledgeBaseQuality.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\KnowledgeBase\\\\Models\\\\KnowledgeBaseQuality\\:\\:newBelongsToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/src/Models/KnowledgeBaseQuality.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\KnowledgeBase\\\\Models\\\\KnowledgeBaseQuality\\:\\:newMorphToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/src/Models/KnowledgeBaseQuality.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\KnowledgeBase\\\\Models\\\\KnowledgeBaseStatus\\:\\:knowledgeBaseItems\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/src/Models/KnowledgeBaseStatus.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\KnowledgeBase\\\\Models\\\\KnowledgeBaseStatus\\:\\:newBelongsToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/src/Models/KnowledgeBaseStatus.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\KnowledgeBase\\\\Models\\\\KnowledgeBaseStatus\\:\\:newMorphToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/src/Models/KnowledgeBaseStatus.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\KnowledgeBase\\\\Policies\\\\KnowledgeBaseCategoryPolicy\\:\\:hasAnyLicense\\(\\) has parameter \\$licenses with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/src/Policies/KnowledgeBaseCategoryPolicy.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\KnowledgeBase\\\\Policies\\\\KnowledgeBaseCategoryPolicy\\:\\:hasLicenses\\(\\) has parameter \\$licenses with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/src/Policies/KnowledgeBaseCategoryPolicy.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\KnowledgeBase\\\\Policies\\\\KnowledgeBaseItemPolicy\\:\\:hasAnyLicense\\(\\) has parameter \\$licenses with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/src/Policies/KnowledgeBaseItemPolicy.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\KnowledgeBase\\\\Policies\\\\KnowledgeBaseItemPolicy\\:\\:hasLicenses\\(\\) has parameter \\$licenses with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/src/Policies/KnowledgeBaseItemPolicy.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\KnowledgeBase\\\\Policies\\\\KnowledgeBaseQualityPolicy\\:\\:hasAnyLicense\\(\\) has parameter \\$licenses with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/src/Policies/KnowledgeBaseQualityPolicy.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\KnowledgeBase\\\\Policies\\\\KnowledgeBaseQualityPolicy\\:\\:hasLicenses\\(\\) has parameter \\$licenses with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/src/Policies/KnowledgeBaseQualityPolicy.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\KnowledgeBase\\\\Policies\\\\KnowledgeBaseStatusPolicy\\:\\:hasAnyLicense\\(\\) has parameter \\$licenses with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/src/Policies/KnowledgeBaseStatusPolicy.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\KnowledgeBase\\\\Policies\\\\KnowledgeBaseStatusPolicy\\:\\:hasLicenses\\(\\) has parameter \\$licenses with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/src/Policies/KnowledgeBaseStatusPolicy.php',
];
$ignoreErrors[] = [
	// identifier: property.notFound
	'message' => '#^Access to an undefined property Pest\\\\Expectation\\<AidingApp\\\\KnowledgeBase\\\\Models\\\\KnowledgeBaseCategory\\|null\\>\\:\\:\\$subCategories\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/tests/KnowledgeBaseCategory/EditKnowledgeBaseCategoryTest.php',
];
$ignoreErrors[] = [
	// identifier: method.notFound
	'message' => '#^Call to an undefined method Illuminate\\\\Testing\\\\TestResponse\\:\\:assertActionDisabled\\(\\)\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/tests/KnowledgeBaseItem/CreateKnowledgeBaseItemTest.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#2 \\$guardName of method class@anonymous/app\\-modules/license\\-management/database/migrations/2024_11_21_194048_data_seed_permissions_for_product_license_module\\.php\\:42\\:\\:deletePermissions\\(\\) expects string, array given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/license-management/database/migrations/2024_11_21_194048_data_seed_permissions_for_product_license_module.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Property class@anonymous/app\\-modules/license\\-management/database/migrations/2024_11_21_194048_data_seed_permissions_for_product_license_module\\.php\\:42\\:\\:\\$guards type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/license-management/database/migrations/2024_11_21_194048_data_seed_permissions_for_product_license_module.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Property class@anonymous/app\\-modules/license\\-management/database/migrations/2024_11_21_194048_data_seed_permissions_for_product_license_module\\.php\\:42\\:\\:\\$permissions type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/license-management/database/migrations/2024_11_21_194048_data_seed_permissions_for_product_license_module.php',
];
$ignoreErrors[] = [
	// identifier: varTag.unresolvableType
	'message' => '#^PHPDoc tag @var for variable \\$resource contains unresolvable type\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/license-management/src/Filament/Resources/ProductResource/Pages/EditProduct.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\LicenseManagement\\\\Models\\\\Product\\:\\:newBelongsToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/license-management/src/Models/Product.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\LicenseManagement\\\\Models\\\\Product\\:\\:newMorphToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/license-management/src/Models/Product.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\LicenseManagement\\\\Models\\\\Product\\:\\:productLicenses\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/license-management/src/Models/Product.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\LicenseManagement\\\\Models\\\\ProductLicense\\:\\:contact\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/license-management/src/Models/ProductLicense.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\LicenseManagement\\\\Models\\\\ProductLicense\\:\\:newBelongsToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/license-management/src/Models/ProductLicense.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\LicenseManagement\\\\Models\\\\ProductLicense\\:\\:newMorphToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/license-management/src/Models/ProductLicense.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\LicenseManagement\\\\Models\\\\ProductLicense\\:\\:product\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/license-management/src/Models/ProductLicense.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\LicenseManagement\\\\Models\\\\ProductLicense\\:\\:status\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Casts\\\\Attribute does not specify its types\\: TGet, TSet$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/license-management/src/Models/ProductLicense.php',
];
$ignoreErrors[] = [
	// identifier: property.phpDocType
	'message' => '#^PHPDoc type array of property AidingApp\\\\LicenseManagement\\\\Models\\\\ProductLicense\\:\\:\\$appends is not covariant with PHPDoc type array\\<int, string\\> of overridden property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$appends\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/license-management/src/Models/ProductLicense.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Property AidingApp\\\\LicenseManagement\\\\Models\\\\ProductLicense\\:\\:\\$appends type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/license-management/src/Models/ProductLicense.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\LicenseManagement\\\\Models\\\\Scopes\\\\AuthorizeLicensesScope\\:\\:apply\\(\\) has parameter \\$builder with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/license-management/src/Models/Scopes/AuthorizeLicensesScope.php',
];
$ignoreErrors[] = [
	// identifier: property.notFound
	'message' => '#^Access to an undefined property AidingApp\\\\LicenseManagement\\\\Models\\\\ProductLicense\\:\\:\\$created_by_id\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/license-management/src/Observers/ProductLicenseObserver.php',
];
$ignoreErrors[] = [
	// identifier: property.notFound
	'message' => '#^Access to an undefined property AidingApp\\\\LicenseManagement\\\\Models\\\\Product\\:\\:\\$created_by_id\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/license-management/src/Observers/ProductObserver.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method AidingApp\\\\LicenseManagement\\\\Providers\\\\LicenseManagementServiceProvider\\:\\:boot\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/license-management/src/Providers/LicenseManagementServiceProvider.php',
];
$ignoreErrors[] = [
	// identifier: property.notFound
	'message' => '#^Access to an undefined property AidingApp\\\\LicenseManagement\\\\Models\\\\ProductLicense\\:\\:\\$status\\.$#',
	'count' => 4,
	'path' => __DIR__ . '/app-modules/license-management/tests/Unit/ProductLicenseStatusTest.php',
];
$ignoreErrors[] = [
	// identifier: argument.templateType
	'message' => '#^Unable to resolve the template type TValue in call to function expect$#',
	'count' => 4,
	'path' => __DIR__ . '/app-modules/license-management/tests/Unit/ProductLicenseStatusTest.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Class AidingApp\\\\Notification\\\\Database\\\\Factories\\\\SubscriptionFactory extends generic class Illuminate\\\\Database\\\\Eloquent\\\\Factories\\\\Factory but does not specify its types\\: TModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/database/factories/SubscriptionFactory.php',
];
$ignoreErrors[] = [
	// identifier: phpDoc.parseError
	'message' => '#^PHPDoc tag @extends has invalid value \\(Subscription\\>\\)\\: Unexpected token "\\>", expected \'\\<\' at offset 28$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/database/factories/SubscriptionFactory.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method AidingApp\\\\Notification\\\\Actions\\\\SubscriptionToggle\\:\\:handle\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/src/Actions/SubscriptionToggle.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Notification\\\\DataTransferObjects\\\\EmailChannelResultData\\:\\:__construct\\(\\) has parameter \\$recipients with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/src/DataTransferObjects/EmailChannelResultData.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Notification\\\\Enums\\\\NotificationChannel\\:\\:getEngagementOptions\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/src/Enums/NotificationChannel.php',
];
$ignoreErrors[] = [
	// identifier: missingType.property
	'message' => '#^Property AidingApp\\\\Notification\\\\Exceptions\\\\NotificationQuotaExceeded\\:\\:\\$message has no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/src/Exceptions/NotificationQuotaExceeded.php',
];
$ignoreErrors[] = [
	// identifier: missingType.property
	'message' => '#^Property AidingApp\\\\Notification\\\\Exceptions\\\\SubscriptionAlreadyExistsException\\:\\:\\$message has no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/src/Exceptions/SubscriptionAlreadyExistsException.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),Illuminate\\\\Database\\\\Eloquent\\\\Model\\>\\:\\:each\\(\\) expects callable\\(Illuminate\\\\Database\\\\Eloquent\\\\Model, int\\|string\\)\\: mixed, Closure\\(AidingApp\\\\Notification\\\\Models\\\\Contracts\\\\Subscribable\\)\\: void given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/src/Filament/Actions/SubscribeBulkAction.php',
];
$ignoreErrors[] = [
	// identifier: method.notFound
	'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:displayNameKey\\(\\)\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/src/Listeners/NotifyUserOfSubscriptionCreated.php',
];
$ignoreErrors[] = [
	// identifier: match.unhandled
	'message' => '#^Match expression does not handle remaining value\\: class\\-string\\<Illuminate\\\\Database\\\\Eloquent\\\\Model\\>&literal\\-string$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/src/Listeners/NotifyUserOfSubscriptionCreated.php',
];
$ignoreErrors[] = [
	// identifier: method.notFound
	'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:displayNameKey\\(\\)\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/src/Listeners/NotifyUserOfSubscriptionDeleted.php',
];
$ignoreErrors[] = [
	// identifier: match.unhandled
	'message' => '#^Match expression does not handle remaining value\\: class\\-string\\<Illuminate\\\\Database\\\\Eloquent\\\\Model\\>&literal\\-string$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/src/Listeners/NotifyUserOfSubscriptionDeleted.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method AidingApp\\\\Notification\\\\Models\\\\Contracts\\\\CanBeNotified\\:\\:notifications\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/src/Models/Contracts/CanBeNotified.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method AidingApp\\\\Notification\\\\Models\\\\Contracts\\\\CanBeNotified\\:\\:notify\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/src/Models/Contracts/CanBeNotified.php',
];
$ignoreErrors[] = [
	// identifier: missingType.parameter
	'message' => '#^Method AidingApp\\\\Notification\\\\Models\\\\Contracts\\\\CanBeNotified\\:\\:notify\\(\\) has parameter \\$instance with no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/src/Models/Contracts/CanBeNotified.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method AidingApp\\\\Notification\\\\Models\\\\Contracts\\\\CanBeNotified\\:\\:notifyNow\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/src/Models/Contracts/CanBeNotified.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Notification\\\\Models\\\\Contracts\\\\CanBeNotified\\:\\:notifyNow\\(\\) has parameter \\$channels with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/src/Models/Contracts/CanBeNotified.php',
];
$ignoreErrors[] = [
	// identifier: missingType.parameter
	'message' => '#^Method AidingApp\\\\Notification\\\\Models\\\\Contracts\\\\CanBeNotified\\:\\:notifyNow\\(\\) has parameter \\$instance with no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/src/Models/Contracts/CanBeNotified.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method AidingApp\\\\Notification\\\\Models\\\\Contracts\\\\CanBeNotified\\:\\:readNotifications\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/src/Models/Contracts/CanBeNotified.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method AidingApp\\\\Notification\\\\Models\\\\Contracts\\\\CanBeNotified\\:\\:routeNotificationFor\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/src/Models/Contracts/CanBeNotified.php',
];
$ignoreErrors[] = [
	// identifier: missingType.parameter
	'message' => '#^Method AidingApp\\\\Notification\\\\Models\\\\Contracts\\\\CanBeNotified\\:\\:routeNotificationFor\\(\\) has parameter \\$driver with no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/src/Models/Contracts/CanBeNotified.php',
];
$ignoreErrors[] = [
	// identifier: missingType.parameter
	'message' => '#^Method AidingApp\\\\Notification\\\\Models\\\\Contracts\\\\CanBeNotified\\:\\:routeNotificationFor\\(\\) has parameter \\$notification with no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/src/Models/Contracts/CanBeNotified.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method AidingApp\\\\Notification\\\\Models\\\\Contracts\\\\CanBeNotified\\:\\:unreadNotifications\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/src/Models/Contracts/CanBeNotified.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Notification\\\\Models\\\\Contracts\\\\Message\\:\\:recipient\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/src/Models/Contracts/Message.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Notification\\\\Models\\\\Contracts\\\\Message\\:\\:related\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/src/Models/Contracts/Message.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Notification\\\\Models\\\\Contracts\\\\Subscribable\\:\\:subscriptions\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/src/Models/Contracts/Subscribable.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Notification\\\\Models\\\\DatabaseMessage\\:\\:recipient\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/src/Models/DatabaseMessage.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Notification\\\\Models\\\\DatabaseMessage\\:\\:related\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/src/Models/DatabaseMessage.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Notification\\\\Models\\\\EmailMessage\\:\\:events\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/src/Models/EmailMessage.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Notification\\\\Models\\\\EmailMessage\\:\\:recipient\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/src/Models/EmailMessage.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Notification\\\\Models\\\\EmailMessage\\:\\:related\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/src/Models/EmailMessage.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Notification\\\\Models\\\\EmailMessageEvent\\:\\:message\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/src/Models/EmailMessageEvent.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Class AidingApp\\\\Notification\\\\Models\\\\Subscription uses generic trait Illuminate\\\\Database\\\\Eloquent\\\\Factories\\\\HasFactory but does not specify its types\\: TFactory$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/src/Models/Subscription.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Notification\\\\Models\\\\Subscription\\:\\:scopeLicensedToEducatable\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/src/Models/Subscription.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Notification\\\\Models\\\\Subscription\\:\\:scopeLicensedToEducatable\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder does not specify its types\\: TModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/src/Models/Subscription.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Notification\\\\Models\\\\Subscription\\:\\:subscribable\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/src/Models/Subscription.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Notification\\\\Models\\\\Subscription\\:\\:user\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/src/Models/Subscription.php',
];
$ignoreErrors[] = [
	// identifier: method.notFound
	'message' => '#^Call to an undefined method Illuminate\\\\Notifications\\\\Notification\\:\\:toDatabase\\(\\)\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/src/Notifications/Channels/DatabaseChannel.php',
];
$ignoreErrors[] = [
	// identifier: match.unhandled
	'message' => '#^Match expression does not handle remaining value\\: true$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/src/Notifications/Channels/DatabaseChannel.php',
];
$ignoreErrors[] = [
	// identifier: method.childReturnType
	'message' => '#^Return type \\(void\\) of method AidingApp\\\\Notification\\\\Notifications\\\\Channels\\\\DatabaseChannel\\:\\:send\\(\\) should be compatible with return type \\(Illuminate\\\\Database\\\\Eloquent\\\\Model\\) of method Illuminate\\\\Notifications\\\\Channels\\\\DatabaseChannel\\:\\:send\\(\\)$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/src/Notifications/Channels/DatabaseChannel.php',
];
$ignoreErrors[] = [
	// identifier: method.notFound
	'message' => '#^Call to an undefined method Illuminate\\\\Notifications\\\\Notification\\:\\:toMail\\(\\)\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/notification/src/Notifications/Channels/MailChannel.php',
];
$ignoreErrors[] = [
	// identifier: nullCoalesce.expr
	'message' => '#^Expression on left side of \\?\\? is not nullable\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/src/Notifications/Channels/MailChannel.php',
];
$ignoreErrors[] = [
	// identifier: method.childReturnType
	'message' => '#^Return type \\(void\\) of method AidingApp\\\\Notification\\\\Notifications\\\\Channels\\\\MailChannel\\:\\:send\\(\\) should be compatible with return type \\(Illuminate\\\\Mail\\\\SentMessage\\|null\\) of method Illuminate\\\\Notifications\\\\Channels\\\\MailChannel\\:\\:send\\(\\)$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/src/Notifications/Channels/MailChannel.php',
];
$ignoreErrors[] = [
	// identifier: nullsafe.neverNull
	'message' => '#^Using nullsafe property access on non\\-nullable type mixed\\. Use \\-\\> instead\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/src/Notifications/Channels/MailChannel.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Notification\\\\Notifications\\\\Contracts\\\\OnDemandNotification\\:\\:identifyRecipient\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/src/Notifications/Contracts/OnDemandNotification.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Notification\\\\Notifications\\\\Messages\\\\MailMessage\\:\\:toArray\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/src/Notifications/Messages/MailMessage.php',
];
$ignoreErrors[] = [
	// identifier: nullCoalesce.property
	'message' => '#^Property Illuminate\\\\Notifications\\\\Messages\\\\SimpleMessage\\:\\:\\$actionUrl \\(string\\) on left side of \\?\\? is not nullable\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/src/Notifications/Messages/MailMessage.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Notification\\\\Notifications\\\\Messages\\\\TwilioMessage\\:\\:toArray\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/src/Notifications/Messages/TwilioMessage.php',
];
$ignoreErrors[] = [
	// identifier: match.unhandled
	'message' => '#^Match expression does not handle remaining value\\: string$#',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/notification/src/Observers/SubscriptionObserver.php',
];
$ignoreErrors[] = [
	// identifier: method.notFound
	'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:getLicenseType\\(\\)\\.$#',
	'count' => 5,
	'path' => __DIR__ . '/app-modules/notification/src/Policies/SubscriptionPolicy.php',
];
$ignoreErrors[] = [
	// identifier: nullsafe.neverNull
	'message' => '#^Using nullsafe method call on non\\-nullable type Illuminate\\\\Database\\\\Eloquent\\\\Model\\. Use \\-\\> instead\\.$#',
	'count' => 5,
	'path' => __DIR__ . '/app-modules/notification/src/Policies/SubscriptionPolicy.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Notification\\\\Rules\\\\SubscribableIdExistsRule\\:\\:setData\\(\\) has parameter \\$data with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/src/Rules/SubscribableIdExistsRule.php',
];
$ignoreErrors[] = [
	// identifier: missingType.property
	'message' => '#^Property AidingApp\\\\Notification\\\\Rules\\\\SubscribableIdExistsRule\\:\\:\\$data has no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/src/Rules/SubscribableIdExistsRule.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Notification\\\\Rules\\\\UniqueSubscriptionRule\\:\\:setData\\(\\) has parameter \\$data with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/src/Rules/UniqueSubscriptionRule.php',
];
$ignoreErrors[] = [
	// identifier: missingType.property
	'message' => '#^Property AidingApp\\\\Notification\\\\Rules\\\\UniqueSubscriptionRule\\:\\:\\$data has no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/src/Rules/UniqueSubscriptionRule.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Notification\\\\Tests\\\\Fixtures\\\\TestDatabaseNotification\\:\\:toDatabase\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/tests/Fixtures/TestDatabaseNotification.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method TestSystemNotification\\:\\:via\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/notification/tests/Notifications/Channels/MailChannelEmailMessageTest.php',
];
$ignoreErrors[] = [
	// identifier: match.unreachable
	'message' => '#^Match arm is unreachable because previous comparison is always true\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/portal/src/Actions/GeneratePortalEmbedCode.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Portal\\\\DataTransferObjects\\\\KnowledgeBaseArticleData\\:\\:__construct\\(\\) has parameter \\$tags with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/portal/src/DataTransferObjects/KnowledgeBaseArticleData.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Portal\\\\DataTransferObjects\\\\KnowledgeBaseArticleData\\:\\:__construct\\(\\) has parameter \\$vote with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/portal/src/DataTransferObjects/KnowledgeBaseArticleData.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Property AidingApp\\\\Portal\\\\DataTransferObjects\\\\KnowledgeManagementSearchData\\:\\:\\$articles with generic class Spatie\\\\LaravelData\\\\PaginatedDataCollection does not specify its types\\: TKey, TValue$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/portal/src/DataTransferObjects/KnowledgeManagementSearchData.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Property AidingApp\\\\Portal\\\\DataTransferObjects\\\\KnowledgeManagementSearchData\\:\\:\\$categories with generic class Spatie\\\\LaravelData\\\\DataCollection does not specify its types\\: TKey, TValue$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/portal/src/DataTransferObjects/KnowledgeManagementSearchData.php',
];
$ignoreErrors[] = [
	// identifier: class.notFound
	'message' => '#^Method AidingApp\\\\Portal\\\\Filament\\\\Pages\\\\ManagePortalSettings\\:\\:getFormActions\\(\\) has invalid return type AidingApp\\\\Portal\\\\Filament\\\\Pages\\\\ActionGroup\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/portal/src/Filament/Pages/ManagePortalSettings.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\Portal\\\\Filament\\\\Pages\\\\ManagePortalSettings\\:\\:getFormActions\\(\\) should return array\\<AidingApp\\\\Portal\\\\Filament\\\\Pages\\\\ActionGroup\\|Filament\\\\Forms\\\\Components\\\\Actions\\\\Action\\> but returns array\\<Filament\\\\Actions\\\\Action\\|Filament\\\\Actions\\\\ActionGroup\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/portal/src/Filament/Pages/ManagePortalSettings.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Portal\\\\Http\\\\Controllers\\\\KnowledgeManagementPortal\\\\CreateServiceRequestController\\:\\:addFieldToStep\\(\\) has parameter \\$block with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/portal/src/Http/Controllers/KnowledgeManagementPortal/CreateServiceRequestController.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Portal\\\\Http\\\\Controllers\\\\KnowledgeManagementPortal\\\\CreateServiceRequestController\\:\\:formatBlock\\(\\) has parameter \\$data with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/portal/src/Http/Controllers/KnowledgeManagementPortal/CreateServiceRequestController.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Portal\\\\Http\\\\Controllers\\\\KnowledgeManagementPortal\\\\CreateServiceRequestController\\:\\:formatBlock\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/portal/src/Http/Controllers/KnowledgeManagementPortal/CreateServiceRequestController.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Portal\\\\Http\\\\Controllers\\\\KnowledgeManagementPortal\\\\CreateServiceRequestController\\:\\:formatStep\\(\\) has parameter \\$content with generic class Illuminate\\\\Support\\\\Collection but does not specify its types\\: TKey, TValue$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/portal/src/Http/Controllers/KnowledgeManagementPortal/CreateServiceRequestController.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Portal\\\\Http\\\\Controllers\\\\KnowledgeManagementPortal\\\\CreateServiceRequestController\\:\\:processSubmissionField\\(\\) has parameter \\$fields with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/portal/src/Http/Controllers/KnowledgeManagementPortal/CreateServiceRequestController.php',
];
$ignoreErrors[] = [
	// identifier: argument.templateType
	'message' => '#^Unable to resolve the template type TKey in call to function collect$#',
	'count' => 3,
	'path' => __DIR__ . '/app-modules/portal/src/Http/Controllers/KnowledgeManagementPortal/CreateServiceRequestController.php',
];
$ignoreErrors[] = [
	// identifier: argument.templateType
	'message' => '#^Unable to resolve the template type TValue in call to function collect$#',
	'count' => 3,
	'path' => __DIR__ . '/app-modules/portal/src/Http/Controllers/KnowledgeManagementPortal/CreateServiceRequestController.php',
];
$ignoreErrors[] = [
	// identifier: nullsafe.neverNull
	'message' => '#^Using nullsafe method call on non\\-nullable type AidingApp\\\\ServiceManagement\\\\Enums\\\\ServiceRequestTypeAssignmentTypes\\. Use \\-\\> instead\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/portal/src/Http/Controllers/KnowledgeManagementPortal/CreateServiceRequestController.php',
];
$ignoreErrors[] = [
	// identifier: nullsafe.neverNull
	'message' => '#^Using nullsafe property access on non\\-nullable type AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestType\\. Use \\-\\> instead\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/portal/src/Http/Controllers/KnowledgeManagementPortal/CreateServiceRequestController.php',
];
$ignoreErrors[] = [
	// identifier: booleanNot.alwaysFalse
	'message' => '#^Negated boolean expression is always false\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/portal/src/Http/Controllers/KnowledgeManagementPortal/KnowledgeManagementPortalLogoutController.php',
];
$ignoreErrors[] = [
	// identifier: property.notFound
	'message' => '#^Access to an undefined property Illuminate\\\\Support\\\\ValidatedInput\\:\\:\\$email\\.$#',
	'count' => 3,
	'path' => __DIR__ . '/app-modules/portal/src/Http/Controllers/KnowledgeManagementPortal/KnowledgeManagementPortalRequestAuthenticationController.php',
];
$ignoreErrors[] = [
	// identifier: property.notFound
	'message' => '#^Access to an undefined property Illuminate\\\\Support\\\\ValidatedInput\\:\\:\\$isSpa\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/portal/src/Http/Controllers/KnowledgeManagementPortal/KnowledgeManagementPortalRequestAuthenticationController.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$value of static method Illuminate\\\\Support\\\\Facades\\\\Hash\\:\\:make\\(\\) expects string, int\\<100000, 999999\\> given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/portal/src/Http/Controllers/KnowledgeManagementPortal/KnowledgeManagementPortalRequestAuthenticationController.php',
];
$ignoreErrors[] = [
	// identifier: property.notFound
	'message' => '#^Access to an undefined property AidingApp\\\\KnowledgeBase\\\\Models\\\\KnowledgeBaseItem\\|Illuminate\\\\Database\\\\Eloquent\\\\Collection\\<int, AidingApp\\\\KnowledgeBase\\\\Models\\\\KnowledgeBaseItem\\>\\:\\:\\$helpful_votes_count\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/portal/src/Http/Controllers/KnowledgeManagementPortal/StoreKnowledgeBaseArticleVoteController.php',
];
$ignoreErrors[] = [
	// identifier: if.alwaysTrue
	'message' => '#^If condition is always true\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/portal/src/Http/Controllers/KnowledgeManagementPortal/StoreKnowledgeBaseArticleVoteController.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Portal\\\\Http\\\\Requests\\\\GetServiceRequestUploadUrlRequest\\:\\:rules\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/portal/src/Http/Requests/GetServiceRequestUploadUrlRequest.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Portal\\\\Http\\\\Requests\\\\KnowledgeManagementPortalAuthenticationRequest\\:\\:rules\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/portal/src/Http/Requests/KnowledgeManagementPortalAuthenticationRequest.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Portal\\\\Http\\\\Requests\\\\KnowledgeManagementPortalRegisterRequest\\:\\:rules\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/portal/src/Http/Requests/KnowledgeManagementPortalRegisterRequest.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method AidingApp\\\\Portal\\\\Http\\\\Routing\\\\ArticleShowMissingHandler\\:\\:__invoke\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/portal/src/Http/Routing/ArticleShowMissingHandler.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method AidingApp\\\\Portal\\\\Http\\\\Routing\\\\CategoryShowMissingHandler\\:\\:__invoke\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/portal/src/Http/Routing/CategoryShowMissingHandler.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Portal\\\\Jobs\\\\PersistServiceRequestUpload\\:\\:middleware\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/portal/src/Jobs/PersistServiceRequestUpload.php',
];
$ignoreErrors[] = [
	// identifier: missingType.property
	'message' => '#^Property AidingApp\\\\Portal\\\\Jobs\\\\PersistServiceRequestUpload\\:\\:\\$deleteWhenMissingModels has no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/portal/src/Jobs/PersistServiceRequestUpload.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Class AidingApp\\\\Portal\\\\Models\\\\KnowledgeBaseArticleVote uses generic trait Illuminate\\\\Database\\\\Eloquent\\\\Factories\\\\HasFactory but does not specify its types\\: TFactory$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/portal/src/Models/KnowledgeBaseArticleVote.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Portal\\\\Models\\\\KnowledgeBaseArticleVote\\:\\:knowledgeBaseArticle\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/portal/src/Models/KnowledgeBaseArticleVote.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method AidingApp\\\\Portal\\\\Models\\\\KnowledgeBaseArticleVote\\:\\:voter\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/portal/src/Models/KnowledgeBaseArticleVote.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Portal\\\\Models\\\\PortalAuthentication\\:\\:educatable\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/portal/src/Models/PortalAuthentication.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Portal\\\\Models\\\\PortalAuthentication\\:\\:prunable\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder does not specify its types\\: TModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/portal/src/Models/PortalAuthentication.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Portal\\\\Models\\\\PortalGuest\\:\\:knowledgeBaseArticleVotes\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/portal/src/Models/PortalGuest.php',
];
$ignoreErrors[] = [
	// identifier: class.notFound
	'message' => '#^Call to method getKey\\(\\) on an unknown class AidingApp\\\\Portal\\\\Notifications\\\\Contact\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/portal/src/Notifications/AuthenticatePortalNotification.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Portal\\\\Notifications\\\\AuthenticatePortalNotification\\:\\:identifyRecipient\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/portal/src/Notifications/AuthenticatePortalNotification.php',
];
$ignoreErrors[] = [
	// identifier: class.notFound
	'message' => '#^PHPDoc tag @var for variable \\$contact contains unknown class AidingApp\\\\Portal\\\\Notifications\\\\Contact\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/portal/src/Notifications/AuthenticatePortalNotification.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method AidingApp\\\\Portal\\\\Providers\\\\PortalServiceProvider\\:\\:boot\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/portal/src/Providers/PortalServiceProvider.php',
];
$ignoreErrors[] = [
	// identifier: property.nonObject
	'message' => '#^Cannot access property \\$code on object\\|string\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/portal/src/Rules/PortalAuthenticateCodeValidation.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Property AidingApp\\\\Portal\\\\Settings\\\\PortalSettings\\:\\:\\$gdpr_banner_text type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/portal/src/Settings/PortalSettings.php',
];
$ignoreErrors[] = [
	// identifier: method.notFound
	'message' => '#^Call to an undefined method Spatie\\\\Image\\\\Drivers\\\\ImageDriver\\:\\:performOnCollections\\(\\)\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/portal/src/Settings/SettingsProperties/PortalSettingsProperty.php',
];
$ignoreErrors[] = [
	// identifier: class.notFound
	'message' => '#^Call to method can\\(\\) on an unknown class AidingApp\\\\Report\\\\Abstract\\\\User\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/report/src/Abstract/EngagementReport.php',
];
$ignoreErrors[] = [
	// identifier: class.notFound
	'message' => '#^Call to method hasLicense\\(\\) on an unknown class AidingApp\\\\Report\\\\Abstract\\\\User\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/report/src/Abstract/EngagementReport.php',
];
$ignoreErrors[] = [
	// identifier: class.notFound
	'message' => '#^PHPDoc tag @var for variable \\$user contains unknown class AidingApp\\\\Report\\\\Abstract\\\\User\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/report/src/Abstract/EngagementReport.php',
];
$ignoreErrors[] = [
	// identifier: class.notFound
	'message' => '#^Call to method can\\(\\) on an unknown class AidingApp\\\\Report\\\\Filament\\\\Pages\\\\User\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/report/src/Filament/Pages/ServiceRequests.php',
];
$ignoreErrors[] = [
	// identifier: class.notFound
	'message' => '#^PHPDoc tag @var for variable \\$user contains unknown class AidingApp\\\\Report\\\\Filament\\\\Pages\\\\User\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/report/src/Filament/Pages/ServiceRequests.php',
];
$ignoreErrors[] = [
	// identifier: missingType.property
	'message' => '#^Property AidingApp\\\\Report\\\\Filament\\\\Pages\\\\ServiceRequests\\:\\:\\$cacheTag has no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/report/src/Filament/Pages/ServiceRequests.php',
];
$ignoreErrors[] = [
	// identifier: missingType.property
	'message' => '#^Property AidingApp\\\\Report\\\\Filament\\\\Pages\\\\TaskManagement\\:\\:\\$cacheTag has no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/report/src/Filament/Pages/TaskManagement.php',
];
$ignoreErrors[] = [
	// identifier: missingType.parameter
	'message' => '#^Method AidingApp\\\\Report\\\\Filament\\\\Widgets\\\\ChartReportWidget\\:\\:mount\\(\\) has parameter \\$cacheTag with no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/ChartReportWidget.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method AidingApp\\\\Report\\\\Filament\\\\Widgets\\\\ChartReportWidget\\:\\:refreshWidget\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/ChartReportWidget.php',
];
$ignoreErrors[] = [
	// identifier: missingType.parameter
	'message' => '#^Method AidingApp\\\\Report\\\\Filament\\\\Widgets\\\\LineChartReportWidget\\:\\:mount\\(\\) has parameter \\$cacheTag with no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/LineChartReportWidget.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method AidingApp\\\\Report\\\\Filament\\\\Widgets\\\\LineChartReportWidget\\:\\:refreshWidget\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/LineChartReportWidget.php',
];
$ignoreErrors[] = [
	// identifier: return.unusedType
	'message' => '#^Anonymous function never returns null so it can be removed from the return type\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/MostRecentTasksTable.php',
];
$ignoreErrors[] = [
	// identifier: function.impossibleType
	'message' => '#^Call to function is_null\\(\\) with AidingApp\\\\Contact\\\\Models\\\\Contact will always evaluate to false\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/MostRecentTasksTable.php',
];
$ignoreErrors[] = [
	// identifier: match.unhandled
	'message' => '#^Match expression does not handle remaining value\\: class\\-string\\<AidingApp\\\\Contact\\\\Models\\\\Contact\\>&literal\\-string$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/MostRecentTasksTable.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method AidingApp\\\\Report\\\\Filament\\\\Widgets\\\\MostRecentTasksTable\\:\\:mount\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/MostRecentTasksTable.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method AidingApp\\\\Report\\\\Filament\\\\Widgets\\\\MostRecentTasksTable\\:\\:refreshWidget\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/MostRecentTasksTable.php',
];
$ignoreErrors[] = [
	// identifier: ternary.alwaysTrue
	'message' => '#^Ternary operator condition is always true\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/MostRecentTasksTable.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method AidingApp\\\\Report\\\\Filament\\\\Widgets\\\\RecentServiceRequestsTable\\:\\:mount\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/RecentServiceRequestsTable.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method AidingApp\\\\Report\\\\Filament\\\\Widgets\\\\RecentServiceRequestsTable\\:\\:refreshWidget\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/RecentServiceRequestsTable.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method AidingApp\\\\Report\\\\Filament\\\\Widgets\\\\RefreshWidget\\:\\:removeWidgetCache\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/RefreshWidget.php',
];
$ignoreErrors[] = [
	// identifier: missingType.parameter
	'message' => '#^Method AidingApp\\\\Report\\\\Filament\\\\Widgets\\\\RefreshWidget\\:\\:removeWidgetCache\\(\\) has parameter \\$cacheTag with no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/RefreshWidget.php',
];
$ignoreErrors[] = [
	// identifier: method.notFound
	'message' => '#^Call to an undefined method AidingApp\\\\ServiceManagement\\\\Enums\\\\ColumnColorOptions\\:\\:getRgb\\(\\)\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/ServiceRequestStatusDistributionDonutChart.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Report\\\\Filament\\\\Widgets\\\\ServiceRequestStatusDistributionDonutChart\\:\\:getOptions\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/ServiceRequestStatusDistributionDonutChart.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Report\\\\Filament\\\\Widgets\\\\ServiceRequestsOverTimeLineChart\\:\\:getOptions\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/ServiceRequestsOverTimeLineChart.php',
];
$ignoreErrors[] = [
	// identifier: if.alwaysTrue
	'message' => '#^If condition is always true\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/ServiceRequestsStats.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Report\\\\Filament\\\\Widgets\\\\ServiceRequestsStats\\:\\:calculateAllServiceRequestStats\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/ServiceRequestsStats.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Report\\\\Filament\\\\Widgets\\\\ServiceRequestsStats\\:\\:calculateAverageServiceResolutionTime\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/ServiceRequestsStats.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Report\\\\Filament\\\\Widgets\\\\ServiceRequestsStats\\:\\:calculateOpenServiceRequestStats\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/ServiceRequestsStats.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Report\\\\Filament\\\\Widgets\\\\ServiceRequestsStats\\:\\:getFormattedPercentageChangeDetails\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/ServiceRequestsStats.php',
];
$ignoreErrors[] = [
	// identifier: missingType.parameter
	'message' => '#^Method AidingApp\\\\Report\\\\Filament\\\\Widgets\\\\ServiceRequestsStats\\:\\:wasOpenAtIntervalStart\\(\\) has parameter \\$openStatusIds with no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/ServiceRequestsStats.php',
];
$ignoreErrors[] = [
	// identifier: deadCode.unreachable
	'message' => '#^Unreachable statement \\- code above always terminates\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/ServiceRequestsStats.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method AidingApp\\\\Report\\\\Filament\\\\Widgets\\\\StatsOverviewReportWidget\\:\\:mount\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/StatsOverviewReportWidget.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method AidingApp\\\\Report\\\\Filament\\\\Widgets\\\\StatsOverviewReportWidget\\:\\:refreshWidget\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/StatsOverviewReportWidget.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Report\\\\Filament\\\\Widgets\\\\TaskCumulativeCountLineChart\\:\\:getOptions\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/TaskCumulativeCountLineChart.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method AidingApp\\\\Report\\\\Filament\\\\Widgets\\\\TopServiceRequestTypesTable\\:\\:mount\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/TopServiceRequestTypesTable.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method AidingApp\\\\Report\\\\Filament\\\\Widgets\\\\TopServiceRequestTypesTable\\:\\:refreshWidget\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/TopServiceRequestTypesTable.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method AidingApp\\\\Report\\\\Providers\\\\ReportServiceProvider\\:\\:boot\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/report/src/Providers/ReportServiceProvider.php',
];
$ignoreErrors[] = [
	// identifier: arguments.count
	'message' => '#^Method Faker\\\\Generator\\:\\:word\\(\\) invoked with 1 parameter, 0 required\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/database/factories/IncidentFactory.php',
];
$ignoreErrors[] = [
	// identifier: arguments.count
	'message' => '#^Method Faker\\\\Generator\\:\\:word\\(\\) invoked with 1 parameter, 0 required\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/database/factories/IncidentSeverityFactory.php',
];
$ignoreErrors[] = [
	// identifier: arguments.count
	'message' => '#^Method Faker\\\\Generator\\:\\:word\\(\\) invoked with 1 parameter, 0 required\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/database/factories/IncidentStatusFactory.php',
];
$ignoreErrors[] = [
	// identifier: arguments.count
	'message' => '#^Method Faker\\\\Generator\\:\\:word\\(\\) invoked with 1 parameter, 0 required\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/database/factories/ServiceMonitoringTargetFactory.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Database\\\\Factories\\\\ServiceRequestPriorityFactory\\:\\:high\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Factories\\\\Factory does not specify its types\\: TModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/database/factories/ServiceRequestPriorityFactory.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Database\\\\Factories\\\\ServiceRequestPriorityFactory\\:\\:low\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Factories\\\\Factory does not specify its types\\: TModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/database/factories/ServiceRequestPriorityFactory.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Database\\\\Factories\\\\ServiceRequestPriorityFactory\\:\\:medium\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Factories\\\\Factory does not specify its types\\: TModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/database/factories/ServiceRequestPriorityFactory.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Database\\\\Factories\\\\ServiceRequestStatusFactory\\:\\:closed\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Factories\\\\Factory does not specify its types\\: TModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/database/factories/ServiceRequestStatusFactory.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Database\\\\Factories\\\\ServiceRequestStatusFactory\\:\\:in_progress\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Factories\\\\Factory does not specify its types\\: TModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/database/factories/ServiceRequestStatusFactory.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Database\\\\Factories\\\\ServiceRequestStatusFactory\\:\\:open\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Factories\\\\Factory does not specify its types\\: TModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/database/factories/ServiceRequestStatusFactory.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Database\\\\Factories\\\\ServiceRequestTypeFactory\\:\\:icons\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/database/factories/ServiceRequestTypeFactory.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$column of method Illuminate\\\\Database\\\\Schema\\\\Blueprint\\:\\:dropConstrainedForeignId\\(\\) expects string, array\\<int, string\\> given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/database/migrations/2024_10_11_154630_add_assignment_type_columns_to_service_request_types_table.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Property class@anonymous/app\\-modules/service\\-management/database/migrations/2025_02_11_095555_seed_permissions_for_incidents\\.php\\:40\\:\\:\\$guards type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/database/migrations/2025_02_11_095555_seed_permissions_for_incidents.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Property class@anonymous/app\\-modules/service\\-management/database/migrations/2025_02_11_095555_seed_permissions_for_incidents\\.php\\:40\\:\\:\\$permissions type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/database/migrations/2025_02_11_095555_seed_permissions_for_incidents.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Property class@anonymous/app\\-modules/service\\-management/database/migrations/2025_02_24_165721_seed_permissions_add_incident_updates_permissions\\.php\\:40\\:\\:\\$guards type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/database/migrations/2025_02_24_165721_seed_permissions_add_incident_updates_permissions.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Property class@anonymous/app\\-modules/service\\-management/database/migrations/2025_02_24_165721_seed_permissions_add_incident_updates_permissions\\.php\\:40\\:\\:\\$permissions type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/database/migrations/2025_02_24_165721_seed_permissions_add_incident_updates_permissions.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Property class@anonymous/app\\-modules/service\\-management/database/migrations/2025_03_18_165045_seed_permissions_for_service_monitorings\\.php\\:40\\:\\:\\$guards type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/database/migrations/2025_03_18_165045_seed_permissions_for_service_monitorings.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Property class@anonymous/app\\-modules/service\\-management/database/migrations/2025_03_18_165045_seed_permissions_for_service_monitorings\\.php\\:40\\:\\:\\$permissions type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/database/migrations/2025_03_18_165045_seed_permissions_for_service_monitorings.php',
];
$ignoreErrors[] = [
	// identifier: if.alwaysFalse
	'message' => '#^If condition is always false\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Actions/ChangeRequest/ApproveChangeRequest.php',
];
$ignoreErrors[] = [
	// identifier: argument.missing
	'message' => '#^Missing parameter \\$relatedModel\\|newState \\(BackedEnum\\|Illuminate\\\\Database\\\\Eloquent\\\\Model\\|string\\) in call to method App\\\\Models\\\\States\\\\StateMachine\\:\\:transitionTo\\(\\)\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Actions/ChangeRequest/ApproveChangeRequest.php',
];
$ignoreErrors[] = [
	// identifier: argument.unknown
	'message' => '#^Unknown parameter \\$newState in call to method App\\\\Models\\\\States\\\\StateMachine\\:\\:transitionTo\\(\\)\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Actions/ChangeRequest/ApproveChangeRequest.php',
];
$ignoreErrors[] = [
	// identifier: argument.unknown
	'message' => '#^Unknown parameter \\$relatedModel in call to method App\\\\Models\\\\States\\\\StateMachine\\:\\:transitionTo\\(\\)\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Actions/ChangeRequest/ApproveChangeRequest.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Actions\\\\CreateServiceRequestAction\\:\\:execute\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Actions/CreateServiceRequestAction.php',
];
$ignoreErrors[] = [
	// identifier: nullsafe.neverNull
	'message' => '#^Using nullsafe method call on non\\-nullable type AidingApp\\\\ServiceManagement\\\\Enums\\\\ServiceRequestTypeAssignmentTypes\\. Use \\-\\> instead\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Actions/CreateServiceRequestAction.php',
];
$ignoreErrors[] = [
	// identifier: nullsafe.neverNull
	'message' => '#^Using nullsafe property access on non\\-nullable type AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestType\\. Use \\-\\> instead\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Actions/CreateServiceRequestAction.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Actions\\\\CreateServiceRequestHistory\\:\\:__construct\\(\\) has parameter \\$changes with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Actions/CreateServiceRequestHistory.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Actions\\\\CreateServiceRequestHistory\\:\\:__construct\\(\\) has parameter \\$original with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Actions/CreateServiceRequestHistory.php',
];
$ignoreErrors[] = [
	// identifier: property.notFound
	'message' => '#^Access to an undefined property AidingApp\\\\Form\\\\Models\\\\Submissible\\:\\:\\$type\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Actions/GenerateServiceRequestFormKitSchema.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Actions\\\\GenerateServiceRequestFormKitSchema\\:\\:__invoke\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Actions/GenerateServiceRequestFormKitSchema.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Actions\\\\ResolveUploadsMediaCollectionForServiceRequest\\:\\:__invoke\\(\\) should return AidingApp\\\\ServiceManagement\\\\Models\\\\MediaCollections\\\\UploadsMediaCollection but returns Spatie\\\\MediaLibrary\\\\MediaCollections\\\\MediaCollection\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Actions/ResolveUploadsMediaCollectionForServiceRequest.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\DataTransferObjects\\\\ServiceRequestDataObject\\:\\:fromData\\(\\) has parameter \\$data with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/DataTransferObjects/ServiceRequestDataObject.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\DataTransferObjects\\\\ServiceRequestDataObject\\:\\:fromData\\(\\) should return static\\(AidingApp\\\\ServiceManagement\\\\DataTransferObjects\\\\ServiceRequestDataObject\\) but returns AidingApp\\\\ServiceManagement\\\\DataTransferObjects\\\\ServiceRequestDataObject\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/DataTransferObjects/ServiceRequestDataObject.php',
];
$ignoreErrors[] = [
	// identifier: match.unreachable
	'message' => '#^Match arm is unreachable because previous comparison is always true\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Enums/SystemIncidentStatusClassification.php',
];
$ignoreErrors[] = [
	// identifier: missingType.property
	'message' => '#^Property AidingApp\\\\ServiceManagement\\\\Exceptions\\\\AttemptedToAssignNonManagerToServiceRequest\\:\\:\\$message has no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Exceptions/AttemptedToAssignNonManagerToServiceRequest.php',
];
$ignoreErrors[] = [
	// identifier: missingType.property
	'message' => '#^Property AidingApp\\\\ServiceManagement\\\\Exceptions\\\\ServiceRequestNumberExceededReRollsException\\:\\:\\$message has no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Exceptions/ServiceRequestNumberExceededReRollsException.php',
];
$ignoreErrors[] = [
	// identifier: missingType.property
	'message' => '#^Property AidingApp\\\\ServiceManagement\\\\Exceptions\\\\ServiceRequestNumberUpdateAttemptException\\:\\:\\$message has no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Exceptions/ServiceRequestNumberUpdateAttemptException.php',
];
$ignoreErrors[] = [
	// identifier: method.notFound
	'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Builder\\:\\:withTrashed\\(\\)\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ChangeRequestResource/Pages/EditChangeRequest.php',
];
$ignoreErrors[] = [
	// identifier: varTag.unresolvableType
	'message' => '#^PHPDoc tag @var for variable \\$resource contains unresolvable type\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ChangeRequestResource/Pages/EditChangeRequest.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Filament\\\\Resources\\\\ChangeRequestStatusResource\\:\\:getEloquentQuery\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder does not specify its types\\: TModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ChangeRequestStatusResource.php',
];
$ignoreErrors[] = [
	// identifier: varTag.unresolvableType
	'message' => '#^PHPDoc tag @var for variable \\$resource contains unresolvable type\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ChangeRequestStatusResource/Pages/EditChangeRequestStatus.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Filament\\\\Resources\\\\ChangeRequestTypeResource\\:\\:getEloquentQuery\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder does not specify its types\\: TModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ChangeRequestTypeResource.php',
];
$ignoreErrors[] = [
	// identifier: varTag.unresolvableType
	'message' => '#^PHPDoc tag @var for variable \\$resource contains unresolvable type\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ChangeRequestTypeResource/Pages/EditChangeRequestType.php',
];
$ignoreErrors[] = [
	// identifier: varTag.unresolvableType
	'message' => '#^PHPDoc tag @var for variable \\$resource contains unresolvable type\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/IncidentResource/Pages/EditIncident.php',
];
$ignoreErrors[] = [
	// identifier: property.notFound
	'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$status\\.$#',
	'count' => 3,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/IncidentResource/Pages/ManageIncidentUpdate.php',
];
$ignoreErrors[] = [
	// identifier: argument.templateType
	'message' => '#^Unable to resolve the template type TGroupKey in call to method Illuminate\\\\Support\\\\Collection\\<int,AidingApp\\\\ServiceManagement\\\\Models\\\\IncidentStatus\\>\\:\\:groupBy\\(\\)$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/IncidentResource/Pages/ManageIncidentUpdate.php',
];
$ignoreErrors[] = [
	// identifier: nullsafe.neverNull
	'message' => '#^Using nullsafe property access on non\\-nullable type Illuminate\\\\Database\\\\Eloquent\\\\Model\\. Use \\-\\> instead\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/IncidentResource/Pages/ManageIncidentUpdate.php',
];
$ignoreErrors[] = [
	// identifier: varTag.unresolvableType
	'message' => '#^PHPDoc tag @var for variable \\$resource contains unresolvable type\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/IncidentSeverityResource/Pages/EditIncidentSeverity.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$length of method Filament\\\\Forms\\\\Components\\\\TextInput\\:\\:maxLength\\(\\) expects Closure\\|int\\|null, \'255\' given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/IncidentStatusResource/Pages/CreateIncidentStatus.php',
];
$ignoreErrors[] = [
	// identifier: varTag.unresolvableType
	'message' => '#^PHPDoc tag @var for variable \\$resource contains unresolvable type\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/IncidentStatusResource/Pages/EditIncidentStatus.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$length of method Filament\\\\Forms\\\\Components\\\\TextInput\\:\\:maxLength\\(\\) expects Closure\\|int\\|null, \'255\' given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/IncidentStatusResource/Pages/EditIncidentStatus.php',
];
$ignoreErrors[] = [
	// identifier: class.notFound
	'message' => '#^Access to property \\$name on an unknown class AidingApp\\\\ServiceManagement\\\\Filament\\\\Resources\\\\IncidentStatusResource\\\\Pages\\\\IncidentStatus\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/IncidentStatusResource/Pages/ViewIncidentStatus.php',
];
$ignoreErrors[] = [
	// identifier: class.notFound
	'message' => '#^PHPDoc tag @var for variable \\$record contains unknown class AidingApp\\\\ServiceManagement\\\\Filament\\\\Resources\\\\IncidentStatusResource\\\\Pages\\\\IncidentStatus\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/IncidentStatusResource/Pages/ViewIncidentStatus.php',
];
$ignoreErrors[] = [
	// identifier: varTag.unresolvableType
	'message' => '#^PHPDoc tag @var for variable \\$resource contains unresolvable type\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/IncidentUpdateResource/Pages/EditIncidentUpdate.php',
];
$ignoreErrors[] = [
	// identifier: property.notFound
	'message' => '#^Access to an undefined property AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceMonitoringTarget\\:\\:\\$name\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceMonitoringResource/Pages/EditServiceMonitoring.php',
];
$ignoreErrors[] = [
	// identifier: varTag.unresolvableType
	'message' => '#^PHPDoc tag @var for variable \\$resource contains unresolvable type\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceMonitoringResource/Pages/EditServiceMonitoring.php',
];
$ignoreErrors[] = [
	// identifier: property.notFound
	'message' => '#^Access to an undefined property AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceMonitoringTarget\\:\\:\\$name\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceMonitoringResource/Pages/ViewServiceMonitoring.php',
];
$ignoreErrors[] = [
	// identifier: method.notFound
	'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:teams\\(\\)\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceMonitoringResource/Pages/ViewServiceMonitoring.php',
];
$ignoreErrors[] = [
	// identifier: method.notFound
	'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:users\\(\\)\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceMonitoringResource/Pages/ViewServiceMonitoring.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Filament\\\\Resources\\\\ServiceRequestFormResource\\\\Pages\\\\CreateServiceRequestForm\\:\\:fields\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestFormResource/Pages/CreateServiceRequestForm.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Filament\\\\Resources\\\\ServiceRequestFormResource\\\\Pages\\\\CreateServiceRequestForm\\:\\:saveFieldsFromComponents\\(\\) has parameter \\$components with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestFormResource/Pages/CreateServiceRequestForm.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Filament\\\\Resources\\\\ServiceRequestFormResource\\\\Pages\\\\CreateServiceRequestForm\\:\\:saveFieldsFromComponents\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestFormResource/Pages/CreateServiceRequestForm.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$serviceRequestForm of method AidingApp\\\\ServiceManagement\\\\Filament\\\\Resources\\\\ServiceRequestFormResource\\\\Pages\\\\CreateServiceRequestForm\\:\\:saveFieldsFromComponents\\(\\) expects AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestForm, AidingApp\\\\Form\\\\Models\\\\Submissible given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestFormResource/Pages/CreateServiceRequestForm.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Filament\\\\Resources\\\\ServiceRequestFormResource\\\\Pages\\\\EditServiceRequestForm\\:\\:fields\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestFormResource/Pages/EditServiceRequestForm.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Filament\\\\Resources\\\\ServiceRequestFormResource\\\\Pages\\\\EditServiceRequestForm\\:\\:saveFieldsFromComponents\\(\\) has parameter \\$components with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestFormResource/Pages/EditServiceRequestForm.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Filament\\\\Resources\\\\ServiceRequestFormResource\\\\Pages\\\\EditServiceRequestForm\\:\\:saveFieldsFromComponents\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestFormResource/Pages/EditServiceRequestForm.php',
];
$ignoreErrors[] = [
	// identifier: varTag.unresolvableType
	'message' => '#^PHPDoc tag @var for variable \\$resource contains unresolvable type\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestFormResource/Pages/EditServiceRequestForm.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$serviceRequestForm of method AidingApp\\\\ServiceManagement\\\\Filament\\\\Resources\\\\ServiceRequestFormResource\\\\Pages\\\\EditServiceRequestForm\\:\\:saveFieldsFromComponents\\(\\) expects AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestForm, AidingApp\\\\Form\\\\Models\\\\Submissible given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestFormResource/Pages/EditServiceRequestForm.php',
];
$ignoreErrors[] = [
	// identifier: property.notFound
	'message' => '#^Access to an undefined property Filament\\\\Resources\\\\Pages\\\\Page\\:\\:\\$record\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestResource.php',
];
$ignoreErrors[] = [
	// identifier: nullsafe.neverNull
	'message' => '#^Using nullsafe property access on non\\-nullable type AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequest\\. Use \\-\\> instead\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestResource/Actions/AddServiceRequestUpdateBulkAction.php',
];
$ignoreErrors[] = [
	// identifier: nullsafe.neverNull
	'message' => '#^Using nullsafe property access on non\\-nullable type AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequest\\. Use \\-\\> instead\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestResource/Actions/ChangeServiceRequestStatusBulkAction.php',
];
$ignoreErrors[] = [
	// identifier: argument.templateType
	'message' => '#^Unable to resolve the template type TGroupKey in call to method Illuminate\\\\Support\\\\Collection\\<int,AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestStatus\\>\\:\\:groupBy\\(\\)$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestResource/Pages/CreateServiceRequest.php',
];
$ignoreErrors[] = [
	// identifier: property.notFound
	'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$priority\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestResource/Pages/EditServiceRequest.php',
];
$ignoreErrors[] = [
	// identifier: varTag.unresolvableType
	'message' => '#^PHPDoc tag @var for variable \\$resource contains unresolvable type\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestResource/Pages/EditServiceRequest.php',
];
$ignoreErrors[] = [
	// identifier: argument.templateType
	'message' => '#^Unable to resolve the template type TGroupKey in call to method Illuminate\\\\Support\\\\Collection\\<int,AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestStatus\\>\\:\\:groupBy\\(\\)$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestResource/Pages/EditServiceRequest.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Filament\\\\Resources\\\\ServiceRequestResource\\\\Pages\\\\ManageAssignments\\:\\:bootServiceRequestLocked\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestResource/Pages/ManageAssignments.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Filament\\\\Resources\\\\ServiceRequestResource\\\\Pages\\\\ManageAssignments\\:\\:managers\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestResource/Pages/ManageAssignments.php',
];
$ignoreErrors[] = [
	// identifier: staticClassAccess.privateMethod
	'message' => '#^Unsafe call to private method AidingApp\\\\ServiceManagement\\\\Filament\\\\Resources\\\\ServiceRequestResource\\\\Pages\\\\ManageAssignments\\:\\:managers\\(\\) through static\\:\\:\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestResource/Pages/ManageAssignments.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Filament\\\\Resources\\\\ServiceRequestResource\\\\Pages\\\\ManageServiceRequestFormSubmission\\:\\:managers\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestResource/Pages/ManageServiceRequestFormSubmission.php',
];
$ignoreErrors[] = [
	// identifier: staticClassAccess.privateMethod
	'message' => '#^Unsafe call to private method AidingApp\\\\ServiceManagement\\\\Filament\\\\Resources\\\\ServiceRequestResource\\\\Pages\\\\ManageServiceRequestFormSubmission\\:\\:managers\\(\\) through static\\:\\:\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestResource/Pages/ManageServiceRequestFormSubmission.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Filament\\\\Resources\\\\ServiceRequestResource\\\\Pages\\\\ManageServiceRequestUpdate\\:\\:bootServiceRequestLocked\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestResource/Pages/ManageServiceRequestUpdate.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Filament\\\\Resources\\\\ServiceRequestResource\\\\Pages\\\\ManageServiceRequestUpdate\\:\\:managers\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestResource/Pages/ManageServiceRequestUpdate.php',
];
$ignoreErrors[] = [
	// identifier: staticClassAccess.privateMethod
	'message' => '#^Unsafe call to private method AidingApp\\\\ServiceManagement\\\\Filament\\\\Resources\\\\ServiceRequestResource\\\\Pages\\\\ManageServiceRequestUpdate\\:\\:managers\\(\\) through static\\:\\:\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestResource/Pages/ManageServiceRequestUpdate.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Property AidingApp\\\\ServiceManagement\\\\Filament\\\\Resources\\\\ServiceRequestResource\\\\Pages\\\\ServiceRequestTimeline\\:\\:\\$modelsToTimeline type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestResource/Pages/ServiceRequestTimeline.php',
];
$ignoreErrors[] = [
	// identifier: return.unusedType
	'message' => '#^Anonymous function never returns null so it can be removed from the return type\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestResource/Pages/ViewServiceRequest.php',
];
$ignoreErrors[] = [
	// identifier: match.unhandled
	'message' => '#^Match expression does not handle remaining value\\: class\\-string\\<AidingApp\\\\Contact\\\\Models\\\\Contact\\>&literal\\-string$#',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestResource/Pages/ViewServiceRequest.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Filament\\\\Resources\\\\ServiceRequestResource\\\\Pages\\\\ViewServiceRequest\\:\\:bootServiceRequestLocked\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestResource/Pages/ViewServiceRequest.php',
];
$ignoreErrors[] = [
	// identifier: nullsafe.neverNull
	'message' => '#^Using nullsafe property access on non\\-nullable type AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestType\\. Use \\-\\> instead\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestResource/RelationManagers/AssignedToRelationManager.php',
];
$ignoreErrors[] = [
	// identifier: property.notFound
	'message' => '#^Access to an undefined property AidingApp\\\\Form\\\\Models\\\\Submissible\\:\\:\\$is_authenticated\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestResource/RelationManagers/ServiceRequestFormSubmissionRelationManager.php',
];
$ignoreErrors[] = [
	// identifier: property.notFound
	'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$status\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestResource/RelationManagers/ServiceRequestUpdatesRelationManager.php',
];
$ignoreErrors[] = [
	// identifier: argument.templateType
	'message' => '#^Unable to resolve the template type TGroupKey in call to method Illuminate\\\\Support\\\\Collection\\<int,AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestStatus\\>\\:\\:groupBy\\(\\)$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestResource/RelationManagers/ServiceRequestUpdatesRelationManager.php',
];
$ignoreErrors[] = [
	// identifier: nullsafe.neverNull
	'message' => '#^Using nullsafe property access on non\\-nullable type Illuminate\\\\Database\\\\Eloquent\\\\Model\\. Use \\-\\> instead\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestResource/RelationManagers/ServiceRequestUpdatesRelationManager.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Filament\\\\Resources\\\\ServiceRequestStatusResource\\:\\:getEloquentQuery\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder does not specify its types\\: TModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestStatusResource.php',
];
$ignoreErrors[] = [
	// identifier: varTag.unresolvableType
	'message' => '#^PHPDoc tag @var for variable \\$resource contains unresolvable type\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestStatusResource/Pages/EditServiceRequestStatus.php',
];
$ignoreErrors[] = [
	// identifier: catch.neverThrown
	'message' => '#^Dead catch \\- Illuminate\\\\Database\\\\QueryException is never thrown in the try block\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestStatusResource/Pages/ListServiceRequestStatuses.php',
];
$ignoreErrors[] = [
	// identifier: property.notFound
	'message' => '#^Access to an undefined property Filament\\\\Resources\\\\Pages\\\\Page\\:\\:\\$record\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestTypeResource.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Filament\\\\Resources\\\\ServiceRequestTypeResource\\:\\:getEloquentQuery\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder does not specify its types\\: TModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestTypeResource.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#2 \\$needles of static method Illuminate\\\\Support\\\\Str\\:\\:endsWith\\(\\) expects iterable\\<string\\>\\|string, AidingApp\\\\ServiceManagement\\\\Enums\\\\ServiceRequestEmailTemplateType\\:\\:Assigned\\|AidingApp\\\\ServiceManagement\\\\Enums\\\\ServiceRequestEmailTemplateType\\:\\:Closed\\|AidingApp\\\\ServiceManagement\\\\Enums\\\\ServiceRequestEmailTemplateType\\:\\:Created\\|AidingApp\\\\ServiceManagement\\\\Enums\\\\ServiceRequestEmailTemplateType\\:\\:StatusChange\\|AidingApp\\\\ServiceManagement\\\\Enums\\\\ServiceRequestEmailTemplateType\\:\\:SurveyResponse\\|AidingApp\\\\ServiceManagement\\\\Enums\\\\ServiceRequestEmailTemplateType\\:\\:Update given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestTypeResource.php',
];
$ignoreErrors[] = [
	// identifier: method.notFound
	'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:priorities\\(\\)\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestTypeResource/Pages/CreateServiceRequestType.php',
];
$ignoreErrors[] = [
	// identifier: varTag.unresolvableType
	'message' => '#^PHPDoc tag @var for variable \\$resource contains unresolvable type\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestTypeResource/Pages/EditServiceRequestType.php',
];
$ignoreErrors[] = [
	// identifier: varTag.unresolvableType
	'message' => '#^PHPDoc tag @var for variable \\$resource contains unresolvable type\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestTypeResource/Pages/EditServiceRequestTypeAssignments.php',
];
$ignoreErrors[] = [
	// identifier: varTag.unresolvableType
	'message' => '#^PHPDoc tag @var for variable \\$resource contains unresolvable type\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestTypeResource/Pages/EditServiceRequestTypeNotifications.php',
];
$ignoreErrors[] = [
	// identifier: argument.templateType
	'message' => '#^Unable to resolve the template type TKey in call to function collect$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestTypeResource/Pages/EditServiceRequestTypeNotifications.php',
];
$ignoreErrors[] = [
	// identifier: argument.templateType
	'message' => '#^Unable to resolve the template type TValue in call to function collect$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestTypeResource/Pages/EditServiceRequestTypeNotifications.php',
];
$ignoreErrors[] = [
	// identifier: property.notFound
	'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$id\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestTypeResource/Pages/ServiceRequestTypeEmailTemplatePage.php',
];
$ignoreErrors[] = [
	// identifier: method.notFound
	'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:templates\\(\\)\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestTypeResource/Pages/ServiceRequestTypeEmailTemplatePage.php',
];
$ignoreErrors[] = [
	// identifier: varTag.unresolvableType
	'message' => '#^PHPDoc tag @var for variable \\$resource contains unresolvable type\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestTypeResource/Pages/ServiceRequestTypeEmailTemplatePage.php',
];
$ignoreErrors[] = [
	// identifier: nullsafe.neverNull
	'message' => '#^Using nullsafe method call on non\\-nullable type Illuminate\\\\Database\\\\Eloquent\\\\Model\\. Use \\-\\> instead\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestTypeResource/Pages/ServiceRequestTypeEmailTemplatePage.php',
];
$ignoreErrors[] = [
	// identifier: nullsafe.neverNull
	'message' => '#^Using nullsafe property access on non\\-nullable type AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestType\\. Use \\-\\> instead\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestTypeResource/Pages/ViewServiceRequestType.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Filament\\\\Resources\\\\ServiceRequestUpdateResource\\\\Components\\\\ServiceRequestAssignmentViewAction\\:\\:serviceRequestAssignmentInfolist\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestUpdateResource/Components/ServiceRequestAssignmentViewAction.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Filament\\\\Resources\\\\ServiceRequestUpdateResource\\\\Components\\\\ServiceRequestHistoryViewAction\\:\\:serviceRequestHistoryInfolist\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestUpdateResource/Components/ServiceRequestHistoryViewAction.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Filament\\\\Resources\\\\ServiceRequestUpdateResource\\\\Components\\\\ServiceRequestUpdateViewAction\\:\\:serviceRequestUpdateInfolist\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestUpdateResource/Components/ServiceRequestUpdateViewAction.php',
];
$ignoreErrors[] = [
	// identifier: varTag.unresolvableType
	'message' => '#^PHPDoc tag @var for variable \\$resource contains unresolvable type\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestUpdateResource/Pages/EditServiceRequestUpdate.php',
];
$ignoreErrors[] = [
	// identifier: varTag.unresolvableType
	'message' => '#^PHPDoc tag @var for variable \\$resource contains unresolvable type\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/SlaResource/Pages/EditSla.php',
];
$ignoreErrors[] = [
	// identifier: nullsafe.neverNull
	'message' => '#^Using nullsafe method call on non\\-nullable type AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequest\\. Use \\-\\> instead\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Http/Controllers/ServiceRequestFeedbackFormWidgetController.php',
];
$ignoreErrors[] = [
	// identifier: nullsafe.neverNull
	'message' => '#^Using nullsafe property access on non\\-nullable type AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequest\\. Use \\-\\> instead\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/service-management/src/Http/Controllers/ServiceRequestFeedbackFormWidgetController.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$value of static method Illuminate\\\\Support\\\\Facades\\\\Hash\\:\\:check\\(\\) expects string, int given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Http/Controllers/ServiceRequestFormWidgetController.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$value of static method Illuminate\\\\Support\\\\Facades\\\\Hash\\:\\:make\\(\\) expects string, int\\<100000, 999999\\> given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Http/Controllers/ServiceRequestFormWidgetController.php',
];
$ignoreErrors[] = [
	// identifier: assign.propertyType
	'message' => '#^Property AidingApp\\\\ServiceManagement\\\\Models\\\\IdeHelperServiceRequestFormSubmission\\:\\:\\$submitted_at \\(Carbon\\\\CarbonImmutable\\|null\\) does not accept Illuminate\\\\Support\\\\Carbon\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Http/Controllers/ServiceRequestFormWidgetController.php',
];
$ignoreErrors[] = [
	// identifier: property.nonObject
	'message' => '#^Cannot access property \\$priority on object\\|string\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Http/Middleware/ServiceRequestTypeFeedbackIsOn.php',
];
$ignoreErrors[] = [
	// identifier: nullsafe.neverNull
	'message' => '#^Using nullsafe property access on non\\-nullable type object\\|string\\. Use \\-\\> instead\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Http/Middleware/ServiceRequestTypeFeedbackIsOn.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Property AidingApp\\\\ServiceManagement\\\\Livewire\\\\RenderServiceRequestFeedbackForm\\:\\:\\$data type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Livewire/RenderServiceRequestFeedbackForm.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Property AidingApp\\\\ServiceManagement\\\\Livewire\\\\RenderServiceRequestForm\\:\\:\\$data type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Livewire/RenderServiceRequestForm.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ChangeRequest\\:\\:accessNestedRelations\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ChangeRequest.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ChangeRequest\\:\\:accessNestedRelations\\(\\) has parameter \\$relations with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ChangeRequest.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ChangeRequest\\:\\:approvals\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ChangeRequest.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ChangeRequest\\:\\:dynamicMethodChain\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ChangeRequest.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ChangeRequest\\:\\:dynamicMethodChain\\(\\) has parameter \\$methods with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ChangeRequest.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ChangeRequest\\:\\:getStateMachineFields\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ChangeRequest.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ChangeRequest\\:\\:newBelongsToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ChangeRequest.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ChangeRequest\\:\\:newMorphToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ChangeRequest.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ChangeRequest\\:\\:responses\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ChangeRequest.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ChangeRequest\\:\\:status\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ChangeRequest.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ChangeRequest\\:\\:type\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ChangeRequest.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ChangeRequest\\:\\:user\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ChangeRequest.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ChangeRequestResponse\\:\\:changeRequest\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ChangeRequestResponse.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ChangeRequestResponse\\:\\:newBelongsToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ChangeRequestResponse.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ChangeRequestResponse\\:\\:newMorphToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ChangeRequestResponse.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ChangeRequestResponse\\:\\:user\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ChangeRequestResponse.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ChangeRequestStatus\\:\\:changeRequests\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ChangeRequestStatus.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ChangeRequestStatus\\:\\:newBelongsToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ChangeRequestStatus.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ChangeRequestStatus\\:\\:newMorphToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ChangeRequestStatus.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ChangeRequestType\\:\\:changeRequests\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ChangeRequestType.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ChangeRequestType\\:\\:newBelongsToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ChangeRequestType.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ChangeRequestType\\:\\:newMorphToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ChangeRequestType.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ChangeRequestType\\:\\:userApprovers\\(\\) should return AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany but returns Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<App\\\\Models\\\\User, \\$this\\(AidingApp\\\\ServiceManagement\\\\Models\\\\ChangeRequestType\\)\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ChangeRequestType.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\Incident\\:\\:assignedTeam\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/Incident.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\Incident\\:\\:incidentUpdates\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/Incident.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\Incident\\:\\:newBelongsToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/Incident.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\Incident\\:\\:newMorphToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/Incident.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\Incident\\:\\:severity\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/Incident.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\Incident\\:\\:status\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/Incident.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\IncidentSeverity\\:\\:incidents\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/IncidentSeverity.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\IncidentSeverity\\:\\:newBelongsToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/IncidentSeverity.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\IncidentSeverity\\:\\:newMorphToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/IncidentSeverity.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\IncidentSeverity\\:\\:rgbColor\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Casts\\\\Attribute does not specify its types\\: TGet, TSet$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/IncidentSeverity.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\IncidentStatus\\:\\:incidents\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/IncidentStatus.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\IncidentStatus\\:\\:newBelongsToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/IncidentStatus.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\IncidentStatus\\:\\:newMorphToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/IncidentStatus.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\IncidentUpdate\\:\\:incident\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/IncidentUpdate.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\IncidentUpdate\\:\\:newBelongsToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/IncidentUpdate.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\IncidentUpdate\\:\\:newMorphToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/IncidentUpdate.php',
];
$ignoreErrors[] = [
	// identifier: missingType.parameter
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\MediaCollections\\\\UploadsMediaCollection\\:\\:create\\(\\) has parameter \\$name with no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/MediaCollections/UploadsMediaCollection.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\MediaCollections\\\\UploadsMediaCollection\\:\\:create\\(\\) should return static\\(AidingApp\\\\ServiceManagement\\\\Models\\\\MediaCollections\\\\UploadsMediaCollection\\) but returns AidingApp\\\\ServiceManagement\\\\Models\\\\MediaCollections\\\\UploadsMediaCollection\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/MediaCollections/UploadsMediaCollection.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\MediaCollections\\\\UploadsMediaCollection\\:\\:getExtensions\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/MediaCollections/UploadsMediaCollection.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\MediaCollections\\\\UploadsMediaCollection\\:\\:getExtensionsFull\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/MediaCollections/UploadsMediaCollection.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\MediaCollections\\\\UploadsMediaCollection\\:\\:getMimes\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/MediaCollections/UploadsMediaCollection.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\MediaCollections\\\\UploadsMediaCollection\\:\\:mimes\\(\\) has parameter \\$mimes with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/MediaCollections/UploadsMediaCollection.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\Scopes\\\\ClassifiedAs\\:\\:__invoke\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/Scopes/ClassifiedAs.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\Scopes\\\\ClassifiedIn\\:\\:__invoke\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/Scopes/ClassifiedIn.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Class AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceMonitoringTarget uses generic trait Illuminate\\\\Database\\\\Eloquent\\\\Factories\\\\HasFactory but does not specify its types\\: TFactory$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceMonitoringTarget.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceMonitoringTarget\\:\\:newBelongsToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceMonitoringTarget.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceMonitoringTarget\\:\\:newMorphToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceMonitoringTarget.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceMonitoringTarget\\:\\:teams\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceMonitoringTarget.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceMonitoringTarget\\:\\:users\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceMonitoringTarget.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Class AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceMonitoringTargetTeam uses generic trait Illuminate\\\\Database\\\\Eloquent\\\\Factories\\\\HasFactory but does not specify its types\\: TFactory$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceMonitoringTargetTeam.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceMonitoringTargetTeam\\:\\:serviceMonitoringTarget\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceMonitoringTargetTeam.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceMonitoringTargetTeam\\:\\:team\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceMonitoringTargetTeam.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Class AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceMonitoringTargetUser uses generic trait Illuminate\\\\Database\\\\Eloquent\\\\Factories\\\\HasFactory but does not specify its types\\: TFactory$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceMonitoringTargetUser.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceMonitoringTargetUser\\:\\:serviceMonitoringTarget\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceMonitoringTargetUser.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceMonitoringTargetUser\\:\\:user\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceMonitoringTargetUser.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Class AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequest uses generic trait Illuminate\\\\Database\\\\Eloquent\\\\Factories\\\\HasFactory but does not specify its types\\: TFactory$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequest.php',
];
$ignoreErrors[] = [
	// identifier: ternary.elseUnreachable
	'message' => '#^Else branch is unreachable because ternary operator condition is always true\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequest.php',
];
$ignoreErrors[] = [
	// identifier: generics.lessTypes
	'message' => '#^Generic type Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphTo\\<AidingApp\\\\Contact\\\\Models\\\\Contact\\> in PHPDoc tag @return does not specify all template types of class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphTo\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequest.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequest\\:\\:assignedTo\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasOne does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequest.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequest\\:\\:assignments\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequest.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequest\\:\\:createdBy\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequest.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequest\\:\\:division\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequest.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequest\\:\\:feedback\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasOne does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequest.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequest\\:\\:getLatestResponseSeconds\\(\\) should return int but returns float\\.$#',
	'count' => 4,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequest.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequest\\:\\:getResolutionSeconds\\(\\) should return int but returns float\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequest.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequest\\:\\:histories\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequest.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequest\\:\\:initialAssignment\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasOne does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequest.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequest\\:\\:latestInboundServiceRequestUpdate\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasOne does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequest.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequest\\:\\:latestOutboundServiceRequestUpdate\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasOne does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequest.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequest\\:\\:newBelongsToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequest.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequest\\:\\:newMorphToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequest.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequest\\:\\:priority\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequest.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequest\\:\\:respondent\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphTo\\<AidingApp\\\\Contact\\\\Models\\\\Contact\\> but returns Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphTo\\<Illuminate\\\\Database\\\\Eloquent\\\\Model, \\$this\\(AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequest\\)\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequest.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequest\\:\\:scopeLicensedToEducatable\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequest.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequest\\:\\:scopeLicensedToEducatable\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder does not specify its types\\: TModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequest.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequest\\:\\:scopeOpen\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequest.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequest\\:\\:serviceRequestFormSubmission\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequest.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequest\\:\\:serviceRequestUpdates\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequest.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequest\\:\\:status\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequest.php',
];
$ignoreErrors[] = [
	// identifier: booleanNot.alwaysTrue
	'message' => '#^Negated boolean expression is always true\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequest.php',
];
$ignoreErrors[] = [
	// identifier: method.notFound
	'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:assignments\\(\\)\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestAssignment.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestAssignment\\:\\:assignedBy\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestAssignment.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestAssignment\\:\\:getTimelineData\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestAssignment.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestAssignment\\:\\:newBelongsToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestAssignment.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestAssignment\\:\\:newMorphToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestAssignment.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestAssignment\\:\\:serviceRequest\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestAssignment.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestAssignment\\:\\:user\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestAssignment.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestFeedback\\:\\:contact\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestFeedback.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestFeedback\\:\\:serviceRequest\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestFeedback.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestForm\\:\\:fields\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestForm.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestForm\\:\\:steps\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestForm.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestForm\\:\\:submissions\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestForm.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestForm\\:\\:type\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestForm.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestFormAuthentication\\:\\:submissible\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestFormAuthentication.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestFormField\\:\\:step\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestFormField.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestFormField\\:\\:submissible\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestFormField.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestFormStep\\:\\:fields\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestFormStep.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestFormStep\\:\\:submissible\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestFormStep.php',
];
$ignoreErrors[] = [
	// identifier: method.notFound
	'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Builder\\:\\:notSubmitted\\(\\)\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestFormSubmission.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestFormSubmission\\:\\:fields\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestFormSubmission.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestFormSubmission\\:\\:priority\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestFormSubmission.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestFormSubmission\\:\\:requester\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestFormSubmission.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestFormSubmission\\:\\:scopeCanceled\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestFormSubmission.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestFormSubmission\\:\\:scopeCanceled\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder does not specify its types\\: TModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestFormSubmission.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestFormSubmission\\:\\:scopeNotCanceled\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestFormSubmission.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestFormSubmission\\:\\:scopeNotCanceled\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder does not specify its types\\: TModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestFormSubmission.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestFormSubmission\\:\\:scopeNotSubmitted\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestFormSubmission.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestFormSubmission\\:\\:scopeNotSubmitted\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder does not specify its types\\: TModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestFormSubmission.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestFormSubmission\\:\\:scopeRequested\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestFormSubmission.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestFormSubmission\\:\\:scopeRequested\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder does not specify its types\\: TModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestFormSubmission.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestFormSubmission\\:\\:scopeSubmitted\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestFormSubmission.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestFormSubmission\\:\\:scopeSubmitted\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder does not specify its types\\: TModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestFormSubmission.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestFormSubmission\\:\\:serviceRequest\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasOne does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestFormSubmission.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestFormSubmission\\:\\:submissible\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestFormSubmission.php',
];
$ignoreErrors[] = [
	// identifier: method.notFound
	'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:histories\\(\\)\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestHistory.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestHistory\\:\\:formatValues\\(\\) has parameter \\$value with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestHistory.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestHistory\\:\\:formatValues\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestHistory.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestHistory\\:\\:getTimelineData\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestHistory.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestHistory\\:\\:getUpdates\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestHistory.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestHistory\\:\\:newValuesFormatted\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Casts\\\\Attribute does not specify its types\\: TGet, TSet$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestHistory.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestHistory\\:\\:originalValuesFormatted\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Casts\\\\Attribute does not specify its types\\: TGet, TSet$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestHistory.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestHistory\\:\\:serviceRequest\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestHistory.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestPriority\\:\\:newBelongsToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestPriority.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestPriority\\:\\:newMorphToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestPriority.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestPriority\\:\\:serviceRequests\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestPriority.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestPriority\\:\\:sla\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestPriority.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestPriority\\:\\:type\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestPriority.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestStatus\\:\\:newBelongsToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestStatus.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestStatus\\:\\:newMorphToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestStatus.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestStatus\\:\\:serviceRequests\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestStatus.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestType\\:\\:assignmentTypeIndividual\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestType.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestType\\:\\:auditors\\(\\) should return AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany but returns Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<AidingApp\\\\Team\\\\Models\\\\Team, \\$this\\(AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestType\\)\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestType.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestType\\:\\:form\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasOne does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestType.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestType\\:\\:lastAssignedUser\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestType.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestType\\:\\:managers\\(\\) should return AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany but returns Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<AidingApp\\\\Team\\\\Models\\\\Team, \\$this\\(AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestType\\)\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestType.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestType\\:\\:newBelongsToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestType.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestType\\:\\:newMorphToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestType.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestType\\:\\:priorities\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestType.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestType\\:\\:serviceRequests\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasManyThrough does not specify its types\\: TRelatedModel, TIntermediateModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestType.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestType\\:\\:templates\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestType.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Class AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestTypeAuditor uses generic trait Illuminate\\\\Database\\\\Eloquent\\\\Factories\\\\HasFactory but does not specify its types\\: TFactory$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestTypeAuditor.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestTypeAuditor\\:\\:serviceRequestType\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestTypeAuditor.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestTypeAuditor\\:\\:team\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestTypeAuditor.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Class AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestTypeEmailTemplate uses generic trait Illuminate\\\\Database\\\\Eloquent\\\\Factories\\\\HasFactory but does not specify its types\\: TFactory$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestTypeEmailTemplate.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestTypeEmailTemplate\\:\\:newBelongsToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestTypeEmailTemplate.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestTypeEmailTemplate\\:\\:newMorphToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestTypeEmailTemplate.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestTypeEmailTemplate\\:\\:serviceRequestType\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestTypeEmailTemplate.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Class AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestTypeManager uses generic trait Illuminate\\\\Database\\\\Eloquent\\\\Factories\\\\HasFactory but does not specify its types\\: TFactory$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestTypeManager.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestTypeManager\\:\\:serviceRequestType\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestTypeManager.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestTypeManager\\:\\:team\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestTypeManager.php',
];
$ignoreErrors[] = [
	// identifier: method.notFound
	'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:serviceRequestUpdates\\(\\)\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestUpdate.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestUpdate\\:\\:getTimelineData\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestUpdate.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestUpdate\\:\\:newBelongsToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestUpdate.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestUpdate\\:\\:newMorphToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestUpdate.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestUpdate\\:\\:serviceRequest\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestUpdate.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\Sla\\:\\:newBelongsToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/Sla.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\Sla\\:\\:newMorphToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/Sla.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\Sla\\:\\:serviceRequestPriorities\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/Sla.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Notifications\\\\ChangeRequestAwaitingApprovalNotification\\:\\:toDatabase\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Notifications/ChangeRequestAwaitingApprovalNotification.php',
];
$ignoreErrors[] = [
	// identifier: property.notFound
	'message' => '#^Access to an undefined property App\\\\Models\\\\Contracts\\\\Educatable\\:\\:\\$first_name\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Notifications/SendClosedServiceFeedbackNotification.php',
];
$ignoreErrors[] = [
	// identifier: match.unhandled
	'message' => '#^Match expression does not handle remaining value\\: class\\-string&literal\\-string$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Notifications/SendClosedServiceFeedbackNotification.php',
];
$ignoreErrors[] = [
	// identifier: property.notFound
	'message' => '#^Access to an undefined property App\\\\Models\\\\Contracts\\\\Educatable\\:\\:\\$first_name\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Notifications/SendEducatableServiceRequestOpenedNotification.php',
];
$ignoreErrors[] = [
	// identifier: match.unhandled
	'message' => '#^Match expression does not handle remaining value\\: class\\-string&literal\\-string$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Notifications/SendEducatableServiceRequestOpenedNotification.php',
];
$ignoreErrors[] = [
	// identifier: match.unhandled
	'message' => '#^Match expression does not handle remaining value\\: class\\-string$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Notifications/ServiceRequestAssigned.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Notifications\\\\ServiceRequestAssigned\\:\\:toDatabase\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Notifications/ServiceRequestAssigned.php',
];
$ignoreErrors[] = [
	// identifier: match.unhandled
	'message' => '#^Match expression does not handle remaining value\\: class\\-string$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Notifications/ServiceRequestClosed.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Notifications\\\\ServiceRequestClosed\\:\\:toDatabase\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Notifications/ServiceRequestClosed.php',
];
$ignoreErrors[] = [
	// identifier: match.unhandled
	'message' => '#^Match expression does not handle remaining value\\: class\\-string$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Notifications/ServiceRequestCreated.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Notifications\\\\ServiceRequestCreated\\:\\:toDatabase\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Notifications/ServiceRequestCreated.php',
];
$ignoreErrors[] = [
	// identifier: match.unhandled
	'message' => '#^Match expression does not handle remaining value\\: class\\-string$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Notifications/ServiceRequestStatusChanged.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Notifications\\\\ServiceRequestStatusChanged\\:\\:toDatabase\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Notifications/ServiceRequestStatusChanged.php',
];
$ignoreErrors[] = [
	// identifier: match.unhandled
	'message' => '#^Match expression does not handle remaining value\\: class\\-string$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Notifications/ServiceRequestUpdated.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Notifications\\\\ServiceRequestUpdated\\:\\:toDatabase\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Notifications/ServiceRequestUpdated.php',
];
$ignoreErrors[] = [
	// identifier: function.impossibleType
	'message' => '#^Call to function is_null\\(\\) with string will always evaluate to false\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Observers/ChangeRequestObserver.php',
];
$ignoreErrors[] = [
	// identifier: nullsafe.neverNull
	'message' => '#^Using nullsafe property access on non\\-nullable type AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestType\\. Use \\-\\> instead\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Observers/ServiceRequestAssignmentObserver.php',
];
$ignoreErrors[] = [
	// identifier: assign.propertyType
	'message' => '#^Property AidingApp\\\\ServiceManagement\\\\Models\\\\IdeHelperServiceRequest\\:\\:\\$status_updated_at \\(Carbon\\\\CarbonImmutable\\|null\\) does not accept Illuminate\\\\Support\\\\Carbon\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Observers/ServiceRequestObserver.php',
];
$ignoreErrors[] = [
	// identifier: assign.propertyType
	'message' => '#^Property AidingApp\\\\ServiceManagement\\\\Models\\\\IdeHelperServiceRequest\\:\\:\\$time_to_resolution \\(int\\|null\\) does not accept float\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Observers/ServiceRequestObserver.php',
];
$ignoreErrors[] = [
	// identifier: nullsafe.neverNull
	'message' => '#^Using nullsafe method call on non\\-nullable type AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequest\\. Use \\-\\> instead\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Observers/ServiceRequestObserver.php',
];
$ignoreErrors[] = [
	// identifier: nullsafe.neverNull
	'message' => '#^Using nullsafe property access on non\\-nullable type AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequest\\. Use \\-\\> instead\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/service-management/src/Observers/ServiceRequestObserver.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Policies\\\\ChangeRequestPolicy\\:\\:hasAnyLicense\\(\\) has parameter \\$licenses with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Policies/ChangeRequestPolicy.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Policies\\\\ChangeRequestPolicy\\:\\:hasLicenses\\(\\) has parameter \\$licenses with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Policies/ChangeRequestPolicy.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Policies\\\\ChangeRequestStatusPolicy\\:\\:hasAnyLicense\\(\\) has parameter \\$licenses with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Policies/ChangeRequestStatusPolicy.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Policies\\\\ChangeRequestStatusPolicy\\:\\:hasLicenses\\(\\) has parameter \\$licenses with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Policies/ChangeRequestStatusPolicy.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Policies\\\\ChangeRequestTypePolicy\\:\\:hasAnyLicense\\(\\) has parameter \\$licenses with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Policies/ChangeRequestTypePolicy.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Policies\\\\ChangeRequestTypePolicy\\:\\:hasLicenses\\(\\) has parameter \\$licenses with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Policies/ChangeRequestTypePolicy.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Policies\\\\IncidentUpdatePolicy\\:\\:requiredFeatures\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Policies/IncidentUpdatePolicy.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Policies\\\\ServiceRequestAssignmentPolicy\\:\\:requiredFeatures\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Policies/ServiceRequestAssignmentPolicy.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Policies\\\\ServiceRequestFormPolicy\\:\\:hasAnyLicense\\(\\) has parameter \\$licenses with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Policies/ServiceRequestFormPolicy.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Policies\\\\ServiceRequestFormPolicy\\:\\:hasLicenses\\(\\) has parameter \\$licenses with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Policies/ServiceRequestFormPolicy.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Policies\\\\ServiceRequestHistoryPolicy\\:\\:requiredFeatures\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Policies/ServiceRequestHistoryPolicy.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Policies\\\\ServiceRequestPolicy\\:\\:requiredFeatures\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Policies/ServiceRequestPolicy.php',
];
$ignoreErrors[] = [
	// identifier: nullsafe.neverNull
	'message' => '#^Using nullsafe property access on non\\-nullable type AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequest\\. Use \\-\\> instead\\.$#',
	'count' => 7,
	'path' => __DIR__ . '/app-modules/service-management/src/Policies/ServiceRequestPolicy.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Policies\\\\ServiceRequestPriorityPolicy\\:\\:requiredFeatures\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Policies/ServiceRequestPriorityPolicy.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Policies\\\\ServiceRequestStatusPolicy\\:\\:requiredFeatures\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Policies/ServiceRequestStatusPolicy.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Policies\\\\ServiceRequestTypePolicy\\:\\:requiredFeatures\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Policies/ServiceRequestTypePolicy.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Policies\\\\ServiceRequestUpdatePolicy\\:\\:requiredFeatures\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Policies/ServiceRequestUpdatePolicy.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Policies\\\\SlaPolicy\\:\\:requiredFeatures\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Policies/SlaPolicy.php',
];
$ignoreErrors[] = [
	// identifier: parameter.phpDocType
	'message' => '#^PHPDoc tag @param for parameter \\$fail with type Illuminate\\\\Translation\\\\PotentiallyTranslatedString is incompatible with native type Closure\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Rules/ManagedServiceRequestType.php',
];
$ignoreErrors[] = [
	// identifier: method.private
	'message' => '#^Call to private method whereRelation\\(\\) of parent class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<Illuminate\\\\Database\\\\Eloquent\\\\Model,Illuminate\\\\Database\\\\Eloquent\\\\Model\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Rules/ServiceRequestTypeAssignmentsIndividualUserMustBeAManager.php',
];
$ignoreErrors[] = [
	// identifier: missingType.parameter
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Services\\\\ServiceRequestNumber\\\\SqidPlusSixServiceRequestNumberGenerator\\:\\:generateRandomString\\(\\) has parameter \\$length with no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Services/ServiceRequestNumber/SqidPlusSixServiceRequestNumberGenerator.php',
];
$ignoreErrors[] = [
	// identifier: nullsafe.neverNull
	'message' => '#^Using nullsafe property access on non\\-nullable type AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequest\\. Use \\-\\> instead\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Services/ServiceRequestType/IndividualAssigner.php',
];
$ignoreErrors[] = [
	// identifier: nullsafe.neverNull
	'message' => '#^Using nullsafe property access on non\\-nullable type AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestType\\. Use \\-\\> instead\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Services/ServiceRequestType/IndividualAssigner.php',
];
$ignoreErrors[] = [
	// identifier: function.impossibleType
	'message' => '#^Call to function is_null\\(\\) with AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestType will always evaluate to false\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Services/ServiceRequestType/RoundRobinAssigner.php',
];
$ignoreErrors[] = [
	// identifier: function.impossibleType
	'message' => '#^Call to function is_null\\(\\) with AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestType will always evaluate to false\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Services/ServiceRequestType/WorkloadAssigner.php',
];
$ignoreErrors[] = [
	// identifier: method.notFound
	'message' => '#^Call to an undefined method Pest\\\\Expectation\\<Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<AidingApp\\\\Team\\\\Models\\\\Team, AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceMonitoringTarget\\>\\|null\\>\\:\\:exists\\(\\)\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/service-management/tests/Filament/ServiceMonitoring/Pages/CreateServiceMonitoringTest.php',
];
$ignoreErrors[] = [
	// identifier: method.notFound
	'message' => '#^Call to an undefined method Pest\\\\Expectation\\<Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<App\\\\Models\\\\User, AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceMonitoringTarget\\>\\|null\\>\\:\\:exists\\(\\)\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/service-management/tests/Filament/ServiceMonitoring/Pages/CreateServiceMonitoringTest.php',
];
$ignoreErrors[] = [
	// identifier: property.notFound
	'message' => '#^Access to an undefined property AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceMonitoringTarget\\:\\:\\$description\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/tests/Filament/ServiceMonitoring/Pages/EditServiceMonitoringTest.php',
];
$ignoreErrors[] = [
	// identifier: property.notFound
	'message' => '#^Access to an undefined property AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceMonitoringTarget\\:\\:\\$domain\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/tests/Filament/ServiceMonitoring/Pages/EditServiceMonitoringTest.php',
];
$ignoreErrors[] = [
	// identifier: property.notFound
	'message' => '#^Access to an undefined property AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceMonitoringTarget\\:\\:\\$frequency\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/tests/Filament/ServiceMonitoring/Pages/EditServiceMonitoringTest.php',
];
$ignoreErrors[] = [
	// identifier: property.notFound
	'message' => '#^Access to an undefined property AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceMonitoringTarget\\:\\:\\$name\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/tests/Filament/ServiceMonitoring/Pages/EditServiceMonitoringTest.php',
];
$ignoreErrors[] = [
	// identifier: argument.templateType
	'message' => '#^Unable to resolve the template type TAndValue in call to method Pest\\\\Expectation\\<mixed\\>\\:\\:and\\(\\)$#',
	'count' => 3,
	'path' => __DIR__ . '/app-modules/service-management/tests/Filament/ServiceMonitoring/Pages/EditServiceMonitoringTest.php',
];
$ignoreErrors[] = [
	// identifier: argument.templateType
	'message' => '#^Unable to resolve the template type TValue in call to function expect$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/tests/Filament/ServiceMonitoring/Pages/EditServiceMonitoringTest.php',
];
$ignoreErrors[] = [
	// identifier: property.notFound
	'message' => '#^Access to an undefined property AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceMonitoringTarget\\:\\:\\$description\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/tests/Filament/ServiceMonitoring/Pages/ViewServiceMonitoringTest.php',
];
$ignoreErrors[] = [
	// identifier: property.notFound
	'message' => '#^Access to an undefined property AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceMonitoringTarget\\:\\:\\$domain\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/tests/Filament/ServiceMonitoring/Pages/ViewServiceMonitoringTest.php',
];
$ignoreErrors[] = [
	// identifier: property.notFound
	'message' => '#^Access to an undefined property AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceMonitoringTarget\\:\\:\\$frequency\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/tests/Filament/ServiceMonitoring/Pages/ViewServiceMonitoringTest.php',
];
$ignoreErrors[] = [
	// identifier: property.notFound
	'message' => '#^Access to an undefined property AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceMonitoringTarget\\:\\:\\$name\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/tests/Filament/ServiceMonitoring/Pages/ViewServiceMonitoringTest.php',
];
$ignoreErrors[] = [
	// identifier: method.notFound
	'message' => '#^Call to an undefined method Illuminate\\\\Testing\\\\TestResponse\\:\\:assertCanSeeTableRecords\\(\\)\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/tests/IncidentUpdate/ListIncidentUpdatesTest.php',
];
$ignoreErrors[] = [
	// identifier: nullCoalesce.expr
	'message' => '#^Expression on left side of \\?\\? is not nullable\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/tests/RequestFactories/CreateServiceRequestPriorityRequestFactory.php',
];
$ignoreErrors[] = [
	// identifier: arguments.count
	'message' => '#^Method Faker\\\\Generator\\:\\:word\\(\\) invoked with 1 parameter, 0 required\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/tests/RequestFactories/IncidentRequestFactory.php',
];
$ignoreErrors[] = [
	// identifier: arguments.count
	'message' => '#^Method Faker\\\\Generator\\:\\:word\\(\\) invoked with 1 parameter, 0 required\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/tests/RequestFactories/IncidentSeverityRequestFactory.php',
];
$ignoreErrors[] = [
	// identifier: arguments.count
	'message' => '#^Method Faker\\\\Generator\\:\\:word\\(\\) invoked with 1 parameter, 0 required\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/tests/RequestFactories/IncidentStatusRequestFactory.php',
];
$ignoreErrors[] = [
	// identifier: arguments.count
	'message' => '#^Method Faker\\\\Generator\\:\\:word\\(\\) invoked with 1 parameter, 0 required\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/tests/RequestFactories/ServiceMonitoringTargetRequestFactory.php',
];
$ignoreErrors[] = [
	// identifier: method.notFound
	'message' => '#^Call to an undefined method Illuminate\\\\Testing\\\\TestResponse\\:\\:assertCountTableRecords\\(\\)\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/service-management/tests/ServiceRequest/Actions/AddServiceRequestUpdateBulkActionTest.php',
];
$ignoreErrors[] = [
	// identifier: property.notFound
	'message' => '#^Access to an undefined property Pest\\\\Expectation\\<AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestAssignment\\|null\\>\\:\\:\\$user\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/tests/ServiceRequest/CreateServiceRequestTest.php',
];
$ignoreErrors[] = [
	// identifier: method.notFound
	'message' => '#^Call to an undefined method Illuminate\\\\Testing\\\\TestResponse\\:\\:assertCanSeeTableRecords\\(\\)\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/tests/ServiceRequest/ListServiceRequestsTest.php',
];
$ignoreErrors[] = [
	// identifier: method.notFound
	'message' => '#^Call to an undefined method Mockery\\\\ExpectationInterface\\|Mockery\\\\HigherOrderMessage\\:\\:twice\\(\\)\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/tests/ServiceRequest/ServiceRequestNumberTest.php',
];
$ignoreErrors[] = [
	// identifier: method.notFound
	'message' => '#^Call to an undefined method Illuminate\\\\Testing\\\\TestResponse\\:\\:assertCanSeeTableRecords\\(\\)\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/tests/ServiceRequestStatus/ListServiceRequestStatusesTest.php',
];
$ignoreErrors[] = [
	// identifier: method.notFound
	'message' => '#^Call to an undefined method Illuminate\\\\Testing\\\\TestResponse\\:\\:assertCanSeeTableRecords\\(\\)\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/tests/ServiceRequestType/ListServiceRequestTypeTest.php',
];
$ignoreErrors[] = [
	// identifier: property.notFound
	'message' => '#^Access to an undefined property Pest\\\\Expectation\\<AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestType\\|null\\>\\:\\:\\$auditors\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/tests/ServiceRequestType/ServiceRequestTypeAuditorsTest.php',
];
$ignoreErrors[] = [
	// identifier: property.notFound
	'message' => '#^Access to an undefined property Pest\\\\Expectation\\<AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestType\\|null\\>\\:\\:\\$managers\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/tests/ServiceRequestType/ServiceRequestTypeManagersTest.php',
];
$ignoreErrors[] = [
	// identifier: property.notFound
	'message' => '#^Access to an undefined property AidingApp\\\\Contact\\\\Models\\\\Contact\\:\\:\\$full\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/tests/ServiceRequestUpdate/ListServiceRequestUpdatesTest.php',
];
$ignoreErrors[] = [
	// identifier: method.notFound
	'message' => '#^Call to an undefined method Illuminate\\\\Testing\\\\TestResponse\\:\\:assertCanSeeTableRecords\\(\\)\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/tests/ServiceRequestUpdate/ListServiceRequestUpdatesTest.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Task\\\\Filament\\\\Pages\\\\Components\\\\TaskKanbanViewAction\\:\\:taskInfoList\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/task/src/Filament/Pages/Components/TaskKanbanViewAction.php',
];
$ignoreErrors[] = [
	// identifier: ternary.alwaysTrue
	'message' => '#^Ternary operator condition is always true\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/task/src/Filament/Pages/Components/TaskKanbanViewAction.php',
];
$ignoreErrors[] = [
	// identifier: nullsafe.neverNull
	'message' => '#^Using nullsafe method call on non\\-nullable type Illuminate\\\\Auth\\\\AuthManager\\. Use \\-\\> instead\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/task/src/Filament/Pages/Components/TaskKanbanViewAction.php',
];
$ignoreErrors[] = [
	// identifier: nullsafe.neverNull
	'message' => '#^Using nullsafe property access on non\\-nullable type AidingApp\\\\Contact\\\\Models\\\\Contact\\. Use \\-\\> instead\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/task/src/Filament/Pages/Components/TaskKanbanViewAction.php',
];
$ignoreErrors[] = [
	// identifier: method.notFound
	'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:getLicenseType\\(\\)\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/task/src/Filament/RelationManagers/BaseTaskRelationManager.php',
];
$ignoreErrors[] = [
	// identifier: ternary.alwaysTrue
	'message' => '#^Ternary operator condition is always true\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/task/src/Filament/RelationManagers/BaseTaskRelationManager.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Task\\\\Filament\\\\Resources\\\\TaskResource\\\\Components\\\\TaskViewAction\\:\\:taskInfoList\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/task/src/Filament/Resources/TaskResource/Components/TaskViewAction.php',
];
$ignoreErrors[] = [
	// identifier: ternary.alwaysTrue
	'message' => '#^Ternary operator condition is always true\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/task/src/Filament/Resources/TaskResource/Components/TaskViewAction.php',
];
$ignoreErrors[] = [
	// identifier: nullsafe.neverNull
	'message' => '#^Using nullsafe method call on non\\-nullable type Illuminate\\\\Auth\\\\AuthManager\\. Use \\-\\> instead\\.$#',
	'count' => 3,
	'path' => __DIR__ . '/app-modules/task/src/Filament/Resources/TaskResource/Components/TaskViewAction.php',
];
$ignoreErrors[] = [
	// identifier: nullsafe.neverNull
	'message' => '#^Using nullsafe property access on non\\-nullable type AidingApp\\\\Contact\\\\Models\\\\Contact\\. Use \\-\\> instead\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/task/src/Filament/Resources/TaskResource/Components/TaskViewAction.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Task\\\\Filament\\\\Resources\\\\TaskResource\\\\Components\\\\TaskViewHeaderAction\\:\\:taskInfoList\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/task/src/Filament/Resources/TaskResource/Components/TaskViewHeaderAction.php',
];
$ignoreErrors[] = [
	// identifier: ternary.alwaysTrue
	'message' => '#^Ternary operator condition is always true\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/task/src/Filament/Resources/TaskResource/Components/TaskViewHeaderAction.php',
];
$ignoreErrors[] = [
	// identifier: nullsafe.neverNull
	'message' => '#^Using nullsafe method call on non\\-nullable type Illuminate\\\\Auth\\\\AuthManager\\. Use \\-\\> instead\\.$#',
	'count' => 3,
	'path' => __DIR__ . '/app-modules/task/src/Filament/Resources/TaskResource/Components/TaskViewHeaderAction.php',
];
$ignoreErrors[] = [
	// identifier: nullsafe.neverNull
	'message' => '#^Using nullsafe property access on non\\-nullable type AidingApp\\\\Contact\\\\Models\\\\Contact\\. Use \\-\\> instead\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/task/src/Filament/Resources/TaskResource/Components/TaskViewHeaderAction.php',
];
$ignoreErrors[] = [
	// identifier: match.alwaysFalse
	'message' => '#^Match arm comparison between true and false is always false\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/task/src/Filament/Resources/TaskResource/Pages/CreateTask.php',
];
$ignoreErrors[] = [
	// identifier: match.unhandled
	'message' => '#^Match expression does not handle remaining value\\: true$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/task/src/Filament/Resources/TaskResource/Pages/CreateTask.php',
];
$ignoreErrors[] = [
	// identifier: match.alwaysFalse
	'message' => '#^Match arm comparison between true and false is always false\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/task/src/Filament/Resources/TaskResource/Pages/EditTask.php',
];
$ignoreErrors[] = [
	// identifier: match.unhandled
	'message' => '#^Match expression does not handle remaining value\\: true$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/task/src/Filament/Resources/TaskResource/Pages/EditTask.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Task\\\\Filament\\\\Resources\\\\TaskResource\\\\Pages\\\\EditTask\\:\\:editFormFields\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/task/src/Filament/Resources/TaskResource/Pages/EditTask.php',
];
$ignoreErrors[] = [
	// identifier: varTag.unresolvableType
	'message' => '#^PHPDoc tag @var for variable \\$resource contains unresolvable type\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/task/src/Filament/Resources/TaskResource/Pages/EditTask.php',
];
$ignoreErrors[] = [
	// identifier: ternary.alwaysTrue
	'message' => '#^Ternary operator condition is always true\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/task/src/Filament/Resources/TaskResource/Pages/ListTasks.php',
];
$ignoreErrors[] = [
	// identifier: nullsafe.neverNull
	'message' => '#^Using nullsafe property access on non\\-nullable type AidingApp\\\\Contact\\\\Models\\\\Contact\\. Use \\-\\> instead\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/task/src/Filament/Resources/TaskResource/Pages/ListTasks.php',
];
$ignoreErrors[] = [
	// identifier: match.unhandled
	'message' => '#^Match expression does not handle remaining value\\: string$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/task/src/Imports/TaskImporter.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Class AidingApp\\\\Task\\\\Models\\\\Task uses generic trait Illuminate\\\\Database\\\\Eloquent\\\\Factories\\\\HasFactory but does not specify its types\\: TFactory$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/task/src/Models/Task.php',
];
$ignoreErrors[] = [
	// identifier: ternary.elseUnreachable
	'message' => '#^Else branch is unreachable because ternary operator condition is always true\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/task/src/Models/Task.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Task\\\\Models\\\\Task\\:\\:assignedTo\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/task/src/Models/Task.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Task\\\\Models\\\\Task\\:\\:concern\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/task/src/Models/Task.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Task\\\\Models\\\\Task\\:\\:createdBy\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/task/src/Models/Task.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Task\\\\Models\\\\Task\\:\\:getStateMachineFields\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/task/src/Models/Task.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\Task\\\\Models\\\\Task\\:\\:newBelongsToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/task/src/Models/Task.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\Task\\\\Models\\\\Task\\:\\:newMorphToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/task/src/Models/Task.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Task\\\\Models\\\\Task\\:\\:scopeByNextDue\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/task/src/Models/Task.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Task\\\\Models\\\\Task\\:\\:scopeOpen\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/task/src/Models/Task.php',
];
$ignoreErrors[] = [
	// identifier: function.impossibleType
	'message' => '#^Call to function is_null\\(\\) with AidingApp\\\\Contact\\\\Models\\\\Contact will always evaluate to false\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/task/src/Notifications/TaskAssignedToUserNotification.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Task\\\\Notifications\\\\TaskAssignedToUserNotification\\:\\:toDatabase\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/task/src/Notifications/TaskAssignedToUserNotification.php',
];
$ignoreErrors[] = [
	// identifier: nullsafe.neverNull
	'message' => '#^Using nullsafe property access on non\\-nullable type AidingApp\\\\Contact\\\\Models\\\\Contact\\. Use \\-\\> instead\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/task/src/Notifications/TaskAssignedToUserNotification.php',
];
$ignoreErrors[] = [
	// identifier: nullsafe.neverNull
	'message' => '#^Using nullsafe property access on non\\-nullable type AidingApp\\\\Contact\\\\Models\\\\Organization\\. Use \\-\\> instead\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/task/src/Notifications/TaskAssignedToUserNotification.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Task\\\\Policies\\\\TaskPolicy\\:\\:hasAnyLicense\\(\\) has parameter \\$licenses with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/task/src/Policies/TaskPolicy.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Task\\\\Policies\\\\TaskPolicy\\:\\:hasLicenses\\(\\) has parameter \\$licenses with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/task/src/Policies/TaskPolicy.php',
];
$ignoreErrors[] = [
	// identifier: nullsafe.neverNull
	'message' => '#^Using nullsafe method call on non\\-nullable type AidingApp\\\\Contact\\\\Models\\\\Contact\\. Use \\-\\> instead\\.$#',
	'count' => 5,
	'path' => __DIR__ . '/app-modules/task/src/Policies/TaskPolicy.php',
];
$ignoreErrors[] = [
	// identifier: property.notFound
	'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$id\\.$#',
	'count' => 5,
	'path' => __DIR__ . '/app-modules/task/tests/TaskAssignmentTest.php',
];
$ignoreErrors[] = [
	// identifier: argument.templateType
	'message' => '#^Unable to resolve the template type TAndValue in call to method Pest\\\\Expectation\\<int\\|null\\>\\:\\:and\\(\\)$#',
	'count' => 5,
	'path' => __DIR__ . '/app-modules/task/tests/TaskAssignmentTest.php',
];
$ignoreErrors[] = [
	// identifier: method.notFound
	'message' => '#^Call to an undefined method Faker\\\\Generator\\:\\:catchPhrase\\(\\)\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/team/database/factories/TeamFactory.php',
];
$ignoreErrors[] = [
	// identifier: varTag.unresolvableType
	'message' => '#^PHPDoc tag @var for variable \\$resource contains unresolvable type\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/team/src/Filament/Resources/TeamResource/Pages/EditTeam.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Team\\\\Models\\\\Team\\:\\:auditableServiceRequestTypes\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/team/src/Models/Team.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Team\\\\Models\\\\Team\\:\\:division\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/team/src/Models/Team.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Team\\\\Models\\\\Team\\:\\:manageableServiceRequestTypes\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/team/src/Models/Team.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Team\\\\Models\\\\Team\\:\\:serviceMonitoringTargets\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/team/src/Models/Team.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Team\\\\Models\\\\Team\\:\\:users\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/team/src/Models/Team.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Team\\\\Models\\\\TeamUser\\:\\:team\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/team/src/Models/TeamUser.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Team\\\\Models\\\\TeamUser\\:\\:user\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/team/src/Models/TeamUser.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method AidingApp\\\\Team\\\\Observers\\\\TeamUserObserver\\:\\:creating\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/team/src/Observers/TeamUserObserver.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method AidingApp\\\\Team\\\\Providers\\\\TeamServiceProvider\\:\\:boot\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/team/src/Providers/TeamServiceProvider.php',
];
$ignoreErrors[] = [
	// identifier: method.notFound
	'message' => '#^Call to an undefined method Spatie\\\\Image\\\\Drivers\\\\ImageDriver\\:\\:keepOriginalImageFormat\\(\\)\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/theme/src/Settings/SettingsProperties/ThemeSettingsProperty.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Timeline\\\\Actions\\\\AggregatesTimelineRecordsForModel\\:\\:handle\\(\\) has parameter \\$modelsToTimeline with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/timeline/src/Actions/AggregatesTimelineRecordsForModel.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Timeline\\\\Actions\\\\AggregatesTimelineRecordsForModel\\:\\:handle\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/timeline/src/Actions/AggregatesTimelineRecordsForModel.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Property AidingApp\\\\Timeline\\\\Actions\\\\AggregatesTimelineRecordsForModel\\:\\:\\$aggregateRecords with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/timeline/src/Actions/AggregatesTimelineRecordsForModel.php',
];
$ignoreErrors[] = [
	// identifier: missingType.parameter
	'message' => '#^Method AidingApp\\\\Timeline\\\\Actions\\\\SyncTimelineData\\:\\:now\\(\\) has parameter \\$modelsToTimeline with no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/timeline/src/Actions/SyncTimelineData.php',
];
$ignoreErrors[] = [
	// identifier: method.notFound
	'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:timeline\\(\\)\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/timeline/src/Filament/Pages/TimelinePage.php',
];
$ignoreErrors[] = [
	// identifier: missingType.parameter
	'message' => '#^Method AidingApp\\\\Timeline\\\\Filament\\\\Pages\\\\TimelinePage\\:\\:mount\\(\\) has parameter \\$record with no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/timeline/src/Filament/Pages/TimelinePage.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method AidingApp\\\\Timeline\\\\Filament\\\\Pages\\\\TimelinePage\\:\\:viewRecord\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/timeline/src/Filament/Pages/TimelinePage.php',
];
$ignoreErrors[] = [
	// identifier: missingType.parameter
	'message' => '#^Method AidingApp\\\\Timeline\\\\Filament\\\\Pages\\\\TimelinePage\\:\\:viewRecord\\(\\) has parameter \\$key with no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/timeline/src/Filament/Pages/TimelinePage.php',
];
$ignoreErrors[] = [
	// identifier: missingType.parameter
	'message' => '#^Method AidingApp\\\\Timeline\\\\Filament\\\\Pages\\\\TimelinePage\\:\\:viewRecord\\(\\) has parameter \\$morphReference with no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/timeline/src/Filament/Pages/TimelinePage.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Property AidingApp\\\\Timeline\\\\Filament\\\\Pages\\\\TimelinePage\\:\\:\\$modelsToTimeline type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/timeline/src/Filament/Pages/TimelinePage.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Property AidingApp\\\\Timeline\\\\Filament\\\\Pages\\\\TimelinePage\\:\\:\\$timelineRecords with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/timeline/src/Filament/Pages/TimelinePage.php',
];
$ignoreErrors[] = [
	// identifier: method.notFound
	'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:timeline\\(\\)\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/timeline/src/Listeners/AddRecordToTimeline.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Timeline\\\\Models\\\\Contracts\\\\ProvidesATimeline\\:\\:getTimelineData\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/timeline/src/Models/Contracts/ProvidesATimeline.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method AidingApp\\\\Timeline\\\\Models\\\\Timeline\\:\\:scopeForEntity\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/timeline/src/Models/Timeline.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Timeline\\\\Models\\\\Timeline\\:\\:scopeForEntity\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/timeline/src/Models/Timeline.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method AidingApp\\\\Timeline\\\\Models\\\\Timeline\\:\\:timelineable\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/timeline/src/Models/Timeline.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method AidingApp\\\\Timeline\\\\Providers\\\\TimelineServiceProvider\\:\\:boot\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/timeline/src/Providers/TimelineServiceProvider.php',
];
$ignoreErrors[] = [
	// identifier: method.notFound
	'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:engagementResponses\\(\\)\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/timeline/tests/Listeners/AddRecordToTimelineTest.php',
];
$ignoreErrors[] = [
	// identifier: method.notFound
	'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:engagementResponses\\(\\)\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/timeline/tests/Listeners/RemoveRecordFromTimelineTest.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method AidingApp\\\\Webhook\\\\Actions\\\\StoreInboundWebhook\\:\\:handle\\(\\) has parameter \\$payload with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/webhook/src/Actions/StoreInboundWebhook.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method AidingApp\\\\Webhook\\\\DataTransferObjects\\\\SnsMessage\\:\\:fromRequest\\(\\) should return static\\(AidingApp\\\\Webhook\\\\DataTransferObjects\\\\SnsMessage\\) but returns AidingApp\\\\Webhook\\\\DataTransferObjects\\\\SnsMessage\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/webhook/src/DataTransferObjects/SnsMessage.php',
];
$ignoreErrors[] = [
	// identifier: function.impossibleType
	'message' => '#^Call to function is_array\\(\\) with string will always evaluate to false\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/webhook/src/Http/Middleware/HandleAwsSnsRequest.php',
];
$ignoreErrors[] = [
	// identifier: property.notFound
	'message' => '#^Access to an undefined property AidingApp\\\\Webhook\\\\Models\\\\LandlordInboundWebhook\\:\\:\\$event\\.$#',
	'count' => 4,
	'path' => __DIR__ . '/app-modules/webhook/tests/Unit/Http/Middleware/HandleAwsSnsRequestTest.php',
];
$ignoreErrors[] = [
	// identifier: property.notFound
	'message' => '#^Access to an undefined property AidingApp\\\\Webhook\\\\Models\\\\LandlordInboundWebhook\\:\\:\\$payload\\.$#',
	'count' => 4,
	'path' => __DIR__ . '/app-modules/webhook/tests/Unit/Http/Middleware/HandleAwsSnsRequestTest.php',
];
$ignoreErrors[] = [
	// identifier: property.notFound
	'message' => '#^Access to an undefined property AidingApp\\\\Webhook\\\\Models\\\\LandlordInboundWebhook\\:\\:\\$url\\.$#',
	'count' => 4,
	'path' => __DIR__ . '/app-modules/webhook/tests/Unit/Http/Middleware/HandleAwsSnsRequestTest.php',
];
$ignoreErrors[] = [
	// identifier: argument.templateType
	'message' => '#^Unable to resolve the template type TAndValue in call to method Pest\\\\Expectation\\<AidingApp\\\\Webhook\\\\Enums\\\\InboundWebhookSource\\>\\:\\:and\\(\\)$#',
	'count' => 4,
	'path' => __DIR__ . '/app-modules/webhook/tests/Unit/Http/Middleware/HandleAwsSnsRequestTest.php',
];
$ignoreErrors[] = [
	// identifier: argument.templateType
	'message' => '#^Unable to resolve the template type TAndValue in call to method Pest\\\\Expectation\\<mixed\\>\\:\\:and\\(\\)$#',
	'count' => 8,
	'path' => __DIR__ . '/app-modules/webhook/tests/Unit/Http/Middleware/HandleAwsSnsRequestTest.php',
];
$ignoreErrors[] = [
	// identifier: method.notFound
	'message' => '#^Call to an undefined method Illuminate\\\\Container\\\\Container\\:\\:getNamespace\\(\\)\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Actions/Finders/ApplicationModels.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method App\\\\Actions\\\\Finders\\\\ApplicationModels\\:\\:all\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Actions/Finders/ApplicationModels.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Actions\\\\Finders\\\\ApplicationModules\\:\\:moduleConfig\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Actions/Finders/ApplicationModules.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Actions\\\\GetRecordFromMorphAndKey\\:\\:via\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Actions/GetRecordFromMorphAndKey.php',
];
$ignoreErrors[] = [
	// identifier: if.alwaysTrue
	'message' => '#^If condition is always true\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Actions/ResolveEducatableFromEmail.php',
];
$ignoreErrors[] = [
	// identifier: deadCode.unreachable
	'message' => '#^Unreachable statement \\- code above always terminates\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Actions/ResolveEducatableFromEmail.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Class App\\\\Casts\\\\CurrencyCast implements generic interface Illuminate\\\\Contracts\\\\Database\\\\Eloquent\\\\CastsAttributes but does not specify its types\\: TGet, TSet$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Casts/CurrencyCast.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Class App\\\\Casts\\\\Encrypted implements generic interface Illuminate\\\\Contracts\\\\Database\\\\Eloquent\\\\CastsAttributes but does not specify its types\\: TGet, TSet$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Casts/Encrypted.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Class App\\\\Casts\\\\LandlordEncrypted implements generic interface Illuminate\\\\Contracts\\\\Database\\\\Eloquent\\\\CastsAttributes but does not specify its types\\: TGet, TSet$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Casts/LandlordEncrypted.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Class App\\\\Casts\\\\TenantEncrypted implements generic interface Illuminate\\\\Contracts\\\\Database\\\\Eloquent\\\\CastsAttributes but does not specify its types\\: TGet, TSet$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Casts/TenantEncrypted.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Console\\\\Commands\\\\BuildAssets\\:\\:handle\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Console/Commands/BuildAssets.php',
];
$ignoreErrors[] = [
	// identifier: method.protected
	'message' => '#^Call to protected method assignRole\\(\\) of class App\\\\Models\\\\Authenticatable\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Console/Commands/CreateSuperAdmin.php',
];
$ignoreErrors[] = [
	// identifier: missingType.parameter
	'message' => '#^Method App\\\\Console\\\\Commands\\\\GenerateLandlordApiKey\\:\\:setKeyInEnvironmentFile\\(\\) has parameter \\$key with no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Console/Commands/GenerateLandlordApiKey.php',
];
$ignoreErrors[] = [
	// identifier: missingType.parameter
	'message' => '#^Method App\\\\Console\\\\Commands\\\\GenerateLandlordApiKey\\:\\:writeNewEnvironmentFileWith\\(\\) has parameter \\$key with no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Console/Commands/GenerateLandlordApiKey.php',
];
$ignoreErrors[] = [
	// identifier: missingType.parameter
	'message' => '#^Method App\\\\Console\\\\Commands\\\\ValidateGraphQL\\:\\:checkModel\\(\\) has parameter \\$fail with no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Console/Commands/ValidateGraphQL.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method App\\\\Console\\\\Commands\\\\ValidateGraphQL\\:\\:checkModel\\(\\) has parameter \\$models with generic class Illuminate\\\\Support\\\\Collection but does not specify its types\\: TKey, TValue$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Console/Commands/ValidateGraphQL.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method App\\\\Console\\\\Commands\\\\ValidateGraphQL\\:\\:checkModel\\(\\) has parameter \\$types with generic class Illuminate\\\\Support\\\\Collection but does not specify its types\\: TKey, TValue$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Console/Commands/ValidateGraphQL.php',
];
$ignoreErrors[] = [
	// identifier: argument.templateType
	'message' => '#^Unable to resolve the template type TKey in call to function collect$#',
	'count' => 2,
	'path' => __DIR__ . '/app/Console/Commands/ValidateGraphQL.php',
];
$ignoreErrors[] = [
	// identifier: argument.templateType
	'message' => '#^Unable to resolve the template type TValue in call to function collect$#',
	'count' => 2,
	'path' => __DIR__ . '/app/Console/Commands/ValidateGraphQL.php',
];
$ignoreErrors[] = [
	// identifier: missingType.parameter
	'message' => '#^Method App\\\\DataTransferObjects\\\\Casts\\\\DataCast\\:\\:get\\(\\) has parameter \\$payload with no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/DataTransferObjects/Casts/DataCast.php',
];
$ignoreErrors[] = [
	// identifier: missingType.parameter
	'message' => '#^Method App\\\\DataTransferObjects\\\\Casts\\\\DataCast\\:\\:set\\(\\) has parameter \\$payload with no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/DataTransferObjects/Casts/DataCast.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\DataTransferObjects\\\\Casts\\\\DataCast\\:\\:set\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/DataTransferObjects/Casts/DataCast.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\DataTransferObjects\\\\LicenseManagement\\\\LicenseLimitsData\\:\\:getResetWindow\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/DataTransferObjects/LicenseManagement/LicenseLimitsData.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method App\\\\Filament\\\\Forms\\\\Components\\\\EducatableSelect\\:\\:getRelationship\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Filament/Forms/Components/EducatableSelect.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Filament\\\\Pages\\\\AmazonS3\\:\\:mutateFormDataBeforeFill\\(\\) has parameter \\$data with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Filament/Pages/AmazonS3.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Filament\\\\Pages\\\\AmazonS3\\:\\:mutateFormDataBeforeFill\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Filament/Pages/AmazonS3.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Filament\\\\Pages\\\\AmazonS3\\:\\:mutateFormDataBeforeSave\\(\\) has parameter \\$data with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Filament/Pages/AmazonS3.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Filament\\\\Pages\\\\AmazonS3\\:\\:mutateFormDataBeforeSave\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Filament/Pages/AmazonS3.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Property App\\\\Filament\\\\Pages\\\\AmazonS3\\:\\:\\$data type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Filament/Pages/AmazonS3.php',
];
$ignoreErrors[] = [
	// identifier: method.notFound
	'message' => '#^Call to an undefined method Filament\\\\Forms\\\\Components\\\\Checkbox\\:\\:lockedWithoutAnyLicenses\\(\\)\\.$#',
	'count' => 4,
	'path' => __DIR__ . '/app/Filament/Pages/EditProfile.php',
];
$ignoreErrors[] = [
	// identifier: method.notFound
	'message' => '#^Call to an undefined method Filament\\\\Forms\\\\Components\\\\Toggle\\:\\:lockedWithoutAnyLicenses\\(\\)\\.$#',
	'count' => 3,
	'path' => __DIR__ . '/app/Filament/Pages/EditProfile.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Filament\\\\Pages\\\\EditProfile\\:\\:getHoursForDays\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Filament/Pages/EditProfile.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Property App\\\\Filament\\\\Pages\\\\EditProfile\\:\\:\\$data type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Filament/Pages/EditProfile.php',
];
$ignoreErrors[] = [
	// identifier: nullsafe.neverNull
	'message' => '#^Using nullsafe method call on non\\-nullable type Illuminate\\\\Database\\\\Eloquent\\\\Collection\\<int, AidingApp\\\\Team\\\\Models\\\\Team\\>\\. Use \\-\\> instead\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/app/Filament/Pages/EditProfile.php',
];
$ignoreErrors[] = [
	// identifier: booleanAnd.leftAlwaysTrue
	'message' => '#^Left side of && is always true\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Filament/Pages/ManageLicenseSettings.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method App\\\\Filament\\\\Pages\\\\ProductHealth\\:\\:getNavigationBadge\\(\\) should return string\\|null but returns int\\<1, max\\>\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Filament/Pages/ProductHealth.php',
];
$ignoreErrors[] = [
	// identifier: varTag.unresolvableType
	'message' => '#^PHPDoc tag @var for variable \\$resource contains unresolvable type\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Filament/Resources/NotificationSettingResource/Pages/EditNotificationSetting.php',
];
$ignoreErrors[] = [
	// identifier: varTag.unresolvableType
	'message' => '#^PHPDoc tag @var for variable \\$resource contains unresolvable type\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Filament/Resources/SystemUserResource/Pages/EditSystemUser.php',
];
$ignoreErrors[] = [
	// identifier: varTag.unresolvableType
	'message' => '#^PHPDoc tag @var for variable \\$resource contains unresolvable type\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Filament/Resources/TagResource/Pages/EditTag.php',
];
$ignoreErrors[] = [
	// identifier: booleanNot.alwaysTrue
	'message' => '#^Negated boolean expression is always true\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Filament/Resources/UserResource/Actions/AssignLicensesBulkAction.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),Illuminate\\\\Database\\\\Eloquent\\\\Model\\>\\:\\:each\\(\\) expects callable\\(Illuminate\\\\Database\\\\Eloquent\\\\Model, int\\|string\\)\\: mixed, Closure\\(App\\\\Models\\\\User\\)\\: void given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Filament/Resources/UserResource/Actions/AssignLicensesBulkAction.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Property App\\\\Filament\\\\Resources\\\\UserResource\\\\Actions\\\\AssignLicensesBulkAction\\:\\:\\$licenseTypes type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Filament/Resources/UserResource/Actions/AssignLicensesBulkAction.php',
];
$ignoreErrors[] = [
	// identifier: method.protected
	'message' => '#^Call to protected method assignRole\\(\\) of class App\\\\Models\\\\Authenticatable\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Filament/Resources/UserResource/Actions/AssignRolesBulkAction.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),Illuminate\\\\Database\\\\Eloquent\\\\Model\\>\\:\\:each\\(\\) expects callable\\(Illuminate\\\\Database\\\\Eloquent\\\\Model, int\\|string\\)\\: mixed, Closure\\(App\\\\Models\\\\User\\)\\: void given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Filament/Resources/UserResource/Actions/AssignRolesBulkAction.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),Illuminate\\\\Database\\\\Eloquent\\\\Model\\>\\:\\:each\\(\\) expects callable\\(Illuminate\\\\Database\\\\Eloquent\\\\Model, int\\|string\\)\\: mixed, Closure\\(App\\\\Models\\\\User\\)\\: void given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Filament/Resources/UserResource/Actions/AssignTeamBulkAction.php',
];
$ignoreErrors[] = [
	// identifier: varTag.unresolvableType
	'message' => '#^PHPDoc tag @var for variable \\$resource contains unresolvable type\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Filament/Resources/UserResource/Pages/EditUser.php',
];
$ignoreErrors[] = [
	// identifier: class.notFound
	'message' => '#^Call to method isSuperAdmin\\(\\) on an unknown class App\\\\Filament\\\\Resources\\\\UserResource\\\\RelationManagers\\\\User\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Filament/Resources/UserResource/RelationManagers/RolesRelationManager.php',
];
$ignoreErrors[] = [
	// identifier: class.notFound
	'message' => '#^PHPDoc tag @var for variable \\$user contains unknown class App\\\\Filament\\\\Resources\\\\UserResource\\\\RelationManagers\\\\User\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Filament/Resources/UserResource/RelationManagers/RolesRelationManager.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Filament\\\\Widgets\\\\ContactGrowthChart\\:\\:getOptions\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Filament/Widgets/ContactGrowthChart.php',
];
$ignoreErrors[] = [
	// identifier: property.notFound
	'message' => '#^Access to an undefined property AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequest\\:\\:\\$service_request_id\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/app/Filament/Widgets/MyServiceRequests.php',
];
$ignoreErrors[] = [
	// identifier: method.notFound
	'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Builder\\|Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\Relation\\:\\:unread\\(\\)\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Filament/Widgets/Notifications.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method App\\\\Filament\\\\Widgets\\\\Notifications\\:\\:getNotifications\\(\\) return type with generic interface Illuminate\\\\Contracts\\\\Pagination\\\\Paginator does not specify its types\\: TItem$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Filament/Widgets/Notifications.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method App\\\\Filament\\\\Widgets\\\\Notifications\\:\\:getNotificationsQuery\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder does not specify its types\\: TModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Filament/Widgets/Notifications.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method App\\\\Filament\\\\Widgets\\\\Notifications\\:\\:getNotificationsQuery\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\Relation does not specify its types\\: TRelatedModel, TDeclaringModel, TResult$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Filament/Widgets/Notifications.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method App\\\\Filament\\\\Widgets\\\\Notifications\\:\\:getUnreadNotificationsQuery\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder does not specify its types\\: TModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Filament/Widgets/Notifications.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method App\\\\Filament\\\\Widgets\\\\Notifications\\:\\:getUnreadNotificationsQuery\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\Relation does not specify its types\\: TRelatedModel, TDeclaringModel, TResult$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Filament/Widgets/Notifications.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Filament\\\\Widgets\\\\RecentContactsList\\:\\:getColumns\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Filament/Widgets/RecentContactsList.php',
];
$ignoreErrors[] = [
	// identifier: method.notFound
	'message' => '#^Call to an undefined method AidingApp\\\\ServiceManagement\\\\Enums\\\\ColumnColorOptions\\:\\:getRgb\\(\\)\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Filament/Widgets/ServiceRequestDonutChart.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Filament\\\\Widgets\\\\ServiceRequestDonutChart\\:\\:getOptions\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Filament/Widgets/ServiceRequestDonutChart.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Filament\\\\Widgets\\\\ServiceRequestLineChart\\:\\:getOptions\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Filament/Widgets/ServiceRequestLineChart.php',
];
$ignoreErrors[] = [
	// identifier: if.alwaysTrue
	'message' => '#^If condition is always true\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Filament/Widgets/ServiceRequestWidget.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Filament\\\\Widgets\\\\ServiceRequestWidget\\:\\:calculateOpenServiceRequestStats\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Filament/Widgets/ServiceRequestWidget.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Filament\\\\Widgets\\\\ServiceRequestWidget\\:\\:calculateUnassignedServiceRequestStats\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Filament/Widgets/ServiceRequestWidget.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Filament\\\\Widgets\\\\ServiceRequestWidget\\:\\:getFormattedPercentageChangeDetails\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Filament/Widgets/ServiceRequestWidget.php',
];
$ignoreErrors[] = [
	// identifier: missingType.parameter
	'message' => '#^Method App\\\\Filament\\\\Widgets\\\\ServiceRequestWidget\\:\\:getPercentageChange\\(\\) has parameter \\$newValue with no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Filament/Widgets/ServiceRequestWidget.php',
];
$ignoreErrors[] = [
	// identifier: missingType.parameter
	'message' => '#^Method App\\\\Filament\\\\Widgets\\\\ServiceRequestWidget\\:\\:getPercentageChange\\(\\) has parameter \\$oldValue with no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Filament/Widgets/ServiceRequestWidget.php',
];
$ignoreErrors[] = [
	// identifier: missingType.parameter
	'message' => '#^Method App\\\\Filament\\\\Widgets\\\\ServiceRequestWidget\\:\\:wasOpenAtIntervalStart\\(\\) has parameter \\$openStatusIds with no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Filament/Widgets/ServiceRequestWidget.php',
];
$ignoreErrors[] = [
	// identifier: deadCode.unreachable
	'message' => '#^Unreachable statement \\- code above always terminates\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Filament/Widgets/ServiceRequestWidget.php',
];
$ignoreErrors[] = [
	// identifier: property.notFound
	'message' => '#^Access to an undefined property GraphQL\\\\Language\\\\AST\\\\TypeExtensionNode\\:\\:\\$directives\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/GraphQL/Directives/FeatureDirective.php',
];
$ignoreErrors[] = [
	// identifier: property.notFound
	'message' => '#^Access to an undefined property GraphQL\\\\Language\\\\AST\\\\TypeExtensionNode\\:\\:\\$fields\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/GraphQL/Directives/FeatureDirective.php',
];
$ignoreErrors[] = [
	// identifier: property.notFound
	'message' => '#^Access to an undefined property GraphQL\\\\Language\\\\AST\\\\TypeExtensionNode\\:\\:\\$interfaces\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/GraphQL/Directives/FeatureDirective.php',
];
$ignoreErrors[] = [
	// identifier: property.notFound
	'message' => '#^Access to an undefined property GraphQL\\\\Language\\\\AST\\\\TypeExtensionNode\\:\\:\\$name\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/app/GraphQL/Directives/FeatureDirective.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#2 \\$typeWithFields of static method Nuwave\\\\Lighthouse\\\\Schema\\\\AST\\\\ASTHelper\\:\\:addDirectiveToFields\\(\\) expects GraphQL\\\\Language\\\\AST\\\\InterfaceTypeDefinitionNode\\|GraphQL\\\\Language\\\\AST\\\\InterfaceTypeExtensionNode\\|GraphQL\\\\Language\\\\AST\\\\ObjectTypeDefinitionNode\\|GraphQL\\\\Language\\\\AST\\\\ObjectTypeExtensionNode, GraphQL\\\\Language\\\\AST\\\\Node&GraphQL\\\\Language\\\\AST\\\\TypeDefinitionNode given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/GraphQL/Directives/FeatureDirective.php',
];
$ignoreErrors[] = [
	// identifier: assign.propertyType
	'message' => '#^Property GraphQL\\\\Language\\\\AST\\\\FieldDefinitionNode\\:\\:\\$directives \\(GraphQL\\\\Language\\\\AST\\\\NodeList\\<GraphQL\\\\Language\\\\AST\\\\DirectiveNode\\>\\) does not accept GraphQL\\\\Language\\\\AST\\\\NodeList\\<GraphQL\\\\Language\\\\AST\\\\Node\\>\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/GraphQL/Directives/FeatureDirective.php',
];
$ignoreErrors[] = [
	// identifier: foreach.nonIterable
	'message' => '#^Argument of an invalid type Nuwave\\\\Lighthouse\\\\Execution\\\\Arguments\\\\Argument supplied for foreach, only iterables are supported\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/GraphQL/Directives/MorphToRelationDirective.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method App\\\\GraphQL\\\\Directives\\\\MorphToRelationDirective\\:\\:build\\(\\) has parameter \\$builder with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app/GraphQL/Directives/MorphToRelationDirective.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\GraphQL\\\\Directives\\\\MorphToRelationDirective\\:\\:build\\(\\) has parameter \\$relationshipTypes with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/GraphQL/Directives/MorphToRelationDirective.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method App\\\\GraphQL\\\\Scalars\\\\UUID\\:\\:getTypeDefinition\\(\\) should return class\\-string\\<\\(GraphQL\\\\Type\\\\Definition\\\\NamedType&GraphQL\\\\Type\\\\Definition\\\\Type\\)\\|UnitEnum\\>\\|\\(GraphQL\\\\Language\\\\AST\\\\Node&GraphQL\\\\Language\\\\AST\\\\TypeDefinitionNode\\)\\|null but returns \\$this\\(App\\\\GraphQL\\\\Scalars\\\\UUID\\)\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/GraphQL/Scalars/UUID.php',
];
$ignoreErrors[] = [
	// identifier: missingType.parameter
	'message' => '#^Method App\\\\GraphQL\\\\Scalars\\\\UUID\\:\\:validateUUID\\(\\) has parameter \\$value with no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/GraphQL/Scalars/UUID.php',
];
$ignoreErrors[] = [
	// identifier: property.notFound
	'message' => '#^Access to an undefined property GraphQL\\\\Language\\\\AST\\\\Node&GraphQL\\\\Language\\\\AST\\\\ValueNode\\:\\:\\$value\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/GraphQL/Scalars/Year.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Http\\\\Controllers\\\\SetAzureSsoSettingController\\:\\:__invoke\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Http/Controllers/SetAzureSsoSettingController.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Http\\\\Controllers\\\\ViewPublicUserProfileController\\:\\:formatHours\\(\\) has parameter \\$hours with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Http/Controllers/ViewPublicUserProfileController.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method App\\\\Http\\\\Controllers\\\\ViewPublicUserProfileController\\:\\:formatHours\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Http/Controllers/ViewPublicUserProfileController.php',
];
$ignoreErrors[] = [
	// identifier: foreach.emptyArray
	'message' => '#^Empty array passed to foreach\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/app/Http/Middleware/AuthGates.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Http\\\\Middleware\\\\AuthGates\\:\\:handle\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Http/Middleware/AuthGates.php',
];
$ignoreErrors[] = [
	// identifier: missingType.parameter
	'message' => '#^Method App\\\\Http\\\\Middleware\\\\AuthGates\\:\\:handle\\(\\) has parameter \\$request with no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Http/Middleware/AuthGates.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Http\\\\Middleware\\\\SetPreferredLocale\\:\\:handle\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Http/Middleware/SetPreferredLocale.php',
];
$ignoreErrors[] = [
	// identifier: missingType.parameter
	'message' => '#^Method App\\\\Http\\\\Middleware\\\\SetPreferredLocale\\:\\:handle\\(\\) has parameter \\$request with no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Http/Middleware/SetPreferredLocale.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Http\\\\Requests\\\\SetAzureSsoSettingRequest\\:\\:rules\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Http/Requests/SetAzureSsoSettingRequest.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Http\\\\Requests\\\\Tenants\\\\CreateTenantRequest\\:\\:rules\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Http/Requests/Tenants/CreateTenantRequest.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Http\\\\Requests\\\\Tenants\\\\SyncTenantRequest\\:\\:rules\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Http/Requests/Tenants/SyncTenantRequest.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Jobs\\\\CreateTenantUser\\:\\:middleware\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Jobs/CreateTenantUser.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Jobs\\\\DispatchTenantSetupCompleteEvent\\:\\:middleware\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Jobs/DispatchTenantSetupCompleteEvent.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Jobs\\\\MigrateTenantDatabase\\:\\:middleware\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Jobs/MigrateTenantDatabase.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Jobs\\\\SeedTenantDatabase\\:\\:middleware\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Jobs/SeedTenantDatabase.php',
];
$ignoreErrors[] = [
	// identifier: method.notFound
	'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:getStateMachine\\(\\)\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Livewire/TaskKanban.php',
];
$ignoreErrors[] = [
	// identifier: match.alwaysFalse
	'message' => '#^Match arm comparison between true and false is always false\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/app/Livewire/TaskKanban.php',
];
$ignoreErrors[] = [
	// identifier: match.unhandled
	'message' => '#^Match expression does not handle remaining value\\: true$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Livewire/TaskKanban.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Livewire\\\\TaskKanban\\:\\:editFormFields\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Livewire/TaskKanban.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method App\\\\Livewire\\\\TaskKanban\\:\\:getTasks\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Livewire/TaskKanban.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Livewire\\\\TaskKanban\\:\\:render\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Livewire/TaskKanban.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Livewire\\\\TaskKanban\\:\\:viewAction\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Livewire/TaskKanban.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Livewire\\\\TaskKanban\\:\\:viewTask\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Livewire/TaskKanban.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Property App\\\\Livewire\\\\TaskKanban\\:\\:\\$statuses type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Livewire/TaskKanban.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Models\\\\Authenticatable\\:\\:canOrElse\\(\\) has parameter \\$abilities with no value type specified in iterable type iterable\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/Authenticatable.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method App\\\\Models\\\\Authenticatable\\:\\:roles\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/Authenticatable.php',
];
$ignoreErrors[] = [
	// identifier: argument.templateType
	'message' => '#^Unable to resolve the template type TRelatedModel in call to method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:morphToMany\\(\\)$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/Authenticatable.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Class App\\\\Models\\\\BaseModel uses generic trait Illuminate\\\\Database\\\\Eloquent\\\\Factories\\\\HasFactory but does not specify its types\\: TFactory$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/BaseModel.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method App\\\\Models\\\\Contracts\\\\HasTags\\:\\:tags\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/Contracts/HasTags.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method App\\\\Models\\\\NotificationSetting\\:\\:divisions\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/NotificationSetting.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method App\\\\Models\\\\NotificationSetting\\:\\:settings\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/NotificationSetting.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method App\\\\Models\\\\NotificationSettingPivot\\:\\:relatedTo\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/NotificationSettingPivot.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method App\\\\Models\\\\NotificationSettingPivot\\:\\:setting\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/NotificationSettingPivot.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method App\\\\Models\\\\Pronouns\\:\\:newBelongsToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/Pronouns.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method App\\\\Models\\\\Pronouns\\:\\:newMorphToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/Pronouns.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method App\\\\Models\\\\Scopes\\\\EducatableSearch\\:\\:__invoke\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/Scopes/EducatableSearch.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method App\\\\Models\\\\Scopes\\\\EducatableSort\\:\\:__invoke\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/Scopes/EducatableSort.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method App\\\\Models\\\\Scopes\\\\HasLicense\\:\\:__invoke\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/Scopes/HasLicense.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method App\\\\Models\\\\Scopes\\\\HasLicense\\:\\:__invoke\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\Relation but does not specify its types\\: TRelatedModel, TDeclaringModel, TResult$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/Scopes/HasLicense.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method App\\\\Models\\\\Scopes\\\\LicensedToEducatable\\:\\:__invoke\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/Scopes/LicensedToEducatable.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method App\\\\Models\\\\Scopes\\\\SearchBy\\:\\:__invoke\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/Scopes/SearchBy.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method App\\\\Models\\\\Scopes\\\\SetupIsComplete\\:\\:__invoke\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/Scopes/SetupIsComplete.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method App\\\\Models\\\\Scopes\\\\TagsForClass\\:\\:__invoke\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/Scopes/TagsForClass.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method App\\\\Models\\\\Scopes\\\\TagsForType\\:\\:__invoke\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/Scopes/TagsForType.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Models\\\\States\\\\StateMachine\\:\\:accessNestedRelations\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/States/StateMachine.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Models\\\\States\\\\StateMachine\\:\\:accessNestedRelations\\(\\) has parameter \\$relations with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/States/StateMachine.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Models\\\\States\\\\StateMachine\\:\\:checkValidEnum\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/States/StateMachine.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Models\\\\States\\\\StateMachine\\:\\:dynamicMethodChain\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/States/StateMachine.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Models\\\\States\\\\StateMachine\\:\\:dynamicMethodChain\\(\\) has parameter \\$methods with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/States/StateMachine.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method App\\\\Models\\\\States\\\\StateMachine\\:\\:getAllStates\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/States/StateMachine.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method App\\\\Models\\\\States\\\\StateMachine\\:\\:getStateTransitions\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/States/StateMachine.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Models\\\\States\\\\StateMachine\\:\\:transitionTo\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/States/StateMachine.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Models\\\\States\\\\StateMachine\\:\\:transitionTo\\(\\) has parameter \\$additionalData with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/States/StateMachine.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Class App\\\\Models\\\\SystemUser uses generic trait Illuminate\\\\Database\\\\Eloquent\\\\Factories\\\\HasFactory but does not specify its types\\: TFactory$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/SystemUser.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method App\\\\Models\\\\SystemUser\\:\\:newBelongsToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/SystemUser.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method App\\\\Models\\\\SystemUser\\:\\:newMorphToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/SystemUser.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Class App\\\\Models\\\\User uses generic trait Illuminate\\\\Database\\\\Eloquent\\\\Factories\\\\HasFactory but does not specify its types\\: TFactory$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/User.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Models\\\\User\\:\\:allowedOperators\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/User.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Models\\\\User\\:\\:assignTeam\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/User.php',
];
$ignoreErrors[] = [
	// identifier: missingType.parameter
	'message' => '#^Method App\\\\Models\\\\User\\:\\:assignTeam\\(\\) has parameter \\$teamId with no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/User.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method App\\\\Models\\\\User\\:\\:assignedTasks\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/User.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method App\\\\Models\\\\User\\:\\:changeRequestResponses\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/User.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method App\\\\Models\\\\User\\:\\:changeRequestTypes\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/User.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method App\\\\Models\\\\User\\:\\:changeRequests\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/User.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method App\\\\Models\\\\User\\:\\:contactAlerts\\(\\) return type with generic class Staudenmeir\\\\EloquentHasManyDeep\\\\HasManyDeep does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/User.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method App\\\\Models\\\\User\\:\\:contactSubscriptions\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/User.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method App\\\\Models\\\\User\\:\\:conversations\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/User.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method App\\\\Models\\\\User\\:\\:engagementBatches\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/User.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method App\\\\Models\\\\User\\:\\:engagements\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/User.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method App\\\\Models\\\\User\\:\\:licenses\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/User.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method App\\\\Models\\\\User\\:\\:newBelongsToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/User.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method App\\\\Models\\\\User\\:\\:newMorphToMany\\(\\) should return Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany\\<TRelatedModel of Illuminate\\\\Database\\\\Eloquent\\\\Model, TDeclaringModel of Illuminate\\\\Database\\\\Eloquent\\\\Model\\> but returns AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/User.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Models\\\\User\\:\\:orderableColumns\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/User.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method App\\\\Models\\\\User\\:\\:permissionsFromRoles\\(\\) return type with generic class Staudenmeir\\\\EloquentHasManyDeep\\\\HasManyDeep does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/User.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Models\\\\User\\:\\:processGlobalSearch\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/User.php',
];
$ignoreErrors[] = [
	// identifier: missingType.parameter
	'message' => '#^Method App\\\\Models\\\\User\\:\\:processGlobalSearch\\(\\) has parameter \\$data with no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/User.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Models\\\\User\\:\\:processQuery\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/User.php',
];
$ignoreErrors[] = [
	// identifier: missingType.parameter
	'message' => '#^Method App\\\\Models\\\\User\\:\\:processQuery\\(\\) has parameter \\$data with no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/User.php',
];
$ignoreErrors[] = [
	// identifier: missingType.parameter
	'message' => '#^Method App\\\\Models\\\\User\\:\\:processQuery\\(\\) has parameter \\$query with no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/User.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method App\\\\Models\\\\User\\:\\:pronouns\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/User.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Models\\\\User\\:\\:scopeAdvancedFilter\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/User.php',
];
$ignoreErrors[] = [
	// identifier: missingType.parameter
	'message' => '#^Method App\\\\Models\\\\User\\:\\:scopeAdvancedFilter\\(\\) has parameter \\$data with no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/User.php',
];
$ignoreErrors[] = [
	// identifier: missingType.parameter
	'message' => '#^Method App\\\\Models\\\\User\\:\\:scopeAdvancedFilter\\(\\) has parameter \\$query with no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/User.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method App\\\\Models\\\\User\\:\\:serviceMonitoringTargets\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/User.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method App\\\\Models\\\\User\\:\\:serviceRequestAssignments\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/User.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method App\\\\Models\\\\User\\:\\:serviceRequestTypeIndividualAssignment\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/User.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method App\\\\Models\\\\User\\:\\:serviceRequests\\(\\) return type with generic class Staudenmeir\\\\EloquentHasManyDeep\\\\HasManyDeep does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/User.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method App\\\\Models\\\\User\\:\\:subscriptions\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/User.php',
];
$ignoreErrors[] = [
	// identifier: missingType.generics
	'message' => '#^Method App\\\\Models\\\\User\\:\\:teams\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/User.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Models\\\\User\\:\\:whiteListColumns\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/User.php',
];
$ignoreErrors[] = [
	// identifier: missingType.property
	'message' => '#^Property App\\\\Models\\\\User\\:\\:\\$filterable has no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/User.php',
];
$ignoreErrors[] = [
	// identifier: missingType.property
	'message' => '#^Property App\\\\Models\\\\User\\:\\:\\$orderable has no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/User.php',
];
$ignoreErrors[] = [
	// identifier: missingType.property
	'message' => '#^Property App\\\\Multitenancy\\\\Exceptions\\\\TenantAppKeyIsNull\\:\\:\\$message has no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Multitenancy/Exceptions/TenantAppKeyIsNull.php',
];
$ignoreErrors[] = [
	// identifier: missingType.property
	'message' => '#^Property App\\\\Multitenancy\\\\Exceptions\\\\UnableToResolveTenantForEncryptionKey\\:\\:\\$message has no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Multitenancy/Exceptions/UnableToResolveTenantForEncryptionKey.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Multitenancy\\\\Http\\\\Middleware\\\\NeedsTenant\\:\\:handleInvalidRequest\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Multitenancy/Http/Middleware/NeedsTenant.php',
];
$ignoreErrors[] = [
	// identifier: property.notFound
	'message' => '#^Access to an undefined property Spatie\\\\Multitenancy\\\\Models\\\\Tenant\\:\\:\\$name\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Multitenancy/Listeners/SetSentryTenantTag.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Multitenancy\\\\Tasks\\\\PrefixCacheTask\\:\\:setCachePrefix\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Multitenancy/Tasks/PrefixCacheTask.php',
];
$ignoreErrors[] = [
	// identifier: property.notFound
	'message' => '#^Access to an undefined property Spatie\\\\Multitenancy\\\\Models\\\\Tenant\\:\\:\\$key\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Multitenancy/Tasks/SwitchAppKey.php',
];
$ignoreErrors[] = [
	// identifier: property.notFound
	'message' => '#^Access to an undefined property Spatie\\\\Multitenancy\\\\Models\\\\Tenant\\:\\:\\$domain\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Multitenancy/Tasks/SwitchAppUrl.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Multitenancy\\\\Tasks\\\\SwitchAppUrl\\:\\:setAppUrl\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Multitenancy/Tasks/SwitchAppUrl.php',
];
$ignoreErrors[] = [
	// identifier: property.notFound
	'message' => '#^Access to an undefined property Spatie\\\\Multitenancy\\\\Models\\\\Tenant\\:\\:\\$config\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Multitenancy/Tasks/SwitchMailTask.php',
];
$ignoreErrors[] = [
	// identifier: property.notFound
	'message' => '#^Access to an undefined property Spatie\\\\Multitenancy\\\\Models\\\\Tenant\\:\\:\\$domain\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Multitenancy/Tasks/SwitchMailTask.php',
];
$ignoreErrors[] = [
	// identifier: class.notFound
	'message' => '#^Access to property \\$fromName on an unknown class App\\\\Multitenancy\\\\Tasks\\\\TenantMailConfig\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Multitenancy/Tasks/SwitchMailTask.php',
];
$ignoreErrors[] = [
	// identifier: class.notFound
	'message' => '#^Access to property \\$mailer on an unknown class App\\\\Multitenancy\\\\Tasks\\\\TenantMailConfig\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Multitenancy/Tasks/SwitchMailTask.php',
];
$ignoreErrors[] = [
	// identifier: class.notFound
	'message' => '#^Access to property \\$mailers on an unknown class App\\\\Multitenancy\\\\Tasks\\\\TenantMailConfig\\.$#',
	'count' => 7,
	'path' => __DIR__ . '/app/Multitenancy/Tasks/SwitchMailTask.php',
];
$ignoreErrors[] = [
	// identifier: class.notFound
	'message' => '#^PHPDoc tag @var for variable \\$config contains unknown class App\\\\Multitenancy\\\\Tasks\\\\TenantMailConfig\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Multitenancy/Tasks/SwitchMailTask.php',
];
$ignoreErrors[] = [
	// identifier: property.notFound
	'message' => '#^Access to an undefined property Spatie\\\\Multitenancy\\\\Models\\\\Tenant\\:\\:\\$config\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Multitenancy/Tasks/SwitchS3FilesystemTask.php',
];
$ignoreErrors[] = [
	// identifier: property.notFound
	'message' => '#^Access to an undefined property Spatie\\\\Multitenancy\\\\Models\\\\Tenant\\:\\:\\$config\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Multitenancy/Tasks/SwitchS3PublicFilesystemTask.php',
];
$ignoreErrors[] = [
	// identifier: property.notFound
	'message' => '#^Access to an undefined property Spatie\\\\Multitenancy\\\\Models\\\\Tenant\\:\\:\\$domain\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Multitenancy/Tasks/SwitchSessionDriver.php',
];
$ignoreErrors[] = [
	// identifier: property.notFound
	'message' => '#^Access to an undefined property Spatie\\\\Multitenancy\\\\Models\\\\Tenant\\:\\:\\$config\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Multitenancy/Tasks/SwitchTenantDatabasesTask.php',
];
$ignoreErrors[] = [
	// identifier: method.notFound
	'message' => '#^Call to an undefined method Illuminate\\\\Foundation\\\\Auth\\\\User\\:\\:isSuperAdmin\\(\\)\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Providers/AuthServiceProvider.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Property App\\\\Providers\\\\MultiConnectionParallelTestingServiceProvider\\:\\:\\$parallelConnections type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Providers/MultiConnectionParallelTestingServiceProvider.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Providers\\\\RouteServiceProvider\\:\\:configureRateLimiting\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Providers/RouteServiceProvider.php',
];
$ignoreErrors[] = [
	// identifier: missingType.parameter
	'message' => '#^Method App\\\\Rules\\\\EmailNotInUseOrSoftDeleted\\:\\:__construct\\(\\) has parameter \\$currentUserId with no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Rules/EmailNotInUseOrSoftDeleted.php',
];
$ignoreErrors[] = [
	// identifier: missingType.property
	'message' => '#^Property App\\\\Rules\\\\EmailNotInUseOrSoftDeleted\\:\\:\\$currentUserId has no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Rules/EmailNotInUseOrSoftDeleted.php',
];
$ignoreErrors[] = [
	// identifier: missingType.property
	'message' => '#^Property App\\\\Rules\\\\EmailNotInUseOrSoftDeleted\\:\\:\\$message has no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Rules/EmailNotInUseOrSoftDeleted.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Settings\\\\LicenseSettings\\:\\:encrypted\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Settings/LicenseSettings.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Settings\\\\OlympusSettings\\:\\:encrypted\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Settings/OlympusSettings.php',
];
$ignoreErrors[] = [
	// identifier: ternary.elseUnreachable
	'message' => '#^Else branch is unreachable because ternary operator condition is always true\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Support/BulkProcessingMachine.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Support\\\\BulkProcessingMachine\\:\\:make\\(\\) has parameter \\$records with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Support/BulkProcessingMachine.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method App\\\\Support\\\\BulkProcessingMachine\\:\\:records\\(\\) has parameter \\$records with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Support/BulkProcessingMachine.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Support\\\\FilterQueryBuilder\\:\\:apply\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Support/FilterQueryBuilder.php',
];
$ignoreErrors[] = [
	// identifier: missingType.parameter
	'message' => '#^Method App\\\\Support\\\\FilterQueryBuilder\\:\\:apply\\(\\) has parameter \\$data with no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Support/FilterQueryBuilder.php',
];
$ignoreErrors[] = [
	// identifier: missingType.parameter
	'message' => '#^Method App\\\\Support\\\\FilterQueryBuilder\\:\\:apply\\(\\) has parameter \\$query with no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Support/FilterQueryBuilder.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Support\\\\FilterQueryBuilder\\:\\:contains\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Support/FilterQueryBuilder.php',
];
$ignoreErrors[] = [
	// identifier: missingType.parameter
	'message' => '#^Method App\\\\Support\\\\FilterQueryBuilder\\:\\:contains\\(\\) has parameter \\$filter with no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Support/FilterQueryBuilder.php',
];
$ignoreErrors[] = [
	// identifier: missingType.parameter
	'message' => '#^Method App\\\\Support\\\\FilterQueryBuilder\\:\\:contains\\(\\) has parameter \\$query with no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Support/FilterQueryBuilder.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Support\\\\FilterQueryBuilder\\:\\:isNestedColumn\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Support/FilterQueryBuilder.php',
];
$ignoreErrors[] = [
	// identifier: missingType.parameter
	'message' => '#^Method App\\\\Support\\\\FilterQueryBuilder\\:\\:isNestedColumn\\(\\) has parameter \\$column with no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Support/FilterQueryBuilder.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Support\\\\FilterQueryBuilder\\:\\:makeFilter\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Support/FilterQueryBuilder.php',
];
$ignoreErrors[] = [
	// identifier: missingType.parameter
	'message' => '#^Method App\\\\Support\\\\FilterQueryBuilder\\:\\:makeFilter\\(\\) has parameter \\$filter with no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Support/FilterQueryBuilder.php',
];
$ignoreErrors[] = [
	// identifier: missingType.parameter
	'message' => '#^Method App\\\\Support\\\\FilterQueryBuilder\\:\\:makeFilter\\(\\) has parameter \\$query with no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Support/FilterQueryBuilder.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method App\\\\Support\\\\FilterQueryBuilder\\:\\:makeOrder\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Support/FilterQueryBuilder.php',
];
$ignoreErrors[] = [
	// identifier: missingType.parameter
	'message' => '#^Method App\\\\Support\\\\FilterQueryBuilder\\:\\:makeOrder\\(\\) has parameter \\$data with no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Support/FilterQueryBuilder.php',
];
$ignoreErrors[] = [
	// identifier: missingType.parameter
	'message' => '#^Method App\\\\Support\\\\FilterQueryBuilder\\:\\:makeOrder\\(\\) has parameter \\$query with no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Support/FilterQueryBuilder.php',
];
$ignoreErrors[] = [
	// identifier: missingType.property
	'message' => '#^Property App\\\\Support\\\\FilterQueryBuilder\\:\\:\\$model has no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Support/FilterQueryBuilder.php',
];
$ignoreErrors[] = [
	// identifier: missingType.property
	'message' => '#^Property App\\\\Support\\\\FilterQueryBuilder\\:\\:\\$table has no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/Support/FilterQueryBuilder.php',
];
$ignoreErrors[] = [
	// identifier: parameter.notFound
	'message' => '#^PHPDoc tag @param references unknown parameter\\: \\$id$#',
	'count' => 1,
	'path' => __DIR__ . '/app/View/Components/SelectList.php',
];
$ignoreErrors[] = [
	// identifier: missingType.property
	'message' => '#^Property App\\\\View\\\\Components\\\\SelectList\\:\\:\\$options has no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/app/View/Components/SelectList.php',
];
$ignoreErrors[] = [
	// identifier: class.notFound
	'message' => '#^PHPDoc tag @extends has invalid type App\\\\Models\\\\Enrollment\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/database/factories/EnrollmentFactory.php',
];
$ignoreErrors[] = [
	// identifier: generics.notSubtype
	'message' => '#^Type App\\\\Models\\\\Enrollment in generic type Illuminate\\\\Database\\\\Eloquent\\\\Factories\\\\Factory\\<App\\\\Models\\\\Enrollment\\> in PHPDoc tag @extends is not subtype of template type TModel of Illuminate\\\\Database\\\\Eloquent\\\\Model of class Illuminate\\\\Database\\\\Eloquent\\\\Factories\\\\Factory\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/database/factories/EnrollmentFactory.php',
];
$ignoreErrors[] = [
	// identifier: class.notFound
	'message' => '#^PHPDoc tag @extends has invalid type App\\\\Models\\\\Program\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/database/factories/ProgramFactory.php',
];
$ignoreErrors[] = [
	// identifier: generics.notSubtype
	'message' => '#^Type App\\\\Models\\\\Program in generic type Illuminate\\\\Database\\\\Eloquent\\\\Factories\\\\Factory\\<App\\\\Models\\\\Program\\> in PHPDoc tag @extends is not subtype of template type TModel of Illuminate\\\\Database\\\\Eloquent\\\\Model of class Illuminate\\\\Database\\\\Eloquent\\\\Factories\\\\Factory\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/database/factories/ProgramFactory.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#2 \\$guardName of method class@anonymous/database/migrations/2024_05_15_200656_seed_permissions_for_tags\\.php\\:42\\:\\:deletePermissions\\(\\) expects string, array given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/database/migrations/2024_05_15_200656_seed_permissions_for_tags.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Property class@anonymous/database/migrations/2024_05_15_200656_seed_permissions_for_tags\\.php\\:42\\:\\:\\$guards type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/database/migrations/2024_05_15_200656_seed_permissions_for_tags.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Property class@anonymous/database/migrations/2024_05_15_200656_seed_permissions_for_tags\\.php\\:42\\:\\:\\$permissions type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/database/migrations/2024_05_15_200656_seed_permissions_for_tags.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#2 \\$guardName of method class@anonymous/database/migrations/2024_06_07_094732_seed_permissions_add_report_library\\.php\\:42\\:\\:deletePermissions\\(\\) expects string, array given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/database/migrations/2024_06_07_094732_seed_permissions_add_report_library.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Property class@anonymous/database/migrations/2024_06_07_094732_seed_permissions_add_report_library\\.php\\:42\\:\\:\\$guards type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/database/migrations/2024_06_07_094732_seed_permissions_add_report_library.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Property class@anonymous/database/migrations/2024_06_07_094732_seed_permissions_add_report_library\\.php\\:42\\:\\:\\$permissions type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/database/migrations/2024_06_07_094732_seed_permissions_add_report_library.php',
];
$ignoreErrors[] = [
	// identifier: booleanAnd.leftAlwaysTrue
	'message' => '#^Left side of && is always true\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/database/migrations/2024_07_10_085735_data_update_value_of_time_to_resolution_in_service_request.php',
];
$ignoreErrors[] = [
	// identifier: booleanAnd.rightAlwaysTrue
	'message' => '#^Right side of && is always true\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/database/migrations/2024_07_10_085735_data_update_value_of_time_to_resolution_in_service_request.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method class@anonymous/database/migrations/2024_08_01_175631_data_fix_image_ids_in_tiptap_content\\.php\\:40\\:\\:fixTipTapBlock\\(\\) has parameter \\$block with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/database/migrations/2024_08_01_175631_data_fix_image_ids_in_tiptap_content.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method class@anonymous/database/migrations/2024_08_01_175631_data_fix_image_ids_in_tiptap_content\\.php\\:40\\:\\:fixTipTapBlock\\(\\) has parameter \\$mediaUuidMap with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/database/migrations/2024_08_01_175631_data_fix_image_ids_in_tiptap_content.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method class@anonymous/database/migrations/2024_08_01_175631_data_fix_image_ids_in_tiptap_content\\.php\\:40\\:\\:fixTipTapBlock\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/database/migrations/2024_08_01_175631_data_fix_image_ids_in_tiptap_content.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method class@anonymous/database/migrations/2024_08_01_175631_data_fix_image_ids_in_tiptap_content\\.php\\:40\\:\\:fixTipTapContent\\(\\) has parameter \\$mediaUuidMap with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/database/migrations/2024_08_01_175631_data_fix_image_ids_in_tiptap_content.php',
];
$ignoreErrors[] = [
	// identifier: argument.type
	'message' => '#^Parameter \\#2 \\$guardName of method class@anonymous/database/migrations/2024_11_19_165344_seed_permissions_for_product_admin\\.php\\:42\\:\\:deletePermissions\\(\\) expects string, array given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/database/migrations/2024_11_19_165344_seed_permissions_for_product_admin.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Property class@anonymous/database/migrations/2024_11_19_165344_seed_permissions_for_product_admin\\.php\\:42\\:\\:\\$guards type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/database/migrations/2024_11_19_165344_seed_permissions_for_product_admin.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Property class@anonymous/database/migrations/2024_11_19_165344_seed_permissions_for_product_admin\\.php\\:42\\:\\:\\$permissions type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/database/migrations/2024_11_19_165344_seed_permissions_for_product_admin.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Property class@anonymous/database/migrations/2024_11_21_002436_seed_permissions_remove_assistant_permissions\\.php\\:42\\:\\:\\$guards type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/database/migrations/2024_11_21_002436_seed_permissions_remove_assistant_permissions.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Property class@anonymous/database/migrations/2024_11_21_002436_seed_permissions_remove_assistant_permissions\\.php\\:42\\:\\:\\$permissions type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/database/migrations/2024_11_21_002436_seed_permissions_remove_assistant_permissions.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Property class@anonymous/database/migrations/2024_12_03_064748_seed_permissions_remove_specific_global_settings_permissions\\.php\\:40\\:\\:\\$guards type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/database/migrations/2024_12_03_064748_seed_permissions_remove_specific_global_settings_permissions.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Property class@anonymous/database/migrations/2024_12_03_064748_seed_permissions_remove_specific_global_settings_permissions\\.php\\:40\\:\\:\\$permissions type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/database/migrations/2024_12_03_064748_seed_permissions_remove_specific_global_settings_permissions.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Property class@anonymous/database/migrations/2024_12_09_162908_seed_permissions_delete_unused_product_administration_permissions\\.php\\:42\\:\\:\\$guards type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/database/migrations/2024_12_09_162908_seed_permissions_delete_unused_product_administration_permissions.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Property class@anonymous/database/migrations/2024_12_09_162908_seed_permissions_delete_unused_product_administration_permissions\\.php\\:42\\:\\:\\$permissions type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/database/migrations/2024_12_09_162908_seed_permissions_delete_unused_product_administration_permissions.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Property class@anonymous/database/migrations/2025_01_21_111815_seed_permissions_delete_view_product_health_dashboard_permissions\\.php\\:40\\:\\:\\$guards type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/database/migrations/2025_01_21_111815_seed_permissions_delete_view_product_health_dashboard_permissions.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Property class@anonymous/database/migrations/2025_01_21_111815_seed_permissions_delete_view_product_health_dashboard_permissions\\.php\\:40\\:\\:\\$permissions type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/database/migrations/2025_01_21_111815_seed_permissions_delete_view_product_health_dashboard_permissions.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Property class@anonymous/database/migrations/2025_03_19_123414_seed_permission_delete_user_view_email_permission\\.php\\:40\\:\\:\\$guards type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/database/migrations/2025_03_19_123414_seed_permission_delete_user_view_email_permission.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Property class@anonymous/database/migrations/2025_03_19_123414_seed_permission_delete_user_view_email_permission\\.php\\:40\\:\\:\\$permissions type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/database/migrations/2025_03_19_123414_seed_permission_delete_user_view_email_permission.php',
];
$ignoreErrors[] = [
	// identifier: argument.templateType
	'message' => '#^Unable to resolve the template type TKey in call to function collect$#',
	'count' => 2,
	'path' => __DIR__ . '/database/seeders/LocalDevelopmentSeeder.php',
];
$ignoreErrors[] = [
	// identifier: argument.templateType
	'message' => '#^Unable to resolve the template type TValue in call to function collect$#',
	'count' => 2,
	'path' => __DIR__ . '/database/seeders/LocalDevelopmentSeeder.php',
];
$ignoreErrors[] = [
	// identifier: method.notFound
	'message' => '#^Call to an undefined method Pest\\\\PendingCalls\\\\TestCall\\|Pest\\\\Support\\\\HigherOrderTapProxy\\:\\:actingAs\\(\\)\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/tests/Helpers.php',
];
$ignoreErrors[] = [
	// identifier: method.protected
	'message' => '#^Call to protected method assignRole\\(\\) of class App\\\\Models\\\\Authenticatable\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/tests/Helpers.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Function Tests\\\\Helpers\\\\testResourceRequiresPermissionForAccess\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/tests/Helpers/AccessControl.php',
];
$ignoreErrors[] = [
	// identifier: missingType.parameter
	'message' => '#^Function Tests\\\\Helpers\\\\testResourceRequiresPermissionForAccess\\(\\) has parameter \\$feature with no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/tests/Helpers/AccessControl.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Function Tests\\\\Helpers\\\\testResourceRequiresPermissionForAccess\\(\\) has parameter \\$permissions with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/tests/Helpers/AccessControl.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Function Tests\\\\Helpers\\\\Events\\\\testEventIsBeingListenedTo\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/tests/Helpers/Events/EventListener.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Function user\\(\\) has parameter \\$licenses with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/tests/Pest.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Function user\\(\\) has parameter \\$permissions with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/tests/Pest.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Function user\\(\\) has parameter \\$roles with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/tests/Pest.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method Tests\\\\TestCase\\:\\:beginDatabaseTransactionOnConnection\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/tests/TestCase.php',
];
$ignoreErrors[] = [
	// identifier: missingType.iterableValue
	'message' => '#^Method Tests\\\\TestCase\\:\\:calculateMigrationChecksum\\(\\) has parameter \\$paths with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/tests/TestCase.php',
];
$ignoreErrors[] = [
	// identifier: missingType.return
	'message' => '#^Method Tests\\\\TestCase\\:\\:refreshTestDatabase\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/tests/TestCase.php',
];
$ignoreErrors[] = [
	// identifier: method.notFound
	'message' => '#^Call to an undefined method Pest\\\\PendingCalls\\\\TestCall\\:\\:expect\\(\\)\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/tests/Unit/ArchTest.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
