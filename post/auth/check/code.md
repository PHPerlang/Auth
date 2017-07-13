# 校验邮箱验证码或短信验证码

[TOC]

## 1. API 描述：

校验邮箱验证码或短信验证码

## 2. 调用方法

> POST /api/auth/check/code


## 3. 请求参数
参数|解释|类型|是否必须|示例数据
:----|:---|:---|:---|:---
member_phone | 用户手机 | number | 当 `auth_type=mobile` 时必须 | `188****8888`
member_email | 用户邮箱 | string | 当 `auth_type=email` 时必须` | `hello@gindowin.com`
auth_type | 验证类型 | string | 是 | `mobile` 、`email`
auth_scene | 校验场景 | string | 是 | `register_code` <br> `reset_password_code` <br> `change_email_code`
code | 手机验证码或邮箱验证码 | number | 是 | 123423

## 4. 响应状态

状态码 | 说明
:---:|:---
200 | 获取成功
1000 | 数据格式错误
1300|验证码不正确

## 5. 响应数据

```json
{
    "code": 200,
    "message": "验证成功",
    "data": {
    }
}
```

## 6. 示例

> POST /api/auth/check/code

**请求数据：**
{
	"auth_type": "mobile",
	"auth_scene": "register_code",
	"member_phone": 188****8888,
	"code": 122123
}

**响应结果：**

```json
{
    "code": 200,
    "message": "验证成功",
    "data": {
    }
}
```