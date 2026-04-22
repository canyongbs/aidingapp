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

namespace AidingApp\Engagement\Filament\Actions;

use AidingApp\Engagement\Actions\CreateEngagement;
use AidingApp\Engagement\DataTransferObjects\EngagementCreationData;
use AidingApp\Engagement\Filament\Schemas\Components\EngagementBodyInput;
use AidingApp\Engagement\Filament\Schemas\Components\EngagementScheduledAtDateTimePicker;
use AidingApp\Engagement\Filament\Schemas\Components\EngagementSendLaterToggle;
use AidingApp\Engagement\Filament\Schemas\Components\EngagementSubjectInput;
use AidingApp\Engagement\Models\Engagement;
use AidingApp\Notification\Enums\NotificationChannel;
use AidingApp\Notification\Models\Contracts\CanBeNotified;
use Closure;
use Exception;
use Filament\Actions\Action;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class SendEngagementAction extends Action
{
    protected bool $showDraftWithAi = true;

    protected ?Closure $draftWithAiActionUsing = null;

    protected ?Closure $resolveRecipientsUsing = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->icon('heroicon-m-chat-bubble-bottom-center-text')
            ->label('New')
            ->modalHeading('Create new email')
            ->slideOver()
            ->model(Engagement::class)
            ->authorize(fn () => auth()->user()->can('create', [Engagement::class, null]))
            ->schema(fn (): array => $this->getFormSchema())
            ->action(function (array $data, Schema $schema) {
                $this->createEngagement($data, $schema);
            })
            ->modalSubmitActionLabel('Send')
            ->modalCloseButton(false)
            ->closeModalByClickingAway(false)
            ->closeModalByEscaping(false)
            ->modalCancelAction(false)
            ->extraModalFooterActions([
                Action::make('cancel')
                    ->color('gray')
                    ->cancelParentActions()
                    ->requiresConfirmation()
                    ->action(fn () => null)
                    ->modalSubmitAction(fn (Action $action) => $action->color('danger')),
            ]);
    }

    public static function getDefaultName(): ?string
    {
        return 'engage';
    }

    public function draftWithAi(bool $show = true): static
    {
        $this->showDraftWithAi = $show;

        return $this;
    }

    public function draftWithAiAction(Closure $callback): static
    {
        $this->draftWithAiActionUsing = $callback;

        return $this;
    }

    /**
     * @return array<string>
     */
    public static function getDefaultMergeTags(): array
    {
        return Engagement::getMergeTags();
    }

    /**
     * @param  Closure(static): Collection<int, Model&CanBeNotified>  $callback
     */
    public function resolveRecipientsUsing(Closure $callback): static
    {
        $this->resolveRecipientsUsing = $callback;

        return $this;
    }

    /**
     * @return array<Component>
     */
    protected function getFormSchema(): array
    {
        return [
            Fieldset::make('Content')
                ->schema([
                    EngagementSubjectInput::make(),
                    EngagementBodyInput::make(),
                    ...($this->showDraftWithAi ? [
                        Actions::make([
                            $this->getDraftWithAiAction(),
                        ]),
                    ] : []),
                ]),
            Fieldset::make('Send your email')
                ->schema([
                    EngagementSendLaterToggle::make(),
                    EngagementScheduledAtDateTimePicker::make(),
                ]),
        ];
    }

    protected function getDraftWithAiAction(): Action
    {
        if ($this->draftWithAiActionUsing) {
            return $this->evaluate($this->draftWithAiActionUsing);
        }

        return RelationManagerDraftWithAiAction::make()
            ->mergeTags(static::getDefaultMergeTags());
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function createEngagement(array $data, Schema $schema): void
    {
        $body = is_array($data['body'] ?? null) ? $data['body'] : ['type' => 'doc', 'content' => []];

        $recipient = $this->evaluate($this->resolveRecipientsUsing)?->first();

        if (! $recipient) {
            throw new Exception('No recipient found');
        }

        app(CreateEngagement::class)->execute(new EngagementCreationData(
            user: auth()->user(),
            recipient: $recipient,
            channel: NotificationChannel::Email,
            subject: $data['subject'],
            body: $body,
            scheduledAt: ($data['send_later'] ?? false) ? Carbon::parse($data['scheduled_at'] ?? null) : null,
            schema: $schema,
        ));
    }
}
