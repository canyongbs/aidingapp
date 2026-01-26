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

namespace AidingApp\ServiceManagement\Models;

use AidingApp\Ai\Models\PortalAssistantThread;
use AidingApp\Audit\Models\Concerns\Auditable as AuditableTrait;
use AidingApp\Contact\Models\Contact;
use AidingApp\Division\Models\Division;
use AidingApp\ServiceManagement\Database\Factories\ServiceRequestFactory;
use AidingApp\ServiceManagement\Enums\ServiceRequestAssignmentStatus;
use AidingApp\ServiceManagement\Enums\SlaComplianceStatus;
use AidingApp\ServiceManagement\Enums\SystemServiceRequestClassification;
use AidingApp\ServiceManagement\Exceptions\ServiceRequestNumberExceededReRollsException;
use AidingApp\ServiceManagement\Models\MediaCollections\UploadsMediaCollection;
use AidingApp\ServiceManagement\Observers\ServiceRequestObserver;
use AidingApp\ServiceManagement\Services\ServiceRequestNumber\Contracts\ServiceRequestNumberGenerator;
use App\Models\Authenticatable;
use App\Models\BaseModel;
use App\Models\Concerns\BelongsToEducatable;
use App\Models\User;
use Carbon\CarbonInterface;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;

/**
 * @property-read Contact $respondent
 *
 * @mixin IdeHelperServiceRequest
 */
#[ObservedBy([ServiceRequestObserver::class])]
class ServiceRequest extends BaseModel implements Auditable, HasMedia
{
    use BelongsToEducatable;
    use SoftDeletes;
    use AuditableTrait;
    use HasRelationships;
    use InteractsWithMedia;

    /** @use HasFactory<ServiceRequestFactory> */
    use HasFactory;

    protected $fillable = [
        'respondent_id',
        'division_id',
        'status_id',
        'priority_id',
        'assigned_to_id',
        'close_details',
        'res_details',
        'created_by_id',
        'status_updated_at',
        'title',
        'service_request_form_submission_id',
        'survey_sent_at',
        'reminder_sent_at',
        'ai_resolution_confidence_score',
        'is_ai_resolution_attempted',
        'is_ai_resolution_successful',
        'is_draft',
        'portal_assistant_thread_id',
    ];

    protected $casts = [
        'status_updated_at' => 'immutable_datetime',
        'time_to_resolution' => 'integer',
        'survey_sent_at' => 'datetime',
        'reminder_sent_at' => 'datetime',
        'ai_resolution_confidence_score' => 'integer',
        'is_ai_resolution_attempted' => 'boolean',
        'is_ai_resolution_successful' => 'boolean',
        'is_draft' => 'boolean',
    ];

    public function registerMediaCollections(): void
    {
        $this->mediaCollections[] = UploadsMediaCollection::create()
            ->maxFileSizeInMB(10)
            ->maxNumberOfFiles(6)
            ->mimes([
                'application/pdf' => ['pdf'],
                'application/vnd.ms-excel' => ['xls'],
                'application/vnd.ms-powerpoint' => ['ppt'],
                'application/vnd.ms-word' => ['doc'],
                'application/vnd.openxmlformats-officedocument.presentationml.presentation' => ['pptx'],
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => ['xlsx'],
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => ['docx'],
                'image/jpeg' => ['jpg', 'jpeg'],
                'image/pdf' => ['pdf'],
                'image/png' => ['png'],
                'text/csv' => ['csv'],
                'text/markdown' => ['md', 'markdown', 'mkd'],
                'text/plain' => ['txt', 'text'],
                'application/octet-stream' => ['log'],
                '.log' => ['log'],
                'video/mp4' => ['mp4'],
                'video/webm' => ['webm'],
                'video/ogg' => ['ogg'],
                'video/quicktime' => ['quicktime'],
                'video/x-msvideo' => ['x-msvideo'],
            ]);
    }

    public function save(array $options = [])
    {
        $attempts = 0;
        $save = false;

        do {
            try {
                DB::transaction(function () use (&$save, $options) {
                    $save = parent::save($options);

                    return $save;
                });

                break;
            } catch (UniqueConstraintViolationException $exception) {
                report($exception);

                $attempts++;

                if ($attempts < 3) {
                    $this->service_request_number = app(ServiceRequestNumberGenerator::class)->generate();
                } else {
                    throw new ServiceRequestNumberExceededReRollsException(
                        previous: $exception,
                    );
                }

                continue;
            }
        } while (! $save && $attempts < 3);

        return $save;
    }

    /** @return BelongsTo<Contact, $this> */
    public function respondent(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'respondent_id');
    }

    /**
     * @return BelongsTo<Division, $this>
     */
    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class, 'division_id');
    }

    /**
     * @return HasMany<ServiceRequestUpdate, $this>
     */
    public function serviceRequestUpdates(): HasMany
    {
        return $this->hasMany(ServiceRequestUpdate::class, 'service_request_id');
    }

    /**
     * @return BelongsTo<ServiceRequestStatus, $this>
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(ServiceRequestStatus::class)->withTrashed();
    }

    /**
     * @return BelongsTo<ServiceRequestPriority, $this>
     */
    public function priority(): BelongsTo
    {
        return $this->belongsTo(ServiceRequestPriority::class)->withTrashed();
    }

    /**
     * @return BelongsTo<ServiceRequestFormSubmission, $this>
     */
    public function serviceRequestFormSubmission(): BelongsTo
    {
        return $this->belongsTo(ServiceRequestFormSubmission::class, 'service_request_form_submission_id')->withTrashed();
    }

    /**
     * @return HasMany<ServiceRequestAssignment, $this>
     */
    public function assignments(): HasMany
    {
        return $this->hasMany(ServiceRequestAssignment::class);
    }

    /**
     * @return HasOne<ServiceRequestAssignment, $this>
     */
    public function assignedTo(): HasOne
    {
        return $this->hasOne(ServiceRequestAssignment::class)
            ->latest('assigned_at')
            ->where('status', ServiceRequestAssignmentStatus::Active);
    }

    /**
     * @return HasOne<ServiceRequestAssignment, $this>
     */
    public function initialAssignment(): HasOne
    {
        return $this->hasOne(ServiceRequestAssignment::class)
            ->oldest('assigned_at');
    }

    /**
     * @return HasMany<ServiceRequestHistory, $this>
     */
    public function histories(): HasMany
    {
        return $this->hasMany(ServiceRequestHistory::class);
    }

    /**
     * @return HasOne<ServiceRequestFeedback, $this>
     */
    public function feedback(): HasOne
    {
        return $this->hasOne(ServiceRequestFeedback::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<PortalAssistantThread, $this>
     */
    public function portalAssistantThread(): BelongsTo
    {
        return $this->belongsTo(PortalAssistantThread::class);
    }

    public function isDraft(): bool
    {
        return $this->is_draft === true;
    }

    public function scopeOpen(Builder $query): void
    {
        $query->whereIn(
            'status_id',
            ServiceRequestStatus::where('classification', SystemServiceRequestClassification::Open)->pluck('id')
        );
    }

    /**
     * @return HasOne<ServiceRequestUpdate, $this>
     */
    public function latestInboundServiceRequestUpdate(): HasOne
    {
        return $this->hasOne(ServiceRequestUpdate::class, 'service_request_id')
            ->ofMany([
                'created_at' => 'max',
            ], function (Builder $query) {
                $query
                    ->whereHas('createdBy', function (Builder $query) {
                        $query->where('created_by_type', (new Contact())->getMorphClass());
                    })
                    ->where('internal', false);
            });
    }

    /**
     * @return HasOne<ServiceRequestUpdate, $this>
     */
    public function latestOutboundServiceRequestUpdate(): HasOne
    {
        return $this->hasOne(ServiceRequestUpdate::class, 'service_request_id')
            ->ofMany([
                'created_at' => 'max',
            ], function (Builder $query) {
                $query
                    ->whereHas('createdBy', function (Builder $query) {
                        $query->where('created_by_type', (new User())->getMorphClass());
                    })
                    ->where('internal', false);
            });
    }

    public function getLatestResponseSeconds(): int
    {
        if (! $this->latestInboundServiceRequestUpdate) {
            return (int) round($this->created_at->diffInSeconds(now()));
        }

        if (
            $this->isResolved() &&
            ($resolvedAt = $this->getResolvedAt())->isAfter($this->latestInboundServiceRequestUpdate->created_at)
        ) {
            return (int) round($resolvedAt->diffInSeconds($this->latestInboundServiceRequestUpdate->created_at));
        }

        if (
            $this->latestOutboundServiceRequestUpdate &&
            $this->latestOutboundServiceRequestUpdate->created_at->isAfter(
                $this->latestInboundServiceRequestUpdate->created_at,
            )
        ) {
            return (int) round($this->latestOutboundServiceRequestUpdate->created_at->diffInSeconds(
                $this->latestInboundServiceRequestUpdate->created_at,
            ));
        }

        return (int) round($this->latestInboundServiceRequestUpdate->created_at->diffInSeconds());
    }

    public function getResolutionSeconds(): int
    {
        if (! $this->isResolved()) {
            return (int) round($this->created_at->diffInSeconds());
        }

        return (int) round($this->created_at->diffInSeconds($this->getResolvedAt()));
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
            if (! auth()->guard('web')->check()) {
                return;
            }

            /** @var Authenticatable $user */
            $user = auth()->guard('web')->user();

            if (! $user->hasLicense(Contact::getLicenseType())) {
                $builder->whereRaw('1 = 0');
            }
        });

        static::addGlobalScope('excludeDrafts', function (Builder $builder) {
            $builder->where('is_draft', false);
        });
    }

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format(config('project.datetime_format') ?? 'Y-m-d H:i:s');
    }
}
