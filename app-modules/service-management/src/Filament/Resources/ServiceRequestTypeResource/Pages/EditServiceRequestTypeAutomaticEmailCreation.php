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
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use App\Concerns\EditPageRedirection;
use App\Models\Tenant;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class EditServiceRequestTypeAutomaticEmailCreation extends EditRecord
{
    use EditPageRedirection;

    protected static string $resource = ServiceRequestTypeResource::class;

    protected static ?string $navigationLabel = ' Email Creation';

    protected static ?string $title = 'Automated Email Creation';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Toggle::make('is_email_automatic_creation_enabled')
                            ->label('Enable Creation by Email')
                            ->live(),
                        TextInput::make('domain')
                            ->label('Domain')
                            ->prefix(function () {
                                $currentTenantDomain = Tenant::current()?->domain;

                                assert(! is_null($currentTenantDomain));

                                return rtrim($currentTenantDomain, '.' . parse_url(Config::string('app.landlord_url'), PHP_URL_HOST)) . '-';
                            })
                            ->suffix(fn () => '@' . Config::string('mail.from.root_domain'))
                            ->required()
                            ->visible(fn (Get $get): bool => $get('is_email_automatic_creation_enabled'))
                            ->columnSpan(1),
                        Select::make('email_automatic_creation_priority_id')
                            ->label('Default Inbound Priority')
                            ->relationship(
                                name: 'emailAutomaticCreationPriority',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn (ServiceRequestType $record, Builder $query) => $query->whereRelation('type', 'id', $record->id)
                                    ->orderBy('order')
                            )
                            ->preload()
                            ->searchable()
                            ->required()
                            ->visible(fn (Get $get): bool => $get('is_email_automatic_creation_enabled'))
                            ->columnSpanFull(),
                        Checkbox::make('is_email_automatic_creation_contact_create_enabled')
                            ->label('Auto create contact if eligible')
                            ->visible(fn (Get $get): bool => $get('is_email_automatic_creation_enabled')),
                    ]),
            ]);
    }

    public function getRelationManagers(): array
    {
        // Needed to prevent Filament from loading the relation managers on this page.
        return [];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $record = $this->getRecord();

        assert($record instanceof ServiceRequestType);

        $data['domain'] = $record->domain?->domain;

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        assert($record instanceof ServiceRequestType);

        if (isset($data['domain'])) {
            $tenantId = Tenant::current()?->getKey();

            assert(! is_null($tenantId));

            $record->domain()
                ->updateOrCreate(
                    ['tenant_id' => $tenantId],
                    ['domain' => $data['domain']]
                );

            unset($data['domain']);
        }

        $record->update($data);

        return $record;
    }
}
