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

use AidingApp\Form\Filament\Blocks\CheckboxFormFieldBlock;
use AidingApp\ServiceManagement\Models\ServiceRequestFormField;
use Illuminate\Support\Facades\Validator;

// TODO: Cleanup Task (convert-checkbox-form-fields): delete this file.
it('validates a legacy checkbox answer as a boolean', function (mixed $response, bool $passes): void {
    $field = new ServiceRequestFormField(['label' => 'Agree', 'config' => []]);

    expect(Validator::make(['response' => $response], ['response' => CheckboxFormFieldBlock::getValidationRules($field)])->passes())->toBe($passes);
})->with([
    'true' => [true, true],
    'false' => [false, true],
    'text' => ['maybe', false],
]);

it('shows a legacy checkbox answer in the submission view', function (mixed $response, string $expected): void {
    $html = view('form::blocks.submissions.checkbox', [
        'label' => 'Agree',
        'isRequired' => false,
        'response' => $response,
    ])->render();

    expect($html)->toContain($expected);
})->with([
    'no response' => [null, 'No response'],
    'checked' => [true, 'text-success-500'],
    'unchecked' => [false, 'text-danger-500'],
]);

it('renders the legacy checkbox label in the preview', function (): void {
    expect(CheckboxFormFieldBlock::toPreviewHtml(['label' => 'Agree', 'isRequired' => false]))->toContain('Agree');
});
