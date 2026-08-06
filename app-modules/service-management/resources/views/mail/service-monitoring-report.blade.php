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
    Your {{ strtolower($serviceMonitoringTarget->report_frequency->value) }} service monitor report for {{ $serviceMonitoringTarget->name }} is ready.

    ## Service Monitor
    {{ $serviceMonitoringTarget->name }}
    {{ $serviceMonitoringTarget->domain }}

    ## Reporting Period
    {{ $reportPeriodStart }} through {{ $reportPeriodEnd }}

    ## Status Summary
    Current Status: {{ $serviceMonitoringTarget->latestHistory->succeeded ? 'Successful' : 'Failed' }}
    Uptime: {{ $uptimePercentage }}
    Successful Checks: {{ $successfulChecks }}
    Failed Checks: {{ $failedChecks }}
    Average Response Time: {{ $averageResponseTime }}
    Total Downtime: {{ $totalDowntime }}

    ## Incidents
    {{ $incidentSummary }}

    Last Checked: {{ $serviceMonitoringTarget->latestHistory->created_at->format('M j, Y g:i a (T)') }}

    {{-- Footer --}}
    <x-slot:footer>
        <x-mail::footer>
            This email was sent using Aiding App®. <br /> <br /> © 2016-{{ date('Y') }} Canyon GBS Inc. All Rights
            Reserved. Canyon GBS® and Aiding App® are trademarks of Canyon GBS Inc.
        </x-mail::footer>
    </x-slot:footer>
</x-mail::layout>