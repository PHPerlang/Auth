<?php

namespace Modules\Auth\Foundation;

use Gindowin\Request;
use Modules\Auth\Models\Guest;

class AuthPermission
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
     * Auth permission scene.
     *
     * @var $scene
     */
    protected $scene;


    /**
     * Handle the auth permissions.
     *
     * @param Request $request
     *
     * @param $scene
     */
    public function __construct(Request $request, $scene = 'api')
    {
        $this->request = $request;

        $this->route = $this->request->route();

        $this->scene = $scene;
    }

    /**
     * Handle the auth permissions.
     */
    public function run()
    {
        $this->checkAccessToken();

        $this->checkGuestIsExist();

        if (env('PERMISSION_SYSTEM') == 'on' && !Guest::instance()->can($this->getRoutePermission())) {

            exception(403);
        }
    }

    /**
     * Check access token when the api is not open.
     */
    protected function checkAccessToken()
    {
        if ($this->scene == 'api' && !$this->route->open && !$this->request->header('X-Access-Token')) {

            exception(910, null);
        }
    }

    /**
     * Check guest when the api is not open.
     */
    protected function checkGuestIsExist()
    {
        if ($this->scene == 'api' && !$this->route->open && !Guest::id()) {

            exception(920, null);
        }
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


}