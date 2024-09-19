<?php

namespace AidingApp\ServiceManagement\Filament\Concerns;

use Filament\View\PanelsRenderHook;
use Illuminate\Contracts\View\View;
use Filament\Support\Facades\FilamentView;

trait ServiceRequestLocked
{
    public function boot()
    {
        FilamentView::registerRenderHook(
            PanelsRenderHook::PAGE_HEADER_ACTIONS_AFTER,
            fn (): View => view('filament.pages.service-request-lock-icon'),
        );
    }
}
