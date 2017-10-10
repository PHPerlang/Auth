<?php

namespace Modules\Auth\Http\Admin;

use Gindowin\Request;
use Modules\Auth\Models\Role;
use Illuminate\Routing\Controller;


class RoleController extends Controller
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

    public function getRolesView()
    {
        $roles = (new Role)->getFixedRoles();

        return view('auth::admin.roles', [
            'roles' => $roles,
            'path' => 'roles',
        ]);
    }

    public function getRoleEditor()
    {
        return view('auth::admin.role-editor', [
            'path' => 'roles',
        ]);
    }
}