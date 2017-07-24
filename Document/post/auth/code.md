# 发送验证码

[TOC]

## 1. API 描述：

支持发送邮箱验证码和短信发送短信验证码，由 `send_channel` 区分。测试环境下验证码可在 `auth_log_codes` 表中查询。

## 2. 调用方法

> POST /api/auth/code

## 3. 请求参数

参数 | 解释 | 类型 | 是否必须 | 示例数据
:---:|:---|:---:|:---:|:---
member_email| 用户邮箱 | string | 当 `send_channel=email` 必须 | im@koyeo.io
member_phone| 手机号码 | number | 当`send_channel=mobile`时必须 | 188****8888
handler_token|处理器秘钥| string | 是 | `auth.register` 
send_channel | 标识注册类型 | string | 是 | `email` / `mobile`

## 4. 响应状态

状态码 | 说明
:---:|:---
200 | 操作成功

## 5. 响应数据

```json
{
    "code": 200,
    "message": "操作成功",
    "data": null
}
```

## 6. 示例

> POST /api/auth/code

**请求数据：**

```josn
{
  "member_email" : "188****8888",
  "handler_token": "auth.register",
  "send_channel" : "mobile"
}
```

**响应结果：**

```josn
{
  "code" : 200,
  "message" : "操作成功",
  "data" : null
}
```