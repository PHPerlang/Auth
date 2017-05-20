<?php

return [
    'name' => 'Auth',

    'create_team_permissions' => [
        'open:post@auth/login',
        'open:post@auth/register/code',
        'self:post@auth/member/new/password',
        'self:post@auth/logout',
    ],

    'create_sense_permissions' => [

    ],

    'create_phase_permissions' => [

    ],


];
