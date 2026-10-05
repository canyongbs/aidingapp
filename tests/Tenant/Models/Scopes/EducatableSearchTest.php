<?php

use AidingApp\Contact\Models\Contact;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use App\Models\Scopes\EducatableSearch;

it('matches related respondent names case insensitively', function (string $firstName, string $search) {
    $respondent = Contact::factory()->state([
        'first_name' => $firstName,
        'last_name' => 'Example',
        'full_name' => "{$firstName} Example",
    ])->create();

    $matchingRequest = ServiceRequest::factory()->for($respondent, 'respondent')->create();

    $otherRespondent = Contact::factory()->state([
        'first_name' => 'Other',
        'last_name' => 'Person',
        'full_name' => 'Other Person',
    ])->create();

    ServiceRequest::factory()->for($otherRespondent, 'respondent')->create();

    $results = ServiceRequest::query()
        ->tap(new EducatableSearch(relationship: 'respondent', search: $search))
        ->get();

    expect($results->modelKeys())->toBe([$matchingRequest->getKey()]);
})->with([
    'ASCII uppercase partial match' => ['Alice', 'LIC'],
    'multibyte uppercase partial match' => ["Ren\u{00E9}e", "N\u{00C9}E"],
]);
