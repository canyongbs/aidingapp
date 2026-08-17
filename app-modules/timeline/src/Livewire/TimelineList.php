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

namespace AidingApp\Timeline\Livewire;

use AidingApp\Timeline\Actions\SyncTimelineData;
use AidingApp\Timeline\Filament\Pages\Concerns\LoadsTimelineRecords;
use AidingApp\Timeline\Models\Contracts\ProvidesATimeline;
use AidingApp\Timeline\Models\Timeline;
use App\Actions\GetRecordFromMorphAndKey;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\ViewAction;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;

class TimelineList extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use LoadsTimelineRecords;

    public Model $recordModel;

    /**
     * @var array<Model|string>
     */
    public array $modelsToTimeline = [];

    public string $emptyStateMessage = 'There are no records to show on this timeline.';

    public string $noMoreRecordsMessage = 'You have reached the end of this timeline.';

    public Model $currentRecordToView;

    /**
     * @param  array<Model|string>  $modelsToTimeline
     */
    public function mount(
        Model $record,
        array $modelsToTimeline,
        ?string $emptyStateMessage = null,
        ?string $noMoreRecordsMessage = null,
    ): void {
        $this->recordModel = $record;
        $this->modelsToTimeline = $modelsToTimeline;

        if (filled($emptyStateMessage)) {
            $this->emptyStateMessage = $emptyStateMessage;
        }

        if (filled($noMoreRecordsMessage)) {
            $this->noMoreRecordsMessage = $noMoreRecordsMessage;
        }

        $this->timelineRecords = collect();

        resolve(SyncTimelineData::class)->now($this->recordModel, $this->modelsToTimeline);

        $this->loadTimelineRecords();
    }

    public function viewRecord(string $key, string $morphReference): void
    {
        abort_unless($this->isOnThisTimeline($key, $morphReference), 404);

        $this->currentRecordToView = resolve(GetRecordFromMorphAndKey::class)->via($morphReference, $key);

        $this->mountAction('view');
    }

    public function viewAction(): ViewAction
    {
        abort_unless(
            isset($this->currentRecordToView) && $this->currentRecordToView instanceof ProvidesATimeline,
            404,
        );

        return $this->currentRecordToView
            ->timeline()
            ->modalViewAction()->slideOver();
    }

    public function render(): View
    {
        return view('timeline::livewire.timeline-list');
    }

    private function isOnThisTimeline(string $key, string $morphReference): bool
    {
        $displayedMorphClasses = collect($this->modelsToTimeline)
            ->map(fn (Model | string $model): string => resolve($model)->getMorphClass());

        if (! $displayedMorphClasses->contains($morphReference)) {
            return false;
        }

        return Timeline::query()
            ->forEntity($this->recordModel)
            ->where('timelineable_type', $morphReference)
            ->where('timelineable_id', $key)
            ->exists();
    }
}
