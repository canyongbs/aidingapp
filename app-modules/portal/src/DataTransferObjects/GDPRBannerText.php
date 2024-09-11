<?php

namespace AidingApp\Portal\DataTransferObjects;

use Spatie\LaravelData\Data;

class GDPRBannerText extends Data
{
    public function __construct(
        public string $text = 'We use cookies to personalize content, to provide social media features, and to analyze our traffic. We also share information about your use of our site with our partners who may combine it with other information that you\'ve provided to them or that they\'ve collected from your use of their services.',
    ) {}
}
