<?php

namespace AidingApp\Form\Filament\Blocks;

use Filament\Forms\Get;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use AidingApp\Form\Models\SubmissibleField;

class UploadFormFieldBlock extends FormFieldBlock
{
    public ?string $icon = 'heroicon-m-document-arrow-up';

    public static function type(): string
    {
        return 'upload';
    }

    public function fields(): array
    {
        return [
            Checkbox::make('multiple')
                ->live(),
            TextInput::make('limit')
                ->integer()
                ->minValue(2)
                ->default(2)
                ->visible(fn (Get $get) => $get('multiple')),
            TagsInput::make('accept')
                ->label('Accepted File Types')
                ->placeholder('e.g. .pdf, .docx, .jpg'),
            TextInput::make('size')
                ->label('Maximum File Size')
                ->integer()
                ->suffix('MB'),
        ];
    }

    public static function getFormKitSchema(SubmissibleField $field): array
    {
        return [
            '$formkit' => 'file',
            // 'validationRules' => '$validationRules',
            'label' => $field->label,
            'name' => $field->getKey(),
            'multiple' => $field->config['multiple'] ?? false,
            'accept' => $field->config['accept'] ?? [],
            ...[
                'validation' => [
                    $field->is_required ? ['required'] : [],
                    $field->config['limit'] ? ['max', $field->config['limit']] : [],
                    $field->config['size'] ? ['size', $field->config['size']] : [],
                ],
            ],
        ];
    }

    public static function getValidationRules(SubmissibleField $field): array
    {
        return [];
    }
}
