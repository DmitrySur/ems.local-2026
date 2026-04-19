<?php

namespace App\Actions;

use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;

class SetDispatchAreaForUserAfterLoginWithFilamentAction
{
    public function handle(Request $request, Authenticatable $user): void
    {
        $currentUser = User::select(['id', 'dispatch_area_id'])->find($user->getAuthIdentifier());
        $dispatchAreaId = $request->get('components')[0]['updates']['data.dispatch_area_id'] ?? null;
        if ($currentUser && $dispatchAreaId && $currentUser->hasRole('linear_dispatcher')) {
            $currentUser->timestamps = false;
            $currentUser->dispatch_area_id = $dispatchAreaId;
            $currentUser->save();
        }
    }
}
