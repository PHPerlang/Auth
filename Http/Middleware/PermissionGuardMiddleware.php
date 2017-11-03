<?php

namespace Modules\Auth\Http\Middleware;

use Closure;
use Gindowin\Request;
use Modules\Auth\Models\Guest;
use Modules\Auth\Foundation\Route;

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


        $this->checkAccessToken();
        $this->checkGuestIsExist();

        if (env('PERMISSION_SYSTEM') == 'on' && !$this->authGuestPermission()) {

            exception(403);
        }


        $response = $next($this->request);

        return $response;
    }

    /**
     * Check access token when the api is not open.
     */
    protected function checkAccessToken()
    {
        if (!$this->route->open && !$this->request->header('X-Access-Token', $this->request->input('X-APP-Id'))) {

            exception(910, null);
        }
    }

    /**
     * Check guest when the api is not open.
     */
    protected function checkGuestIsExist()
    {
        if (!$this->route->open && !Guest::id()) {

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
    protected function getRoutePermission()
    {
        $method = strtolower($this->request->getMethod());

        $uri = $this->request->path();

        return $method . '@' . $uri;
    }

    /**
     * Auth the guest if have the permission to access the api.
     *
     * @return bool
     */
    protected function authGuestPermission()
    {
        Guest::setRoutePermission($this->getRoutePermission());

        $guest_permissions = Guest::permissions()->toArray();
        $permissionLimitParams = Guest::params($guest_permissions);
        $routeGuardFields = $this->getRouteGuardFields();

        if (!$guest_permissions) {
            return false;
        }

        foreach ($routeGuardFields as $field) {

            if (!$this->request->input($field)) {

                exception('1000', $field . ' 字段不能为空');
            }

            if (array_key_exists($field, $permissionLimitParams)) {

                if (!$this->authLimitField($permissionLimitParams, $field)) {

                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Auth limit field value.
     *
     * @param array $permissionLimitParams
     * @param string $field
     *
     * @return bool
     */
    protected function authLimitField($permissionLimitParams, $field)
    {
        if (!isset($permissionLimitParams[$field])) {

            return true;
        }

        $input = $this->request->input($field);

        $list = $permissionLimitParams[$field];

        foreach ($list as $value) {

            if ($input == $value || $value == '*') {

                return true;
            }

            if ($field == 'member_id' && $value == 'guest') {

                if ($this->request->input('member_id') == Guest::id()) {

                    return true;
                }
            }

        }

        return false;

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