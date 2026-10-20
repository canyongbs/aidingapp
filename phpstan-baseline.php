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
*/ declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\Ai\\\\Actions\\\\CompletePrompt" defines disallowed public method "execute"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/ai/src/Actions/CompletePrompt.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\Ai\\\\Actions\\\\CompletePrompt" must define a public "__invoke" method\\. Action classes should be accessed via invocation and keep other behavior in private/protected helpers\\.$#',
    'identifier' => 'Common.actionClassMustBeInvokable',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/ai/src/Actions/CompletePrompt.php',
];
$ignoreErrors[] = [
    'message' => '#^Avoid direct request IP accessors \\(ip\\(\\), ips\\(\\), getClientIp\\(\\), getClientIps\\(\\)\\)\\. Use CanyonGBS\\\\Common\\\\Support\\\\ClientIp\\:\\:resolve\\(\\) instead so client IP resolution remains Cloudflare and ALB aware\\.$#',
    'identifier' => 'Common.useClientIpResolver',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/ai/src/Actions/CompletePrompt.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\Ai\\\\Actions\\\\GenerateAssistantEmbedCode" defines disallowed public method "handle"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/ai/src/Actions/GenerateAssistantEmbedCode.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\Ai\\\\Actions\\\\GenerateAssistantEmbedCode" must define a public "__invoke" method\\. Action classes should be accessed via invocation and keep other behavior in private/protected helpers\\.$#',
    'identifier' => 'Common.actionClassMustBeInvokable',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/ai/src/Actions/GenerateAssistantEmbedCode.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\Ai\\\\Actions\\\\GenerateServiceRequestQuestionAiPrompt" defines disallowed public method "execute"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/ai/src/Actions/GenerateServiceRequestQuestionAiPrompt.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\Ai\\\\Actions\\\\GenerateServiceRequestQuestionAiPrompt" must define a public "__invoke" method\\. Action classes should be accessed via invocation and keep other behavior in private/protected helpers\\.$#',
    'identifier' => 'Common.actionClassMustBeInvokable',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/ai/src/Actions/GenerateServiceRequestQuestionAiPrompt.php',
];
$ignoreErrors[] = [
    'message' => '#^Avoid direct request IP accessors \\(ip\\(\\), ips\\(\\), getClientIp\\(\\), getClientIps\\(\\)\\)\\. Use CanyonGBS\\\\Common\\\\Support\\\\ClientIp\\:\\:resolve\\(\\) instead so client IP resolution remains Cloudflare and ALB aware\\.$#',
    'identifier' => 'Common.useClientIpResolver',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/ai/src/Http/Controllers/AssistantWidget/RetryMessageController.php',
];
$ignoreErrors[] = [
    'message' => '#^Avoid direct request IP accessors \\(ip\\(\\), ips\\(\\), getClientIp\\(\\), getClientIps\\(\\)\\)\\. Use CanyonGBS\\\\Common\\\\Support\\\\ClientIp\\:\\:resolve\\(\\) instead so client IP resolution remains Cloudflare and ALB aware\\.$#',
    'identifier' => 'Common.useClientIpResolver',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/ai/src/Http/Controllers/AssistantWidget/SendMessageController.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Pest\\\\Mixins\\\\Expectation\\<string\\|null\\>\\:\\:\\$not\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/ai/tests/Tenant/Actions/GenerateServiceRequestQuestionAiPromptTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Function assistantAuthenticate\\(\\) has parameter \\$body with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/ai/tests/Tenant/Http/Controllers/AssistantWidget/AuthenticateControllerTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Function assistantAuthenticate\\(\\) return type with generic class Illuminate\\\\Testing\\\\TestResponse does not specify its types\\: TResponse$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/ai/tests/Tenant/Http/Controllers/AssistantWidget/AuthenticateControllerTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$value of static method Illuminate\\\\Support\\\\Facades\\\\Hash\\:\\:make\\(\\) expects string, int given\\.$#',
    'identifier' => 'argument.type',
    'count' => 4,
    'path' => __DIR__ . '/app-modules/ai/tests/Tenant/Http/Controllers/AssistantWidget/AuthenticateControllerTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Function widgetFormRequestAsContact\\(\\) return type with generic class Illuminate\\\\Testing\\\\TestResponse does not specify its types\\: TResponse$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/ai/tests/Tenant/Http/Controllers/AssistantWidget/GetServiceRequestFormControllerTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Property AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestFormStep\\:\\:\\$content \\(never\\) does not accept array\\<string, list\\<array\\<string, array\\<string, array\\<string, mixed\\>\\|string\\>\\|string\\>\\>\\|string\\>\\.$#',
    'identifier' => 'assign.propertyType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/ai/tests/Tenant/Http/Controllers/AssistantWidget/GetServiceRequestFormControllerTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Function authenticatedWidgetRequest\\(\\) return type with generic class Illuminate\\\\Testing\\\\TestResponse does not specify its types\\: TResponse$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/ai/tests/Tenant/Http/Controllers/AssistantWidget/GetServiceRequestTypesControllerTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Function widgetRequestAsContact\\(\\) return type with generic class Illuminate\\\\Testing\\\\TestResponse does not specify its types\\: TResponse$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/ai/tests/Tenant/Http/Controllers/AssistantWidget/GetServiceRequestTypesControllerTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Unable to resolve the template type TKey in call to function collect$#',
    'identifier' => 'argument.templateType',
    'count' => 14,
    'path' => __DIR__ . '/app-modules/ai/tests/Tenant/Http/Controllers/AssistantWidget/GetServiceRequestTypesControllerTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Unable to resolve the template type TValue in call to function collect$#',
    'identifier' => 'argument.templateType',
    'count' => 14,
    'path' => __DIR__ . '/app-modules/ai/tests/Tenant/Http/Controllers/AssistantWidget/GetServiceRequestTypesControllerTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Variable name "\\$categoryAData" is not in camelCase\\.$#',
    'identifier' => 'MeliorStan.variableNameNotCamelCase',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/ai/tests/Tenant/Http/Controllers/AssistantWidget/GetServiceRequestTypesControllerTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Variable name "\\$categoryBData" is not in camelCase\\.$#',
    'identifier' => 'MeliorStan.variableNameNotCamelCase',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/ai/tests/Tenant/Http/Controllers/AssistantWidget/GetServiceRequestTypesControllerTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Pest\\\\PendingCalls\\\\TestCall\\:\\:\\$manifestOriginalContents\\.$#',
    'identifier' => 'property.notFound',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/ai/tests/Tenant/Http/Controllers/AssistantWidget/GetWidgetConfigControllerTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Pest\\\\PendingCalls\\\\TestCall\\:\\:\\$manifestPath\\.$#',
    'identifier' => 'property.notFound',
    'count' => 3,
    'path' => __DIR__ . '/app-modules/ai/tests/Tenant/Http/Controllers/AssistantWidget/GetWidgetConfigControllerTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Function getWidgetConfig\\(\\) return type with generic class Illuminate\\\\Testing\\\\TestResponse does not specify its types\\: TResponse$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/ai/tests/Tenant/Http/Controllers/AssistantWidget/GetWidgetConfigControllerTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Function requestAssistantAuthentication\\(\\) has parameter \\$body with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/ai/tests/Tenant/Http/Controllers/AssistantWidget/RequestAuthenticationControllerTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Function requestAssistantAuthentication\\(\\) return type with generic class Illuminate\\\\Testing\\\\TestResponse does not specify its types\\: TResponse$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/ai/tests/Tenant/Http/Controllers/AssistantWidget/RequestAuthenticationControllerTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\Pivot\\:\\:\\$response\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/ai/tests/Tenant/Http/Controllers/AssistantWidget/StoreServiceRequestControllerTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Function postAssistantServiceRequest\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/ai/tests/Tenant/Http/Controllers/AssistantWidget/StoreServiceRequestControllerTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Function postAssistantServiceRequest\\(\\) has parameter \\$data with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/ai/tests/Tenant/Http/Controllers/AssistantWidget/StoreServiceRequestControllerTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Unable to resolve the template type TAndValue in call to method Pest\\\\Expectation\\<bool\\|null\\>\\:\\:and\\(\\)$#',
    'identifier' => 'argument.templateType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/ai/tests/Tenant/Http/Controllers/AssistantWidget/StoreServiceRequestControllerTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Pest\\\\PendingCalls\\\\TestCall\\:\\:postJson\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/ai/tests/Tenant/Http/Controllers/AssistantWidget/StoreServiceRequestSecretControllerTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\Audit\\\\Actions\\\\Finders\\\\AuditableModels" defines disallowed public method "all"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/audit/src/Actions/Finders/AuditableModels.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\Audit\\\\Actions\\\\Finders\\\\AuditableModels" must define a public "__invoke" method\\. Action classes should be accessed via invocation and keep other behavior in private/protected helpers\\.$#',
    'identifier' => 'Common.actionClassMustBeInvokable',
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
    'message' => '#^Class AidingApp\\\\Audit\\\\Overrides\\\\MorphToMany extends generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\MorphToMany but does not specify its types\\: TRelatedModel, TDeclaringModel, TPivotModel, TAccessor \\(2\\-4 required\\)$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/audit/src/Overrides/MorphToMany.php',
];
$ignoreErrors[] = [
    'message' => '#^Property AidingApp\\\\Audit\\\\Settings\\\\AuditSettings\\:\\:\\$audited_models_exclude \\(array\\<string, string\\>\\) does not accept array\\<int, string\\>\\.$#',
    'identifier' => 'assign.propertyType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/audit/tests/Tenant/AuditListenerTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Pest\\\\PendingCalls\\\\TestCall\\:\\:expect\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/audit/tests/Tenant/AuditTraitUsageTest.php',
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
    'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Builder\\:\\:api\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/authorization/src/Filament/Resources/Roles/Pages/ListRoles.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Builder\\:\\:web\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/authorization/src/Filament/Resources/Roles/Pages/ListRoles.php',
];
$ignoreErrors[] = [
    'message' => '#^Avoid strtolower\\(\\) as it is not multibyte\\-safe\\. Use Laravel\'s Str\\:\\:lower\\(\\) instead, or mb_strtolower\\(\\) if a framework\\-free alternative is required\\. If you are certain you specifically need strtolower\\(\\), add an inline ignore for this rule \\(// @phpstan\\-ignore Common\\.noStrtolower\\)\\.$#',
    'identifier' => 'Common.noStrtolower',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/authorization/src/Http/Controllers/SocialiteController.php',
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
    'message' => '#^Eloquent local scopes are not allowed\\. The "scopeApi" method on "AidingApp\\\\Authorization\\\\Models\\\\Permission" defines a local scope, which relies on magic methods that static analysis cannot reason about\\. Use a tappable scope instead\\. See https\\://seankegel\\.com/elevate\\-your\\-laravel\\-eloquent\\-queries\\-with\\-tappable\\-scopes\\.$#',
    'identifier' => 'Common.noLocalModelScope',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/authorization/src/Models/Permission.php',
];
$ignoreErrors[] = [
    'message' => '#^Eloquent local scopes are not allowed\\. The "scopeWeb" method on "AidingApp\\\\Authorization\\\\Models\\\\Permission" defines a local scope, which relies on magic methods that static analysis cannot reason about\\. Use a tappable scope instead\\. See https\\://seankegel\\.com/elevate\\-your\\-laravel\\-eloquent\\-queries\\-with\\-tappable\\-scopes\\.$#',
    'identifier' => 'Common.noLocalModelScope',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/authorization/src/Models/Permission.php',
];
$ignoreErrors[] = [
    'message' => '#^Eloquent local scopes are not allowed\\. The "scopeApi" method on "AidingApp\\\\Authorization\\\\Models\\\\Role" defines a local scope, which relies on magic methods that static analysis cannot reason about\\. Use a tappable scope instead\\. See https\\://seankegel\\.com/elevate\\-your\\-laravel\\-eloquent\\-queries\\-with\\-tappable\\-scopes\\.$#',
    'identifier' => 'Common.noLocalModelScope',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/authorization/src/Models/Role.php',
];
$ignoreErrors[] = [
    'message' => '#^Eloquent local scopes are not allowed\\. The "scopeSuperAdmin" method on "AidingApp\\\\Authorization\\\\Models\\\\Role" defines a local scope, which relies on magic methods that static analysis cannot reason about\\. Use a tappable scope instead\\. See https\\://seankegel\\.com/elevate\\-your\\-laravel\\-eloquent\\-queries\\-with\\-tappable\\-scopes\\.$#',
    'identifier' => 'Common.noLocalModelScope',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/authorization/src/Models/Role.php',
];
$ignoreErrors[] = [
    'message' => '#^Eloquent local scopes are not allowed\\. The "scopeWeb" method on "AidingApp\\\\Authorization\\\\Models\\\\Role" defines a local scope, which relies on magic methods that static analysis cannot reason about\\. Use a tappable scope instead\\. See https\\://seankegel\\.com/elevate\\-your\\-laravel\\-eloquent\\-queries\\-with\\-tappable\\-scopes\\.$#',
    'identifier' => 'Common.noLocalModelScope',
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
    'message' => '#^Parameter \\#1 \\$data of anonymous function has no typehint\\.$#',
    'identifier' => 'MeliorStan.closureParameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/authorization/tests/Tenant/Feature/Filament/Resources/UserResources/Pages/CreateUserTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$errors of anonymous function has no typehint\\.$#',
    'identifier' => 'MeliorStan.closureParameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/authorization/tests/Tenant/Feature/Filament/Resources/UserResources/Pages/CreateUserTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$data of anonymous function has no typehint\\.$#',
    'identifier' => 'MeliorStan.closureParameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/authorization/tests/Tenant/Feature/Filament/Resources/UserResources/Pages/EditUserTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$errors of anonymous function has no typehint\\.$#',
    'identifier' => 'MeliorStan.closureParameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/authorization/tests/Tenant/Feature/Filament/Resources/UserResources/Pages/EditUserTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to method getOptions\\(\\) on an unknown class Select\\.$#',
    'identifier' => 'class.notFound',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/authorization/tests/Tenant/Feature/Filament/Resources/UserResources/Pages/ListUsersTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Class Select not found\\.$#',
    'identifier' => 'class.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/authorization/tests/Tenant/Feature/Filament/Resources/UserResources/Pages/ListUsersTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Unable to resolve the template type TValue in call to function expect$#',
    'identifier' => 'argument.templateType',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/authorization/tests/Tenant/Feature/Filament/Resources/UserResources/Pages/ListUsersTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Avoid assertSee\\(\\) and its alternatives\\: they only prove the text appears somewhere in the rendered output, not that it appears where the test actually expects it, so they can pass even when the feature under test is broken\\. Prefer a more precise assertion \\(e\\.g\\. asserting component/view state, a scoped selector, or the specific structure produced\\)\\. If you are certain you specifically need this assertion, add an inline ignore for this rule \\(// @phpstan\\-ignore Common\\.noAssertSee\\)\\.$#',
    'identifier' => 'Common.noAssertSee',
    'count' => 3,
    'path' => __DIR__ . '/app-modules/authorization/tests/Tenant/Feature/Filament/Resources/UserResources/Pages/ViewUserTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Property App\\\\Models\\\\IdeHelperUser\\:\\:\\$created_at \\(Illuminate\\\\Support\\\\Carbon\\|null\\) does not accept string\\.$#',
    'identifier' => 'assign.propertyType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/authorization/tests/Tenant/Feature/Filament/Resources/UserResources/Pages/ViewUserTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Pest\\\\PendingCalls\\\\TestCall\\:\\:fail\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/authorization/tests/Tenant/Unit/NormalizedPermissionsTest.php',
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
    'count' => 1,
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
    'message' => '#^Parameter \\#1 \\$entity of class AidingApp\\\\Timeline\\\\Events\\\\TimelineableRecordCreated constructor expects Illuminate\\\\Database\\\\Eloquent\\\\Model, App\\\\Models\\\\Contracts\\\\Educatable given\\.$#',
    'identifier' => 'argument.type',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/contact/tests/Tenant/Contact/ListContactTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$response of anonymous function has no typehint\\.$#',
    'identifier' => 'MeliorStan.closureParameterMissingTypehint',
    'count' => 4,
    'path' => __DIR__ . '/app-modules/contact/tests/Tenant/Contact/ListContactTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$entity of class AidingApp\\\\Timeline\\\\Events\\\\TimelineableRecordCreated constructor expects Illuminate\\\\Database\\\\Eloquent\\\\Model, App\\\\Models\\\\Contracts\\\\Educatable given\\.$#',
    'identifier' => 'argument.type',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/contact/tests/Tenant/Contact/RelationManagers/EngagementsRelationManagerTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Class ContactServiceManagement not found\\.$#',
    'identifier' => 'class.notFound',
    'count' => 3,
    'path' => __DIR__ . '/app-modules/contact/tests/Tenant/Contact/RelationManagers/ServiceRequestsRelationManagerTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Property AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestForm\\:\\:\\$content \\(never\\) does not accept array\\<string, list\\<array\\<string, array\\<string, array\\<string, mixed\\>\\|string\\>\\|string\\>\\>\\|string\\>\\.$#',
    'identifier' => 'assign.propertyType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/contact/tests/Tenant/Contact/RelationManagers/ServiceRequestsRelationManagerTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Faker\\\\Generator\\:\\:secondaryAddress\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/contact/tests/Tenant/Contact/RequestFactories/CreateContactRequestFactory.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Faker\\\\Generator\\:\\:stateAbbr\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/contact/tests/Tenant/Contact/RequestFactories/CreateContactRequestFactory.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Faker\\\\Generator\\:\\:secondaryAddress\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/contact/tests/Tenant/Contact/RequestFactories/EditContactRequestFactory.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Faker\\\\Generator\\:\\:stateAbbr\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/contact/tests/Tenant/Contact/RequestFactories/EditContactRequestFactory.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Pest\\\\Mixins\\\\Expectation\\<array\\<int, string\\>\\|string\\>\\|Pest\\\\Mixins\\\\Expectation\\<Pest\\\\Expectation\\<array\\<int, string\\>\\|string\\>\\>\\:\\:\\$address_2\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/contact/tests/Tenant/Contact/ViewContactTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Pest\\\\Mixins\\\\Expectation\\<array\\<int, string\\>\\|string\\>\\|Pest\\\\Mixins\\\\Expectation\\<Pest\\\\Expectation\\<array\\<int, string\\>\\|string\\>\\>\\:\\:\\$employee_id\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/contact/tests/Tenant/Contact/ViewContactTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Pest\\\\Mixins\\\\Expectation\\<array\\<int, string\\>\\|string\\>\\|Pest\\\\Mixins\\\\Expectation\\<Pest\\\\Expectation\\<array\\<int, string\\>\\|string\\>\\>\\:\\:\\$first_name\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/contact/tests/Tenant/Contact/ViewContactTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Pest\\\\Mixins\\\\Expectation\\<array\\<int, string\\>\\|string\\>\\|Pest\\\\Mixins\\\\Expectation\\<Pest\\\\Expectation\\<array\\<int, string\\>\\|string\\>\\>\\:\\:\\$school\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/contact/tests/Tenant/Contact/ViewContactTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Avoid assertSee\\(\\) and its alternatives\\: they only prove the text appears somewhere in the rendered output, not that it appears where the test actually expects it, so they can pass even when the feature under test is broken\\. Prefer a more precise assertion \\(e\\.g\\. asserting component/view state, a scoped selector, or the specific structure produced\\)\\. If you are certain you specifically need this assertion, add an inline ignore for this rule \\(// @phpstan\\-ignore Common\\.noAssertSee\\)\\.$#',
    'identifier' => 'Common.noAssertSee',
    'count' => 11,
    'path' => __DIR__ . '/app-modules/contact/tests/Tenant/Contact/ViewContactTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Livewire\\\\Features\\\\SupportTesting\\\\Testable\\:\\:assertDontSeeLivewire\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 4,
    'path' => __DIR__ . '/app-modules/contact/tests/Tenant/Contact/ViewContactTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Avoid assertSee\\(\\) and its alternatives\\: they only prove the text appears somewhere in the rendered output, not that it appears where the test actually expects it, so they can pass even when the feature under test is broken\\. Prefer a more precise assertion \\(e\\.g\\. asserting component/view state, a scoped selector, or the specific structure produced\\)\\. If you are certain you specifically need this assertion, add an inline ignore for this rule \\(// @phpstan\\-ignore Common\\.noAssertSee\\)\\.$#',
    'identifier' => 'Common.noAssertSee',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/contact/tests/Tenant/ContactType/ViewContactTypeTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to function assert\\(\\) with true will always evaluate to true\\.$#',
    'identifier' => 'function.alreadyNarrowedType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/contact/tests/Tenant/Jobs/MatchUnaffiliatedContactsJobTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Instanceof between Illuminate\\\\Queue\\\\Middleware\\\\WithoutOverlapping and Illuminate\\\\Queue\\\\Middleware\\\\WithoutOverlapping will always evaluate to true\\.$#',
    'identifier' => 'instanceof.alwaysTrue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/contact/tests/Tenant/Jobs/MatchUnaffiliatedContactsJobTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Pest\\\\PendingCalls\\\\TestCall\\:\\:mock\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/contact/tests/Tenant/Observers/ContactObserverTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Faker\\\\Generator\\:\\:state\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/contact/tests/Tenant/Organization/RequestFactories/CreateOrganizationRequestFactory.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Faker\\\\Generator\\:\\:state\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/contact/tests/Tenant/Organization/RequestFactories/EditOrganizationRequestFactory.php',
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
    'message' => '#^Action class "AidingApp\\\\Engagement\\\\Actions\\\\CreateEngagement" defines disallowed public method "execute"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/engagement/src/Actions/CreateEngagement.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\Engagement\\\\Actions\\\\CreateEngagement" must define a public "__invoke" method\\. Action classes should be accessed via invocation and keep other behavior in private/protected helpers\\.$#',
    'identifier' => 'Common.actionClassMustBeInvokable',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/engagement/src/Actions/CreateEngagement.php',
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
    'message' => '#^Action class "AidingApp\\\\Engagement\\\\Actions\\\\CreateEngagementBatch" defines disallowed public method "execute"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/engagement/src/Actions/CreateEngagementBatch.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\Engagement\\\\Actions\\\\CreateEngagementBatch" must define a public "__invoke" method\\. Action classes should be accessed via invocation and keep other behavior in private/protected helpers\\.$#',
    'identifier' => 'Common.actionClassMustBeInvokable',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/engagement/src/Actions/CreateEngagementBatch.php',
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
    'message' => '#^Parameter \\$recipient of class AidingApp\\\\Engagement\\\\DataTransferObjects\\\\EngagementCreationData constructor expects AidingApp\\\\Notification\\\\Models\\\\Contracts\\\\CanBeNotified\\|Illuminate\\\\Database\\\\Eloquent\\\\Collection, Illuminate\\\\Support\\\\Collection given\\.$#',
    'identifier' => 'argument.type',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/engagement/src/Filament/Actions/BulkEngagementAction.php',
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
    'message' => '#^Avoid strtolower\\(\\) as it is not multibyte\\-safe\\. Use Laravel\'s Str\\:\\:lower\\(\\) instead, or mb_strtolower\\(\\) if a framework\\-free alternative is required\\. If you are certain you specifically need strtolower\\(\\), add an inline ignore for this rule \\(// @phpstan\\-ignore Common\\.noStrtolower\\)\\.$#',
    'identifier' => 'Common.noStrtolower',
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
    'message' => '#^Eloquent local scopes are not allowed\\. The "scopeIsNotPartOfABatch" method on "AidingApp\\\\Engagement\\\\Models\\\\Engagement" defines a local scope, which relies on magic methods that static analysis cannot reason about\\. Use a tappable scope instead\\. See https\\://seankegel\\.com/elevate\\-your\\-laravel\\-eloquent\\-queries\\-with\\-tappable\\-scopes\\.$#',
    'identifier' => 'Common.noLocalModelScope',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/engagement/src/Models/Engagement.php',
];
$ignoreErrors[] = [
    'message' => '#^Eloquent local scopes are not allowed\\. The "scopeSentToContact" method on "AidingApp\\\\Engagement\\\\Models\\\\Engagement" defines a local scope, which relies on magic methods that static analysis cannot reason about\\. Use a tappable scope instead\\. See https\\://seankegel\\.com/elevate\\-your\\-laravel\\-eloquent\\-queries\\-with\\-tappable\\-scopes\\.$#',
    'identifier' => 'Common.noLocalModelScope',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/engagement/src/Models/Engagement.php',
];
$ignoreErrors[] = [
    'message' => '#^Method name "scopeIsNotPartOfABatch" is not in camelCase\\.$#',
    'identifier' => 'MeliorStan.methodNameNotCamelCase',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/engagement/src/Models/Engagement.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AidingApp\\\\Engagement\\\\Models\\\\EngagementFile\\:\\:prunable\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/engagement/src/Models/EngagementFile.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:orderedEngagementResponses\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/engagement/src/Models/EngagementResponse.php',
];
$ignoreErrors[] = [
    'message' => '#^Eloquent local scopes are not allowed\\. The "scopeSentByContact" method on "AidingApp\\\\Engagement\\\\Models\\\\EngagementResponse" defines a local scope, which relies on magic methods that static analysis cannot reason about\\. Use a tappable scope instead\\. See https\\://seankegel\\.com/elevate\\-your\\-laravel\\-eloquent\\-queries\\-with\\-tappable\\-scopes\\.$#',
    'identifier' => 'Common.noLocalModelScope',
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
    'count' => 1,
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
    'message' => '#^Match expression does not handle remaining value\\: AidingApp\\\\Notification\\\\Enums\\\\NotificationChannel\\:\\:Database$#',
    'identifier' => 'match.unhandled',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/engagement/src/Notifications/EngagementNotification.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\Form\\\\Actions\\\\DeliverFormSubmissionRequestByEmail" defines disallowed public method "__serialize"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Actions/DeliverFormSubmissionRequestByEmail.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\Form\\\\Actions\\\\DeliverFormSubmissionRequestByEmail" defines disallowed public method "__unserialize"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Actions/DeliverFormSubmissionRequestByEmail.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\Form\\\\Actions\\\\DeliverFormSubmissionRequestByEmail" defines disallowed public method "afterCommit"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Actions/DeliverFormSubmissionRequestByEmail.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\Form\\\\Actions\\\\DeliverFormSubmissionRequestByEmail" defines disallowed public method "allOnConnection"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Actions/DeliverFormSubmissionRequestByEmail.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\Form\\\\Actions\\\\DeliverFormSubmissionRequestByEmail" defines disallowed public method "allOnQueue"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Actions/DeliverFormSubmissionRequestByEmail.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\Form\\\\Actions\\\\DeliverFormSubmissionRequestByEmail" defines disallowed public method "appendToChain"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Actions/DeliverFormSubmissionRequestByEmail.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\Form\\\\Actions\\\\DeliverFormSubmissionRequestByEmail" defines disallowed public method "assertDeleted"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Actions/DeliverFormSubmissionRequestByEmail.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\Form\\\\Actions\\\\DeliverFormSubmissionRequestByEmail" defines disallowed public method "assertDoesntHaveChain"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Actions/DeliverFormSubmissionRequestByEmail.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\Form\\\\Actions\\\\DeliverFormSubmissionRequestByEmail" defines disallowed public method "assertFailed"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Actions/DeliverFormSubmissionRequestByEmail.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\Form\\\\Actions\\\\DeliverFormSubmissionRequestByEmail" defines disallowed public method "assertFailedWith"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Actions/DeliverFormSubmissionRequestByEmail.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\Form\\\\Actions\\\\DeliverFormSubmissionRequestByEmail" defines disallowed public method "assertHasChain"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Actions/DeliverFormSubmissionRequestByEmail.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\Form\\\\Actions\\\\DeliverFormSubmissionRequestByEmail" defines disallowed public method "assertNotDeleted"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Actions/DeliverFormSubmissionRequestByEmail.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\Form\\\\Actions\\\\DeliverFormSubmissionRequestByEmail" defines disallowed public method "assertNotFailed"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Actions/DeliverFormSubmissionRequestByEmail.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\Form\\\\Actions\\\\DeliverFormSubmissionRequestByEmail" defines disallowed public method "assertNotReleased"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Actions/DeliverFormSubmissionRequestByEmail.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\Form\\\\Actions\\\\DeliverFormSubmissionRequestByEmail" defines disallowed public method "assertReleased"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Actions/DeliverFormSubmissionRequestByEmail.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\Form\\\\Actions\\\\DeliverFormSubmissionRequestByEmail" defines disallowed public method "attempts"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Actions/DeliverFormSubmissionRequestByEmail.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\Form\\\\Actions\\\\DeliverFormSubmissionRequestByEmail" defines disallowed public method "beforeCommit"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Actions/DeliverFormSubmissionRequestByEmail.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\Form\\\\Actions\\\\DeliverFormSubmissionRequestByEmail" defines disallowed public method "chain"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Actions/DeliverFormSubmissionRequestByEmail.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\Form\\\\Actions\\\\DeliverFormSubmissionRequestByEmail" defines disallowed public method "delay"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Actions/DeliverFormSubmissionRequestByEmail.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\Form\\\\Actions\\\\DeliverFormSubmissionRequestByEmail" defines disallowed public method "delete"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Actions/DeliverFormSubmissionRequestByEmail.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\Form\\\\Actions\\\\DeliverFormSubmissionRequestByEmail" defines disallowed public method "dispatch"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Actions/DeliverFormSubmissionRequestByEmail.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\Form\\\\Actions\\\\DeliverFormSubmissionRequestByEmail" defines disallowed public method "dispatchAfterResponse"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Actions/DeliverFormSubmissionRequestByEmail.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\Form\\\\Actions\\\\DeliverFormSubmissionRequestByEmail" defines disallowed public method "dispatchIf"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Actions/DeliverFormSubmissionRequestByEmail.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\Form\\\\Actions\\\\DeliverFormSubmissionRequestByEmail" defines disallowed public method "dispatchNextJobInChain"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Actions/DeliverFormSubmissionRequestByEmail.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\Form\\\\Actions\\\\DeliverFormSubmissionRequestByEmail" defines disallowed public method "dispatchSync"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Actions/DeliverFormSubmissionRequestByEmail.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\Form\\\\Actions\\\\DeliverFormSubmissionRequestByEmail" defines disallowed public method "dispatchUnless"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Actions/DeliverFormSubmissionRequestByEmail.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\Form\\\\Actions\\\\DeliverFormSubmissionRequestByEmail" defines disallowed public method "fail"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Actions/DeliverFormSubmissionRequestByEmail.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\Form\\\\Actions\\\\DeliverFormSubmissionRequestByEmail" defines disallowed public method "handle"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Actions/DeliverFormSubmissionRequestByEmail.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\Form\\\\Actions\\\\DeliverFormSubmissionRequestByEmail" defines disallowed public method "invokeChainCatchCallbacks"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Actions/DeliverFormSubmissionRequestByEmail.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\Form\\\\Actions\\\\DeliverFormSubmissionRequestByEmail" defines disallowed public method "onConnection"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Actions/DeliverFormSubmissionRequestByEmail.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\Form\\\\Actions\\\\DeliverFormSubmissionRequestByEmail" defines disallowed public method "onGroup"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Actions/DeliverFormSubmissionRequestByEmail.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\Form\\\\Actions\\\\DeliverFormSubmissionRequestByEmail" defines disallowed public method "onQueue"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Actions/DeliverFormSubmissionRequestByEmail.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\Form\\\\Actions\\\\DeliverFormSubmissionRequestByEmail" defines disallowed public method "prependToChain"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Actions/DeliverFormSubmissionRequestByEmail.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\Form\\\\Actions\\\\DeliverFormSubmissionRequestByEmail" defines disallowed public method "release"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Actions/DeliverFormSubmissionRequestByEmail.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\Form\\\\Actions\\\\DeliverFormSubmissionRequestByEmail" defines disallowed public method "restoreModel"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Actions/DeliverFormSubmissionRequestByEmail.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\Form\\\\Actions\\\\DeliverFormSubmissionRequestByEmail" defines disallowed public method "setJob"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Actions/DeliverFormSubmissionRequestByEmail.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\Form\\\\Actions\\\\DeliverFormSubmissionRequestByEmail" defines disallowed public method "through"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Actions/DeliverFormSubmissionRequestByEmail.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\Form\\\\Actions\\\\DeliverFormSubmissionRequestByEmail" defines disallowed public method "withChain"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Actions/DeliverFormSubmissionRequestByEmail.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\Form\\\\Actions\\\\DeliverFormSubmissionRequestByEmail" defines disallowed public method "withDeduplicator"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Actions/DeliverFormSubmissionRequestByEmail.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\Form\\\\Actions\\\\DeliverFormSubmissionRequestByEmail" defines disallowed public method "withFakeQueueInteractions"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Actions/DeliverFormSubmissionRequestByEmail.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\Form\\\\Actions\\\\DeliverFormSubmissionRequestByEmail" defines disallowed public method "withoutDelay"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Actions/DeliverFormSubmissionRequestByEmail.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\Form\\\\Actions\\\\DeliverFormSubmissionRequestByEmail" must define a public "__invoke" method\\. Action classes should be accessed via invocation and keep other behavior in private/protected helpers\\.$#',
    'identifier' => 'Common.actionClassMustBeInvokable',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Actions/DeliverFormSubmissionRequestByEmail.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\Form\\\\Actions\\\\GenerateFormKitSchema" defines disallowed public method "content"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Actions/GenerateFormKitSchema.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\Form\\\\Actions\\\\GenerateFormKitSchema" defines disallowed public method "grid"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Actions/GenerateFormKitSchema.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\Form\\\\Actions\\\\GenerateFormKitSchema" defines disallowed public method "text"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Actions/GenerateFormKitSchema.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\Form\\\\Actions\\\\GenerateFormKitSchema" defines disallowed public method "wizardContent"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Actions/GenerateFormKitSchema.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\Form\\\\Actions\\\\GenerateServiceRequestFeedbackFormEmbedCode" defines disallowed public method "handle"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Actions/GenerateServiceRequestFeedbackFormEmbedCode.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\Form\\\\Actions\\\\GenerateServiceRequestFeedbackFormEmbedCode" must define a public "__invoke" method\\. Action classes should be accessed via invocation and keep other behavior in private/protected helpers\\.$#',
    'identifier' => 'Common.actionClassMustBeInvokable',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Actions/GenerateServiceRequestFeedbackFormEmbedCode.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\Form\\\\Actions\\\\GenerateSubmissibleValidation" defines disallowed public method "fields"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Actions/GenerateSubmissibleValidation.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\Form\\\\Actions\\\\GenerateSubmissibleValidation" defines disallowed public method "wizardRules"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Actions/GenerateSubmissibleValidation.php',
];
$ignoreErrors[] = [
    'message' => '#^Variable \\$block in PHPDoc tag @var does not exist\\.$#',
    'identifier' => 'varTag.variableNotFound',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/form/src/Filament/Blocks/FormFieldBlockRegistry.php',
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
    'count' => 1,
    'path' => __DIR__ . '/app-modules/form/src/Notifications/FormSubmissionRequestNotification.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\InAppCommunication\\\\Actions\\\\GetUserConversations" defines disallowed public method "pinned"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/in-app-communication/src/Actions/GetUserConversations.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Pest\\\\Mixins\\\\Expectation\\<array\\<int, string\\>\\|string\\>\\|Pest\\\\Mixins\\\\Expectation\\<Pest\\\\Expectation\\<array\\<int, string\\>\\|string\\>\\>\\:\\:\\$participant_id\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/in-app-communication/tests/Tenant/Actions/AddParticipantTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$event of anonymous function has no typehint\\.$#',
    'identifier' => 'MeliorStan.closureParameterMissingTypehint',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/in-app-communication/tests/Tenant/Actions/AddParticipantTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Pest\\\\Mixins\\\\Expectation\\<array\\<int, string\\>\\|string\\>\\|Pest\\\\Mixins\\\\Expectation\\<Pest\\\\Expectation\\<array\\<int, string\\>\\|string\\>\\>\\:\\:\\$ephemeral_period\\.$#',
    'identifier' => 'property.notFound',
    'count' => 3,
    'path' => __DIR__ . '/app-modules/in-app-communication/tests/Tenant/Actions/CreateConversationTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Pest\\\\Mixins\\\\Expectation\\<array\\<int, string\\>\\|string\\>\\|Pest\\\\Mixins\\\\Expectation\\<Pest\\\\Expectation\\<array\\<int, string\\>\\|string\\>\\>\\:\\:\\$is_private\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/in-app-communication/tests/Tenant/Actions/CreateConversationTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Pest\\\\Mixins\\\\Expectation\\<array\\<int, string\\>\\|string\\>\\|Pest\\\\Mixins\\\\Expectation\\<Pest\\\\Expectation\\<array\\<int, string\\>\\|string\\>\\>\\:\\:\\$name\\.$#',
    'identifier' => 'property.notFound',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/in-app-communication/tests/Tenant/Actions/CreateConversationTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Pest\\\\Mixins\\\\Expectation\\<array\\<mixed\\>\\|null\\>\\:\\:\\$not\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/in-app-communication/tests/Tenant/Actions/GetUserConversationsTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Pest\\\\Mixins\\\\Expectation\\<mixed\\>\\:\\:\\$not\\.$#',
    'identifier' => 'property.notFound',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/in-app-communication/tests/Tenant/Actions/GetUserConversationsTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Illuminate\\\\Contracts\\\\Pagination\\\\CursorPaginator\\<int, AidingApp\\\\InAppCommunication\\\\Models\\\\Conversation\\>\\:\\:pluck\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 3,
    'path' => __DIR__ . '/app-modules/in-app-communication/tests/Tenant/Actions/GetUserConversationsTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Pest\\\\Mixins\\\\Expectation\\<array\\<int, string\\>\\|string\\>\\|Pest\\\\Mixins\\\\Expectation\\<Pest\\\\Expectation\\<array\\<int, string\\>\\|string\\>\\>\\:\\:\\$participant_id\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/in-app-communication/tests/Tenant/Actions/JoinChannelTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$event of anonymous function has no typehint\\.$#',
    'identifier' => 'MeliorStan.closureParameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/in-app-communication/tests/Tenant/Actions/MarkConversationAsReadTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Pest\\\\Mixins\\\\Expectation\\<array\\<int, string\\>\\|string\\>\\|Pest\\\\Mixins\\\\Expectation\\<Pest\\\\Expectation\\<array\\<int, string\\>\\|string\\>\\>\\:\\:\\$author_id\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/in-app-communication/tests/Tenant/Actions/SendMessageTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$event of anonymous function has no typehint\\.$#',
    'identifier' => 'MeliorStan.closureParameterMissingTypehint',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/in-app-communication/tests/Tenant/Actions/SendMessageTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$event of anonymous function has no typehint\\.$#',
    'identifier' => 'MeliorStan.closureParameterMissingTypehint',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/in-app-communication/tests/Tenant/Actions/UpdateConversationTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Pest\\\\Mixins\\\\Expectation\\<mixed\\>\\:\\:\\$is_manager\\.$#',
    'identifier' => 'property.notFound',
    'count' => 3,
    'path' => __DIR__ . '/app-modules/in-app-communication/tests/Tenant/Actions/UpdateParticipantRoleTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Pest\\\\Mixins\\\\Expectation\\<mixed\\>\\:\\:\\$is_pinned\\.$#',
    'identifier' => 'property.notFound',
    'count' => 4,
    'path' => __DIR__ . '/app-modules/in-app-communication/tests/Tenant/Actions/UpdateParticipantSettingsTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Pest\\\\Mixins\\\\Expectation\\<mixed\\>\\:\\:\\$notification_preference\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/in-app-communication/tests/Tenant/Actions/UpdateParticipantSettingsTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Variable name "\\$i" is shorter than minimum length of 3 characters\\.$#',
    'identifier' => 'MeliorStan.shortVariable',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/in-app-communication/tests/Tenant/Http/Controllers/Conversations/ListConversationsControllerTest.php',
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
    'message' => '#^Call to an undefined method Pest\\\\PendingCalls\\\\TestCall\\:\\:expectExceptionMessage\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/integration-aws-ses-event-handling/tests/Tenant/Unit/SesConfigurationSetTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Avoid direct request IP accessors \\(ip\\(\\), ips\\(\\), getClientIp\\(\\), getClientIps\\(\\)\\)\\. Use CanyonGBS\\\\Common\\\\Support\\\\ClientIp\\:\\:resolve\\(\\) instead so client IP resolution remains Cloudflare and ALB aware\\.$#',
    'identifier' => 'Common.useClientIpResolver',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/integration-google-recaptcha/src/Rules/RecaptchaTokenValid.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Pest\\\\Mixins\\\\Expectation\\<array\\<int, string\\>\\|string\\>\\|Pest\\\\Mixins\\\\Expectation\\<Pest\\\\Expectation\\<array\\<int, string\\>\\|string\\>\\>\\:\\:\\$vector_store_id\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/integration-open-ai/tests/Tenant/Feature/Services/OpenAiGptTestServiceTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Faker\\\\Generator\\:\\:catchPhrase\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/inventory-management/database/factories/AssetFactory.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\InventoryManagement\\\\Actions\\\\UpdateAssetStatusBasedOnMaintenanceActivity" defines disallowed public method "handle"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/inventory-management/src/Actions/UpdateAssetStatusBasedOnMaintenanceActivity.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\InventoryManagement\\\\Actions\\\\UpdateAssetStatusBasedOnMaintenanceActivity" must define a public "__invoke" method\\. Action classes should be accessed via invocation and keep other behavior in private/protected helpers\\.$#',
    'identifier' => 'Common.actionClassMustBeInvokable',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/inventory-management/src/Actions/UpdateAssetStatusBasedOnMaintenanceActivity.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Builder\\:\\:withoutReturned\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/inventory-management/src/Filament/Resources/AssetCheckOuts/Pages/ListAssetCheckOuts.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Filament\\\\Resources\\\\Assets\\\\Pages\\\\ManageAssetMaintenanceActivity\\:\\:managers\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/inventory-management/src/Filament/Resources/Assets/Pages/ManageAssetMaintenanceActivity.php',
];
$ignoreErrors[] = [
    'message' => '#^Unsafe call to private method AidingApp\\\\InventoryManagement\\\\Filament\\\\Resources\\\\Assets\\\\Pages\\\\ManageAssetMaintenanceActivity\\:\\:managers\\(\\) through static\\:\\:\\.$#',
    'identifier' => 'staticClassAccess.privateMethod',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/inventory-management/src/Filament/Resources/Assets/Pages/ManageAssetMaintenanceActivity.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:checkIns\\(\\)\\.$#',
    'identifier' => 'method.notFound',
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
    'message' => '#^Eloquent local scopes are not allowed\\. The "scopeWithoutReturned" method on "AidingApp\\\\InventoryManagement\\\\Models\\\\AssetCheckOut" defines a local scope, which relies on magic methods that static analysis cannot reason about\\. Use a tappable scope instead\\. See https\\://seankegel\\.com/elevate\\-your\\-laravel\\-eloquent\\-queries\\-with\\-tappable\\-scopes\\.$#',
    'identifier' => 'Common.noLocalModelScope',
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
    'message' => '#^Method AidingApp\\\\InventoryManagement\\\\Models\\\\Scopes\\\\ClassifiedAs\\:\\:__invoke\\(\\) has parameter \\$query with generic class Illuminate\\\\Database\\\\Eloquent\\\\Builder but does not specify its types\\: TModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/inventory-management/src/Models/Scopes/ClassifiedAs.php',
];
$ignoreErrors[] = [
    'message' => '#^Avoid strtolower\\(\\) as it is not multibyte\\-safe\\. Use Laravel\'s Str\\:\\:lower\\(\\) instead, or mb_strtolower\\(\\) if a framework\\-free alternative is required\\. If you are certain you specifically need strtolower\\(\\), add an inline ignore for this rule \\(// @phpstan\\-ignore Common\\.noStrtolower\\)\\.$#',
    'identifier' => 'Common.noStrtolower',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/knowledge-base/src/Filament/Resources/KnowledgeBaseCategories/Pages/CreateKnowledgeBaseCategory.php',
];
$ignoreErrors[] = [
    'message' => '#^Avoid strtolower\\(\\) as it is not multibyte\\-safe\\. Use Laravel\'s Str\\:\\:lower\\(\\) instead, or mb_strtolower\\(\\) if a framework\\-free alternative is required\\. If you are certain you specifically need strtolower\\(\\), add an inline ignore for this rule \\(// @phpstan\\-ignore Common\\.noStrtolower\\)\\.$#',
    'identifier' => 'Common.noStrtolower',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/knowledge-base/src/Filament/Resources/KnowledgeBaseCategories/Pages/EditKnowledgeBaseCategory.php',
];
$ignoreErrors[] = [
    'message' => '#^Avoid strtolower\\(\\) as it is not multibyte\\-safe\\. Use Laravel\'s Str\\:\\:lower\\(\\) instead, or mb_strtolower\\(\\) if a framework\\-free alternative is required\\. If you are certain you specifically need strtolower\\(\\), add an inline ignore for this rule \\(// @phpstan\\-ignore Common\\.noStrtolower\\)\\.$#',
    'identifier' => 'Common.noStrtolower',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/knowledge-base/src/Filament/Resources/KnowledgeBaseCategories/RelationManagers/SubCategoriesRelationManager.php',
];
$ignoreErrors[] = [
    'message' => '#^Eloquent local scopes are not allowed\\. The "scopePublic" method on "AidingApp\\\\KnowledgeBase\\\\Models\\\\KnowledgeBaseItem" defines a local scope, which relies on magic methods that static analysis cannot reason about\\. Use a tappable scope instead\\. See https\\://seankegel\\.com/elevate\\-your\\-laravel\\-eloquent\\-queries\\-with\\-tappable\\-scopes\\.$#',
    'identifier' => 'Common.noLocalModelScope',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/knowledge-base/src/Models/KnowledgeBaseItem.php',
];
$ignoreErrors[] = [
    'message' => '#^PHPDoc tag @var with type AidingApp\\\\KnowledgeBase\\\\Jobs\\\\CheckKnowledgeBaseArticleImagesJob is not subtype of native type Mockery\\\\MockInterface\\.$#',
    'identifier' => 'varTag.nativeType',
    'count' => 3,
    'path' => __DIR__ . '/app-modules/knowledge-base/tests/Tenant/Filament/Jobs/CheckKnowledgeBaseArticleImagesJobTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Cannot call method pluck\\(\\) on array\\<int, string\\>\\|Pest\\\\Expectation\\<array\\<int, string\\>\\|string\\>\\|Pest\\\\Expectations\\\\EachExpectation\\<array\\<int, string\\>\\|string\\>\\|Pest\\\\Expectations\\\\HigherOrderExpectation\\<Pest\\\\Expectation\\<array\\<int, string\\>\\|string\\>, array\\<int, string\\>\\|string\\|null\\>\\|Pest\\\\Expectations\\\\OppositeExpectation\\<array\\<int, string\\>\\|string\\>\\|string\\.$#',
    'identifier' => 'method.nonObject',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/knowledge-base/tests/Tenant/Filament/Resources/KnowledgeBaseCategories/Pages/EditKnowledgeBaseCategoryTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to protected property Spatie\\\\Invade\\\\Invader\\<App\\\\Filament\\\\Forms\\\\Components\\\\UserSelect\\>\\:\\:\\$filterAdmins\\.$#',
    'identifier' => 'property.protected',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/knowledge-base/tests/Tenant/Filament/Resources/KnowledgeBaseItems/Pages/EditKnowledgeBaseItemTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$guardName of method Illuminate\\\\Database\\\\Migrations\\\\Migration@anonymous/app\\-modules/license\\-management/database/migrations/2024_11_21_194048_data_seed_permissions_for_product_license_module\\.php\\:42\\:\\:deletePermissions\\(\\) expects string, array\\<string\\> given\\.$#',
    'identifier' => 'argument.type',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/license-management/database/migrations/2024_11_21_194048_data_seed_permissions_for_product_license_module.php',
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
    'message' => '#^Access to an undefined property AidingApp\\\\LicenseManagement\\\\Models\\\\Product\\:\\:\\$created_by_id\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/license-management/src/Observers/ProductObserver.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AidingApp\\\\Notification\\\\DataTransferObjects\\\\EmailChannelResultData\\:\\:__construct\\(\\) has parameter \\$recipients with no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/notification/src/DataTransferObjects/EmailChannelResultData.php',
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
    'message' => '#^Action class "AidingApp\\\\Portal\\\\Actions\\\\GenerateAiResolutionPrompt" defines disallowed public method "execute"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/portal/src/Actions/GenerateAiResolutionPrompt.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\Portal\\\\Actions\\\\GenerateAiResolutionPrompt" must define a public "__invoke" method\\. Action classes should be accessed via invocation and keep other behavior in private/protected helpers\\.$#',
    'identifier' => 'Common.actionClassMustBeInvokable',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/portal/src/Actions/GenerateAiResolutionPrompt.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\Portal\\\\Actions\\\\GeneratePortalEmbedCode" defines disallowed public method "handle"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/portal/src/Actions/GeneratePortalEmbedCode.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\Portal\\\\Actions\\\\GeneratePortalEmbedCode" must define a public "__invoke" method\\. Action classes should be accessed via invocation and keep other behavior in private/protected helpers\\.$#',
    'identifier' => 'Common.actionClassMustBeInvokable',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/portal/src/Actions/GeneratePortalEmbedCode.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\Portal\\\\Actions\\\\GenerateServiceRequestForm" defines disallowed public method "execute"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/portal/src/Actions/GenerateServiceRequestForm.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\Portal\\\\Actions\\\\GenerateServiceRequestForm" must define a public "__invoke" method\\. Action classes should be accessed via invocation and keep other behavior in private/protected helpers\\.$#',
    'identifier' => 'Common.actionClassMustBeInvokable',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/portal/src/Actions/GenerateServiceRequestForm.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\Portal\\\\Actions\\\\GenerateServiceRequestQuestionsAiPrompt" defines disallowed public method "execute"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/portal/src/Actions/GenerateServiceRequestQuestionsAiPrompt.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\Portal\\\\Actions\\\\GenerateServiceRequestQuestionsAiPrompt" must define a public "__invoke" method\\. Action classes should be accessed via invocation and keep other behavior in private/protected helpers\\.$#',
    'identifier' => 'Common.actionClassMustBeInvokable',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/portal/src/Actions/GenerateServiceRequestQuestionsAiPrompt.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\Portal\\\\Actions\\\\ProcessServiceRequestSubmissionField" defines disallowed public method "execute"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/portal/src/Actions/ProcessServiceRequestSubmissionField.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\Portal\\\\Actions\\\\ProcessServiceRequestSubmissionField" must define a public "__invoke" method\\. Action classes should be accessed via invocation and keep other behavior in private/protected helpers\\.$#',
    'identifier' => 'Common.actionClassMustBeInvokable',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/portal/src/Actions/ProcessServiceRequestSubmissionField.php',
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
    'message' => '#^Negated boolean expression is always false\\.$#',
    'identifier' => 'booleanNot.alwaysFalse',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/portal/src/Http/Controllers/KnowledgeManagementPortal/KnowledgeManagementPortalLogoutController.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Support\\\\ValidatedInput\\:\\:\\$email\\.$#',
    'identifier' => 'property.notFound',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/portal/src/Http/Controllers/KnowledgeManagementPortal/KnowledgeManagementPortalRequestAuthenticationController.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Support\\\\ValidatedInput\\:\\:\\$isSpa\\.$#',
    'identifier' => 'property.notFound',
    'count' => 2,
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
    'message' => '#^Eloquent models must define a "\\$fillable" property\\. Add a "\\$fillable" property to this model or a parent model\\.$#',
    'identifier' => 'Common.modelMissingFillable',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/portal/src/Models/PortalAuthentication.php',
];
$ignoreErrors[] = [
    'message' => '#^Eloquent models must define a "\\$fillable" property\\. Add a "\\$fillable" property to this model or a parent model\\.$#',
    'identifier' => 'Common.modelMissingFillable',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/portal/src/Models/PortalGuest.php',
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
    'message' => '#^Access to an undefined property Pest\\\\Mixins\\\\Expectation\\<string\\|null\\>\\:\\:\\$not\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/portal/tests/Tenant/Actions/GenerateAiResolutionPromptTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Function serviceRequestFormStepContent\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/portal/tests/Tenant/Actions/GenerateServiceRequestFormTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Pest\\\\Mixins\\\\Expectation\\<string\\|null\\>\\:\\:\\$not\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/portal/tests/Tenant/Actions/GenerateServiceRequestQuestionsAiPromptTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Unable to resolve the template type TKey in call to function collect$#',
    'identifier' => 'argument.templateType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/portal/tests/Tenant/Http/Controllers/KnowledgeManagementPortal/GetServiceRequestFormControllerTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Unable to resolve the template type TValue in call to function collect$#',
    'identifier' => 'argument.templateType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/portal/tests/Tenant/Http/Controllers/KnowledgeManagementPortal/GetServiceRequestFormControllerTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$value of static method Illuminate\\\\Support\\\\Facades\\\\Hash\\:\\:make\\(\\) expects string, int given\\.$#',
    'identifier' => 'argument.type',
    'count' => 7,
    'path' => __DIR__ . '/app-modules/portal/tests/Tenant/Http/Controllers/KnowledgeManagementPortal/KnowledgeManagementPortalAuthenticateControllerTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Unable to resolve the template type TKey in call to function collect$#',
    'identifier' => 'argument.templateType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/portal/tests/Tenant/Http/Controllers/KnowledgeManagementPortal/KnowledgeManagementPortalCategoryControllerTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Unable to resolve the template type TValue in call to function collect$#',
    'identifier' => 'argument.templateType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/portal/tests/Tenant/Http/Controllers/KnowledgeManagementPortal/KnowledgeManagementPortalCategoryControllerTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Unable to resolve the template type TKey in call to function collect$#',
    'identifier' => 'argument.templateType',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/portal/tests/Tenant/Http/Controllers/KnowledgeManagementPortal/KnowledgeManagementPortalCategorySortTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Unable to resolve the template type TValue in call to function collect$#',
    'identifier' => 'argument.templateType',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/portal/tests/Tenant/Http/Controllers/KnowledgeManagementPortal/KnowledgeManagementPortalCategorySortTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$value of static method Illuminate\\\\Support\\\\Facades\\\\Hash\\:\\:make\\(\\) expects string, int given\\.$#',
    'identifier' => 'argument.type',
    'count' => 7,
    'path' => __DIR__ . '/app-modules/portal/tests/Tenant/Http/Controllers/KnowledgeManagementPortal/KnowledgeManagementPortalRegisterControllerTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Unable to resolve the template type TKey in call to function collect$#',
    'identifier' => 'argument.templateType',
    'count' => 21,
    'path' => __DIR__ . '/app-modules/portal/tests/Tenant/Http/Controllers/KnowledgeManagementPortal/ServiceRequestTypesControllerTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Unable to resolve the template type TValue in call to function collect$#',
    'identifier' => 'argument.templateType',
    'count' => 21,
    'path' => __DIR__ . '/app-modules/portal/tests/Tenant/Http/Controllers/KnowledgeManagementPortal/ServiceRequestTypesControllerTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Variable name "\\$categoryAData" is not in camelCase\\.$#',
    'identifier' => 'MeliorStan.variableNameNotCamelCase',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/portal/tests/Tenant/Http/Controllers/KnowledgeManagementPortal/ServiceRequestTypesControllerTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Variable name "\\$categoryBData" is not in camelCase\\.$#',
    'identifier' => 'MeliorStan.variableNameNotCamelCase',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/portal/tests/Tenant/Http/Controllers/KnowledgeManagementPortal/ServiceRequestTypesControllerTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Pest\\\\PendingCalls\\\\TestCall\\:\\:\\$contact\\.$#',
    'identifier' => 'property.notFound',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/portal/tests/Tenant/Http/Controllers/KnowledgeManagementPortal/ShowServiceMonitorStatusControllerTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Pest\\\\PendingCalls\\\\TestCall\\:\\:postJson\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/portal/tests/Tenant/Http/Controllers/KnowledgeManagementPortal/StoreServiceRequestSecretControllerTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Unable to resolve the template type TKey in call to function collect$#',
    'identifier' => 'argument.templateType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/portal/tests/Tenant/Unit/AdvisoryApiTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Unable to resolve the template type TValue in call to function collect$#',
    'identifier' => 'argument.templateType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/portal/tests/Tenant/Unit/AdvisoryApiTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Avoid assertSee\\(\\) and its alternatives\\: they only prove the text appears somewhere in the rendered output, not that it appears where the test actually expects it, so they can pass even when the feature under test is broken\\. Prefer a more precise assertion \\(e\\.g\\. asserting component/view state, a scoped selector, or the specific structure produced\\)\\. If you are certain you specifically need this assertion, add an inline ignore for this rule \\(// @phpstan\\-ignore Common\\.noAssertSee\\)\\.$#',
    'identifier' => 'Common.noAssertSee',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/portal/tests/Tenant/Unit/KnowlegeManagementPortalSearchTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Function getServiceMonitorStatuses\\(\\) return type with generic class Illuminate\\\\Testing\\\\TestResponse does not specify its types\\: TResponse$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/portal/tests/Tenant/Unit/ServiceMonitorStatusApiTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Unable to resolve the template type TKey in call to function collect$#',
    'identifier' => 'argument.templateType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/portal/tests/Tenant/Unit/ServiceMonitorStatusApiTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Unable to resolve the template type TValue in call to function collect$#',
    'identifier' => 'argument.templateType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/portal/tests/Tenant/Unit/ServiceMonitorStatusApiTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$data of anonymous function has no typehint\\.$#',
    'identifier' => 'MeliorStan.closureParameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/project/tests/Tenant/Filament/Resources/PipelineResource/Pages/CreatePipelineTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$errors of anonymous function has no typehint\\.$#',
    'identifier' => 'MeliorStan.closureParameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/project/tests/Tenant/Filament/Resources/PipelineResource/Pages/CreatePipelineTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$data of anonymous function has no typehint\\.$#',
    'identifier' => 'MeliorStan.closureParameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/project/tests/Tenant/Filament/Resources/PipelineResource/Pages/EditPipelineTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$errors of anonymous function has no typehint\\.$#',
    'identifier' => 'MeliorStan.closureParameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/project/tests/Tenant/Filament/Resources/PipelineResource/Pages/EditPipelineTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Avoid assertSee\\(\\) and its alternatives\\: they only prove the text appears somewhere in the rendered output, not that it appears where the test actually expects it, so they can pass even when the feature under test is broken\\. Prefer a more precise assertion \\(e\\.g\\. asserting component/view state, a scoped selector, or the specific structure produced\\)\\. If you are certain you specifically need this assertion, add an inline ignore for this rule \\(// @phpstan\\-ignore Common\\.noAssertSee\\)\\.$#',
    'identifier' => 'Common.noAssertSee',
    'count' => 3,
    'path' => __DIR__ . '/app-modules/project/tests/Tenant/Filament/Resources/PipelineResource/Pages/ManagePipelineEntriesTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Avoid assertSee\\(\\) and its alternatives\\: they only prove the text appears somewhere in the rendered output, not that it appears where the test actually expects it, so they can pass even when the feature under test is broken\\. Prefer a more precise assertion \\(e\\.g\\. asserting component/view state, a scoped selector, or the specific structure produced\\)\\. If you are certain you specifically need this assertion, add an inline ignore for this rule \\(// @phpstan\\-ignore Common\\.noAssertSee\\)\\.$#',
    'identifier' => 'Common.noAssertSee',
    'count' => 6,
    'path' => __DIR__ . '/app-modules/project/tests/Tenant/Filament/Resources/PipelineResource/Pages/ViewPipelineTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property AidingApp\\\\Project\\\\Models\\\\ProjectMilestone\\:\\:\\$completed_tasks_count\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/project/tests/Tenant/Filament/Resources/Pipelines/Forms/PipelineEntryFormTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property AidingApp\\\\Project\\\\Models\\\\ProjectMilestone\\:\\:\\$tasks_count\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/project/tests/Tenant/Filament/Resources/Pipelines/Forms/PipelineEntryFormTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Anonymous function should return Pest\\\\Mixins\\\\Expectation\\<bool\\|null\\> but returns Pest\\\\Mixins\\\\Expectation\\<bool\\|null\\>\\.$#',
    'identifier' => 'return.type',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/project/tests/Tenant/Filament/Resources/ProjectResource/Pages/ListProjectsTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$data of anonymous function has no typehint\\.$#',
    'identifier' => 'MeliorStan.closureParameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/project/tests/Tenant/Filament/Resources/ProjectResource/Pages/ManageFilesTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$errors of anonymous function has no typehint\\.$#',
    'identifier' => 'MeliorStan.closureParameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/project/tests/Tenant/Filament/Resources/ProjectResource/Pages/ManageFilesTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Anonymous function should return Pest\\\\Mixins\\\\Expectation\\<bool\\|null\\> but returns Pest\\\\Mixins\\\\Expectation\\<bool\\|null\\>\\.$#',
    'identifier' => 'return.type',
    'count' => 3,
    'path' => __DIR__ . '/app-modules/project/tests/Tenant/PipelineArchivingFeatureCascadeTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$id\\.$#',
    'identifier' => 'property.notFound',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/project/tests/Tenant/PipelineEntryRelationshipsTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Unable to resolve the template type TValue in call to function expect$#',
    'identifier' => 'argument.templateType',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/project/tests/Tenant/PipelineEntryRelationshipsTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Pest\\\\Mixins\\\\Expectation\\<array\\<int, mixed\\>\\|null\\>\\:\\:\\$not\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/project/tests/Tenant/PipelineRelationshipsTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Avoid strtolower\\(\\) as it is not multibyte\\-safe\\. Use Laravel\'s Str\\:\\:lower\\(\\) instead, or mb_strtolower\\(\\) if a framework\\-free alternative is required\\. If you are certain you specifically need strtolower\\(\\), add an inline ignore for this rule \\(// @phpstan\\-ignore Common\\.noStrtolower\\)\\.$#',
    'identifier' => 'Common.noStrtolower',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/AssetsTable.php',
];
$ignoreErrors[] = [
    'message' => '#^If condition is always true\\.$#',
    'identifier' => 'if.alwaysTrue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/ServiceRequestsStats.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$query of anonymous function has no typehint\\.$#',
    'identifier' => 'MeliorStan.closureParameterMissingTypehint',
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
    'message' => '#^Avoid strtolower\\(\\) as it is not multibyte\\-safe\\. Use Laravel\'s Str\\:\\:lower\\(\\) instead, or mb_strtolower\\(\\) if a framework\\-free alternative is required\\. If you are certain you specifically need strtolower\\(\\), add an inline ignore for this rule \\(// @phpstan\\-ignore Common\\.noStrtolower\\)\\.$#',
    'identifier' => 'Common.noStrtolower',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/report/src/Filament/Widgets/ServiceRequestsTable.php',
];
$ignoreErrors[] = [
    'message' => '#^Function richContent\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/report/tests/Tenant/Filament/Pages/RequestCommunicationsTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Pest\\\\Mixins\\\\Expectation\\<array\\<int, mixed\\>\\|null\\>\\:\\:\\$not\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/report/tests/Tenant/Filament/Pages/ServiceRequestTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Pest\\\\Mixins\\\\Expectation\\<mixed\\>\\:\\:\\$not\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/report/tests/Tenant/Filament/Pages/ServiceRequestTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Avoid assertSee\\(\\) and its alternatives\\: they only prove the text appears somewhere in the rendered output, not that it appears where the test actually expects it, so they can pass even when the feature under test is broken\\. Prefer a more precise assertion \\(e\\.g\\. asserting component/view state, a scoped selector, or the specific structure produced\\)\\. If you are certain you specifically need this assertion, add an inline ignore for this rule \\(// @phpstan\\-ignore Common\\.noAssertSee\\)\\.$#',
    'identifier' => 'Common.noAssertSee',
    'count' => 3,
    'path' => __DIR__ . '/app-modules/report/tests/Tenant/Filament/Pages/ServiceRequestTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Offset \'children\' on array\\{name\\: string, value\\: string, children\\: array\\<int, mixed\\>\\} on left side of \\?\\? always exists and is not nullable\\.$#',
    'identifier' => 'nullCoalesce.offset',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/report/tests/Tenant/Filament/Pages/ServiceRequestTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Unable to resolve the template type TKey in call to function collect$#',
    'identifier' => 'argument.templateType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/report/tests/Tenant/Filament/Pages/ServiceRequestTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Unable to resolve the template type TValue in call to function collect$#',
    'identifier' => 'argument.templateType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/report/tests/Tenant/Filament/Pages/ServiceRequestTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Pest\\\\Mixins\\\\Expectation\\<array\\<string, string\\>\\|null\\>\\:\\:\\$not\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/report/tests/Tenant/Filament/Pages/SlaTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\ServiceManagement\\\\Actions\\\\AcceptServiceRequestConversation" defines disallowed public method "execute"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Actions/AcceptServiceRequestConversation.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\ServiceManagement\\\\Actions\\\\AcceptServiceRequestConversation" must define a public "__invoke" method\\. Action classes should be accessed via invocation and keep other behavior in private/protected helpers\\.$#',
    'identifier' => 'Common.actionClassMustBeInvokable',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Actions/AcceptServiceRequestConversation.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\ServiceManagement\\\\Actions\\\\AssignServiceRequestToDepartment" defines disallowed public method "execute"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Actions/AssignServiceRequestToDepartment.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\ServiceManagement\\\\Actions\\\\AssignServiceRequestToDepartment" must define a public "__invoke" method\\. Action classes should be accessed via invocation and keep other behavior in private/protected helpers\\.$#',
    'identifier' => 'Common.actionClassMustBeInvokable',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Actions/AssignServiceRequestToDepartment.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\ServiceManagement\\\\Actions\\\\BuildContactServiceRequestTypeTree" defines disallowed public method "execute"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Actions/BuildContactServiceRequestTypeTree.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\ServiceManagement\\\\Actions\\\\BuildContactServiceRequestTypeTree" must define a public "__invoke" method\\. Action classes should be accessed via invocation and keep other behavior in private/protected helpers\\.$#',
    'identifier' => 'Common.actionClassMustBeInvokable',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Actions/BuildContactServiceRequestTypeTree.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\ServiceManagement\\\\Actions\\\\ChangeRequest\\\\ApproveChangeRequest" defines disallowed public method "handle"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Actions/ChangeRequest/ApproveChangeRequest.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\ServiceManagement\\\\Actions\\\\ChangeRequest\\\\ApproveChangeRequest" must define a public "__invoke" method\\. Action classes should be accessed via invocation and keep other behavior in private/protected helpers\\.$#',
    'identifier' => 'Common.actionClassMustBeInvokable',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Actions/ChangeRequest/ApproveChangeRequest.php',
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
    'message' => '#^Action class "AidingApp\\\\ServiceManagement\\\\Actions\\\\CountActiveServiceRequestConversations" defines disallowed public method "execute"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Actions/CountActiveServiceRequestConversations.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\ServiceManagement\\\\Actions\\\\CountActiveServiceRequestConversations" must define a public "__invoke" method\\. Action classes should be accessed via invocation and keep other behavior in private/protected helpers\\.$#',
    'identifier' => 'Common.actionClassMustBeInvokable',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Actions/CountActiveServiceRequestConversations.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\ServiceManagement\\\\Actions\\\\CreateServiceRequestAction" defines disallowed public method "execute"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Actions/CreateServiceRequestAction.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\ServiceManagement\\\\Actions\\\\CreateServiceRequestAction" must define a public "__invoke" method\\. Action classes should be accessed via invocation and keep other behavior in private/protected helpers\\.$#',
    'identifier' => 'Common.actionClassMustBeInvokable',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Actions/CreateServiceRequestAction.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\ServiceManagement\\\\Actions\\\\CreateServiceRequestHistory" defines disallowed public method "__serialize"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Actions/CreateServiceRequestHistory.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\ServiceManagement\\\\Actions\\\\CreateServiceRequestHistory" defines disallowed public method "__unserialize"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Actions/CreateServiceRequestHistory.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\ServiceManagement\\\\Actions\\\\CreateServiceRequestHistory" defines disallowed public method "afterCommit"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Actions/CreateServiceRequestHistory.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\ServiceManagement\\\\Actions\\\\CreateServiceRequestHistory" defines disallowed public method "allOnConnection"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Actions/CreateServiceRequestHistory.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\ServiceManagement\\\\Actions\\\\CreateServiceRequestHistory" defines disallowed public method "allOnQueue"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Actions/CreateServiceRequestHistory.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\ServiceManagement\\\\Actions\\\\CreateServiceRequestHistory" defines disallowed public method "appendToChain"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Actions/CreateServiceRequestHistory.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\ServiceManagement\\\\Actions\\\\CreateServiceRequestHistory" defines disallowed public method "assertDeleted"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Actions/CreateServiceRequestHistory.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\ServiceManagement\\\\Actions\\\\CreateServiceRequestHistory" defines disallowed public method "assertDoesntHaveChain"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Actions/CreateServiceRequestHistory.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\ServiceManagement\\\\Actions\\\\CreateServiceRequestHistory" defines disallowed public method "assertFailed"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Actions/CreateServiceRequestHistory.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\ServiceManagement\\\\Actions\\\\CreateServiceRequestHistory" defines disallowed public method "assertFailedWith"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Actions/CreateServiceRequestHistory.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\ServiceManagement\\\\Actions\\\\CreateServiceRequestHistory" defines disallowed public method "assertHasChain"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Actions/CreateServiceRequestHistory.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\ServiceManagement\\\\Actions\\\\CreateServiceRequestHistory" defines disallowed public method "assertNotDeleted"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Actions/CreateServiceRequestHistory.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\ServiceManagement\\\\Actions\\\\CreateServiceRequestHistory" defines disallowed public method "assertNotFailed"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Actions/CreateServiceRequestHistory.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\ServiceManagement\\\\Actions\\\\CreateServiceRequestHistory" defines disallowed public method "assertNotReleased"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Actions/CreateServiceRequestHistory.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\ServiceManagement\\\\Actions\\\\CreateServiceRequestHistory" defines disallowed public method "assertReleased"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Actions/CreateServiceRequestHistory.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\ServiceManagement\\\\Actions\\\\CreateServiceRequestHistory" defines disallowed public method "attempts"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Actions/CreateServiceRequestHistory.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\ServiceManagement\\\\Actions\\\\CreateServiceRequestHistory" defines disallowed public method "beforeCommit"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Actions/CreateServiceRequestHistory.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\ServiceManagement\\\\Actions\\\\CreateServiceRequestHistory" defines disallowed public method "chain"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Actions/CreateServiceRequestHistory.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\ServiceManagement\\\\Actions\\\\CreateServiceRequestHistory" defines disallowed public method "delay"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Actions/CreateServiceRequestHistory.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\ServiceManagement\\\\Actions\\\\CreateServiceRequestHistory" defines disallowed public method "delete"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Actions/CreateServiceRequestHistory.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\ServiceManagement\\\\Actions\\\\CreateServiceRequestHistory" defines disallowed public method "dispatch"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Actions/CreateServiceRequestHistory.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\ServiceManagement\\\\Actions\\\\CreateServiceRequestHistory" defines disallowed public method "dispatchAfterResponse"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Actions/CreateServiceRequestHistory.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\ServiceManagement\\\\Actions\\\\CreateServiceRequestHistory" defines disallowed public method "dispatchIf"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Actions/CreateServiceRequestHistory.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\ServiceManagement\\\\Actions\\\\CreateServiceRequestHistory" defines disallowed public method "dispatchNextJobInChain"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Actions/CreateServiceRequestHistory.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\ServiceManagement\\\\Actions\\\\CreateServiceRequestHistory" defines disallowed public method "dispatchSync"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Actions/CreateServiceRequestHistory.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\ServiceManagement\\\\Actions\\\\CreateServiceRequestHistory" defines disallowed public method "dispatchUnless"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Actions/CreateServiceRequestHistory.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\ServiceManagement\\\\Actions\\\\CreateServiceRequestHistory" defines disallowed public method "fail"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Actions/CreateServiceRequestHistory.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\ServiceManagement\\\\Actions\\\\CreateServiceRequestHistory" defines disallowed public method "handle"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Actions/CreateServiceRequestHistory.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\ServiceManagement\\\\Actions\\\\CreateServiceRequestHistory" defines disallowed public method "invokeChainCatchCallbacks"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Actions/CreateServiceRequestHistory.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\ServiceManagement\\\\Actions\\\\CreateServiceRequestHistory" defines disallowed public method "onConnection"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Actions/CreateServiceRequestHistory.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\ServiceManagement\\\\Actions\\\\CreateServiceRequestHistory" defines disallowed public method "onGroup"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Actions/CreateServiceRequestHistory.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\ServiceManagement\\\\Actions\\\\CreateServiceRequestHistory" defines disallowed public method "onQueue"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Actions/CreateServiceRequestHistory.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\ServiceManagement\\\\Actions\\\\CreateServiceRequestHistory" defines disallowed public method "prependToChain"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Actions/CreateServiceRequestHistory.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\ServiceManagement\\\\Actions\\\\CreateServiceRequestHistory" defines disallowed public method "release"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Actions/CreateServiceRequestHistory.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\ServiceManagement\\\\Actions\\\\CreateServiceRequestHistory" defines disallowed public method "restoreModel"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Actions/CreateServiceRequestHistory.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\ServiceManagement\\\\Actions\\\\CreateServiceRequestHistory" defines disallowed public method "setJob"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Actions/CreateServiceRequestHistory.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\ServiceManagement\\\\Actions\\\\CreateServiceRequestHistory" defines disallowed public method "through"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Actions/CreateServiceRequestHistory.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\ServiceManagement\\\\Actions\\\\CreateServiceRequestHistory" defines disallowed public method "withChain"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Actions/CreateServiceRequestHistory.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\ServiceManagement\\\\Actions\\\\CreateServiceRequestHistory" defines disallowed public method "withDeduplicator"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Actions/CreateServiceRequestHistory.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\ServiceManagement\\\\Actions\\\\CreateServiceRequestHistory" defines disallowed public method "withFakeQueueInteractions"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Actions/CreateServiceRequestHistory.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\ServiceManagement\\\\Actions\\\\CreateServiceRequestHistory" defines disallowed public method "withoutDelay"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Actions/CreateServiceRequestHistory.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\ServiceManagement\\\\Actions\\\\CreateServiceRequestHistory" must define a public "__invoke" method\\. Action classes should be accessed via invocation and keep other behavior in private/protected helpers\\.$#',
    'identifier' => 'Common.actionClassMustBeInvokable',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Actions/CreateServiceRequestHistory.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\ServiceManagement\\\\Actions\\\\CreateServiceRequestUpdateAction" defines disallowed public method "execute"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Actions/CreateServiceRequestUpdateAction.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\ServiceManagement\\\\Actions\\\\CreateServiceRequestUpdateAction" must define a public "__invoke" method\\. Action classes should be accessed via invocation and keep other behavior in private/protected helpers\\.$#',
    'identifier' => 'Common.actionClassMustBeInvokable',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Actions/CreateServiceRequestUpdateAction.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\ServiceManagement\\\\Actions\\\\DeclineServiceRequestConversation" defines disallowed public method "execute"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Actions/DeclineServiceRequestConversation.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\ServiceManagement\\\\Actions\\\\DeclineServiceRequestConversation" must define a public "__invoke" method\\. Action classes should be accessed via invocation and keep other behavior in private/protected helpers\\.$#',
    'identifier' => 'Common.actionClassMustBeInvokable',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Actions/DeclineServiceRequestConversation.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\ServiceManagement\\\\Actions\\\\EndServiceRequestConversation" defines disallowed public method "execute"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Actions/EndServiceRequestConversation.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\ServiceManagement\\\\Actions\\\\EndServiceRequestConversation" must define a public "__invoke" method\\. Action classes should be accessed via invocation and keep other behavior in private/protected helpers\\.$#',
    'identifier' => 'Common.actionClassMustBeInvokable',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Actions/EndServiceRequestConversation.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\ServiceManagement\\\\Actions\\\\GenerateServiceRequestFilamentFormSchema" defines disallowed public method "content"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Actions/GenerateServiceRequestFilamentFormSchema.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\ServiceManagement\\\\Actions\\\\GenerateServiceRequestFilamentFormSchema" defines disallowed public method "grid"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Actions/GenerateServiceRequestFilamentFormSchema.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property AidingApp\\\\Form\\\\Models\\\\Submissible\\:\\:\\$type\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Actions/GenerateServiceRequestFormKitSchema.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\ServiceManagement\\\\Actions\\\\GenerateServiceRequestFormKitSchema" defines disallowed public method "content"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Actions/GenerateServiceRequestFormKitSchema.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\ServiceManagement\\\\Actions\\\\GenerateServiceRequestFormKitSchema" defines disallowed public method "grid"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Actions/GenerateServiceRequestFormKitSchema.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\ServiceManagement\\\\Actions\\\\GenerateServiceRequestFormKitSchema" defines disallowed public method "text"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Actions/GenerateServiceRequestFormKitSchema.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\ServiceManagement\\\\Actions\\\\GenerateServiceRequestFormKitSchema" defines disallowed public method "wizardContent"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Actions/GenerateServiceRequestFormKitSchema.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\ServiceManagement\\\\Actions\\\\NotifyServiceRequestUsers" defines disallowed public method "execute"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Actions/NotifyServiceRequestUsers.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\ServiceManagement\\\\Actions\\\\NotifyServiceRequestUsers" must define a public "__invoke" method\\. Action classes should be accessed via invocation and keep other behavior in private/protected helpers\\.$#',
    'identifier' => 'Common.actionClassMustBeInvokable',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Actions/NotifyServiceRequestUsers.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\ServiceManagement\\\\Actions\\\\PreloadServiceRequestTypeBaseTemplates" defines disallowed public method "execute"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Actions/PreloadServiceRequestTypeBaseTemplates.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\ServiceManagement\\\\Actions\\\\PreloadServiceRequestTypeBaseTemplates" must define a public "__invoke" method\\. Action classes should be accessed via invocation and keep other behavior in private/protected helpers\\.$#',
    'identifier' => 'Common.actionClassMustBeInvokable',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Actions/PreloadServiceRequestTypeBaseTemplates.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\ServiceManagement\\\\Actions\\\\QueueServiceRequestConversation" defines disallowed public method "execute"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Actions/QueueServiceRequestConversation.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\ServiceManagement\\\\Actions\\\\QueueServiceRequestConversation" must define a public "__invoke" method\\. Action classes should be accessed via invocation and keep other behavior in private/protected helpers\\.$#',
    'identifier' => 'Common.actionClassMustBeInvokable',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Actions/QueueServiceRequestConversation.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\ServiceManagement\\\\Actions\\\\RecordServiceRequestStatusPeriod" defines disallowed public method "execute"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Actions/RecordServiceRequestStatusPeriod.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\ServiceManagement\\\\Actions\\\\RecordServiceRequestStatusPeriod" must define a public "__invoke" method\\. Action classes should be accessed via invocation and keep other behavior in private/protected helpers\\.$#',
    'identifier' => 'Common.actionClassMustBeInvokable',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Actions/RecordServiceRequestStatusPeriod.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\ServiceManagement\\\\Actions\\\\ReopenServiceRequestAction" defines disallowed public method "execute"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Actions/ReopenServiceRequestAction.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\ServiceManagement\\\\Actions\\\\ReopenServiceRequestAction" must define a public "__invoke" method\\. Action classes should be accessed via invocation and keep other behavior in private/protected helpers\\.$#',
    'identifier' => 'Common.actionClassMustBeInvokable',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Actions/ReopenServiceRequestAction.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\ServiceManagement\\\\Actions\\\\ResetMonitoringAction" defines disallowed public method "execute"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Actions/ResetMonitoringAction.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\ServiceManagement\\\\Actions\\\\ResetMonitoringAction" must define a public "__invoke" method\\. Action classes should be accessed via invocation and keep other behavior in private/protected helpers\\.$#',
    'identifier' => 'Common.actionClassMustBeInvokable',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Actions/ResetMonitoringAction.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Actions\\\\ResolveUploadsMediaCollectionForServiceRequest\\:\\:__invoke\\(\\) should return AidingApp\\\\ServiceManagement\\\\Models\\\\MediaCollections\\\\UploadsMediaCollection\\|null but returns Spatie\\\\MediaLibrary\\\\MediaCollections\\\\MediaCollection\\|null\\.$#',
    'identifier' => 'return.type',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Actions/ResolveUploadsMediaCollectionForServiceRequest.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\ServiceManagement\\\\Actions\\\\SubmitServiceRequestDraft" defines disallowed public method "execute"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Actions/SubmitServiceRequestDraft.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\ServiceManagement\\\\Actions\\\\SubmitServiceRequestDraft" must define a public "__invoke" method\\. Action classes should be accessed via invocation and keep other behavior in private/protected helpers\\.$#',
    'identifier' => 'Common.actionClassMustBeInvokable',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Actions/SubmitServiceRequestDraft.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\ServiceManagement\\\\Actions\\\\UpdateServiceRequestAction" defines disallowed public method "execute"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Actions/UpdateServiceRequestAction.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\ServiceManagement\\\\Actions\\\\UpdateServiceRequestAction" must define a public "__invoke" method\\. Action classes should be accessed via invocation and keep other behavior in private/protected helpers\\.$#',
    'identifier' => 'Common.actionClassMustBeInvokable',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Actions/UpdateServiceRequestAction.php',
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
    'message' => '#^Parameter name "close_details" is not in camelCase\\.$#',
    'identifier' => 'MeliorStan.parameterNameNotCamelCase',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/DataTransferObjects/ServiceRequestDataObject.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter name "priority_id" is not in camelCase\\.$#',
    'identifier' => 'MeliorStan.parameterNameNotCamelCase',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/DataTransferObjects/ServiceRequestDataObject.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter name "respondent_id" is not in camelCase\\.$#',
    'identifier' => 'MeliorStan.parameterNameNotCamelCase',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/DataTransferObjects/ServiceRequestDataObject.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter name "status_id" is not in camelCase\\.$#',
    'identifier' => 'MeliorStan.parameterNameNotCamelCase',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/DataTransferObjects/ServiceRequestDataObject.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter name "type_id" is not in camelCase\\.$#',
    'identifier' => 'MeliorStan.parameterNameNotCamelCase',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/DataTransferObjects/ServiceRequestDataObject.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Builder\\:\\:withTrashed\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ChangeRequests/Pages/EditChangeRequest.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Filament\\\\Resources\\\\Pages\\\\Page\\:\\:\\$record\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequestTypes/ServiceRequestTypeResource.php',
];
$ignoreErrors[] = [
    'message' => '#^Avoid strtolower\\(\\) as it is not multibyte\\-safe\\. Use Laravel\'s Str\\:\\:lower\\(\\) instead, or mb_strtolower\\(\\) if a framework\\-free alternative is required\\. If you are certain you specifically need strtolower\\(\\), add an inline ignore for this rule \\(// @phpstan\\-ignore Common\\.noStrtolower\\)\\.$#',
    'identifier' => 'Common.noStrtolower',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/service-management/src/Filament/Resources/ServiceRequests/Pages/ListServiceRequests.php',
];
$ignoreErrors[] = [
    'message' => '#^Avoid strtolower\\(\\) as it is not multibyte\\-safe\\. Use Laravel\'s Str\\:\\:lower\\(\\) instead, or mb_strtolower\\(\\) if a framework\\-free alternative is required\\. If you are certain you specifically need strtolower\\(\\), add an inline ignore for this rule \\(// @phpstan\\-ignore Common\\.noStrtolower\\)\\.$#',
    'identifier' => 'Common.noStrtolower',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/service-management/src/Filament/Widgets/ServiceRequestMediaTable.php',
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
    'message' => '#^Eloquent local scopes are not allowed\\. The "scopeOpen" method on "AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequest" defines a local scope, which relies on magic methods that static analysis cannot reason about\\. Use a tappable scope instead\\. See https\\://seankegel\\.com/elevate\\-your\\-laravel\\-eloquent\\-queries\\-with\\-tappable\\-scopes\\.$#',
    'identifier' => 'Common.noLocalModelScope',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequest.php',
];
$ignoreErrors[] = [
    'message' => '#^Return type \\(Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany\\<AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestFormField, \\$this\\(AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestForm\\)\\>\\) of method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestForm\\:\\:fields\\(\\) should be compatible with return type \\(Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany\\<AidingApp\\\\Form\\\\Models\\\\SubmissibleField, \\$this\\(AidingApp\\\\Form\\\\Models\\\\Submissible\\)\\>\\) of method AidingApp\\\\Form\\\\Models\\\\Submissible\\:\\:fields\\(\\)$#',
    'identifier' => 'method.childReturnType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestForm.php',
];
$ignoreErrors[] = [
    'message' => '#^Return type \\(Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany\\<AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestFormStep, \\$this\\(AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestForm\\)\\>\\) of method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestForm\\:\\:steps\\(\\) should be compatible with return type \\(Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany\\<AidingApp\\\\Form\\\\Models\\\\SubmissibleStep, \\$this\\(AidingApp\\\\Form\\\\Models\\\\Submissible\\)\\>\\) of method AidingApp\\\\Form\\\\Models\\\\Submissible\\:\\:steps\\(\\)$#',
    'identifier' => 'method.childReturnType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestForm.php',
];
$ignoreErrors[] = [
    'message' => '#^Return type \\(Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany\\<AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestFormSubmission, \\$this\\(AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestForm\\)\\>\\) of method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestForm\\:\\:submissions\\(\\) should be compatible with return type \\(Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany\\<AidingApp\\\\Form\\\\Models\\\\Submission, \\$this\\(AidingApp\\\\Form\\\\Models\\\\Submissible\\)\\>\\) of method AidingApp\\\\Form\\\\Models\\\\Submissible\\:\\:submissions\\(\\)$#',
    'identifier' => 'method.childReturnType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestForm.php',
];
$ignoreErrors[] = [
    'message' => '#^Eloquent models must define a "\\$fillable" property\\. Add a "\\$fillable" property to this model or a parent model\\.$#',
    'identifier' => 'Common.modelMissingFillable',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestFormAuthentication.php',
];
$ignoreErrors[] = [
    'message' => '#^Return type \\(Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo\\<AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestForm, \\$this\\(AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestFormAuthentication\\)\\>\\) of method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestFormAuthentication\\:\\:submissible\\(\\) should be compatible with return type \\(Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo\\<AidingApp\\\\Form\\\\Models\\\\Submissible, \\$this\\(AidingApp\\\\Form\\\\Models\\\\SubmissibleAuthentication\\)\\>\\) of method AidingApp\\\\Form\\\\Models\\\\SubmissibleAuthentication\\:\\:submissible\\(\\)$#',
    'identifier' => 'method.childReturnType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestFormAuthentication.php',
];
$ignoreErrors[] = [
    'message' => '#^Return type \\(Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo\\<AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestForm, \\$this\\(AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestFormField\\)\\>\\) of method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestFormField\\:\\:submissible\\(\\) should be compatible with return type \\(Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo\\<AidingApp\\\\Form\\\\Models\\\\Submissible, \\$this\\(AidingApp\\\\Form\\\\Models\\\\SubmissibleField\\)\\>\\) of method AidingApp\\\\Form\\\\Models\\\\SubmissibleField\\:\\:submissible\\(\\)$#',
    'identifier' => 'method.childReturnType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestFormField.php',
];
$ignoreErrors[] = [
    'message' => '#^Return type \\(Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo\\<AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestFormStep, \\$this\\(AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestFormField\\)\\>\\) of method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestFormField\\:\\:step\\(\\) should be compatible with return type \\(Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo\\<AidingApp\\\\Form\\\\Models\\\\SubmissibleStep, \\$this\\(AidingApp\\\\Form\\\\Models\\\\SubmissibleField\\)\\>\\) of method AidingApp\\\\Form\\\\Models\\\\SubmissibleField\\:\\:step\\(\\)$#',
    'identifier' => 'method.childReturnType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestFormField.php',
];
$ignoreErrors[] = [
    'message' => '#^Return type \\(Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo\\<AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestForm, \\$this\\(AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestFormStep\\)\\>\\) of method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestFormStep\\:\\:submissible\\(\\) should be compatible with return type \\(Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo\\<AidingApp\\\\Form\\\\Models\\\\Submissible, \\$this\\(AidingApp\\\\Form\\\\Models\\\\SubmissibleStep\\)\\>\\) of method AidingApp\\\\Form\\\\Models\\\\SubmissibleStep\\:\\:submissible\\(\\)$#',
    'identifier' => 'method.childReturnType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestFormStep.php',
];
$ignoreErrors[] = [
    'message' => '#^Return type \\(Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany\\<AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestFormField, \\$this\\(AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestFormStep\\)\\>\\) of method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestFormStep\\:\\:fields\\(\\) should be compatible with return type \\(Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasMany\\<AidingApp\\\\Form\\\\Models\\\\SubmissibleField, \\$this\\(AidingApp\\\\Form\\\\Models\\\\SubmissibleStep\\)\\>\\) of method AidingApp\\\\Form\\\\Models\\\\SubmissibleStep\\:\\:fields\\(\\)$#',
    'identifier' => 'method.childReturnType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestFormStep.php',
];
$ignoreErrors[] = [
    'message' => '#^Eloquent local scopes are not allowed\\. The "scopeCanceled" method on "AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestFormSubmission" defines a local scope, which relies on magic methods that static analysis cannot reason about\\. Use a tappable scope instead\\. See https\\://seankegel\\.com/elevate\\-your\\-laravel\\-eloquent\\-queries\\-with\\-tappable\\-scopes\\.$#',
    'identifier' => 'Common.noLocalModelScope',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestFormSubmission.php',
];
$ignoreErrors[] = [
    'message' => '#^Eloquent local scopes are not allowed\\. The "scopeNotCanceled" method on "AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestFormSubmission" defines a local scope, which relies on magic methods that static analysis cannot reason about\\. Use a tappable scope instead\\. See https\\://seankegel\\.com/elevate\\-your\\-laravel\\-eloquent\\-queries\\-with\\-tappable\\-scopes\\.$#',
    'identifier' => 'Common.noLocalModelScope',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestFormSubmission.php',
];
$ignoreErrors[] = [
    'message' => '#^Eloquent local scopes are not allowed\\. The "scopeNotSubmitted" method on "AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestFormSubmission" defines a local scope, which relies on magic methods that static analysis cannot reason about\\. Use a tappable scope instead\\. See https\\://seankegel\\.com/elevate\\-your\\-laravel\\-eloquent\\-queries\\-with\\-tappable\\-scopes\\.$#',
    'identifier' => 'Common.noLocalModelScope',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestFormSubmission.php',
];
$ignoreErrors[] = [
    'message' => '#^Eloquent local scopes are not allowed\\. The "scopeRequested" method on "AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestFormSubmission" defines a local scope, which relies on magic methods that static analysis cannot reason about\\. Use a tappable scope instead\\. See https\\://seankegel\\.com/elevate\\-your\\-laravel\\-eloquent\\-queries\\-with\\-tappable\\-scopes\\.$#',
    'identifier' => 'Common.noLocalModelScope',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestFormSubmission.php',
];
$ignoreErrors[] = [
    'message' => '#^Eloquent local scopes are not allowed\\. The "scopeSubmitted" method on "AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestFormSubmission" defines a local scope, which relies on magic methods that static analysis cannot reason about\\. Use a tappable scope instead\\. See https\\://seankegel\\.com/elevate\\-your\\-laravel\\-eloquent\\-queries\\-with\\-tappable\\-scopes\\.$#',
    'identifier' => 'Common.noLocalModelScope',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestFormSubmission.php',
];
$ignoreErrors[] = [
    'message' => '#^Return type \\(Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo\\<AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestForm, \\$this\\(AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestFormSubmission\\)\\>\\) of method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestFormSubmission\\:\\:submissible\\(\\) should be compatible with return type \\(Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsTo\\<AidingApp\\\\Form\\\\Models\\\\Submissible, \\$this\\(AidingApp\\\\Form\\\\Models\\\\Submission\\)\\>\\) of method AidingApp\\\\Form\\\\Models\\\\Submission\\:\\:submissible\\(\\)$#',
    'identifier' => 'method.childReturnType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestFormSubmission.php',
];
$ignoreErrors[] = [
    'message' => '#^Return type \\(Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestFormField, \\$this\\(AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestFormSubmission\\), Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\Pivot, \'pivot\'\\>\\) of method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestFormSubmission\\:\\:fields\\(\\) should be compatible with return type \\(Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<AidingApp\\\\Form\\\\Models\\\\SubmissibleField, \\$this\\(AidingApp\\\\Form\\\\Models\\\\Submission\\), Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\Pivot, \'pivot\'\\>\\) of method AidingApp\\\\Form\\\\Models\\\\Submission\\:\\:fields\\(\\)$#',
    'identifier' => 'method.childReturnType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestFormSubmission.php',
];
$ignoreErrors[] = [
    'message' => '#^Method AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestType\\:\\:serviceRequests\\(\\) return type with generic class Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\HasManyThrough does not specify its types\\: TRelatedModel, TIntermediateModel, TDeclaringModel$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Models/ServiceRequestType.php',
];
$ignoreErrors[] = [
    'message' => '#^PHPDoc tag @param for parameter \\$fail with type Illuminate\\\\Translation\\\\PotentiallyTranslatedString is incompatible with native type Closure\\.$#',
    'identifier' => 'parameter.phpDocType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Rules/ManagedServiceRequestType.php',
];
$ignoreErrors[] = [
    'message' => '#^Class name "ServiceRequestTypeAssignmentsIndividualUserMustBeAManager" is not in PascalCase\\.$#',
    'identifier' => 'MeliorStan.classNameNotPascalCase',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/src/Rules/ServiceRequestTypeAssignmentsIndividualUserMustBeAManager.php',
];
$ignoreErrors[] = [
    'message' => '#^Function serviceRequestTypeTemplatesDoc\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/tests/Tenant/Filament/Clusters/ServiceManagementAdministration/Pages/ServiceRequestTypeTemplatesTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$data of anonymous function has no typehint\\.$#',
    'identifier' => 'MeliorStan.closureParameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/tests/Tenant/Filament/Resources/Advisories/Pages/ListAdvisoriesTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$errors of anonymous function has no typehint\\.$#',
    'identifier' => 'MeliorStan.closureParameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/tests/Tenant/Filament/Resources/Advisories/Pages/ListAdvisoriesTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Pest\\\\Mixins\\\\Expectation\\<array\\<int, string\\>\\|string\\>\\|Pest\\\\Mixins\\\\Expectation\\<Pest\\\\Expectation\\<array\\<int, string\\>\\|string\\>\\>\\:\\:\\$description\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/tests/Tenant/Filament/Resources/Advisories/Pages/ViewAdvisoryTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Pest\\\\Mixins\\\\Expectation\\<array\\<int, string\\>\\|string\\>\\|Pest\\\\Mixins\\\\Expectation\\<Pest\\\\Expectation\\<array\\<int, string\\>\\|string\\>\\>\\:\\:\\$status_id\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/tests/Tenant/Filament/Resources/Advisories/Pages/ViewAdvisoryTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Avoid assertSee\\(\\) and its alternatives\\: they only prove the text appears somewhere in the rendered output, not that it appears where the test actually expects it, so they can pass even when the feature under test is broken\\. Prefer a more precise assertion \\(e\\.g\\. asserting component/view state, a scoped selector, or the specific structure produced\\)\\. If you are certain you specifically need this assertion, add an inline ignore for this rule \\(// @phpstan\\-ignore Common\\.noAssertSee\\)\\.$#',
    'identifier' => 'Common.noAssertSee',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/service-management/tests/Tenant/Filament/Resources/Advisories/Pages/ViewAdvisoryTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$overrides of anonymous function has no typehint\\.$#',
    'identifier' => 'MeliorStan.closureParameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/tests/Tenant/Filament/Resources/Advisories/Pages/ViewAdvisoryTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$errors of anonymous function has no typehint\\.$#',
    'identifier' => 'MeliorStan.closureParameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/tests/Tenant/Filament/Resources/Advisories/Pages/ViewAdvisoryTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Pest\\\\Mixins\\\\Expectation\\<array\\<int, string\\>\\|string\\>\\|Pest\\\\Mixins\\\\Expectation\\<Pest\\\\Expectation\\<array\\<int, string\\>\\|string\\>\\>\\:\\:\\$update\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/tests/Tenant/Filament/Resources/Advisories/RelationManagers/AdvisoryUpdatesRelationManagerTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$data of anonymous function has no typehint\\.$#',
    'identifier' => 'MeliorStan.closureParameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/tests/Tenant/Filament/Resources/AdvisorySeverities/Pages/CreateAdvisorySeverityTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$errors of anonymous function has no typehint\\.$#',
    'identifier' => 'MeliorStan.closureParameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/tests/Tenant/Filament/Resources/AdvisorySeverities/Pages/CreateAdvisorySeverityTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$data of anonymous function has no typehint\\.$#',
    'identifier' => 'MeliorStan.closureParameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/tests/Tenant/Filament/Resources/AdvisorySeverities/Pages/EditAdvisorySeverityTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$errors of anonymous function has no typehint\\.$#',
    'identifier' => 'MeliorStan.closureParameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/tests/Tenant/Filament/Resources/AdvisorySeverities/Pages/EditAdvisorySeverityTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property AidingApp\\\\ServiceManagement\\\\Models\\\\AdvisorySeverity\\:\\:\\$getRgb\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/tests/Tenant/Filament/Resources/AdvisorySeverities/Pages/ViewAdvisorySeverityTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Avoid assertSee\\(\\) and its alternatives\\: they only prove the text appears somewhere in the rendered output, not that it appears where the test actually expects it, so they can pass even when the feature under test is broken\\. Prefer a more precise assertion \\(e\\.g\\. asserting component/view state, a scoped selector, or the specific structure produced\\)\\. If you are certain you specifically need this assertion, add an inline ignore for this rule \\(// @phpstan\\-ignore Common\\.noAssertSee\\)\\.$#',
    'identifier' => 'Common.noAssertSee',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/tests/Tenant/Filament/Resources/AdvisorySeverities/Pages/ViewAdvisorySeverityTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$data of anonymous function has no typehint\\.$#',
    'identifier' => 'MeliorStan.closureParameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/tests/Tenant/Filament/Resources/AdvisoryStatuses/Pages/CreateAdvisoryStatusTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$errors of anonymous function has no typehint\\.$#',
    'identifier' => 'MeliorStan.closureParameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/tests/Tenant/Filament/Resources/AdvisoryStatuses/Pages/CreateAdvisoryStatusTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$data of anonymous function has no typehint\\.$#',
    'identifier' => 'MeliorStan.closureParameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/tests/Tenant/Filament/Resources/AdvisoryStatuses/Pages/EditAdvisoryStatusTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$errors of anonymous function has no typehint\\.$#',
    'identifier' => 'MeliorStan.closureParameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/tests/Tenant/Filament/Resources/AdvisoryStatuses/Pages/EditAdvisoryStatusTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Avoid assertSee\\(\\) and its alternatives\\: they only prove the text appears somewhere in the rendered output, not that it appears where the test actually expects it, so they can pass even when the feature under test is broken\\. Prefer a more precise assertion \\(e\\.g\\. asserting component/view state, a scoped selector, or the specific structure produced\\)\\. If you are certain you specifically need this assertion, add an inline ignore for this rule \\(// @phpstan\\-ignore Common\\.noAssertSee\\)\\.$#',
    'identifier' => 'Common.noAssertSee',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/tests/Tenant/Filament/Resources/AdvisoryStatuses/Pages/ViewAdvisoryStatusTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Using nullsafe method call on non\\-nullable type AidingApp\\\\ServiceManagement\\\\Models\\\\ChangeRequest\\. Use \\-\\> instead\\.$#',
    'identifier' => 'nullsafe.neverNull',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/tests/Tenant/Filament/Resources/ChangeRequests/Pages/CreateChangeRequestTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Using nullsafe method call on non\\-nullable type Illuminate\\\\Support\\\\Carbon\\. Use \\-\\> instead\\.$#',
    'identifier' => 'nullsafe.neverNull',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/service-management/tests/Tenant/Filament/Resources/ChangeRequests/Pages/EditChangeRequestTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Pest\\\\Expectation\\<Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<AidingApp\\\\Department\\\\Models\\\\Department, AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceMonitoringTarget, covariant AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceMonitoringTargetDepartment, string\\>\\|null\\>\\:\\:exists\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/service-management/tests/Tenant/Filament/Resources/ServiceMonitorings/Pages/CreateServiceMonitoringTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Pest\\\\Expectation\\<Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\BelongsToMany\\<App\\\\Models\\\\User, AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceMonitoringTarget, covariant AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceMonitoringTargetUser, string\\>\\|null\\>\\:\\:exists\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/service-management/tests/Tenant/Filament/Resources/ServiceMonitorings/Pages/CreateServiceMonitoringTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$data of anonymous function has no typehint\\.$#',
    'identifier' => 'MeliorStan.closureParameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/tests/Tenant/Filament/Resources/ServiceMonitorings/Pages/CreateServiceMonitoringTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$errors of anonymous function has no typehint\\.$#',
    'identifier' => 'MeliorStan.closureParameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/tests/Tenant/Filament/Resources/ServiceMonitorings/Pages/CreateServiceMonitoringTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$data of anonymous function has no typehint\\.$#',
    'identifier' => 'MeliorStan.closureParameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/tests/Tenant/Filament/Resources/ServiceMonitorings/Pages/EditServiceMonitoringTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$errors of anonymous function has no typehint\\.$#',
    'identifier' => 'MeliorStan.closureParameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/tests/Tenant/Filament/Resources/ServiceMonitorings/Pages/EditServiceMonitoringTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Avoid assertSee\\(\\) and its alternatives\\: they only prove the text appears somewhere in the rendered output, not that it appears where the test actually expects it, so they can pass even when the feature under test is broken\\. Prefer a more precise assertion \\(e\\.g\\. asserting component/view state, a scoped selector, or the specific structure produced\\)\\. If you are certain you specifically need this assertion, add an inline ignore for this rule \\(// @phpstan\\-ignore Common\\.noAssertSee\\)\\.$#',
    'identifier' => 'Common.noAssertSee',
    'count' => 19,
    'path' => __DIR__ . '/app-modules/service-management/tests/Tenant/Filament/Resources/ServiceMonitorings/Pages/ViewServiceMonitoringTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$data of anonymous function has no typehint\\.$#',
    'identifier' => 'MeliorStan.closureParameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/tests/Tenant/Filament/Resources/ServiceRequestStatuses/Pages/CreateServiceRequestStatusTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$errors of anonymous function has no typehint\\.$#',
    'identifier' => 'MeliorStan.closureParameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/tests/Tenant/Filament/Resources/ServiceRequestStatuses/Pages/CreateServiceRequestStatusTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$data of anonymous function has no typehint\\.$#',
    'identifier' => 'MeliorStan.closureParameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/tests/Tenant/Filament/Resources/ServiceRequestStatuses/Pages/EditServiceRequestStatusTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$errors of anonymous function has no typehint\\.$#',
    'identifier' => 'MeliorStan.closureParameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/tests/Tenant/Filament/Resources/ServiceRequestStatuses/Pages/EditServiceRequestStatusTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Anonymous function should return Pest\\\\Mixins\\\\Expectation\\<bool\\|null\\> but returns Pest\\\\Mixins\\\\Expectation\\<bool\\|null\\>\\.$#',
    'identifier' => 'return.type',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/tests/Tenant/Filament/Resources/ServiceRequestStatuses/Pages/ListServiceRequestStatusesTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Pest\\\\PendingCalls\\\\TestCall\\:\\:travel\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/tests/Tenant/Filament/Resources/ServiceRequestStatuses/Pages/ListServiceRequestStatusesTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Avoid assertSee\\(\\) and its alternatives\\: they only prove the text appears somewhere in the rendered output, not that it appears where the test actually expects it, so they can pass even when the feature under test is broken\\. Prefer a more precise assertion \\(e\\.g\\. asserting component/view state, a scoped selector, or the specific structure produced\\)\\. If you are certain you specifically need this assertion, add an inline ignore for this rule \\(// @phpstan\\-ignore Common\\.noAssertSee\\)\\.$#',
    'identifier' => 'Common.noAssertSee',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/tests/Tenant/Filament/Resources/ServiceRequestStatuses/Pages/ViewServiceRequestStatusTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$values of method Illuminate\\\\Testing\\\\TestResponse\\<Symfony\\\\Component\\\\HttpFoundation\\\\Response\\>\\:\\:assertSeeTextInOrder\\(\\) expects list\\<string\\>, array\\{\'Name\', string, \'Color\', string, \'Sort Order\', int\\} given\\.$#',
    'identifier' => 'argument.type',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/tests/Tenant/Filament/Resources/ServiceRequestStatuses/Pages/ViewServiceRequestStatusTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$data of anonymous function has no typehint\\.$#',
    'identifier' => 'MeliorStan.closureParameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/tests/Tenant/Filament/Resources/ServiceRequestTypes/Pages/CreateServiceRequestTypeTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$errors of anonymous function has no typehint\\.$#',
    'identifier' => 'MeliorStan.closureParameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/tests/Tenant/Filament/Resources/ServiceRequestTypes/Pages/CreateServiceRequestTypeTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$errors of anonymous function has no typehint\\.$#',
    'identifier' => 'MeliorStan.closureParameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/tests/Tenant/Filament/Resources/ServiceRequestTypes/Pages/EditServiceRequestTypeAssignmentsTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Function allNotificationSettings\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/tests/Tenant/Filament/Resources/ServiceRequestTypes/Pages/EditServiceRequestTypeNotificationsTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$data of anonymous function has no typehint\\.$#',
    'identifier' => 'MeliorStan.closureParameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/tests/Tenant/Filament/Resources/ServiceRequestTypes/Pages/EditServiceRequestTypeTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$errors of anonymous function has no typehint\\.$#',
    'identifier' => 'MeliorStan.closureParameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/tests/Tenant/Filament/Resources/ServiceRequestTypes/Pages/EditServiceRequestTypeTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Unable to resolve the template type TKey in call to function collect$#',
    'identifier' => 'argument.templateType',
    'count' => 5,
    'path' => __DIR__ . '/app-modules/service-management/tests/Tenant/Filament/Resources/ServiceRequestTypes/Pages/ListServiceRequestTypeTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Unable to resolve the template type TValue in call to function collect$#',
    'identifier' => 'argument.templateType',
    'count' => 5,
    'path' => __DIR__ . '/app-modules/service-management/tests/Tenant/Filament/Resources/ServiceRequestTypes/Pages/ListServiceRequestTypeTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Variable name "\\$categoryAData" is not in camelCase\\.$#',
    'identifier' => 'MeliorStan.variableNameNotCamelCase',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/tests/Tenant/Filament/Resources/ServiceRequestTypes/Pages/ListServiceRequestTypeTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Variable name "\\$categoryBData" is not in camelCase\\.$#',
    'identifier' => 'MeliorStan.variableNameNotCamelCase',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/tests/Tenant/Filament/Resources/ServiceRequestTypes/Pages/ListServiceRequestTypeTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Unable to resolve the template type TKey in call to function collect$#',
    'identifier' => 'argument.templateType',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/service-management/tests/Tenant/Filament/Resources/ServiceRequestTypes/Pages/ListServiceRequestTypesMultipleCategoriesTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Unable to resolve the template type TValue in call to function collect$#',
    'identifier' => 'argument.templateType',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/service-management/tests/Tenant/Filament/Resources/ServiceRequestTypes/Pages/ListServiceRequestTypesMultipleCategoriesTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\Pivot\\:\\:\\$response\\.$#',
    'identifier' => 'property.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/tests/Tenant/Filament/Resources/ServiceRequestTypes/Pages/ManageServiceRequestTypeCustomFormTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Function customFormContent\\(\\) return type has no value type specified in iterable type array\\.$#',
    'identifier' => 'missingType.iterableValue',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/tests/Tenant/Filament/Resources/ServiceRequestTypes/Pages/ManageServiceRequestTypeCustomFormTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Unable to resolve the template type TAndValue in call to method Pest\\\\Expectation\\<mixed\\>\\:\\:and\\(\\)$#',
    'identifier' => 'argument.templateType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/tests/Tenant/Filament/Resources/ServiceRequestTypes/Pages/ManageServiceRequestTypeCustomFormTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Avoid assertSee\\(\\) and its alternatives\\: they only prove the text appears somewhere in the rendered output, not that it appears where the test actually expects it, so they can pass even when the feature under test is broken\\. Prefer a more precise assertion \\(e\\.g\\. asserting component/view state, a scoped selector, or the specific structure produced\\)\\. If you are certain you specifically need this assertion, add an inline ignore for this rule \\(// @phpstan\\-ignore Common\\.noAssertSee\\)\\.$#',
    'identifier' => 'Common.noAssertSee',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/tests/Tenant/Filament/Resources/ServiceRequestTypes/Pages/ViewServiceRequestTypeTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$data of anonymous function has no typehint\\.$#',
    'identifier' => 'MeliorStan.closureParameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/tests/Tenant/Filament/Resources/ServiceRequestUpdates/Pages/EditServiceRequestUpdateTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$errors of anonymous function has no typehint\\.$#',
    'identifier' => 'MeliorStan.closureParameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/tests/Tenant/Filament/Resources/ServiceRequestUpdates/Pages/EditServiceRequestUpdateTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Avoid assertSee\\(\\) and its alternatives\\: they only prove the text appears somewhere in the rendered output, not that it appears where the test actually expects it, so they can pass even when the feature under test is broken\\. Prefer a more precise assertion \\(e\\.g\\. asserting component/view state, a scoped selector, or the specific structure produced\\)\\. If you are certain you specifically need this assertion, add an inline ignore for this rule \\(// @phpstan\\-ignore Common\\.noAssertSee\\)\\.$#',
    'identifier' => 'Common.noAssertSee',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/tests/Tenant/Filament/Resources/ServiceRequestUpdates/Pages/ViewServiceRequestUpdateTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Anonymous function should return Pest\\\\Mixins\\\\Expectation\\<string\\|null\\> but returns Pest\\\\Mixins\\\\Expectation\\<string\\|null\\>\\.$#',
    'identifier' => 'return.type',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/service-management/tests/Tenant/Filament/Resources/ServiceRequests/Actions/ChangeServiceRequestStatusBulkActionTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Variable name "\\$sr" is shorter than minimum length of 3 characters\\.$#',
    'identifier' => 'MeliorStan.shortVariable',
    'count' => 4,
    'path' => __DIR__ . '/app-modules/service-management/tests/Tenant/Filament/Resources/ServiceRequests/Actions/DeleteServiceRequestBulkActionTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$field of anonymous function has no typehint\\.$#',
    'identifier' => 'MeliorStan.closureParameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/tests/Tenant/Filament/Resources/ServiceRequests/Actions/ReclassifyServiceRequestActionTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$tree of anonymous function has no typehint\\.$#',
    'identifier' => 'MeliorStan.closureParameterMissingTypehint',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/service-management/tests/Tenant/Filament/Resources/ServiceRequests/Actions/ReclassifyServiceRequestActionTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to function assert\\(\\) with true will always evaluate to true\\.$#',
    'identifier' => 'function.alreadyNarrowedType',
    'count' => 7,
    'path' => __DIR__ . '/app-modules/service-management/tests/Tenant/Filament/Resources/ServiceRequests/Actions/ViewServiceRequestContactActionTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Function mountViewContact\\(\\) has no return type specified\\.$#',
    'identifier' => 'missingType.return',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/tests/Tenant/Filament/Resources/ServiceRequests/Actions/ViewServiceRequestContactActionTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Instanceof between AidingApp\\\\Contact\\\\Models\\\\Contact and AidingApp\\\\Contact\\\\Models\\\\Contact will always evaluate to true\\.$#',
    'identifier' => 'instanceof.alwaysTrue',
    'count' => 7,
    'path' => __DIR__ . '/app-modules/service-management/tests/Tenant/Filament/Resources/ServiceRequests/Actions/ViewServiceRequestContactActionTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Relations\\\\Pivot\\:\\:\\$response\\.$#',
    'identifier' => 'property.notFound',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/service-management/tests/Tenant/Filament/Resources/ServiceRequests/Pages/CreateServiceRequestTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$data of anonymous function has no typehint\\.$#',
    'identifier' => 'MeliorStan.closureParameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/tests/Tenant/Filament/Resources/ServiceRequests/Pages/CreateServiceRequestTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$serviceRequest of anonymous function has no typehint\\.$#',
    'identifier' => 'MeliorStan.closureParameterMissingTypehint',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/service-management/tests/Tenant/Filament/Resources/ServiceRequests/Pages/CreateServiceRequestTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$tree of anonymous function has no typehint\\.$#',
    'identifier' => 'MeliorStan.closureParameterMissingTypehint',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/service-management/tests/Tenant/Filament/Resources/ServiceRequests/Pages/CreateServiceRequestTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#2 \\$errors of anonymous function has no typehint\\.$#',
    'identifier' => 'MeliorStan.closureParameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/tests/Tenant/Filament/Resources/ServiceRequests/Pages/CreateServiceRequestTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#3 \\$setup of anonymous function has no typehint\\.$#',
    'identifier' => 'MeliorStan.closureParameterMissingTypehint',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/tests/Tenant/Filament/Resources/ServiceRequests/Pages/CreateServiceRequestTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Property AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestForm\\:\\:\\$content \\(never\\) does not accept array\\<string, list\\<array\\<string, array\\<string, array\\<string, mixed\\>\\|string\\>\\|string\\>\\>\\|string\\>\\.$#',
    'identifier' => 'assign.propertyType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/tests/Tenant/Filament/Resources/ServiceRequests/Pages/CreateServiceRequestTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Unable to resolve the template type TAndValue in call to method Pest\\\\Expectation\\<bool\\|null\\>\\:\\:and\\(\\)$#',
    'identifier' => 'argument.templateType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/tests/Tenant/Filament/Resources/ServiceRequests/Pages/CreateServiceRequestTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Unable to resolve the template type TAndValue in call to method Pest\\\\Expectation\\<mixed\\>\\:\\:and\\(\\)$#',
    'identifier' => 'argument.templateType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/tests/Tenant/Filament/Resources/ServiceRequests/Pages/CreateServiceRequestTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Access to an undefined property Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:\\$service_request_number\\.$#',
    'identifier' => 'property.notFound',
    'count' => 6,
    'path' => __DIR__ . '/app-modules/service-management/tests/Tenant/Filament/Resources/ServiceRequests/Pages/GlobalSearchServiceRequestTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Unable to resolve the template type TValue in call to function expect$#',
    'identifier' => 'argument.templateType',
    'count' => 6,
    'path' => __DIR__ . '/app-modules/service-management/tests/Tenant/Filament/Resources/ServiceRequests/Pages/GlobalSearchServiceRequestTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Avoid assertSee\\(\\) and its alternatives\\: they only prove the text appears somewhere in the rendered output, not that it appears where the test actually expects it, so they can pass even when the feature under test is broken\\. Prefer a more precise assertion \\(e\\.g\\. asserting component/view state, a scoped selector, or the specific structure produced\\)\\. If you are certain you specifically need this assertion, add an inline ignore for this rule \\(// @phpstan\\-ignore Common\\.noAssertSee\\)\\.$#',
    'identifier' => 'Common.noAssertSee',
    'count' => 37,
    'path' => __DIR__ . '/app-modules/service-management/tests/Tenant/Filament/Resources/ServiceRequests/Pages/ViewServiceRequestTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Pest\\\\Mixins\\\\Expectation\\<mixed\\>\\:\\:isMarkdown\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/tests/Tenant/Filament/Resources/ServiceRequests/Pages/ViewServiceRequestTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Property AidingApp\\\\ServiceManagement\\\\Models\\\\ServiceRequestForm\\:\\:\\$content \\(never\\) does not accept array\\<string, list\\<array\\<string, array\\<string, array\\<string, mixed\\>\\|string\\>\\|string\\>\\>\\|string\\>\\.$#',
    'identifier' => 'assign.propertyType',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/service-management/tests/Tenant/Filament/Resources/ServiceRequests/Pages/ViewServiceRequestTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Avoid assertSee\\(\\) and its alternatives\\: they only prove the text appears somewhere in the rendered output, not that it appears where the test actually expects it, so they can pass even when the feature under test is broken\\. Prefer a more precise assertion \\(e\\.g\\. asserting component/view state, a scoped selector, or the specific structure produced\\)\\. If you are certain you specifically need this assertion, add an inline ignore for this rule \\(// @phpstan\\-ignore Common\\.noAssertSee\\)\\.$#',
    'identifier' => 'Common.noAssertSee',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/tests/Tenant/Filament/Resources/ServiceRequests/RelationManagers/ServiceRequestConversationsRelationManagerTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Avoid assertSee\\(\\) and its alternatives\\: they only prove the text appears somewhere in the rendered output, not that it appears where the test actually expects it, so they can pass even when the feature under test is broken\\. Prefer a more precise assertion \\(e\\.g\\. asserting component/view state, a scoped selector, or the specific structure produced\\)\\. If you are certain you specifically need this assertion, add an inline ignore for this rule \\(// @phpstan\\-ignore Common\\.noAssertSee\\)\\.$#',
    'identifier' => 'Common.noAssertSee',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/service-management/tests/Tenant/Filament/Sequences/Pages/SequenceIndexTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Avoid assertSee\\(\\) and its alternatives\\: they only prove the text appears somewhere in the rendered output, not that it appears where the test actually expects it, so they can pass even when the feature under test is broken\\. Prefer a more precise assertion \\(e\\.g\\. asserting component/view state, a scoped selector, or the specific structure produced\\)\\. If you are certain you specifically need this assertion, add an inline ignore for this rule \\(// @phpstan\\-ignore Common\\.noAssertSee\\)\\.$#',
    'identifier' => 'Common.noAssertSee',
    'count' => 2,
    'path' => __DIR__ . '/app-modules/service-management/tests/Tenant/Filament/Widgets/ServiceRequestMediaTableTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter name "\\$m" is shorter than minimum length of 3 characters\\.$#',
    'identifier' => 'MeliorStan.shortVariable',
    'count' => 8,
    'path' => __DIR__ . '/app-modules/service-management/tests/Tenant/Filament/Widgets/ServiceRequestMediaTableTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Spatie\\\\Image\\\\Drivers\\\\ImageDriver\\:\\:keepOriginalImageFormat\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/theme/src/Settings/SettingsProperties/ThemeSettingsProperty.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\Timeline\\\\Actions\\\\AggregatesTimelineRecordsForModel" defines disallowed public method "handle"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/timeline/src/Actions/AggregatesTimelineRecordsForModel.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\Timeline\\\\Actions\\\\AggregatesTimelineRecordsForModel" must define a public "__invoke" method\\. Action classes should be accessed via invocation and keep other behavior in private/protected helpers\\.$#',
    'identifier' => 'Common.actionClassMustBeInvokable',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/timeline/src/Actions/AggregatesTimelineRecordsForModel.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\Timeline\\\\Actions\\\\SyncTimelineData" defines disallowed public method "now"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/timeline/src/Actions/SyncTimelineData.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\Timeline\\\\Actions\\\\SyncTimelineData" must define a public "__invoke" method\\. Action classes should be accessed via invocation and keep other behavior in private/protected helpers\\.$#',
    'identifier' => 'Common.actionClassMustBeInvokable',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/timeline/src/Actions/SyncTimelineData.php',
];
$ignoreErrors[] = [
    'message' => '#^Class name "ModelMustHaveATimeline" is not in PascalCase\\.$#',
    'identifier' => 'MeliorStan.classNameNotPascalCase',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/timeline/src/Exceptions/ModelMustHaveATimeline.php',
];
$ignoreErrors[] = [
    'message' => '#^Eloquent local scopes are not allowed\\. The "scopeForEntity" method on "AidingApp\\\\Timeline\\\\Models\\\\Timeline" defines a local scope, which relies on magic methods that static analysis cannot reason about\\. Use a tappable scope instead\\. See https\\://seankegel\\.com/elevate\\-your\\-laravel\\-eloquent\\-queries\\-with\\-tappable\\-scopes\\.$#',
    'identifier' => 'Common.noLocalModelScope',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/timeline/src/Models/Timeline.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:engagementResponses\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/timeline/tests/Tenant/Listeners/AddRecordToTimelineTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Illuminate\\\\Database\\\\Eloquent\\\\Model\\:\\:engagementResponses\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/timeline/tests/Tenant/Listeners/RemoveRecordFromTimelineTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Avoid assertSee\\(\\) and its alternatives\\: they only prove the text appears somewhere in the rendered output, not that it appears where the test actually expects it, so they can pass even when the feature under test is broken\\. Prefer a more precise assertion \\(e\\.g\\. asserting component/view state, a scoped selector, or the specific structure produced\\)\\. If you are certain you specifically need this assertion, add an inline ignore for this rule \\(// @phpstan\\-ignore Common\\.noAssertSee\\)\\.$#',
    'identifier' => 'Common.noAssertSee',
    'count' => 6,
    'path' => __DIR__ . '/app-modules/timeline/tests/Tenant/Livewire/TimelineListTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Unable to resolve the template type TAndValue in call to method Pest\\\\Expectation\\<mixed\\>\\:\\:and\\(\\)$#',
    'identifier' => 'argument.templateType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/timeline/tests/Tenant/Livewire/TimelineListTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Unable to resolve the template type TValue in call to function expect$#',
    'identifier' => 'argument.templateType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/timeline/tests/Tenant/Livewire/TimelineListTest.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\Webhook\\\\Actions\\\\StoreInboundWebhook" defines disallowed public method "handle"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/webhook/src/Actions/StoreInboundWebhook.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "AidingApp\\\\Webhook\\\\Actions\\\\StoreInboundWebhook" must define a public "__invoke" method\\. Action classes should be accessed via invocation and keep other behavior in private/protected helpers\\.$#',
    'identifier' => 'Common.actionClassMustBeInvokable',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/webhook/src/Actions/StoreInboundWebhook.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to function is_array\\(\\) with string will always evaluate to false\\.$#',
    'identifier' => 'function.impossibleType',
    'count' => 1,
    'path' => __DIR__ . '/app-modules/webhook/src/Http/Middleware/HandleAwsSnsRequest.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "App\\\\Actions\\\\CreateUserAction" defines disallowed public method "execute"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app/Actions/CreateUserAction.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "App\\\\Actions\\\\CreateUserAction" must define a public "__invoke" method\\. Action classes should be accessed via invocation and keep other behavior in private/protected helpers\\.$#',
    'identifier' => 'Common.actionClassMustBeInvokable',
    'count' => 1,
    'path' => __DIR__ . '/app/Actions/CreateUserAction.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "App\\\\Actions\\\\Finders\\\\ApplicationModels" defines disallowed public method "all"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app/Actions/Finders/ApplicationModels.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "App\\\\Actions\\\\Finders\\\\ApplicationModels" must define a public "__invoke" method\\. Action classes should be accessed via invocation and keep other behavior in private/protected helpers\\.$#',
    'identifier' => 'Common.actionClassMustBeInvokable',
    'count' => 1,
    'path' => __DIR__ . '/app/Actions/Finders/ApplicationModels.php',
];
$ignoreErrors[] = [
    'message' => '#^Call to an undefined method Illuminate\\\\Container\\\\Container\\:\\:getNamespace\\(\\)\\.$#',
    'identifier' => 'method.notFound',
    'count' => 1,
    'path' => __DIR__ . '/app/Actions/Finders/ApplicationModels.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "App\\\\Actions\\\\Finders\\\\ApplicationModules" defines disallowed public method "moduleConfig"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app/Actions/Finders/ApplicationModules.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "App\\\\Actions\\\\Finders\\\\ApplicationModules" must define a public "__invoke" method\\. Action classes should be accessed via invocation and keep other behavior in private/protected helpers\\.$#',
    'identifier' => 'Common.actionClassMustBeInvokable',
    'count' => 1,
    'path' => __DIR__ . '/app/Actions/Finders/ApplicationModules.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "App\\\\Actions\\\\GetRecordFromMorphAndKey" defines disallowed public method "via"\\. Only "__invoke" and "__construct" may be public on action classes\\.$#',
    'identifier' => 'Common.actionClassHasDisallowedPublicMethod',
    'count' => 1,
    'path' => __DIR__ . '/app/Actions/GetRecordFromMorphAndKey.php',
];
$ignoreErrors[] = [
    'message' => '#^Action class "App\\\\Actions\\\\GetRecordFromMorphAndKey" must define a public "__invoke" method\\. Action classes should be accessed via invocation and keep other behavior in private/protected helpers\\.$#',
    'identifier' => 'Common.actionClassMustBeInvokable',
    'count' => 1,
    'path' => __DIR__ . '/app/Actions/GetRecordFromMorphAndKey.php',
];
$ignoreErrors[] = [
    'message' => '#^Class App\\\\Casts\\\\CurrencyCast implements generic interface Illuminate\\\\Contracts\\\\Database\\\\Eloquent\\\\CastsAttributes but does not specify its types\\: TGet, TSet$#',
    'identifier' => 'missingType.generics',
    'count' => 1,
    'path' => __DIR__ . '/app/Casts/CurrencyCast.php',
];
$ignoreErrors[] = [
    'message' => '#^Avoid strtolower\\(\\) as it is not multibyte\\-safe\\. Use Laravel\'s Str\\:\\:lower\\(\\) instead, or mb_strtolower\\(\\) if a framework\\-free alternative is required\\. If you are certain you specifically need strtolower\\(\\), add an inline ignore for this rule \\(// @phpstan\\-ignore Common\\.noStrtolower\\)\\.$#',
    'identifier' => 'Common.noStrtolower',
    'count' => 1,
    'path' => __DIR__ . '/app/Filament/Resources/Users/RelationManagers/RolesRelationManager.php',
];
$ignoreErrors[] = [
    'message' => '#^Avoid strtolower\\(\\) as it is not multibyte\\-safe\\. Use Laravel\'s Str\\:\\:lower\\(\\) instead, or mb_strtolower\\(\\) if a framework\\-free alternative is required\\. If you are certain you specifically need strtolower\\(\\), add an inline ignore for this rule \\(// @phpstan\\-ignore Common\\.noStrtolower\\)\\.$#',
    'identifier' => 'Common.noStrtolower',
    'count' => 1,
    'path' => __DIR__ . '/app/Filament/Widgets/ListServiceRequestTableWidgets.php',
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
    'message' => '#^If condition is always true\\.$#',
    'identifier' => 'if.alwaysTrue',
    'count' => 1,
    'path' => __DIR__ . '/app/Filament/Widgets/ServiceRequestWidget.php',
];
$ignoreErrors[] = [
    'message' => '#^Parameter \\#1 \\$query of anonymous function has no typehint\\.$#',
    'identifier' => 'MeliorStan.closureParameterMissingTypehint',
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
    'message' => '#^Avoid strtolower\\(\\) as it is not multibyte\\-safe\\. Use Laravel\'s Str\\:\\:lower\\(\\) instead, or mb_strtolower\\(\\) if a framework\\-free alternative is required\\. If you are certain you specifically need strtolower\\(\\), add an inline ignore for this rule \\(// @phpstan\\-ignore Common\\.noStrtolower\\)\\.$#',
    'identifier' => 'Common.noStrtolower',
    'count' => 4,
    'path' => __DIR__ . '/app/Http/Requests/Api/V1/Users/CreateUserRequest.php',
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
    'message' => '#^Avoid strtolower\\(\\) as it is not multibyte\\-safe\\. Use Laravel\'s Str\\:\\:lower\\(\\) instead, or mb_strtolower\\(\\) if a framework\\-free alternative is required\\. If you are certain you specifically need strtolower\\(\\), add an inline ignore for this rule \\(// @phpstan\\-ignore Common\\.noStrtolower\\)\\.$#',
    'identifier' => 'Common.noStrtolower',
    'count' => 1,
    'path' => __DIR__ . '/app/Models/Scopes/EducatableSearch.php',
];
$ignoreErrors[] = [
    'message' => '#^Eloquent local scopes are not allowed\\. The "scopeAdvancedFilter" method on "App\\\\Support\\\\HasAdvancedFilter" defines a local scope, which relies on magic methods that static analysis cannot reason about\\. Use a tappable scope instead\\. See https\\://seankegel\\.com/elevate\\-your\\-laravel\\-eloquent\\-queries\\-with\\-tappable\\-scopes\\.$#',
    'identifier' => 'Common.noLocalModelScope',
    'count' => 1,
    'path' => __DIR__ . '/app/Support/HasAdvancedFilter.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
