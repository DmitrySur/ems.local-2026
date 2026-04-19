<?php

namespace App\Policies\DropVoltage;

use App\Enum\DropVoltageStatuses;
use App\Models\DropVoltage\DropVoltage;
use App\Models\User;
use Auth;
use Illuminate\Auth\Access\HandlesAuthorization;

class DropVoltagePolicy
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

    public function view(User $user, DropVoltage $dropVoltage): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('drop_voltage_create');
    }

    public function update(User $user, DropVoltage $dropVoltage): bool
    {
        if ($user->hasPermissionTo('drop_voltage_all_edit_with_closed')) {
            return true;
        }
        if ($user->hasPermissionTo('drop_voltage_all_edit') && $dropVoltage->status_drop === DropVoltageStatuses::Opened->value) {
            return true;
        }
        if ($user->hasPermissionTo('drop_voltage_self_edit') &&
            in_array($dropVoltage->group_infrastructure_object_id,
                Auth::user()?->dispatchArea?->groupInfrastructureObjects?->pluck('id')?->toArray() ?? []) &&
            $dropVoltage->status_drop === DropVoltageStatuses::Opened->value) {
            return true;
        }
        return false;
    }

    public function delete(User $user, DropVoltage $dropVoltage): bool
    {
        if ($user->hasPermissionTo('drop_voltage_delete_all_opened') &&
            $dropVoltage->status_drop !== DropVoltageStatuses::Closed->value) {
            return true;
        }
        if ($dropVoltage->created_by === $user->id &&
            $user->hasPermissionTo('drop_voltage_delete_self_opened') &&
            $dropVoltage->status_drop !== DropVoltageStatuses::Closed->value) {
            return true;
        }
        return false;
    }

    public function restore(User $user, DropVoltage $dropVoltage): bool
    {
        return $user->hasRole('admin');
    }

    public function forceDelete(User $user, DropVoltage $dropVoltage): bool
    {
        return $user->hasRole('admin');
    }
}
