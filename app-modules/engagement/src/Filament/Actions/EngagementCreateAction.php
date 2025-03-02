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

namespace AidingApp\Engagement\Filament\Actions;

use AidingApp\Engagement\Enums\EngagementDeliveryMethod;
use AidingApp\Engagement\Models\Engagement;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Form;
use Illuminate\Database\Eloquent\Model;

class EngagementCreateAction
{
    public static function make(Model $educatable)
    {
        return CreateAction::make('engage')
            ->record($educatable)
            ->icon('heroicon-o-chat-bubble-bottom-center-text')
            ->modalHeading('Send Engagement')
            ->modalDescription("Send an engagement to {$educatable->display_name}.")
            ->steps([
                Step::make('Choose your delivery method')
                    ->description('Select email')
                    ->schema([
                        Select::make('delivery_method')
                            ->label('How would you like to send this engagement?')
                            ->options(EngagementDeliveryMethod::class)
                            ->validationAttribute('Delivery Method')
                            ->required(),
                    ]),
                Step::make('Engagement Details')
                    ->description('Add the details of the engagement.')
                    ->schema([
                        TextInput::make('subject')
                            ->autofocus()
                            ->required()
                            ->placeholder(__('Subject'))
                            ->hidden(fn (callable $get) => collect($get('delivery_method'))->doesntContain(EngagementDeliveryMethod::Email->value)),
                        Textarea::make('body')
                            ->placeholder(__('Body'))
                            ->required()
                            ->maxLength(function (callable $get) {
                                return 65535;
                            })
                            ->helperText(function (callable $get) {
                                return 'The body of your message can be up to 65,535 characters long.';
                            }),
                    ]),
            ])
            ->action(function (array $data, Form $form) use ($educatable) {
                $createOnDemandEngagement = resolve(CreateOnDemandEngagement::class);

                $createOnDemandEngagement(
                    $educatable,
                    $data,
                    afterCreation: fn (Engagement $engagement) => $form->model($engagement)->saveRelationships(),
                );
            })
            ->modalSubmitActionLabel('Send')
            ->modalCloseButton(false)
            ->closeModalByClickingAway(false)
            // FIXME This is currently not working exactly as expected. Dan is taking a look and will report back
            ->modalCancelAction(
                fn ($action) => Action::make('cancel')
                    ->requiresConfirmation()
                    ->modalDescription(fn () => 'The message has not been sent, are you sure you wish to cancel?')
                    ->cancelParentActions()
                    ->close()
                    ->color('gray'),
            );
    }
}
