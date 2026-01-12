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
      of the licensor in the software. Any use of the licensor's trademarks is subject
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

namespace AidingApp\Ai\Validators;

use AidingApp\ServiceManagement\Models\ServiceRequestFormField;
use AidingApp\ServiceManagement\Models\ServiceRequestType;

class InternalContentValidator
{
    public const TYPE_FIELD_RESPONSE = 'field_response';

    public const TYPE_TYPE_SELECTION = 'type_selection';

    public const TYPE_PRIORITY_SELECTION = 'priority_selection';

    public const TYPE_WIDGET_CANCELLED = 'widget_cancelled';

    public const TYPE_WIDGET_ERROR = 'widget_error';

    protected const ALLOWED_TYPES = [
        self::TYPE_FIELD_RESPONSE,
        self::TYPE_TYPE_SELECTION,
        self::TYPE_PRIORITY_SELECTION,
        self::TYPE_WIDGET_CANCELLED,
        self::TYPE_WIDGET_ERROR,
    ];

    public function validate(?array $internalContent): InternalContentValidationResult
    {
        if ($internalContent === null) {
            return InternalContentValidationResult::success();
        }

        if (! isset($internalContent['type'])) {
            return InternalContentValidationResult::failure(['type' => 'The type field is required.']);
        }

        if (! in_array($internalContent['type'], self::ALLOWED_TYPES, true)) {
            return InternalContentValidationResult::failure([
                'type' => 'The type must be one of: ' . implode(', ', self::ALLOWED_TYPES) . '.',
            ]);
        }

        return match ($internalContent['type']) {
            self::TYPE_FIELD_RESPONSE => $this->validateFieldResponse($internalContent),
            self::TYPE_TYPE_SELECTION => $this->validateTypeSelection($internalContent),
            self::TYPE_PRIORITY_SELECTION => $this->validatePrioritySelection($internalContent),
            self::TYPE_WIDGET_CANCELLED, self::TYPE_WIDGET_ERROR => InternalContentValidationResult::success(),
        };
    }

    protected function validateFieldResponse(array $internalContent): InternalContentValidationResult
    {
        $errors = [];

        if (! isset($internalContent['field_id'])) {
            $errors['field_id'] = 'The field_id is required for field_response type.';
        } elseif (! ServiceRequestFormField::where('id', $internalContent['field_id'])->exists()) {
            $errors['field_id'] = 'The specified field does not exist.';
        }

        if (! array_key_exists('value', $internalContent)) {
            $errors['value'] = 'The value is required for field_response type.';
        }

        if (! empty($errors)) {
            return InternalContentValidationResult::failure($errors);
        }

        return InternalContentValidationResult::success();
    }

    protected function validateTypeSelection(array $internalContent): InternalContentValidationResult
    {
        $errors = [];

        if (! isset($internalContent['type_id'])) {
            $errors['type_id'] = 'The type_id is required for type_selection type.';
        } else {
            $serviceRequestType = ServiceRequestType::where('id', $internalContent['type_id'])
                ->whereHas('form')
                ->first();

            if (! $serviceRequestType) {
                $errors['type_id'] = 'The specified service request type does not exist or is not submittable.';
            }
        }

        if (! empty($errors)) {
            return InternalContentValidationResult::failure($errors);
        }

        return InternalContentValidationResult::success();
    }

    protected function validatePrioritySelection(array $internalContent): InternalContentValidationResult
    {
        $errors = [];

        if (! isset($internalContent['priority_id'])) {
            $errors['priority_id'] = 'The priority_id is required for priority_selection type.';
        } elseif (! \AidingApp\ServiceManagement\Models\ServiceRequestPriority::where('id', $internalContent['priority_id'])->exists()) {
            $errors['priority_id'] = 'The specified priority does not exist.';
        }

        if (! empty($errors)) {
            return InternalContentValidationResult::failure($errors);
        }

        return InternalContentValidationResult::success();
    }
}
