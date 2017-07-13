<?php

namespace Modules\Auth\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Session\Middleware\StartSession as LaravelStartSession;

class StartSession extends LaravelStartSession
{
    /**
     * Get the session implementation from the manager.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\Session\Session
     */
    public function getSession(Request $request)
    {
        return tap($this->manager->driver(), function ($session) use ($request) {

            if ($request->headers->has('X-Session-Token')) {

                $sessionId = $request->headers->get('X-Session-Token');

            } else {

                $sessionId = $request->cookies->get($session->getName());
            }

            $session->setId($sessionId);

        });
    }
}
