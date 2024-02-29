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

namespace AdvisingApp\Survey\Models;

use App\Models\User;
use AdvisingApp\Contact\Models\Contact;
use AdvisingApp\Form\Models\Submission;
use Illuminate\Database\Eloquent\Builder;
use AdvisingApp\Form\Enums\FormSubmissionStatus;
use AdvisingApp\StudentDataModel\Models\Student;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use AdvisingApp\Form\Enums\FormSubmissionRequestDeliveryMethod;
use AdvisingApp\StudentDataModel\Models\Scopes\LicensedToEducatable;

/**
 * @property Student|Contact|null $author
 *
 * @mixin IdeHelperSurveySubmission
 */
class SurveySubmission extends Submission
{
    protected $fillable = [
        'canceled_at',
        'survey_id',
        'request_method',
        'request_note',
        'submitted_at',
    ];

    protected $casts = [
        'submitted_at' => 'immutable_datetime',
        'canceled_at' => 'immutable_datetime',
        'request_method' => FormSubmissionRequestDeliveryMethod::class,
    ];

    public function submissible(): BelongsTo
    {
        return $this
            ->belongsTo(Survey::class, 'survey_id');
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function fields(): BelongsToMany
    {
        return $this->belongsToMany(
            SurveyField::class,
            'survey_field_submission',
            'submission_id',
            'field_id',
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
