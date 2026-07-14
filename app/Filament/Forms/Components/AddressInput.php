<?php

namespace App\Filament\Forms\Components;

use App\Services\AwsGeoPlacesService;
use DefStudio\SearchableInput\Forms\Components\SearchableInput;
use Filament\Notifications\Notification;
use Throwable;

class AddressInput
{
    public static function make(): SearchableInput
    {
        return SearchableInput::make('address')
            ->label('Address')
            ->searchUsing(function (string $search) {
                if (strlen($search) < 3) {
                    return [];
                }

                try {
                    $results = app(AwsGeoPlacesService::class)->autocomplete($search);

                    session()->forget('has_aws_geo_places_error_notification_sent');

                    return $results;
                } catch (Throwable $exception) {
                    if (! session()->has('has_aws_geo_places_error_notification_sent')) {
                        Notification::make()
                            ->title('Failed to fetch address suggestions')
                            ->body('An error occurred while fetching address suggestions. Please try again later.')
                            ->danger()
                            ->send();

                        session()->put('has_aws_geo_places_error_notification_sent', true);
                    }

                    report($exception);

                    return [];
                }
            })
            ->extraInputAttributes(['data-1p-ignore' => true, 'data-lpignore' => 'true', 'data-form-type' => 'other', 'data-bwignore' => true]);
    }
}