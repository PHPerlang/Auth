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

    // 发送验证码，支持手机验证码，邮箱验证码
    Route::post('/code', 'AuthController@postCode')->codes([
        200 => '验证码发送成功',
        1000 => '数据格式不正确',
        1300 => '通道不支持',
        3000 => '无有效处理器',
    ])->open();


    // 用户注册
    Route::post('/register', 'AuthController@postRegister')->codes([
        200 => '注册成功',
        1000 => '数据校验失败',
        1300 => '验证码不正确',
        2000 => '该注册类型通道已关闭',
    ])->open();

    // 为新用户设置密码
    Route::post('/member/password', 'AuthController@postNewPassword')->codes([
        200 => '用户密码修改成功',
        1100 => '该用户不存在',
        1300 => '验证码不正确',

    ]);

    // 用户登录
    Route::post('/login', 'AuthController@postLogin')->codes([
        200 => '登录成功',
        1000 => '数据校验失败',
        1100 => '账号或密码不正确',
        1300 => '邮箱未验证，不能登录',
        1500 => '手机未验证，不能登录',
        2000 => '该登录类型通道已关闭',
    ])->open();

    // 注销登录
    Route::get('/logout', 'AuthController@getLogout');


    // 绑定微信账户
    Route::get('/bind/wechat/code', 'AuthController@bindWechatByCode')->codes([
        1500 => '微信服务器错误',
        1600 => '微信请求错误',
    ]);


    // 发送密码重置验证码
    Route::post('/reset/password/code', 'AuthController@postResetPasswordCode')->codes([
        200 => '密码重置验证码发送成功',
        2000 => '该找回密码通道已关闭',
        2010 => '注册码已超出最大发送次数，请明天再试',
        2020 => '发送太频繁，请 60 秒后再试',
        2030 => '该用户不存在',
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

    // 校验验证码
    Route::post('/check/code', 'AuthController@postCheckCode')->codes([
        '200' => '验证成功',
        '1000' => '数据格式错误',
        '1300' => '验证码不正确'
    ])->open();

    // 获取图形验证码
    Route::get('/captcha', 'AuthController@getCaptcha')->open();

    // 刷新图形验证码图片
    Route::get('/captcha/{config?}', 'AuthController@getCaptchaImage')->open();

    // 校验图像验证码
    Route::post('/check/captcha', 'AuthController@postCheckCaptchaCode')->codes([
        '200' => '验证成功',
        '1001' => '图形验证码不正确',
    ])->open();

    // 获取当前登录用户信息
    Route::get('/guest', 'AuthController@getGuest');

    // 查询用户是否存在
    Route::post('/check/member/exists', 'AuthController@checkMemberExists')->codes([
        200 => '查询用户存在',
        1000 => '数据格式错误',
        1001 => '查询用户不存在'
    ])->open();

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

