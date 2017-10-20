@extends('auth::admin.layout')

@section('page-title')
    <ol class="breadcrumb">
        <li class="active"><a href="#">用户管理</a></li>
        <li class="active">角色列表</li>
    </ol>
@stop
@section('layout-page')

    <div id="layout-page-header">
        <h1 class="pull-left">角色列表</h1>
        <div class="pull-right">
        </div>
    </div>

    <div id="layout-content">
        <nav id="layout-page-handles" class="navbar navbar-toolbar" role="navigation">
            <div class="btn-toolbar" role="toolbar">
                <div class="btn-group">
                    <a class="btn btn-default dropdown-toggle" data-toggle="dropdown" href="#">
                        筛选 <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="#">管理员</a>
                        </li>
                    </ul>
                </div>

                {{--<a href="/admin/auth/role/editor" class="btn btn-primary">添加角色</a>--}}


                {{--<div class="pull-right btn-group">--}}
                {{--<a href="#" class="btn btn-default"><i class="glyphicon glyphicon-chevron-left"></i></a>--}}
                {{--<a href="#" class="btn btn-default"><i class="glyphicon glyphicon-chevron-right"></i></a>--}}
                {{--</div>--}}

                <a href="#" class="pagination-count pull-right btn">共 10 个角色 </a>
            </div>
        </nav>
        <table class="table hover">
            <thead>
            <tr>
                <th>ID</th>
                <th>角色名</th>
                <th>角色状态</th>
                <th>角色时效</th>
                <th>权限数</th>
                <th>角色描述</th>
                <th>所属模块</th>
                <th>创建时间</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            @foreach($roles as $role)
                <tr>
                    <td>{{ $role->role_id }}</td>
                    <td>{{ $role->role_name }}</td>
                    <td>{{ $role->role_status = 1 ? '激活' : '停用' }}</td>
                    <td>{{ $role->role_type = 1 ? '永久角色' : '临时角色' }}</td>
                    <td>{{ $role->permission_amount }}</td>
                    <td>{{ $role->role_desc }}</td>
                    <td>{{ $role->module ? $role->module : '-' }}</td>
                    <td>{{ $role->created_at }}</td>
                    <td>
                        <div class="dropdown">
                            <div class="btn-group">

                                <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                    操作
                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenu1">
                                    <li><a href="#">相关用户</a></li>
                                    <li><a href="#">用户通知</a></li>
                                    <li role="separator" class="divider"></li>
                                    <li><a href="#">停用</a></li>
                                </ul>
                            </div>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection

@section('style')
@stop
@section('script')

@stop