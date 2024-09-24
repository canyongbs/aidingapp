<?php

namespace AidingApp\ServiceManagement\Filament\Concerns;

use Filament\View\PanelsRenderHook;
use Illuminate\Contracts\View\View;
use Filament\Support\Facades\FilamentView;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestResource\Pages\ManageAssignments;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestResource\Pages\ViewServiceRequest;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestResource\Pages\ManageServiceRequestUpdate;

trait ServiceRequestLocked
{
    public function bootServiceRequestLocked()
    {
        FilamentView::registerRenderHook(
            PanelsRenderHook::PAGE_HEADER_ACTIONS_AFTER,
            fn (): View => view('filament.pages.service-request-lock-icon'),
            scopes: [
                ViewServiceRequest::class,
                ManageAssignments::class,
                ManageServiceRequestUpdate::class,
            ],
        );
    }
}
