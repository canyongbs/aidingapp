<?php

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
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestPriority;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use AidingApp\ServiceManagement\Models\TenantServiceRequestTypeDomain;
use App\Actions\Paths\ModulePath;
use App\Models\Tenant;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Mockery\MockInterface;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseEmpty;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\partialMock;
use function Pest\Laravel\seed;

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
    Storage::fake('s3');
    $filesystem = Storage::fake('s3-inbound-email');

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

it('handles attachements properly', function () {})->todo('Test attachments handling in ProcessSesS3InboundEmail');

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

it('sends ineligible email when no contact is found for a service request and we cannot match the email to an organization')->todo();

it('sends ineligible email when no contact is found for a service request and is_email_automatic_creation_contact_create_enabled is disabled')->todo();

it('ineligible email is sent bcc to provided email when set')->todo();

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

    $contact = $serviceRequest->respondent;

    expect($serviceRequest->title)->toBe('This is a test')
        ->and($serviceRequest->close_details)->toContain('Hello there! This should be put in S3!')
        ->and($serviceRequest->respondent->is($contact))->toBeTrue()
        ->and($serviceRequest->priority->is($assignedPriority))->toBeTrue()
        ->and($serviceRequest->priority->type->is($serviceRequestType))->toBeTrue()
        ->and($contact->email)->toBe('kevin.ullyott@canyongbs.com')
        ->and($contact->first_name)->toBe('Kevin')
        ->and($contact->last_name)->toBe('Ullyott')
        ->and($contact->full_name)->toBe('Kevin Ullyott')
        ->and($contact->organization->is($organization))->toBeTrue();

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
