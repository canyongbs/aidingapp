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

namespace AidingApp\Engagement\Actions;

use AidingApp\Engagement\Filament\Resources\EngagementResource\Pages\CreateEngagement;
use AidingApp\Engagement\Models\Engagement;
use Carbon\Carbon;
use Filament\Actions\StaticAction;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\CreateAction;
use Illuminate\Support\Facades\DB;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class RelationManagerSendEngagementAction extends CreateAction
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->icon('heroicon-m-envelope')
            ->label('New')
            ->modalHeading('Create new email')
            ->model(Engagement::class)
            ->authorize(function (RelationManager $livewire) {
                $ownerRecord = $livewire->getOwnerRecord();

                return auth()->user()->can('create', [Engagement::class, $ownerRecord]);
            })
            ->form(function (Form $form) {
                return (resolve(CreateEngagement::class))->form($form);
            })
            ->action(function (array $data, Form $form, RelationManager $livewire) {
                $engagement = new Engagement();
                $engagement->user()->associate(auth()->user());
                $engagement->recipient()->associate($livewire->getOwnerRecord());
                $engagement->subject = $data['subject'];
                $engagement->deliver_at = ($data['send_later'] ?? false) ? Carbon::parse($data['deliver_at'] ?? null) : null;
                $engagement->scheduled = true;

                $data['temporaryBodyImages'] = array_map(
                    fn (TemporaryUploadedFile $file): array => [
                        'extension' => $file->getClientOriginalExtension(),
                        'path' => (fn () => $this->path)->call($file),
                    ],
                    $form->getFlatFields()['body']->getTemporaryImages(),
                );

                DB::transaction(function () use ($data, $engagement) {
                    $engagement->save();

                    [$engagement->body] = tiptap_converter()->saveImages(
                        $data['body'],
                        disk: 's3-public',
                        record: $engagement,
                        recordAttribute: 'body',
                        newImages: $data['temporaryBodyImages'],
                    );

                    $engagement->save();
                });

                $form->model($engagement)->saveRelationships();

                $createEngagementDeliverable = resolve(CreateEngagementDeliverable::class);

                $createEngagementDeliverable($engagement, $data['delivery_method']);

                Notification::make()
                    ->title('Created')
                    ->success()
                    ->send();
            })
            ->modalSubmitActionLabel('Send')
            ->modalCloseButton(false)
            ->closeModalByClickingAway(false)
            ->closeModalByEscaping(false)
            ->createAnother(false)
            ->modalCancelAction(false)
            ->extraModalFooterActions([
                Action::make('cancel')
                    ->color('gray')
                    ->cancelParentActions()
                    ->requiresConfirmation()
                    ->action(fn () => null)
                    ->modalSubmitAction(fn (StaticAction $action) => $action->color('danger')),
            ]);
    }

    public static function getDefaultName(): ?string
    {
        return 'engage';
    }
}
