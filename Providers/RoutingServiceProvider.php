<?php

namespace Modules\Core\Providers;

use Modules\Core\Foundation\Router;
use Illuminate\Routing\RoutingServiceProvider as LaravelRoutingServiceProvider;

class RoutingServiceProvider extends LaravelRoutingServiceProvider
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