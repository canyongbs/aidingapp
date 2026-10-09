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

namespace AidingApp\Contact\Filament\Resources\ContactResource\Pages;

use AidingApp\Contact\Filament\Resources\ContactResource;
use AidingApp\Contact\Filament\Resources\ContactResource\Schemas\ContactEmailHealthCallout;
use AidingApp\Contact\Filament\Resources\ContactResource\Schemas\ContactFormSchema;
use AidingApp\Contact\Filament\Resources\ContactResource\Schemas\ContactInfolist;
use AidingApp\Contact\Models\Contact;
use App\Features\ContactTrackingFeature;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ViewContact extends ViewRecord
{
    protected static string $resource = ContactResource::class;

    public function infolist(Schema $schema): Schema
    {
        $contact = $this->getRecord();

        assert($contact instanceof Contact);

        return $schema
            ->components([
                ContactEmailHealthCallout::make($contact),
                Section::make('Demographic Information')
                    ->key('demographicInformation')
                    ->headerActions([
                        self::sectionEditAction('editDemographicInformation', 'Demographic Information')
                            ->schema(ContactFormSchema::demographicInformationFields()),
                    ])
                    ->schema([
                        TextEntry::make('first_name')
                            ->label('First Name'),
                        TextEntry::make('last_name')
                            ->label('Last Name'),
                        TextEntry::make(Contact::displayNameKey())
                            ->label('Full Name'),
                        TextEntry::make('preferred')
                            ->label('Preferred Name'),
                        TextEntry::make('presence_status')
                            ->label('Presence')
                            ->visible(fn (): bool => ContactTrackingFeature::active())
                            ->state(fn (Contact $record) => $record->presenceStatus())
                            ->badge()
                            ->color(fn (Contact $record) => $record->presenceStatus()->getColor())
                            ->icon(fn (Contact $record) => $record->presenceStatus()->getIcon())
                            ->formatStateUsing(fn (Contact $record) => $record->presenceStatus()->getLabel()),
                        TextEntry::make('type.name')
                            ->label('Type'),
                    ])
                    ->columns(2),
                Section::make('Contact Information')
                    ->key('contactInformation')
                    ->headerActions([
                        self::sectionEditAction('editContactInformation', 'Contact Information')
                            ->schema(ContactFormSchema::contactInformationFields(ignoreRecord: true)),
                    ])
                    ->schema([
                        TextEntry::make('email')
                            ->label('Email'),
                        TextEntry::make('mobile')
                            ->label('Mobile'),
                        TextEntry::make('phone')
                            ->label('Phone'),
                    ])
                    ->columns(2),
                Section::make('Employment Information')
                    ->key('employmentInformation')
                    ->headerActions([
                        self::sectionEditAction('editEmploymentInformation', 'Employment Information')
                            ->schema(ContactFormSchema::employmentInformationFields()),
                    ])
                    ->schema([
                        TextEntry::make('job_title')
                            ->label('Job Title'),
                        TextEntry::make('employee_id')
                            ->label('Employee ID'),
                        TextEntry::make('work_number')
                            ->label('Work Number'),
                        TextEntry::make('work_extension')
                            ->label('Work Extension'),
                    ])
                    ->columns(2),
                Section::make('Academic Information')
                    ->key('academicInformation')
                    ->headerActions([
                        self::sectionEditAction('editAcademicInformation', 'Academic Information')
                            ->schema(ContactFormSchema::academicInformationFields()),
                    ])
                    ->schema([
                        TextEntry::make('student_id')
                            ->label('Student ID'),
                        TextEntry::make('school')
                            ->label('School'),
                        TextEntry::make('academic_department')
                            ->label('Academic Department'),
                        TextEntry::make('program')
                            ->label('Program'),
                    ])
                    ->columns(2),
                Section::make('Address Information')
                    ->key('addressInformation')
                    ->headerActions([
                        self::sectionEditAction('editAddressInformation', 'Address Information')
                            ->schema(ContactFormSchema::addressInformationFields()),
                    ])
                    ->schema([
                        TextEntry::make('address')
                            ->label('Address'),
                        TextEntry::make('address_2')
                            ->label('Address 2'),
                        TextEntry::make('city')
                            ->label('City'),
                        TextEntry::make('state')
                            ->label('State'),
                        TextEntry::make('postal')
                            ->label('Postal'),
                        TextEntry::make('country')
                            ->label('Country'),
                    ])
                    ->columns(2),
                Section::make('Customer Information')
                    ->key('customerInformation')
                    ->headerActions([
                        self::sectionEditAction('editCustomerInformation', 'Customer Information')
                            ->schema(ContactFormSchema::customerInformationFields()),
                    ])
                    ->schema([
                        TextEntry::make('organization.name')
                            ->label('Organization'),
                    ])
                    ->columns(2),
                Section::make('Engagement Restrictions')
                    ->key('engagementRestrictions')
                    ->headerActions([
                        self::sectionEditAction('editEngagementRestrictions', 'Engagement Restrictions')
                            ->schema(ContactFormSchema::engagementRestrictionsFields()),
                    ])
                    ->schema([
                        IconEntry::make('email_bounce')
                            ->label('Email Bounce')
                            ->boolean(),
                    ])
                    ->columns(2),
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
                Tabs::make()
                    ->columnSpanFull()
                    ->tabs(ContactInfolist::tabs($contact, static::class)),
            ]);
    }

    protected function getHeaderActions(): array
    {
        $contact = $this->getRecord();

        assert($contact instanceof Contact);

        if ($contact->isManaged()) {
            return [
                Action::make('managed')
                    ->hiddenLabel()
                    ->icon(Heroicon::LockClosed)
                    ->color('gray')
                    ->tooltip('This is a User\'s managed non-administrative account for the self-service portal. The information displayed is synchronized directly from the User record.')
                    ->disabled(),
            ];
        }

        return [
            DeleteAction::make(),
        ];
    }

    protected static function sectionEditAction(string $name, string $sectionLabel): EditAction
    {
        return EditAction::make($name)
            ->label('Edit')
            ->modalHeading("Edit {$sectionLabel}")
            ->modalSubmitActionLabel('Save')
            ->slideOver()
            ->after(fn (Contact $record) => $record->refresh());
    }
}
