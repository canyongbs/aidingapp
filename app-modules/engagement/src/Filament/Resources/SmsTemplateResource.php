<?php

/*
<COPYRIGHT>

Copyright © 2022-2023, Canyon GBS LLC

All rights reserved.

This file is part of a project developed using Laravel, which is an open-source framework for PHP.
Canyon GBS LLC acknowledges and respects the copyright of Laravel and other open-source
projects used in the development of this solution.

This project is licensed under the Affero General Public License (AGPL) 3.0.
For more details, see https://github.com/canyongbs/assistbycanyongbs/blob/main/LICENSE.

Notice:
- The copyright notice in this file and across all files and applications in this
 repository cannot be removed or altered without violating the terms of the AGPL 3.0 License.
- The software solution, including services, infrastructure, and code, is offered as a
 Software as a Service (SaaS) by Canyon GBS LLC.
- Use of this software implies agreement to the license terms and conditions as stated
 in the AGPL 3.0 License.

For more information or inquiries please visit our website at
https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace Assist\Engagement\Filament\Resources;

use Filament\Resources\Resource;
use Assist\Engagement\Models\SmsTemplate;
use Assist\Engagement\Filament\Resources\SmsTemplateResource\Pages\EditSmsTemplate;
use Assist\Engagement\Filament\Resources\SmsTemplateResource\Pages\ListSmsTemplates;
use Assist\Engagement\Filament\Resources\SmsTemplateResource\Pages\CreateSmsTemplate;

class SmsTemplateResource extends Resource
{
    protected static ?string $model = SmsTemplate::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Product Administration';

    protected static ?string $navigationLabel = 'Text Message Templates';

    protected static ?string $modelLabel = 'text message template';

    protected static ?int $navigationSort = 12;

    protected static bool $shouldRegisterNavigation = false;

    public static function getPages(): array
    {
        return [
            'index' => ListSmsTemplates::route('/'),
            'create' => CreateSmsTemplate::route('/create'),
            'edit' => EditSmsTemplate::route('/{record}/edit'),
        ];
    }
}
