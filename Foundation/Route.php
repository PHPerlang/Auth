<?php

namespace Modules\Core\Foundation;

use Illuminate\Routing\Route as LaravelRoute;

class Route extends LaravelRoute
{

    /**
     * The API codes the route responds to.
     *
     * @var array
     */
    public $codes;

    /**
     * Indicates if the route is public to everyone.
     *
     * @var mixed
     */
    public $open;


    /**
     * Indicates if the route is public or protected to visitor.
     *
     * @var mixed
     */
    public $mask;


    /**
     * Store the route received query params.
     *
     * @var array
     */
    public $query = [];

    /**
     * Resource Identify
     *
     * @var string
     */
    public $resource_identify;

    /**
     * Register the codes with chain function call.
     *
     * @param array $codes
     *
     * @return $this
     */
    public function codes($codes = [])
    {
        $this->codes = $codes;

        return $this;
    }

    /**
     * Set the route open attribute.
     *
     * @return $this
     */
    public function open()
    {
        if ($this->mask) {

            $this->throwRouteTypeExistsError('mask');
        }

        $this->open = true;

        return $this;
    }

    /**
     * Set the route mask attribute.
     *
     * @return $this
     */
    public function mask()
    {
        if ($this->open) {

            $this->throwRouteTypeExistsError('open');
        }

        $this->mask = true;

        return $this;
    }

    /**
     * Protect the route resource identify use in permission guard middleware.
     *
     * @param array $identify
     *
     * @return $this
     */
    public function protect(array $identify)
    {
        $this->resource_identify = $identify;

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
    public function query(array $params)
    {
        $this->query = $params;

        return $this;
    }

    /**
     * Throw a route type error when route's type has been defined.
     *
     * @param string $type
     *
     * @throws \Exception
     */
    public function throwRouteTypeExistsError($type)
    {
        throw new \Exception('The route type has been defined with: ' . $type);
    }
}