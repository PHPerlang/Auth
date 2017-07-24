# 用户登录

[TOC]

## 1. API 描述：

用户通过手机、邮箱、或用户名登录，需要开启对应的登录通道。

## 2. 调用方法

> POST /api/auth/login

## 2. 请求参数

参数 | 解释 | 类型 | 是否必须 | 示例数据
:---:|:---|:---:|:---:|:---
member_email | 用户邮箱 | string | `login_type=email`时必须 | `im@koyeo.io`
member_phone | 用户手机 | number | `login_type=mobile`时必须 | `188****8888`
member_account | 用户名 | string | `login_type=username`时必须 | `koyeo`
member_password | 用户密码，不低于 6 位 | string | 是 | x123456
login_channel | 标识登录类型 | string | 是 | `email`/`mobile`/`username`

## 3. 响应状态

状态码 | 说明
:---:|:---
200 | 操作成功
1000 | 数据格式错误
1100 | 账号或密码不正确
1300 | 邮箱未验证，不能登录
1500 | 手机未验证，不能登录
2000 | 该登录类型通道已关闭

## 4. 响应数据

```json
{
    "code": 200,
    "message": "操作成功",
    "data": {
        "member_id": 2,        // 用户 ID
        "access_token": "bc1016507a50341a2dad243efb1cdd85", // 用户访问秘钥
        "client_group": "mobile",    // 客户端分组
        "client_id": "h5", // 客户端 ID
        "client_version": "1.0.0",    // 客户端版本号
        "expired_at": "2017-07-06 22:08:16",    // 秘钥失效时间
        "updated_at": "2017-07-06 22:08:16",   // 用户更新时间
        "created_at": "2017-07-06 22:08:16"     // 用户创建时间
    }
}
```

## 5. 示例

> POST /api/auth/login

**请求数据：**

```josn
{
   "member_phone" : "188****8888",
   "member_password" : "123456",
   "login_channel" : "mobile"
}
```

**响应结果：**

```josn
{
  "code": 200,
  "message": "登录成功",
  "data": {
    "access_token": "1e10adc3949ba59abbe56e057f20f883e",
    "client_id": "ios",
    "client_group": "mobile",
    "client_version": "1.0.1",
    "created_at": "2017-04-26 18:54:00",
    "expired_at": "2017-04-26 18:54:00"
  }
}
```