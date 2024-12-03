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

namespace AidingApp\Contact\Filament\Resources\ContactResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Authenticatable;
use Filament\Infolists\Infolist;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use App\Filament\Tables\Columns\IdColumn;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\RelationManagers\RelationManager;
use AidingApp\ServiceManagement\Models\ServiceRequestPriority;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestResource\Pages\ViewServiceRequest;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestResource\Pages\CreateServiceRequest;

class ServiceRequestsRelationManager extends RelationManager
{
    protected static string $relationship = 'serviceRequests';

    public function form(Form $form): Form
    {
        return (resolve(CreateServiceRequest::class))->form($form);
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return (resolve(ViewServiceRequest::class))->infolist($infolist);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->modifyQueryUsing(function ($query) {
                $query->when(! auth()->user()->hasRole(Authenticatable::SUPER_ADMIN_ROLE), function (Builder $q) {
                    return $q->whereHas('priority.type.managers', function (Builder $query): void {
                        $query->where('teams.id', auth()->user()->teams()->first()?->getKey());
                    })
                        ->orWhereHas('priority.type.auditors', function (Builder $query): void {
                            $query->where('teams.id', auth()->user()->teams()->first()?->getKey());
                        })
                        ->whereHas('respondent', function (Builder $query) {
                            $query
                                ->where('respondent_id', $this->getOwnerRecord()->getKey())
                                ->where('respondent_type', $this->getOwnerRecord()->getMorphClass());
                        });
                });
            })
            ->columns([
                IdColumn::make(),
                TextColumn::make('service_request_number')
                    ->label('Service Request #')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('priority.name')
                    ->label('Priority')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('assignedTo.user.name')
                    ->label('Assigned to')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('priority')
                    ->relationship(
                        name: 'priority',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn (Builder $query) => $query
                            ->whereHas('type')
                            ->with('type')
                    )
                    ->getOptionLabelFromRecordUsing(fn (ServiceRequestPriority $record) => "{$record->type->name} - {$record->name}")
                    ->multiple()
                    ->preload(),
                SelectFilter::make('status')
                    ->relationship('status', 'name')
                    ->multiple()
                    ->preload(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('New Service Request')
                    ->modalHeading('Create new service request'),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make()
                    ->mutateRecordDataUsing(function (array $data, $record) {
                        $data['type_id'] = $record?->priority?->type_id;

                        return $data;
                    }),
            ])
            ->bulkActions([
            ])
            ->defaultSort('created_at', 'desc');
    }
}
