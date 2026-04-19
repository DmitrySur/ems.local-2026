<?php

namespace App\Policies\Incident;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class IncidentEmployeeReferralPolicy
{
    use HandlesAuthorization;

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
        return $user->hasPermissionTo('incident_employee_referral_management');
    }

    public function update(User $user): bool
    {
        return $user->hasPermissionTo('incident_employee_referral_management');
    }

    public function delete(User $user): bool
    {
        return $user->hasPermissionTo('incident_employee_referral_management');
    }

    public function restore(User $user): bool
    {
        return $user->hasPermissionTo('incident_employee_referral_management');
    }

    public function forceDelete(User $user): bool
    {
        return $user->hasPermissionTo('incident_employee_referral_management');
    }
}
