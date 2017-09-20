@extends('auth::admin.layout')

@section('page-title')
    <ol class="breadcrumb">
        <li class="active"><a href="#">用户管理</a></li>
        <li class="active">添加用户</li>
    </ol>
@stop
@section('page-content')
    <div id="page-deploy-node-editor">
        <nav id="layout-page-handles" class="navbar navbar-toolbar" role="navigation">
            <div class="btn-toolbar" role="toolbar">
                <div class="btn-group">
                    <a href="/admin/auth/members" class="btn btn-default">返回</a>
                </div>

                <a @click="submit()" class="pull-right btn btn-primary" href="javascript:;">添加</a>
            </div>
        </nav>

        <br>
    </div>
    <form>
        <div class="container">

            <div class="container">
                <div class="form-block"><i></i> 用户信息</div>
            </div>
            <div class="hr-line-dashed"></div>
            <div class="form-group">
                <label for="exampleInputEmail1">用户名</label>
                <input type="text" class="form-control" id="exampleInputEmail1" placeholder="">
            </div>
            <div class="form-group">
                <label for="exampleInputEmail1">用户邮箱</label>
                <input type="text" class="form-control" id="exampleInputEmail1" placeholder="">
            </div>
            <div class="form-group">
                <label for="exampleInputEmail1">电话号码</label>
                <input type="text" class="form-control" id="exampleInputEmail1" placeholder="">
            </div>
            <div class="form-group">
                <label for="exampleInputEmail1">密码</label>
                <input type="text" class="form-control" id="exampleInputEmail1" placeholder="">
            </div>
        </div>
        <div class="hr-line-dashed"></div>
        <div class="container">
            <div class="form-block"><i></i> 权限信息</div>
        </div>
        <div class="hr-line-dashed"></div>
        <div class="container">
            <div class="form-group">
                <label for="exampleInputEmail1">选择角色</label>
                <select class="form-control" name="" id="">
                    <option value=""></option>
                </select>
            </div>
        </div>
    </form>
@endsection

@section('style')
@stop
@section('script')

@stop