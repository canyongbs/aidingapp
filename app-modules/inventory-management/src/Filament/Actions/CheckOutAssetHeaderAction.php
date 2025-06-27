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

namespace AidingApp\InventoryManagement\Filament\Actions;

use AidingApp\Contact\Models\Contact;
use AidingApp\InventoryManagement\Enums\SystemAssetStatusClassification;
use AidingApp\InventoryManagement\Models\Asset;
use AidingApp\InventoryManagement\Models\AssetStatus;
use AidingApp\InventoryManagement\Models\Scopes\ClassifiedAs;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;

class CheckOutAssetHeaderAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->button();

        $this->label(__('Check Out'));

        /** @var Asset $asset */
        $asset = $this->getRecord();

        $this->modalHeading(__("Check out {$asset->name}"));

        $this->modalSubmitActionLabel(__('Done'));

        $this->successNotificationTitle(__("Successfully checked out {$asset->name}"));

        $this->form([
            Select::make('checked_out_to_id')
                ->label('Select Contact')
                ->options(Contact::all()->pluck('full_name', 'id'))
                ->searchable()
                ->required(),
            Textarea::make('notes')
                ->autofocus(),
            DateTimePicker::make('expected_check_in_at')
                ->label('Expected Return Date'),
        ]);

        $this->action(function (array $data): void {
            /** @var Asset $asset */
            $asset = $this->getRecord();

            if (! $asset->isAvailable()) {
                $this->failure();
            }

            $asset->checkOuts()->create([
                  'checked_out_by_type' => auth()->user()?->getMorphClass(),
                  'checked_out_by_id' => auth()->user()?->id,
                  'checked_out_to_id' => $data['checked_out_to_id'],
                  'notes' => $data['notes'],
                  'checked_out_at' => now(),
                  'expected_check_in_at' => $data['expected_check_in_at'],
              ]);

            $asset->status()->associate(AssetStatus::tap(new ClassifiedAs(SystemAssetStatusClassification::CheckedOut))->first());
            $asset->save();

            $this->success();
        });
    }
}
