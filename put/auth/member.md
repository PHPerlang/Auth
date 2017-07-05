# 注册时编辑用户资料

[TOC]

## 1. API 描述：

注册时编辑用户资料

## 2. 调用方法

> put /api/auth/member

## 3. 请求参数

参数 | 解释 | 类型 | 是否必须 | 示例数据
:---:|:---|:---:|:---:|:---
member_id | 用户 ID | int | 是 | 3
member_avatar | 用户头像链接地址 | string | 否 | http://example.com/avatar.png
member_nickname | 用户昵称 | string | 否 | 古月

## 4. 响应状态

状态码 | 说明
:---:|:---
200 | 操作成功
1001 | 用户不存在

## 5. 响应数据

无

## 6. 示例

> PUT /api/member?member_id=2

### 请求数据：

```josn
{
  "member_avatar" : "http://example.com/avatar.png",
  "member_nickname" : "古月"
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