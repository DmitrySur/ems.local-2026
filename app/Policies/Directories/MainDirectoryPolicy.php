<?php

namespace App\Policies\Directories;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MainDirectoryPolicy
{
    use HandlesAuthorization;

    public function before(User $user): bool|null
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        return null;
    }

    public function viewAny(): bool
    {
        return true;
    }

    public function view(): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('moderate_directories');
    }

    public function update(User $user): bool
    {
        return $user->hasRole('moderate_directories');
    }

    public function delete(User $user): bool
    {
        return $user->hasRole('moderate_directories');
    }

    public function restore(User $user): bool
    {
        return $user->hasRole('moderate_directories');
    }

    public function forceDelete(User $user): bool
    {
        return $user->hasRole('moderate_directories');
    }
}
