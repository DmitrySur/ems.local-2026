<?php

namespace App\Filament\Clusters\UsersAndPermissionCluster\Resources\CustomUserResource\Pages;

use App\Filament\Clusters\UsersAndPermissionCluster\Resources\CustomUserResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCustomUsers extends ListRecords
{
    protected static string $resource = CustomUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
