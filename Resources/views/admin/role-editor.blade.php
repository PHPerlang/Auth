@extends('auth::admin.layout')

@section('page-title')
    <ol class="breadcrumb">
        <li class="active"><a href="#">用户管理</a></li>
        <li class="active">添加角色</li>
    </ol>
@stop
@section('page-content')
    <div id="page-deploy-node-editor">
        <nav id="layout-page-handles" class="navbar navbar-toolbar" role="navigation">
            <div class="btn-toolbar" role="toolbar">
                <div class="btn-group">
                    <a href="/admin/auth/roles" class="btn btn-default">返回</a>
                </div>

                <a @click="submit()" class="pull-right btn btn-primary" href="javascript:;">添加</a>
            </div>
        </nav>

        <br>
    </div>
    <form>
        <div class="container">

            <div class="container">
                <div class="form-block"><i></i> 角色信息</div>
            </div>
            <div class="hr-line-dashed"></div>
            <div class="form-group">
                <label for="exampleInputEmail1">角色名</label>
                <input type="text" class="form-control" id="exampleInputEmail1" placeholder="">
            </div>
            <div class="form-group">
                <label for="exampleInputEmail1">权限时效</label>
                <input type="text" class="form-control" id="exampleInputEmail1" placeholder="">
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="exampleInputEmail1">生效时间</label>
                        <input type="text" class="form-control" id="exampleInputEmail1" placeholder="">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="exampleInputEmail1">失效时间</label>
                        <input type="text" class="form-control" id="exampleInputEmail1" placeholder="">
                    </div>
                </div>
            </div>
        </div>
        <div class="hr-line-dashed"></div>
        <div class="container">
            <div class="form-block"><i></i> 绑定权限</div>
        </div>
        <div class="hr-line-dashed"></div>
        <div class="container">
            <div class="form-group">
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