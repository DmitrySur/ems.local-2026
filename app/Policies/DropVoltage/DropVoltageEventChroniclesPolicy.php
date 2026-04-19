<?php

namespace App\Policies\DropVoltage;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DropVoltageEventChroniclesPolicy
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
        return $user->hasPermissionTo('drop_voltage_manage');
    }

    public function update(User $user): bool
    {
        return $user->hasPermissionTo('drop_voltage_manage');
    }

    public function delete(User $user): bool
    {
        return $user->hasPermissionTo('drop_voltage_manage');
    }

    public function restore(User $user): bool
    {
        return $user->hasPermissionTo('drop_voltage_manage');
    }

    public function forceDelete(User $user): bool
    {
        return $user->hasPermissionTo('drop_voltage_manage');
    }
}
