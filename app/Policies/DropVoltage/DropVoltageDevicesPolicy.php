<?php

namespace App\Policies\DropVoltage;

use App\Models\DropVoltage\DropVoltageDevices;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DropVoltageDevicesPolicy
{
    use HandlesAuthorization;

    public function before(User $user): bool|null
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, DropVoltageDevices $dropVoltageDevices): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('drop_voltage_manage');
    }

    public function update(User $user, DropVoltageDevices $dropVoltageDevices): bool
    {
        return $user->hasPermissionTo('drop_voltage_manage');
    }

    public function delete(User $user, DropVoltageDevices $dropVoltageDevices): bool
    {
        return $user->hasPermissionTo('drop_voltage_manage');
    }

    public function restore(User $user, DropVoltageDevices $dropVoltageDevices): bool
    {
        return $user->hasPermissionTo('drop_voltage_manage');
    }

    public function forceDelete(User $user, DropVoltageDevices $dropVoltageDevices): bool
    {
        return $user->hasPermissionTo('drop_voltage_manage');
    }
}
