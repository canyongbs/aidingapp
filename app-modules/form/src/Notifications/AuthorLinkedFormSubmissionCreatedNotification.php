<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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
*/

namespace AdvisingApp\Form\Notifications;

use Illuminate\Support\HtmlString;
use AdvisingApp\Contact\Models\Contact;
use AdvisingApp\Form\Models\FormSubmission;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\Notification\Notifications\BaseNotification;
use AdvisingApp\Contact\Filament\Resources\ContactResource;
use AdvisingApp\Notification\Notifications\DatabaseNotification;
use Filament\Notifications\Notification as FilamentNotification;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource;
use AdvisingApp\Notification\Notifications\Concerns\DatabaseChannelTrait;

class AuthorLinkedFormSubmissionCreatedNotification extends BaseNotification implements DatabaseNotification
{
    use DatabaseChannelTrait;

    public function __construct(public FormSubmission $submission) {}

    public function toDatabase(object $notifiable): array
    {
        $author = $this->submission->author;

        $name = $author->{$author->displayNameKey()};

        $target = match ($author::class) {
            Contact::class => ContactResource::class,
            Student::class => StudentResource::class,
        };

        $formSubmissionUrl = $target::getUrl('manage-form-submissions', ['record' => $author]);

        $formSubmissionLink = new HtmlString("<a href='{$formSubmissionUrl}' target='_blank' class='underline'>form submission</a>");

        $morph = str($author->getMorphClass());

        $morphUrl = $target::getUrl('view', ['record' => $author]);

        $morphLink = new HtmlString("<a href='{$morphUrl}' target='_blank' class='underline'>{$name}</a>");

        return FilamentNotification::make()
            ->warning()
            ->title("A {$formSubmissionLink} has been submitted by {$morph} {$morphLink}")
            ->getDatabaseMessage();
    }
}
