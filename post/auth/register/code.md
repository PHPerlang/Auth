# 用户注册发送邮件验证码

[TOC]

## 1. API 描述：

用户注册发送邮箱验证码，**可在 `数据表 email_codes` 中查看邮件验证码。**

## 2. 调用方法

> POST /api/auth/register/code

## 2. 请求参数

参数 | 解释 | 类型 | 是否必须 | 示例数据
:---:|:---|:---:|:---:|:---
member_email| 用户邮箱 | string | 是 | im@koyeo.io
captcha | 图像验证码，一个邮箱每天超过3次发送验证码时，要求输入图形验证码 | string | 否 | 6c5a70
register_type | 标识注册类型 | string | 是 | `email`

## 3. 响应状态

状态码 | 说明
:---:|:---
200 | 验证码发送成功
1000 | 邮箱格式不正确
1001 | 邮箱或手机不能为空
2000 | 该注册类型通道已关闭
2001 |注册码已超出最大发送次数，请明天再试
2002| 发送太频繁，请 60 秒后再试
3001 | 图形验证码不正确
3002 | 该邮箱已注册



## 4. 响应数据

null

## 5. 示例

> POST /api/auth/register/code

### 请求数据：

```josn
{
  "member_email" : "im@koyeo.io",
  "register_type" : "email",
  "captcha" : "",
}
```

### 响应结果：

```josn
{
  "code" : 200,
  "message" : "用户注册成功",
  "data" : null
}
```