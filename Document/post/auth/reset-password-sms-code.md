# 用户注册发送短信验证码

[TOC]

## 1. API 描述：

测试环境下验证码可在 `auth_log_codes` 中查询。

## 2. 调用方法

> POST /api/auth/code

## 3. 请求参数

参数 | 解释 | 类型 | 是否必须 | 示例数据
:---:|:---|:---:|:---:|:---
member_mobile| 手机号码 | number | 是 | 188****8888
send_channel | 标识注册类型 | string | 是 | `mobile`
handler_token | 处理器秘钥 | string | 是 | `auth.reset.password`


## 4. 响应状态

状态码 | 说明
:---:|:---
200|  操作成功
1000| 数据格式不正确
1100 | 通道不支持
1500 | 短信服务商错误
2020 | 注册码已超出最大发送次数，请明天再试
2010 | 发送太频繁，请 60 秒后再试




## 5. 响应数据

```json
{
    "code": 200,
    "message": "验证码发送成功",
    "data": null
}
```

## 6. 示例

> POST /api/auth/code

**请求数据：**

```josn
{
  "member_email" : "188****8888",
  "send_channel" : "mobile",
  "handler_token" : "auth.reset.password",
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