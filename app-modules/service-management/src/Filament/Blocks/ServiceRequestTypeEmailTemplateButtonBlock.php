<?php

namespace AidingApp\ServiceManagement\Filament\Blocks;

use AdvisingApp\Form\Models\SubmissibleField;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use FilamentTiptapEditor\TiptapBlock;

class ServiceRequestTypeEmailTemplateButtonBlock extends TiptapBlock
{
    public string $preview = 'service-management::blocks.previews.service-request-link';

    public string $rendered = 'service-management::blocks.rendered.service-request-link';

    public ?string $icon = null;

    public ?string $label = 'Button';

    public string $width = 'md';

    public function getFormSchema(): array
    {
        return [
            // Select::make('link_type')
            //     ->label('Link Type')
            //     ->options([
            //         'sr-customer-link' => 'Customer Portal',
            //         'sr-staff-link' => 'Staff Portal',
            //     ])
            //     ->default('sr-customer-link')
            //     ->required(),

            TextInput::make('button_text')
                ->label('Button Text')
                ->default('View Service Request')
                ->required(),

            Select::make('button_position')
                ->label('Button Position')
                ->options([
                    'left' => 'Left',
                    'right' => 'Right',
                    'center' => 'Center',
                ])
                ->default('left')
                ->required()
                ->placeholder('Enter the button position (e.g. left, right, center)'),

            // Select::make('button_color')
            //     ->label('Button Color')
            //     ->options([
            //         'primary' => 'Primary (Blue)',
            //         'success' => 'Success (Green)',
            //         'danger' => 'Danger (Red)',
            //         'warning' => 'Warning (Yellow)',
            //         'secondary' => 'Secondary (Gray)',
            //     ])
            //     ->default('primary')
            //     ->required(),
        ];
    }
}