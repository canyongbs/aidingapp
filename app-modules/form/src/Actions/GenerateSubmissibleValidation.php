<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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

namespace AidingApp\Form\Actions;

use AidingApp\Form\Models\Submissible;
use AidingApp\Form\Models\SubmissibleField;
use AidingApp\IntegrationGoogleRecaptcha\Rules\RecaptchaTokenValid;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;

class GenerateSubmissibleValidation
{
    public function __invoke(Submissible $submissible): array
    {
        $rules = [];

        if ($submissible->recaptcha_enabled === true) {
            $rules['recaptcha-token'] = [new RecaptchaTokenValid()];
        }

        if ($submissible->is_wizard) {
            return array_merge($rules, $this->wizardRules($submissible));
        }

        $blocks = app(ResolveBlockRegistry::class)($submissible);

        return array_merge($rules, $this->fields($blocks, $submissible->fields));
    }

    public function fields(array $blocks, Collection $fields): array
    {
        return $fields
            ->mapWithKeys(function (SubmissibleField $field) use ($blocks) {
                $rules = collect();

                if ($field->is_required) {
                    $rules->push('required');
                }

                if (is_null($field->type)) {
                    return [];
                }

                return [
                    $field->getKey() => $rules
                        ->merge($blocks[$field->type]::getValidationRules($field))
                        ->all(),
                ];
            })
            ->all();
    }

    public function wizardRules(Submissible $submissible): array
    {
        $rules = collect();

        $blocks = app(ResolveBlockRegistry::class)($submissible, true);

        foreach ($submissible->steps as $step) {
            $rules = $rules->merge(
                Arr::prependKeysWith(
                    $this->fields($blocks, $step->fields),
                    prependWith: "{$step->label}.",
                ),
            );
        }

        return $rules->all();
    }
}
