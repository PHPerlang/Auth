<?php

return array(

    'root' => [
        'name' => trans('auth::role.root'),
        'permissions' => [
            'get@api/auth/role?team_id=1,2,3&sense_id=2&phase_id=3&member_id=5',
            'get@api/auth/auth/forgot/password/link',
            'post@api/auth/member',
            'put@api/auth/member?member_id=2',
            'get@api/auth/member?member_id=2',
            'get@api/auth/members',
            'delete@api/auth/member',
            'get@api/auth/role?role_id=6&member_id=4,12,43&team_id=88,99',
            'get@api/auth/roles?role_id=*'
        ]
    ],

);