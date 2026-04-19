<?php

namespace App\Filament\Resources\Incident\IncidentResource\Pages;

use App\Filament\Resources\Incident\IncidentResource;
use App\Models\Incident\Incident;
use Auth;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\MaxWidth;

class ListIncidents extends ListRecords
{
    protected static string $resource = IncidentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->modalWidth(MaxWidth::SevenExtraLarge)
                ->stickyModalFooter(),
        ];
    }

    public function getTabs(): array
    {
        $userDispatchAreasId = Auth::user()?->dispatch_area_id;
        return [
            'all_in_repair' => Tab::make('Все не устраненные')
                ->modifyQueryUsing(function () use ($userDispatchAreasId) {
                    return Incident::query()->allInRepairWithDispatchAreas($userDispatchAreasId)->WithRelationForIncidentListPage();
                })
                ->badge(function () use ($userDispatchAreasId) {
                    return Incident::query()->allInRepairWithDispatchAreas($userDispatchAreasId)->count();
                })
                ->badgeColor('danger'),
            'all_in_repair_per_day' => Tab::make('Все не устраненные за сутки')
                ->modifyQueryUsing(function () use ($userDispatchAreasId) {
                    return Incident::query()->allInRepairWithDispatchAreasPerDay($userDispatchAreasId)->WithRelationForIncidentListPage();
                })->badge(function () use ($userDispatchAreasId) {
                    return Incident::query()->allInRepairWithDispatchAreasPerDay($userDispatchAreasId)->count();
                })
                ->badgeColor('danger'),
            'all_per_day' => Tab::make('Все за сутки')
                ->modifyQueryUsing(function () use ($userDispatchAreasId) {
                    return Incident::query()->allPerDay($userDispatchAreasId)->WithRelationForIncidentListPage();
                }),
            'all' => Tab::make('Все')
                ->modifyQueryUsing(function () use ($userDispatchAreasId) {
                    return Incident::query()->allIncidents($userDispatchAreasId)->WithRelationForIncidentListPage();
                })
        ];
    }


}
