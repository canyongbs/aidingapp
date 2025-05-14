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
	'message' => '#^Class AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany extends generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany but does not specify its types\\: TRelatedModel, TDeclaringModel, TPivotModel, TAccessor \\(2\\-4 required\\)$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
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
	'message' => '#^Class AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany extends generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany but does not specify its types\\: TRelatedModel, TDeclaringModel, TPivotModel, TAccessor \\(2\\-4 required\\)$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/audit/src/Overrides/MorphToMany.php',
];
$ignoreErrors[] = [
	'message' => '#^Property AidingApp\\\\Audit\\\\Settings\\\\AuditSettings\\:\\:\\$audited_models_exclude \\(array\\<string, string\\>\\) does not accept array\\<int, string\\>\\.$#',
	'identifier' => 'assign.propertyType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/audit/tests/AuditListenerTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method Pest\\\\PendingCalls\\\\TestCall\\|Pest\\\\Support\\\\HigherOrderTapProxy\\:\\:expect\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/audit/tests/AuditTraitUsageTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method App\\\\Models\\\\User\\:\\:getMail\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/authorization/src/Enums/SocialiteProvider.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method App\\\\Models\\\\User\\:\\:getPrincipalName\\(\\)\\.$#',
	'identifier' => 'method.notFound',
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
	'message' => '#^Property AidingApp\\\\Authorization\\\\Filament\\\\Pages\\\\Auth\\\\SetPassword\\:\\:\\$data type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/authorization/src/Filament/Pages/Auth/SetPassword.php',
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
	'message' => '#^Method AidingApp\\\\Authorization\\\\Models\\\\Role\\:\\:users\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany does not specify its types\\: TRelatedModel, TDeclaringModel, TPivotModel, TAccessor \\(2\\-4 required\\)$#',
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
	'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\Collection\\<\\(int\\|string\\),Illuminate\\\\Database\\\\Eloquent\\\\Model\\>\\:\\:each\\(\\) expects callable\\(Illuminate\\\\Database\\\\Eloquent\\\\Model, int\\|string\\)\\: mixed, Closure\\(AidingApp\\\\Contact\\\\Models\\\\Contact\\)\\: bool given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Filament/Resources/ContactResource/Pages/ListContacts.php',
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
	'message' => '#^Call to an undefined method Staudenmeir\\\\EloquentHasManyDeep\\\\HasManyDeep\\:\\:status\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Filament/Widgets/ContactStats.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Contact\\\\Models\\\\Contact\\:\\:subscriptions\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/src/Models/Contact.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$entity of class AidingApp\\\\Timeline\\\\Events\\\\TimelineableRecordCreated constructor expects Illuminate\\\\Database\\\\Eloquent\\\\Model, App\\\\Models\\\\Contracts\\\\Educatable given\\.$#',
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
	'message' => '#^Call to function PHPUnit\\\\Framework\\\\assertEmpty\\(\\) with Illuminate\\\\Database\\\\Eloquent\\\\Collection\\<int, AidingApp\\\\Contact\\\\Models\\\\ContactStatus\\> will always evaluate to false\\.$#',
	'identifier' => 'function.impossibleType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contact/tests/ContactStatus/CreateContactStatusTest.php',
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
	'message' => '#^Property AidingApp\\\\ContractManagement\\\\Models\\\\Contract\\:\\:\\$append has no type specified\\.$#',
	'identifier' => 'missingType.property',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/contract-management/src/Models/Contract.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Division\\\\Database\\\\Factories\\\\DivisionFactory\\:\\:default\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Factories\\\\Factory does not specify its types\\: TModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/division/database/factories/DivisionFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method App\\\\Models\\\\Contracts\\\\Educatable\\:\\:notify\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Actions/CreateEngagement.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$model of method Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphTo\\<Illuminate\\\\Database\\\\Eloquent\\\\Model,AidingApp\\\\Engagement\\\\Models\\\\Engagement\\>\\:\\:associate\\(\\) expects Illuminate\\\\Database\\\\Eloquent\\\\Model\\|null, AidingApp\\\\Notification\\\\Models\\\\Contracts\\\\CanBeNotified\\|Illuminate\\\\Database\\\\Eloquent\\\\Collection given\\.$#',
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
	'message' => '#^Call to an undefined method Filament\\\\Forms\\\\Components\\\\Field\\:\\:getTemporaryImages\\(\\)\\.$#',
	'identifier' => 'method.notFound',
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
	'message' => '#^Call to an undefined method App\\\\Models\\\\Contracts\\\\Educatable\\:\\:notifyNow\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Jobs/CreateBatchedEngagement.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$model of method Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphTo\\<Illuminate\\\\Database\\\\Eloquent\\\\Model,AidingApp\\\\Engagement\\\\Models\\\\Engagement\\>\\:\\:associate\\(\\) expects Illuminate\\\\Database\\\\Eloquent\\\\Model\\|null, AidingApp\\\\Notification\\\\Models\\\\Contracts\\\\CanBeNotified given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Jobs/CreateBatchedEngagement.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method App\\\\Models\\\\Contracts\\\\Educatable\\:\\:notify\\(\\)\\.$#',
	'identifier' => 'method.notFound',
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
	'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:orderedEngagements\\(\\)\\.$#',
	'identifier' => 'method.notFound',
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
	'message' => '#^Call to an undefined method DOMNode\\:\\:getAttribute\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Models/EngagementBatch.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Engagement\\\\Models\\\\EngagementFile\\:\\:prunable\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder does not specify its types\\: TModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Models/EngagementFile.php',
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
	'message' => '#^Method AidingApp\\\\Engagement\\\\Models\\\\EngagementResponse\\:\\:sender\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Models/EngagementResponse.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Engagement\\\\Notifications\\\\EngagementBatchFinishedNotification\\:\\:toDatabase\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Notifications/EngagementBatchFinishedNotification.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Engagement\\\\Notifications\\\\EngagementBatchStartedNotification\\:\\:toDatabase\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/engagement/src/Notifications/EngagementBatchStartedNotification.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property App\\\\Models\\\\Contracts\\\\Educatable\\:\\:\\$display_name\\.$#',
	'identifier' => 'property.notFound',
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
	'message' => '#^Access to an undefined property App\\\\Models\\\\Contracts\\\\Educatable\\:\\:\\$display_name\\.$#',
	'identifier' => 'property.notFound',
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
	'message' => '#^Offset \'content\' on string on left side of \\?\\? does not exist\\.$#',
	'identifier' => 'nullCoalesce.offset',
	'count' => 2,
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
	'message' => '#^Class AidingApp\\\\Form\\\\Exports\\\\FormSubmissionExport implements generic interface Maatwebsite\\\\Excel\\\\Concerns\\\\WithMapping but does not specify its types\\: RowType$#',
	'identifier' => 'missingType.generics',
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
	'message' => '#^Method AidingApp\\\\Form\\\\Filament\\\\Blocks\\\\DateFormFieldBlock\\:\\:fields\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/DateFormFieldBlock.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Form\\\\Filament\\\\Blocks\\\\EmailFormFieldBlock\\:\\:fields\\(\\) return type has no value type specified in iterable type array\\.$#',
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
	'message' => '#^Method AidingApp\\\\Form\\\\Filament\\\\Blocks\\\\PhoneFormFieldBlock\\:\\:fields\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/PhoneFormFieldBlock.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'options\' does not exist on string\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/RadioFormFieldBlock.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'options\' does not exist on string\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/SelectFormFieldBlock.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'placeholder\' on string on left side of \\?\\? does not exist\\.$#',
	'identifier' => 'nullCoalesce.offset',
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
	'message' => '#^Method AidingApp\\\\Form\\\\Filament\\\\Blocks\\\\TextAreaFormFieldBlock\\:\\:fields\\(\\) return type has no value type specified in iterable type array\\.$#',
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
	'message' => '#^Method AidingApp\\\\Form\\\\Filament\\\\Blocks\\\\TimeFormFieldBlock\\:\\:fields\\(\\) return type has no value type specified in iterable type array\\.$#',
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
	'message' => '#^Offset \'accept\' on string on left side of \\?\\? does not exist\\.$#',
	'identifier' => 'nullCoalesce.offset',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/UploadFormFieldBlock.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'limit\' on string on left side of \\?\\? does not exist\\.$#',
	'identifier' => 'nullCoalesce.offset',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/UploadFormFieldBlock.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'multiple\' on string on left side of \\?\\? does not exist\\.$#',
	'identifier' => 'nullCoalesce.offset',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/UploadFormFieldBlock.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'size\' on string on left side of \\?\\? does not exist\\.$#',
	'identifier' => 'nullCoalesce.offset',
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
	'message' => '#^Method AidingApp\\\\Form\\\\Models\\\\Submissible\\:\\:fields\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
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
	'message' => '#^Method AidingApp\\\\Form\\\\Models\\\\SubmissibleStep\\:\\:fields\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
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
	'message' => '#^Method AidingApp\\\\Form\\\\Models\\\\Submission\\:\\:fields\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany does not specify its types\\: TRelatedModel, TDeclaringModel, TPivotModel, TAccessor \\(2\\-4 required\\)$#',
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
	'message' => '#^Method AidingApp\\\\InAppCommunication\\\\Events\\\\ConversationMessageSent\\:\\:__construct\\(\\) has parameter \\$messageContent with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/in-app-communication/src/Events/ConversationMessageSent.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Collection\\<int, \\*NEVER\\*\\>\\:\\:\\$participant\\.$#',
	'identifier' => 'property.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/in-app-communication/src/Filament/Pages/UserChat.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access property \\$notification_preference on null\\.$#',
	'identifier' => 'property.nonObject',
	'count' => 3,
	'path' => __DIR__ . '/app-modules/in-app-communication/src/Filament/Pages/UserChat.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access property \\$participant on null\\.$#',
	'identifier' => 'property.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/in-app-communication/src/Filament/Pages/UserChat.php',
];
$ignoreErrors[] = [
	'message' => '#^If condition is always true\\.$#',
	'identifier' => 'if.alwaysTrue',
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
	'message' => '#^Method AidingApp\\\\InAppCommunication\\\\Filament\\\\Pages\\\\UserChat\\:\\:onMessageSent\\(\\) has parameter \\$messageContent with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/in-app-communication/src/Filament/Pages/UserChat.php',
];
$ignoreErrors[] = [
	'message' => '#^Negated boolean expression is always false\\.$#',
	'identifier' => 'booleanNot.alwaysFalse',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/in-app-communication/src/Filament/Pages/UserChat.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @property for property AidingApp\\\\InAppCommunication\\\\Filament\\\\Pages\\\\UserChat\\:\\:\\$conversations contains generic class Illuminate\\\\Database\\\\Eloquent\\\\Collection but does not specify its types\\: TKey, TModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/in-app-communication/src/Filament/Pages/UserChat.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$relations of method Illuminate\\\\Database\\\\Eloquent\\\\Builder\\<AidingApp\\\\InAppCommunication\\\\Models\\\\TwilioConversation\\>\\:\\:with\\(\\) expects array\\<array\\|\\(Closure\\(Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\Relation\\<\\*, \\*, \\*\\>\\)\\: mixed\\)\\|string\\>\\|string, array\\{participants\\: Closure\\(Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\)\\: Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\} given\\.$#',
	'identifier' => 'argument.type',
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
	'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Collection\\<int, \\*NEVER\\*\\>\\:\\:\\$participant\\.$#',
	'identifier' => 'property.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/in-app-communication/src/Jobs/NotifyConversationParticipant.php',
];
$ignoreErrors[] = [
	'message' => '#^Using nullsafe property access on non\\-nullable type Illuminate\\\\Database\\\\Eloquent\\\\Collection\\<int, \\*NEVER\\*\\>\\. Use \\-\\> instead\\.$#',
	'identifier' => 'nullsafe.neverNull',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/in-app-communication/src/Jobs/NotifyConversationParticipant.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$callback of method Illuminate\\\\Support\\\\LazyCollection\\<int,\\*NEVER\\*\\>\\:\\:each\\(\\) contains unresolvable type\\.$#',
	'identifier' => 'argument.unresolvableType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/in-app-communication/src/Jobs/NotifyConversationParticipants.php',
];
$ignoreErrors[] = [
	'message' => '#^Return type of call to method Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<App\\\\Models\\\\User,AidingApp\\\\InAppCommunication\\\\Models\\\\TwilioConversation,covariant AidingApp\\\\InAppCommunication\\\\Models\\\\TwilioConversationUser,string\\>\\:\\:lazyById\\(\\) contains unresolvable type\\.$#',
	'identifier' => 'method.unresolvableReturnType',
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
	'message' => '#^Property AidingApp\\\\InAppCommunication\\\\Models\\\\IdeHelperTwilioConversationUser\\:\\:\\$last_read_at \\(Carbon\\\\CarbonImmutable\\|null\\) does not accept Illuminate\\\\Support\\\\Carbon\\.$#',
	'identifier' => 'assign.propertyType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/in-app-communication/src/Models/TwilioConversationUser.php',
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
	'message' => '#^Method AidingApp\\\\IntegrationAwsSesEventHandling\\\\Http\\\\Controllers\\\\AwsSesInboundWebhookController\\:\\:__invoke\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/integration-aws-ses-event-handling/src/Http/Controllers/AwsSesInboundWebhookController.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to property \\$mail on an unknown class TenantConfig\\.$#',
	'identifier' => 'class.notFound',
	'count' => 8,
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
	'message' => '#^Call to an undefined method Faker\\\\Generator\\:\\:catchPhrase\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/database/factories/AssetFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Match expression does not handle remaining value\\: mixed$#',
	'identifier' => 'match.unhandled',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Filament/Actions/CheckOutAssetHeaderAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Builder\\:\\:withoutReturned\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Filament/Resources/AssetCheckOutResource/Pages/ListAssetCheckOuts.php',
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
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Models\\\\Asset\\:\\:purchaseAge\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Casts\\\\Attribute does not specify its types\\: TGet, TSet$#',
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
	'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:checkOuts\\(\\)\\.$#',
	'identifier' => 'method.notFound',
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
	'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:maintenanceActivities\\(\\)\\.$#',
	'identifier' => 'method.notFound',
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
	'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Models\\\\Scopes\\\\ClassifiedAs\\:\\:__invoke\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/inventory-management/src/Models/Scopes/ClassifiedAs.php',
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
	'message' => '#^Method AidingApp\\\\KnowledgeBase\\\\Models\\\\KnowledgeBaseQuality\\:\\:knowledgeBaseItems\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
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
	'message' => '#^Access to an undefined property Pest\\\\Expectation\\<AidingApp\\\\KnowledgeBase\\\\Models\\\\KnowledgeBaseCategory\\|null\\>\\:\\:\\$subCategories\\.$#',
	'identifier' => 'property.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/knowledge-base/tests/KnowledgeBaseCategory/EditKnowledgeBaseCategoryTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Using nullsafe property access "\\?\\-\\>id" on left side of \\?\\? is unnecessary\\. Use \\-\\> instead\\.$#',
	'identifier' => 'nullsafe.neverNull',
	'count' => 4,
	'path' => __DIR__ . '/app-modules/knowledge-base/tests/KnowledgeBaseItem/RequestFactories/CreateKnowledgeBaseItemRequestFactory.php',
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
	'message' => '#^Property AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestForm\\:\\:\\$is_wizard \\(never\\) does not accept true\\.$#',
	'identifier' => 'assign.propertyType',
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
	'message' => '#^Using nullsafe property access "\\?\\-\\>description" on left side of \\?\\? is unnecessary\\. Use \\-\\> instead\\.$#',
	'identifier' => 'nullsafe.neverNull',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/portal/src/Http/Controllers/KnowledgeManagementPortal/GetServiceRequestsController.php',
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
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Filament\\\\Resources\\\\ChangeRequestTypeResource\\:\\:getEloquentQuery\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder does not specify its types\\: TModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ChangeRequestTypeResource.php',
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
	'message' => '#^Parameter \\#1 \\$length of method Filament\\\\Forms\\\\Components\\\\TextInput\\:\\:maxLength\\(\\) expects Closure\\|int\\|null, \'255\' given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/IncidentStatusResource/Pages/CreateIncidentStatus.php',
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
	'message' => '#^Property AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestForm\\:\\:\\$content \\(never\\) does not accept array\\.$#',
	'identifier' => 'assign.propertyType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestFormResource/Pages/CreateServiceRequestForm.php',
];
$ignoreErrors[] = [
	'message' => '#^Property AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestForm\\:\\:\\$content \\(never\\) does not accept null\\.$#',
	'identifier' => 'assign.propertyType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestFormResource/Pages/CreateServiceRequestForm.php',
];
$ignoreErrors[] = [
	'message' => '#^Property AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestFormField\\:\\:\\$config \\(never\\) does not accept mixed\\.$#',
	'identifier' => 'assign.propertyType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestFormResource/Pages/CreateServiceRequestForm.php',
];
$ignoreErrors[] = [
	'message' => '#^Property AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestFormField\\:\\:\\$is_required \\(never\\) does not accept mixed\\.$#',
	'identifier' => 'assign.propertyType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestFormResource/Pages/CreateServiceRequestForm.php',
];
$ignoreErrors[] = [
	'message' => '#^Property AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestFormField\\:\\:\\$label \\(never\\) does not accept mixed\\.$#',
	'identifier' => 'assign.propertyType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestFormResource/Pages/CreateServiceRequestForm.php',
];
$ignoreErrors[] = [
	'message' => '#^Property AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestFormField\\:\\:\\$type \\(never\\) does not accept mixed\\.$#',
	'identifier' => 'assign.propertyType',
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
	'message' => '#^Parameter \\#1 \\$serviceRequestForm of method AidingApp\\\\ServiceManagement\\\\Filament\\\\Resources\\\\ServiceRequestFormResource\\\\Pages\\\\EditServiceRequestForm\\:\\:saveFieldsFromComponents\\(\\) expects AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestForm, AidingApp\\\\Form\\\\Models\\\\Submissible given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestFormResource/Pages/EditServiceRequestForm.php',
];
$ignoreErrors[] = [
	'message' => '#^Property AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestForm\\:\\:\\$content \\(never\\) does not accept array\\.$#',
	'identifier' => 'assign.propertyType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestFormResource/Pages/EditServiceRequestForm.php',
];
$ignoreErrors[] = [
	'message' => '#^Property AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestForm\\:\\:\\$content \\(never\\) does not accept null\\.$#',
	'identifier' => 'assign.propertyType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestFormResource/Pages/EditServiceRequestForm.php',
];
$ignoreErrors[] = [
	'message' => '#^Property AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestFormField\\:\\:\\$config \\(never\\) does not accept mixed\\.$#',
	'identifier' => 'assign.propertyType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestFormResource/Pages/EditServiceRequestForm.php',
];
$ignoreErrors[] = [
	'message' => '#^Property AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestFormField\\:\\:\\$is_required \\(never\\) does not accept mixed\\.$#',
	'identifier' => 'assign.propertyType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestFormResource/Pages/EditServiceRequestForm.php',
];
$ignoreErrors[] = [
	'message' => '#^Property AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestFormField\\:\\:\\$label \\(never\\) does not accept mixed\\.$#',
	'identifier' => 'assign.propertyType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestFormResource/Pages/EditServiceRequestForm.php',
];
$ignoreErrors[] = [
	'message' => '#^Property AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestFormField\\:\\:\\$type \\(never\\) does not accept mixed\\.$#',
	'identifier' => 'assign.propertyType',
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
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ChangeRequestType\\:\\:changeRequests\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ChangeRequestType.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @return contains generic type AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\<App\\\\Models\\\\User, \\$this\\(AidingApp\\\\ServiceManagement\\\\Models\\\\ChangeRequestType\\)\\> but class AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany is not generic\\.$#',
	'identifier' => 'generics.notGeneric',
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
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\IncidentUpdate\\:\\:incident\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
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
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestFormSubmission\\:\\:fields\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany does not specify its types\\: TRelatedModel, TDeclaringModel, TPivotModel, TAccessor \\(2\\-4 required\\)$#',
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
	'message' => '#^PHPDoc tag @return contains generic type AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\<AidingApp\\\\Team\\\\Models\\\\Team, \\$this\\(AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestType\\), covariant AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestTypeAuditor\\> but class AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany is not generic\\.$#',
	'identifier' => 'generics.notGeneric',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestType.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @return contains generic type AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany\\<AidingApp\\\\Team\\\\Models\\\\Team, \\$this\\(AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestType\\), covariant AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestTypeManager\\> but class AidingApp\\\\Audit\\\\Overrides\\\\BelongsToMany is not generic\\.$#',
	'identifier' => 'generics.notGeneric',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestType.php',
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
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestUpdate\\:\\:serviceRequest\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestUpdate.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\Sla\\:\\:serviceRequestPriorities\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/src/Models/Sla.php',
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
	'message' => '#^Call to private method whereRelation\\(\\) of parent class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<Illuminate\\\\Database\\\\Eloquent\\\\Model,Illuminate\\\\Database\\\\Eloquent\\\\Model,Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\Pivot,string\\>\\.$#',
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
	'message' => '#^Call to an undefined method Pest\\\\Expectation\\<Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<AidingApp\\\\Team\\\\Models\\\\Team, AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceMonitoringTarget, covariant AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceMonitoringTargetTeam, string\\>\\|null\\>\\:\\:exists\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/service-management/tests/Filament/ServiceMonitoring/Pages/CreateServiceMonitoringTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method Pest\\\\Expectation\\<Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<App\\\\Models\\\\User, AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceMonitoringTarget, covariant AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceMonitoringTargetUser, string\\>\\|null\\>\\:\\:exists\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 2,
	'path' => __DIR__ . '/app-modules/service-management/tests/Filament/ServiceMonitoring/Pages/CreateServiceMonitoringTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to function PHPUnit\\\\Framework\\\\assertEmpty\\(\\) with Illuminate\\\\Database\\\\Eloquent\\\\Collection\\<int, AidingApp\\\\ServiceManagement\\\\Models\\\\IncidentUpdate\\> will always evaluate to false\\.$#',
	'identifier' => 'function.impossibleType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/tests/IncidentUpdate/CreateIncidentUpdateTest.php',
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
	'message' => '#^Access to an undefined property Pest\\\\Expectation\\<AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestAssignment\\|null\\>\\:\\:\\$user\\.$#',
	'identifier' => 'property.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/tests/ServiceRequest/CreateServiceRequestTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method Mockery\\\\ExpectationInterface\\|Mockery\\\\HigherOrderMessage\\:\\:twice\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/tests/ServiceRequest/ServiceRequestNumberTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to function PHPUnit\\\\Framework\\\\assertEmpty\\(\\) with Illuminate\\\\Database\\\\Eloquent\\\\Collection\\<int, AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestType\\> will always evaluate to false\\.$#',
	'identifier' => 'function.impossibleType',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/tests/ServiceRequestType/CreateServiceRequestTypeTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property Pest\\\\Expectation\\<AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestType\\|null\\>\\:\\:\\$auditors\\.$#',
	'identifier' => 'property.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/service-management/tests/ServiceRequestType/ServiceRequestTypeAuditorsTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Access to an undefined property Pest\\\\Expectation\\<AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestType\\|null\\>\\:\\:\\$managers\\.$#',
	'identifier' => 'property.notFound',
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
	'message' => '#^Access to an undefined property AidingApp\\\\Contact\\\\Models\\\\Contact\\:\\:\\$full\\.$#',
	'identifier' => 'property.notFound',
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
	'message' => '#^Instanceof between AidingApp\\\\Contact\\\\Models\\\\Contact and AidingApp\\\\Notification\\\\Models\\\\Contracts\\\\Subscribable will always evaluate to true\\.$#',
	'identifier' => 'instanceof.alwaysTrue',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/task/src/Models/Task.php',
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
	'message' => '#^Method AidingApp\\\\Team\\\\Models\\\\Team\\:\\:division\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/team/src/Models/Team.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AidingApp\\\\Team\\\\Providers\\\\TeamServiceProvider\\:\\:boot\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/app-modules/team/src/Providers/TeamServiceProvider.php',
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
	'message' => '#^Access to an undefined property AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequest\\:\\:\\$service_request_id\\.$#',
	'identifier' => 'property.notFound',
	'count' => 2,
	'path' => __DIR__ . '/app/Filament/Widgets/MyServiceRequests.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Builder\\|Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\Relation\\:\\:unread\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/app/Filament/Widgets/Notifications.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Filament\\\\Widgets\\\\Notifications\\:\\:getNotifications\\(\\) return type with generic interface Illuminate\\\\Contracts\\\\Pagination\\\\Paginator does not specify its types\\: TKey, TValue$#',
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
	'message' => '#^Method App\\\\Livewire\\\\TaskKanban\\:\\:getTasks\\(\\) return type with generic class Illuminate\\\\Support\\\\Collection does not specify its types\\: TKey, TValue$#',
	'identifier' => 'missingType.generics',
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
	'message' => '#^Method App\\\\Models\\\\Authenticatable\\:\\:roles\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany does not specify its types\\: TRelatedModel, TDeclaringModel, TPivotModel, TAccessor \\(2\\-4 required\\)$#',
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
	'message' => '#^Method App\\\\Models\\\\Contracts\\\\HasTags\\:\\:tags\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany does not specify its types\\: TRelatedModel, TDeclaringModel, TPivotModel, TAccessor \\(2\\-4 required\\)$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/Contracts/HasTags.php',
];
$ignoreErrors[] = [
	'message' => '#^Method App\\\\Models\\\\NotificationSettingPivot\\:\\:relatedTo\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphTo does not specify its types\\: TRelatedModel, TDeclaringModel$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/app/Models/NotificationSettingPivot.php',
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
	'message' => '#^Method App\\\\Models\\\\User\\:\\:assignTeam\\(\\) has parameter \\$teamId with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
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
	'message' => '#^Method App\\\\Models\\\\User\\:\\:permissionsFromRoles\\(\\) return type with generic class Staudenmeir\\\\EloquentHasManyDeep\\\\HasManyDeep does not specify its types\\: TRelatedModel, TDeclaringModel$#',
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
	'message' => '#^Property App\\\\Multitenancy\\\\Exceptions\\\\UnableToResolveTenantForEncryptionKey\\:\\:\\$message has no type specified\\.$#',
	'identifier' => 'missingType.property',
	'count' => 1,
	'path' => __DIR__ . '/app/Multitenancy/Exceptions/UnableToResolveTenantForEncryptionKey.php',
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
	'message' => '#^Trait Database\\\\Migrations\\\\Concerns\\\\CanModifySettings is used zero times and is not analysed\\.$#',
	'identifier' => 'trait.unused',
	'count' => 1,
	'path' => __DIR__ . '/database/migrations/Concerns/CanModifySettings.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method Pest\\\\PendingCalls\\\\TestCall\\|Pest\\\\Support\\\\HigherOrderTapProxy\\:\\:actingAs\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/tests/Helpers.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
