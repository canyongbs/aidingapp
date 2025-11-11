<?php

namespace AidingApp\Portal\Actions;

use AidingApp\Contact\Models\Contact;
use AidingApp\ServiceManagement\Models\ServiceRequestFormField;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use Illuminate\Support\Collection;

class GenerateServiceRequestQuestionsAiPrompt
{
    /**
     * @param array<string, mixed> $formData
     */
    public function __invoke(ServiceRequestType $serviceRequestType, array $formData, Contact $contact): string
    {
        $requesterName = $contact->full_name ?? $contact->name ?? 'the user';

        $fieldsMap = $this->buildFieldsMapFromType($serviceRequestType);

        $missingDetailKeys = $this->findMissingDetailKeys($formData, $fieldsMap);

        if (! empty($missingDetailKeys)) {
            $databaseLabels = $this->fetchMissingFieldLabels($missingDetailKeys);

            foreach ($databaseLabels as $id => $label) {
                $fieldsMap[$id] = $label;
            }
        }

        $formDataMarkdown = $this->renderFormDataMarkdown($formData, $fieldsMap);

        $prompt = "Review the support ticket submitted by {$requesterName} for the service request type {$serviceRequestType->name}.";

        if (! empty($serviceRequestType->description)) {
            $prompt .= " The service request type description is: {$serviceRequestType->description}.";
        }

        $prompt .= "\n\nPlease review the submitted form data (below) and, imagining you are the support technician, come up with three clarifying questions that will help you collect the information needed to adequately service this ticket. Be concise and focused on actionable, specific information to resolve the request.\n\n";

        $prompt .= $formDataMarkdown;

        $prompt .= "\nExample: if the request is to replace a light bulb, ask which building and room the issue is located in.\n";

        return $prompt;
    }

    /**
     * @return Collection<int, string>
     */
    protected function buildFieldsMapFromType(ServiceRequestType $serviceRequestType): Collection
    {
        $map = collect();

        if (! $serviceRequestType->form) {
            return $map;
        }

        foreach ($serviceRequestType->form->steps ?? [] as $step) {
            foreach ($step->fields ?? [] as $field) {
                $map[$field->getKey()] = $field->label;
            }
        }

        return $map;
    }

    /**
     * @param array<string, mixed> $formData
     * @param Collection<int, string> $fieldsMap
     *
     * @return array<int, string>
     */
    protected function findMissingDetailKeys(array $formData, Collection $fieldsMap): array
    {
        /** @var array<string, mixed> */
        $details = $formData['Details'] ?? [];

        $detailKeys = collect($details)->keys()->filter()->values();

        return $detailKeys->filter(fn ($key) => ! $fieldsMap->has($key))->all();
    }

    /**
     * @param array<int, string> $ids
     *
     * @return array<int, string>
     */
    protected function fetchMissingFieldLabels(array $ids): array
    {
        return ServiceRequestFormField::query()
            ->whereIn('id', $ids)
            ->pluck('label', 'id')
            ->all();
    }

    /**
     * @param array<string, mixed> $formData
     * @param Collection<int, string> $fieldsMap
     */
    protected function renderFormDataMarkdown(array $formData, Collection $fieldsMap): string
    {
        $markdown = "## Submitted form data\n\n";

        foreach ($formData as $section => $values) {
            $markdown .= "### {$section}\n\n";

            if (is_array($values)) {
                foreach ($values as $key => $value) {
                    $label = $fieldsMap[$key] ?? $key;

                    if (is_array($value) || is_object($value)) {
                        $valueString = json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                    } elseif ($value === null || $value === '') {
                        $valueString = '—';
                    } else {
                        $valueString = (string) $value;
                    }

                    $markdown .= "- **{$label}**: {$valueString}\n";
                }
            } else {
                $markdown .= '- ' . json_encode($values) . "\n";
            }

            $markdown .= "\n";
        }

        return $markdown;
    }
}
