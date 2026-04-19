<?php

namespace App\Filament\Clusters\UsersAndPermissionCluster\Resources\CustomRoleUserResource\Pages;

use App\Filament\Clusters\UsersAndPermissionCluster;
use App\Filament\Clusters\UsersAndPermissionCluster\Resources\CustomRoleUserResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCustomRoleUser extends EditRecord
{
    protected static string $resource = CustomRoleUserResource::class;
    protected static ?string $cluster = UsersAndPermissionCluster::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
