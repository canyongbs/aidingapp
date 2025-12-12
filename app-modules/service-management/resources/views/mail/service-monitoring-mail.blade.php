{{--
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
Hello {{ $user->name }},

This is an automated alert from Aiding App.

<b>Issue Details:</b>
<ul>
<li><b>Service Name:</b> {{ $historicalServiceMonitoring->serviceMonitoringTarget->name }}</li>
<li><b>Domain:</b> {{ $historicalServiceMonitoring->serviceMonitoringTarget->domain }}</li>
<li><b>Expected HTTP Status:</b> 200</li>
<li><b>Actual HTTP Status:</b> {{ $historicalServiceMonitoring->response }}</li>
<li><b>Response Time:</b> {{ $historicalServiceMonitoring->response_time }} seconds</li>
<li><b>Time of Incident:</b> {{ $historicalServiceMonitoring->created_at }}</li>
</ul>

Our system detected that the service did not return the expected response during its latest check.

    {{-- Footer --}}
    <x-slot:footer>
        <x-mail::footer>
            This email was sent using Aiding App™. <br /> <br /> © 2016-{{ date('Y') }} Canyon GBS LLC. All Rights
            Reserved. Canyon GBS™ and Aiding App™ are trademarks of Canyon GBS LLC.
        </x-mail::footer>
    </x-slot:footer>
</x-mail::layout>
