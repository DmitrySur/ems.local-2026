<?php

namespace App\Filament\Resources\Directories\DispatchAreaResource\Pages;

use App\Filament\Resources\Directories\DispatchAreaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDispatchAreas extends ListRecords
{
    protected static string $resource = DispatchAreaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
