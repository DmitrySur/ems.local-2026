<?php

namespace App\Filament\Resources\Directories\IncidentTypeResource\Pages;

use App\Filament\Resources\Directories\IncidentTypeResource;
use App\Models\Directories\IncidentType;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\MaxWidth;

class ListIncidentTypes extends ListRecords
{
    protected static string $resource = IncidentTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->modalWidth(MaxWidth::SevenExtraLarge)
                ->successRedirectUrl(fn(IncidentType $record
                ): string => ViewIncidentTypes::getUrl(['record' => $record]))
        ];
    }


}
