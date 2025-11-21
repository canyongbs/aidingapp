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

namespace AidingApp\ServiceManagement\Filament\Resources\ServiceRequestResource\Pages;

use AidingApp\Contact\Models\Contact;
use AidingApp\Division\Models\Division;
use AidingApp\ServiceManagement\Actions\CreateServiceRequestAction;
use AidingApp\ServiceManagement\Actions\GenerateServiceRequestFilamentFormSchema;
use AidingApp\ServiceManagement\DataTransferObjects\ServiceRequestDataObject;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestResource;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestFormField;
use AidingApp\ServiceManagement\Models\ServiceRequestFormStep;
use AidingApp\ServiceManagement\Models\ServiceRequestPriority;
use AidingApp\ServiceManagement\Models\ServiceRequestStatus;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use AidingApp\ServiceManagement\Rules\ManagedServiceRequestType;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreateServiceRequest extends CreateRecord
{
    protected static string $resource = ServiceRequestResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('division_id')
                    ->relationship('division', 'name')
                    ->label('Division')
                    ->required()
                    ->exists((new Division())->getTable(), 'id')
                    ->default(auth()->user()->team()->count() ? auth()->user()?->team?->division?->id : ''),
                Select::make('status_id')
                    ->relationship('status', 'name')
                    ->label('Status')
                    ->allowHtml()
                    ->options(fn () => ServiceRequestStatus::query()
                        ->orderBy('classification')
                        ->orderBy('name')
                        ->get(['id', 'name', 'classification', 'color'])
                        ->groupBy(fn (ServiceRequestStatus $status) => $status->classification->getlabel())
                        ->map(fn (Collection $group) => $group->mapWithKeys(fn (ServiceRequestStatus $status): array => [
                            $status->getKey() => view('service-management::components.service-request-status-select-option-label', ['status' => $status])->render(),
                        ])))
                    ->required()
                    ->exists((new ServiceRequestStatus())->getTable(), 'id'),
                Grid::make()
                    ->schema([
                        Select::make('type_id')
                            ->options(ServiceRequestType::when(! auth()->user()->isSuperAdmin(), function (Builder $query) {
                                $query->whereHas('managers', function (Builder $query): void {
                                    $query->where('teams.id', auth()->user()->team?->getKey());
                                });
                            })
                                ->pluck('name', 'id'))
                            ->rule(new ManagedServiceRequestType())
                            ->afterStateUpdated(function (Set $set, Select $component) {
                                $set('priority_id', null);
                                $component
                                    ->getContainer()
                                    ->getParentComponent()
                                    ->getContainer()
                                    ->getComponent('dynamicTypeFields')
                                    ?->getChildComponentContainer()
                                    ->fill();
                            })
                            ->label('Type')
                            ->required()
                            ->live()
                            ->exists(ServiceRequestType::class, 'id'),
                        Select::make('priority_id')
                            ->relationship(
                                name: 'priority',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn (Get $get, Builder $query) => $query->where('type_id', $get('type_id'))->orderBy('order'),
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
                Select::make('respondent_id')
                    ->relationship('respondent', 'full_name')
                    ->label('Related To')
                    ->required()
                    ->exists((new Contact())->getTable(), 'id'),
                Section::make('Additional Information')
                    ->schema(fn (Get $get): array => $this->getDynamicFields($get('type_id')))
                    ->statePath('dynamic_fields')
                    ->key('dynamicTypeFields')
                    ->visible(fn (Get $get): bool => filled($get('type_id')) && ! empty($this->getDynamicFields($get('type_id')))),
            ]);
    }

    /**
     * @return array<Component>
     */
    protected function getDynamicFields(?string $typeId): array
    {
        if (! $typeId) {
            return [];
        }

        $type = ServiceRequestType::with('form.fields', 'form.steps.fields')->find($typeId);

        if (! $type || ! $type->form) {
            return [];
        }

        return app(GenerateServiceRequestFilamentFormSchema::class)($type->form);
    }

    protected function handleRecordCreation(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            $dynamicFields = $data['dynamic_fields'] ?? [];
            $typeId = $data['type_id'];
            unset($data['dynamic_fields']);

            $serviceRequestDataObject = ServiceRequestDataObject::fromData($data);
            $serviceRequest = app(CreateServiceRequestAction::class)->execute($serviceRequestDataObject);

            $this->saveDynamicFormFields($serviceRequest, $typeId, $dynamicFields);

            return $serviceRequest;
        });
    }

    /**
     * @param  array<string, mixed>  $dynamicFields
     */
    protected function saveDynamicFormFields(ServiceRequest $serviceRequest, string $typeId, array $dynamicFields): void
    {
        if (empty($dynamicFields)) {
            return;
        }

        $type = ServiceRequestType::with('form.fields', 'form.steps.fields')->find($typeId);

        if (! $type || ! $type->form) {
            return;
        }

        $form = $type->form;

        $submission = $form->submissions()->make([
            'submitted_at' => now(),
        ]);

        $submission->author()->associate(auth()->user());
        $submission->priority()->associate($serviceRequest->priority);
        $submission->save();

        /** @var Collection<string, ServiceRequestFormField> $allFields */
        $allFields = collect();

        /** @var Collection<int, ServiceRequestFormField> $fields */
        $fields = $form->fields;
        $fields->each(function (ServiceRequestFormField $field) use (&$allFields) {
            $allFields->put($field->id, $field);
        });

        if ($form->steps->isNotEmpty()) {
            /** @var Collection<int, ServiceRequestFormStep> $steps */
            $steps = $form->steps;
            $steps->each(function (ServiceRequestFormStep $step) use (&$allFields) {
                /** @var Collection<int, ServiceRequestFormField> $stepFields */
                $stepFields = $step->fields;
                $stepFields->each(function (ServiceRequestFormField $field) use (&$allFields) {
                    $allFields->put($field->id, $field);
                });
            });
        }

        foreach ($dynamicFields as $fieldKey => $response) {
            $field = $allFields->get($fieldKey);

            if ($field && filled($response)) {
                $submission->fields()->attach($field->id, [
                    'id' => Str::orderedUuid(),
                    'response' => $response,
                ]);
            }
        }

        $serviceRequest->serviceRequestFormSubmission()->associate($submission);
        $serviceRequest->save();
    }
}
