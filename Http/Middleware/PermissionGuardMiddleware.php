<?php

namespace Modules\Auth\Http\Middleware;

use Closure;
use Gindowin\Request;
use Modules\Auth\Foundation\Route;
use Modules\Auth\Foundation\AuthPermission;

class PermissionGuardMiddleware
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
        // 开放路由和业务路由不需要权限验证
        if (!$request->route()->open && !$request->route()->pure) {

            (new AuthPermission($request))->run();
        }

        $response = $next($request);

        return $response;
    }


}