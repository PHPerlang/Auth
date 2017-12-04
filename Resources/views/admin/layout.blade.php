@extends('admin::layout')
@section('content')

        <div id="layout-navigation">
            <div id="layout-module-name">
                <span><i class="fa fa-users"></i></span>
                成员管理
            </div>

            <ul class="nav nav-pills nav-stacked">
                <li role="presentation" class="{{ $path == 'members' ? 'active' : '' }}">
                    <a href="/admin/auth/members">成员列表</a>
                </li>
                {{--<li role="presentation" class="{{ $path == 'roles' ? 'active' : '' }}">--}}
                    {{--<a href="/admin/auth/roles">角色列表</a>--}}
                {{--</li>--}}
                {{--<li role="presentation" class="{{ $path == 'permissions' ? 'active' : '' }}">--}}
                    {{--<a href="/admin/auth/permissions">权限列表</a>--}}
                {{--</li>--}}
                <li role="presentation" class="{{ $path == 'logs' ? 'active' : '' }}">
                    <a href="/admin/auth/permissions">操作日志</a>
                </li>
            </ul>
        </div>
        <div id="layout-page"> @yield('layout-page')</div>

@endsection
