@extends('admin::layout')
@section('content')
    @if(!$pjax)
        <div id="layout-navigation">
            <div id="layout-module-name">
                <span><i class="fa fa-user"></i></span>
                用户管理
            </div>

            <ul class="nav nav-pills nav-stacked">
                <li role="presentation" class="{{ $path == 'members' ? 'active' : '' }}">
                    <a href="/admin/auth/members">用户列表</a>
                </li>
                <li role="presentation" class="{{ $path == 'roles' ? 'active' : '' }}">
                    <a href="/admin/auth/roles">角色列表</a>
                </li>
                <li role="presentation" class="{{ $path == 'permissions' ? 'active' : '' }}">
                    <a href="/admin/auth/permissions">权限列表</a>
                </li>
                <li role="presentation" class="{{ $path == 'permissions' ? 'active' : '' }}">
                    <a href="/admin/auth/permissions">用户日志</a>
                </li>
            </ul>
        </div>
        <div id="layout-page"> @yield('layout-page')</div>
    @else
        @yield('layout-page')
    @endif

@endsection
