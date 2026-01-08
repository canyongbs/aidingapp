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

namespace AidingApp\Ai\Tools\PortalAssistant;

use AidingApp\Portal\Actions\GenerateServiceRequestForm;
use AidingApp\ServiceManagement\Actions\ResolveUploadsMediaCollectionForServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestFormField;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use Prism\Prism\Tool;

class GetServiceRequestFormTool extends Tool
{
    public function __construct()
    {
        $this
            ->as('get_service_request_form')
            ->for('Retrieves the form fields for a service request type. Use this after the user selects a type to know what information to collect.')
            ->withStringParameter('type_id', 'The UUID of the service request type')
            ->using($this);
    }

    public function __invoke(string $type_id): string
    {
        $type = ServiceRequestType::with('category')->find($type_id);

        if (! $type) {
            return json_encode([
                'error' => true,
                'message' => 'Service request type not found.',
            ]);
        }

        $uploadsMediaCollection = app(ResolveUploadsMediaCollectionForServiceRequest::class)();
        $form = app(GenerateServiceRequestForm::class)->execute($type, $uploadsMediaCollection);

        $categoryPath = $this->buildCategoryPath($type);

        $fields = [];

        foreach ($form->steps as $step) {
            foreach ($step->fields as $field) {
                $fields[] = $this->formatField($field, $step->label);
            }
        }

        $priorities = $type->priorities()
            ->orderByDesc('order')
            ->get(['id', 'name'])
            ->map(fn ($p) => ['id' => $p->id, 'name' => $p->name])
            ->values()
            ->all();

        return json_encode([
            'type_id' => $type->getKey(),
            'type_name' => $type->name,
            'category_path' => $categoryPath,
            'priorities' => $priorities,
            'fields' => $fields,
        ]);
    }

    protected function buildCategoryPath(ServiceRequestType $type): string
    {
        if (! $type->category) {
            return '';
        }

        $path = collect([$type->category->name]);
        $current = $type->category;

        while ($current->parent) {
            $current = $current->parent;
            $path->prepend($current->name);
        }

        return $path->implode(' > ');
    }

    /**
     * @return array<string, mixed>
     */
    protected function formatField(ServiceRequestFormField $field, string $stepLabel): array
    {
        $data = [
            'field_id' => $field->getKey(),
            'step' => $stepLabel,
            'label' => $field->label,
            'type' => $field->type,
            'required' => (bool) $field->is_required,
        ];

        if (isset($field->config['options'])) {
            $data['options'] = $field->config['options'];
        }

        if (isset($field->config['placeholder'])) {
            $data['placeholder'] = $field->config['placeholder'];
        }

        return $data;
    }
}
