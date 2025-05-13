<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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

use Database\Migrations\Concerns\CanModifyPermissions;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    use CanModifyPermissions;

    /**
    * @var array<string, string>
    */
    private array $permissions = [
        'change_request_response.view-any' => 'Change Request Response',
        'change_request_response.create' => 'Change Request Response',
        'change_request_response.*.view' => 'Change Request Response',
        'change_request_response.*.update' => 'Change Request Response',
        'change_request_response.*.delete' => 'Change Request Response',
        'change_request_response.*.restore' => 'Change Request Response',
        'change_request_response.*.force-delete' => 'Change Request Response',

        'consent_agreement.view-any' => 'Consent Agreement',
        'consent_agreement.create' => 'Consent Agreement',
        'consent_agreement.*.view' => 'Consent Agreement',
        'consent_agreement.*.update' => 'Consent Agreement',
        'consent_agreement.*.delete' => 'Consent Agreement',
        'consent_agreement.*.restore' => 'Consent Agreement',
        'consent_agreement.*.force-delete' => 'Consent Agreement',

        'engagement_batch.view-any' => 'Engagement Batch',
        'engagement_batch.create' => 'Engagement Batch',
        'engagement_batch.*.view' => 'Engagement Batch',
        'engagement_batch.*.update' => 'Engagement Batch',
        'engagement_batch.*.delete' => 'Engagement Batch',
        'engagement_batch.*.restore' => 'Engagement Batch',
        'engagement_batch.*.force-delete' => 'Engagement Batch',

        'outbound_deliverable.view-any' => 'Outbound Deliverable',
        'outbound_deliverable.create' => 'Outbound Deliverable',
        'outbound_deliverable.*.view' => 'Outbound Deliverable',
        'outbound_deliverable.*.update' => 'Outbound Deliverable',
        'outbound_deliverable.*.delete' => 'Outbound Deliverable',
        'outbound_deliverable.*.restore' => 'Outbound Deliverable',
        'outbound_deliverable.*.force-delete' => 'Outbound Deliverable',

        'purchase.view-any' => 'Purchase',
        'purchase.create' => 'Purchase',
        'purchase.*.view' => 'Purchase',
        'purchase.*.update' => 'Purchase',
        'purchase.*.delete' => 'Purchase',
        'purchase.*.restore' => 'Purchase',
        'purchase.*.force-delete' => 'Purchase',

        'survey.view-any' => 'Survey',
        'survey.create' => 'Survey',
        'survey.*.view' => 'Survey',
        'survey.*.update' => 'Survey',
        'survey.*.delete' => 'Survey',
        'survey.*.restore' => 'Survey',
        'survey.*.force-delete' => 'Survey',

        'timeline.view-any' => 'Timeline',
        'timeline.create' => 'Timeline',
        'timeline.*.view' => 'Timeline',
        'timeline.*.update' => 'Timeline',
        'timeline.*.delete' => 'Timeline',
        'timeline.*.restore' => 'Timeline',
        'timeline.*.force-delete' => 'Timeline',
    ];

    /**
    * @var array<string>
    */
    private array $guards = [
        'web',
        'api',
    ];

    public function up(): void
    {
        collect($this->guards)
            ->each(fn (string $guard) => $this->deletePermissions(array_keys($this->permissions), $guard));
    }

    public function down(): void
    {
        collect($this->guards)
            ->each(function (string $guard) {
                $permissions = Arr::except($this->permissions, keys: DB::table('permissions')
                    ->where('guard_name', $guard)
                    ->pluck('name')
                    ->all());

                $this->createPermissions($permissions, $guard);
            });
    }
};
