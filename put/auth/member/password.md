# 设置新注册用户密码

[TOC]

## 1. API 描述：

在注册用户时，使用邮箱验证码来设置用户的密码

## 2. 调用方法

> POST /api/auth/member/password

## 3. 请求参数

参数 | 解释 | 类型 | 是否必须 | 示例数据
:---:|:---|:---:|:---:|:---
member_id | 用户 ID | int | 是 | 2
member_email | 用户邮箱 | string | 是 | im@koyeo.io
member_password | 用户密码 | string | 是 | 123456
register_code| 邮箱验证码 | string | 是 | 154523
register_type | 标识注册类型 | string | 是 | `email`

## 4. 响应状态

状态码 | 说明
:---:|:---
200 | 操作成功
1000 | 数据校验错误
1001 | 用户不存在
1300 | 邮箱验证码不正确

## 5. 响应数据

无

## 6. 示例

> POST /api/auth/member/password

### 请求数据：

```josn
{
  "member_email" : "im@koyeo.io",
  "member_password" : "123456",
  "register_code" : "148249",
  "register_type" : "email"
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