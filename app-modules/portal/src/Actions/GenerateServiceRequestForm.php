<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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

namespace AidingApp\Portal\Actions;

use AidingApp\Form\Filament\Blocks\SelectFormFieldBlock;
use AidingApp\Form\Filament\Blocks\TextAreaFormFieldBlock;
use AidingApp\Form\Filament\Blocks\TextInputFormFieldBlock;
use AidingApp\Form\Filament\Blocks\UploadFormFieldBlock;
use AidingApp\ServiceManagement\Models\MediaCollections\UploadsMediaCollection;
use AidingApp\ServiceManagement\Models\ServiceRequestForm;
use AidingApp\ServiceManagement\Models\ServiceRequestFormField;
use AidingApp\ServiceManagement\Models\ServiceRequestFormStep;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use Illuminate\Support\Collection;

class GenerateServiceRequestForm
{
    public function execute(ServiceRequestType $type, UploadsMediaCollection $uploadsMediaCollection): ServiceRequestForm
    {
        $form = $type->form ?? new ServiceRequestForm();

        /** @var array<int, array<string, mixed>> $contentData */
        $contentData = data_get($form, 'content.content', []);
        $content = collect($contentData);

        if ($content->isNotEmpty() && $form->steps->isEmpty()) {
            $form->steps->push($this->formatStep('Details', 0, $content));
        }

        /** @phpstan-ignore assign.propertyType */
        $form->is_wizard = true;

        $content = collect([
            $this->formatBlock('Title', TextInputFormFieldBlock::type()),
            $this->formatBlock('Description', TextAreaFormFieldBlock::type()),
            $this->formatBlock('Priority', SelectFormFieldBlock::type(), data: [
                'options' => $type
                    ->priorities()
                    ->orderByDesc('order')
                    ->pluck('name', 'id'),
                'placeholder' => 'Select a priority',
            ]),
            $this->formatBlock('Upload File', UploadFormFieldBlock::type(), false, [
                'multiple' => $uploadsMediaCollection->getMaxNumberOfFiles() > 1,
                'limit' => $uploadsMediaCollection->getMaxNumberOfFiles(),
                'accept' => $uploadsMediaCollection->getExtensionsFull(),
                'size' => $uploadsMediaCollection->getMaxFileSizeInMB(),
                'uploadUrl' => route('api.portal.service-request.request-upload-url'),
            ]),
        ]);

        $form->steps->prepend($this->formatStep('Main', -1, $content));

        $maxOrder = $form->steps->max('order') ?? 0;
        $form->steps->push($this->formatStep('Questions', $maxOrder + 1, collect([])));

        return $form;
    }

    /**
     * @param Collection<int, array<string, mixed>> $content
     */
    protected function formatStep(string $label, int $order, Collection $content): ServiceRequestFormStep
    {
        $step = new ServiceRequestFormStep([
            'label' => $label,
            'order' => $order,
            'content' => [
                'content' => $content,
                'type' => 'doc',
            ],
        ]);

        $content->each(fn (array $block) => $this->addFieldToStep($step, $block));

        return $step;
    }

    /**
     * @param array<string, mixed> $block
     */
    protected function addFieldToStep(ServiceRequestFormStep $step, array $block): void
    {
        /** @var array<string, mixed> $attrs */
        $attrs = $block['attrs'];
        $attributes = collect($attrs);

        $step->fields->push(
            (new ServiceRequestFormField())
                ->forceFill([
                    'id' => $attributes->pull('id'),
                    'type' => $attributes->pull('type'),
                    'label' => $attributes->pull('data.label'),
                    'is_required' => $attributes->pull('data.isRequired'),
                    'config' => $attributes->pull('data'),
                ])
        );
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>
     */
    protected function formatBlock(string $label, string $type, bool $required = true, array $data = []): array
    {
        return [
            'type' => 'tiptapBlock',
            'attrs' => [
                'id' => str($label)->slug()->toString(),
                'type' => $type,
                'data' => [
                    'label' => $label,
                    'isRequired' => $required,
                    ...$data,
                ],
            ],
        ];
    }
}
