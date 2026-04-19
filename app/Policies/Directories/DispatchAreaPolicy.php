<?php

namespace App\Policies\Directories;

use Illuminate\Auth\Access\HandlesAuthorization;

class DispatchAreaPolicy extends MainDirectoryPolicy
{
    use HandlesAuthorization;
}
