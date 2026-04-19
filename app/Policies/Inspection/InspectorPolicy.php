<?php

namespace App\Policies\Inspection;

use App\Models\Inspection\Inspector;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class InspectorPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {

    }

    public function view(User $user, Inspector $inspector): bool
    {
    }

    public function create(User $user): bool
    {
    }

    public function update(User $user, Inspector $inspector): bool
    {
    }

    public function delete(User $user, Inspector $inspector): bool
    {
    }

    public function restore(User $user, Inspector $inspector): bool
    {
    }

    public function forceDelete(User $user, Inspector $inspector): bool
    {
    }
}
