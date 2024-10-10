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

namespace AidingApp\ServiceManagement\Filament\Resources\ServiceRequestTypeResource\Pages;

use Filament\Forms\Form;
use Filament\Forms\Components\Section;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Forms\Components\Heading;
use App\Filament\Forms\Components\Paragraph;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestTypeResource;
use Filament\Forms\Components\Radio;

class EditServiceRequestTypeAssignment extends EditRecord
{
    protected static string $resource = ServiceRequestTypeResource::class;

    protected static ?string $title = 'Assignments';

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
                            ->content('Assignments'),
                        Paragraph::make()
                            ->content('This page is used to configure the assignment methodology for this service request type.'),
                        Radio::make('assignment_type')
                            ->label('Assignment Type')
                            ->options([
                                'round_robin' => 'Round Robin',
                                'least_busy' => 'Least Busy',
                                'random' => 'Random',
                            ]),
                        Heading::make()
                            ->three()
                            ->content('Assignment Types'),
                        Paragraph::make()
                            ->content('None: No assignment is made when this option is selected, allowing service requests to remain unassigned until manual intervention. Ideal for flexible workflows where task assignment is determined later.'),
                        Paragraph::make()
                            ->content('Individual: All service requests are assigned to a specific manager. Best suited for cases with a dedicated resource responsible for managing tasks, ensuring consistent oversight and accountability.'),
                        Paragraph::make()
                            ->content('Round Robin: Service requests are distributed evenly among request managers in a circular order. This ensures fair ticket allocation but doesn\'t factor in current workloads.'),
                        Paragraph::make()
                            ->content('Workload: Assignments are made based on the current workload, with requests directed to the user handling the fewest open tickets. This method balances workloads, improving efficiency and avoiding bottlenecks.'),
                    ]),
            ]);
    }
}
