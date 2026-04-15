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

namespace AidingApp\ServiceManagement\Filament\Actions;

use Closure;
use Filament\Actions\AssociateAction;
use Filament\Forms\Components\TableSelect;
use Filament\Resources\RelationManagers\RelationManager;

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

        $livewire = $table->getLivewire();
        assert($livewire instanceof RelationManager);
        $relationshipName = $livewire::getRelationshipName();

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
