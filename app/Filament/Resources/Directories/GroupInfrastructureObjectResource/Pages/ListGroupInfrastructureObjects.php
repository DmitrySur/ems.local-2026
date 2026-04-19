<?php

namespace App\Filament\Resources\Directories\GroupInfrastructureObjectResource\Pages;

use App\Filament\Resources\Directories\GroupInfrastructureObjectResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGroupInfrastructureObjects extends ListRecords
{
    protected static string $resource = GroupInfrastructureObjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
