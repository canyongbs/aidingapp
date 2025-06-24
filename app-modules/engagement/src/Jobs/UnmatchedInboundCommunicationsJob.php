<?php

namespace AidingApp\Engagement\Jobs;

use AidingApp\Contact\Models\Contact;
use AidingApp\Engagement\Enums\EngagementResponseType;
use AidingApp\Engagement\Models\UnmatchedInboundCommunication;
use App\Features\UnMatchInboundCommunicationFeature;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Query\Expression;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Str;

class UnmatchedInboundCommunicationsJob implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        if (! UnMatchInboundCommunicationFeature::active()) {
            return;
        }
        UnmatchedInboundCommunication::query()
            ->chunkById(100, function ($communications) {
                foreach ($communications as $communication) {
                    match ($communication->type) {
                        EngagementResponseType::Email => $this->processEmail($communication),
                    };
                }
            });
    }

    protected function processEmail(UnmatchedInboundCommunication $communication): void
    {
        $contacts = Contact::query()
            ->where(new Expression('lower(email)'), Str::lower($communication->sender))
            ->get();

        if ($contacts->isNotEmpty()) {
            $contacts->each(function (Contact $contact) use ($communication) {
                $contact->engagementResponses()
                    ->create([
                        'subject' => $communication->subject,
                        'content' => $communication->body,
                        'sent_at' => $communication->occurred_at,
                        'type' => EngagementResponseType::Email,
                    ]);
            });
            $communication->delete();

            return;
        }
    }
}
