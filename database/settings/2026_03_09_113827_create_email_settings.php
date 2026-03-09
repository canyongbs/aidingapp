<?php

use Google\Service\Calendar\Setting;
use Illuminate\Database\Migrations\Migration;
use Spatie\LaravelSettings\Exceptions\SettingAlreadyExists;
use Spatie\LaravelSettings\Migrations\SettingsMigration;
use Tpetry\PostgresqlEnhanced\Schema\Blueprint;
use Tpetry\PostgresqlEnhanced\Support\Facades\Schema;

return new class extends SettingsMigration
{
    public function up(): void
    {
        try{
            $this->migrator->add('email.header_logo');
        }catch(SettingAlreadyExists $exception){
            // Setting already exists, do nothing
        }

        try{
            $this->migrator->add('email.background_color');
        }catch(SettingAlreadyExists $exception){
            // Setting already exists, do nothing
        }

        try{
            $this->migrator->add('email.h1_text_color');
        }catch(SettingAlreadyExists $exception){
            // Setting already exists, do nothing
        }

        try{
            $this->migrator->add('email.h2_text_color');
        }catch(SettingAlreadyExists $exception){
            // Setting already exists, do nothing
        }

        try{
            $this->migrator->add('email.paragraph_text_color');
        }catch(SettingAlreadyExists $exception){
            // Setting already exists, do nothing
        }

        try{
            $this->migrator->add('email.footer');
        }catch(SettingAlreadyExists $exception){
            // Setting already exists, do nothing
        }

        
    }

    public function down(): void
    {
        $this->migrator->delete('email.header_logo');
        $this->migrator->delete('email.background_color');
        $this->migrator->delete('email.h1_text_color');
        $this->migrator->delete('email.h2_text_color');
        $this->migrator->delete('email.paragraph_text_color');
        $this->migrator->delete('email.footer');
    }
};
