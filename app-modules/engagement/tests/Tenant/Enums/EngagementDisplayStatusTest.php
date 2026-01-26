<?php

use AidingApp\Engagement\Enums\EngagementDisplayStatus;
use AidingApp\Engagement\Models\Engagement;
use AidingApp\Notification\Enums\EmailMessageEventType;
use AidingApp\Notification\Models\EmailMessage;
use AidingApp\Notification\Models\EmailMessageEvent;

it('returns the correct case given a particular Engagement', function (Engagement $engagement, EngagementDisplayStatus $expectedStatus) {
    expect(EngagementDisplayStatus::getStatus($engagement))->toBe($expectedStatus);
})->with([
    'email | not scheduled' => [
        fn () => Engagement::factory()->email()->deliverNow()->create(),
        EngagementDisplayStatus::Pending,
    ],
    'email | scheduled' => [
        fn () => Engagement::factory()->email()->deliverLater()->create(),
        EngagementDisplayStatus::Scheduled,
    ],
    'email | scheduled, dispatched' => [
        fn () => Engagement::factory()
            ->has(
                EmailMessage::factory()
                    ->has(
                        EmailMessageEvent::factory()
                            ->state(['type' => EmailMessageEventType::Dispatched]),
                        'events'
                    ),
                'latestEmailMessage'
            )
            ->email()
            ->deliverLater()
            ->create(),
        EngagementDisplayStatus::Pending,
    ],
    'email | dispatched' => [
        fn () => Engagement::factory()
            ->has(
                EmailMessage::factory()
                    ->has(
                        EmailMessageEvent::factory()
                            ->state(['type' => EmailMessageEventType::Dispatched]),
                        'events'
                    ),
                'latestEmailMessage'
            )
            ->email()
            ->deliverNow()
            ->create(),
        EngagementDisplayStatus::Pending,
    ],
    'email | dispatched, send' => [
        fn () => Engagement::factory()
            ->has(
                EmailMessage::factory()
                    ->has(
                        EmailMessageEvent::factory()
                            ->count(2)
                            ->sequence(
                                [
                                    'type' => EmailMessageEventType::Dispatched,
                                ],
                                [
                                    'type' => EmailMessageEventType::Send,
                                ],
                            ),
                        'events'
                    ),
                relationship: 'emailMessages'
            )
            ->email()
            ->deliverNow()
            ->create(),
        EngagementDisplayStatus::Sent,
    ],
    'email | send, dispatched' => [
        fn () => Engagement::factory()
            ->has(
                EmailMessage::factory()
                    ->has(
                        EmailMessageEvent::factory()
                            ->count(2)
                            ->sequence(
                                [
                                    'type' => EmailMessageEventType::Send,
                                ],
                                [
                                    'type' => EmailMessageEventType::Dispatched,
                                ],
                            ),
                        'events'
                    ),
                relationship: 'emailMessages'
            )
            ->email()
            ->deliverNow()
            ->create(),
        EngagementDisplayStatus::Sent,
    ],
    'email | failedDispatch' => [
        fn () => Engagement::factory()
            ->has(
                EmailMessage::factory()
                    ->has(
                        EmailMessageEvent::factory()
                            ->state(['type' => EmailMessageEventType::FailedDispatch]),
                        'events'
                    ),
                'latestEmailMessage'
            )
            ->email()
            ->deliverNow()
            ->create(),
        EngagementDisplayStatus::SystemDelayed,
    ],
    'email | rateLimited' => [
        fn () => Engagement::factory()
            ->has(
                EmailMessage::factory()
                    ->has(
                        EmailMessageEvent::factory()
                            ->state(['type' => EmailMessageEventType::RateLimited]),
                        'events'
                    ),
                'latestEmailMessage'
            )
            ->email()
            ->deliverNow()
            ->create(),
        EngagementDisplayStatus::Failed,
    ],
    'email | blockedByDemoMode' => [
        fn () => Engagement::factory()
            ->has(
                EmailMessage::factory()
                    ->has(
                        EmailMessageEvent::factory()
                            ->state(['type' => EmailMessageEventType::BlockedByDemoMode]),
                        'events'
                    ),
                'latestEmailMessage'
            )
            ->email()
            ->deliverNow()
            ->create(),
        EngagementDisplayStatus::Delivered,
    ],
    'email | dispatched, send, bounce' => [
        fn () => Engagement::factory()
            ->has(
                EmailMessage::factory()
                    ->has(
                        EmailMessageEvent::factory()
                            ->count(3)
                            ->sequence(
                                ['type' => EmailMessageEventType::Dispatched],
                                ['type' => EmailMessageEventType::Send],
                                ['type' => EmailMessageEventType::Bounce],
                            ),
                        'events'
                    ),
                'latestEmailMessage'
            )
            ->email()
            ->deliverNow()
            ->create(),
        EngagementDisplayStatus::Bounced,
    ],
    'email | dispatched, send, delivery' => [
        fn () => Engagement::factory()
            ->has(
                EmailMessage::factory()
                    ->has(
                        EmailMessageEvent::factory()
                            ->count(3)
                            ->sequence(
                                ['type' => EmailMessageEventType::Dispatched],
                                ['type' => EmailMessageEventType::Send],
                                ['type' => EmailMessageEventType::Delivery],
                            ),
                        'events'
                    ),
                'latestEmailMessage'
            )
            ->email()
            ->deliverNow()
            ->create(),
        EngagementDisplayStatus::Delivered,
    ],
    'email | dispatched, send, delivery, complaint' => [
        fn () => Engagement::factory()
            ->has(
                EmailMessage::factory()
                    ->has(
                        EmailMessageEvent::factory()
                            ->count(4)
                            ->sequence(
                                ['type' => EmailMessageEventType::Dispatched],
                                ['type' => EmailMessageEventType::Send],
                                ['type' => EmailMessageEventType::Delivery],
                                ['type' => EmailMessageEventType::Complaint],
                            ),
                        'events'
                    ),
                'latestEmailMessage'
            )
            ->email()
            ->deliverNow()
            ->create(),
        EngagementDisplayStatus::Complaint,
    ],
    'email | dispatched, send, reject' => [
        fn () => Engagement::factory()
            ->has(
                EmailMessage::factory()
                    ->has(
                        EmailMessageEvent::factory()
                            ->count(3)
                            ->sequence(
                                ['type' => EmailMessageEventType::Dispatched],
                                ['type' => EmailMessageEventType::Send],
                                ['type' => EmailMessageEventType::Reject],
                            ),
                        'events'
                    ),
                'latestEmailMessage'
            )
            ->email()
            ->deliverNow()
            ->create(),
        EngagementDisplayStatus::Failed,
    ],
    'email | dispatched, send, delivery, open' => [
        fn () => Engagement::factory()
            ->has(
                EmailMessage::factory()
                    ->has(
                        EmailMessageEvent::factory()
                            ->count(4)
                            ->sequence(
                                ['type' => EmailMessageEventType::Dispatched],
                                ['type' => EmailMessageEventType::Send],
                                ['type' => EmailMessageEventType::Delivery],
                                ['type' => EmailMessageEventType::Open],
                            ),
                        'events'
                    ),
                'latestEmailMessage'
            )
            ->email()
            ->deliverNow()
            ->create(),
        EngagementDisplayStatus::Read,
    ],
    'email | dispatched, send, delivery, open, click' => [
        fn () => Engagement::factory()
            ->has(
                EmailMessage::factory()
                    ->has(
                        EmailMessageEvent::factory()
                            ->count(5)
                            ->sequence(
                                ['type' => EmailMessageEventType::Dispatched],
                                ['type' => EmailMessageEventType::Send],
                                ['type' => EmailMessageEventType::Delivery],
                                ['type' => EmailMessageEventType::Open],
                                ['type' => EmailMessageEventType::Click],
                            ),
                        'events'
                    ),
                'latestEmailMessage'
            )
            ->email()
            ->deliverNow()
            ->create(),
        EngagementDisplayStatus::Clicked,
    ],
    'email | dispatched, send, delivery, open, click, open' => [
        fn () => Engagement::factory()
            ->has(
                EmailMessage::factory()
                    ->has(
                        EmailMessageEvent::factory()
                            ->count(6)
                            ->sequence(
                                ['type' => EmailMessageEventType::Dispatched],
                                ['type' => EmailMessageEventType::Send],
                                ['type' => EmailMessageEventType::Delivery],
                                ['type' => EmailMessageEventType::Open],
                                ['type' => EmailMessageEventType::Click],
                                ['type' => EmailMessageEventType::Open],
                            ),
                        'events'
                    ),
                'latestEmailMessage'
            )
            ->email()
            ->deliverNow()
            ->create(),
        EngagementDisplayStatus::Clicked,
    ],
    'email | dispatched, RenderingFailure' => [
        fn () => Engagement::factory()
            ->has(
                EmailMessage::factory()
                    ->has(
                        EmailMessageEvent::factory()
                            ->count(2)
                            ->sequence(
                                ['type' => EmailMessageEventType::Dispatched],
                                ['type' => EmailMessageEventType::RenderingFailure],
                            ),
                        'events'
                    ),
                'latestEmailMessage'
            )
            ->email()
            ->deliverNow()
            ->create(),
        EngagementDisplayStatus::Failed,
    ],
    'email | dispatched, send, delivery, open, click, subscription' => [
        fn () => Engagement::factory()
            ->has(
                EmailMessage::factory()
                    ->has(
                        EmailMessageEvent::factory()
                            ->count(6)
                            ->sequence(
                                ['type' => EmailMessageEventType::Dispatched],
                                ['type' => EmailMessageEventType::Send],
                                ['type' => EmailMessageEventType::Delivery],
                                ['type' => EmailMessageEventType::Open],
                                ['type' => EmailMessageEventType::Click],
                                ['type' => EmailMessageEventType::Subscription],
                            ),
                        'events'
                    ),
                'latestEmailMessage'
            )
            ->email()
            ->deliverNow()
            ->create(),
        EngagementDisplayStatus::Unsubscribed,
    ],
    'email | dispatched, send, DeliveryDelay' => [
        fn () => Engagement::factory()
            ->has(
                EmailMessage::factory()
                    ->has(
                        EmailMessageEvent::factory()
                            ->count(3)
                            ->sequence(
                                ['type' => EmailMessageEventType::Dispatched],
                                ['type' => EmailMessageEventType::Send],
                                ['type' => EmailMessageEventType::DeliveryDelay],
                            ),
                        'events'
                    ),
                'latestEmailMessage'
            )
            ->email()
            ->deliverNow()
            ->create(),
        EngagementDisplayStatus::Delayed,
    ],
]);
