@extends('admin::layout')
@section('content')

    <div id="layout-module-name">
        <i class="fa fa-user"></i> 用户管理
    </div>
    <div id="layout-navigation">
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
        </ul>
    </div>

    @yield('page-content')
@endsection
