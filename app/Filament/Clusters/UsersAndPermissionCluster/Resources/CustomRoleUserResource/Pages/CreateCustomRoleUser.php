<?php

namespace App\Filament\Clusters\UsersAndPermissionCluster\Resources\CustomRoleUserResource\Pages;

use App\Filament\Clusters\UsersAndPermissionCluster;
use App\Filament\Clusters\UsersAndPermissionCluster\Resources\CustomRoleUserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCustomRoleUser extends CreateRecord
{
    protected static string $resource = CustomRoleUserResource::class;
    protected static ?string $cluster = UsersAndPermissionCluster::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
