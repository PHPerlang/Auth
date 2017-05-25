<?php

return array(

    'root' => [
        'name' => trans('auth::role.root'),
        'permissions' => [
            'get@api/auth/auth/forgot/password/link',
            'post@api/auth/member',
            'put@api/auth/member?member_id=2',
            'get@api/auth/member?member_id=2',
            'get@api/auth/members',
            'delete@api/auth/member',
            'get@api/auth/role?role_id=1'
        ]
    ],

);