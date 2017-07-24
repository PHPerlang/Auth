<?php

namespace Modules\Auth\Http\API;

use Gindowin\Request;
use Illuminate\Routing\Controller;


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
