<?php

use InterNACHI\Modular\Support\ModuleConfig;
use InterNACHI\Modular\Support\ModuleRegistry;

arch('app')
    ->expect('App\Settings')
    ->toHaveDefaultsForAllProperties();

// app(ModuleRegistry::class, [
//     'modules_path' => 'app-modules',
//     'cache_path' => 'cache/modules.php'
// ])->modules()->each(function(ModuleConfig $module) {
//     arch('app-modules-' . $module->name)
//         ->expect($module->namespace() . 'Settings')
//         ->toHaveAttribute('App\Settings\Contracts\HasDefaultSettings');
// });