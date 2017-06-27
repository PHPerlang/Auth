# Jindowin 认证登录

[TOC]

<!-- [TOC] -->

## 配置
 
支持三种注册登录方式: 邮箱、手机号码、和用户名。

配置文件：

```php
// Config/config.php

<?php

return [

    /*
    |--------------------------------------------------------------------------
    | 定义注册方式
    |--------------------------------------------------------------------------
    |
    | 支持 3 种注册方式，邮箱，短信和用户名。
    |
    */
    'register_types' => ['email', 'sms', 'username'],

    /*
    |--------------------------------------------------------------------------
    | 定义登录方式
    |--------------------------------------------------------------------------
    |
    | 支持 3 种登录方式，邮箱，短信和用户名。
    |
    */
    'login_types' => ['email', 'sms', 'username'],

    /*
    |--------------------------------------------------------------------------
    | 定义找回密码的方式
    |--------------------------------------------------------------------------
    |
    | 支持 3 种通行证方式，邮箱，短信。
    |
    */
    'find_password_types' => ['email', 'sms'],

    /*
    |--------------------------------------------------------------------------
    | 云片 APIKEY
    |--------------------------------------------------------------------------
    |
    | 云片发送的 API 密匙，请登录云片管理台首页获取
    */
    'yunpian_apikey' => env('YUNPIAN_APIKEY', ''),

    /*
    |--------------------------------------------------------------------------
    | 云片短信验证码模板
    |--------------------------------------------------------------------------
    |
    | 需要自行在云片管理后台添加并审核通过
    */
    'yunpian_code_template' => env('YUNPIAN_CODE_TEMPLATE',''),
];
```

## 环境变量

```
YUNPIAN_APIKEY=             # 云片秘钥
YUNPIAN_CODE_TEMPLATE=      # 云片验证码模板

```

## API 

* [登录](Document/api/login.md)
* [注册](Document/api/register.md)
* [忘记密码](Document/api/forgot.md)
* [忘记密码2](Document/api/forgot2.md)

