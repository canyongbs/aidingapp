<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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

namespace AidingApp\Engagement\Jobs;

use AidingApp\Contact\Enums\SystemContactClassification;
use AidingApp\Contact\Models\Contact;
use AidingApp\Contact\Models\ContactSource;
use AidingApp\Contact\Models\ContactStatus;
use AidingApp\Contact\Models\Organization;
use AidingApp\Engagement\Enums\EngagementResponseType;
use AidingApp\Engagement\Exceptions\SesS3InboundServiceRequestTypeNotFound;
use AidingApp\Engagement\Exceptions\SesS3InboundSpamOrVirusDetected;
use AidingApp\Engagement\Exceptions\UnableToDetectTenantFromSesS3EmailPayload;
use AidingApp\Engagement\Exceptions\UnableToRetrieveContentFromSesS3EmailPayload;
use AidingApp\Engagement\Models\EngagementResponse;
use AidingApp\Engagement\Models\UnmatchedInboundCommunication;
use AidingApp\ServiceManagement\Enums\SystemServiceRequestClassification;
use AidingApp\ServiceManagement\Models\ServiceRequestStatus;
use AidingApp\ServiceManagement\Models\TenantServiceRequestTypeDomain;
use App\Models\Tenant;
use Aws\Crypto\KmsMaterialsProviderV2;
use Aws\Kms\KmsClient;
use Aws\S3\Crypto\S3EncryptionClientV2;
use Aws\S3\S3Client;
use Carbon\CarbonImmutable;
use Exception;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpMimeMailParser\Attachment;
use PhpMimeMailParser\Parser;
use Spatie\Multitenancy\Jobs\NotTenantAware;
use Throwable;

class ProcessSesS3InboundEmail implements ShouldQueue, ShouldBeUnique, NotTenantAware
{
    use Queueable;

    // Unique for 30 minutes
    public int $uniqueFor = 1800;

    public function __construct(
        protected string $emailFilePath
    ) {}

    public function uniqueId(): string
    {
        return $this->emailFilePath;
    }

    public function handle(): void
    {
        DB::beginTransaction();

        try {
            $content = $this->getContent();

            $parser = (new Parser())
                ->setText($content);

            // In the future, this probably shouldn't result in a job failure. We should just log it and move on.
            throw_if(
                $parser->getHeader('X-SES-Spam-Verdict') !== 'PASS' || $parser->getHeader('X-SES-Virus-Verdict') !== 'PASS',
                new SesS3InboundSpamOrVirusDetected($this->emailFilePath, $parser->getHeader('X-SES-Spam-Verdict'), $parser->getHeader('X-SES-Virus-Verdict')),
            );

            $matchedTenants = collect($parser->getAddresses('to'))
                ->pluck('address')
                ->mapToGroups(function (string $address) {
                    $localPart = filter_var($address, FILTER_VALIDATE_EMAIL) ? explode('@', $address)[0] : null;

                    if ($localPart === null) {
                        return null;
                    }

                    $tenant = Tenant::query()
                        ->where(
                            DB::raw('LOWER(domain)'),
                            'like',
                            strtolower("{$localPart}.%")
                        )
                        ->first();

                    if ($tenant) {
                        return ['engagements' => $tenant];
                    }

                    preg_match('/([a-z0-9\-]+)\-([a-z0-9]+)/', $localPart, $matches);

                    if (isset($matches[1]) && isset($matches[2])) {
                        $tenantDomain = mb_strtolower($matches[1]);
                        $typeDomain = mb_strtolower($matches[2]);

                        $serviceRequestTypeDomain = TenantServiceRequestTypeDomain::query()
                            ->where(
                                DB::raw('LOWER(domain)'),
                                $typeDomain
                            )
                            ->whereRelation('tenant', 'domain', 'like', "{$tenantDomain}.%")
                            ->first();

                        if ($serviceRequestTypeDomain) {
                            return ['service_request' => $serviceRequestTypeDomain];
                        }
                    }

                    return null;
                })
                ->filter();

            throw_if(
                $matchedTenants->isEmpty(),
                new UnableToDetectTenantFromSesS3EmailPayload($this->emailFilePath),
            );

            $sender = $parser->getAddresses('from')[0]['address'];

            $matchedTenants->get('engagements')?->each(function (Tenant $tenant) use ($parser, $content, $sender) {
                $tenant->execute(function () use ($parser, $content, $sender) {
                    $contacts = Contact::query()
                        ->where('email', $sender)
                        ->get();

                    if ($contacts->isEmpty()) {
                        UnmatchedInboundCommunication::create([
                            'type' => EngagementResponseType::Email,
                            'subject' => $parser->getHeader('subject'),
                            'body' => $parser->getMessageBody('htmlEmbedded'),
                            'occurred_at' => $parser->getHeader('date'),
                            'sender' => $sender,
                        ]);

                        Storage::disk('s3-inbound-email')->delete($this->emailFilePath);

                        DB::commit();

                        return;
                    }

                    $contacts->each(function (Contact $contact) use ($parser, $content) {
                        /** @var EngagementResponse $engagementResponse */
                        $engagementResponse = $contact->engagementResponses()
                            ->create([
                                'subject' => $parser->getHeader('subject'),
                                'content' => $parser->getMessageBody('htmlEmbedded'),
                                'sent_at' => $parser->getHeader('date'),
                                'type' => EngagementResponseType::Email,
                                'raw' => $content,
                            ]);

                        collect($parser->getAttachments())->each(function (Attachment $attachment) use ($engagementResponse) {
                            $engagementResponse->addMediaFromStream($attachment->getStream())
                                ->setName($attachment->getFilename())
                                ->setFileName($attachment->getFilename())
                                ->toMediaCollection('attachments');
                        });
                    });

                    Storage::disk('s3-inbound-email')->delete($this->emailFilePath);

                    DB::commit();
                });
            });

            $matchedTenants->get('service_request')?->each(function (TenantServiceRequestTypeDomain $serviceRequestTypeDomain) use ($parser, $sender) {
                $serviceRequestTypeDomain->tenant->execute(function () use ($serviceRequestTypeDomain, $parser, $sender) {
                    $serviceRequestType = $serviceRequestTypeDomain->serviceRequestType;

                    if (is_null($serviceRequestType)) {
                        throw new SesS3InboundServiceRequestTypeNotFound(
                            $this->emailFilePath,
                            $serviceRequestTypeDomain
                        );
                    }

                    if (! $serviceRequestType->is_email_automatic_creation_enabled) {
                        Storage::disk('s3-inbound-email')->delete($this->emailFilePath);

                        return;
                    }

                    $contacts = Contact::query()
                        ->where('email', $sender)
                        ->get();

                    if ($contacts->isEmpty()) {
                        // If no contacts are found, we will try to find an organization with the same domain
                        $domain = Str::afterLast($sender, '@');

                        $organization = Organization::query()
                            ->whereRaw('LOWER(?) = ANY (SELECT LOWER(value) FROM jsonb_array_elements_text(domains))', [mb_strtolower($domain)])
                            ->first();

                        if (! $organization || $serviceRequestType->is_email_automatic_creation_contact_create_enabled === false) {
                            // TODO: Send ineligible email
                            // TODO: Test "sends ineligible email when no contact is found for a service request and we cannot match the email to an organization"
                            // TODO: Test "sends ineligible email when no contact is found for a service request and is_email_automatic_creation_contact_create_enabled is disabled"

                            Storage::disk('s3-inbound-email')->delete($this->emailFilePath);

                            return;
                        }

                        $senderName = $parser->getAddresses('from')[0]['display'];

                        $firstName = Str::before($senderName, ' ') ?: 'Unknown';
                        $lastName = Str::after($senderName, ' ') ?: 'Unknown';
                        $fullName = $firstName . ' ' . $lastName;

                        // If an organization domain is found, we will create a new contact with the email address
                        $contact = Contact::query()
                            ->make([
                                'email' => $sender,
                                'first_name' => $firstName,
                                'last_name' => $lastName,
                                'full_name' => $fullName,
                            ]);

                        $status = ContactStatus::query()
                            ->where('classification', SystemContactClassification::New)
                            ->firstOrFail();

                        $contact->status()->associate($status);

                        // TODO: Talk with Product about whether or not this is correct.
                        $source = ContactSource::query()
                            ->where('name', 'Service Request Email Auto Creation')
                            ->first();

                        if (! $source) {
                            $source = ContactSource::query()
                                ->create([
                                    'name' => 'Service Request Email Auto Creation',
                                ]);
                        }

                        $contact->source()->associate($source);

                        $contact->organization()->associate($organization);

                        $contact->save();

                        $contacts = $contacts->add($contact);
                    }

                    $contacts->each(function (Contact $contact) use ($serviceRequestType, $parser) {
                        $serviceRequest = $contact->serviceRequests()
                            ->make([
                                'title' => $parser->getHeader('subject'),
                                'close_details' => $parser->getMessageBody('text'),
                            ]);

                        $serviceRequestStatus = ServiceRequestStatus::query()
                            ->where('classification', SystemServiceRequestClassification::Open)
                            ->where('name', 'New')
                            ->where('is_system_protected', true)
                            ->firstOrFail();

                        $serviceRequest->status()->associate($serviceRequestStatus);
                        $serviceRequest->status_updated_at = CarbonImmutable::now();

                        $serviceRequest->respondent()->associate($contact);

                        // TODO: This should not be possible if this ID does not match to anything
                        $serviceRequest->priority()->associate($serviceRequestType->email_automatic_creation_priority_id);

                        $serviceRequest->saveOrFail();

                        // TODO: Handle attachments
                    });

                    Storage::disk('s3-inbound-email')->delete($this->emailFilePath);
                });
            });

            DB::commit();
        } catch (
            UnableToRetrieveContentFromSesS3EmailPayload | SesS3InboundSpamOrVirusDetected | UnableToDetectTenantFromSesS3EmailPayload | SesS3InboundServiceRequestTypeNotFound $e) {
                DB::rollBack();

                // Instantly fail for this exception
                $this->fail($e);
            } catch (Throwable $e) {
                DB::rollBack();

                throw $e;
            }
    }

    public function failed(?Throwable $exception): void
    {
        if ($exception === null) {
            $this->moveFile('/failed');

            return;
        }

        match ($exception::class) {
            SesS3InboundSpamOrVirusDetected::class => $this->moveFile('/spam-or-virus-detected'),
            default => $this->moveFile('/failed'),
        };

        report($exception);
    }

    protected function getContent(): string
    {
        $encryptionClient = new S3EncryptionClientV2(
            new S3Client([
                'credentials' => [
                    'key' => config('filesystems.disks.s3.key'),
                    'secret' => config('filesystems.disks.s3.secret'),
                ],
                'region' => config('filesystems.disks.s3.region'),
            ])
        );

        // Needed to suppress warnings from the SDK. SES encrypts using V1 so we need @SecurityProfile to be V2_AND_LEGACY
        // But the SDK throws a warning when using V2_AND_LEGACY
        $errorReportingLevel = error_reporting();
        error_reporting(E_ERROR & ~E_WARNING);

        try {
            $result = $encryptionClient->getObject([
                '@KmsAllowDecryptWithAnyCmk' => false,
                '@SecurityProfile' => 'V2_AND_LEGACY',
                '@MaterialsProvider' => new KmsMaterialsProviderV2(
                    new KmsClient([
                        'credentials' => [
                            'key' => config('filesystems.disks.s3.key'),
                            'secret' => config('filesystems.disks.s3.secret'),
                        ],
                        'region' => 'us-west-2',
                    ]),
                    Config::string('services.kms.ses_s3_key_id')
                ),
                '@CipherOptions' => [
                    'Cipher' => 'gcm',
                    'KeySize' => 256,
                ],
                'Bucket' => config('filesystems.disks.s3.bucket'),
                'Key' => Config::string('filesystems.disks.s3-inbound-email.root') . '/' . $this->emailFilePath,
            ]);
        } finally {
            // Reset the error reporting level
            error_reporting($errorReportingLevel);
        }

        try {
            // @phpstan-ignore method.nonObject
            $content = $result['Body']?->getContents();
        } catch (Throwable $e) {
            throw new UnableToRetrieveContentFromSesS3EmailPayload($this->emailFilePath, $e);
        }

        throw_if(empty($content), new UnableToRetrieveContentFromSesS3EmailPayload($this->emailFilePath));

        return $content;
    }

    protected function moveFile(string $destination): void
    {
        Storage::disk('s3-inbound-email')->move($this->emailFilePath, $destination . '/' . $this->emailFilePath);
    }
}
