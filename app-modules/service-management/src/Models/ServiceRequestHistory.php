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
use AidingApp\Division\Models\Division;
use AidingApp\ServiceManagement\Database\Factories\ServiceRequestHistoryFactory;
use AidingApp\ServiceManagement\Observers\ServiceRequestHistoryObserver;
use AidingApp\Timeline\Models\Contracts\ProvidesATimeline;
use AidingApp\Timeline\Timelines\ServiceRequestHistoryTimeline;
use App\Models\BaseModel;
use Exception;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * @mixin IdeHelperServiceRequestHistory
 */
#[ObservedBy([ServiceRequestHistoryObserver::class])]
class ServiceRequestHistory extends BaseModel implements ProvidesATimeline
{
    use SoftDeletes;

    /** @use HasFactory<ServiceRequestHistoryFactory> */
    use HasFactory;

    protected $casts = [
        'original_values' => 'array',
        'new_values' => 'array',
    ];

    protected $fillable = [
        'original_values',
        'new_values',
    ];

    public function serviceRequest(): BelongsTo
    {
        return $this->belongsTo(ServiceRequest::class);
    }

    public function timeline(): ServiceRequestHistoryTimeline
    {
        return new ServiceRequestHistoryTimeline($this);
    }

    public static function getTimelineData(Model $forModel): Collection
    {
        return $forModel->histories()->get();
    }

    public function getUpdates(): array
    {
        $updates = [];

        foreach ($this->new_values as $key => $value) {
            $updates[] = [
                'key' => $key,
                'old' => $this->original_values[$key],
                'new' => $value,
            ];
        }

        return $updates;
    }

    protected function newValuesFormatted(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => $this->formatValues(json_decode($attributes['new_values'], true)),
        );
    }

    protected function originalValuesFormatted(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => $this->formatValues(json_decode($attributes['original_values'], true)),
        );
    }

    protected function formatValues(array $value): array
    {
        $relationsMap = [
            'priority_id' => [ServiceRequestPriority::class, 'name'],
            'status_id' => [ServiceRequestStatus::class, 'name'],
            'division_id' => [Division::class, 'name'],
            'type_id' => [ServiceRequestType::class, 'name'],
            'respondent_id' => [
                [Contact::class],
            ],
        ];

        foreach ($value as $key => $data) {
            $readableKey = $this->transformReadableKey($key);

            $value[$readableKey] = $value[$key];

            if (array_key_exists($key, $relationsMap)) {
                if (is_array($relationsMap[$key][0])) {
                    foreach ($relationsMap[$key][0] as $educatableClass) {
                        $found = null;

                        // This is to overcome an issue that comes from an incorrect type when trying to find a contact with the wrong data type
                        try {
                            $found = $educatableClass::find($value[$key]);
                        } catch (Exception $e) {
                        }

                        if (! is_null($found)) {
                            $value[$readableKey] = $found->{$educatableClass::displayNameKey()};
                        }
                    }
                } else {
                    $value[$readableKey] = ! is_null($value[$key]) ? $relationsMap[$key][0]::find($value[$key])->{$relationsMap[$key][1]} : 'NULL';
                }
            }

            unset($value[$key]);
        }

        return $value;
    }

    protected function transformReadableKey(string $key): string
    {
        if (Str::endsWith($key, '_id')) {
            $key = Str::replaceLast('_id', '', $key);
        }

        return Str::of($key)->replace('_', ' ')->title()->toString();
    }
}
