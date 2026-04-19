<?php

namespace App\Policies\Incident;

use App\Enum\IncidentCardStatuses;
use App\Models\Incident\Incident;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class IncidentPolicy
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
        return $user->hasPermissionTo('incident_create');
    }

    public function update(User $user, Incident $incident): bool
    {
        if ($user->hasPermissionTo('incident_all_edit') && $incident->status_incident === IncidentCardStatuses::Opened->value) {
            return true;
        }
        if ($user->hasPermissionTo('incident_self_edit') &&
            $incident->dispatch_area_id === $user->dispatch_area_id &&
            $incident->status_incident === IncidentCardStatuses::Opened->value) {
            return true;
        }
        return false;
    }

    public function delete(User $user, Incident $incident): bool
    {
        if ($user->hasPermissionTo('incidents_delete_all_opened') &&
            $incident->status_incident !== IncidentCardStatuses::Closed->value) {
            return true;
        }
        if ($incident->created_by === $user->id &&
            $user->hasPermissionTo('incidents_delete_self_opened') &&
            $incident->status_incident !== IncidentCardStatuses::Closed->value) {
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
