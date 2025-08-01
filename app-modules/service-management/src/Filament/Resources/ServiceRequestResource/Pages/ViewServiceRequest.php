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

use AidingApp\Contact\Filament\Resources\ContactResource;
use AidingApp\Contact\Models\Contact;
use AidingApp\ServiceManagement\Actions\ResolveUploadsMediaCollectionForServiceRequest;
use AidingApp\ServiceManagement\Enums\SlaComplianceStatus;
use AidingApp\ServiceManagement\Filament\Concerns\ServiceRequestLocked;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestResource;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\Actions\Action as InfolistAction;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\IconEntry\IconEntrySize;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ViewServiceRequest extends ViewRecord
{
    use ServiceRequestLocked;

    protected static string $resource = ServiceRequestResource::class;

    protected static ?string $navigationLabel = 'View';

    public function infolist(Infolist $infolist): Infolist
    {
        $formatSecondsAsInterval = fn (?int $state): ?string => $state ? CarbonInterval::seconds($state)->cascade()->forHumans(short: true) : null;

        $uploadsMediaCollection = app(ResolveUploadsMediaCollectionForServiceRequest::class)->__invoke();

        return $infolist
            ->schema([
                Section::make()
                    ->schema([
                        TextEntry::make('service_request_number')
                            ->label('Service Request Number'),
                        TextEntry::make('division.name')
                            ->label('Division'),
                        TextEntry::make('status.name')
                            ->label('Status')
                            ->badge()
                            ->color(fn (ServiceRequest $record): string => $record->status->color->value),
                        TextEntry::make('title'),
                        TextEntry::make('priority.name')
                            ->label('Priority'),
                        TextEntry::make('priority.type.name')
                            ->state(
                                fn (ServiceRequest $record) => $record->priority->type()->withTrashed()->first()?->name
                            )
                            ->label('Type'),
                        TextEntry::make('close_details')
                            ->label('Description')
                            ->columnSpanFull(),
                        TextEntry::make('res_details')
                            ->label('Internal Details')
                            ->columnSpanFull(),
                        TextEntry::make('respondent')
                            ->label('Related To')
                            ->color('primary')
                            ->state(function (ServiceRequest $record): string {
                                /** @var Contact $respondent */
                                $respondent = $record->respondent;

                                return match ($respondent::class) {
                                    Contact::class => "{$respondent->{Contact::displayNameKey()}} (Contact)",
                                };
                            })
                            ->url(function (ServiceRequest $record) {
                                /** @var Contact $respondent */
                                $respondent = $record->respondent;

                                return match ($respondent::class) {
                                    Contact::class => ContactResource::getUrl('view', ['record' => $respondent->id]),
                                };
                            }),
                        TextEntry::make('time_to_resolution')
                            ->label('Time to Resolution')
                            ->formatStateUsing(function ($state) {
                                $interval = Carbon::now()->diffAsCarbonInterval(Carbon::now()->addSeconds($state));
                                $days = $interval->d;
                                $hours = $interval->h;
                                $minutes = $interval->i;

                                return "{$days}d {$hours}h {$minutes}m";
                            })
                            ->columnSpan(1),
                    ])
                    ->columns(),
                Section::make('Uploads')
                    ->visible(fn (ServiceRequest $record): bool => $record->hasMedia($uploadsMediaCollection->getName()))
                    ->schema(
                        fn (ServiceRequest $record) => $record
                            ->getMedia($uploadsMediaCollection->getName())
                            ->map(function (Media $media) {
                                $mimeType = $media->mime_type;
                                $isImage = in_array($mimeType, ['image/jpeg', 'image/png']);

                                $downloadAction = InfolistAction::make('download')
                                    ->label('Download')
                                    ->icon('heroicon-m-arrow-down-tray')
                                    ->color('primary')
                                    ->url($media->getTemporaryUrl(now()->addMinute()), true);

                                if ($isImage) {
                                    return ImageEntry::make($media->getKey())
                                        ->label($media->name)
                                        ->visibility('private')
                                        ->getStateUsing($media->getTemporaryUrl(now()->addMinute()))
                                        ->hintAction($downloadAction);
                                }

                                return IconEntry::make($media->getKey())
                                    ->label($media->name)
                                    ->state($mimeType)
                                    ->icon(match ($mimeType) {
                                        'application/pdf',
                                        'application/vnd.ms-word',
                                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                        'image/pdf',
                                        'text/markdown',
                                        'text/plain' => 'heroicon-o-document-text',
                                        'application/vnd.ms-excel',
                                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                        'text/csv' => 'heroicon-o-table-cells',
                                        'application/vnd.ms-powerpoint',
                                        'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'heroicon-o-presentation-chart-bar',
                                        default => 'heroicon-o-paper-clip',
                                    })
                                    ->size(IconEntrySize::TwoExtraLarge)
                                    ->hintAction($downloadAction);
                            })
                            ->toArray()
                    ),
                Section::make('SLA Management')
                    ->visible(fn (ServiceRequest $record): bool => $record->priority?->sla !== null)
                    ->schema([
                        Group::make([
                            TextEntry::make('sla_response_seconds')
                                ->label('Response agreement')
                                ->state(fn (ServiceRequest $record): ?int => $record->getSlaResponseSeconds())
                                ->formatStateUsing($formatSecondsAsInterval)
                                ->placeholder('-'),
                            TextEntry::make('response_age')
                                ->label('Response age')
                                ->state(fn (ServiceRequest $record): ?int => $record->getLatestResponseSeconds())
                                ->formatStateUsing($formatSecondsAsInterval)
                                ->placeholder('-'),
                            TextEntry::make('response_sla_compliance')
                                ->label('Response compliance')
                                ->badge()
                                ->state(fn (ServiceRequest $record): ?SlaComplianceStatus => $record->getResponseSlaComplianceStatus()),
                        ]),
                        Group::make([
                            TextEntry::make('sla_resolution_seconds')
                                ->label('Resolution agreement')
                                ->state(fn (ServiceRequest $record): ?int => $record->getSlaResolutionSeconds())
                                ->formatStateUsing($formatSecondsAsInterval)
                                ->placeholder('-'),
                            TextEntry::make('resolution_seconds')
                                ->label('Resolution age')
                                ->state(fn (ServiceRequest $record): int => $record->getResolutionSeconds())
                                ->formatStateUsing($formatSecondsAsInterval)
                                ->placeholder('-'),
                            TextEntry::make('resolution_sla_compliance')
                                ->label('Resolution compliance')
                                ->badge()
                                ->state(fn (ServiceRequest $record): ?SlaComplianceStatus => $record->getResolutionSlaComplianceStatus()),
                        ]),
                    ])
                    ->columns(),

                Section::make('Feedback')
                    ->visible(fn (ServiceRequest $record): bool => $record->feedback()->exists())
                    ->schema([
                        TextEntry::make('feedback.csat_answer')
                            ->label('CSAT')
                            ->badge(),
                        TextEntry::make('feedback.nps_answer')
                            ->label('NPS')
                            ->badge(),
                    ])
                    ->columns(),
                Section::make('Form Submission Details')
                    ->collapsed()
                    ->visible(fn (ServiceRequest $record): bool => ! is_null($record->serviceRequestFormSubmission))
                    ->schema([
                        TextEntry::make('serviceRequestFormSubmission.submitted_at')
                            ->dateTime(),
                        ViewEntry::make('serviceRequestFormSubmission')
                            ->view('filament.infolists.components.submission-entry'),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
