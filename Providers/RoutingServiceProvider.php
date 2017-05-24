<?php

namespace Modules\Auth\Providers;

use Modules\Auth\Foundation\Router;
use Jindowin\Providers\RoutingServiceProvider as JindowinRoutingServiceProvider;

class RoutingServiceProvider extends JindowinRoutingServiceProvider
{
    /**
     * Register the router instance.
     *
     * @return void
     */
    protected function registerRouter()
    {

        $this->app->singleton('router', function ($app) {

            return new Router($app['events'], $app);
        });

    }


}