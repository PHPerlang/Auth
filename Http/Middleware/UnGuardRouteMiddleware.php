<?php

namespace Modules\Auth\Http\Middleware;

use Closure;
use Gindowin\Request;
use Modules\Auth\Foundation\Route;

class UnGuardRouteMiddleware
{

    /**
     * The request route object.
     *
     * @var Route
     */
    protected $route;

    /**
     * The request.
     *
     * @var Request
     */
    protected $request;


    /**
     * Handle the middleware process.
     *
     * @param $request
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $request->route()->pure();

        $response = $next($request);

        return $response;
    }


}