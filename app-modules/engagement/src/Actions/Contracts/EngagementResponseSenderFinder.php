<?php

namespace AidingApp\Engagement\Actions\Contracts;

use AidingApp\Contact\Models\Contact;

interface EngagementResponseSenderFinder
{
    public function find(string $phoneNumber): Contact|null;
}
