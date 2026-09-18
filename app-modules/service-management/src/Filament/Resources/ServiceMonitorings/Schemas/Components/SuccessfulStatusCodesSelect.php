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

namespace AidingApp\ServiceManagement\Filament\Resources\ServiceMonitorings\Schemas\Components;

use AidingApp\ServiceManagement\Enums\MonitorType;
use AidingApp\ServiceManagement\Rules\ValidHttpStatusCodes;
use App\Features\ServiceMonitoringApiEndpointFeature;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Utilities\Get;

class SuccessfulStatusCodesSelect
{
    public static function make(): Select
    {
        return Select::make('successful_status_codes')
            ->label('Successful HTTP Status Codes')
            ->multiple()
            ->options(self::options())
            ->searchable()
            ->default([200])
            ->required()
            ->rules([new ValidHttpStatusCodes()])
            ->hintIcon('heroicon-m-question-mark-circle', 'The response is considered successful when its final HTTP status code (after following any redirects, if enabled) matches one of the selected codes.')
            ->visible(fn (Get $get): bool => $get('monitor_type') === MonitorType::ApiEndpoint && ServiceMonitoringApiEndpointFeature::active())
            ->columnSpanFull();
    }

    /**
     * @return array<int, string>
     */
    public static function options(): array
    {
        return [
            100 => '100 Continue',
            101 => '101 Switching Protocols',
            102 => '102 Processing',
            103 => '103 Early Hints',
            200 => '200 OK',
            201 => '201 Created',
            202 => '202 Accepted',
            203 => '203 Non-Authoritative Information',
            204 => '204 No Content',
            205 => '205 Reset Content',
            206 => '206 Partial Content',
            207 => '207 Multi-Status',
            208 => '208 Already Reported',
            226 => '226 IM Used',
            300 => '300 Multiple Choices',
            301 => '301 Moved Permanently',
            302 => '302 Found',
            303 => '303 See Other',
            304 => '304 Not Modified',
            305 => '305 Use Proxy',
            307 => '307 Temporary Redirect',
            308 => '308 Permanent Redirect',
            400 => '400 Bad Request',
            401 => '401 Unauthorized',
            402 => '402 Payment Required',
            403 => '403 Forbidden',
            404 => '404 Not Found',
            405 => '405 Method Not Allowed',
            406 => '406 Not Acceptable',
            407 => '407 Proxy Authentication Required',
            408 => '408 Request Timeout',
            409 => '409 Conflict',
            410 => '410 Gone',
            411 => '411 Length Required',
            412 => '412 Precondition Failed',
            413 => '413 Payload Too Large',
            414 => '414 URI Too Long',
            415 => '415 Unsupported Media Type',
            416 => '416 Range Not Satisfiable',
            417 => '417 Expectation Failed',
            418 => "418 I'm a Teapot",
            421 => '421 Misdirected Request',
            422 => '422 Unprocessable Content',
            423 => '423 Locked',
            424 => '424 Failed Dependency',
            425 => '425 Too Early',
            426 => '426 Upgrade Required',
            428 => '428 Precondition Required',
            429 => '429 Too Many Requests',
            431 => '431 Request Header Fields Too Large',
            451 => '451 Unavailable For Legal Reasons',
            500 => '500 Internal Server Error',
            501 => '501 Not Implemented',
            502 => '502 Bad Gateway',
            503 => '503 Service Unavailable',
            504 => '504 Gateway Timeout',
            505 => '505 HTTP Version Not Supported',
            506 => '506 Variant Also Negotiates',
            507 => '507 Insufficient Storage',
            508 => '508 Loop Detected',
            510 => '510 Not Extended',
            511 => '511 Network Authentication Required',
        ];
    }
}
