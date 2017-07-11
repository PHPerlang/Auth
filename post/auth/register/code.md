# 用户注册发送验证码

[TOC]

## 1. API 描述：

支持邮箱发送验证码和短信发送验证码，由 `register_type` 区分。测试环境下验证码可在 `email_codes` 或 `sms_codes` 中查询。

## 2. 调用方法

> POST /api/auth/register/code

## 2. 请求参数

参数 | 解释 | 类型 | 是否必须 | 示例数据
:---:|:---|:---:|:---:|:---
member_email| 用户邮箱 | string | 当 `register_type=email` 必须 | im@koyeo.io
member_phone| 手机号码 | number | 当`register_type=mobile`时必须 | 188****8888
register_type | 标识注册类型 | string | 是 | `email` / `mobile`
captcha（预留字段） | 图像验证码，一个邮箱每天超过3次发送验证码时，要求输入图形验证码 | string | 否 | 6c5a70


## 3. 响应状态

状态码 | 说明
:---:|:---
200 | 验证码发送成功
1000 | 数据格式不正确
1001 | 邮箱或手机不能为空
2000 | 该注册类型通道已关闭
2001 | 注册码已超出最大发送次数，请明天再试
2002 | 发送太频繁，请 60 秒后再试
3001 | 图形验证码不正确
3002 | 该邮箱已注册
3003 | 该手机号已注册
3004 | 短信网络配置错误



## 4. 响应数据

```json
{
    "code": 200,
    "message": "验证码发送成功",
    "data": null
}
```

## 5. 示例

> POST /api/auth/register/code

**请求数据：**

```josn
{
  "member_email" : "188****8888",
  "register_type" : "email",
  "captcha" : "",
}
```

**响应结果：**

```josn
{
  "code" : 200,
  "message" : "验证码发送成功",
  "data" : null
}
```