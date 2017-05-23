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
     * The request uri.
     *
     * @var string
     */
    protected $uri;

    /**
     * The request method.
     *
     * @var string
     */
    protected $method;

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
     * The access token from the header.
     *
     * @var string
     */
    protected $access_token;

    /**
     * The client object from the ResolveClientMiddleware.
     *
     * @var object
     */
    protected $client;

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

        $this->method = strtolower($this->request->getMethod());

        $this->uri = $this->request->path();

        $this->route = $this->request->route();

        $this->client = $this->request->client;

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