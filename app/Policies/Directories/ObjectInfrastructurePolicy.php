<?php

namespace App\Policies\Directories;

use Illuminate\Auth\Access\HandlesAuthorization;

class ObjectInfrastructurePolicy extends MainDirectoryPolicy
{
    use HandlesAuthorization;
}
