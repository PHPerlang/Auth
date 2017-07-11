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
        3005 => '密码不能为空',

    ])->open();

    // 为新用户设置密码
    Route::post('/member/password', 'AuthController@postNewPassword')->codes([
        200 => '用户密码修改成功',
        1001 => '该用户不存在',
        1300 => '验证码不正确',

    ]);

    // 用户登录
    Route::post('/login', 'AuthController@postLogin')->codes([
        200 => '登录成功',
        1000 => '数据校验失败',
        1001 => '账号或密码不正确',
        2000 => '该登录类型通道已关闭',
    ])->open();

    // 发送密码重置验证码
    Route::post('/reset/password/code', 'AuthController@postResetPasswordCode')->codes([
        200 => '密码重置验证码发送成功',
        2000 => '该找回密码通道已关闭',
        2001 => '注册码已超出最大发送次数，请明天再试',
        2002 => '发送太频繁，请 60 秒后再试',
        2010 => '该用户不存在',
    ])->open();

    // 重置密码邮件链接跳转
    Route::get('/reset/password/{encrypt_email}/{encrypt_code}', 'AuthController@getResetPasswordLinkRedirect')->open();

    // 重置找回密码
    Route::put('/reset/password', 'AuthController@putResetPassword')->codes([
        200 => '重置密码成功',
        1000 => '数据校验失败',
        1300 => '验证码不正确',
        2000 => '该找回密码通道已关闭',
    ])->open();

    // 更改密码
    Route::put('/password', 'AuthController@putPassword')->codes([
        1000 => '密码格式校验错误',
        10001 => '新密码不能与原密码相同',
    ]);

    // 用户退出登录
    Route::post('/logout', 'AuthController@postLogout')->open();

    // 发送忘记密码链接
    Route::get('/forgot/password/link', 'AuthController@getForgotPasswordLink');

    // 发送更换邮箱链接
    Route::post('/change/email', 'AuthController@postChangeEmailLink')->codes([
        1000 => '邮箱格式错误',
        1001 => '新邮箱不能与原邮箱相同',
        1002 => '新邮箱是已经存在',
    ]);

    // 更换邮箱链接点击跳转
    Route::get('/change/email/{encrypt}', 'AuthController@getChangeEmail')->open();

    // 获取图形验证码
    Route::get('/captcha', 'AuthController@getCaptcha')->open();


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

