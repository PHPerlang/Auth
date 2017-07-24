# 用户注册

[TOC]

## 1. API 描述：

通过用户名、邮箱或手机号完成用户注册。

## 2. 调用方法

> POST /api/auth/register

## 3. 请求参数

参数 | 解释 | 类型 | 是否必须 | 示例数据
:---:|:---|:---:|:---:|:---
member_email | 用户邮箱 | string | `当 register_channel=email` 必须 | im@koyeo.io
member_phone | 用户手机 | string | `当 register_channel=mobile` 必须 | `188****8888`
member_password | 用户密码 | string | 否 | Hello7
register_channel | 标识注册类型 | string | 是 | `email`、`mobile`
register_code | 邮箱里接收的验证码 | string | 是 | 0893

用户密码要求6~32 位，且必须是数字字母的组合，如果不传用户密码，系统将为该用户分配随机密码。

## 4. 响应状态

状态码 | 说明
:---:|:---
200 | 注册成功
1000 | 数据格式错误
1300 | 验证码不正确
2000 | 该注册类型通道已关闭

## 5. 响应数据

```json
{
    "code": 200,
    "message": "操作成功",
    "data": {
        "member_id": 2,		// 用户 ID
        "access_token": "bc1016507a50341a2dad243efb1cdd85", // 用户访问秘钥
        "client_group": "mobile",	// 客户端分组
        "client_id": "h5", // 客户端 ID
        "client_version": "1.0.0",	// 客户端版本号
        "expired_at": "2017-07-06 22:08:16",	// 秘钥失效时间
        "updated_at": "2017-07-06 22:08:16",   // 用户更新时间
        "created_at": "2017-07-06 22:08:16"     // 用户创建时间
    }
}
```

## 6. 示例

> POST /api/auth/register

**请求数据：**

```josn
{
  "member_phone" : "188****8888",
  "member_password" : "Hello7",
  "register_channel" : "mobile",
  "register_code" : "8099"
}
```

**响应结果：**

```josn
{
    "code": 200,
    "message": "操作成功",
    "data": {
        "member_id": 2,
        "access_token": "bc1016507a50341a2dad243efb1cdd85",
        "client_group": "mobile",
        "client_id": "h5",
        "client_version": "1.0.0",
        "expired_at": "2017-07-06 22:08:16",
        "updated_at": "2017-07-06 22:08:16",
        "created_at": "2017-07-06 22:08:16"
    }
}
```