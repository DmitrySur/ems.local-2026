<?php

namespace App\Listeners;

use App\Actions\SetDispatchAreaForUserAfterLoginWithFilamentAction;
use Illuminate\Auth\Events\Login;
use Illuminate\Http\Request;

class LoginEventListener
{
    protected Request $request;
    protected SetDispatchAreaForUserAfterLoginWithFilamentAction $setUserDispatchAreaAction;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->setUserDispatchAreaAction = new SetDispatchAreaForUserAfterLoginWithFilamentAction();
    }

    public function handle(Login $event): void
    {
        $this->setUserDispatchAreaAction->handle($this->request, $event->user);
    }
}
