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

namespace AidingApp\Contact\Filament\Resources\ContactResource\Actions;

use AidingApp\Contact\Filament\Resources\ContactResource;
use AidingApp\Contact\Filament\Resources\ContactResource\Pages\ViewContact;
use AidingApp\Contact\Filament\Resources\ContactResource\Schemas\ContactEmailHealthCallout;
use AidingApp\Contact\Filament\Resources\ContactResource\Schemas\ContactFormSchema;
use AidingApp\Contact\Filament\Resources\ContactResource\Schemas\ContactInfolist;
use AidingApp\Contact\Models\Contact;
use Filament\Actions\Action;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;

class ViewContactAction
{
    public static function make(Contact $contact): Action
    {
        return Action::make('viewContact')
            ->label('View contact')
            ->icon(Heroicon::Eye)
            ->iconButton()
            ->record($contact)
            ->fillForm($contact->attributesToArray())
            ->authorize('view', $contact)
            ->slideOver()
            ->modalWidth(Width::FourExtraLarge)
            ->modalHeading($contact->{Contact::displayNameKey()})
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Close')
            ->schema(fn (): array => [
                ContactEmailHealthCallout::make($contact),
                Actions::make([
                    Action::make('goToContact')
                        ->label('Go to Contact')
                        ->url(ContactResource::getUrl('view', ['record' => $contact])),
                ])->alignment(Alignment::End),
                Group::make()
                    ->disabled()
                    ->schema([
                        ...ContactFormSchema::make(),
                        Section::make('System Information')
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label('Created At')
                                    ->dateTime(),
                                TextEntry::make('updated_at')
                                    ->label('Updated At')
                                    ->dateTime(),
                            ])
                            ->columns(2),
                    ]),
                Tabs::make()
                    ->columnSpanFull()
                    ->tabs(ContactInfolist::tabs($contact, ViewContact::class)),
            ]);
    }
}
