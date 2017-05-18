<?php

namespace Modules\Core\Http\Controllers\API;

use Illuminate\Routing\Controller;
use Modules\Core\Contracts\Context;

class PermissionController extends Controller
{

    protected $context;

    public $codes = [
        200 => 'Success',
        4900 => 'Permissions',
    ];

    public function __construct(Context $context)
    {
        $this->context = $context;
    }


    public function postPermission()
    {

    }

}
