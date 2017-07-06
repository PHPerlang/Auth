# 忘记密码发送验证码

[TOC]

## 1. API 描述：

通过发送邮箱验证码找回用户密码。发送的邮箱验证码可以在 `email_codes` 表中查询。

## 2. 调用方法

> POST /api/auth/reset/password/code

## 3. 请求参数

参数 | 解释 | 类型 | 是否必须 | 示例数据
:---:|:---|:---:|:---:|:---
参数名 | 参数解释 | string | 是 | 示例数据
member_email | 用户邮箱 | string | 是 | im@koyeo.io
find_passwrod_type | 找回密码方式 | string | 是 | email


## 4. 响应状态

状态码 | 说明
:---:|:---
200 | 密码重置验证码发送成功
2000 | 该找回密码通道已关闭
2001 | 注册码已超出最大发送次数，请明天再试
2002 | 发送太频繁，请 60 秒后再试
2010 | 该用户不存在

## 5. 响应数据

参数 | 解释 | 类型 | 示例数据
:---:|:---|:---:|:---
参数名 | 参数解释 | string | 示例数据

## 6. 示例

> POST /api/auth/reset/password/code

### 请求数据：

```josn
{
  "member_email" : "im@koyeo.io",
  "find_passwrod_type":"email"
}
```

### 响应结果：

```josn
{
  "code" : 200,
  "message" : 操作成功",
  "data" : null
}
```