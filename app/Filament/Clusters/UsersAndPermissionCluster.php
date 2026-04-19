<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;

class UsersAndPermissionCluster extends Cluster
{
    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';
    protected static ?string $clusterBreadcrumb = 'Пользователи, группы и права доступа';
    protected static ?string $navigationLabel = 'Пользователи';
}
