<?php

return array(

    'root' => [
        'name' => '超级管理员',
        'description' => '超级管理员，拥有系统的一切权限',
        'permissions' => [
            'get@api/auth/role'
        ],
    ],

    'staff' => [
        'name' => '职员',
        'description' => '内部职员角色，拥有基本的后台登录权限',
        'permissions' => [
            'get@admin'
        ]
    ]

);