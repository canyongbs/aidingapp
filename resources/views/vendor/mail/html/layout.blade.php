{{--
    <COPYRIGHT>
    
    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.
    
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
@props(['url' => null,'settings' => null])
@php
    use AidingApp\IntegrationAwsSesEventHandling\Settings\SesSettings;
    use Filament\Support\Colors\Color;
    use App\Settings\EmailSettings;
    use Filament\Forms\Components\RichEditor\RichContentRenderer;use App\Models\SettingsProperty;
    use AidingApp\Theme\Settings\ThemeSettings;

    $themeSettings = app(ThemeSettings::class);
    $settingsProperty = $themeSettings::getSettingsPropertyModel('theme.is_logo_active');
    $logo = $settingsProperty->getFirstMedia('logo');

    $emailSettings = app(EmailSettings::class);
    $paragraphTextColor = $emailSettings->paragraph_text_color ?? app(SesSettings::class)->paragraph_text_color;
    $h1TextColor = $emailSettings->h1_text_color ?? null;
    $h2TextColor = $emailSettings->h2_text_color ?? null;
    $backgroundColor = $emailSettings->background_color ?? null;
    $color = Color::all()[$settings?->primary_color ?? 'blue'];
    $footer = $emailSettings->footer ? RichContentRenderer::make($emailSettings->footer)->toHtml() : null;
    $headerLogoModel = $emailSettings->getSettingsPropertyModel('email.header_logo');
    $headerLogo = $headerLogoModel->getFirstMedia('header_logo')
        ? $headerLogoModel->getFirstTemporaryUrl(now()->addDays(6), 'header_logo')
        : null;
    $settingsLogoUrl = $settings?->getFirstMedia('logo')
        ? $settings->getFirstTemporaryUrl(now()->addDays(6), 'logo')
        : null;
@endphp

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>Message</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
    <style>
        
        body,
        .panel-content,
        .panel-content p {
            color: {{ $paragraphTextColor }};
        }
        @if($h1TextColor)
        h1,
        .panel-content h1 {
            color: {{ $h1TextColor ?? '#3d4852' }};
        }
        @endif

        @if($h2TextColor)
        h2,
        .panel-content h2 {
            color: {{ $h2TextColor }};
        }
        @endif

        @media only screen and (max-width: 600px) {
            .inner-body {
                width: 100% !important;
            }

            .footer {
                width: 100% !important;
            }
        }

        @media only screen and (max-width: 500px) {
            .button {
                width: 100% !important;
            }
        }

        .button-primary {
            background-color: rgb({{ $color[600] }});
            border-bottom: 8px solid rgb({{ $color[600] }});
            border-left: 18px solid rgb({{ $color[600] }});
            border-right: 18px solid rgb({{ $color[600] }});
            border-top: 8px solid rgb({{ $color[600] }});
        }
    </style>
</head>
<body >

<table class="wrapper" width="100%" cellpadding="0" cellspacing="0" role="presentation">
    <tr>
        <td align="center" >                      
            <table class="content" width="100%" cellpadding="0" cellspacing="0" role="presentation">
                {{-- Header --}}
                <tr>
                    <td class="header">
                        <a href="{{ $url ?? config('app.url') }}" style="display: inline-block;">
                            @if ($headerLogo)
                                <img src="{{ $headerLogo }}" class="logo" alt="Logo">
                            @elseif ($settings?->hasMedia('logo'))
                                <img src="{{ $settingsLogoUrl }}"
                                    style="height: 75px; max-height: 75px; max-width: 100vw;"
                                    alt="Logo">
                            @elseif ($themeSettings->is_logo_active && $logo)
                                <img src="{{ $logo->getTemporaryUrl(now()->addDays(6)) }}"
                                    style="height: 75px; max-height: 75px; max-width: 100vw;"
                                    alt="Logo">
                            @else
                                <img src="{{ url(Vite::asset('resources/images/default-logo-light-1735308866.svg')) }}"
                                    style="height: 75px; max-height: 75px; max-width: 100vw;"
                                    alt="Logo">
                            @endif
                        </a>
                    </td>
                </tr>
                <!-- Email Body -->
                <tr>
                    <td class="body" width="100%" cellpadding="0" cellspacing="0" style="border: hidden !important;" >
                        <table class="inner-body" align="center" width="570" cellpadding="0" cellspacing="0"
                               role="presentation">
                            <!-- Body content -->
                            
                            <tr @if($backgroundColor) style="background-color: {{ $backgroundColor }};" @endif>
                                
                                <td class="content-cell">
                                    {{ Illuminate\Mail\Markdown::parse($slot) }}

                                    {{ $subcopy ?? '' }}
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            {{-- Footer --}}
            <tr>
                <td>
                <table class="footer" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation">
                <tr>
                <td class="content-cell" align="center">
                @if ($footer ?? null)
                {!! $footer !!}
                @else
                    This email was sent using Aiding App™. <br /> <br /> © 2016-{{ date('Y') }} Canyon GBS LLC. All Rights Reserved. Canyon GBS™ and Aiding App™ are trademarks of Canyon GBS LLC.
                @endif    
                </td>
                </tr>
                </table>
                </td>
            </tr>
                        
        </td>
    </tr>
    
</table>
</body>
</html>
