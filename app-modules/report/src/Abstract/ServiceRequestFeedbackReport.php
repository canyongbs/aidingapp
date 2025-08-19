<?php

namespace AidingApp\Report\Abstract;

use AidingApp\Authorization\Enums\LicenseType;
use AidingApp\Report\Abstract\Concerns\HasFiltersForm;
use App\Models\User;
use Filament\Pages\Dashboard;

abstract class ServiceRequestFeedbackReport extends Dashboard
{
    use HasFiltersForm;

    protected static string $view = 'report::filament.pages.report';

    public static function canAccess(): bool
    {
        /** @var User $user */
        $user = auth()->user();

        return $user->hasLicense(LicenseType::RecruitmentCrm) && $user->can('report-library.view-any');
    }
}
