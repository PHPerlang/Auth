<?php

namespace Modules\Auth\Foundation;

use Gindowin\Foundation\Router as JindowinRouter;

class Router extends JindowinRouter
{
    /**
     * Create a new Route object.
     *
     * @param  array|string $methods
     * @param  string $uri
     * @param  mixed $action
     * @return \Illuminate\Routing\Route
     */
    protected function newRoute($methods, $uri, $action)
    {
        return (new Route($methods, $uri, $action))
            ->setRouter($this)
            ->setContainer($this->container);
    }

}