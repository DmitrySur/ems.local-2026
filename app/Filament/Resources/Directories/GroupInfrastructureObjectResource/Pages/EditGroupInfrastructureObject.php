<?php

namespace App\Filament\Resources\Directories\GroupInfrastructureObjectResource\Pages;

use App\Filament\Resources\Directories\GroupInfrastructureObjectResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGroupInfrastructureObject extends EditRecord
{
    protected static string $resource = GroupInfrastructureObjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
