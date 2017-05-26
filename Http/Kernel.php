<?php

namespace Modules\Auth\Http;

use \Modules\Auth\Foundation\Router;
use Jindowin\Foundation\Application;
use Jindowin\Http\Kernel as JindowinHttpKernel;

class Kernel extends JindowinHttpKernel
{

    /**
     * Create a new HTTP kernel instance.
     *
     * @param Application $app
     * @param Router $router
     */
    public function __construct(Application $app, Router $router)
    {
        parent::__construct($app, $router);
    }

}
