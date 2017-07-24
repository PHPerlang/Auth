# 查询用户是否存在

[TOC]

## 1. API 描述：

根据手机、邮箱、用户名查询用户是否存在。

## 2. 调用方法

> POST /api/auth/check/member/exists

## 3. 请求参数

参数|解释|类型|是否必须|示例数据
:---|:---|:---|:---|:---
member_mobile | 用户手机 | number | 否 | `188****8888`
member_email|用户邮箱|string|否|`hi@gindowin.com`
member_account|用户名|string|否|`koyeo`
check_channel | 检查类型 | string |是| `mobile` <br> `email` <br> `username`

## 4. 响应状态

状态码 | 说明
:---:|:---
200 | 查询用户存在
1000 | 数据格式错误
1001 | 查询用户不存在

## 5. 响应数据

```json
{
    "code": 200,
    "message": "操作成功",
    "data":null
}
```

## 6. 示例

> POST /api/auth/check/member/exists


**响应结果：**

```josn
{
    "code": 200,
    "message": "查询用户存在",
    "data": null
}
```