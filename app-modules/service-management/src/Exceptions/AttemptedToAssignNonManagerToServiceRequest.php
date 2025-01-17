<?php

namespace AidingApp\ServiceManagement\Exceptions;

use Exception;

class AttemptedToAssignNonManagerToServiceRequest extends Exception
{
    protected $message = 'It is not allowed to assign a non-manager to a service request';
}
