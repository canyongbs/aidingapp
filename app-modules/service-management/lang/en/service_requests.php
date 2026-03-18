<?php

/*
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
      of the licensor in the software. Any use of the licensor's trademarks is subject
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
*/

return [
    'feedback' => [
        // Shown when feedback management is enabled but feedback collection is disabled for the service request type.
        'type_feedback_disabled' => 'Feedback has not been enabled for this Service Request Type.',

        // Shown when the service request has not yet reached a closed classification status.
        'not_closed' => "Since this service request is still not closed, we haven't sent out customer surveys yet. As a result, we're currently unable to report on customer feedback for this service request.",

        // Shown when the service request is closed but no feedback survey has been sent (survey_sent_at is null).
        'no_survey_sent' => 'No feedback survey was sent for this closed request.',

        // Shown when the service request is closed, a survey was sent, but no feedback record exists yet.
        // :sent_at — formatted datetime of survey_sent_at in the user's timezone.
        'survey_sent' => 'Feedback survey was sent at :sent_at. Waiting on reply...',

        // Appended on a new line to the survey_sent message when a reminder was also sent (reminder_sent_at is not null).
        // :reminder_at — formatted datetime of reminder_sent_at in the user's timezone.
        'reminder_sent' => 'Feedback survey reminder sent at :reminder_at.',
    ],
];
