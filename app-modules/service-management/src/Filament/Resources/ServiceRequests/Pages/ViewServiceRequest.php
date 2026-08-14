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

namespace AidingApp\ServiceManagement\Filament\Resources\ServiceRequests\Pages;

use AidingApp\Contact\Filament\Resources\ContactResource;
use AidingApp\Contact\Models\Contact;
use AidingApp\Division\Models\Division;
use AidingApp\ServiceManagement\Actions\ResolveUploadsMediaCollectionForServiceRequest;
use AidingApp\ServiceManagement\Enums\ServiceRequestTab;
use AidingApp\ServiceManagement\Enums\SlaComplianceStatus;
use AidingApp\ServiceManagement\Filament\Actions\ReclassifyServiceRequestAction;
use AidingApp\ServiceManagement\Filament\Concerns\ServiceRequestLocked;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequests\RelationManagers\AssignedToRelationManager;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequests\RelationManagers\AssignmentHistoryRelationManager;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequests\RelationManagers\CreatedByRelationManager;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequests\RelationManagers\ServiceRequestConversationsRelationManager;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequests\RelationManagers\ServiceRequestUpdatesRelationManager;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequests\ServiceRequestResource;
use AidingApp\ServiceManagement\Filament\Widgets\ServiceRequestMediaTable;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestAssignment;
use AidingApp\ServiceManagement\Models\ServiceRequestFeedback;
use AidingApp\ServiceManagement\Models\ServiceRequestHistory;
use AidingApp\ServiceManagement\Models\ServiceRequestUpdate;
use AidingApp\Timeline\Livewire\TimelineList;
use App\Enums\Feature;
use App\Filament\Concerns\FiltersManagersFromGroups;
use App\Settings\DisplaySettings;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\HtmlString;
use Livewire\Attributes\Url;

class ViewServiceRequest extends ViewRecord
{
    use FiltersManagersFromGroups;
    use ServiceRequestLocked;

    protected static string $resource = ServiceRequestResource::class;

    #[Url]
    public ?string $tab = null;

    public function mount(int | string $record): void
    {
        parent::mount($record);

        $this->tab = (ServiceRequestTab::tryFrom($this->tab ?? '') ?? ServiceRequestTab::default())->value;
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->schema([
                $this->headerSection(),
                Tabs::make()
                    ->columnSpanFull()
                    ->livewireProperty('tab')
                    ->tabs([
                        ServiceRequestTab::Request->value => Tab::make(ServiceRequestTab::Request->getLabel())
                            ->schema($this->requestTabSchema()),
                        ServiceRequestTab::Files->value => Tab::make(ServiceRequestTab::Files->getLabel())
                            ->schema($this->filesTabSchema()),
                        ServiceRequestTab::Assignments->value => Tab::make(ServiceRequestTab::Assignments->getLabel())
                            ->visible(fn (ServiceRequest $record): bool => filled($this->assignmentRelationManagers($record)))
                            ->schema(fn (ServiceRequest $record): array => $this->assignmentRelationManagers($record)),
                        ServiceRequestTab::Updates->value => Tab::make(ServiceRequestTab::Updates->getLabel())
                            ->visible(fn (ServiceRequest $record): bool => ServiceRequestUpdatesRelationManager::canViewForRecord($record, static::class))
                            ->schema([
                                Livewire::make(ServiceRequestUpdatesRelationManager::class, fn (ServiceRequest $record): array => [
                                    'ownerRecord' => $record,
                                    'pageClass' => static::class,
                                ])->key(ServiceRequestUpdatesRelationManager::class),
                            ]),
                        ServiceRequestTab::Feedback->value => Tab::make(ServiceRequestTab::Feedback->getLabel())
                            ->visible(fn (ServiceRequest $record): bool => $this->canViewFeedback($record))
                            ->schema($this->feedbackTabSchema()),
                        ServiceRequestTab::Chats->value => Tab::make(ServiceRequestTab::Chats->getLabel())
                            ->visible(fn (ServiceRequest $record): bool => ServiceRequestConversationsRelationManager::canViewForRecord($record, static::class))
                            ->schema([
                                Livewire::make(ServiceRequestConversationsRelationManager::class, fn (ServiceRequest $record): array => [
                                    'ownerRecord' => $record,
                                    'pageClass' => static::class,
                                ])->key(ServiceRequestConversationsRelationManager::class),
                            ]),
                        ServiceRequestTab::Timeline->value => Tab::make(ServiceRequestTab::Timeline->getLabel())
                            ->visible(fn (): bool => $this->canViewTimeline())
                            ->schema([
                                Livewire::make(TimelineList::class, fn (ServiceRequest $record): array => [
                                    'record' => $record,
                                    'modelsToTimeline' => [
                                        ServiceRequestUpdate::class,
                                        ServiceRequestAssignment::class,
                                        ServiceRequestHistory::class,
                                    ],
                                    'emptyStateMessage' => 'There is no timeline available for this Service Request.',
                                    'noMoreRecordsMessage' => "You have reached the end of this service request's timeline.",
                                ])->key(TimelineList::class),
                            ]),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            ReclassifyServiceRequestAction::make('reclassify')
                ->record($this->getRecord())
                ->slideOver(),
        ];
    }

    private function headerSection(): Section
    {
        return Section::make()
            ->heading(fn (ServiceRequest $record): HtmlString => new HtmlString(
                view('filament.infolists.components.service-request-heading', [
                    'serviceRequestNumber' => $record->service_request_number,
                    'category' => $record->category,
                    'type' => $record->priority?->type()->first()?->name,
                ])->render()
            ))
            ->schema([
                TextEntry::make('division.name')
                    ->visible(fn (): bool => Division::count() > 1)
                    ->label('Division'),
                Grid::make(3)
                    ->schema([
                        TextEntry::make('respondent')
                            ->label('Customer Contact')
                            ->color('primary')
                            ->html()
                            ->state(function (ServiceRequest $record): string {
                                /** @var Contact $respondent */
                                $respondent = $record->respondent;
                                $organizationName = $respondent->organization->name ?? 'Unaffiliated';

                                return "{$respondent->{Contact::displayNameKey()}} ({$respondent->type->name})<br>{$organizationName}";
                            })
                            ->url(function (ServiceRequest $record) {
                                /** @var Contact $respondent */
                                $respondent = $record->respondent;

                                return ContactResource::getUrl('view', ['record' => $respondent->id]);
                            }),
                        TextEntry::make('created_at')
                            ->label('Created')
                            ->dateTime()
                            ->hintIcon(null),
                        TextEntry::make('updated_at')
                            ->label('Last Updated')
                            ->dateTime()
                            ->hintIcon(null),
                    ])->columns(3),
                Grid::make(3)
                    ->schema([
                        TextEntry::make('status.name')
                            ->label('Status')
                            ->badge()
                            ->color(fn (ServiceRequest $record): string => $record->status->color->value),
                        TextEntry::make('priority.name')
                            ->label('Priority'),
                    ])->columns(3),
            ])
            ->columns();
    }

    /**
     * @return array<Section>
     */
    private function requestTabSchema(): array
    {
        $formatSecondsAsInterval = fn (?int $state): ?string => $state ? CarbonInterval::seconds($state)->cascade()->forHumans(short: true) : null;

        return [
            Section::make('Title')
                ->schema([
                    TextEntry::make('title')
                        ->hiddenLabel(),
                ]),
            Section::make('Description')
                ->schema([
                    TextEntry::make('close_details')
                        ->hiddenLabel()
                        ->markdown(),
                ]),
            Section::make('Form Details')
                ->visible(fn (ServiceRequest $record): bool => ! is_null($record->serviceRequestFormSubmission))
                ->schema([
                    Grid::make(3)
                        ->schema([
                            TextEntry::make('serviceRequestFormSubmission.submitted_at')
                                ->label('Submitted')
                                ->dateTime(),
                            TextEntry::make('serviceRequestFormSubmission.author.email')
                                ->label('Submitted By')
                                ->color('primary')
                                ->url(function (ServiceRequest $record): ?string {
                                    $author = $record->serviceRequestFormSubmission?->author;

                                    return $author
                                        ? resolve($author::filamentResource())->getUrl('view', ['record' => $author])
                                        : null;
                                })
                                ->placeholder('-'),
                            TextEntry::make('serviceRequestFormSubmission.author_type')
                                ->label('Submitted By Type')
                                ->badge()
                                ->color('success')
                                ->formatStateUsing(fn (?string $state): ?string => filled($state) ? ucfirst($state) : null)
                                ->placeholder('-'),
                        ]),
                    ViewEntry::make('serviceRequestFormSubmission')
                        ->view('filament.infolists.components.submission-entry'),
                ]),
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
                            ->state(fn (ServiceRequest $record): int => $record->getLatestResponseSeconds())
                            ->formatStateUsing($formatSecondsAsInterval)
                            ->placeholder('-'),
                        TextEntry::make('response_sla_compliance')
                            ->label('Response compliance')
                            ->badge()
                            ->state(fn (ServiceRequest $record): ?SlaComplianceStatus => $record->getResponseSlaComplianceStatus()),
                        TextEntry::make('time_to_resolution')
                            ->label('Time to Resolution')
                            ->formatStateUsing(function (int $state) {
                                $interval = Carbon::now()->diffAsCarbonInterval(Carbon::now()->addSeconds($state));
                                $days = $interval->d;
                                $hours = $interval->h;
                                $minutes = $interval->i;

                                return "{$days}d {$hours}h {$minutes}m";
                            }),
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
        ];
    }

    /**
     * @return array<Section>
     */
    private function filesTabSchema(): array
    {
        $uploadsMediaCollection = app(ResolveUploadsMediaCollectionForServiceRequest::class)->__invoke();

        return [
            Section::make()
                ->schema(fn (ServiceRequest $record): array => [
                    Livewire::make(ServiceRequestMediaTable::class, [
                        'record' => $record,
                        'collectionName' => $uploadsMediaCollection->getName(),
                    ]),
                ]),
        ];
    }

    /**
     * @return array<Section>
     */
    private function feedbackTabSchema(): array
    {
        return [
            Section::make()
                ->visible(fn (ServiceRequest $record): bool => $record->feedback()->exists())
                ->schema([
                    TextEntry::make('feedback.csat_answer')
                        ->label('Customer Satisfaction (CSAT)')
                        ->default('N/A'),
                    TextEntry::make('feedback.nps_answer')
                        ->label('Net Promoter Score (NPS)')
                        ->default('N/A'),
                ])
                ->columns(),
            Section::make()
                ->visible(fn (ServiceRequest $record): bool => ! $record->feedback()->exists())
                ->schema([
                    TextEntry::make('feedback_notice')
                        ->color('primary')
                        ->hiddenLabel()
                        ->state(fn (ServiceRequest $record): string => $this->buildFeedbackNoticeMessage($record))
                        ->html(),
                ])
                ->columns(1),
        ];
    }

    /**
     * @return array<Livewire>
     */
    private function assignmentRelationManagers(ServiceRequest $record): array
    {
        return collect([
            AssignedToRelationManager::class,
            AssignmentHistoryRelationManager::class,
            CreatedByRelationManager::class,
        ])
            ->map(fn (string $relationManager) => self::filterRelationManagers($relationManager, $record))
            ->filter()
            ->map(fn (string $relationManager): Livewire => Livewire::make($relationManager, [
                'ownerRecord' => $record,
                'pageClass' => static::class,
            ])->key($relationManager))
            ->values()
            ->all();
    }

    private function canViewFeedback(ServiceRequest $record): bool
    {
        return Gate::check(Feature::FeedbackManagement->getGateName())
            && auth()->user()->can('viewAny', [ServiceRequestFeedback::class, $record]);
    }

    private function canViewTimeline(): bool
    {
        return auth()->user()->can(['engagement.view-any', 'engagement.*.view']);
    }

    private function buildFeedbackNoticeMessage(ServiceRequest $serviceRequest): string
    {
        if (! ($serviceRequest->priority?->type->has_enabled_feedback_collection ?? false)) {
            return __('service-management::service_requests.feedback.type_feedback_disabled');
        }

        if (! $serviceRequest->isResolved()) {
            return __('service-management::service_requests.feedback.not_closed');
        }

        if (blank($serviceRequest->survey_sent_at)) {
            return __('service-management::service_requests.feedback.no_survey_sent');
        }

        $timezone = app(DisplaySettings::class)->getTimezone();
        $sentAt = $serviceRequest->survey_sent_at->setTimezone($timezone)->format('M j, Y g:i a (T)');
        $message = __('service-management::service_requests.feedback.survey_sent', ['sent_at' => $sentAt]);

        if (filled($serviceRequest->reminder_sent_at)) {
            $reminderAt = $serviceRequest->reminder_sent_at->setTimezone($timezone)->format('M j, Y g:i a (T)');
            $message .= '<br>' . __('service-management::service_requests.feedback.reminder_sent', ['reminder_at' => $reminderAt]);
        }

        return $message;
    }
}
