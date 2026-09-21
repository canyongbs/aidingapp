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
use AidingApp\ServiceManagement\Rules\UniqueRequestHeaderNames;
use App\Features\ServiceMonitoringApiEndpointFeature;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;

class RequestHeadersRepeater
{
    public static function make(): Repeater
    {
        return Repeater::make('request_headers')
            ->label('Request Headers')
            ->schema([
                TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->maxLength(255)
                    // RFC 7230 token grammar: header field names can't contain spaces, colons, or
                    // other characters an HTTP client rejects when actually sending the request.
                    ->regex('/^[!#$%&\'*+\-.^_`|~0-9A-Za-z]+$/')
                    ->validationMessages([
                        'regex' => 'The header name may only contain letters, digits, and the characters !#$%&\'*+-.^_`|~.',
                    ]),
                TextInput::make('value')
                    ->label('Value')
                    ->required()
                    ->maxLength(65535)
                    ->regex('/^[^\r\n]*$/')
                    ->validationMessages([
                        'regex' => 'The header value may not contain line breaks.',
                    ]),
            ])
            ->columns(2)
            ->addActionLabel('Add Header')
            ->defaultItems(0)
            ->rules([new UniqueRequestHeaderNames()])
            ->visible(fn (Get $get): bool => $get('monitor_type') === MonitorType::ApiEndpoint && ServiceMonitoringApiEndpointFeature::active())
            ->columnSpanFull();
    }
}
