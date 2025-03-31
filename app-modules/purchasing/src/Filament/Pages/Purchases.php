<?php

namespace AidingApp\Purchasing\Filament\Pages;

use App\Models\User;
use Filament\Pages\Page;

class Purchases extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.coming-soon';

    protected static ?int $navigationSort = 30;

    protected static ?string $navigationGroup = 'Purchasing';

    public static function canAccess(): bool
    {
        /** @var User $user */
        $user = auth()->user();

        return $user->can(['purchase.view-any', 'purchase.*.view']);
    }
}
