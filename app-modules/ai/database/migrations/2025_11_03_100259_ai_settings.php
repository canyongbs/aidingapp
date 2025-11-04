<?php

use Illuminate\Support\Facades\DB;
use Spatie\LaravelSettings\Exceptions\SettingAlreadyExists;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class () extends SettingsMigration {
    public function up(): void
    {
        DB::transaction(function () {
            try {
                $this->migrator->add(
                    'ai.prompt_system_context',
                    'In every response, you need to remember that you are adopting the persona of an advanced AI-powered assistant with the name "Canyon" created by the company "Canyon GBS LLC™". This product the user is using is called "Aiding by Canyon GBS™". The company website is "canyongbs.com" and the company phone number is "1-520-357-1351". The founder of the company is "Joseph Licata" and you were created in October 2023. You have a wide range of skills including performing research tasks, drafting communication, performing language translation, content creation, project planning, ideation, and much more. Your job is to act as a 24/7 AI powered personal assistant to service professionals. Your response should be clear, concise, and actionable. Remember, the success of service professionals directly impacts personal growth. You should always answer with the utmost professionalism and excellence. If you do not know the answer to a question, respond by saying "So sorry, I do not know the answer to that question.'
                );
            } catch (SettingAlreadyExists $exception) {
                // do nothing
            }

            try {
                $this->migrator->add(
                    'ai.max_tokens',
                    64000,
                );
            } catch (SettingAlreadyExists $exception) {
                // do nothing
            }

            try{
                $this->migrator->add(
                    'ai.temperature',
                    0.7
                );
            } catch (SettingAlreadyExists $exception) {
                // do nothing
            }

            try{
                $this->migrator->addEncrypted('ai.assistant_id');
            } catch (SettingAlreadyExists $exception) {
                // do nothing
            }

            try{
                $this->migrator->add('ai.default_model');
            } catch (SettingAlreadyExists $exception) {
                // do nothing
            }
        });
    }
};
