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

namespace AidingApp\ServiceManagement\Filament\Resources\ServiceRequestTypeResource\Pages;

use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestTypeResource;
use App\Filament\Forms\Components\Heading;
use App\Filament\Forms\Components\Paragraph;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;

class EditServiceRequestTypeNotifications extends EditRecord
{
    protected static string $resource = ServiceRequestTypeResource::class;

    protected static ?string $title = 'Notifications';

    protected static ?string $navigationIcon = 'heroicon-o-bell-alert';

    public function getRelationManagers(): array
    {
        // Needed to prevent Filament from loading the relation managers on this page.
        return [];
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->columns()
                    ->schema([
                        Heading::make()
                            ->content('Notifications and Alerts'),
                        Paragraph::make()
                            ->content('This page is used to configure notifications and alerts for this service request type.'),
                        Heading::make()
                            ->two()
                            ->content('Managers'),
                        Heading::make()
                            ->three()
                            ->content('Service Request Created'),
                        Toggle::make('is_managers_service_request_created_email_enabled')
                            ->label('Email'),
                        Toggle::make('is_managers_service_request_created_notification_enabled')
                            ->label('Notification'),
                        Heading::make()
                            ->three()
                            ->content('Service Request Assigned'),
                        Toggle::make('is_managers_service_request_assigned_email_enabled')
                            ->label('Email'),
                        Toggle::make('is_managers_service_request_assigned_notification_enabled')
                            ->label('Notification'),
                        Heading::make()
                            ->three()
                            ->content('Service Request Update'),
                        Toggle::make('is_managers_service_request_update_email_enabled')
                            ->label('Email'),
                        Toggle::make('is_managers_service_request_update_notification_enabled')
                            ->label('Notification'),
                        Heading::make()
                            ->three()
                            ->content('Service Request Status Change (Non-Closed)'),
                        Toggle::make('is_managers_service_request_status_change_email_enabled')
                            ->label('Email'),
                        Toggle::make('is_managers_service_request_status_change_notification_enabled')
                            ->label('Notification'),
                        Heading::make()
                            ->three()
                            ->content('Service Request Closed'),
                        Toggle::make('is_managers_service_request_resolved_email_enabled')
                            ->label('Email'),
                        Toggle::make('is_managers_service_request_resolved_notification_enabled')
                            ->label('Notification'),
                        Heading::make()
                            ->two()
                            ->content('Auditors'),
                        Heading::make()
                            ->three()
                            ->content('Service Request Created'),
                        Toggle::make('is_auditors_service_request_created_email_enabled')
                            ->label('Email'),
                        Toggle::make('is_auditors_service_request_created_notification_enabled')
                            ->label('Notification'),
                        Heading::make()
                            ->three()
                            ->content('Service Request Assigned'),
                        Toggle::make('is_auditors_service_request_assigned_email_enabled')
                            ->label('Email'),
                        Toggle::make('is_auditors_service_request_assigned_notification_enabled')
                            ->label('Notification'),
                        Heading::make()
                            ->three()
                            ->content('Service Request Update'),
                        Toggle::make('is_auditors_service_request_update_email_enabled')
                            ->label('Email'),
                        Toggle::make('is_auditors_service_request_update_notification_enabled')
                            ->label('Notification'),
                        Heading::make()
                            ->three()
                            ->content('Service Request Status Change (Non-Closed)'),
                        Toggle::make('is_auditors_service_request_status_change_email_enabled')
                            ->label('Email'),
                        Toggle::make('is_auditors_service_request_status_change_notification_enabled')
                            ->label('Notification'),
                        Heading::make()
                            ->three()
                            ->content('Service Request Closed'),
                        Toggle::make('is_auditors_service_request_resolved_email_enabled')
                            ->label('Email'),
                        Toggle::make('is_auditors_service_request_resolved_notification_enabled')
                            ->label('Notification'),
                        Heading::make()
                            ->two()
                            ->content('Customers'),
                        Heading::make()
                            ->three()
                            ->content('Service Request Created'),
                        Toggle::make('is_customers_service_request_created_email_enabled')
                            ->label('Email'),
                        Toggle::make('is_customers_service_request_created_notification_enabled')
                            ->label('Notification'),
                        Heading::make()
                            ->three()
                            ->content('Service Request Assigned'),
                        Toggle::make('is_customers_service_request_assigned_email_enabled')
                            ->label('Email'),
                        Toggle::make('is_customers_service_request_assigned_notification_enabled')
                            ->label('Notification'),
                        Heading::make()
                            ->three()
                            ->content('Service Request Update'),
                        Toggle::make('is_customers_service_request_update_email_enabled')
                            ->label('Email'),
                        Toggle::make('is_customers_service_request_update_notification_enabled')
                            ->label('Notification'),
                        Heading::make()
                            ->three()
                            ->content('Service Request Status Change (Non-Closed)'),
                        Toggle::make('is_customers_service_request_status_change_email_enabled')
                            ->label('Email'),
                        Toggle::make('is_customers_service_request_status_change_notification_enabled')
                            ->label('Notification'),
                        Heading::make()
                            ->three()
                            ->content('Service Request Closed'),
                        Toggle::make('is_customers_service_request_closed_email_enabled')
                            ->label('Email'),
                        Toggle::make('is_customers_service_request_closed_notification_enabled')
                            ->label('Notification'),
                    ]),
            ]);
    }
}
