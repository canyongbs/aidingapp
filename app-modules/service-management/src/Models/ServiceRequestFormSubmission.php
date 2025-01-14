<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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

namespace AidingApp\ServiceManagement\Models;

use AidingApp\Contact\Models\Contact;
use AidingApp\Form\Enums\FormSubmissionRequestDeliveryMethod;
use AidingApp\Form\Enums\FormSubmissionStatus;
use AidingApp\Form\Models\Submission;
use App\Models\Scopes\LicensedToEducatable;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property Contact|null $author
 *
 * @mixin IdeHelperServiceRequestFormSubmission
 */
class ServiceRequestFormSubmission extends Submission
{
    protected $fillable = [
        'canceled_at',
        'description',
        'request_method',
        'request_note',
        'service_request_form_id',
        'submitted_at',
    ];

    protected $casts = [
        'canceled_at' => 'immutable_datetime',
        'request_method' => FormSubmissionRequestDeliveryMethod::class,
        'submitted_at' => 'immutable_datetime',
    ];

    public function serviceRequest(): HasOne
    {
        return $this->hasOne(ServiceRequest::class, 'service_request_form_submission_id');
    }

    public function submissible(): BelongsTo
    {
        return $this->belongsTo(ServiceRequestForm::class, 'service_request_form_id');
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function priority(): BelongsTo
    {
        return $this->belongsTo(ServiceRequestPriority::class, 'service_request_priority_id');
    }

    public function fields(): BelongsToMany
    {
        return $this->belongsToMany(
            ServiceRequestFormField::class,
            'service_request_form_field_submission',
            'service_request_form_submission_id',
            'service_request_form_field_id',
        )
            ->withPivot(['id', 'response']);
    }

    public function deliverRequest(): void
    {
        $this->request_method->deliver($this);
    }

    public function scopeRequested(Builder $query): Builder
    {
        return $query->notSubmitted()->notCanceled();
    }

    public function scopeSubmitted(Builder $query): Builder
    {
        return $query->whereNotNull('submitted_at');
    }

    public function scopeCanceled(Builder $query): Builder
    {
        return $query->notSubmitted()->whereNotNull('canceled_at');
    }

    public function scopeNotSubmitted(Builder $query): Builder
    {
        return $query->whereNull('submitted_at');
    }

    public function scopeNotCanceled(Builder $query): Builder
    {
        return $query->whereNull('canceled_at');
    }

    public function getStatus(): FormSubmissionStatus
    {
        if ($this->submitted_at) {
            return FormSubmissionStatus::Submitted;
        }

        if ($this->canceled_at) {
            return FormSubmissionStatus::Canceled;
        }

        return FormSubmissionStatus::Requested;
    }

    protected static function booted(): void
    {
        static::addGlobalScope('licensed', function (Builder $builder) {
            $builder->tap(new LicensedToEducatable('author'));
        });
    }
}
