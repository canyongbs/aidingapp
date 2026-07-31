{{--
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
--}}
@php
    use Filament\Support\Colors\Color;
    use Filament\Support\Facades\FilamentColor;

    $palettes = Color::all();
    $palette = FilamentColor::getColor($color ?? 'blue') ?? ($palettes[$color ?? 'blue'] ?? $palettes['blue']);

    $gradient = [
        'from' => $palette[400],
        'to' => $palette[700],
        'darkFrom' => $palette[500],
        'darkTo' => $palette[800],
    ];
@endphp
<span class="flex items-center gap-2" data-icon="{{ $icon }}">
    <span
        class="flex h-6 w-6 shrink-0 items-center justify-center rounded-md bg-linear-to-b from-(--project-gradient-from) to-(--project-gradient-to) dark:from-(--project-gradient-from-dark) dark:to-(--project-gradient-to-dark)"
        style="
            --project-gradient-from: {{ $gradient['from'] }};
            --project-gradient-to: {{ $gradient['to'] }};
            --project-gradient-from-dark: {{ $gradient['darkFrom'] }};
            --project-gradient-to-dark: {{ $gradient['darkTo'] }};
        "
    >
        <x-filament::icon :icon="$icon" class="h-4 w-4 shrink-0 text-white" />
    </span>
    <span>{{ $name }}</span>
</span>
