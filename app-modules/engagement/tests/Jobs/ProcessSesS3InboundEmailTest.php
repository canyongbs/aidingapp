<?php

use AidingApp\Contact\Models\Contact;
use AidingApp\Engagement\Enums\EngagementResponseType;
use AidingApp\Engagement\Exceptions\SesS3InboundSpamOrVirusDetected;
use AidingApp\Engagement\Exceptions\UnableToRetrieveContentFromSesS3EmailPayload;
use AidingApp\Engagement\Jobs\ProcessSesS3InboundEmail;
use AidingApp\Engagement\Models\EngagementResponse;
use AidingApp\Engagement\Models\UnmatchedInboundCommunication;
use AidingApp\ServiceManagement\Models\ServiceRequestPriority;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use AidingApp\ServiceManagement\Models\TenantServiceRequestTypeDomain;
use App\Actions\Paths\ModulePath;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Mockery\MockInterface;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseEmpty;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\partialMock;

it('handles spam verdict failure properly', function () {
    Storage::fake('s3');
    Storage::fake('s3-inbound-email');

    $modulePath = resolve(ModulePath::class);

    $content = file_get_contents($modulePath('engagement', 'tests/Fixtures/s3_email_spam'));

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

    $content = file_get_contents($modulePath('engagement', 'tests/Fixtures/s3_email_virus'));

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
    Storage::fake('s3-inbound-email');

    $modulePath = resolve(ModulePath::class);

    $content = file_get_contents($modulePath('engagement', 'tests/Fixtures/s3_email'));

    $file = UploadedFile::fake()->createWithContent('s3_email', $content);

    Storage::disk('s3-inbound-email')->putFileAs('', $file, 's3_email');

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
    Storage::disk('s3-inbound-email')->assertMissing('s3_email');
});

it('properly creates an EngagementResponse for an inbound email', function () {
    Storage::fake('s3');
    Storage::fake('s3-inbound-email');

    $contact = Contact::factory()->create([
        'email' => 'kevin.ullyott@canyongbs.com',
    ]);

    $modulePath = resolve(ModulePath::class);

    $content = file_get_contents($modulePath('engagement', 'tests/Fixtures/s3_email'));

    $file = UploadedFile::fake()->createWithContent('s3_email', $content);

    Storage::disk('s3-inbound-email')->putFileAs('', $file, 's3_email');

    /** @var ProcessSesS3InboundEmail $mock */
    $mock = partialMock(ProcessSesS3InboundEmail::class, function (MockInterface $mock) use ($content) {
        $mock
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('getContent')
            ->once()
            ->andReturn($content);
    });

    invade($mock)->emailFilePath = 's3_email';

    Storage::disk('s3-inbound-email')->assertExists('s3_email');

    $mock->handle();

    assertDatabaseHas(EngagementResponse::class, [
        'subject' => 'This is a test',
        'sender_id' => $contact->getKey(),
        'sender_type' => $contact->getMorphClass(),
        'type' => EngagementResponseType::Email->value,
        'raw' => file_get_contents($modulePath('engagement', 'tests/Fixtures/s3_email')),
    ]);

    Storage::disk('s3-inbound-email')->assertMissing('s3_email');
});

it('handles attachements properly', function () {})->todo('Test attachments handling in ProcessSesS3InboundEmail');

it('handles exceptions correctly in the failed method', function (?Exception $exception, string $expectedPath) {
    Storage::fake('s3');
    $filesystem = Storage::fake('s3-inbound-email');

    $modulePath = resolve(ModulePath::class);

    $content = file_get_contents($modulePath('engagement', 'tests/Fixtures/s3_email'));

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
]);

it('throws the right exception when unable to find the ServiceRequestType')->todo();

it('handles is_email_automatic_creation_enabled being disabled properly')->todo();

it('sends ineligible email when no contact is found for a service request and we cannot match the email to an organization')->todo();

it('sends ineligible email when no contact is found for a service request and is_email_automatic_creation_contact_create_enabled is disabled')->todo();

it('creates a new contact for a service request when is_email_automatic_creation_contact_create_enabled is enabled')->todo();

it('properly creates a service request for a matching contact', function () {
    Storage::fake('s3');
    Storage::fake('s3-inbound-email');

    $contact = Contact::factory()->create([
        'email' => 'kevin.ullyott@canyongbs.com',
    ]);

    $modulePath = resolve(ModulePath::class);

    $content = file_get_contents($modulePath('engagement', 'tests/Fixtures/s3_email_for_service_request'));

    $file = UploadedFile::fake()->createWithContent('s3_email', $content);

    Storage::disk('s3-inbound-email')->putFileAs('', $file, 's3_email');

    /** @var ProcessSesS3InboundEmail $mock */
    $mock = partialMock(ProcessSesS3InboundEmail::class, function (MockInterface $mock) use ($content) {
        $mock
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('getContent')
            ->once()
            ->andReturn($content);
    });

    invade($mock)->emailFilePath = 's3_email';

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

    $serviceRequestType->update([
        'email_automatic_creation_priority_id' => $serviceRequestType->priorities->first()->getKey(),
    ]);

    Storage::disk('s3-inbound-email')->assertExists('s3_email');

    $mock->handle();

    assertDatabaseEmpty(EngagementResponse::class);

    Storage::disk('s3-inbound-email')->assertMissing('s3_email');
})->only();
