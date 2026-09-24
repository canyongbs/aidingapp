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

use AidingApp\ServiceManagement\Models\ServiceMonitoringTarget;

it('excludes its basic auth credentials from serialization and audits', function () {
    $serviceMonitoringTarget = ServiceMonitoringTarget::factory()->basicAuth()->create();

    $audit = $serviceMonitoringTarget->audits()->latest()->firstOrFail();

    expect($serviceMonitoringTarget->toArray())->not->toHaveKey('auth_username')
        ->and($serviceMonitoringTarget->toArray())->not->toHaveKey('auth_password')
        ->and($audit->new_values)->not->toHaveKey('auth_username')
        ->and($audit->new_values)->not->toHaveKey('auth_password')
        ->and($audit->old_values)->not->toHaveKey('auth_username')
        ->and($audit->old_values)->not->toHaveKey('auth_password');
});

it('computes is_max_latency_enabled from whether max_latency_ms is set', function () {
    $enabled = ServiceMonitoringTarget::factory()->apiEndpoint()->create(['max_latency_ms' => 500]);
    $disabled = ServiceMonitoringTarget::factory()->apiEndpoint()->create(['max_latency_ms' => null]);

    expect($enabled->is_max_latency_enabled)->toBeTrue()
        ->and($disabled->is_max_latency_enabled)->toBeFalse();
});
