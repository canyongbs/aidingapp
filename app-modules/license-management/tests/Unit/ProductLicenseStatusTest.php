<?php

use AidingApp\LicenseManagement\Models\ProductLicense;

it('returns Pending when the current date is before the start date', function () {
    $productLicense = ProductLicense::factory()->pending()->create();

    expect($productLicense->status)->toBe('Pending');
});

it('returns Active when the current date is between start and expiration dates', function () {
    $productLicense = ProductLicense::factory()->active()->create();

    expect($productLicense->status)->toBe('Active');
});

it('returns Expired when the current date is after the expiration date', function () {
    $productLicense = ProductLicense::factory()->expired()->create();

    expect($productLicense->status)->toBe('Expired');
});

it('returns Active when there is no expiration date and the current date is after the start date', function () {
    $productLicense = ProductLicense::factory()->noExpiration()->create();

    expect($productLicense->status)->toBe('Active');
});
