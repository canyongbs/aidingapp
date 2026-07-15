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

namespace App\Providers\Filament;

use AidingApp\Authorization\Filament\Pages\Auth\Login;
use AidingApp\Theme\Settings\ThemeSettings;
use App\Enums\NavigationGroup;
use App\Features\SubscriptionExpirationFeature;
use App\Filament\Clusters\ProfileSettings;
use App\Filament\Pages\Dashboard;
use App\Filament\Pages\ProductHealth;
use App\Health\Checks\AzureCredentialsExpiringCheck;
use App\Http\Middleware\TrackPresence;
use App\Models\HealthCheckResultHistoryItem;
use App\Models\Tenant;
use App\Multitenancy\Http\Middleware\NeedsTenant;
use Filament\Actions\Action;
use Filament\Actions\ExportAction;
use Filament\Actions\ImportAction;
use Filament\Forms\Components\Field;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Infolists\Components\Entry;
use Filament\Navigation\MenuItem;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\Column;
use Filament\View\PanelsRenderHook;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\HtmlString;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Saade\FilamentFullCalendar\FilamentFullCalendarPlugin;
use ShuvroRoy\FilamentSpatieLaravelHealth\FilamentSpatieLaravelHealthPlugin;
use Spatie\Health\Enums\Status;
use Spatie\Multitenancy\Http\Middleware\EnsureValidTenantSession;

class AdminPanelProvider extends PanelProvider
{
    public function register(): void
    {
        parent::register();

        Field::configureUsing(fn ($field) => $field->translateLabel());
        Entry::configureUsing(fn ($entry) => $entry->translateLabel());
        Column::configureUsing(fn ($column) => $column->translateLabel());
        ExportAction::configureUsing(fn (ExportAction $action) => $action->maxRows(100000));
        ImportAction::configureUsing(fn (ImportAction $action) => $action->maxRows(100000));
    }

    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('/')
            ->login(Login::class)
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->favicon(function () {
                if (! Tenant::checkCurrent()) {
                    return asset('/images/default-favicon.png');
                }

                $themeSettings = app(ThemeSettings::class);
                $favicon = $themeSettings::getSettingsPropertyModel('theme.is_favicon_active')->getFirstMedia('favicon');

                return $themeSettings->is_favicon_active && $favicon ? $favicon->getTemporaryUrl(now()->addMinutes(5)) : asset('/images/default-favicon.png');
            })
            ->readOnlyRelationManagersOnResourceViewPagesByDefault(false)
            ->resourceEditPageRedirect('view')
            ->maxContentWidth('full')
            ->navigationGroups(NavigationGroup::class)
            ->discoverClusters(in: app_path('Filament/Clusters'), for: 'App\\Filament\\Clusters')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->resources([])
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([])
            ->middleware([
                NeedsTenant::class,
                StartSession::class,
                EnsureValidTenantSession::class,
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
                TrackPresence::class,
            ], isPersistent: true)
            ->sidebarCollapsibleOnDesktop()
            ->plugins([
                FilamentSpatieLaravelHealthPlugin::make()
                    ->usingPage(ProductHealth::class),
                FilamentFullCalendarPlugin::make(),
            ])
            ->userMenuItems([
                MenuItem::make()
                    ->label('Profile Settings')
                    ->url(fn () => ProfileSettings::getUrl())
                    ->icon('heroicon-s-cog-6-tooth'),
                Action::make('about')
                    ->label('About')
                    ->modalHeading('Aiding App® by Canyon GBS')
                    ->modalDescription('Version ' . config('sentry.release'))
                    ->modalContent(fn () => view('components.about-modal'))
                    ->modalFooterActions([])
                    ->modalWidth(Width::Small)
                    ->icon('heroicon-s-information-circle'),
            ])
            ->renderHook(
                PanelsRenderHook::TOPBAR_AFTER,
                function (): ?Htmlable {
                    $showBanner = Cache::remember('azure_credentials_expiring', now()->addDay(), function () {
                        $credentialsCheck = HealthCheckResultHistoryItem::where('check_name', app(AzureCredentialsExpiringCheck::class)->getName())
                            ->latest()
                            ->first();

                        if ($credentialsCheck?->status !== Status::warning()->value) {
                            return false;
                        }

                        return true;
                    });

                    return $showBanner ? new HtmlString(Blade::render('<livewire:sso-credentials-expiring-alert />')) : null;
                },
            )
            ->renderHook(
                PanelsRenderHook::TOPBAR_AFTER,
                function (): ?Htmlable {
                    if (! SubscriptionExpirationFeature::active()) {
                        return null;
                    }

                    $tenant = Tenant::current();

                    if (! $tenant?->subscription_status?->showsExpirationBanner()) {
                        return null;
                    }

                    return new HtmlString(Blade::render('<livewire:subscription-expired-banner />'));
                },
            )
            ->globalSearchResourceOptIn();
    }

    public function boot(): void {}
}
