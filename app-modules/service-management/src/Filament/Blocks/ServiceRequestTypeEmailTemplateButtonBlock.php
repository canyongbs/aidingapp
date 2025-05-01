<?php

namespace AidingApp\ServiceManagement\Filament\Blocks;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\Select;
use FilamentTiptapEditor\TiptapBlock;

class ServiceRequestTypeEmailTemplateButtonBlock extends TiptapBlock
{
    public string $preview = 'service-management::blocks.previews.service-request-link';

    public string $rendered = 'service-management::blocks.rendered.service-request-link';

    public ?string $icon = null;

    public ?string $label = 'Button';

    public string $width = 'md';

    /**
     * @return array<int, Component>
     */
    public function getFormSchema(): array
    {
        return [
            Select::make('button_position')
                ->label('Button Position')
                ->options([
                    'left' => 'Left',
                    'right' => 'Right',
                    'center' => 'Center',
                ])
                ->default('center')
                ->required()
                ->placeholder('Enter the button position (e.g. left, right, center)'),
        ];
    }
}
