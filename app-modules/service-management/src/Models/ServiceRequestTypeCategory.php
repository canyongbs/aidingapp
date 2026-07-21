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

namespace AidingApp\ServiceManagement\Models;

use AidingApp\Audit\Models\Concerns\Auditable as AuditableTrait;
use AidingApp\Contact\Models\ContactType;
use AidingApp\ServiceManagement\Database\Factories\ServiceRequestTypeCategoryFactory;
use AidingApp\ServiceManagement\Models\Concerns\RestrictsVisibilityToContactTypes;
use AidingApp\ServiceManagement\Observers\ServiceRequestTypeCategoryObserver;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use Staudenmeir\EloquentHasManyDeep\HasManyDeep;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;

/**
 * @mixin IdeHelperServiceRequestTypeCategory
 */
#[ObservedBy([ServiceRequestTypeCategoryObserver::class])]
class ServiceRequestTypeCategory extends BaseModel implements Auditable
{
    use SoftDeletes;
    use AuditableTrait;
    use HasRelationships;
    use RestrictsVisibilityToContactTypes;

    /** @use HasFactory<ServiceRequestTypeCategoryFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'sort',
        'parent_id',
        'is_visibility_restricted',
    ];

    protected $casts = [
        'sort' => 'integer',
        'is_visibility_restricted' => 'boolean',
    ];

    /**
     * @return BelongsTo<ServiceRequestTypeCategory, $this>
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(ServiceRequestTypeCategory::class, 'parent_id');
    }

    /**
     * @return HasMany<ServiceRequestTypeCategory, $this>
     */
    public function children(): HasMany
    {
        return $this->hasMany(ServiceRequestTypeCategory::class, 'parent_id')->orderBy('sort');
    }

    /**
     * @return BelongsToMany<ServiceRequestType, $this, covariant ServiceRequestCategoryType>
     */
    public function types(): BelongsToMany
    {
        return $this->belongsToMany(
            related: ServiceRequestType::class,
            table: 'service_request_category_types',
            foreignPivotKey: 'service_request_type_category_id',
            relatedPivotKey: 'service_request_type_id',
        )
            ->using(ServiceRequestCategoryType::class)
            ->withPivot('id', 'sort')
            ->withTimestamps()
            ->orderByPivot('sort');
    }

    /**
     * @return BelongsToMany<ContactType, $this, ServiceRequestTypeCategoryVisibilityContactType>
     */
    public function restrictedToContactTypes(): BelongsToMany
    {
        return $this->belongsToMany(
            related: ContactType::class,
            table: 'service_request_type_category_visibility_contact_types',
            foreignPivotKey: 'service_request_type_category_id',
            relatedPivotKey: 'contact_type_id',
        )
            ->using(ServiceRequestTypeCategoryVisibilityContactType::class)
            ->withTimestamps();
    }

    /**
     * @return iterable<ServiceRequestTypeCategory>
     */
    public function visibilityRestrictionParents(): iterable
    {
        return $this->parent !== null ? [$this->parent] : [];
    }

    /**
     * @return HasManyDeep<ServiceRequest, $this>
     */
    public function descendantServiceRequests(): HasManyDeep
    {
        // @phpstan-ignore return.type (hasManyDeepFromRelations() infers a generic PHPStan cannot match to HasManyDeep<ServiceRequest, $this>.)
        return $this->hasManyDeepFromRelations(
            $this->types(),
            (new ServiceRequestType())->serviceRequests(),
        );
    }
}
