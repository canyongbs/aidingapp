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

use AidingApp\Form\Filament\Blocks\EducatableEmailFormFieldBlock;
use AidingApp\Form\Filament\Blocks\FormFieldBlock;
use AidingApp\Form\Filament\Blocks\RadioFormFieldBlock;
use AidingApp\Form\Filament\Blocks\TextInputFormFieldBlock;
use Filament\Actions\Action;

it('configures editor actions as slide-overs', function (string $block): void {
    $action = $block::configureEditorAction(Action::make('configure'));

    expect($action->isModalSlideOver())->toBeTrue();
})->with([
    'base block' => FormFieldBlock::class,
    'text input block' => TextInputFormFieldBlock::class,
    'radio block' => RadioFormFieldBlock::class,
    'educatable email block' => EducatableEmailFormFieldBlock::class,
]);

it('always uses the block type label for the rich editor header, regardless of the configured field label', function (): void {
    expect(TextInputFormFieldBlock::getPreviewLabel(['label' => 'First Name']))->toBe('Text input');
});

it('still renders the configured field label inside the block preview body', function (): void {
    expect(TextInputFormFieldBlock::toPreviewHtml(['label' => 'First Name', 'isRequired' => true]))
        ->toContain('First Name');
});

it('falls back to the block type label inside the preview body when no field label has been configured yet', function (): void {
    expect(TextInputFormFieldBlock::toPreviewHtml([]))
        ->toContain('Text input');
});
