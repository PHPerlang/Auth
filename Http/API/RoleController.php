<?php

namespace Modules\Auth\Http\API;

use Jindowin\Request;
use Modules\Auth\Models\Role;
use Modules\Auth\Models\Guest;
use Illuminate\Validation\Rule;
use Illuminate\Routing\Controller;


class RoleController extends Controller
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * 添加角色
     *
     * @return \Modules\Auth\Models\Status
     */
    public function postRole()
    {

        validate($this->request->input(), [
            'role_name' => 'required',
            'role_type' => ['required', Rule::in(['descendant', 'self'])],
            'role_status' => ['required', Rule::in(['forever', 'temporary'])],
        ]);

        $role = new Role;

        $role->role_creator_id = Guest::id();
        $role->role_name = $this->request->input('role_name');
        $role->role_type = $this->request->input('role_type');
        $role->role_status = $this->request->input('role_status');
        $role->role_desc = $this->request->input('role_desc', $role->role_desc);
        $role->role_desc = $this->request->input('role_desc', $role->role_desc);


        return status(200, $role);
    }


    public function getRole()
    {
        dd(Guest::params());
    }

}
