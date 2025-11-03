<?php

namespace AidingApp\Ai\Enums;

use Filament\Support\Contracts\HasLabel;

enum AiModelApplicabilityFeature: string implements HasLabel
{
    case StaffAdvisor = 'staff_advisor';

    case CustomAdvisors = 'custom_advisors';

    case IntegratedAdvisor = 'integrated_advisor';

    public function getLabel(): string
    {
        return match ($this) {
            self::StaffAdvisor => 'Organization Copilot',
            self::CustomAdvisors => 'Custom Copilots',
            self::IntegratedAdvisor => 'Integrated Advisor',
        };
    }

    /**
     * @return array<AiModel>
     */
    public function getModels(): array
    {
        return array_filter(
            AiModel::cases(),
            fn (AiModel $model): bool => in_array($this, $model->getApplicableFeatures()),
        );
    }

    /**
     * @return array<string, string>
     */
    public function getModelsAsSelectOptions(): array
    {
        return array_reduce(
            $this->getModels(),
            function (array $carry, AiModel $model): array {
                $carry[$model->value] = $model->getLabel();

                return $carry;
            },
            initial: [],
        );
    }

    public static function parse(string | self | null $value): ?self
    {
        if ($value instanceof self) {
            return $value;
        }

        return self::tryFrom($value);
    }
}
