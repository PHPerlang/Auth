# 校验图形验证码

[TOC]

## 1. API 描述：

校验图形验证码

## 2. 调用方法

> POST /api/auth/check/captcha

## 2. 请求头
请求头 | 描述
`X-Captcha-Token` | 通过 `GET /api/auth/captcha` 接口获取的图形验证码校验 Token

## 2. 请求参数
参数|解释|类型|是否必须|示例数据
:----|:---|:---|:---|:---
captcha_code | 图形验证码 | string | 是 | xrjs
captcha_token | 通过 `GET /api/auth/captcha` 接口获取的图形验证码校验 Token | string | 是 | -
## 3. 响应状态

状态码 | 说明
:---:|:---
200 | 获取成功
1001| 图形验证码不正确

## 4. 响应数据

```json
{
    "code": 200,
    "message": "验证成功",
    "data": {
    }
}
```

## 5. 示例

> POST /api/auth/check/captcha

**请求数据：**

```json
{
    "captcha": "x2dk"
}
```

**响应结果：**

```json
{
    "code": 200,
    "message": "验证成功",
    "data": {
    }
}
```