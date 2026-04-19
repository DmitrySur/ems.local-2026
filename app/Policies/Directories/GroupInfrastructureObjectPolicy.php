<?php

namespace App\Policies\Directories;

use Illuminate\Auth\Access\HandlesAuthorization;

class GroupInfrastructureObjectPolicy extends MainDirectoryPolicy
{
    use HandlesAuthorization;
}
