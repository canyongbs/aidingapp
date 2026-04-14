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

use Google\Service\Calendar\Setting;
use Spatie\LaravelSettings\Exceptions\SettingAlreadyExists;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class () extends SettingsMigration {
    public function up(): void
    {
        try {
            $this->migrator->add('email.header_logo');
        } catch (SettingAlreadyExists $exception) {
            // Setting already exists, do nothing
        }

        try {
            $this->migrator->add('email.background_color');
        } catch (SettingAlreadyExists $exception) {
            // Setting already exists, do nothing
        }

        try {
            $this->migrator->add('email.h1_text_color');
        } catch (SettingAlreadyExists $exception) {
            // Setting already exists, do nothing
        }

        try {
            $this->migrator->add('email.h2_text_color');
        } catch (SettingAlreadyExists $exception) {
            // Setting already exists, do nothing
        }

        try {
            $this->migrator->add('email.paragraph_text_color');
        } catch (SettingAlreadyExists $exception) {
            // Setting already exists, do nothing
        }

        try {
            $this->migrator->add('email.footer');
        } catch (SettingAlreadyExists $exception) {
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
