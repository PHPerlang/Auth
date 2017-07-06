# Jindowin 认证登录

[TOC]


## 1. 安装

```sh
$ gindowin install Auth
```

## 2. 接口列表

* [用户注册发送验证验证码 /api/auth/register/code](post/auth/register/code.md)
* [用户注册 /api/auth/register](post/auth/register.md)
* [编辑用户资料 /api/member](put/auth/member.md)
* [设置新用户密码 /api/member/password](put/auth/member/password.md)
* [用户登录 /api/auth/login](post/auth/login.md)
* [忘记密码发送验证码 /api/auth/reset/password/code](post/auth/reset/password/code.md)
* [重置密码 /api/auth/reset/password](post/auth/reset/password.md)
* [安全密码 /api/auth/password](put/auth/password.md)
* [更换邮箱 /api/auth/change/email](post/auth/change/email.md)

## 3. 接口规范

### 3.1 请求头

参数 | 说明
:---:| :---:
X-App-Id | 必须，应用 ID，格式：h5:1.0.0
X-UDID | 非必须，设备 ID
X-Access-Token | 部分接口必须，用户接入秘钥
Accept-Language | 非必须，显示语言（en, zh-CN, zh），默认为 zh-CN

### 3.2 响应格式

参数|解释|类型
:---:| :---| :---:
code|处理状态码，操作成功为 200|int
message|状态码文案| string
data|数据部分|mixed

**示例：**

```json
{
  "code": 200,
  "message": "操作成功",
  "data": null
}
```

### 3.3 全局状态码

状态码 | 消息 | 说明
:---: | :---: | :---
200 | 操作成功 | 所有操作成功的状态统一使用 200 状态码标识
800 | 数据库错误 | 系统如有某处数据库操作报错，将会触发此状态，生产环境下，将抹掉具体的报错信息
900 | 请求头 `X-App-Id` 不能为空 | -
901 | 请求头 `X-App-Id` 格式不正确 | 要求格式 `客户端:版本号(x.x.x)`
902 | 请求头 `X-App-Id` 不正确 | 客户端不正确，目前支持的客户端有 web,pad,ios,android,h5
910 | 请求头 `X-Access-Token` 不能为空 | -
920 | 请求头 `X-Access-Token` 无效 | 用户登录过期出现此状态
930 | 查询数据为空 | 只要是数据库查询结果为空的给出此状态码
1000 | 数据格式校验错误 | 系统针对批量数据校验统一给出 `1000` 状态码

### 3.4 数据格式校验响应

```json
{
  "code": 1000,
  "message": "数据校验错误",
  "data": {
    "member_email": [
      "用户邮箱不能为空"
    ],
    "member_password": [
      "用户密码不能为空"
    ]
  }
}
```



## 4.应用配置
 
支持三种注册登录方式: 邮箱、手机号码、和用户名。

配置文件：

> modules/Auth/Config/config.php

```php
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

## 5. 环境变量

```
YUNPIAN_APIKEY=             # 云片秘钥
YUNPIAN_CODE_TEMPLATE=      # 云片验证码模板
```