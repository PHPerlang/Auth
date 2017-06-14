<?php

/*
|--------------------------------------------------------------------------
| Defined API routes.
|--------------------------------------------------------------------------
|
*/

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'api', 'prefix' => '/api/auth', 'namespace' => 'Modules\Auth\Http\API'], function () {

    /*
     |--------------------------------------------------------------------------
     | 认证路由
     |--------------------------------------------------------------------------
     |
     */

    // 用户注册发送验证码，支持手机验证码，邮箱验证码
    Route::post('/register/code', 'AuthController@postRegisterCode')->codes([
        200 => '验证码发送成功',
        1000 => '邮箱格式不正确',
        1001 => '邮箱或手机不能为空',
        2000 => '该注册类型通道已关闭',
        2001 => '注册码已超出最大发送次数，请明天再试',
        2002 => '发送太频繁，请 60 秒后再试',
        3001 => '图形验证码不正确',
        3002 => '该邮箱已注册',
        3003 => '该手机号已注册',
        3004 => '短信网络配置错误',
    ])->open();

    // 用户注册
    Route::post('/register', 'AuthController@postRegister')->codes([
        200 => '注册成功',
        1000 => '数据校验失败',
        1300 => '验证码不正确',
        2000 => '该注册类型通道已关闭',
        3001 => '图形验证码不正确',
        3002 => '该邮箱已注册',
        3003 => '该手机号已注册',
        3004 => '该用户名已注册',

    ])->open();

    // 为新用户设置密码
    Route::post('/member/password', 'AuthController@postNewPassword')->codes([
        200 => '用户密码修改成功',
        1001 => '该用户不存在',
        1002 => '邮箱验证码不正确',
    ]);

    // 用户登录
    Route::post('/login', 'AuthController@postLogin')->codes([
        200 => '登录成功',
        1000 => '数据校验失败',
        1001 => '邮箱或密码不正确'
    ])->open();

    // 发送密码重置邮件
    Route::post('/forgot/password', 'AuthController@postForgotPassword')->codes([
        200 => '密码重置邮件发送成功',
    ])->open();

    // 用户退出登录
    Route::post('/logout', 'AuthController@postLogout', [

    ])->open();

    Route::get('/forgot/password/link', 'AuthController@getForgotPasswordLink');

    /*
    |--------------------------------------------------------------------------
    | 用户路由
    |--------------------------------------------------------------------------
    |
    */
    Route::post('/member', 'MemberController@postMember')->codes([
        200 => '用户添加成功',
    ]);

    Route::put('/member', 'MemberController@putMember')->codes([
        200 => '更新用户信息成功',
        1001 => '用户不存在',
    ]);

    Route::get('/member', 'MemberController@getMember');
    Route::get('/members', 'MemberController@getMembers');
    Route::delete('/member', 'MemberController@deleteMember');


    /*
    |--------------------------------------------------------------------------
    | 用户路由
    |--------------------------------------------------------------------------
    |
    */
    Route::group(['prefix' => 'role'], function () {

        Route::get('/', 'RoleController@getRole')->guard([
            'role_id',
        ]);

    });

});