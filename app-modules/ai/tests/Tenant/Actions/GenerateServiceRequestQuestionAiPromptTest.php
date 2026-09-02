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

use AidingApp\Ai\Actions\GenerateServiceRequestQuestionAiPrompt;
use AidingApp\Contact\Models\Contact;
use AidingApp\Form\Filament\Blocks\PasswordFormFieldBlock;
use AidingApp\ServiceManagement\Models\ServiceRequestForm;
use AidingApp\ServiceManagement\Models\ServiceRequestFormField;
use AidingApp\ServiceManagement\Models\ServiceRequestType;

it('excludes password fields from the assistant clarification prompt', function () {
    $type = ServiceRequestType::factory()->create();
    $form = ServiceRequestForm::factory()->for($type, 'type')->create();

    $passwordField = new ServiceRequestFormField([
        'label' => 'Private credential',
        'type' => PasswordFormFieldBlock::type(),
        'is_required' => false,
        'config' => [],
    ]);
    $passwordField->submissible()->associate($form);
    $passwordField->save();

    $textField = new ServiceRequestFormField([
        'label' => 'Visible response',
        'type' => 'text',
        'is_required' => false,
        'config' => [],
    ]);
    $textField->submissible()->associate($form);
    $textField->save();

    $secretId = '00000000-0000-0000-0000-000000000001';
    $prompt = app(GenerateServiceRequestQuestionAiPrompt::class)->execute(
        $type,
        ['Details' => [
            $passwordField->getKey() => $secretId,
            $textField->getKey() => 'Visible answer',
        ]],
        [],
        1,
        Contact::factory()->create(),
    );

    expect($prompt)->toContain('Visible response', 'Visible answer')
        ->not->toContain('Private credential', $secretId);
});
