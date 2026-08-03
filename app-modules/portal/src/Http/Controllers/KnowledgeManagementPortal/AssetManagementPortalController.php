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

namespace AidingApp\Portal\Http\Controllers\KnowledgeManagementPortal;

use AidingApp\Contact\Models\Contact;
use AidingApp\InventoryManagement\Models\Asset;
use AidingApp\InventoryManagement\Models\AssetCheckIn;
use AidingApp\InventoryManagement\Models\AssetCheckOut;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class AssetManagementPortalController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $contact = auth('contact')->user();

        $request->validate(['filter' => 'in:all,checked_out,returned']);

        $filter = $request->input('filter', 'all');

        if ($request->has('filter')) {
            return response()->json([
                ...$this->getEnvelope($contact, $filter),
                'counts' => $this->getCounts($contact),
            ]);
        }

        return response()->json([
            'all' => $this->getEnvelope($contact, 'all'),
            'checked_out' => $this->getEnvelope($contact, 'checked_out'),
            'returned' => $this->getEnvelope($contact, 'returned'),
            'counts' => $this->getCounts($contact),
        ]);
    }

    /**
     * @param  Contact  $contact
     *
     * @return array{
    *     data: iterable<int, array<string, mixed>>,
     *     meta: array{
     *         current_page: int,
     *         last_page: int,
     *         from: int,
     *         to: int,
     *         total: int,
     *         per_page: int
     *     }
     * }
     */
    private function getEnvelope($contact, string $filter): array
    {
        $paginator = $this->getPaginator($contact, $filter);

        return [
            'data' => $this->mapItems($paginator),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'from' => $paginator->firstItem() ?? 0,
                'to' => $paginator->lastItem() ?? 0,
                'total' => $paginator->total(),
                'per_page' => $paginator->perPage(),
            ],
        ];
    }

    /**
     * @param  Contact  $contact
     *
     * @return array{total: int, checked_out: int, returned: int}
     */
    private function getCounts($contact): array
    {
        $baseQuery = $this->getBaseQuery($contact);

        return [
            'total' => (clone $baseQuery)->count(),
            'checked_out' => (clone $baseQuery)->whereNull('asset_check_in_id')->count(),
            'returned' => (clone $baseQuery)->whereNotNull('asset_check_in_id')->count(),
        ];
    }

    /**
     * @param  Contact  $contact
     *
     * @return LengthAwarePaginator<int, AssetCheckOut>
     */
    private function getPaginator($contact, string $filter): LengthAwarePaginator
    {
        $baseQuery = $this->getBaseQuery($contact);

        $filteredQuery = match ($filter) {
            'checked_out' => $baseQuery->whereNull('asset_check_in_id'),
            'returned' => $baseQuery->whereNotNull('asset_check_in_id'),
            default => $baseQuery,
        };

        if ($filter === 'returned') {
            return $filteredQuery
                ->orderByDesc(
                    AssetCheckIn::select('checked_in_at')
                        ->whereColumn('asset_check_ins.id', 'asset_check_outs.asset_check_in_id')
                        ->limit(1)
                )
                ->paginate(10);
        }

        return $filteredQuery
            ->orderByDesc('checked_out_at')
            ->paginate(10);
    }

    /**
     * @param  Contact  $contact
     *
     * @return HasMany<AssetCheckOut, Contact>
     */
    private function getBaseQuery($contact): HasMany
    {
        return $contact->assetCheckOuts()
            ->with([
                'asset:id,name,serial_number,description,type_id,location_id,purchase_date',
                'asset.type:id,name',
                'asset.location:id,name',
                'checkIn:id,checked_in_at',
            ]);
    }

    /**
     * @param  LengthAwarePaginator<int, AssetCheckOut>  $paginator
     *
     * @return iterable<int, array<string, mixed>>
     */
    private function mapItems(LengthAwarePaginator $paginator): iterable
    {
        return $paginator->getCollection()->map(function (AssetCheckOut $checkOut): array {
            $isReturned = (bool) $checkOut->checkIn;

            return [
                'id' => $checkOut->getKey(),
                'status' => $isReturned ? 'returned' : 'checked_out',
                'checked_out_at' => $checkOut->getRawOriginal('checked_out_at') !== null
                    ? $checkOut->checked_out_at->toIso8601String()
                    : null,
                'checked_in_at' => $checkOut->checkIn !== null && $checkOut->checkIn->getRawOriginal('checked_in_at') !== null
                    ? $checkOut->checkIn->checked_in_at->toIso8601String()
                    : null,
                'asset' => [
                    'id' => $checkOut->asset->getKey(),
                    'name' => $checkOut->asset->name,
                    'description' => $checkOut->asset->description,
                    'serial_number' => $checkOut->asset->serial_number,
                    'purchase_age' => $this->calculatePurchaseAge($checkOut->asset),
                    'type' => $checkOut->asset->getRawOriginal('type_id') !== null
                        ? ['name' => $checkOut->asset->type->name]
                        : null,
                    'location' => $checkOut->asset->getRawOriginal('location_id') !== null
                        ? ['name' => $checkOut->asset->location->name]
                        : null,
                ],
            ];
        });
    }

    private function calculatePurchaseAge(Asset $asset): ?string
    {
        if ($asset->getRawOriginal('purchase_date') === null) {
            return null;
        }

        $date = $asset->purchase_date;

        if ($date->isFuture()) {
            return '0 Years 0 Months';
        }

        $diff = $date->roundMonth()->diff(now());

        return $diff->y . ' ' . ($diff->y === 1 ? 'Year' : 'Years') . ' ' .
            $diff->m . ' ' . ($diff->m === 1 ? 'Month' : 'Months');
    }
}
