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

use AidingApp\Authorization\Filament\Pages\Auth\Login;
use AidingApp\Authorization\Settings\AzureSsoSettings;
use AidingApp\Authorization\Settings\GoogleSsoSettings;
use Dom\Element;
use Dom\HTMLDocument;

use function Pest\Laravel\get;

$browserUserAgent = 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/15E148';

$mobileAppUserAgent = 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/15E148 AidingAppMobile/1.0';

$enableGoogleSso = function (): void {
    $googleSsoSettings = app(GoogleSsoSettings::class);
    $googleSsoSettings->is_enabled = true;
    $googleSsoSettings->client_id = 'test-client-id';
    $googleSsoSettings->client_secret = 'test-client-secret';
    $googleSsoSettings->save();
};

$enableAzureSso = function (): void {
    $azureSsoSettings = app(AzureSsoSettings::class);
    $azureSsoSettings->is_enabled = true;
    $azureSsoSettings->client_id = 'test-client-id';
    $azureSsoSettings->client_secret = 'test-client-secret';
    $azureSsoSettings->tenant_id = 'test-tenant-id';
    $azureSsoSettings->save();
};

$renderLoginPage = function (string $userAgent): HTMLDocument {
    $response = get(route('filament.admin.auth.login'), ['User-Agent' => $userAgent])
        ->assertOk();

    return HTMLDocument::createFromString((string) $response->getContent(), LIBXML_NOERROR);
};

$textsOf = fn (HTMLDocument $page, string $selector): array => array_map(
    fn (Element $element): string => trim((string) $element->textContent),
    iterator_to_array($page->querySelectorAll($selector)),
);

$switchTenantLinkSelector = 'a[href="' . Login::SWITCH_TENANT_URL . '"]';

$ssoLinkSelector = fn (string $provider): string => 'form a[href="' . route('socialite.redirect', ['provider' => $provider]) . '"]';

it('does not show the switch tenant control in a browser', function () use ($browserUserAgent, $renderLoginPage, $textsOf, $switchTenantLinkSelector): void {
    $page = $renderLoginPage($browserUserAgent);

    expect($textsOf($page, $switchTenantLinkSelector))->toBe([]);
});

it('shows the google sign-in button in a browser when google sso is enabled', function () use ($browserUserAgent, $enableGoogleSso, $renderLoginPage, $textsOf, $ssoLinkSelector): void {
    $enableGoogleSso();

    $page = $renderLoginPage($browserUserAgent);

    expect($textsOf($page, $ssoLinkSelector('google')))->toBe(['Google'])
        ->and($textsOf($page, 'form p'))->not->toContain(Login::GOOGLE_SSO_UNAVAILABLE_IN_MOBILE_APP_MESSAGE);
});

it('shows the switch tenant control in the mobile app', function () use ($mobileAppUserAgent, $renderLoginPage, $textsOf, $switchTenantLinkSelector): void {
    $page = $renderLoginPage($mobileAppUserAgent);

    expect($textsOf($page, $switchTenantLinkSelector))->toBe(['Switch tenant']);
});

it('replaces the google sign-in button with an unavailable message in the mobile app when google sso is enabled', function () use ($mobileAppUserAgent, $enableGoogleSso, $renderLoginPage, $textsOf, $ssoLinkSelector): void {
    $enableGoogleSso();

    $page = $renderLoginPage($mobileAppUserAgent);

    expect($textsOf($page, 'form p'))->toContain(Login::GOOGLE_SSO_UNAVAILABLE_IN_MOBILE_APP_MESSAGE)
        ->and($textsOf($page, $ssoLinkSelector('google')))->toBe([])
        ->and($textsOf($page, 'form small'))->not->toContain('or log in with single sign-on');
});

it('does not show the google unavailable message in the mobile app when google sso is not enabled', function () use ($mobileAppUserAgent, $renderLoginPage, $textsOf): void {
    $page = $renderLoginPage($mobileAppUserAgent);

    expect($textsOf($page, 'form p'))->not->toContain(Login::GOOGLE_SSO_UNAVAILABLE_IN_MOBILE_APP_MESSAGE);
});

it('keeps the microsoft sign-in and password login available', function (string $userAgent) use ($enableAzureSso, $enableGoogleSso, $renderLoginPage, $textsOf, $ssoLinkSelector): void {
    $enableAzureSso();
    $enableGoogleSso();

    $page = $renderLoginPage($userAgent);

    expect($textsOf($page, $ssoLinkSelector('azure')))->toBe(['Microsoft'])
        ->and($page->querySelectorAll('form input[type="password"]'))->toHaveCount(1);
})->with([
    'browser' => [$browserUserAgent],
    'mobile app' => [$mobileAppUserAgent],
]);
