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

use AidingApp\Form\Filament\Blocks\CheckboxesFormFieldBlock;
use AidingApp\Form\Filament\Blocks\CheckboxFormFieldBlock;
use AidingApp\Form\Filament\Blocks\FormFieldBlockRegistry;
use App\Features\CheckboxesFormFieldFeature;

// TODO: Cleanup Task (convert-checkbox-form-fields): delete this test.
it('offers the legacy checkbox block while the feature flag is inactive', function (): void {
    CheckboxesFormFieldFeature::deactivate();

    expect(FormFieldBlockRegistry::get())
        ->toContain(CheckboxFormFieldBlock::class)
        ->and(FormFieldBlockRegistry::get())->not()->toContain(CheckboxesFormFieldBlock::class)
        ->and(FormFieldBlockRegistry::keyByType())->toHaveKey('checkbox')->not()->toHaveKey('checkboxes');
});

it('offers the checkboxes block instead of the legacy checkbox block', function (): void {
    expect(FormFieldBlockRegistry::get())
        ->toContain(CheckboxesFormFieldBlock::class)
        ->and(FormFieldBlockRegistry::get())->not()->toContain(CheckboxFormFieldBlock::class)
        ->and(FormFieldBlockRegistry::keyByType())->toHaveKey('checkboxes')->not()->toHaveKey('checkbox');
});
