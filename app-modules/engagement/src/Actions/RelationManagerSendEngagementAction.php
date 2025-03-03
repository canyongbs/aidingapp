<?php

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
