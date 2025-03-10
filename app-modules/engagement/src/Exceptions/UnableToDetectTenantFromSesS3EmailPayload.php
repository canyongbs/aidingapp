<?php

namespace AidingApp\Engagement\Exceptions;

use Exception;

class UnableToDetectTenantFromSesS3EmailPayload extends Exception
{
    public function __construct(
        protected string $file,
    ) {
        parent::__construct(message: 'Unable to detect tenant from SES S3 email payload.');
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
