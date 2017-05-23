<?php

namespace Modules\Auth\Http\Middleware;

use Closure;
use Jindowin\Module;
use Jindowin\Request;
use Modules\Auth\Models\Guest;
use Modules\Auth\Foundation\Route;
use Modules\Auth\Models\MemberRole;
use Modules\Auth\Models\Permission;

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

        $this->request = $request;

        $this->route = $this->request->route();

        if (!$this->route->open) {

            $this->checkAccessToken();
            $this->checkGuestIsExist();
            $this->authGuestPermission(Guest::id());
        }

        $response = $next($this->request);

        return $response;
    }

    /**
     * Check access token when the api is not open.
     */
    protected function checkAccessToken()
    {
        if (!$this->request->getAccessToken()) {

            exception(910, null);
        }
    }

    /**
     * Check guest when the api is not open.
     */
    protected function checkGuestIsExist()
    {
        if (!Guest::id()) {

            exception(920, null);
        }
    }

    protected function authClient()
    {
        $client = $this->request->client;
    }

    protected function getCurrentPermission()
    {
        $method = strtolower($this->request->getMethod());

        $uri = $this->request->path();
    }

    /**
     * Auth the guest if have the permission to access the api.
     *
     * @param $guest_id
     */
    protected function authGuestPermission($guest_id)
    {


        if ($affectMemberId = $this->request->query('member_id', null)) {


            if ($affectMemberId != $guest_id) {

                exception('当前用户无法操作该资源');
            }
        }

    }

    /**
     * Get the guest roles.
     *
     * @param string $guest_id
     *
     * @return array
     */
    protected function getGuestRoles($guest_id)
    {
        return MemberRole::where('member_id', $guest_id)->list('role_id');
    }

    /**
     * Get the guest permissions.
     *
     * @param string $guest_id
     *
     * @return array
     */
    protected function getGuestPermissions($guest_id)
    {
        return Permission::whereIn('role_id', $this->getGuestRoles($guest_id))->list('id');
    }


}