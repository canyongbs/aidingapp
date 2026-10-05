<?php

use AidingApp\Contact\Models\Contact;
use AidingApp\ServiceManagement\Enums\SystemServiceRequestClassification;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestStatus;
use App\Filament\Widgets\ListServiceRequestTableWidgets;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

it('searches respondent names case insensitively', function (string $firstName, string $search) {
    actingAs(User::factory()->state(['timezone' => 'UTC'])->create());

    $status = ServiceRequestStatus::factory()->state([
        'classification' => SystemServiceRequestClassification::Open,
    ])->create();

    $respondent = Contact::factory()->state([
        'first_name' => $firstName,
        'last_name' => 'Example',
        'full_name' => "{$firstName} Example",
    ])->create();

    $matchingRequest = ServiceRequest::factory()
        ->for($respondent, 'respondent')
        ->for($status, 'status')
        ->state(['title' => 'Matching request'])
        ->create();

    $otherRespondent = Contact::factory()->state([
        'first_name' => 'Other',
        'last_name' => 'Person',
        'full_name' => 'Other Person',
    ])->create();

    $otherRequest = ServiceRequest::factory()
        ->for($otherRespondent, 'respondent')
        ->for($status, 'status')
        ->state(['title' => 'Unrelated request'])
        ->create();

    livewire(ListServiceRequestTableWidgets::class)
        ->assertCanSeeTableRecords(collect([$matchingRequest, $otherRequest]))
        ->searchTable($search)
        ->assertCanSeeTableRecords(collect([$matchingRequest]))
        ->assertCanNotSeeTableRecords(collect([$otherRequest]));
})->with([
    'ASCII uppercase partial match' => ['Alice', 'LIC'],
    'multibyte uppercase partial match' => ["Ren\u{00E9}e", "N\u{00C9}E"],
]);
