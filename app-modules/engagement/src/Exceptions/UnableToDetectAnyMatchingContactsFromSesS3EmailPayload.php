<?php

namespace AidingApp\Engagement\Exceptions;

use Exception;

class UnableToDetectAnyMatchingContactsFromSesS3EmailPayload extends Exception
{
    public function __construct(
        protected string $file,
    ) {
        parent::__construct(message: 'Unable to detect any matching Contacts from SES S3 email payload.');
    }

    /**
     * Get the exception's context information.
     *
     * @return array<string, mixed>
     */
    public function context(): array
    {
        return [
            'file' => $this->file,
        ];
    }
}
