<?php

use AidingApp\Contact\Models\Contact;
use AidingApp\Notification\Tests\Fixtures\TestDatabaseNotification;
use AidingApp\Notification\Tests\Fixtures\TestDualNotification;
use AidingApp\Notification\Tests\Fixtures\TestEmailNotification;
use Illuminate\Cache\RateLimiter;
use Illuminate\Cache\RateLimiting\Unlimited;
use Illuminate\Container\Container;
use Illuminate\Notifications\SendQueuedNotifications;

it('has the notification rate limiting applied properly for email notifications', function () {
    $recipient = Contact::factory()->create();
    $notification = new TestEmailNotification();

    $job = new SendQueuedNotifications(
        $recipient, // @phpstan-ignore argument.type
        $notification,
        ['mail'],
    );

    $limiter = Container::getInstance()->make(RateLimiter::class)->limiter('notifications');

    $limits = $limiter($job);

    /** @phpstan-ignore property.notFound */
    expect($limits)
        ->toHaveCount(1)
        ->and($limits[0])
        ->key->toEqual('mail')
        ->maxAttempts->toEqual(14)
        ->decaySeconds->toEqual(1);
});

it('has the notification rate limiting applied properly for database notifications', function () {
    $recipient = Contact::factory()->create();
    $notification = new TestDatabaseNotification();

    $job = new SendQueuedNotifications(
        $recipient, // @phpstan-ignore argument.type
        $notification,
        ['database'],
    );

    $limiter = Container::getInstance()->make(RateLimiter::class)->limiter('notifications');

    $limits = $limiter($job);

    expect($limits)
        ->toHaveCount(1)
        ->and($limits[0])
        ->toBeInstanceOf(Unlimited::class);
});

it('has the notification rate limiting applied properly for notifications that send to multiple channels', function () {
    $recipient = Contact::factory()->create();
    $notification = new TestDualNotification();

    $job = new SendQueuedNotifications(
        $recipient, // @phpstan-ignore argument.type
        $notification,
        ['mail', 'database'],
    );

    $limiter = Container::getInstance()->make(RateLimiter::class)->limiter('notifications');

    $limits = $limiter($job);

    /** @phpstan-ignore property.notFound */
    expect($limits)
        ->toHaveCount(2)
        ->and($limits[0])
        ->key->toEqual('mail')
        ->maxAttempts->toEqual(14)
        ->decaySeconds->toEqual(1)
        ->and($limits[1])
        ->toBeInstanceOf(Unlimited::class);
});
