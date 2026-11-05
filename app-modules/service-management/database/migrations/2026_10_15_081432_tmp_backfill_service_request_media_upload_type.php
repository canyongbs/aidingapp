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

use App\Features\ServiceRequestFileUploadTypeFeature;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    public function up(): void
    {
        DB::transaction(function (): void {
            // Files attached to a service request update are always an "Update", so only files attached directly
            // to a service request are labelled. Staff could never attach files while creating a service request,
            // so a file uploaded by a contact was a submission and a file uploaded by a user was a staff upload.
            // Files from before uploaders were recorded fall back to how soon after the request they were added.
            // A non-object value (`[]`) is replaced with `{}`, since merging into a jsonb array would append to it.
            DB::statement(<<<'SQL'
                UPDATE media
                SET custom_properties = (
                    CASE WHEN jsonb_typeof(media.custom_properties) = 'object' THEN media.custom_properties ELSE '{}'::jsonb END
                ) || jsonb_build_object('upload_type', CASE
                    WHEN media.created_by_type = 'contact' THEN 'request_submission'
                    WHEN media.created_by_type = 'user' THEN 'staff_upload'
                    WHEN media.created_at <= service_requests.created_at + INTERVAL '10 minutes' THEN 'request_submission'
                    ELSE 'staff_upload'
                END)
                FROM service_requests
                WHERE service_requests.id = media.model_id
                    AND media.model_type = 'service_request'
                    AND media.collection_name = 'uploads'
                    AND media.custom_properties -> 'upload_type' IS NULL
                SQL);

            ServiceRequestFileUploadTypeFeature::activate();
        });
    }

    public function down(): void
    {
        DB::transaction(function (): void {
            ServiceRequestFileUploadTypeFeature::deactivate();

            // The backfilled upload types are indistinguishable from those recorded at upload time, so they are kept
        });
    }
};
