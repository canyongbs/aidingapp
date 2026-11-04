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

namespace AidingApp\Contact\Actions;

use AidingApp\Contact\DataTransferObjects\ContactDataObject;
use AidingApp\Contact\Models\Contact;
use AidingApp\Contact\Models\ContactType;
use AidingApp\Contact\Models\Organization;
use AidingApp\Contact\Support\ContactFullName;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Spatie\LaravelData\Optional;

class SaveContact
{
    /**
     * Creates a contact, or updates the given one. Only attributes present on the data object are
     * written when updating, and an omitted type falls back to the default contact type when creating.
     *
     * An existing contact is re-read under a row lock, so the full name is derived from its current
     * names and concurrent partial updates cannot leave the name columns inconsistent.
     *
     * @throws ValidationException
     */
    public function __invoke(ContactDataObject $data, ?Contact $existing = null): Contact
    {
        [$type, $organization] = $this->resolveRelationships($data);

        try {
            return DB::transaction(function () use ($data, $existing, $type, $organization): Contact {
                $contact = $existing === null
                    ? new Contact()
                    : Contact::query()->withoutArchived()->whereKey($existing->getKey())->lockForUpdate()->firstOrFail();

                $attributes = Arr::except($data->toArray(), ['type', 'organization']);

                $contact->fill([...$attributes, ...$this->fullNameAttributes($contact, $attributes)]);

                $type ??= $existing === null ? ContactType::resolveDefault() : null;

                if ($type !== null) {
                    $contact->type()->associate($type);
                }

                if ($existing === null || ! $data->organization instanceof Optional) {
                    $contact->organization()->associate($organization);
                }

                $contact->save();

                return $contact;
            });
        } catch (UniqueConstraintViolationException $exception) {
            // A concurrent request can claim the email after the request was validated.
            if (! str_contains($exception->getMessage(), 'contacts_email_unique')) {
                throw $exception;
            }

            throw ValidationException::withMessages([
                'email' => __('validation.unique', ['attribute' => 'email']),
            ]);
        }
    }

    /**
     * Types and organizations are referenced by name, ignoring case. Every problem is reported together.
     *
     *
     * @throws ValidationException
     *
     * @return array{0: ?ContactType, 1: ?Organization}
     */
    private function resolveRelationships(ContactDataObject $data): array
    {
        $errors = [];
        $type = null;
        $organization = null;

        if (is_string($data->type)) {
            $types = ContactType::query()
                ->where(new Expression('lower(name)'), '=', Str::lower($data->type))
                ->limit(2)
                ->get();

            if ($types->isEmpty()) {
                $errors['type'] = 'The specified contact type does not exist.';
            } elseif ($types->count() > 1) {
                $errors['type'] = 'The specified contact type name is ambiguous.';
            } else {
                $type = $types->first();
            }
        }

        if (is_string($data->organization)) {
            // `organizations.name` is `citext` with a unique index, so a plain comparison is case-insensitive and indexed.
            $organization = Organization::query()
                ->withoutArchived()
                ->where('name', $data->organization)
                ->first();

            if ($organization === null) {
                $errors['organization'] = 'The specified organization does not exist.';
            }
        }

        if ($errors !== []) {
            throw ValidationException::withMessages($errors);
        }

        return [$type, $organization];
    }

    /**
     * @param array<string, mixed> $attributes
     *
     * @throws ValidationException
     *
     * @return array<string, string>
     */
    private function fullNameAttributes(Contact $contact, array $attributes): array
    {
        if (! array_key_exists('first_name', $attributes) && ! array_key_exists('last_name', $attributes)) {
            return [];
        }

        $firstName = (string) ($attributes['first_name'] ?? $contact->first_name);
        $lastName = (string) ($attributes['last_name'] ?? $contact->last_name);

        if (ContactFullName::exceedsMaxLength($firstName, $lastName)) {
            throw ValidationException::withMessages(
                collect(['first_name', 'last_name'])
                    ->filter(fn (string $field): bool => array_key_exists($field, $attributes))
                    ->mapWithKeys(fn (string $field): array => [$field => ContactFullName::tooLongMessage()])
                    ->all(),
            );
        }

        return ['full_name' => ContactFullName::make($firstName, $lastName)];
    }
}
