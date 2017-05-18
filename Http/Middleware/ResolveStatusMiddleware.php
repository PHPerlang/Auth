<?php

namespace Modules\Core\Http\Middleware;

use Closure;
use Modules\Core\Models\Status;

class ResolveStatusMiddleware
{

    /**
     * Handle the middleware process.
     *
     * @param $request
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        $response = $next($request);

        if ($response->original instanceof Status) {

            $response->setContent($response->original)->header('Content-Type', 'application/json');
        }

        return $response;
    }

}