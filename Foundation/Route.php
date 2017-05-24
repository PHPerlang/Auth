<?php

namespace Modules\Auth\Foundation;

use Jindowin\Foundation\Route as JindowinRoute;

class Route extends JindowinRoute
{
    /**
     * Indicates if the route is public to everyone.
     *
     * @var mixed
     */
    public $open;

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
     *
     * @return $this
     *
     */
    public function guard(array $params)
    {
        $this->guard = $params;

        return $this;
    }

    public function query($query)
    {
        return $this;
    }
}