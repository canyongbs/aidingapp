<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

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

use AidingApp\Ai\Actions\PortalAssistant\GetDraftStatus;
use AidingApp\Ai\Settings\AiResolutionSettings;
use AidingApp\Contact\Models\Contact;
use AidingApp\Form\Filament\Blocks\CheckboxFormFieldBlock;
use AidingApp\Form\Filament\Blocks\SelectFormFieldBlock;
use AidingApp\Form\Filament\Blocks\SignatureFormFieldBlock;
use AidingApp\Form\Filament\Blocks\TextInputFormFieldBlock;
use AidingApp\ServiceManagement\Enums\ServiceRequestUpdateType;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestPriority;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

describe('Stage Determination', function () {
    it('returns null draft_stage when service request is not a draft', function () {
        $type = ServiceRequestType::factory()->create();
        $priority = ServiceRequestPriority::factory()->create(['type_id' => $type->getKey()]);

        $serviceRequest = ServiceRequest::factory()->create([
            'is_draft' => false,
            'title' => 'Test Title',
            'close_details' => 'Test Description',
            'priority_id' => $priority->getKey(),
        ]);

        $result = app(GetDraftStatus::class)->execute($serviceRequest);

        expect($result['draft_stage'])->toBeNull();
    });

    it('returns data_collection stage when missing title', function () {
        $draft = createBasicDraft(['title' => null]);

        $result = app(GetDraftStatus::class)->execute($draft);

        expect($result['draft_stage'])->toBe('data_collection');
    });

    it('returns data_collection stage when missing description', function () {
        $draft = createBasicDraft(['close_details' => null]);

        $result = app(GetDraftStatus::class)->execute($draft);

        expect($result['draft_stage'])->toBe('data_collection');
    });

    it('returns clarifying_questions stage when data complete but questions less than 3', function () {
        $draft = createCompleteDraft();

        addClarifyingQuestions($draft, 2);

        $result = app(GetDraftStatus::class)->execute($draft);

        expect($result['draft_stage'])->toBe('clarifying_questions');
    });

    it('returns resolution stage when 3 clarifying questions completed', function () {
        $draft = createCompleteDraft();

        addClarifyingQuestions($draft, 3);

        $result = app(GetDraftStatus::class)->execute($draft);

        expect($result['draft_stage'])->toBe('resolution');
    });
});

describe('Result Structure', function () {
    it('includes type_name from priority type', function () {
        $type = ServiceRequestType::factory()->create(['name' => 'Custom Type Name']);
        $priority = ServiceRequestPriority::factory()->create(['type_id' => $type->getKey()]);

        $draft = ServiceRequest::factory()->create([
            'is_draft' => true,
            'title' => null,
            'priority_id' => $priority->getKey(),
        ]);

        $result = app(GetDraftStatus::class)->execute($draft);

        expect($result['type_name'])->toBe('Custom Type Name');
    });
});

describe('DataCollection Stage Data', function () {
    it('includes title and description in data_collection stage', function () {
        $type = ServiceRequestType::factory()->create();
        $priority = ServiceRequestPriority::factory()->create(['type_id' => $type->getKey()]);

        $form = $type->form()->create(['name' => 'Test Form']);
        $form->fields()->create([
            'label' => 'Required Field',
            'type' => TextInputFormFieldBlock::type(),
            'is_required' => true,
            'config' => [],
        ]);

        $draft = ServiceRequest::factory()->create([
            'is_draft' => true,
            'title' => 'My Title',
            'close_details' => 'My Description',
            'priority_id' => $priority->getKey(),
        ]);

        $result = app(GetDraftStatus::class)->execute($draft);

        expect($result['draft_stage'])->toBe('data_collection')
            ->and($result)->toHaveKey('title')
            ->and($result)->toHaveKey('description')
            ->and($result['title'])->toBe('My Title')
            ->and($result['description'])->toBe('My Description');
    });

    it('includes next_field with title when missing', function () {
        $draft = createBasicDraft(['title' => null]);

        $result = app(GetDraftStatus::class)->execute($draft);

        expect($result['next_field'])->not->toBeNull()
            ->and($result['next_field']['type'])->toBe('title')
            ->and($result['next_field']['field_id'])->toBe('title');
    });

    it('includes next_field for optional form field when it has no submission', function () {
        $type = ServiceRequestType::factory()->create();
        $priority = ServiceRequestPriority::factory()->create(['type_id' => $type->getKey()]);

        $form = $type->form()->create(['name' => 'Test Form']);
        $field = $form->fields()->create([
            'label' => 'Optional Field',
            'type' => TextInputFormFieldBlock::type(),
            'is_required' => false,
            'config' => [],
        ]);

        $draft = ServiceRequest::factory()->create([
            'is_draft' => true,
            'title' => null,
            'priority_id' => $priority->getKey(),
        ]);

        $result = app(GetDraftStatus::class)->execute($draft);

        expect($result['next_field'])->not->toBeNull()
            ->and($result['next_field']['label'])->toBe('Optional Field')
            ->and($result['next_field']['is_optional'])->toBeTrue()
            ->and($result['next_field']['required'])->toBeFalse();
    });

    it('includes has_custom_form_fields flag when form has fields', function () {
        $type = ServiceRequestType::factory()->create();
        $priority = ServiceRequestPriority::factory()->create(['type_id' => $type->getKey()]);

        $form = $type->form()->create(['name' => 'Test Form']);
        $form->fields()->create([
            'label' => 'Test Field',
            'type' => TextInputFormFieldBlock::type(),
            'is_required' => true,
            'config' => [],
        ]);

        $draft = ServiceRequest::factory()->create([
            'is_draft' => true,
            'title' => null,
            'priority_id' => $priority->getKey(),
        ]);

        $result = app(GetDraftStatus::class)->execute($draft);

        expect($result['has_custom_form_fields'])->toBeTrue();
    });

    it('has_custom_form_fields is false when no form', function () {
        $draft = createBasicDraft(['title' => null]);

        $result = app(GetDraftStatus::class)->execute($draft);

        expect($result['has_custom_form_fields'])->toBeFalse();
    });

    it('includes filled_form_fields when only title and description remain', function () {
        $type = ServiceRequestType::factory()->create();
        $priority = ServiceRequestPriority::factory()->create(['type_id' => $type->getKey()]);

        $form = $type->form()->create(['name' => 'Test Form']);
        $field = $form->fields()->create([
            'label' => 'Custom Field',
            'type' => TextInputFormFieldBlock::type(),
            'is_required' => true,
            'config' => [],
        ]);

        $draft = ServiceRequest::factory()->create([
            'is_draft' => true,
            'title' => null,
            'close_details' => null,
            'priority_id' => $priority->getKey(),
        ]);

        $submission = $form->submissions()->create();
        $draft->service_request_form_submission_id = $submission->getKey();
        $draft->saveQuietly();

        createFieldSubmission($submission, $field, 'Field Value');

        $result = app(GetDraftStatus::class)->execute($draft);

        expect($result)->toHaveKey('filled_form_fields')
            ->and($result['filled_form_fields'])->toHaveCount(1)
            ->and($result['filled_form_fields'][0]['label'])->toBe('Custom Field')
            ->and($result['filled_form_fields'][0]['value'])->toBe('Field Value');
    });

    it('does not include filled_form_fields when custom form fields still need submission', function () {
        $type = ServiceRequestType::factory()->create();
        $priority = ServiceRequestPriority::factory()->create(['type_id' => $type->getKey()]);

        $form = $type->form()->create(['name' => 'Test Form']);
        $form->fields()->create([
            'label' => 'Required Field',
            'type' => TextInputFormFieldBlock::type(),
            'is_required' => true,
            'config' => [],
        ]);

        $draft = ServiceRequest::factory()->create([
            'is_draft' => true,
            'title' => 'Test Title',
            'close_details' => 'Test Description',
            'priority_id' => $priority->getKey(),
        ]);

        $result = app(GetDraftStatus::class)->execute($draft);

        expect($result)->not->toHaveKey('filled_form_fields');
    });
});

describe('ClarifyingQuestions and Resolution Stage Data', function () {
    it('includes title and description in clarifying_questions stage', function () {
        $draft = createCompleteDraft();

        $result = app(GetDraftStatus::class)->execute($draft);

        expect($result['draft_stage'])->toBe('clarifying_questions')
            ->and($result)->toHaveKey('title')
            ->and($result)->toHaveKey('description');
    });

    it('includes filled_form_fields in clarifying_questions stage', function () {
        $type = ServiceRequestType::factory()->create();
        $priority = ServiceRequestPriority::factory()->create(['type_id' => $type->getKey()]);

        $form = $type->form()->create(['name' => 'Test Form']);
        $field = $form->fields()->create([
            'label' => 'Custom Field',
            'type' => TextInputFormFieldBlock::type(),
            'is_required' => true,
            'config' => [],
        ]);

        $contact = Contact::factory()->create();

        $draft = ServiceRequest::factory()->create([
            'is_draft' => true,
            'title' => 'Test Title',
            'close_details' => 'Test Description',
            'priority_id' => $priority->getKey(),
            'respondent_id' => $contact->getKey(),
        ]);

        $submission = $form->submissions()->create();
        $draft->service_request_form_submission_id = $submission->getKey();
        $draft->saveQuietly();

        createFieldSubmission($submission, $field, 'Field Value');

        $result = app(GetDraftStatus::class)->execute($draft);

        expect($result['draft_stage'])->toBe('clarifying_questions')
            ->and($result)->toHaveKey('filled_form_fields')
            ->and($result['filled_form_fields'])->toHaveCount(1);
    });

    it('includes questions_completed count', function () {
        $draft = createCompleteDraft();

        addClarifyingQuestions($draft, 2);

        $result = app(GetDraftStatus::class)->execute($draft);

        expect($result['questions_completed'])->toBe(2);
    });
});

describe('Next Field Tracking', function () {
    it('returns first form field without submission as next_field', function () {
        $type = ServiceRequestType::factory()->create();
        $priority = ServiceRequestPriority::factory()->create(['type_id' => $type->getKey()]);

        $form = $type->form()->create(['name' => 'Test Form']);
        $step = $form->steps()->create(['label' => 'Step 1', 'sort' => 1]);

        $field1 = $form->fields()->create([
            'label' => 'First Field',
            'type' => TextInputFormFieldBlock::type(),
            'is_required' => true,
            'config' => [],
            'service_request_form_step_id' => $step->getKey(),
        ]);

        $form->fields()->create([
            'label' => 'Second Field',
            'type' => TextInputFormFieldBlock::type(),
            'is_required' => true,
            'config' => [],
            'service_request_form_step_id' => $step->getKey(),
        ]);

        $draft = ServiceRequest::factory()->create([
            'is_draft' => true,
            'title' => 'Test',
            'close_details' => 'Test',
            'priority_id' => $priority->getKey(),
        ]);

        $result = app(GetDraftStatus::class)->execute($draft);

        expect($result['next_field']['label'])->toBe('First Field');
    });

    it('moves to second field when first has submission', function () {
        $type = ServiceRequestType::factory()->create();
        $priority = ServiceRequestPriority::factory()->create(['type_id' => $type->getKey()]);

        $form = $type->form()->create(['name' => 'Test Form']);
        $step = $form->steps()->create(['label' => 'Step 1', 'sort' => 1]);

        $field1 = $form->fields()->create([
            'label' => 'First Field',
            'type' => TextInputFormFieldBlock::type(),
            'is_required' => true,
            'config' => [],
            'service_request_form_step_id' => $step->getKey(),
        ]);

        $form->fields()->create([
            'label' => 'Second Field',
            'type' => TextInputFormFieldBlock::type(),
            'is_required' => true,
            'config' => [],
            'service_request_form_step_id' => $step->getKey(),
        ]);

        $draft = ServiceRequest::factory()->create([
            'is_draft' => true,
            'title' => 'Test',
            'close_details' => 'Test',
            'priority_id' => $priority->getKey(),
        ]);

        $submission = $form->submissions()->create();
        $draft->service_request_form_submission_id = $submission->getKey();
        $draft->saveQuietly();

        createFieldSubmission($submission, $field1, 'Filled Value');

        $result = app(GetDraftStatus::class)->execute($draft);

        expect($result['next_field']['label'])->toBe('Second Field');
    });

    it('returns description as next_field when all form fields have submissions', function () {
        $type = ServiceRequestType::factory()->create();
        $priority = ServiceRequestPriority::factory()->create(['type_id' => $type->getKey()]);

        $form = $type->form()->create(['name' => 'Test Form']);
        $field = $form->fields()->create([
            'label' => 'Test Field',
            'type' => TextInputFormFieldBlock::type(),
            'is_required' => true,
            'config' => [],
        ]);

        $draft = ServiceRequest::factory()->create([
            'is_draft' => true,
            'title' => 'Test',
            'close_details' => null,
            'priority_id' => $priority->getKey(),
        ]);

        $submission = $form->submissions()->create();
        $draft->service_request_form_submission_id = $submission->getKey();
        $draft->saveQuietly();

        createFieldSubmission($submission, $field, 'Filled');

        $result = app(GetDraftStatus::class)->execute($draft);

        expect($result['next_field']['field_id'])->toBe('description')
            ->and($result['next_field']['type'])->toBe('description');
    });

    it('returns title as next_field when description is filled but title is empty', function () {
        $draft = createBasicDraft([
            'title' => null,
            'close_details' => 'Test Description',
        ]);

        $result = app(GetDraftStatus::class)->execute($draft);

        expect($result['next_field']['field_id'])->toBe('title')
            ->and($result['next_field']['type'])->toBe('title');
    });

    it('returns null next_field when all fields collected', function () {
        $draft = createCompleteDraft();

        $result = app(GetDraftStatus::class)->execute($draft);

        expect($result)->not->toHaveKey('next_field');
    });

    it('includes options in next_field for fields with options', function () {
        $type = ServiceRequestType::factory()->create();
        $priority = ServiceRequestPriority::factory()->create(['type_id' => $type->getKey()]);

        $form = $type->form()->create(['name' => 'Test Form']);

        $form->fields()->create([
            'label' => 'Select Field',
            'type' => SelectFormFieldBlock::type(),
            'is_required' => true,
            'config' => [
                'options' => [
                    ['label' => 'Option 1', 'value' => 'opt1'],
                    ['label' => 'Option 2', 'value' => 'opt2'],
                ],
            ],
        ]);

        $draft = ServiceRequest::factory()->create([
            'is_draft' => true,
            'title' => 'Test',
            'close_details' => 'Test',
            'priority_id' => $priority->getKey(),
        ]);

        $result = app(GetDraftStatus::class)->execute($draft);

        expect($result['next_field'])->toHaveKey('options')
            ->and($result['next_field']['options'])->toHaveCount(2);
    });

    it('includes collection_method in next_field', function () {
        $type = ServiceRequestType::factory()->create();
        $priority = ServiceRequestPriority::factory()->create(['type_id' => $type->getKey()]);

        $form = $type->form()->create(['name' => 'Test Form']);

        $form->fields()->create([
            'label' => 'Text Field',
            'type' => TextInputFormFieldBlock::type(),
            'is_required' => true,
            'config' => [],
        ]);

        $draft = ServiceRequest::factory()->create([
            'is_draft' => true,
            'title' => 'Test',
            'close_details' => 'Test',
            'priority_id' => $priority->getKey(),
        ]);

        $result = app(GetDraftStatus::class)->execute($draft);

        expect($result['next_field']['collection_method'])->toBe('text');
    });
});

describe('Skipped Optional Fields', function () {
    it('treats empty string submission as skipped and moves to next field', function () {
        $type = ServiceRequestType::factory()->create();
        $priority = ServiceRequestPriority::factory()->create(['type_id' => $type->getKey()]);

        $form = $type->form()->create(['name' => 'Test Form']);
        $step = $form->steps()->create(['label' => 'Step 1', 'sort' => 1]);

        $optionalField = $form->fields()->create([
            'label' => 'Optional Skipped',
            'type' => TextInputFormFieldBlock::type(),
            'is_required' => false,
            'config' => [],
            'service_request_form_step_id' => $step->getKey(),
        ]);

        $form->fields()->create([
            'label' => 'Required Field',
            'type' => TextInputFormFieldBlock::type(),
            'is_required' => true,
            'config' => [],
            'service_request_form_step_id' => $step->getKey(),
        ]);

        $draft = ServiceRequest::factory()->create([
            'is_draft' => true,
            'title' => 'Test',
            'close_details' => 'Test',
            'priority_id' => $priority->getKey(),
        ]);

        $submission = $form->submissions()->create();
        $draft->service_request_form_submission_id = $submission->getKey();
        $draft->saveQuietly();

        createFieldSubmission($submission, $optionalField, '');

        $result = app(GetDraftStatus::class)->execute($draft);

        expect($result['next_field']['label'])->toBe('Required Field');
    });

    it('skipped optional field does not appear in filled_form_fields', function () {
        $type = ServiceRequestType::factory()->create();
        $priority = ServiceRequestPriority::factory()->create(['type_id' => $type->getKey()]);

        $form = $type->form()->create(['name' => 'Test Form']);

        $optionalField = $form->fields()->create([
            'label' => 'Optional Skipped',
            'type' => TextInputFormFieldBlock::type(),
            'is_required' => false,
            'config' => [],
        ]);

        $contact = Contact::factory()->create();

        $draft = ServiceRequest::factory()->create([
            'is_draft' => true,
            'title' => 'Test',
            'close_details' => 'Test',
            'priority_id' => $priority->getKey(),
            'respondent_id' => $contact->getKey(),
        ]);

        $submission = $form->submissions()->create();
        $draft->service_request_form_submission_id = $submission->getKey();
        $draft->saveQuietly();

        createFieldSubmission($submission, $optionalField, '');

        $result = app(GetDraftStatus::class)->execute($draft);

        expect($result['filled_form_fields'])->toBeEmpty();
    });
});

describe('Stage Instructions', function () {
    it('returns error message when stage is null', function () {
        $type = ServiceRequestType::factory()->create();
        $priority = ServiceRequestPriority::factory()->create(['type_id' => $type->getKey()]);

        $serviceRequest = ServiceRequest::factory()->create([
            'is_draft' => false,
            'title' => 'Test',
            'close_details' => 'Test',
            'priority_id' => $priority->getKey(),
        ]);

        $result = app(GetDraftStatus::class)->execute($serviceRequest);

        expect($result['next_instruction'])->toContain('could not be determined');
    });
});

describe('DataCollection Instructions', function () {
    it('instruction mentions transition to clarifying questions when all collected', function () {
        $draft = createCompleteDraft();

        $result = app(GetDraftStatus::class)->execute($draft);

        expect($result['draft_stage'])->toBe('clarifying_questions')
            ->and($result['next_instruction'])->toContain('clarifying');
    });

    it('instruction for description varies based on has_custom_form_fields', function () {
        $draftNoForm = createBasicDraft([
            'title' => 'Test Title',
            'close_details' => null,
        ]);

        $resultNoForm = app(GetDraftStatus::class)->execute($draftNoForm);

        $type = ServiceRequestType::factory()->create();
        $priority = ServiceRequestPriority::factory()->create(['type_id' => $type->getKey()]);

        $form = $type->form()->create(['name' => 'Test Form']);
        $field = $form->fields()->create([
            'label' => 'Custom Field',
            'type' => TextInputFormFieldBlock::type(),
            'is_required' => true,
            'config' => [],
        ]);

        $draftWithForm = ServiceRequest::factory()->create([
            'is_draft' => true,
            'title' => 'Test Title',
            'close_details' => null,
            'priority_id' => $priority->getKey(),
        ]);

        $submission = $form->submissions()->create();
        $draftWithForm->service_request_form_submission_id = $submission->getKey();
        $draftWithForm->saveQuietly();

        createFieldSubmission($submission, $field, 'Filled');

        $resultWithForm = app(GetDraftStatus::class)->execute($draftWithForm);

        expect($resultNoForm['next_instruction'])->toContain('describe')
            ->and($resultWithForm['next_instruction'])->toContain('anything else');
    });

    it('instruction for title field mentions suggesting a title', function () {
        $draft = createBasicDraft([
            'title' => null,
            'close_details' => 'Test Description',
        ]);

        $result = app(GetDraftStatus::class)->execute($draft);

        expect($result['next_instruction'])->toContain('title')
            ->and($result['next_instruction'])->toContain('suggest');
    });

    it('instruction for complex field mentions show_field_input', function () {
        $type = ServiceRequestType::factory()->create();
        $priority = ServiceRequestPriority::factory()->create(['type_id' => $type->getKey()]);

        $form = $type->form()->create(['name' => 'Test Form']);

        $form->fields()->create([
            'label' => 'Select Field',
            'type' => SelectFormFieldBlock::type(),
            'is_required' => true,
            'config' => [
                'options' => [
                    ['label' => 'Option 1', 'value' => 'opt1'],
                ],
            ],
        ]);

        $draft = ServiceRequest::factory()->create([
            'is_draft' => true,
            'title' => 'Test',
            'close_details' => 'Test',
            'priority_id' => $priority->getKey(),
        ]);

        $result = app(GetDraftStatus::class)->execute($draft);

        expect($result['next_instruction'])->toContain('show_field_input');
    });

    it('instruction for text field mentions update_form_field', function () {
        $type = ServiceRequestType::factory()->create();
        $priority = ServiceRequestPriority::factory()->create(['type_id' => $type->getKey()]);

        $form = $type->form()->create(['name' => 'Test Form']);

        $form->fields()->create([
            'label' => 'Text Field',
            'type' => TextInputFormFieldBlock::type(),
            'is_required' => true,
            'config' => [],
        ]);

        $draft = ServiceRequest::factory()->create([
            'is_draft' => true,
            'title' => 'Test',
            'close_details' => 'Test',
            'priority_id' => $priority->getKey(),
        ]);

        $result = app(GetDraftStatus::class)->execute($draft);

        expect($result['next_instruction'])->toContain('update_form_field');
    });

    it('instruction for optional field mentions skip_form_field', function () {
        $type = ServiceRequestType::factory()->create();
        $priority = ServiceRequestPriority::factory()->create(['type_id' => $type->getKey()]);

        $form = $type->form()->create(['name' => 'Test Form']);

        $form->fields()->create([
            'label' => 'Optional Field',
            'type' => TextInputFormFieldBlock::type(),
            'is_required' => false,
            'config' => [],
        ]);

        $draft = ServiceRequest::factory()->create([
            'is_draft' => true,
            'title' => 'Test',
            'close_details' => 'Test',
            'priority_id' => $priority->getKey(),
        ]);

        $result = app(GetDraftStatus::class)->execute($draft);

        expect($result['next_instruction'])->toContain('skip_form_field')
            ->and($result['next_instruction'])->toContain('optional');
    });
});

describe('ClarifyingQuestions Instructions', function () {
    it('instruction for 0 questions shows 0/3 saved', function () {
        $draft = createCompleteDraft();

        $result = app(GetDraftStatus::class)->execute($draft);

        expect($result['draft_stage'])->toBe('clarifying_questions')
            ->and($result['next_instruction'])->toContain('(0/3 saved)');
    });

    it('instruction for 1 question mentions save_clarifying_question_answer', function () {
        $draft = createCompleteDraft();

        addClarifyingQuestions($draft, 1);

        $result = app(GetDraftStatus::class)->execute($draft);

        expect($result['draft_stage'])->toBe('clarifying_questions')
            ->and($result['next_instruction'])->toContain('save_clarifying_question_answer')
            ->and($result['next_instruction'])->toContain('(1/3 saved)');
    });

    it('instruction for 3 questions transitions to resolution stage', function () {
        $draft = createCompleteDraft();

        addClarifyingQuestions($draft, 3);

        $result = app(GetDraftStatus::class)->execute($draft);

        expect($result['draft_stage'])->toBe('resolution');
    });
});

describe('Resolution Instructions', function () {
    it('instruction when AI resolution is disabled mentions team member', function () {
        $settings = app(AiResolutionSettings::class);
        $settings->is_enabled = false;
        $settings->save();

        $draft = createCompleteDraft();

        addClarifyingQuestions($draft, 3);

        $result = app(GetDraftStatus::class)->execute($draft);

        expect($result['draft_stage'])->toBe('resolution')
            ->and($result['next_instruction'])->toContain('team member');
    });

    it('instruction when AI resolution enabled but not yet proposed mentions check_ai_resolution_validity', function () {
        $settings = app(AiResolutionSettings::class);
        $settings->is_enabled = true;
        $settings->save();

        $draft = createCompleteDraft();

        addClarifyingQuestions($draft, 3);

        $result = app(GetDraftStatus::class)->execute($draft);

        expect($result['draft_stage'])->toBe('resolution')
            ->and($result['next_instruction'])->toContain('check_ai_resolution_validity');
    });

    it('instruction when AI resolution already proposed mentions record_resolution_response', function () {
        $settings = app(AiResolutionSettings::class);
        $settings->is_enabled = true;
        $settings->save();

        $draft = createCompleteDraft();

        addClarifyingQuestions($draft, 3);

        $draft->serviceRequestUpdates()->createQuietly([
            'update' => 'AI proposed resolution',
            'update_type' => ServiceRequestUpdateType::AiResolutionProposed,
            'internal' => false,
            'created_by_id' => $draft->getKey(),
            'created_by_type' => $draft->getMorphClass(),
        ]);

        $result = app(GetDraftStatus::class)->execute($draft);

        expect($result['draft_stage'])->toBe('resolution')
            ->and($result['next_instruction'])->toContain('record_resolution_response');
    });
});

describe('Filled Form Fields Display', function () {
    it('includes label and value for filled fields', function () {
        $type = ServiceRequestType::factory()->create();
        $priority = ServiceRequestPriority::factory()->create(['type_id' => $type->getKey()]);

        $form = $type->form()->create(['name' => 'Test Form']);
        $field = $form->fields()->create([
            'label' => 'My Field Label',
            'type' => TextInputFormFieldBlock::type(),
            'is_required' => false,
            'config' => [],
        ]);

        $contact = Contact::factory()->create();

        $draft = ServiceRequest::factory()->create([
            'is_draft' => true,
            'title' => 'Test',
            'close_details' => 'Test',
            'priority_id' => $priority->getKey(),
            'respondent_id' => $contact->getKey(),
        ]);

        $submission = $form->submissions()->create();
        $draft->service_request_form_submission_id = $submission->getKey();
        $draft->saveQuietly();

        createFieldSubmission($submission, $field, 'My Field Value');

        $result = app(GetDraftStatus::class)->execute($draft);

        expect($result['filled_form_fields'])->toHaveCount(1)
            ->and($result['filled_form_fields'][0]['label'])->toBe('My Field Label')
            ->and($result['filled_form_fields'][0]['value'])->toBe('My Field Value');
    });

    it('signature field shows placeholder text', function () {
        $type = ServiceRequestType::factory()->create();
        $priority = ServiceRequestPriority::factory()->create(['type_id' => $type->getKey()]);

        $form = $type->form()->create(['name' => 'Test Form']);
        $field = $form->fields()->create([
            'label' => 'Signature',
            'type' => SignatureFormFieldBlock::type(),
            'is_required' => false,
            'config' => [],
        ]);

        $contact = Contact::factory()->create();

        $draft = ServiceRequest::factory()->create([
            'is_draft' => true,
            'title' => 'Test',
            'close_details' => 'Test',
            'priority_id' => $priority->getKey(),
            'respondent_id' => $contact->getKey(),
        ]);

        $submission = $form->submissions()->create();
        $draft->service_request_form_submission_id = $submission->getKey();
        $draft->saveQuietly();

        createFieldSubmission($submission, $field, 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAUA...');

        $result = app(GetDraftStatus::class)->execute($draft);

        expect($result['filled_form_fields'][0]['value'])->toBe('[Signature provided]');
    });

    it('checkbox field true shows Yes', function () {
        $type = ServiceRequestType::factory()->create();
        $priority = ServiceRequestPriority::factory()->create(['type_id' => $type->getKey()]);

        $form = $type->form()->create(['name' => 'Test Form']);
        $field = $form->fields()->create([
            'label' => 'Checkbox',
            'type' => CheckboxFormFieldBlock::type(),
            'is_required' => false,
            'config' => [],
        ]);

        $contact = Contact::factory()->create();

        $draft = ServiceRequest::factory()->create([
            'is_draft' => true,
            'title' => 'Test',
            'close_details' => 'Test',
            'priority_id' => $priority->getKey(),
            'respondent_id' => $contact->getKey(),
        ]);

        $submission = $form->submissions()->create();
        $draft->service_request_form_submission_id = $submission->getKey();
        $draft->saveQuietly();

        createFieldSubmission($submission, $field, true);

        $result = app(GetDraftStatus::class)->execute($draft);

        expect($result['filled_form_fields'][0]['value'])->toBe('Yes');
    });

    it('checkbox field false shows No', function () {
        $type = ServiceRequestType::factory()->create();
        $priority = ServiceRequestPriority::factory()->create(['type_id' => $type->getKey()]);

        $form = $type->form()->create(['name' => 'Test Form']);
        $field = $form->fields()->create([
            'label' => 'Checkbox',
            'type' => CheckboxFormFieldBlock::type(),
            'is_required' => false,
            'config' => [],
        ]);

        $contact = Contact::factory()->create();

        $draft = ServiceRequest::factory()->create([
            'is_draft' => true,
            'title' => 'Test',
            'close_details' => 'Test',
            'priority_id' => $priority->getKey(),
            'respondent_id' => $contact->getKey(),
        ]);

        $submission = $form->submissions()->create();
        $draft->service_request_form_submission_id = $submission->getKey();
        $draft->saveQuietly();

        createFieldSubmission($submission, $field, false);

        $result = app(GetDraftStatus::class)->execute($draft);

        expect($result['filled_form_fields'][0]['value'])->toBe('No');
    });
});

function createBasicDraft(array $attributes = []): ServiceRequest
{
    $type = ServiceRequestType::factory()->create();
    $priority = ServiceRequestPriority::factory()->create(['type_id' => $type->getKey()]);

    return ServiceRequest::factory()->create(array_merge([
        'is_draft' => true,
        'title' => 'Test Title',
        'close_details' => 'Test Description',
        'priority_id' => $priority->getKey(),
    ], $attributes));
}

function createCompleteDraft(): ServiceRequest
{
    $type = ServiceRequestType::factory()->create();
    $priority = ServiceRequestPriority::factory()->create(['type_id' => $type->getKey()]);
    $contact = Contact::factory()->create();

    return ServiceRequest::factory()->create([
        'is_draft' => true,
        'title' => 'Test Title',
        'close_details' => 'Test Description',
        'priority_id' => $priority->getKey(),
        'respondent_id' => $contact->getKey(),
    ]);
}

function addClarifyingQuestions(ServiceRequest $draft, int $count): void
{
    for ($i = 0; $i < $count; $i++) {
        $draft->serviceRequestUpdates()->createQuietly([
            'update' => "Question {$i}",
            'update_type' => ServiceRequestUpdateType::ClarifyingQuestion,
            'internal' => false,
            'created_by_id' => $draft->getKey(),
            'created_by_type' => $draft->getMorphClass(),
        ]);
    }
}

function createFieldSubmission($submission, $field, mixed $value): void
{
    DB::table('service_request_form_field_submission')->insert([
        'id' => Str::uuid()->toString(),
        'service_request_form_submission_id' => $submission->getKey(),
        'service_request_form_field_id' => $field->getKey(),
        'response' => is_bool($value) ? ($value ? '1' : '0') : $value,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
}
