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

namespace App\Http\Requests\Api\V1\Users;

use AidingApp\Authorization\Models\Role;
use AidingApp\Department\Models\Department;
use App\Models\Authenticatable;
use App\Rules\EmailNotInUseOrSoftDeleted;
use Illuminate\Database\Query\Expression;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Collection;
use Illuminate\Validation\Validator;

class CreateUserRequest extends FormRequest
{
    protected ?Department $resolvedDepartment = null;

    /** @var Collection<int, Role>|null */
    protected ?Collection $resolvedRoles = null;

    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', new EmailNotInUseOrSoftDeleted()],
            'is_external' => ['required', 'boolean'],
            'job_title' => ['nullable', 'string', 'max:255'],
            'phone_number' => ['nullable', 'string', 'max:255'],
            'work_number' => ['nullable', 'string', 'max:255'],
            'work_extension' => ['nullable', 'integer'],
            'mobile' => ['nullable', 'string', 'max:255'],
            'department' => ['nullable', 'string', 'max:255'],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['string', 'max:255'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $this->validateDepartment($validator);
            $this->validateRoles($validator);
        });
    }

    public function getResolvedDepartment(): ?Department
    {
        return $this->resolvedDepartment;
    }

    /**
     * @return Collection<int, Role>|null
     */
    public function getResolvedRoles(): ?Collection
    {
        return $this->resolvedRoles;
    }

    protected function validateDepartment(Validator $validator): void
    {
        $departmentName = $this->input('department');

        if (empty($departmentName)) {
            return;
        }

        $this->resolvedDepartment = Department::query()
            ->where(new Expression('lower(name)'), 'like', '%' . strtolower($departmentName) . '%')
            ->first();

        if (! $this->resolvedDepartment) {
            $validator->errors()->add(
                'department',
                'The specified department does not exist.'
            );
        }
    }

    protected function validateRoles(Validator $validator): void
    {
        $roleNames = $this->input('roles', []);

        if (empty($roleNames)) {
            return;
        }

        $adminRoleNames = [
            Authenticatable::SUPER_ADMIN_ROLE,
            Authenticatable::PARTNER_ADMIN_ROLE,
            Authenticatable::AI_ADMIN_ROLE,
        ];

        $loweredAdminNames = array_map('strtolower', $adminRoleNames);

        foreach ($roleNames as $index => $roleName) {
            if (in_array(strtolower($roleName), $loweredAdminNames, true)) {
                $validator->errors()->add(
                    "roles.{$index}",
                    'Admin roles cannot be assigned through this endpoint.'
                );

                return;
            }
        }

        $this->resolvedRoles = Role::query()
            ->whereRaw('LOWER(name) IN (' . implode(',', array_fill(0, count($roleNames), '?')) . ')', array_map('strtolower', $roleNames))
            ->get();

        if ($this->resolvedRoles->count() !== count($roleNames)) {
            $foundNames = $this->resolvedRoles->pluck('name')->map(fn($name) => strtolower($name))->toArray();

            foreach ($roleNames as $index => $roleName) {
                if (! in_array(strtolower($roleName), $foundNames, true)) {
                    $validator->errors()->add(
                        "roles.{$index}",
                        "The role '{$roleName}' does not exist."
                    );
                }
            }
        }
    }
}
