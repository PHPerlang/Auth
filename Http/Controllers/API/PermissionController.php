<?php

namespace Modules\Auth\Http\Controllers\API;

use Illuminate\Routing\Controller;
use Modules\Auth\Contracts\Request;

class PermissionController extends Controller
{

    protected $request;

    public $codes = [
        200 => 'Success',
        4900 => 'Permissions',
    ];

    public function __construct(Request $request)
    {
        $this->request = $request;
    }


    public function postPermission()
    {

    }

}
