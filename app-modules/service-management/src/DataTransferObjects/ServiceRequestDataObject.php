<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

    Aiding App™ is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Aiding App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    <https://www.canyongbs.com> or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AidingApp\ServiceManagement\DataTransferObjects;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class ServiceRequestDataObject extends Data
{
    public function __construct(
        public string|Optional $division_id,
        public string|Optional $status_id,
        public string $type_id,
        public string|Optional $priority_id,
        public string|Optional $title,
        public string|Optional $close_details,
        public string|Optional $res_details,
        public string $respondent_id,
    ) {}

    public static function fromData(array $data): static
    {
        return new self(
            division_id: $data['division_id'] ?? Optional::create(),
            status_id: $data['status_id'] ?? Optional::create(),
            type_id: $data['type_id'],
            priority_id: $data['priority_id'] ?? Optional::create(),
            title: $data['title'] ?? Optional::create(),
            close_details: $data['close_details'] ?? Optional::create(),
            res_details: $data['res_details'] ?? Optional::create(),
            respondent_id: $data['respondent_id'],
        );
    }
}
