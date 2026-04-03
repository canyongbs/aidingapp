<?php

namespace App\Health\Checks;

use AidingApp\Authorization\Settings\AzureSsoSettings;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Spatie\Health\Checks\Check;
use Spatie\Health\Checks\Result;
use stdClass;

class AzureCredentialsExpiringCheck extends Check
{
    public function run(): Result
    {
        try {
            $azureSsoSettings = app(AzureSsoSettings::class);

            $response = Http::asForm()->post(
                'https://login.microsoftonline.com/' . $azureSsoSettings->tenant_id . '/oauth2/v2.0/token',
                [
                    'client_id' => $azureSsoSettings->client_id,
                    'client_secret' => $azureSsoSettings->client_secret,
                    'grant_type' => 'client_credentials',
                    'scope' => 'https://graph.microsoft.com/.default',
                ]
            );

            $data = Http::withToken($response->object()->access_token)
                ->get("https://graph.microsoft.com/v1.0/applications(appId='{$azureSsoSettings->client_id}')" . '?$select=passwordCredentials');

            /** @var Collection<int, stdClass> $passwordCredentials */
            $passwordCredentials = $data->object()->passwordCredentials;

            $credentials = collect($passwordCredentials)->filter(function (stdClass $item) use ($azureSsoSettings) {
                return is_null($item->hint) || Str::startsWith($azureSsoSettings->client_secret, $item->hint);
            });

            if (count($credentials) > 1) {
                $endDateTime = Carbon::parse($credentials->sortBy(fn (stdClass $item) => Carbon::parse($item->endDateTime))->first()->endDateTime);
            } else {
                $endDateTime = Carbon::parse($credentials->first()->endDateTime);
            }

            if ($endDateTime->isPast()) {
                return Result::make()->failed();
            }

            if ($endDateTime->lte(now()->addDays(45))) {
                return Result::make()->warning();
            }
        } catch (Exception $exception) {
            report($exception);
        }

        return Result::make()->ok();
    }
}