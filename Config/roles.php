<?php

return array(

    'root' => [
        'name' => trans('auth::role.root'),
        'permissions' => [
            'descendant:post@api/auth/auth/member/new/password',
            'descendant:get@api/auth/auth/forgot/password/link',
            'descendant:post@api/auth/member',
            'descendant:put@api/auth/member/{member_id}',
            'descendant:get@api/auth/member/{member_id}',
            'descendant:get@api/auth/members',
            'descendant:delete@api/auth/member',
            'descendant:get@api/auth/role?role_id',
        ]
    ],

);