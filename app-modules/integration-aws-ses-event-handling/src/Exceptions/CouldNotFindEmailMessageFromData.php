<?php

namespace AidingApp\IntegrationAwsSesEventHandling\Exceptions;

use AidingApp\IntegrationAwsSesEventHandling\DataTransferObjects\SesEventData;
use Exception;

class CouldNotFindEmailMessageFromData extends Exception
{
    public function __construct(
        protected SesEventData $data,
    ) {
        parent::__construct('Could not find an email message from the given data.');
    }

    /**
     * Get the exception's context information.
     *
     * @return array<string, mixed>
     */
    public function context(): array
    {
        return [
            'event_data' => $this->data->toArray(),
        ];
    }
}
