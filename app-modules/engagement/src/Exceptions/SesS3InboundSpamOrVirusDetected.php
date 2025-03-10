<?php

namespace AidingApp\Engagement\Exceptions;

use Exception;

class SesS3InboundSpamOrVirusDetected extends Exception
{
    public function __construct(
        protected string $file,
        protected string $spamVerdict,
        protected string $virusVerdict,
    ) {
        parent::__construct('An inbound email was detected as spam or containing a virus.');
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
            'spam_verdict' => $this->spamVerdict,
            'virus_verdict' => $this->virusVerdict,
        ];
    }
}
