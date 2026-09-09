<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Aiding App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Aiding App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    <https://www.canyongbs.com> or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

use App\Features\ServiceMonitoringReportConfigurationsFeature;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class () extends Migration {
    /**
     * @var array<string, string>
     */
    private array $recipientColumnsByType = [
        'user' => 'user_id',
        'department' => 'department_id',
        'contact' => 'contact_id',
    ];

    public function up(): void
    {
        DB::transaction(function (): void {
            DB::table('service_monitoring_targets')
                ->select(['id', 'report_frequency', 'is_reported_via_email', 'is_reported_via_database'])
                ->where('is_reporting_active', true)
                ->whereNotNull('report_frequency')
                ->whereNotExists(function (Builder $query): void {
                    $query->select(DB::raw(1))
                        ->from('service_monitoring_report_configurations')
                        ->whereColumn('service_monitoring_report_configurations.service_monitoring_target_id', 'service_monitoring_targets.id');
                })
                ->orderBy('id')
                ->chunkById(200, function (Collection $targets): void {
                    foreach ($targets as $target) {
                        $configurationId = (string) Str::uuid();

                        DB::table('service_monitoring_report_configurations')->insert([
                            'id' => $configurationId,
                            'service_monitoring_target_id' => $target->id,
                            'frequency' => $target->report_frequency,
                            'is_active' => true,
                            'is_reported_via_email' => $target->is_reported_via_email,
                            'is_reported_via_database' => $target->is_reported_via_database,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                        foreach ($this->recipientColumnsByType as $type => $column) {
                            $this->copyRecipients($target->id, $configurationId, $type, $column);
                        }
                    }
                });

            ServiceMonitoringReportConfigurationsFeature::activate();
        });
    }

    public function down(): void
    {
        DB::transaction(function (): void {
            ServiceMonitoringReportConfigurationsFeature::deactivate();

            DB::table('service_monitoring_report_configurations')->delete();
        });
    }

    private function copyRecipients(string $targetId, string $configurationId, string $type, string $column): void
    {
        $recipients = DB::table("service_monitoring_target_report_{$type}")
            ->where('service_monitoring_target_id', $targetId)
            ->get(['id', $column]);

        if ($recipients->isEmpty()) {
            return;
        }

        DB::table("service_monitoring_report_configuration_{$type}")->insert(
            $recipients->map(fn (object $recipient): array => [
                'id' => (string) Str::uuid(),
                'service_monitoring_report_configuration_id' => $configurationId,
                $column => $recipient->$column,
                'created_at' => now(),
                'updated_at' => now(),
            ])->all()
        );
    }
};
