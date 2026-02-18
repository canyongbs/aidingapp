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

namespace AidingApp\ServiceManagement\Http\Controllers;

use AidingApp\Form\Actions\GenerateSubmissibleValidation;
use AidingApp\Form\Actions\ResolveSubmissionAuthorFromEmail;
use AidingApp\Form\Filament\Blocks\EducatableEmailFormFieldBlock;
use AidingApp\Form\Notifications\AuthenticateFormNotification;
use AidingApp\IntegrationGoogleRecaptcha\Settings\GoogleRecaptchaSettings;
use AidingApp\ServiceManagement\Actions\GenerateServiceRequestFormKitSchema;
use AidingApp\ServiceManagement\Models\ServiceRequestForm;
use AidingApp\ServiceManagement\Models\ServiceRequestFormAuthentication;
use AidingApp\ServiceManagement\Models\ServiceRequestFormSubmission;
use App\Http\Controllers\Controller;
use Closure;
use Filament\Support\Colors\Color;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ServiceRequestFormWidgetController extends Controller
{
    public function assets(Request $request, ServiceRequestForm $serviceRequestForm): JsonResponse
    {
        // Read the Vite manifest to determine the correct asset paths
        $manifestPath = public_path('storage/widgets/service-requests/forms/.vite/manifest.json');
        /** @var array<string, array{file: string, name: string, src: string, isEntry: bool}> $manifest */
        $manifest = json_decode(File::get($manifestPath), true, 512, JSON_THROW_ON_ERROR);

        $widgetEntry = $manifest['src/widget.js'];

        return response()->json([
            'asset_url' => route('widgets.service-requests.forms.asset'),
            'entry' => route('widgets.service-requests.forms.api.entry', ['serviceRequestForm' => $serviceRequestForm]),
            'js' => route('widgets.service-requests.forms.asset', ['file' => $widgetEntry['file']]),
        ]);
    }

    public function asset(Request $request, string $file): StreamedResponse
    {
        $path = "widgets/service-requests/forms/{$file}";

        $disk = Storage::disk('public');

        abort_if(! $disk->exists($path), 404, 'File not found.');

        $mimeType = $disk->mimeType($path);

        $stream = $disk->readStream($path);

        abort_if(is_null($stream), 404, 'File not found.');

        return response()->streamDownload(
            function () use ($stream) {
                fpassthru($stream);
                fclose($stream);
            },
            $file,
            ['Content-Type' => $mimeType]
        );
    }

    public function view(GenerateServiceRequestFormKitSchema $generateSchema, ServiceRequestForm $serviceRequestForm): JsonResponse
    {
        return response()->json(
            [
                'name' => $serviceRequestForm->name,
                'description' => $serviceRequestForm->description,
                'is_authenticated' => $serviceRequestForm->is_authenticated,
                ...($serviceRequestForm->is_authenticated ? [
                    'authentication_url' => URL::signedRoute(
                        name: 'widgets.service-requests.forms.api.request-authentication',
                        parameters: ['serviceRequestForm' => $serviceRequestForm],
                    ),
                ] : [
                    'submission_url' => URL::signedRoute(
                        name: 'widgets.service-requests.forms.api.submit',
                        parameters: ['serviceRequestForm' => $serviceRequestForm],
                    ),
                ]),
                'recaptcha_enabled' => $serviceRequestForm->recaptcha_enabled,
                ...($serviceRequestForm->recaptcha_enabled ? [
                    'recaptcha_site_key' => app(GoogleRecaptchaSettings::class)->site_key,
                ] : []),
                'schema' => $generateSchema($serviceRequestForm),
                'primary_color' => collect(Color::all()[$serviceRequestForm->primary_color ?? 'blue'])
                    ->map(Color::convertToRgb(...))
                    ->map(fn (string $value): string => (string) str($value)->after('rgb(')->before(')'))
                    ->all(),
                'rounding' => $serviceRequestForm->rounding,
            ],
        );
    }

    public function requestAuthentication(Request $request, ResolveSubmissionAuthorFromEmail $resolveSubmissionAuthorFromEmail, ServiceRequestForm $serviceRequestForm): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $author = $resolveSubmissionAuthorFromEmail($data['email']);

        if (! $author) {
            throw ValidationException::withMessages([
                'email' => 'A contact with that email address could not be found. Please contact your system administrator.',
            ]);
        }

        $code = random_int(100000, 999999);

        $authentication = new ServiceRequestFormAuthentication();
        $authentication->author()->associate($author);
        $authentication->submissible()->associate($serviceRequestForm);
        $authentication->code = Hash::make((string) $code);
        $authentication->save();

        Notification::route('mail', [
            $data['email'] => $author->getAttributeValue($author::displayNameKey()),
        ])->notify(new AuthenticateFormNotification($authentication, $code));

        return response()->json([
            'message' => "We've sent an authentication code to {$data['email']}.",
            'authentication_url' => URL::signedRoute(
                name: 'widgets.service-requests.forms.api.authenticate',
                parameters: [
                    'serviceRequestForm' => $serviceRequestForm,
                    'authentication' => $authentication,
                ],
            ),
        ]);
    }

    public function authenticate(Request $request, ServiceRequestForm $serviceRequestForm, ServiceRequestFormAuthentication $authentication): JsonResponse
    {
        if ($authentication->isExpired()) {
            return response()->json([
                'is_expired' => true,
            ]);
        }

        $request->validate([
            'code' => ['required', 'integer', 'digits:6', function (string $attribute, int $value, Closure $fail) use ($authentication) {
                if (Hash::check((string) $value, $authentication->code)) {
                    return;
                }

                $fail('The provided code is invalid.');
            }],
        ]);

        return response()->json([
            'submission_url' => URL::signedRoute(
                name: 'widgets.service-requests.forms.api.submit',
                parameters: [
                    'authentication' => $authentication,
                    'serviceRequestForm' => $authentication->submissible,
                ],
            ),
        ]);
    }

    public function store(
        Request $request,
        GenerateSubmissibleValidation $generateValidation,
        ResolveSubmissionAuthorFromEmail $resolveSubmissionAuthorFromEmail,
        ServiceRequestForm $serviceRequestForm,
    ): JsonResponse {
        $authentication = $request->query('authentication');

        if (filled($authentication)) {
            $authentication = ServiceRequestFormAuthentication::findOrFail($authentication);
        }

        if (
            $serviceRequestForm->is_authenticated &&
            ($authentication?->isExpired() ?? true)
        ) {
            abort(Response::HTTP_UNAUTHORIZED);
        }

        $validator = Validator::make(
            $request->all(),
            $generateValidation($serviceRequestForm)
        );

        if ($validator->fails()) {
            return response()->json(
                [
                    'errors' => (object) $validator->errors(),
                ],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        /** @var ?ServiceRequestFormSubmission $submission */
        $submission = $authentication ? $serviceRequestForm->submissions()
            ->requested()
            ->whereMorphedTo('author', $authentication->author)
            ->first() : null;

        $submission ??= $serviceRequestForm->submissions()->make();

        $submission
            ->priority()
            ->associate(
                $serviceRequestForm
                    ->type
                    ->priorities()
                    ->findOrFail(
                        $request->input('priority')
                    )
            );

        if ($authentication) {
            $submission->author()->associate($authentication->author);

            $authentication->delete();
        }

        $submission->submitted_at = now()->toImmutable();

        $submission->save();

        $data = $validator->validated();

        unset($data['recaptcha-token']);

        if ($serviceRequestForm->is_wizard) {
            foreach ($serviceRequestForm->steps as $step) {
                $stepFields = $step->fields()->pluck('type', 'id')->all();

                foreach ($data[$step->label] as $fieldId => $response) {
                    $submission->fields()->attach(
                        $fieldId,
                        ['id' => Str::orderedUuid(), 'response' => $response],
                    );

                    if ($submission->author) {
                        continue;
                    }

                    if ($stepFields[$fieldId] !== EducatableEmailFormFieldBlock::type()) {
                        continue;
                    }

                    $author = $resolveSubmissionAuthorFromEmail($response);

                    if (! $author) {
                        continue;
                    }

                    $submission->author()->associate($author);
                }
            }
        } else {
            $formFields = $serviceRequestForm->fields()->pluck('type', 'id')->all();

            foreach ($data as $fieldId => $response) {
                $submission->fields()->attach(
                    $fieldId,
                    ['id' => Str::orderedUuid(), 'response' => $response],
                );

                if ($submission->author) {
                    continue;
                }

                if ($formFields[$fieldId] !== EducatableEmailFormFieldBlock::type()) {
                    continue;
                }

                $author = $resolveSubmissionAuthorFromEmail($response);

                if (! $author) {
                    continue;
                }

                $submission->author()->associate($author);
            }
        }

        $submission->save();

        return response()->json(
            [
                'message' => 'Service Request Form submitted successfully.',
            ]
        );
    }
}
