<?php

namespace Modules\Auth\Http\Middleware;

use Closure;
use Jindowin\Module;
use Jindowin\Request;
use Modules\Auth\Models\Guest;
use Modules\Auth\Foundation\Route;
use Modules\Auth\Models\MemberRole;
use Modules\Auth\Models\Permission;
use Modules\Auth\Models\RolePermissions;

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

            if (!$this->authGuestPermission(Guest::id())) {

                exception(403);
            }
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

    /**
     * Get the current route permission identify.
     *
     * @return string
     */
    protected function getRoutePermissionId()
    {
        $method = strtolower($this->request->getMethod());

        $uri = $this->request->path();

        return $method . '@' . $uri;
    }

    /**
     * Auth the guest if have the permission to access the api.
     *
     * @param $guest_id
     *
     * @return bool
     */
    protected function authGuestPermission($guest_id)
    {

        $guest_roles = $this->getGuestRoles($guest_id)->toArray();
        $guest_permissions = $this->getGuestPermissions($guest_roles)->toArray();
        $permissionLimitParams = $this->getPermissionLimitParams($guest_permissions);
        $routeGuardFields = $this->getRouteGuardFields();

        foreach ($routeGuardFields as $field) {

            if (array_key_exists($field, $permissionLimitParams)) {

                $input = $this->request->input($field);

                foreach ($permissionLimitParams[$field] as $value) {

                    if ($input == $value) {

                        return true;
                    }
                }

                return false;

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
        return MemberRole::where('member_id', $guest_id)->pluck('role_id');
    }

    /**
     * Get the guest permissions.
     *
     * @param array $guest_roles
     *
     * @return array
     *
     */
    protected function getGuestPermissions(array $guest_roles)
    {
        return RolePermissions::where('permission_id', $this->getRoutePermissionId())
            ->whereIn('role_id', $guest_roles)
            ->get(['permission_id', 'limit_params', 'limit_parse', 'permission_type', 'expired_at']);
    }

    /**
     * Get permission all limit params.
     *
     * @param array $guest_permissions
     *
     * @return array
     */
    protected function getPermissionLimitParams(array $guest_permissions)
    {
        $guard_fields = [];

        foreach ($guest_permissions as $guest_permission) {

            if (!$guest_permission['limit_parse']) {

                continue;
            }

            $fields = json_decode($guest_permission['limit_parse'], true);

            foreach ($fields as $filed => $value) {

                if (key_exists($filed, $guard_fields)) {

                    $guard_fields[$filed] = array_merge($guard_fields[$filed], $value);
                } else {

                    $guard_fields[$filed] = $value;
                }
            }
        }


        return $guard_fields;
    }

    /**
     * Get route guard fields from route guard attribute.
     *
     * @return array
     */
    protected function getRouteGuardFields()
    {
        $guard_field = [];

        foreach ($this->request->route()->guard as $key => $value) {

            if (is_int($key)) {

                array_push($guard_field, $value);
            } else {

                array_push($guard_field, $key);
            }
        }

        return $guard_field;
    }


}