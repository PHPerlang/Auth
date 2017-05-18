<?php

return array (
  'post@api/core/auth/register/code' => 
  array (
    200 => '注册码发送成功',
    1000 => '邮箱格式不正确',
    1001 => '图形验证码不正确',
    1002 => '该邮箱已注册',
  ),
  'post@api/core/auth/register' => 
  array (
    200 => '注册成功',
    1000 => '数据校验失败',
    1001 => '邮箱验证码不正确',
    1002 => '该邮箱已注册',
  ),
  'post@api/core/auth/member/new/password' => 
  array (
    200 => '注册成功',
    1001 => '该用户不存在',
  ),
  'post@api/core/auth/login' => 
  array (
    200 => 'Login Success！',
    1000 => '数据校验失败',
    1001 => '邮箱或密码不正确',
  ),
  'post@api/core/auth/forgot/password' => 
  array (
    200 => 'Send account password reset link success',
  ),
  'post@api/core/auth/logout' => NULL,
  'get@api/core/auth/forgot/password/link' => NULL,
  'post@api/core/member' => 
  array (
    200 => 'Add member success!',
  ),
  'put@api/core/member/{member_id}' => 
  array (
    200 => '更新用户信息成功',
    1001 => '用户不存在',
  ),
  'get@api/core/member/{member_id}' => NULL,
  'get@api/core/members' => NULL,
  'delete@api/core/member' => NULL,
  'get@api/core/role' => NULL,
);