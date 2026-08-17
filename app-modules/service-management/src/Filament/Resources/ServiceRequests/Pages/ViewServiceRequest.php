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

use AidingApp\ServiceManagement\Enums\ServiceRequestTab;
use AidingApp\ServiceManagement\Filament\Actions\ReclassifyServiceRequestAction;
use AidingApp\ServiceManagement\Filament\Concerns\ServiceRequestLocked;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequests\RelationManagers\AssignedToRelationManager;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequests\RelationManagers\AssignmentHistoryRelationManager;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequests\RelationManagers\CreatedByRelationManager;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequests\RelationManagers\ServiceRequestConversationsRelationManager;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequests\RelationManagers\ServiceRequestUpdatesRelationManager;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequests\Schemas\ServiceRequestInfolist;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequests\ServiceRequestResource;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestAssignment;
use AidingApp\ServiceManagement\Models\ServiceRequestFeedback;
use AidingApp\ServiceManagement\Models\ServiceRequestHistory;
use AidingApp\ServiceManagement\Models\ServiceRequestUpdate;
use AidingApp\Timeline\Livewire\TimelineList;
use App\Enums\Feature;
use App\Filament\Concerns\FiltersManagersFromGroups;
use App\Settings\DisplaySettings;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Gate;
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
                ServiceRequestInfolist::headerSection(),
                Tabs::make()
                    ->columnSpanFull()
                    ->livewireProperty('tab')
                    ->tabs([
                        ServiceRequestTab::Request->value => Tab::make(ServiceRequestTab::Request->getLabel())
                            ->schema(ServiceRequestInfolist::detailSections()),
                        ServiceRequestTab::Files->value => Tab::make(ServiceRequestTab::Files->getLabel())
                            ->schema([ServiceRequestInfolist::filesSection()]),
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
