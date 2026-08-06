{{--
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
--}}
@props(['settings' => null])
<x-mail::layout :settings="$settings">
    {{-- Header --}}
    <x-slot:header>
        <x-mail::header
            :url="config('app.url')"
            :settings="$settings"
        ></x-mail::header>
    </x-slot:header>

    {{-- Body --}}
    Your {{ strtolower($serviceMonitoringTarget->report_frequency?->value ?? 'monthly') }} service monitor report for {{ $serviceMonitoringTarget->name }} is ready.

    ## Service Monitor
    {{ $serviceMonitoringTarget->name }}
    {{ $serviceMonitoringTarget->domain }}

    ## Reporting Period
    {{ $reportPeriodStart }} through {{ $reportPeriodEnd }}

    ## Status Summary
    Current Status: {{ is_null($serviceMonitoringTarget->latestHistory->succeeded) ? 'N/A' : $serviceMonitoringTarget->latestHistory->succeeded ? 'Successful' : 'Failed' }}
    Uptime: {{ $uptimePercentage }}
    Successful Checks: {{ $successfulChecks }}
    Failed Checks: {{ $failedChecks }}
    Average Response Time: {{ $averageResponseTime }}
    Total Downtime: {{ $totalDowntime }}

    ## Incidents
    {{ $incidentSummary }}

    Last Checked: {{ is_null($serviceMonitoringTarget->latestHistory) ? 'N/A' : $serviceMonitoringTarget->latestHistory->created_at->format('M j, Y g:i a (T)') }}

    {{-- Footer --}}
    <x-slot:footer>
        <x-mail::footer>
            This email was sent using Aiding App®. <br /> <br /> © 2016-{{ date('Y') }} Canyon GBS Inc. All Rights
            Reserved. Canyon GBS® and Aiding App® are trademarks of Canyon GBS Inc.
        </x-mail::footer>
    </x-slot:footer>
</x-mail::layout>