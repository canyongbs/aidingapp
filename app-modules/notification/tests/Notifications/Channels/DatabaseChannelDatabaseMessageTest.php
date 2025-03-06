<?php

use AidingApp\Notification\Models\DatabaseMessage;
use AidingApp\Notification\Tests\Fixtures\TestDatabaseNotification;
use App\Models\User;

it('will create an EmailMessage for the notification', function () {
    $notifiable = User::factory()->create();

    $notification = new TestDatabaseNotification();

    $notifiable->notify($notification);

    $databaseMessages = DatabaseMessage::all();

    expect($databaseMessages->count())->toBe(1);
    expect($databaseMessages->first()->notification_class)->toBe(TestDatabaseNotification::class);
});
