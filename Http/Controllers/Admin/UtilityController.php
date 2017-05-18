<?php

namespace Modules\Core\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Modules\Core\Contracts\Context;
use Illuminate\Support\Facades\Artisan;

class UtilityController extends Controller
{

    protected $context;

    public function __construct(Context $context)
    {
        $this->context = $context;
    }

    /**
     * Print project all routes by Artisan.
     */
    public function getRoutes()
    {

        Artisan::call('route:list');

        $routeCollection = Artisan::output();

        echo '<pre>';
        print_r($routeCollection);
        echo '</pre>';

    }


}