<?php

return array(

    'root' => [
        'name' => trans('auth::role.root'),
        'permissions' => [
            'get@api/auth/role?team_id=*&sense_id=2&phase_id=3&member_id=guest',
            'get@api/auth/auth/forgot/password/link',
            'post@api/auth/member',
            'put@api/auth/member?member_id=2',
            'get@api/auth/member?member_id=2',
            'get@api/auth/members',
            'delete@api/auth/member',
            'get@api/auth/role?role_id=6&member_id=4,12,43&member_id=guest',
            'get@api/auth/roles?role_id=*'
        ]
    ],

);