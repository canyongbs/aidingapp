<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

    Aiding App™ is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Aiding App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    <https://www.canyongbs.com> or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

use AidingApp\Contact\Database\Seeders\ContactStatusSeeder;
use AidingApp\Contact\Models\Contact;
use AidingApp\Contact\Models\Organization;
use AidingApp\Engagement\Enums\EngagementResponseType;
use AidingApp\Engagement\Exceptions\SesS3InboundServiceRequestTypeNotFound;
use AidingApp\Engagement\Exceptions\SesS3InboundSpamOrVirusDetected;
use AidingApp\Engagement\Exceptions\UnableToRetrieveContentFromSesS3EmailPayload;
use AidingApp\Engagement\Jobs\ProcessSesS3InboundEmail;
use AidingApp\Engagement\Models\EngagementResponse;
use AidingApp\Engagement\Models\UnmatchedInboundCommunication;
use AidingApp\Engagement\Notifications\IneligibleContactSesS3InboundEmailServiceRequestNotification;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestPriority;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use AidingApp\ServiceManagement\Models\TenantServiceRequestTypeDomain;
use App\Actions\Paths\ModulePath;
use App\Models\Tenant;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\UploadedFile;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Mockery\MockInterface;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseEmpty;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\partialMock;
use function Pest\Laravel\seed;

use Spatie\Multitenancy\Events\MadeTenantCurrentEvent;

it('handles spam verdict failure properly', function () {
    Storage::fake('s3');
    Storage::fake('s3-inbound-email');

    $modulePath = resolve(ModulePath::class);

    $content = file_get_contents($modulePath('engagement', 'tests/Landlord/Fixtures/s3_email_spam'));

    $file = UploadedFile::fake()->createWithContent('s3_email_spam', $content);

    Storage::disk('s3-inbound-email')->putFileAs('', $file, 's3_email_spam');

    /** @var ProcessSesS3InboundEmail $mock */
    $mock = partialMock(ProcessSesS3InboundEmail::class, function (MockInterface $mock) use ($content) {
        $mock
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('getContent')
            ->once()
            ->andReturn($content);

        $mock
            ->shouldReceive('fail')
            ->once()
            ->withArgs(function (Throwable $e) {
                $invadedException = invade($e);

                return $e instanceof SesS3InboundSpamOrVirusDetected
                    && $invadedException->spamVerdict === 'FAIL'
                    && $invadedException->virusVerdict === 'PASS';
            });
    });

    invade($mock)->emailFilePath = 's3_email_spam';

    $mock->handle();

    assertDatabaseCount(Contact::class, 0);
    assertDatabaseCount(EngagementResponse::class, 0);
});

it('handles virus verdict failure properly', function () {
    Storage::fake('s3');
    Storage::fake('s3-inbound-email');

    $modulePath = resolve(ModulePath::class);

    $content = file_get_contents($modulePath('engagement', 'tests/Landlord/Fixtures/s3_email_virus'));

    $file = UploadedFile::fake()->createWithContent('s3_email_virus', $content);

    Storage::disk('s3-inbound-email')->putFileAs('', $file, 's3_email_virus');

    /** @var ProcessSesS3InboundEmail $mock */
    $mock = partialMock(ProcessSesS3InboundEmail::class, function (MockInterface $mock) use ($content) {
        $mock
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('getContent')
            ->once()
            ->andReturn($content);

        $mock
            ->shouldReceive('fail')
            ->once()
            ->withArgs(function (Throwable $e) {
                $invadedException = invade($e);

                return $e instanceof SesS3InboundSpamOrVirusDetected
                    && $invadedException->spamVerdict === 'PASS'
                    && $invadedException->virusVerdict === 'FAIL';
            });
    });

    invade($mock)->emailFilePath = 's3_email_virus';

    $mock->handle();

    assertDatabaseCount(Contact::class, 0);
    assertDatabaseCount(EngagementResponse::class, 0);
});

it('properly handles not finding a Contact match for emails that should be an EngagementResponse', function () {
    Storage::fake('s3');
    $filesystem = Storage::fake('s3-inbound-email');

    assert($filesystem instanceof FilesystemAdapter);

    $modulePath = resolve(ModulePath::class);

    $content = file_get_contents($modulePath('engagement', 'tests/Landlord/Fixtures/s3_email'));

    $file = UploadedFile::fake()->createWithContent('s3_email', $content);

    $filesystem->putFileAs('', $file, 's3_email');

    /** @var ProcessSesS3InboundEmail $mock */
    $mock = partialMock(ProcessSesS3InboundEmail::class, function (MockInterface $mock) use ($content) {
        $mock
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('getContent')
            ->once()
            ->andReturn($content);
    });

    invade($mock)->emailFilePath = 's3_email';

    $mock->handle();

    assertDatabaseCount(Contact::class, 0);
    assertDatabaseCount(EngagementResponse::class, 0);
    assertDatabaseCount(UnmatchedInboundCommunication::class, 1);
    assertDatabaseHas(UnmatchedInboundCommunication::class, [
        'subject' => 'This is a test',
        'sender' => 'kevin.ullyott@canyongbs.com',
        'type' => EngagementResponseType::Email->value,
    ]);
    $filesystem->assertMissing('s3_email');
});

it('properly creates an EngagementResponse for an inbound email', function () {
    $fakeStorage = Storage::fake('s3');
    $filesystem = Storage::fake('s3-inbound-email');

    Event::listen(
        MadeTenantCurrentEvent::class,
        function (MadeTenantCurrentEvent $event) use ($fakeStorage) {
            Storage::set('s3', $fakeStorage);
        }
    );

    assert($filesystem instanceof FilesystemAdapter);

    $tenant = Tenant::query()->first();

    assert($tenant instanceof Tenant);

    $contact = null;

    $tenant->execute(function () use (&$contact) {
        $contact = Contact::factory()->create([
            'email' => 'kevin.ullyott@canyongbs.com',
        ]);
    });

    assert($contact instanceof Contact);

    $modulePath = resolve(ModulePath::class);

    $content = file_get_contents($modulePath('engagement', 'tests/Landlord/Fixtures/s3_email'));

    $file = UploadedFile::fake()->createWithContent('s3_email', $content);

    $filesystem->putFileAs('', $file, 's3_email');

    /** @var ProcessSesS3InboundEmail $mock */
    $mock = partialMock(ProcessSesS3InboundEmail::class, function (MockInterface $mock) use ($content) {
        $mock
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('getContent')
            ->once()
            ->andReturn($content);
    });

    invade($mock)->emailFilePath = 's3_email';

    $filesystem->assertExists('s3_email');

    $mock->handle();

    $tenant->execute(function () use ($contact, $modulePath) {
        assertDatabaseHas(EngagementResponse::class, [
            'subject' => 'This is a test',
            'sender_id' => $contact->getKey(),
            'sender_type' => $contact->getMorphClass(),
            'type' => EngagementResponseType::Email->value,
            'raw' => file_get_contents($modulePath('engagement', 'tests/Landlord/Fixtures/s3_email')),
        ]);
    });

    $filesystem->assertMissing('s3_email');
});

it('handles attachments properly', function () {
    $fakeStorage = Storage::fake('s3');
    $filesystem = Storage::fake('s3-inbound-email');

    Event::listen(
        MadeTenantCurrentEvent::class,
        function (MadeTenantCurrentEvent $event) use ($fakeStorage) {
            Storage::set('s3', $fakeStorage);
        }
    );

    assert($filesystem instanceof FilesystemAdapter);

    $tenant = Tenant::query()->first();

    assert($tenant instanceof Tenant);

    $contact = null;

    $tenant->execute(function () use (&$contact) {
        $contact = Contact::factory()->create([
            'email' => 'kevin.ullyott@canyongbs.com',
        ]);
    });

    assert($contact instanceof Contact);

    $modulePath = resolve(ModulePath::class);

    $content = file_get_contents($modulePath('engagement', 'tests/Landlord/Fixtures/s3_email_with_attachments'));

    $file = UploadedFile::fake()->createWithContent('s3_email', $content);

    $filesystem->putFileAs('', $file, 's3_email');

    /** @var ProcessSesS3InboundEmail $mock */
    $mock = partialMock(ProcessSesS3InboundEmail::class, function (MockInterface $mock) use ($content) {
        $mock
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('getContent')
            ->once()
            ->andReturn($content);
    });

    invade($mock)->emailFilePath = 's3_email';

    $filesystem->assertExists('s3_email');

    $mock->handle();

    $tenant->execute(function () use ($contact, $modulePath) {
        $engagementResponses = EngagementResponse::all();

        expect($engagementResponses)->toHaveCount(1);

        $engagementResponse = $engagementResponses->first();

        assert($engagementResponse instanceof EngagementResponse);

        expect($engagementResponse->getMedia('attachments'))->toHaveCount(3)
            ->and($engagementResponse->subject)->toBe('This is a test')
            ->and($engagementResponse->sender_id)->toBe($contact->getKey())
            ->and($engagementResponse->sender_type)->toBe($contact->getMorphClass())
            ->and($engagementResponse->type)->toBe(EngagementResponseType::Email)
            ->and($engagementResponse->raw)->toBe(file_get_contents($modulePath('engagement', 'tests/Landlord/Fixtures/s3_email_with_attachments')));
    });

    $filesystem->assertMissing('s3_email');
});

it('handles exceptions correctly in the failed method', function (?Exception $exception, string $expectedPath) {
    Storage::fake('s3');
    $filesystem = Storage::fake('s3-inbound-email');

    assert($filesystem instanceof FilesystemAdapter);

    $modulePath = resolve(ModulePath::class);

    $content = file_get_contents($modulePath('engagement', 'tests/Landlord/Fixtures/s3_email'));

    $file = UploadedFile::fake()->createWithContent('s3_email', $content);

    $filesystem->putFileAs('', $file, 's3_email');

    $filesystem->assertExists('s3_email');

    $job = new ProcessSesS3InboundEmail('s3_email');

    $job->failed($exception);

    $filesystem->assertExists("{$expectedPath}/s3_email");
})->with([
    'null exception' => [null, '/failed'],
    'general exception' => [new Exception('General error'), '/failed'],
    'spam exception' => [new SesS3InboundSpamOrVirusDetected('s3_email', 'FAIL', 'PASS'), '/spam-or-virus-detected'],
    'virus exception' => [new SesS3InboundSpamOrVirusDetected('s3_email', 'PASS', 'FAIL'), '/spam-or-virus-detected'],
    'unable to retrieve content exception' => [new UnableToRetrieveContentFromSesS3EmailPayload('s3_email'), '/failed'],
    'unable to find service request type exception' => [fn () => new SesS3InboundServiceRequestTypeNotFound('s3_email', TenantServiceRequestTypeDomain::factory()->createQuietly(['tenant_id' => Tenant::query()->firstOrFail()])), '/failed'],
]);

it('properly handles being unable to find the ServiceRequestType', function () {
    $tenant = Tenant::query()->firstOrFail();

    assert($tenant instanceof Tenant);

    [$contact, $serviceRequestTypeDomain] = $tenant->execute(function () use ($tenant) {
        $contact = Contact::factory()->create([
            'email' => 'kevin.ullyott@canyongbs.com',
        ]);

        $serviceRequestTypeDomain = TenantServiceRequestTypeDomain::factory()->create([
            'tenant_id' => $tenant->getKey(),
            'domain' => 'help',
            'service_request_type_id' => Str::orderedUuid(),
        ]);

        return [$contact, $serviceRequestTypeDomain];
    });

    Storage::fake('s3');
    $filesystem = Storage::fake('s3-inbound-email');

    assert($filesystem instanceof FilesystemAdapter);

    $modulePath = resolve(ModulePath::class);

    $content = file_get_contents($modulePath('engagement', 'tests/Landlord/Fixtures/s3_email_for_service_request'));

    $file = UploadedFile::fake()->createWithContent('s3_email', $content);

    $filesystem->putFileAs('', $file, 's3_email');

    /** @var ProcessSesS3InboundEmail $mock */
    $mock = partialMock(ProcessSesS3InboundEmail::class, function (MockInterface $mock) use ($content, $serviceRequestTypeDomain) {
        $mock
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('getContent')
            ->once()
            ->andReturn($content);

        $mock
            ->shouldReceive('fail')
            ->once()
            ->withArgs(function (Throwable $e) use ($serviceRequestTypeDomain) {
                $invadedException = invade($e);

                return $e instanceof SesS3InboundServiceRequestTypeNotFound
                    && $invadedException->file === 's3_email'
                    && $invadedException->serviceRequestTypeDomain->is($serviceRequestTypeDomain);
            });
    });

    invade($mock)->emailFilePath = 's3_email';

    $filesystem->assertExists('s3_email');

    $mock->handle();

    $tenant->makeCurrent();

    assertDatabaseEmpty(EngagementResponse::class);

    assertDatabaseEmpty(ServiceRequest::class);
});

it('handles is_email_automatic_creation_enabled being disabled properly', function () {
    $tenant = Tenant::query()->firstOrFail();

    assert($tenant instanceof Tenant);

    $tenant->execute(function () {
        Contact::factory()->create([
            'email' => 'kevin.ullyott@canyongbs.com',
        ]);

        ServiceRequestType::factory()
            ->has(
                TenantServiceRequestTypeDomain::factory()->state([
                    'domain' => 'help',
                ]),
                'domain'
            )
            ->has(
                ServiceRequestPriority::factory()->count(3),
                'priorities'
            )
            ->create([
                'is_email_automatic_creation_enabled' => false,
            ]);
    });

    Storage::fake('s3');
    $filesystem = Storage::fake('s3-inbound-email');

    assert($filesystem instanceof FilesystemAdapter);

    $modulePath = resolve(ModulePath::class);

    $content = file_get_contents($modulePath('engagement', 'tests/Landlord/Fixtures/s3_email_for_service_request'));

    $file = UploadedFile::fake()->createWithContent('s3_email', $content);

    $filesystem->putFileAs('', $file, 's3_email');

    /** @var ProcessSesS3InboundEmail $mock */
    $mock = partialMock(ProcessSesS3InboundEmail::class, function (MockInterface $mock) use ($content) {
        $mock
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('getContent')
            ->once()
            ->andReturn($content);
    });

    invade($mock)->emailFilePath = 's3_email';

    $filesystem->assertExists('s3_email');

    $mock->handle();

    $tenant->makeCurrent();

    assertDatabaseEmpty(EngagementResponse::class);

    assertDatabaseEmpty(ServiceRequest::class);

    $filesystem->assertMissing('s3_email');
});

it('sends ineligible email when no contact is found for a service request and we cannot match the email to an organization', function () {
    $notificationFake = Notification::fake();

    Event::listen(
        MadeTenantCurrentEvent::class,
        function (MadeTenantCurrentEvent $event) use ($notificationFake) {
            Notification::swap($notificationFake);
        }
    );

    $tenant = Tenant::query()->firstOrFail();

    assert($tenant instanceof Tenant);

    $tenant->execute(function () {
        $serviceRequestType = ServiceRequestType::factory()
            ->has(
                TenantServiceRequestTypeDomain::factory()->state([
                    'domain' => 'help',
                ]),
                'domain'
            )
            ->has(
                ServiceRequestPriority::factory()->count(3),
                'priorities'
            )
            ->create([
                'is_email_automatic_creation_enabled' => true,
                'is_email_automatic_creation_contact_create_enabled' => true,
            ]);

        $assignedPriority = $serviceRequestType->priorities->first();

        $serviceRequestType->update([
            'email_automatic_creation_priority_id' => $assignedPriority->getKey(),
        ]);
    });

    Storage::fake('s3');
    $filesystem = Storage::fake('s3-inbound-email');

    assert($filesystem instanceof FilesystemAdapter);

    $modulePath = resolve(ModulePath::class);

    $content = file_get_contents($modulePath('engagement', 'tests/Landlord/Fixtures/s3_email_for_service_request'));

    $file = UploadedFile::fake()->createWithContent('s3_email', $content);

    $filesystem->putFileAs('', $file, 's3_email');

    /** @var ProcessSesS3InboundEmail $mock */
    $mock = partialMock(ProcessSesS3InboundEmail::class, function (MockInterface $mock) use ($content) {
        $mock
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('getContent')
            ->once()
            ->andReturn($content);
    });

    invade($mock)->emailFilePath = 's3_email';

    $filesystem->assertExists('s3_email');

    assertDatabaseEmpty(Contact::class);

    $mock->handle();

    $tenant->makeCurrent();

    assertDatabaseEmpty(EngagementResponse::class);

    assertDatabaseEmpty(ServiceRequest::class);

    assertDatabaseEmpty(Contact::class);

    $filesystem->assertMissing('s3_email');

    $notificationFake->assertSentOnDemand(
        IneligibleContactSesS3InboundEmailServiceRequestNotification::class,
        function (IneligibleContactSesS3InboundEmailServiceRequestNotification $notification, array $channels, object $notifiable) {
            return $notifiable->routes['mail'] === 'kevin.ullyott@canyongbs.com';
        }
    );
});

it('sends ineligible email when no contact is found for a service request and is_email_automatic_creation_contact_create_enabled is disabled', function () {
    $notificationFake = Notification::fake();

    Event::listen(
        MadeTenantCurrentEvent::class,
        function (MadeTenantCurrentEvent $event) use ($notificationFake) {
            Notification::swap($notificationFake);
        }
    );

    $tenant = Tenant::query()->firstOrFail();

    assert($tenant instanceof Tenant);

    $tenant->execute(function () {
        Organization::factory()->create([
            'domains' => ['canyongbs.com'],
        ]);

        $serviceRequestType = ServiceRequestType::factory()
            ->has(
                TenantServiceRequestTypeDomain::factory()->state([
                    'domain' => 'help',
                ]),
                'domain'
            )
            ->has(
                ServiceRequestPriority::factory()->count(3),
                'priorities'
            )
            ->create([
                'is_email_automatic_creation_enabled' => true,
                'is_email_automatic_creation_contact_create_enabled' => false,
            ]);

        $assignedPriority = $serviceRequestType->priorities->first();

        $serviceRequestType->update([
            'email_automatic_creation_priority_id' => $assignedPriority->getKey(),
        ]);
    });

    Storage::fake('s3');
    $filesystem = Storage::fake('s3-inbound-email');

    assert($filesystem instanceof FilesystemAdapter);

    $modulePath = resolve(ModulePath::class);

    $content = file_get_contents($modulePath('engagement', 'tests/Landlord/Fixtures/s3_email_for_service_request'));

    $file = UploadedFile::fake()->createWithContent('s3_email', $content);

    $filesystem->putFileAs('', $file, 's3_email');

    /** @var ProcessSesS3InboundEmail $mock */
    $mock = partialMock(ProcessSesS3InboundEmail::class, function (MockInterface $mock) use ($content) {
        $mock
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('getContent')
            ->once()
            ->andReturn($content);
    });

    invade($mock)->emailFilePath = 's3_email';

    $filesystem->assertExists('s3_email');

    assertDatabaseEmpty(Contact::class);

    $mock->handle();

    $tenant->makeCurrent();

    assertDatabaseEmpty(EngagementResponse::class);

    assertDatabaseEmpty(ServiceRequest::class);

    assertDatabaseEmpty(Contact::class);

    $filesystem->assertMissing('s3_email');

    $notificationFake->assertSentOnDemand(
        IneligibleContactSesS3InboundEmailServiceRequestNotification::class,
        function (IneligibleContactSesS3InboundEmailServiceRequestNotification $notification, array $channels, object $notifiable) {
            return $notifiable->routes['mail'] === 'kevin.ullyott@canyongbs.com';
        }
    );
});

it('ineligible email includes proper bcc to provided email when set', function () {
    Event::fake([MessageSending::class]);

    $tenant = Tenant::query()->firstOrFail();

    assert($tenant instanceof Tenant);

    $bccEmail = fake()->safeEmail();

    $tenant->execute(function () use ($bccEmail) {
        Organization::factory()->create([
            'domains' => ['canyongbs.com'],
        ]);

        $serviceRequestType = ServiceRequestType::factory()
            ->has(
                TenantServiceRequestTypeDomain::factory()->state([
                    'domain' => 'help',
                ]),
                'domain'
            )
            ->has(
                ServiceRequestPriority::factory()->count(3),
                'priorities'
            )
            ->create([
                'is_email_automatic_creation_enabled' => true,
                'is_email_automatic_creation_contact_create_enabled' => false,
                'email_automatic_creation_bcc' => $bccEmail,
            ]);

        $assignedPriority = $serviceRequestType->priorities->first();

        $serviceRequestType->update([
            'email_automatic_creation_priority_id' => $assignedPriority->getKey(),
        ]);
    });

    Storage::fake('s3');
    $filesystem = Storage::fake('s3-inbound-email');

    assert($filesystem instanceof FilesystemAdapter);

    $modulePath = resolve(ModulePath::class);

    $content = file_get_contents($modulePath('engagement', 'tests/Landlord/Fixtures/s3_email_for_service_request'));

    $file = UploadedFile::fake()->createWithContent('s3_email', $content);

    $filesystem->putFileAs('', $file, 's3_email');

    /** @var ProcessSesS3InboundEmail $mock */
    $mock = partialMock(ProcessSesS3InboundEmail::class, function (MockInterface $mock) use ($content) {
        $mock
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('getContent')
            ->once()
            ->andReturn($content);
    });

    invade($mock)->emailFilePath = 's3_email';

    $filesystem->assertExists('s3_email');

    assertDatabaseEmpty(Contact::class);

    $mock->handle();

    $tenant->makeCurrent();

    assertDatabaseEmpty(EngagementResponse::class);

    assertDatabaseEmpty(ServiceRequest::class);

    assertDatabaseEmpty(Contact::class);

    $filesystem->assertMissing('s3_email');

    Event::assertDispatched(MessageSending::class, function (MessageSending $event) use ($bccEmail) {
        $bcc = collect($event->message->getBcc())
            ->map(fn ($address) => is_array($address) ? $address[0] : $address);

        expect($bcc)->toHaveCount(1)
            ->and($bcc[0]->getAddress())->toBe($bccEmail);

        return true;
    });
});

it('creates a new contact for a service request when is_email_automatic_creation_contact_create_enabled is enabled', function () {
    $tenant = Tenant::query()->firstOrFail();

    assert($tenant instanceof Tenant);

    [$organization, $serviceRequestType, $assignedPriority] = $tenant->execute(function () {
        seed(ContactStatusSeeder::class);

        $organization = Organization::factory()->create([
            'domains' => ['canyongbs.com'],
        ]);

        $serviceRequestType = ServiceRequestType::factory()
            ->has(
                TenantServiceRequestTypeDomain::factory()->state([
                    'domain' => 'help',
                ]),
                'domain'
            )
            ->has(
                ServiceRequestPriority::factory()->count(3),
                'priorities'
            )
            ->create([
                'is_email_automatic_creation_enabled' => true,
                'is_email_automatic_creation_contact_create_enabled' => true,
            ]);

        $assignedPriority = $serviceRequestType->priorities->first();

        $serviceRequestType->update([
            'email_automatic_creation_priority_id' => $assignedPriority->getKey(),
        ]);

        return [$organization, $serviceRequestType, $assignedPriority];
    });

    Storage::fake('s3');
    $filesystem = Storage::fake('s3-inbound-email');

    assert($filesystem instanceof FilesystemAdapter);

    $modulePath = resolve(ModulePath::class);

    $content = file_get_contents($modulePath('engagement', 'tests/Landlord/Fixtures/s3_email_for_service_request'));

    $file = UploadedFile::fake()->createWithContent('s3_email', $content);

    $filesystem->putFileAs('', $file, 's3_email');

    /** @var ProcessSesS3InboundEmail $mock */
    $mock = partialMock(ProcessSesS3InboundEmail::class, function (MockInterface $mock) use ($content) {
        $mock
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('getContent')
            ->once()
            ->andReturn($content);
    });

    invade($mock)->emailFilePath = 's3_email';

    $filesystem->assertExists('s3_email');

    assertDatabaseEmpty(Contact::class);

    $mock->handle();

    $tenant->makeCurrent();

    assertDatabaseEmpty(EngagementResponse::class);

    $serviceRequests = ServiceRequest::all();

    expect($serviceRequests)->toHaveCount(1);

    $serviceRequest = $serviceRequests->first();

    assert($serviceRequest instanceof ServiceRequest);

    $respondent = $serviceRequest->respondent;

    expect($serviceRequest->title)->toBe('This is a test')
        ->and($serviceRequest->close_details)->toContain('Hello there! This should be put in S3!')
        ->and($serviceRequest->priority->is($assignedPriority))->toBeTrue()
        ->and($serviceRequest->priority->type->is($serviceRequestType))->toBeTrue()
        ->and($respondent->email)->toBe('kevin.ullyott@canyongbs.com')
        ->and($respondent->first_name)->toBe('Kevin')
        ->and($respondent->last_name)->toBe('Ullyott')
        ->and($respondent->full_name)->toBe('Kevin Ullyott')
        ->and($respondent->organization->is($organization))->toBeTrue();

    $filesystem->assertMissing('s3_email');
});

it('properly creates a service request for a matching contact', function () {
    $tenant = Tenant::query()->firstOrFail();

    assert($tenant instanceof Tenant);

    [$contact, $serviceRequestType, $assignedPriority] = $tenant->execute(function () {
        $contact = Contact::factory()->create([
            'email' => 'kevin.ullyott@canyongbs.com',
        ]);

        $serviceRequestType = ServiceRequestType::factory()
            ->has(
                TenantServiceRequestTypeDomain::factory()->state([
                    'domain' => 'help',
                ]),
                'domain'
            )
            ->has(
                ServiceRequestPriority::factory()->count(3),
                'priorities'
            )
            ->create([
                'is_email_automatic_creation_enabled' => true,
                'is_email_automatic_creation_contact_create_enabled' => false,
            ]);

        $assignedPriority = $serviceRequestType->priorities->first();

        $serviceRequestType->update([
            'email_automatic_creation_priority_id' => $assignedPriority->getKey(),
        ]);

        return [$contact, $serviceRequestType, $assignedPriority];
    });

    Storage::fake('s3');
    $filesystem = Storage::fake('s3-inbound-email');

    assert($filesystem instanceof FilesystemAdapter);

    $modulePath = resolve(ModulePath::class);

    $content = file_get_contents($modulePath('engagement', 'tests/Landlord/Fixtures/s3_email_for_service_request'));

    $file = UploadedFile::fake()->createWithContent('s3_email', $content);

    $filesystem->putFileAs('', $file, 's3_email');

    /** @var ProcessSesS3InboundEmail $mock */
    $mock = partialMock(ProcessSesS3InboundEmail::class, function (MockInterface $mock) use ($content) {
        $mock
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('getContent')
            ->once()
            ->andReturn($content);
    });

    invade($mock)->emailFilePath = 's3_email';

    $filesystem->assertExists('s3_email');

    $mock->handle();

    $tenant->makeCurrent();

    assertDatabaseEmpty(EngagementResponse::class);

    $serviceRequests = ServiceRequest::all();

    expect($serviceRequests)->toHaveCount(1);

    $serviceRequest = $serviceRequests->first();

    assert($serviceRequest instanceof ServiceRequest);

    expect($serviceRequest->title)->toBe('This is a test')
        ->and($serviceRequest->close_details)->toContain('Hello there! This should be put in S3!')
        ->and($serviceRequest->respondent->is($contact))->toBeTrue()
        ->and($serviceRequest->priority->is($assignedPriority))->toBeTrue()
        ->and($serviceRequest->priority->type->is($serviceRequestType))->toBeTrue();

    $filesystem->assertMissing('s3_email');
});

it('properly creates a service request for emails with attachments', function () {
    $tenant = Tenant::query()->firstOrFail();

    assert($tenant instanceof Tenant);

    [$contact, $serviceRequestType, $assignedPriority] = $tenant->execute(function () {
        $contact = Contact::factory()->create([
            'email' => 'kevin.ullyott@canyongbs.com',
        ]);

        $serviceRequestType = ServiceRequestType::factory()
            ->has(
                TenantServiceRequestTypeDomain::factory()->state([
                    'domain' => 'help',
                ]),
                'domain'
            )
            ->has(
                ServiceRequestPriority::factory()->count(3),
                'priorities'
            )
            ->create([
                'is_email_automatic_creation_enabled' => true,
                'is_email_automatic_creation_contact_create_enabled' => false,
            ]);

        $assignedPriority = $serviceRequestType->priorities->first();

        $serviceRequestType->update([
            'email_automatic_creation_priority_id' => $assignedPriority->getKey(),
        ]);

        return [$contact, $serviceRequestType, $assignedPriority];
    });

    $fakeStorage = Storage::fake('s3');
    $filesystem = Storage::fake('s3-inbound-email');

    Event::listen(
        MadeTenantCurrentEvent::class,
        function (MadeTenantCurrentEvent $event) use ($fakeStorage) {
            Storage::set('s3', $fakeStorage);
        }
    );

    assert($filesystem instanceof FilesystemAdapter);

    $modulePath = resolve(ModulePath::class);

    $content = file_get_contents($modulePath('engagement', 'tests/Landlord/Fixtures/s3_email_for_service_request_with_attachments'));

    $file = UploadedFile::fake()->createWithContent('s3_email', $content);

    $filesystem->putFileAs('', $file, 's3_email');

    /** @var ProcessSesS3InboundEmail $mock */
    $mock = partialMock(ProcessSesS3InboundEmail::class, function (MockInterface $mock) use ($content) {
        $mock
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('getContent')
            ->once()
            ->andReturn($content);
    });

    invade($mock)->emailFilePath = 's3_email';

    $filesystem->assertExists('s3_email');

    $mock->handle();

    $tenant->makeCurrent();

    assertDatabaseEmpty(EngagementResponse::class);

    $serviceRequests = ServiceRequest::all();

    expect($serviceRequests)->toHaveCount(1);

    $serviceRequest = $serviceRequests->first();

    assert($serviceRequest instanceof ServiceRequest);

    expect($serviceRequest->title)->toBe('This is a test')
        ->and($serviceRequest->close_details)->toContain('This should show up in S3 with attachments.')
        ->and($serviceRequest->respondent->is($contact))->toBeTrue()
        ->and($serviceRequest->priority->is($assignedPriority))->toBeTrue()
        ->and($serviceRequest->priority->type->is($serviceRequestType))->toBeTrue()
        ->and($serviceRequest->getMedia('uploads'))->toHaveCount(2);

    $filesystem->assertMissing('s3_email');
});
