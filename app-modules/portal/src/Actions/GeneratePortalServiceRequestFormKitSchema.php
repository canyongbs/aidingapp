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

namespace AidingApp\Portal\Actions;

use AidingApp\Form\Actions\GenerateFormKitSchema;
use AidingApp\Form\Models\Submissible;
use AidingApp\Form\Models\SubmissibleStep;
use App\Features\StepDescriptionFeature;

/**
 * @phpstan-import-type FormKitBlocks from GenerateFormKitSchema
 * @phpstan-import-type FormKitChildren from GenerateFormKitSchema
 */
class GeneratePortalServiceRequestFormKitSchema extends GenerateFormKitSchema
{
    /**
     * @param FormKitBlocks $blocks
     *
     * @return FormKitChildren
     */
    public function wizardContent(array $blocks, Submissible $submissible): array
    {
        $isMultiStep = $submissible->steps->count() > 1;

        return [
            [
                '$el' => 'div',
                'attrs' => [
                    'class' => 'form-body',
                ],
                'children' => [
                    ...$submissible->steps->map(fn (SubmissibleStep $step): array => [
                        '$el' => 'section',
                        'attrs' => [
                            'style' => [
                                'if' => '$activeStep !== "' . $step->label . '"',
                                'then' => 'display: none;',
                            ],
                        ],
                        'children' => [
                            ...($isMultiStep ? [$this->stepHeader($step)] : []),
                            [
                                '$formkit' => 'group',
                                'id' => $step->label,
                                'name' => $step->label,
                                'children' => $this->content(
                                    $blocks,
                                    is_array($step->content) ? ($step->content['content'] ?? []) : [],
                                    $step->fields->keyBy('id'),
                                ),
                            ],
                        ],
                    ]),
                    [
                        '$el' => 'nav',
                        'attrs' => [
                            'class' => 'step-nav',
                            'aria-label' => 'Form steps',
                        ],
                        'children' => [
                            [
                                '$el' => 'div',
                                'attrs' => [
                                    'class' => 'step-nav__back',
                                    'style' => $isMultiStep ? null : 'display: none;',
                                ],
                                'children' => [
                                    [
                                        '$formkit' => 'button',
                                        'disabled' => '$currentStep === 1',
                                        'onClick' => '$setStep(-1)',
                                        'children' => 'Back',
                                    ],
                                ],
                            ],
                            [
                                '$el' => 'div',
                                'attrs' => [
                                    'class' => 'step-progress',
                                    'aria-live' => 'polite',
                                    'style' => $isMultiStep ? null : 'display: none;',
                                ],
                                'children' => [
                                    [
                                        '$el' => 'p',
                                        'attrs' => [
                                            'class' => 'step-progress__label',
                                        ],
                                        'children' => '$: "Step " + $currentStep + " of " + $totalSteps',
                                    ],
                                    [
                                        '$el' => 'p',
                                        'attrs' => [
                                            'class' => 'step-progress__percent',
                                        ],
                                        'children' => '$percentComplete + "% complete"',
                                    ],
                                ],
                            ],
                            [
                                '$el' => 'div',
                                'attrs' => [
                                    'class' => 'step-nav__next',
                                ],
                                'children' => [
                                    [
                                        '$el' => 'div',
                                        'attrs' => [
                                            'class' => 'step-nav__next-btn',
                                            'style' => [
                                                'if' => '$currentStep === $totalSteps',
                                                'then' => 'display: none;',
                                            ],
                                        ],
                                        'children' => [
                                            [
                                                '$formkit' => 'button',
                                                'onClick' => '$setStep(1)',
                                                'children' => 'Next',
                                            ],
                                        ],
                                    ],
                                    [
                                        '$el' => 'div',
                                        'attrs' => [
                                            'style' => [
                                                'if' => '$currentStep !== $totalSteps',
                                                'then' => 'display: none;',
                                            ],
                                        ],
                                        'children' => [
                                            [
                                                '$formkit' => 'submit',
                                                'label' => 'Submit',
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function stepHeader(SubmissibleStep $step): array
    {
        return [
            '$el' => 'div',
            'attrs' => [
                'class' => 'wizard-header',
            ],
            'children' => [
                [
                    '$el' => 'h2',
                    'attrs' => [
                        'class' => 'wizard-title',
                    ],
                    'children' => $step->label,
                ],
                ...((StepDescriptionFeature::active() && filled($step->description)) ? [[
                    '$el' => 'p',
                    'attrs' => [
                        'class' => 'wizard-description',
                    ],
                    'children' => $step->description,
                ]] : []),
            ],
        ];
    }
}
