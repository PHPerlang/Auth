@extends('auth::admin.layout')

@section('page-title')
    <ol class="breadcrumb">
        <li class="active"><a href="#">用户管理</a></li>
        <li class="active">用户列表</li>
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

            <a href="/admin/auth/member/editor" class="btn btn-primary">添加用户</a>


            <div class="pull-right btn-group">
                <a href="#" class="btn btn-default"><i class="glyphicon glyphicon-chevron-left"></i></a>
                <a href="#" class="btn btn-default"><i class="glyphicon glyphicon-chevron-right"></i></a>
            </div>

            <a href="#" class="pagination-count pull-right btn">第 1 - 5 位用户，共 105 位 </a>

        </div>
    </nav>

    <table class="table hover">
        <thead>
        <tr>
            <th>ID</th>
            <th>姓名</th>
            <th>用户名</th>
            <th>邮箱</th>
            <th>电话号码</th>
            <th>角色列表</th>
            <th>注册方式</th>
            <th>注册来源</th>
            <th>注册时间</th>
            <th>上次登录</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        @foreach($members as $member)
            <tr>
                <td>{{ $member->member_id }}</td>
                <td>{{ $member->member_name ? $member->member_name : '-'}}</td>
                <td>{{ $member->account ? $member->account : '-'}}</td>
                <td>{{ $member->member_email ? $member->member_email : '-'}}</td>
                <td>{{ $member->member_mobile ? $member->member_mobile : '-'}}</td>
                <td>管理员<br>会员</td>
                <td>{{ $member->register_channel ? $member->register_channel : '-'}}</td>
                <td>{{ $member->register_source ? $member->register_source : '-'}}</td>
                <td>{{ $member->last_login ? $member->last_login : '-'}}</td>
                <td>{{ $member->created_at }}</td>
                <td>
                    <div class="dropdown">
                        <div class="btn-group">
                            <button class="btn btn-default">编辑</button>
                            <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenu1">
                                <li><a href="#">登录日志</a></li>
                                <li><a href="#">加入黑名单</a></li>
                                <li><a href="#">删除用户</a></li>
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