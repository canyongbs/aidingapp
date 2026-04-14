<?php

namespace AidingApp\ServiceManagement\Filament\Actions;

use Closure;
use Filament\Actions\AssociateAction;
use Filament\Forms\Components\TableSelect;

class TableSelectAssociateAction extends AssociateAction
{
    protected string | Closure | null $tableSelectConfiguration = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->schema(function (): array {
            if (filled($this->getTableSelectConfiguration())) {
                return [$this->getTableRecordSelect()];
            }

            return [$this->getRecordSelect()];
        });
    }

    public function tableSelect(string | Closure | null $configuration): static
    {
        $this->tableSelectConfiguration = $configuration;

        return $this;
    }

    public function getTableSelectConfiguration(): ?string
    {
        return $this->evaluate($this->tableSelectConfiguration);
    }

    protected function getTableRecordSelect(): TableSelect
    {
        $table = $this->getTable();
        $relationship = $table->getRelationship();
        $relationshipName = $table->getLivewire()::getRelationshipName();

        return TableSelect::make('recordId')
            ->label(__('filament-actions::associate.single.modal.fields.record_id.label'))
            ->hiddenLabel()
            ->ignoreRelatedRecords()
            ->tableConfiguration($this->getTableSelectConfiguration())
            ->model($relationship->getParent())
            ->relationshipName($relationshipName)
            ->multiple($this->isMultiple());
    }
}
