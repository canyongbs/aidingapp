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

use AidingApp\InAppCommunication\Models\Message;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequests\ServiceRequestResource;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestConversation;
use App\Enums\Feature;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Locked;

class ViewLiveChatTranscript extends Page
{
    use InteractsWithRecord;

    protected static string $resource = ServiceRequestResource::class;

    protected static ?string $navigationLabel = null;

    protected ?string $heading = 'Live Chat Transcript';

    protected static ?string $breadcrumb = 'View Transcript';

    protected string $view = 'service-management::filament.resources.service-requests.pages.view-live-chat-transcript';

    #[Locked]
    public ServiceRequestConversation $conversation;

    public function mount(int | string $record, ServiceRequestConversation $conversation): void
    {
        $this->record = $this->resolveRecord($record);

        abort_unless(Gate::check(Feature::RealtimeChat->getGateName()), 403);

        Gate::authorize('view', $this->record);

        abort_unless(
            $conversation->serviceRequest()->is($this->record) && $conversation->conversation()->exists(),
            404,
        );

        $this->conversation = $conversation;
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->state($this->conversation)
            ->schema([
                Section::make()
                    ->schema([
                        TextEntry::make('contact.full_name')
                            ->label('Contact'),
                        TextEntry::make('user.name')
                            ->label('Agent'),
                        TextEntry::make('queued_at')
                            ->label('Queued')
                            ->dateTime(),
                        TextEntry::make('accepted_at')
                            ->label('Accepted')
                            ->dateTime(),
                        TextEntry::make('finished_at')
                            ->label('Finished')
                            ->dateTime(),
                        TextEntry::make('finished_reason')
                            ->label('Finish Reason')
                            ->badge(),
                    ])
                    ->columns(3),
            ]);
    }

    /**
     * @return Collection<int, Message>
     */
    public function getMessages(): Collection
    {
        return $this->conversation->conversation
            ->messages()
            ->with('author')
            ->orderBy('created_at')
            ->get();
    }

    /**
     * @return array<string>
     */
    public function getBreadcrumbs(): array
    {
        $record = $this->record;
        assert($record instanceof ServiceRequest);

        return [
            ...$this->getResourceBreadcrumbs(),
            ServiceRequestResource::getUrl('view', ['record' => $record]) => $this->getRecordTitle(),
            ServiceRequestResource::getUrl('manage-live-chats', ['record' => $record]) => 'Live Chats',
            $this->getBreadcrumb(),
        ];
    }
}
