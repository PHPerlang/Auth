<?php

return array(

    'root' => [
        'name' => trans('core::role.root'),
        'permissions' => [
            'descendant:post@api/core/auth/member/new/password',
            'descendant:get@api/core/auth/forgot/password/link',
            'descendant:post@api/core/member',
            'descendant:put@api/core/member/{member_id}',
            'descendant:get@api/core/member/{member_id}',
            'descendant:get@api/core/members',
            'descendant:delete@api/core/member',
            'descendant:get@api/core/role?role_id',
        ]
    ],

);