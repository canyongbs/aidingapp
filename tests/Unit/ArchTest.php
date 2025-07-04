<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

    Aiding App™ is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Aiding App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    <https://www.canyongbs.com> or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

use App\Concerns\EditPageRedirection;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use InterNACHI\Modular\Support\ModuleConfig;
use InterNACHI\Modular\Support\ModuleRegistry;
use PHPUnit\Framework\Assert;

arch('All Core Settings classes should have defaults for all properties')
    ->expect('App\Settings')
    ->toHaveDefaultsForAllProperties();

arch('All Core Models should not use HasUuids trait')
    ->expect('App\Models')
    ->extending(Model::class)
    ->not->toUseTrait('Illuminate\Database\Eloquent\Concerns\HasUuids');

arch('All Core Factories should not use the fake global function')
    ->expect('Database\Factories')
    ->not->toUse('fake');

app(ModuleRegistry::class, [
    'modules_path' => 'app-modules',
    'cache_path' => 'cache/modules.php',
])->modules()->each(function (ModuleConfig $module) {
    arch("All {$module->name} Settings classes should have defaults for all properties")
        ->expect($module->namespace() . 'Settings')
        ->toHaveDefaultsForAllProperties();

    arch("All {$module->name} Models should not use HasUuids trait")
        ->expect($module->namespace() . 'Models')
        ->extending(Model::class)
        ->not->toUseTrait('Illuminate\Database\Eloquent\Concerns\HasUuids');

    arch("All {$module->name} Factories should not use the fake global function")
        ->expect($module->namespace() . 'Database\Factories')
        ->not->toUse('fake');
});

test('pages extending EditRecord have the EditPageRedirection test', function () {
    foreach (get_declared_classes() as $class) {
        if (
            (! str_starts_with($class, 'App\\'))
            && (! str_starts_with($class, 'AidingApp\\'))
        ) {
            continue;
        }

        if (! is_subclass_of($class, EditRecord::class)) {
            continue;
        }

        Assert::assertContains(
            EditPageRedirection::class,
            class_uses_recursive($class),
            "Class [{$class}] does not use the EditPageRedirection trait.",
        );
    }
});
