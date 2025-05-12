<?php

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
