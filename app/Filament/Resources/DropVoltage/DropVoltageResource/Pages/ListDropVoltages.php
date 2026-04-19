<?php

namespace App\Filament\Resources\DropVoltage\DropVoltageResource\Pages;

use App\Filament\Resources\DropVoltage\DropVoltageResource;
use App\Models\DropVoltage\DropVoltage;
use Auth;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Contracts\Support\Htmlable;

class ListDropVoltages extends ListRecords
{
    protected static string $resource = DropVoltageResource::class;

    public function getTitle(): string|Htmlable
    {
        return 'Посадки напряжения';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->modalWidth(MaxWidth::SevenExtraLarge),
        ];
    }

    public function getTabs(): array
    {
        $arrayGroupInfrastructureObjectsByUserDispatchArea = Auth::user()?->dispatchArea?->groupInfrastructureObjects?->pluck('id')?->toArray();
        return [
            'all_open' => Tab::make('Все открытые')
                ->modifyQueryUsing(function () use ($arrayGroupInfrastructureObjectsByUserDispatchArea) {
                    return DropVoltage::query()->getAllOpenedDropVoltage($arrayGroupInfrastructureObjectsByUserDispatchArea);
                }),
            'all_opened_per_day' => Tab::make('Все открытые за сутки')
                ->modifyQueryUsing(function () use ($arrayGroupInfrastructureObjectsByUserDispatchArea) {
                    return DropVoltage::query()->getAllOpenedDropVoltagePerDay($arrayGroupInfrastructureObjectsByUserDispatchArea);
                }),
            'all_closed' => Tab::make('Все закрытые')
                ->modifyQueryUsing(function () use ($arrayGroupInfrastructureObjectsByUserDispatchArea) {
                    return DropVoltage::query()->getAllClosedDropVoltage($arrayGroupInfrastructureObjectsByUserDispatchArea);
                }),
            'all_closed_per_day' => Tab::make('Все закрытые за сутки')
                ->modifyQueryUsing(function () use ($arrayGroupInfrastructureObjectsByUserDispatchArea) {
                    return DropVoltage::query()->getAllClosedDropVoltagePerDay($arrayGroupInfrastructureObjectsByUserDispatchArea);
                }),
        ];
    }
}
