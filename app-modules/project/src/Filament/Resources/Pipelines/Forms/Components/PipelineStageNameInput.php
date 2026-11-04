<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Aiding App® is licensed under the Elastic License 2.0. For more details,
    see <https://github.com/canyongbs/aidingapp/blob/main/LICENSE.>

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Aiding App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    <https://www.canyongbs.com> or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AidingApp\Project\Filament\Resources\Pipelines\Forms\Components;

use App\Features\PipelineStageNameUniquenessFeature;
use Closure;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class PipelineStageNameInput
{
    public static function make(): TextInput
    {
        return TextInput::make('name')
            ->label('Stage')
            ->required()
            ->maxLength(255)
            // TODO: Cleanup Task (pipeline-name-uniqueness): remove this `when()` and always apply the case-insensitive rule (drop the `distinct()` fallback) once the flag is removed.
            ->when(
                PipelineStageNameUniquenessFeature::active(),
                fn (TextInput $input): TextInput => $input->rule(static fn (TextInput $component): Closure => function (string $attribute, mixed $value, Closure $fail) use ($component): void {
                    $repeater = $component->getParentRepeater();
                    $item = $component->getParentRepeaterItem();

                    if (! is_string($value) || blank($value) || ! $repeater || ! $item instanceof Schema) {
                        return;
                    }

                    $name = Str::lower($value);

                    $siblings = Arr::except((array) $repeater->getRawState(), [$item->getStatePath(isAbsolute: false)]);

                    $hasDuplicateSibling = array_any(
                        $siblings,
                        fn (mixed $sibling): bool => is_array($sibling) && is_string($sibling['name'] ?? null) && Str::lower($sibling['name']) === $name,
                    );

                    if ($hasDuplicateSibling) {
                        $fail(__('validation.distinct', ['attribute' => $component->getValidationAttribute()]));
                    }
                }),
                fn (TextInput $input): TextInput => $input->distinct(),
            );
    }
}
