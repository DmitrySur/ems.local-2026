<?php

namespace App\Filament\Clusters\UsersAndPermissionCluster\Resources\CustomUserResource\Pages;

use App\Filament\Clusters\UsersAndPermissionCluster\Resources\CustomUserResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCustomUser extends EditRecord
{
    protected static string $resource = CustomUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
