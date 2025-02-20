<?php

namespace AidingApp\Report\Abstract;

use AidingApp\Authorization\Enums\LicenseType;
use Filament\Pages\Dashboard;

abstract class EngagementReport extends Dashboard
{
    public static function canAccess(): bool
    {
        /** @var User $user */
        $user = auth()->user();

        return $user->hasLicense(LicenseType::RecruitmentCrm) && $user->can('report-library.view-any');
    }
}

