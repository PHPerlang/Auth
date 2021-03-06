# 通过验证码重置密码

[TOC]

## 1. API 描述：

通过邮箱验证码或短信验证码重置用户密码，开发环境下验证码可以在 `auth_code_logs` 表中查询。

## 2. 调用方法

> PUT /api/auth/reset/password

## 3. 请求参数

参数 | 解释 | 类型 | 是否必须 | 示例数据
:---:|:---|:---:|:---:|:---
参数名 | 参数解释 | string | 是 | 示例数据
member_email | 用户邮箱 | string | 条件控制 | im@koyeo.io
member_mobile | 用户邮箱 | string | 条件控制 | im@koyeo.io
member_password | 用户新密码 | string | 是 | 123456
find_password_channel | 找回密码方式 | string | 是 | `email`、`mobile`
reset_code | 重置密码验证码 | string | 是 | 294495


## 4. 响应状态

状态码 | 说明
:---:|:---
200 | 重置密码成功
1000 | 数据校验失败
1300 | 验证码不正确
2000 | 该找回密码通道已关闭

## 5. 响应数据

```json
{
    "code": 200,
    "message": "操作成功",
    "data": null
}
```

## 6. 示例

> POST /api/auth/reset/password

**请求数据：**

```josn
{
  "member_mobile" : "188****1823",
  "find_password_channel":"mobile",
  "reset_code" : "用户重置密码验证码",
  "member_password" : "123456"
}
```

** 响应结果：**

```josn
{
  "code" : 200,
  "message" : 操作成功",
  "data" : null
}
```