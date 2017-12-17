<?php

/**
 * 注册角色列表
 *
 * 超级管理员角色拥有一切权限，不用在此注册
 * staff 角色代表内部职工，拥有使用后台的基本权限
 */
return array(

    'staff' => [
        'name' => '职员',
        'description' => '内部职员角色，拥有基本的后台登录权限',
        'permissions' => [
            'get@admin/auth/member/profile',
            'get@admin/auth/member/setting',
            'get@admin/auth/member/login/log',
        ]
    ]

);