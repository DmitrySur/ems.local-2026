<?php

namespace App\Filament\Resources\DropVoltage\DropVoltageResource\Pages;

use App\Enum\DropVoltageStatuses;
use App\Filament\Resources\DropVoltage\DropVoltageResource;
use Filament\Actions;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Contracts\Support\Htmlable;

class ViewDropVoltage extends ViewRecord
{
    protected static string $resource = DropVoltageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->modalWidth(MaxWidth::SevenExtraLarge),
        ];
    }

    public function getTitle(): string|Htmlable
    {
        return 'Просмотр посадки № ' . $this->record['id'] ?? '';
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make()
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('datetime_drop')
                                    ->dateTime('d F Y H:i')
                                    ->size(TextEntry\TextEntrySize::Large)
                                    ->label('Дата и время посадки'),
                                TextEntry::make('groupInfrastructureObject.title')
                                    ->label('Группа объектов инфраструктуры')
                                    ->size(TextEntry\TextEntrySize::Large),
                                TextEntry::make('status_drop')
                                    ->label('Статус посадки')
                                    ->icon(fn(string $state): string => match ($state) {
                                        DropVoltageStatuses::Opened->value => 'heroicon-o-x-mark',
                                        DropVoltageStatuses::Closed->value => 'heroicon-o-check-badge',
                                    })
                                    ->color(fn(string $state): string => match ($state) {
                                        DropVoltageStatuses::Opened->value => 'danger',
                                        DropVoltageStatuses::Closed->value => 'success',
                                    })
                                    ->formatStateUsing(function (string $state) {
                                        return DropVoltageStatuses::tryFrom($state)?->getLabel() ?? '-';
                                    })
                                    ->size(TextEntry\TextEntrySize::Large)
                                    ->badge()
                            ]),
                        Grid::make()
                            ->schema([
                                TextEntry::make('detail_location')
                                    ->label('Участок посадки')
                                    ->size(TextEntry\TextEntrySize::Large),
                                TextEntry::make('detail_drop')
                                    ->label('Описание посадки')
                                    ->size(TextEntry\TextEntrySize::Large)
                            ])
                    ])
            ]);
    }
}
