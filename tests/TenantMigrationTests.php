<?php

use App\Enums\Locality;
use App\Models\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

test('2025_09_26_183417_tmp_data_backfill_service_request_update_created_by', function () {
    isolatedMigration(
        '2025_09_26_183417_tmp_data_backfill_service_request_update_created_by',
        function () {
            // $locality = fake()->randomElement(Locality::cases());

            // assert($locality instanceof Locality);

            // $client = Client::factory()->createQuietly(['state' => $locality->getLabel()]);

            $migrate = Artisan::call('migrate', ['--path' => 'app-modules/service-management/database/migrations/2025_09_26_183417_tmp_data_backfill_service_request_update_created_by.php']);

            expect($migrate)->toBe(Command::SUCCESS);

            // expect($client->refresh()->state)->toBe($locality->value);
        }
    );
});
