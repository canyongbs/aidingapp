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

namespace AidingApp\ServiceManagement\Filament\Resources\ServiceRequestResource\Pages;

use Filament\Actions;
use App\Enums\Feature;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Illuminate\Support\Collection;
use Filament\Forms\Components\Grid;
use Illuminate\Support\Facades\Gate;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use AidingApp\Division\Models\Division;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Forms\Components\EducatableSelect;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use AidingApp\ServiceManagement\Models\ServiceRequestStatus;
use AidingApp\ServiceManagement\Models\ServiceRequestFeedback;
use AidingApp\ServiceManagement\Models\ServiceRequestPriority;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestResource;
use AidingApp\ServiceManagement\Actions\ResolveUploadsMediaCollectionForServiceRequest;
use AidingApp\ServiceManagement\Notifications\SendClosedServiceFeedbackNotification;

class EditServiceRequest extends EditRecord
{
    protected static string $resource = ServiceRequestResource::class;

    public function form(Form $form): Form
    {
        $disabledStatuses = ServiceRequestStatus::onlyTrashed()->pluck('id');
        $disabledTypes = ServiceRequestType::onlyTrashed()->pluck('id');

        $uploadsMediaCollection = app(ResolveUploadsMediaCollectionForServiceRequest::class)();

        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Select::make('division_id')
                            ->relationship('division', 'name')
                            ->label('Division')
                            ->required()
                            ->exists((new Division())->getTable(), 'id'),
                        Select::make('status_id')
                            ->relationship('status', 'name')
                            ->label('Status')
                            ->options(fn (ServiceRequest $record) => ServiceRequestStatus::withTrashed()
                                ->whereKey($record->status_id)
                                ->orWhereNull('deleted_at')
                                ->orderBy('classification')
                                ->orderBy('name')
                                ->get(['id', 'name', 'classification'])
                                ->groupBy(fn (ServiceRequestStatus $status) => $status->classification->getlabel())
                                ->map(fn (Collection $group) => $group->pluck('name', 'id')))
                            ->required()
                            ->exists((new ServiceRequestStatus())->getTable(), 'id')
                            ->disableOptionWhen(fn (string $value) => $disabledStatuses->contains($value)),
                        Grid::make()
                            ->schema([
                                Select::make('type_id')
                                    ->options(
                                        fn (ServiceRequest $record) => ServiceRequestType::withTrashed()
                                            ->whereKey($record->priority?->type_id)
                                            ->orWhereNull('deleted_at')
                                            ->orderBy('name')
                                            ->pluck('name', 'id')
                                    )
                                    ->afterStateUpdated(fn (Set $set) => $set('priority_id', null))
                                    ->label('Type')
                                    ->required()
                                    ->live()
                                    ->exists(ServiceRequestType::class, 'id')
                                    ->disableOptionWhen(fn (string $value) => $disabledTypes->contains($value)),
                                Select::make('priority_id')
                                    ->relationship(
                                        name: 'priority',
                                        titleAttribute: 'name',
                                        modifyQueryUsing: fn (Get $get, Builder $query, $record) => $query->where('type_id', $get('type_id'))->orderBy('order'),
                                    )
                                    ->label('Priority')
                                    ->required()
                                    ->exists(ServiceRequestPriority::class, 'id')
                                    ->visible(fn (Get $get): bool => filled($get('type_id'))),
                            ]),
                        TextInput::make('title')
                            ->required()
                            ->string()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Textarea::make('close_details')
                            ->label('Description')
                            ->nullable()
                            ->string()
                            ->columnSpan(1),
                        Textarea::make('res_details')
                            ->label('Internal Details')
                            ->nullable()
                            ->string()
                            ->columnSpan(1),
                        EducatableSelect::make('respondent')
                            ->label('Related To')
                            ->required(),
                    ]),
                Section::make('Uploads')
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('uploads')
                            ->hiddenLabel()
                            ->visibility('private')
                            ->collection($uploadsMediaCollection->getName())
                            ->multiple($uploadsMediaCollection->getMaxNumberOfFiles() > 1)
                            ->when($uploadsMediaCollection->getMaxNumberOfFiles(), fn (SpatieMediaLibraryFileUpload $component) => $component->maxFiles($uploadsMediaCollection->getMaxNumberOfFiles()))
                            ->when($uploadsMediaCollection->getMaxFileSizeInMB(), fn (SpatieMediaLibraryFileUpload $component) => $component->maxSize($uploadsMediaCollection->getMaxFileSizeInMB() * 1000))
                            ->acceptedFileTypes($uploadsMediaCollection->getMimes())
                            ->downloadable(),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['type_id'] = $this->getRecord()->priority->type_id;

        return $data;
    }

    protected function afterSave(): void
    {
        if (Gate::check(Feature::FeedbackManagement->getGateName()) && $this->getRecord()?->priority?->type?->has_enabled_feedback_collection && $this->getRecord()?->status?->name == 'Closed') {
            //On close status of service request checking feedback collection type
            $has_enabled_csat = $this->getRecord()?->priority?->type?->has_enabled_csat;
            $has_enabled_nps = $this->getRecord()?->priority?->type?->has_enabled_nps;

            $contact = $this->getRecord()->respondent;

            $contact->notify(new SendClosedServiceFeedbackNotification($this->getRecord()));
        }
    }
}
