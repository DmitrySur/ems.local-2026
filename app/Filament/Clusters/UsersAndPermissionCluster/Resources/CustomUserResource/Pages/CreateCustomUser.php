<?php

namespace App\Filament\Clusters\UsersAndPermissionCluster\Resources\CustomUserResource\Pages;

use App\Filament\Clusters\UsersAndPermissionCluster\Resources\CustomUserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCustomUser extends CreateRecord
{
    protected static string $resource = CustomUserResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
