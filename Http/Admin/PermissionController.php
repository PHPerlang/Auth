<?php

namespace Modules\Auth\Http\Admin;

use Gindowin\Request;
use Illuminate\Routing\Controller;
use Modules\Auth\Models\Permission;


class PermissionController extends Controller
{
    /**
     * 客户端请求
     *
     * @var Request
     */
    public $request;

    /**
     * 构造函数
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function getPermissionsView()
    {
        $permissions = Permission::get();

        return view('auth::admin.permissions', [
            'permissions' => $permissions,
            'path'=>'permissions',
        ]);
    }
}