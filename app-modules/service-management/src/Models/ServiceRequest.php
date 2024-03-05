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

namespace AidingApp\ServiceManagement\Models;

use App\Models\User;
use DateTimeInterface;
use App\Models\BaseModel;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;
use App\Models\Contracts\Educatable;
use AidingApp\Contact\Models\Contact;
use Kirschbaum\PowerJoins\PowerJoins;
use AidingApp\Division\Models\Division;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Scopes\LicensedToEducatable;
use App\Models\Concerns\BelongsToEducatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use AidingApp\StudentDataModel\Models\Student;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;
use AidingApp\Notification\Models\OutboundDeliverable;
use AidingApp\Notification\Models\Contracts\Subscribable;
use AidingApp\ServiceManagement\Enums\SlaComplianceStatus;
use Illuminate\Database\UniqueConstraintViolationException;
use AidingApp\Audit\Models\Concerns\Auditable as AuditableTrait;
use AidingApp\ServiceManagement\Enums\ServiceRequestUpdateDirection;
use AidingApp\Interaction\Models\Concerns\HasManyMorphedInteractions;
use AidingApp\ServiceManagement\Enums\ServiceRequestAssignmentStatus;
use AidingApp\Notification\Models\Contracts\CanTriggerAutoSubscription;
use AidingApp\ServiceManagement\Enums\SystemServiceRequestClassification;
use AidingApp\ServiceManagement\Exceptions\ServiceRequestNumberExceededReRollsException;
use AidingApp\ServiceManagement\Services\ServiceRequestNumber\Contracts\ServiceRequestNumberGenerator;

/**
 * @property-read Student|Contact $respondent
 *
 * @mixin IdeHelperServiceRequest
 */
class ServiceRequest extends BaseModel implements Auditable, CanTriggerAutoSubscription
{
    use BelongsToEducatable;
    use SoftDeletes;
    use PowerJoins;
    use AuditableTrait;
    use HasManyMorphedInteractions;
    use HasRelationships;

    protected $fillable = [
        'respondent_type',
        'respondent_id',
        'division_id',
        'status_id',
        'priority_id',
        'assigned_to_id',
        'close_details',
        'res_details',
        'created_by_id',
        'status_updated_at',
    ];

    protected $casts = [
        'status_updated_at' => 'immutable_datetime',
    ];

    public function save(array $options = [])
    {
        $attempts = 0;

        do {
            try {
                DB::beginTransaction();

                $save = parent::save($options);
            } catch (UniqueConstraintViolationException $e) {
                $attempts++;
                $save = false;

                if ($attempts < 3) {
                    $this->service_request_number = app(ServiceRequestNumberGenerator::class)->generate();
                }

                DB::rollBack();

                if ($attempts >= 3) {
                    throw new ServiceRequestNumberExceededReRollsException(
                        previous: $e,
                    );
                }

                continue;
            }

            DB::commit();

            break;
        } while ($attempts < 3);

        return $save;
    }

    public function getSubscribable(): ?Subscribable
    {
        return $this->respondent instanceof Subscribable ? $this->respondent : null;
    }

    /** @return MorphTo<Educatable> */
    public function respondent(): MorphTo
    {
        return $this->morphTo(
            name: 'respondent',
            type: 'respondent_type',
            id: 'respondent_id',
        );
    }

    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class, 'division_id');
    }

    public function serviceRequestUpdates(): HasMany
    {
        return $this->hasMany(ServiceRequestUpdate::class, 'service_request_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(ServiceRequestStatus::class);
    }

    public function priority(): BelongsTo
    {
        return $this->belongsTo(ServiceRequestPriority::class);
    }

    public function serviceRequestFormSubmission(): BelongsTo
    {
        return $this->belongsTo(ServiceRequestFormSubmission::class, 'service_request_form_submission_id');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(ServiceRequestAssignment::class);
    }

    public function assignedTo(): HasOne
    {
        return $this->hasOne(ServiceRequestAssignment::class)
            ->latest('assigned_at')
            ->where('status', ServiceRequestAssignmentStatus::Active);
    }

    public function initialAssignment(): HasOne
    {
        return $this->hasOne(ServiceRequestAssignment::class)
            ->oldest('assigned_at');
    }

    public function histories(): HasMany
    {
        return $this->hasMany(ServiceRequestHistory::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeOpen(Builder $query): void
    {
        $query->whereIn(
            'status_id',
            ServiceRequestStatus::where('classification', SystemServiceRequestClassification::Open)->pluck('id')
        );
    }

    public function latestInboundServiceRequestUpdate(): HasOne
    {
        return $this->hasOne(ServiceRequestUpdate::class, 'service_request_id')
            ->ofMany([
                'created_at' => 'max',
            ], function (Builder $query) {
                $query
                    ->where('direction', ServiceRequestUpdateDirection::Inbound)
                    ->where('internal', false);
            });
    }

    public function latestOutboundServiceRequestUpdate(): HasOne
    {
        return $this->hasOne(ServiceRequestUpdate::class, 'service_request_id')
            ->ofMany([
                'created_at' => 'max',
            ], function (Builder $query) {
                $query
                    ->where('direction', ServiceRequestUpdateDirection::Outbound)
                    ->where('internal', false);
            });
    }

    public function deliverables(): MorphMany
    {
        return $this->morphMany(OutboundDeliverable::class, 'related');
    }

    public function getLatestResponseSeconds(): int
    {
        if (! $this->latestInboundServiceRequestUpdate) {
            return $this->created_at->diffInSeconds(now());
        }

        if (
            $this->isResolved() &&
            ($resolvedAt = $this->getResolvedAt())->isAfter($this->latestInboundServiceRequestUpdate->created_at)
        ) {
            return $resolvedAt->diffInSeconds($this->latestInboundServiceRequestUpdate->created_at);
        }

        if (
            $this->latestOutboundServiceRequestUpdate &&
            $this->latestOutboundServiceRequestUpdate->created_at->isAfter(
                $this->latestInboundServiceRequestUpdate->created_at,
            )
        ) {
            return $this->latestOutboundServiceRequestUpdate->created_at->diffInSeconds(
                $this->latestInboundServiceRequestUpdate->created_at,
            );
        }

        return $this->latestInboundServiceRequestUpdate->created_at->diffInSeconds();
    }

    public function getResolutionSeconds(): int
    {
        if (! $this->isResolved()) {
            return $this->created_at->diffInSeconds();
        }

        return $this->created_at->diffInSeconds($this->getResolvedAt());
    }

    public function getSlaResponseSeconds(): ?int
    {
        return $this->priority?->sla?->response_seconds;
    }

    public function getSlaResolutionSeconds(): ?int
    {
        return $this->priority?->sla?->resolution_seconds;
    }

    public function getResponseSlaComplianceStatus(): ?SlaComplianceStatus
    {
        $slaResponseSeconds = $this->getSlaResponseSeconds();

        if (! $slaResponseSeconds) {
            return null;
        }

        $latestResponseSeconds = $this->getLatestResponseSeconds();

        return $latestResponseSeconds <= $slaResponseSeconds
            ? SlaComplianceStatus::Compliant
            : SlaComplianceStatus::NonCompliant;
    }

    public function getResolutionSlaComplianceStatus(): ?SlaComplianceStatus
    {
        $slaResolutionSeconds = $this->getSlaResolutionSeconds();

        if (! $slaResolutionSeconds) {
            return null;
        }

        $resolutionSeconds = $this->getResolutionSeconds();

        return ($resolutionSeconds <= $slaResolutionSeconds)
            ? SlaComplianceStatus::Compliant
            : SlaComplianceStatus::NonCompliant;
    }

    public function getResolvedAt(): CarbonInterface
    {
        return $this->status_updated_at ?? $this->updated_at ?? $this->created_at;
    }

    public function isResolved(): bool
    {
        return $this->status->classification === SystemServiceRequestClassification::Closed;
    }

    protected static function booted(): void
    {
        static::addGlobalScope('licensed', function (Builder $builder) {
            $builder->tap(new LicensedToEducatable('respondent'));
        });
    }

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format(config('project.datetime_format') ?? 'Y-m-d H:i:s');
    }
}
