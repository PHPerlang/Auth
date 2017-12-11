<?php

namespace Modules\Auth\Http\Middleware;

use Closure;
use Gindowin\Request;
use Modules\Auth\Models\Guest;
use Modules\Auth\Foundation\AuthPermission;

class AdminAuthMiddleware
{

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {

        if (!$request->route()->open) {

            if (session()->has('admin.member') && isset(session('admin.member')->member_id)) {

                Guest::init(session('admin.member')->member_id);

                (new AuthPermission($request, 'admin'))->run();

            } else if ($request->path() != 'admin/login') {

                return redirect('/admin/login');
            }
        }

        return $next($request);
    }

}