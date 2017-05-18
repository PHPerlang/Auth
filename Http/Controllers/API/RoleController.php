<?php

namespace Modules\Core\Http\Controllers\API;

use Modules\Core\Models\Role;
use Modules\Core\Models\Guest;
use Illuminate\Validation\Rule;
use Illuminate\Routing\Controller;
use Modules\Core\Contracts\Context;


class RoleController extends Controller
{
    protected $context;

    public function __construct(Context $context)
    {
        $this->context = $context;
    }

    /**
     * 添加角色
     *
     * @return \Modules\Core\Models\Status
     */
    public function postRole()
    {

        validate($this->context->data(), [
            'role_name' => 'required',
            'role_type' => ['required', Rule::in(['descendant', 'self'])],
            'role_status' => ['required', Rule::in(['forever', 'temporary'])],
        ]);

        $role = new Role;

        $role->role_creator_id = Guest::id();
        $role->role_name = $this->context->data('role_name');
        $role->role_type = $this->context->data('role_type');
        $role->role_status = $this->context->data('role_status');
        $role->role_desc = $this->context->data('role_desc', $role->role_desc);
        $role->role_desc = $this->context->data('role_desc', $role->role_desc);


        return $this->context->status(200, $role);
    }


    public function getRole()
    {
        dd(Guest::id());
    }

}
