<?php

namespace App\Filament\Actions;

use Exception;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;

class ArchiveAction
{
    public static function make(): Action
    {
        return Action::make('archive')
            ->label('Archive')
            ->modalHeading(fn (Action $action): string => "Archive {$action->getRecordTitle()}")
            ->modalSubmitActionLabel('Archive')
            ->successNotificationTitle('Archived')
            ->defaultColor('warning')
            ->groupedIcon('heroicon-m-archive-box')
            ->modalIcon('heroicon-o-archive-box')
            ->hidden(static function (Model $record): bool {
                if (! method_exists($record, 'isArchived')) {
                    throw new Exception('The [ArchiveAction] requires the model to use the [CanBeArchived] trait.');
                }

                return $record->isArchived();
            })
            ->requiresConfirmation()
            ->action(function (Action $action, Model $record): void {
                if (! method_exists($record, 'archive')) {
                    throw new Exception('The [ArchiveAction] requires the model to use the [CanBeArchived] trait.');
                }

                $result = $record->archive();

                if (! $result) {
                    $action->failure();

                    return;
                }

                $action->success();
            })
            ->authorize(function (Model $record, Component $livewire): bool {
                if ((! $livewire instanceof EditRecord) && (! $livewire instanceof ViewRecord)) {
                    throw new Exception('Unsupported Livewire component for [ArchiveAction] authorization. It must be used within [EditRecord] or [ViewRecord], or a custom [authorize()] function must be used.');
                }

                return $livewire::getResource()::can('archive', $record);
            })
            ->successRedirectUrl(function (Component $livewire): string {
                if ((! $livewire instanceof EditRecord) && (! $livewire instanceof ViewRecord)) {
                    throw new Exception('Unsupported Livewire component for [ArchiveAction] redirect. It must be used within [EditRecord] or [ViewRecord], or a custom [successRedirectUrl()] function must be used.');
                }

                return $livewire::getResource()::getUrl('index');
            });
    }
}
