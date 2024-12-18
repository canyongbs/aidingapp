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

namespace AidingApp\Task\Filament\Resources\TaskResource\Components;

use AidingApp\Task\Enums\TaskStatus;
use AidingApp\Task\Filament\Concerns\TaskViewActionInfoList;
use AidingApp\Task\Models\Task;
use Filament\Actions\Action;

class TaskViewHeaderAction extends Action
{
    use TaskViewActionInfoList;

    protected function setUp(): void
    {
        parent::setUp();

        $this->extraModalFooterActions([
            Action::make('mark_as_in_progress')
                ->label('Mark as In Progress')
                ->action(fn (Task $record) => $record->getStateMachine('status')->transitionTo(TaskStatus::InProgress))
                ->cancelParentActions()
                ->hidden(fn (Task $record) => $record->getStateMachine('status')->getStateTransitions()->doesntContain(TaskStatus::InProgress->value) || auth()?->user()?->cannot("task.{$record->id}.update")),
            Action::make('mark_as_completed')
                ->label('Mark as Completed')
                ->action(fn (Task $record) => $record->getStateMachine('status')->transitionTo(TaskStatus::Completed))
                ->cancelParentActions()
                ->hidden(fn (Task $record) => $record->getStateMachine('status')->getStateTransitions()->doesntContain(TaskStatus::Completed->value) || auth()?->user()?->cannot("task.{$record->id}.update")),
            Action::make('mark_as_canceled')
                ->label('Mark as Canceled')
                ->action(fn (Task $record) => $record->getStateMachine('status')->transitionTo(TaskStatus::Canceled))
                ->cancelParentActions()
                ->hidden(fn (Task $record) => $record->getStateMachine('status')->getStateTransitions()->doesntContain(TaskStatus::Canceled->value) || auth()?->user()?->cannot("task.{$record->id}.update")),
        ])->modalSubmitAction(false)
            ->modalCancelAction(function ($action) {
                $action->label('Close');
            })
            ->infolist($this->taskInfoList());
    }
}
