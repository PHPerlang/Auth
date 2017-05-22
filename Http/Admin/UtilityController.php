<?php

namespace Modules\Auth\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Modules\Auth\Contracts\Request;
use Illuminate\Support\Facades\Artisan;

class UtilityController extends Controller
{

    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
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