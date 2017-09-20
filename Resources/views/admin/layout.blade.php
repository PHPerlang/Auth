@extends('admin::layout')
@section('content')

    <div id="layout-module-name">
        <i class="fa fa-user"></i> 用户管理
    </div>
    <div id="layout-navigation">
        <ul class="nav nav-pills nav-stacked">
            <li role="presentation" class="active">
                <a href="/admin/deploy/records">用户列表</a>
            </li>
            <li role="presentation" class="">
                <a href="/admin/deploy/nodes">角色列表</a>
            </li>
            <li role="presentation" class="">
                <a href="/admin/deploy/scripts">权限列表</a>
            </li>
        </ul>
    </div>

    @yield('page-content')
@endsection
