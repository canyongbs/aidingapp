<?php

namespace App\Livewire;

use CanyonGBS\Common\Enums\Color;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class SsoCredentialsExpiringAlert extends Component
{
    public ?Color $color = Color::Yellow;

    public function render(): View
    {
        return view('filament.components.sso-credentials-expiring-alert');
    }
}