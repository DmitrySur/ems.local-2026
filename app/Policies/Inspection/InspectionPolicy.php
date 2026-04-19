<?php

namespace App\Policies\Inspection;

use App\Models\Inspection\Inspection;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class InspectionPolicy
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
        return $user->hasPermissionTo('inspections_management');
    }

    public function update(User $user): bool
    {
        return $user->hasPermissionTo('inspections_management');
    }

    public function delete(User $user, Inspection $inspection): bool
    {
        if ($user->hasPermissionTo('inspections_management') &&
            $inspection->created_by === $user->id) {
            return true;
        }
        if ($user->hasPermissionTo('inspections_all_delete')) {
            return true;
        }
        return false;
    }

    public function restore(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function forceDelete(User $user): bool
    {
        return $user->hasRole('admin');
    }
}
