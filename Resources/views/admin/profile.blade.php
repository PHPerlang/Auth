@extends('admin::layout')
@section('content')
    @if(!$pjax)
        <div id="layout-navigation">
            <div id="layout-module-name">
                <span><i class="fa fa-id-card"></i></span>
                个人中心
            </div>
            <ul class="nav nav-pills nav-stacked">
                <li role="presentation" class="{{ $path == 'profile' ? 'active' : '' }}">
                    <a href="/admin/auth/member/profile">基本资料</a>
                </li>
                <li role="presentation" class="{{ $path == 'setting' ? 'active' : '' }}">
                    <a href="/admin/auth/member/setting">账户设置</a>
                </li>
                <li role="presentation" class="{{ $path == 'login-log' ? 'active' : '' }}">
                    <a href="/admin/auth/member/login/log">登录日志</a>
                </li>
            </ul>
        </div>
        <div id="layout-page"> @yield('layout-page')</div>
    @else
        @yield('layout-page')
    @endif
@endsection
