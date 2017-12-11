<?php

namespace Modules\Auth\Foundation;

use Gindowin\Foundation\Route as GindowinRoute;

class Route extends GindowinRoute
{
    /**
     * Indicates if the route is public to everyone.
     *
     * @var mixed
     */
    public $open = false;


    /**
     * Indicates if the route should auth with permissions.
     *
     * @var mixed
     */
    public $pure = false;

    /**
     * Store the route received query params.
     *
     * @var array
     */
    public $guard = [];

    /**
     *
     * Store the mask fileds.
     *
     * @var array
     */
    public $mask = [];


    /**
     * Set the route open attribute.
     *
     * @return $this
     */
    public function open()
    {
        $this->open = true;

        return $this;
    }


    public function pure()
    {
        $this->pure = true;
    }


    /**
     * Protect the route resource identify use in permission guard middleware.
     *
     * @param array $identify
     *
     * @return $this
     */
    public function mask(array $identify)
    {
        return $this;
    }

    /**
     * Register the route received query params.
     *
     * @param array $params
     * @param string $model
     *
     * @return $this
     *
     */
    public function guard($params, $model = '')
    {
        if (is_array($params)) {
            $this->guard = $params;

        } else if (is_string($params)) {
            $this->guard = [$params => $model];
        }

        return $this;
    }

}