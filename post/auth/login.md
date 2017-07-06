# 用户登录

[TOC]

## 1. API 描述：

用户通过手机、邮箱、或用户名登录，需要开启对应的登录通道。

## 2. 调用方法

> POST /api/auth/login

## 2. 请求参数

参数 | 解释 | 类型 | 是否必须 | 示例数据
:---:|:---|:---:|:---:|:---
member_email | 用户邮箱 | string | `login_type=email` | `im@koyeo.io`
member_email | 用户手机 | number | `login_type=mobile` | `188****8888`
member_account | 用户名 | string | `login_type=username` | `koyeo`
member_password | 用户密码，不低于 6 位 | string | 是 | x123456
captcha（预留字段） | 图形验证码，当用户输入账户密码错误 3 次后需要 | string | 否 |  x7fj
login_type | 标识登录类型 | string | 是 | `email`/`mobile`/`username`

## 3. 响应状态

状态码 | 说明
:---:|:---
200 | 操作成功
1000 | 表单数据校验失败，包括邮箱校验，密码校验
1001 | 账号或密码不正确
2000 | 该登录类型通道已关闭

## 4. 响应数据

参数 | 解释 | 类型 | 示例数据
:---:|:---|:---:|:---
access_token | 用户的系统接入秘钥 | string | 1e10adc3949ba59abbe56e057f20f883e
client_id | 客户端 ID | string | h5
client_group | 客户端分组，系统针对不同的客户端分组，实现多点登录 | string | mobile
client_version | 客户端版本号 | string | 1.0.0
created_at | 用户第一次登陆时间 | string | 2017-04-26 18:54:00
updated_at | 用户最近登陆时间 | string | 2017-04-26 18:54:00
expired_at | 登陆有效期截止时间，如果最近一次登陆时间与有效期时间相等，表示永久有效 | string | 2017-04-26 18:54:00

## 5. 示例

> POST /api/auth/login

**请求数据：**

```josn
{
   "member_email" : "188****8888",
   "member_password" : "123456",
   "login_type" : "mobile"
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