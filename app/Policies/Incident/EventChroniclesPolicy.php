<?php

namespace App\Policies\Incident;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class EventChroniclesPolicy
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
        return $user->hasPermissionTo('event_chronicles_management');
    }

    public function update(User $user): bool
    {
        return $user->hasPermissionTo('event_chronicles_management');
    }

    public function delete(User $user): bool
    {
        return $user->hasPermissionTo('event_chronicles_management');
    }

    public function restore(User $user): bool
    {
        return $user->hasPermissionTo('event_chronicles_management');
    }

    public function forceDelete(User $user): bool
    {
        return $user->hasPermissionTo('event_chronicles_management');
    }
}
