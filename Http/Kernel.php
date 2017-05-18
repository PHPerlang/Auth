<?php

namespace Modules\Core\Http;

use Modules\Core\Foundation\Router;
use Modules\Core\Foundation\Application;
use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{


    /**
     * Create a new HTTP kernel instance.
     *
     * @param \Illuminate\Contracts\Foundation\Application|Application $app
     * @param \Illuminate\Routing\Router|Router $router
     */
    public function __construct(Application $app, Router $router)
    {
        $this->app = $app;
        $this->router = $router;

        $router->middlewarePriority = $this->middlewarePriority;

        foreach ($this->middlewareGroups as $key => $middleware) {
            $router->middlewareGroup($key, $middleware);
        }

        foreach ($this->routeMiddleware as $key => $middleware) {
            $router->aliasMiddleware($key, $middleware);
        }
    }


    /**
     * Register middlewares in router.
     */
    public function RegisterMiddlewares()
    {
        foreach ($this->middlewareGroups as $key => $middleware) {
            $this->router->middlewareGroup($key, $middleware);
        }

        foreach ($this->routeMiddleware as $key => $middleware) {
            $this->router->aliasMiddleware($key, $middleware);
        }
    }


    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [];

    /**
     * Add application's global HTTP middleware.
     *
     * @param string $middleware
     */
    public function addMiddleware($middleware)
    {
        array_push($this->middleware, $middleware);
    }

    /**
     * application's route middleware groups.
     *
     * @param string $groupName
     * @param array $middlewares
     *
     * @throws \Exception
     */
    public function addMiddlewareGroups($groupName, array $middlewares)
    {
        if (!isset($this->middlewareGroups[$groupName])) {

            $this->middlewareGroups[$groupName] = $middlewares;

        } else {

            $this->middlewareGroups[$groupName] = array_merge($this->middlewareGroups[$groupName], $middlewares);
        }
    }

    /**
     * Add application's route middleware.
     *
     * @param array $middlewares
     */
    public function addRouteMiddlewares(array $middlewares)
    {
        $this->routeMiddleware = array_merge($this->routeMiddleware, $middlewares);
    }

}
