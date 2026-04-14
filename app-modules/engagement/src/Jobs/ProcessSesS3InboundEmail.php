<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Aiding App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Aiding App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    <https://www.canyongbs.com> or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AidingApp\Engagement\Jobs;

use AidingApp\Contact\Enums\SystemContactClassification;
use AidingApp\Contact\Models\Contact;
use AidingApp\Contact\Models\ContactType;
use AidingApp\Contact\Models\Organization;
use AidingApp\Engagement\Enums\EngagementResponseType;
use AidingApp\Engagement\Exceptions\SesS3InboundServiceRequestTypeNotFound;
use AidingApp\Engagement\Exceptions\SesS3InboundSpamOrVirusDetected;
use AidingApp\Engagement\Exceptions\UnableToDetectTenantFromSesS3EmailPayload;
use AidingApp\Engagement\Exceptions\UnableToRetrieveContentFromSesS3EmailPayload;
use AidingApp\Engagement\Models\EngagementResponse;
use AidingApp\Engagement\Models\UnmatchedInboundCommunication;
use AidingApp\Engagement\Notifications\IneligibleContactSesS3InboundEmailServiceRequestNotification;
use AidingApp\Notification\Models\OutboundEmailMessageId;
use AidingApp\ServiceManagement\Enums\SystemServiceRequestClassification;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestStatus;
use AidingApp\ServiceManagement\Models\TenantServiceRequestTypeDomain;
use App\Features\ServiceRequestEmailThreading;
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
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
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

            $classified = $this->classifyAddresses($parser);

            throw_if(
                $classified->isEmpty(),
                new UnableToDetectTenantFromSesS3EmailPayload($this->emailFilePath),
            );

            $sender = $parser->getAddresses('from')[0]['address'];

            $classified->each(function (array $entry) use ($parser, $content, $sender) {
                $type = $entry['type'];
                $tenant = $entry['tenant'];
                $srTypeDomain = $entry['srTypeDomain'] ?? null;

                match ($type) {
                    'engagement' => $this->handleEngagement($tenant, $parser, $content, $sender),
                    'service_request_reply' => $this->handleServiceRequestReply($tenant, $parser, $sender),
                    'service_request_type' => $this->handleServiceRequestType($srTypeDomain, $parser, $sender),
                    'legacy' => $this->handleLegacyAddress($tenant, $parser, $sender),
                    default => null,
                };
            });

            Storage::disk('s3-inbound-email')->delete($this->emailFilePath);

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
            report(new Exception('ProcessSesS3InboundEmail job failed with null exception.'));

            $this->moveFile('/failed');

            return;
        }

        report($exception);

        match ($exception::class) {
            SesS3InboundSpamOrVirusDetected::class => $this->moveFile('/spam-or-virus-detected'),
            default => $this->moveFile('/failed'),
        };
    }

    /**
     * Classify inbound email addresses into routing groups.
     *
     * @return Collection<int, array{type: string, tenant: Tenant, srTypeDomain?: TenantServiceRequestTypeDomain}>
     */
    protected function classifyAddresses(Parser $parser): Collection
    {
        return collect($parser->getAddresses('to'))
            ->pluck('address')
            ->map(function (string $address) {
                $localPart = filter_var($address, FILTER_VALIDATE_EMAIL) ? explode('@', $address)[0] : null;

                if ($localPart === null) {
                    return null;
                }

                $localPartLower = mb_strtolower($localPart);

                // noreply — discard immediately
                // TODO: Future auto-reply to noreply address
                if ($localPartLower === 'noreply') {
                    return null;
                }

                // {tenant}-msg — engagement path
                if (preg_match('/^(.+)-msg$/', $localPartLower, $matches)) {
                    $tenant = $this->resolveTenant($matches[1]);

                    if ($tenant) {
                        return ['type' => 'engagement', 'tenant' => $tenant];
                    }
                }

                // {tenant}-sr-{type} — new SR creation for type (must check before {tenant}-sr)
                if (preg_match('/^(.+)-sr-(.+)$/', $localPartLower, $matches)) {
                    $tenantSubdomain = $matches[1];
                    $typeDomain = $matches[2];

                    $serviceRequestTypeDomain = TenantServiceRequestTypeDomain::query()
                        ->where(DB::raw('LOWER(domain)'), $typeDomain)
                        ->whereRelation('tenant', 'domain', 'like', "{$tenantSubdomain}.%")
                        ->first();

                    if ($serviceRequestTypeDomain) {
                        return [
                            'type' => 'service_request_type',
                            'tenant' => $serviceRequestTypeDomain->tenant,
                            'srTypeDomain' => $serviceRequestTypeDomain,
                        ];
                    }
                }

                // {tenant}-sr — SR reply path (header matching)
                if (preg_match('/^(.+)-sr$/', $localPartLower, $matches)) {
                    $tenant = $this->resolveTenant($matches[1]);

                    if ($tenant) {
                        return ['type' => 'service_request_reply', 'tenant' => $tenant];
                    }
                }

                // Legacy: bare {tenant} — create UnmatchedInboundCommunication
                $tenant = $this->resolveTenant($localPartLower);

                if ($tenant) {
                    return ['type' => 'legacy', 'tenant' => $tenant];
                }

                return null;
            })
            ->filter()
            ->values();
    }

    protected function resolveTenant(string $subdomain): ?Tenant
    {
        return Tenant::query()
            ->where(
                DB::raw('LOWER(domain)'),
                'like',
                strtolower("{$subdomain}.%")
            )
            ->first();
    }

    protected function handleEngagement(Tenant $tenant, Parser $parser, string $content, string $sender): void
    {
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
        });
    }

    protected function handleServiceRequestReply(Tenant $tenant, Parser $parser, string $sender): void
    {
        $tenant->execute(function () use ($parser, $sender) {
            // TODO: FeatureFlag Cleanup - Remove this check after ServiceRequestEmailThreading is removed
            if (! ServiceRequestEmailThreading::active()) {
                // FF not active — can't do header matching, ignore the email
                return;
            }

            $serviceRequest = $this->resolveServiceRequestFromHeaders($parser);

            if (! $serviceRequest) {
                // TODO: Future auto-reply informing sender that the system could not match their email to an existing Service Request
                return;
            }

            // Verify sender matches the SR respondent
            if (mb_strtolower($sender) !== mb_strtolower($serviceRequest->respondent->email)) {
                // TODO: Future auto-reply informing sender of mismatch
                return;
            }

            // Create SR Update — do NOT change status (even if closed)
            $serviceRequest->serviceRequestUpdates()->create([
                'update' => $parser->getMessageBody('text') ?: $parser->getMessageBody('html'),
                'internal' => false,
                'created_by_id' => $serviceRequest->respondent->getKey(),
                'created_by_type' => $serviceRequest->respondent->getMorphClass(),
            ]);
        });
    }

    protected function resolveServiceRequestFromHeaders(Parser $parser): ?ServiceRequest
    {
        $inboundDomain = config('mail.from.root_domain');

        // Try In-Reply-To header first
        $inReplyTo = $parser->getHeader('In-Reply-To');

        if ($inReplyTo) {
            // Strip angle brackets
            $messageId = trim($inReplyTo, '<> ');

            $outbound = OutboundEmailMessageId::query()
                ->where('message_id', $messageId)
                ->first();

            if ($outbound) {
                $trackable = $outbound->trackable;

                if ($trackable instanceof ServiceRequest) {
                    return $trackable;
                }
            }
        }

        // Try References header
        $references = $parser->getHeader('References');

        if ($references) {
            // Extract all Message-IDs from References header
            preg_match_all('/<([^>]+)>/', $references, $matches);

            if (! empty($matches[1])) {
                // Filter to our domain and look up
                $ourMessageIds = collect($matches[1])
                    ->filter(fn (string $id) => str_contains($id, $inboundDomain));

                if ($ourMessageIds->isNotEmpty()) {
                    $outbound = OutboundEmailMessageId::query()
                        ->whereIn('message_id', $ourMessageIds->all())
                        ->orderByDesc('id')
                        ->first();

                    if ($outbound) {
                        $trackable = $outbound->trackable;

                        if ($trackable instanceof ServiceRequest) {
                            return $trackable;
                        }
                    }
                }
            }
        }

        return null;
    }

    protected function handleServiceRequestType(TenantServiceRequestTypeDomain $serviceRequestTypeDomain, Parser $parser, string $sender): void
    {
        $serviceRequestTypeDomain->tenant->execute(function () use ($serviceRequestTypeDomain, $parser, $sender) {
            $serviceRequestType = $serviceRequestTypeDomain->serviceRequestType;

            if (is_null($serviceRequestType)) {
                throw new SesS3InboundServiceRequestTypeNotFound(
                    $this->emailFilePath,
                    $serviceRequestTypeDomain
                );
            }

            if (! $serviceRequestType->is_email_automatic_creation_enabled) {
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
                    Notification::route('mail', $sender)
                        ->notifyNow(new IneligibleContactSesS3InboundEmailServiceRequestNotification(
                            $serviceRequestTypeDomain,
                            $parser->getMessageBody('htmlEmbedded')
                        ));

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

                $type = ContactType::query()
                    ->where('classification', SystemContactClassification::New)
                    ->firstOrFail();

                $contact->type()->associate($type);

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

                $serviceRequest->priority()->associate($serviceRequestType->email_automatic_creation_priority_id);

                $serviceRequest->saveOrFail();

                foreach ($parser->getAttachments(false) as $attachment) {
                    $serviceRequest->addMediaFromStream($attachment->getStream())
                        ->setName($attachment->getFilename())
                        ->setFileName($attachment->getFilename())
                        ->toMediaCollection('uploads');
                }
            });
        });
    }

    protected function handleLegacyAddress(Tenant $tenant, Parser $parser, string $sender): void
    {
        $tenant->execute(function () use ($parser, $sender) {
            UnmatchedInboundCommunication::create([
                'type' => EngagementResponseType::Email,
                'subject' => $parser->getHeader('subject'),
                'body' => $parser->getMessageBody('htmlEmbedded'),
                'occurred_at' => $parser->getHeader('date'),
                'sender' => $sender,
            ]);
        });
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
                '@CommitmentPolicy' => 'FORBID_ENCRYPT_ALLOW_DECRYPT',
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
