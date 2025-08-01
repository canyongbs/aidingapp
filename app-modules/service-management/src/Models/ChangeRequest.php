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

use AidingApp\Audit\Models\Concerns\Auditable as AuditableTrait;
use AidingApp\ServiceManagement\Database\Factories\ChangeRequestFactory;
use AidingApp\ServiceManagement\Enums\SystemChangeRequestClassification;
use AidingApp\ServiceManagement\Observers\ChangeRequestObserver;
use App\Models\BaseModel;
use App\Models\Concerns\HasRelationBasedStateMachine;
use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * @mixin IdeHelperChangeRequest
 */
#[ObservedBy([ChangeRequestObserver::class])]
class ChangeRequest extends BaseModel implements Auditable
{
    use AuditableTrait;
    use HasRelationBasedStateMachine;

    /** @use HasFactory<ChangeRequestFactory> */
    use HasFactory;

    protected $fillable = [
        'backout_strategy',
        'change_request_status_id',
        'change_request_type_id',
        'created_by',
        'description',
        'end_time',
        'impact',
        'likelihood',
        'reason',
        'start_time',
        'title',
    ];

    protected $casts = [
        'end_time' => 'datetime',
        'impact' => 'integer',
        'likelihood' => 'integer',
        'risk_score' => 'integer',
        'start_time' => 'datetime',
    ];

    public function getStateMachineFields(): array
    {
        return [
            'status.classification',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(ChangeRequestType::class, 'change_request_type_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(ChangeRequestStatus::class, 'change_request_status_id');
    }

    public function responses(): HasMany
    {
        return $this->hasMany(ChangeRequestResponse::class, 'change_request_id');
    }

    public function approvals(): HasMany
    {
        return $this->responses()->where('approved', '=', true);
    }

    public function hasApproval(): bool
    {
        return $this->approvals()->count() >= $this->type()->withTrashed()->first()->number_of_required_approvals;
    }

    public function isApproved(): bool
    {
        return $this->status()->withTrashed()->first()->classification === SystemChangeRequestClassification::Approved;
    }

    public function isNotNew(): bool
    {
        return $this->status()->withTrashed()->first()->classification !== SystemChangeRequestClassification::New;
    }

    public function canBeApprovedBy(User $user): bool
    {
        return $this->type()->withTrashed()->first()->userApprovers()->where('user_id', $user->id)->exists() && ! $this->hasBeenApprovedBy($user);
    }

    public function hasBeenApprovedBy(User $user): bool
    {
        return $this->approvals()->where('user_id', $user->id)->exists();
    }

    public function doesNotNeedExplicitApproval(): bool
    {
        return $this->type()->withTrashed()->first()->number_of_required_approvals === 0;
    }

    public function getIcon(): string
    {
        return match (true) {
            $this->isApproved() || $this->hasApproval() => 'heroicon-s-check-circle',
            default => 'heroicon-s-clock',
        };
    }

    public function getIconColor(): string
    {
        return match (true) {
            $this->isApproved() || $this->hasApproval() => 'success',
            default => 'gray',
        };
    }

    public static function getColorBasedOnRisk(?int $value): string
    {
        $classMap = [
            '1-4' => 'green',
            '5-10' => 'yellow',
            '11-16' => 'orange',
            '17-25' => 'red',
        ];

        foreach ($classMap as $range => $classes) {
            [$min, $max] = explode('-', $range);

            if ($value >= (int) $min && $value <= (int) $max) {
                return $classes;
            }
        }

        return '';
    }
}
