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

namespace AidingApp\ServiceManagement\Livewire;

use AidingApp\Ai\Models\AiAssistant;
use AidingApp\ServiceManagement\Enums\SystemServiceRequestClassification;
use AidingApp\ServiceManagement\Filament\Actions\DraftServiceRequestUpdateWithAiAction;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequests\Schemas\Components\ServiceRequestStatusSelect;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequests\Schemas\Components\ServiceRequestUpdateUploadsFileUpload;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequests\Schemas\Components\ServiceRequestUpdateVisibilityToggleButtons;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestUpdate;
use App\Settings\DisplaySettings;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Concerns\RestrictsFileUploadsToSchemaComponents;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Size;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Vite;
use Livewire\Attributes\Locked;
use Livewire\Component;

/**
 * @property-read Schema $form
 */
class ServiceRequestUpdates extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use RestrictsFileUploadsToSchemaComponents;

    #[Locked]
    public ServiceRequest $serviceRequest;

    #[Locked]
    public int $visibleUpdatesLimit = 50;

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    public function mount(): void
    {
        Gate::authorize('viewAny', ServiceRequestUpdate::class);

        $this->resetComposer();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Textarea::make('update')
                    ->label('Add an update')
                    ->placeholder('Write an update…')
                    ->rows(4)
                    ->autosize()
                    ->required()
                    ->string()
                    ->hintAction(DraftServiceRequestUpdateWithAiAction::make()),
                Grid::make(['default' => 1, 'md' => 2])
                    ->schema([
                        ServiceRequestUpdateVisibilityToggleButtons::make(),
                        ServiceRequestStatusSelect::make(selectedId: $this->serviceRequest->status_id)
                            ->visible(fn (): bool => $this->canUpdateServiceRequest()),
                    ]),
                ServiceRequestUpdateUploadsFileUpload::make(),
            ])
            ->model(ServiceRequestUpdate::class)
            ->statePath('data');
    }

    public function create(): void
    {
        abort_unless($this->canCreateUpdates(), 403);

        $data = $this->form->getState();

        DB::transaction(function () use ($data): void {
            $serviceRequestUpdate = new ServiceRequestUpdate();
            $serviceRequestUpdate->update = $data['update'];
            $serviceRequestUpdate->internal = $data['internal'];
            $serviceRequestUpdate->serviceRequest()->associate($this->serviceRequest);
            $serviceRequestUpdate->save();

            $this->form->model($serviceRequestUpdate)->saveRelationships();

            if (filled($data['status_id'] ?? null) && $this->canUpdateServiceRequest()) {
                $this->serviceRequest->update(['status_id' => $data['status_id']]);
            }
        });

        Notification::make()
            ->title($data['internal'] ? 'Internal note added' : 'Update sent')
            ->success()
            ->send();

        $this->serviceRequest->refresh();

        $this->resetComposer();
    }

    public function deleteUpdateAction(): DeleteAction
    {
        return DeleteAction::make('deleteUpdate')
            ->record(fn (array $arguments): ?ServiceRequestUpdate => $this->serviceRequest
                ->serviceRequestUpdates()
                ->find($arguments['update'] ?? null))
            ->authorize('delete')
            ->hidden(fn (?ServiceRequestUpdate $record): bool => (! $record) || $record->trashed())
            ->modalHeading('Delete update')
            ->icon(Heroicon::Trash)
            ->tooltip('Delete')
            ->keyBindings([])
            ->iconButton()
            ->size(Size::Small)
            ->color('gray');
    }

    public function loadEarlierUpdates(): void
    {
        $this->visibleUpdatesLimit += 50;
    }

    public function render(): View
    {
        $timezone = app(DisplaySettings::class)->getTimezone();

        $updatesCount = $this->serviceRequest->serviceRequestUpdates()->count();

        $updates = $this->serviceRequest
            ->serviceRequestUpdates()
            ->with([
                'createdBy' => function (Relation $relation): void {
                    assert($relation instanceof MorphTo);

                    $relation->withTrashed();
                },
                'media',
            ])
            ->latest()
            ->orderByDesc('id')
            ->limit($this->visibleUpdatesLimit)
            ->get()
            ->reverse();

        return view('service-management::livewire.service-request-updates', [
            'aiAvatarUrl' => $updates->contains(fn (ServiceRequestUpdate $update): bool => $update->createdBy instanceof ServiceRequest)
                ? $this->getAiAvatarUrl()
                : null,
            'canCreateUpdates' => $this->canCreateUpdates(),
            'hasEarlierUpdates' => $updatesCount > $updates->count(),
            'isClosed' => $this->isServiceRequestClosed(),
            'timezone' => $timezone,
            'updatesCount' => $updatesCount,
            'updatesByDate' => $updates->groupBy(
                fn (ServiceRequestUpdate $update): string => $update->created_at->copy()->setTimezone($timezone)->toDateString(),
            ),
        ]);
    }

    protected function resetComposer(): void
    {
        $this->form->fill([
            'internal' => false,
            'status_id' => $this->serviceRequest->status_id,
        ]);
    }

    protected function getAiAvatarUrl(): string
    {
        return AiAssistant::query()->where('is_default', true)->first()
            ?->getFirstTemporaryUrl(now()->addHour(), 'avatar', 'avatar-height-250px') ?: Vite::asset('resources/images/canyon-ai-headshot.jpg');
    }

    protected function canCreateUpdates(): bool
    {
        return (! $this->isServiceRequestClosed()) && Gate::allows('create', ServiceRequestUpdate::class);
    }

    protected function canUpdateServiceRequest(): bool
    {
        return Gate::allows('update', $this->serviceRequest);
    }

    protected function isServiceRequestClosed(): bool
    {
        return $this->serviceRequest->status?->classification === SystemServiceRequestClassification::Closed;
    }
}
