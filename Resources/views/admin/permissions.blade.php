@extends('auth::admin.layout')

@section('page-title')
    <ol class="breadcrumb">
        <li class="active"><a href="#">用户管理</a></li>
        <li class="active">权限列表</li>
    </ol>
@stop
@section('page-content')
    <nav id="layout-page-handles" class="navbar navbar-toolbar" role="navigation">
        <div class="btn-toolbar" role="toolbar">

            <div class="btn-group">
                <a class="btn btn-default dropdown-toggle" data-toggle="dropdown" href="#">
                    筛选 <span class="caret"></span>
                </a>
                <ul class="dropdown-menu">
                    <li>
                        <a href="#">节点记录</a>
                    </li>
                </ul>
            </div>

            <div class="pull-right btn-group">
                <a href="#" class="btn btn-default"><i class="glyphicon glyphicon-chevron-left"></i></a>
                <a href="#" class="btn btn-default"><i class="glyphicon glyphicon-chevron-right"></i></a>
            </div>

            <a href="#" class="pagination-count pull-right btn">第 1 - 5 条部署记录，共 105 条 </a>

        </div>
    </nav>

    <table class="table hover">
        <thead>
        <tr>
            <th>权限名</th>
            <th>权限描述</th>
            <th>标识符</th>
            <th>限制参数</th>
            <th>所属模块</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        @foreach($permissions as $permission)
            <tr>
                <td>{{ $permission->permission_name }}</td>
                <td>{{ $permission->permission_desc ? $permission->permission_desc : '-' }}</td>
                <td>{{ $permission->permission_id }}</td>
                <td>-</td>
                <td>{{ $permission->module }}</td>
                <td>
                    <div class="dropdown">
                        <div class="btn-group">
                            <button class="btn btn-default">操作</button>
                            <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenu1">
                                <li><a href="#">测试</a></li>
                                <li><a href="#">访问日志</a></li>
                            </ul>
                        </div>
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection

@section('style')
@stop
@section('script')

@stop