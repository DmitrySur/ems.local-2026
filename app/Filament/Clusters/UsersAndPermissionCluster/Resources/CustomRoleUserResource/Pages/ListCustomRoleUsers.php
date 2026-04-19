<?php

namespace App\Filament\Clusters\UsersAndPermissionCluster\Resources\CustomRoleUserResource\Pages;

use App\Filament\Clusters\UsersAndPermissionCluster;
use App\Filament\Clusters\UsersAndPermissionCluster\Resources\CustomRoleUserResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCustomRoleUsers extends ListRecords
{
    protected static string $resource = CustomRoleUserResource::class;
    protected static ?string $cluster = UsersAndPermissionCluster::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
