# 获取图形验证码

[TOC]

## 1. API 描述：

调用需要图形验证码的接口，图形验证码从此接口获取

## 2. 调用方法

> GET /api/auth/captcha

## 2. 请求参数

无

## 3. 响应状态

状态码 | 说明
:---:|:---
200 | 获取成功

## 4. 响应数据

```json
{
    "code": 200,
    "message": "操作成功",
    "data": {
        "captcha_src": "http://127.0.0.1:8000/api/auth/captcha/default?VZTwt5OM",   // 图形验证码地址
        "captcha_token": "198c4ef13b134acb21a4ca5cc8056d92" // 图形验证码 TOKEN
    }
}
```

## 5. 示例

> POST /api/auth/captcha

**请求数据：**

无

**响应结果：**

```josn
{
    "code": 200,
    "message": "操作成功",
    "data": {
        "captcha_src": "http://127.0.0.1:8000/api/auth/captcha/default?VZTwt5OM",
        "captcha_token": "198c4ef13b134acb21a4ca5cc8056d92"
    }
}
```