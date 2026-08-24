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

namespace AidingApp\Project\Filament\Resources\Pipelines\Actions;

use AidingApp\Project\Filament\Resources\Pipelines\Forms\PipelineEntryForm;
use AidingApp\Project\Models\Pipeline;
use AidingApp\Project\Models\PipelineEntry;
use App\Features\PipelineEntryMilestoneFeature;
use Closure;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;

class EditPipelineEntryAction
{
    /**
     * Builds the shared "edit pipeline task" slide-over action so every surface
     * (kanban board, pipeline task table, project widget) reuses the same form and
     * relationship-syncing logic.
     *
     * @param  (Closure(): void)|null  $after
     *     Optional callback run after the related records have been synced, e.g. to
     *     dispatch a UI refresh event on the calling component.
     * @param  (Closure(): void)|null  $afterArchive
     *     Optional no-argument callback run after the task has been archived. Use it to
     *     refresh the calling surface or dispatch related UI updates.
     */
    public static function make(?Pipeline $pipeline = null, ?string $name = null, ?Closure $after = null, ?Closure $afterArchive = null): EditAction
    {
        return EditAction::make($name ?? 'edit')
            ->slideOver()
            ->modalHeading('Edit Pipeline Task')
            ->schema(PipelineEntryForm::components($pipeline))
            ->extraModalFooterActions(fn (Action $action): array => [
                self::archivePipelineEntryAction(
                    $pipeline,
                    $action->getArguments()['entry'] ?? null,
                    $afterArchive,
                ),
            ])
            ->after(function (PipelineEntry $record, array $data) use ($after): void {
                if (! PipelineEntryMilestoneFeature::active()) {
                    $record->milestones()->sync($data['milestones'] ?? []);
                }
                $record->assets()->sync($data['assets'] ?? []);
                $record->serviceRequests()->sync($data['serviceRequests'] ?? []);

                if ($after !== null) {
                    $after();
                }
            })
            ->successNotificationTitle('Pipeline task updated successfully');
    }

    private static function archivePipelineEntryAction(?Pipeline $pipeline, mixed $entryId, ?Closure $afterArchive): Action
    {
        return Action::make('archivePipelineEntry')
            ->label('Archive')
            ->color('danger')
            ->visible(fn (): bool => filled($entryId))
            ->requiresConfirmation()
            ->modalHeading('Archive Pipeline Task')
            ->modalDescription('Are you sure you want to archive this pipeline task?')
            ->modalSubmitActionLabel('Archive')
            ->cancelParentActions()
            ->authorize(fn (): bool => $pipeline !== null && auth()->user()->can('delete', $pipeline))
            ->action(function () use ($pipeline, $entryId, $afterArchive): void {
                if ($pipeline === null) {
                    return;
                }

                $entry = $pipeline->entries()->whereKey($entryId)->first();

                if ($entry === null) {
                    Notification::make()
                        ->danger()
                        ->title('Pipeline task could not be found')
                        ->body('The pipeline task may have already been archived or removed.')
                        ->send();

                    return;
                }

                if (! $entry->archive()) {
                    Notification::make()
                        ->danger()
                        ->title('Pipeline task could not be archived')
                        ->send();

                    return;
                }

                if ($afterArchive !== null) {
                    $afterArchive();
                }

                Notification::make()
                    ->success()
                    ->title('Pipeline task archived')
                    ->send();
            });
    }
}
