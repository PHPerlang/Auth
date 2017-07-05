# 用户注册

[TOC]

## 1. API 描述：

用户填入邮件里收到的邮箱验证码，和用户邮箱及密码，完成用户注册。

## 2. 调用方法

> POST /api/auth/register

## 2. 请求参数

参数 | 解释 | 类型 | 是否必须 | 示例数据
:---:|:---|:---:|:---:|:---
member_email | 用户邮箱 | string | 是 | im@koyeo.io
member_password | 用户密码，6~32 位，且必须是数字字母的组合，如果没有密码，系统将分配随机密码 | string | 否 | Hello7
register_type | 标识注册类型 | string | 是 | `email`
register_code | 邮箱里接收的验证码 | string | 是 | 089463

## 3. 响应状态

状态码 | 说明
:---:|:---
200 | 注册成功
1000 | 数据校验失败
1300 | 验证码不正确
2000 | 该注册类型通道已关闭
3001 | 图形验证码不正确
3002 | 该邮箱已注册

## 4. 响应数据

参数 | 解释 | 类型 | 是否必须 | 示例数据
:---:|:---|:---:|:---:|:---
参数名 | 参数解释 | string | 是 | 示例数据 

## 5. 示例

> POST /api/auth/register

### 请求数据：

```josn
{
  "member_email" : "im@koyeo.io",
  "member_password" : "Hello7",
  "register_type" : "email",
  "register_code" : "809990"
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