# 忘记密码发送验证码

[TOC]

## 1. API 描述：

通过发送邮箱验证码或手机验证码找回用户密码。验证码可以在 `email_codes` 或 `sms_codes` 表中查询。

## 2. 调用方法

> POST /api/auth/reset/password/code

## 3. 请求参数

参数 | 解释 | 类型 | 是否必须 | 示例数据
:---:|:---|:---:|:---:|:---
参数名 | 参数解释 | string | 是 | 示例数据
member_email | 用户邮箱 | string | 当 `find_password_type=email` 时必须 | `im@koyeo.io`
member_phone | 用户手机 | string | 当 `find_password_type=mobile` 时必须 |`188****8888`
find_passwrod_type | 找回密码方式 | string | 是 | `email` 或 `mobile`


## 4. 响应状态

状态码 | 说明
:---:|:---
200 | 密码重置验证码发送成功
2000 | 该找回密码通道已关闭
2001 | 注册码已超出最大发送次数，请明天再试
2002 | 发送太频繁，请 60 秒后再试
2010 | 该用户不存在

## 5. 响应数据

```josn
{
  "member_phone" : "188****8888",
  "find_passwrod_type":"mobile"
}
```

## 6. 示例

> POST /api/auth/reset/password/code

**请求数据：**

```josn
{
  "member_phone" : "188****8888",
  "find_passwrod_type":"mobile"
}
```

**响应结果：**

```josn
{
  "code" : 200,
  "message" : 操作成功",
  "data" : null
}
```