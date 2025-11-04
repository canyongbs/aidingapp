<?php

namespace AidingApp\Ai\Exceptions;

use Exception;

class DefaultAssistantLockedPropertyException extends Exception
{
    public function __construct(
        protected string $property,
    ) {
        parent::__construct("Cannot change the {$property} of the Organization Copilot.");
    }
}
