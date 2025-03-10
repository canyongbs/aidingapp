<?php

namespace AidingApp\Engagement\Exceptions;

use Exception;
use Throwable;

class UnableToRetrieveContentFromSesS3EmailPayload extends Exception
{
    protected string $file;

    public function __construct(
        string $file,
        ?Throwable $previous = null,
    ) {
        $this->file = $file;

        parent::__construct(
            message: 'Unable to retrieve content from SES S3 email payload.',
            previous: $previous,
        );
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
