<?php

namespace App\Filament\Tables\Columns;

use Filament\Tables\Columns\TextColumn;

class MaskColumn extends TextColumn
{
    protected string $view = 'filament.tables.columns.masked-column';
}
