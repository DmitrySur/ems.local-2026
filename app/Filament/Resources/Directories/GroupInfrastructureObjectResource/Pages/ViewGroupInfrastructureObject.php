<?php

namespace App\Filament\Resources\Directories\GroupInfrastructureObjectResource\Pages;

use App\Filament\Resources\Directories\GroupInfrastructureObjectResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewGroupInfrastructureObject extends ViewRecord
{
    protected static string $resource = GroupInfrastructureObjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
