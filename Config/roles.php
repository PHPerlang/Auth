<?php

return array(

    'root' => [
//        'get@api/auth/auth/forgot/password/link',
//        'post@api/auth/member',
//        'put@api/auth/member?member_id=2',
//        'get@api/auth/member?member_id=2',
//        'get@api/auth/members',
//        'delete@api/auth/member',
//        'get@api/auth/role?role_id=1',
        'get@api/kong/card?team_id={{ $team_id }}&sense_id={{ $sense_id }}&phase_id={{ $phase_id }}',
        'get@api/kong/card?team_id={{ $team_id }}&phase_id={{ $phase_id }}',
        'get@api/kong/card?team_id={{ $team_id }}&sense_id={{ $sense_id }}',
        'get@api/kong/card?team_id={{ $team_id }}&sense_id={{ $sense_id }}',
        'get@api/kong/card?phase_id={{ $phase_id }}',
    ],

);