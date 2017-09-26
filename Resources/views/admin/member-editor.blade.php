@extends('auth::admin.layout')

@section('page-title')
    <ol class="breadcrumb">
        <li class="active"><a href="#">用户管理</a></li>
        <li class="active">添加用户</li>
    </ol>
@stop
@section('page-content')
    <div id="page-member-editor">
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
                    <label for="">姓名</label>
                    <input v-model="form.member_name" type="text" class="form-control" id="" placeholder="">
                </div>
                <div class="form-group">
                    <label for="">用户名</label>
                    <input v-model="form.member_account" type="text" class="form-control" id="" placeholder="">
                </div>
                <div class="form-group">
                    <label for="">用户邮箱</label>
                    <input v-model="form.member_email" type="text" class="form-control" id="" placeholder="">
                </div>
                <div class="form-group">
                    <label for="">电话号码</label>
                    <input v-model="form.member_mobile" type="text" class="form-control" id="" placeholder="">
                </div>
                <div class="form-group">
                    <label for="">密码</label>
                    <input v-model="form.member_password" type="password" class="form-control" id="" placeholder="">
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
    </div>
@endsection

@section('style')
@stop
@section('script')
    <script src="{{ asset('/modules/template/vue/vue.min.js') }}"></script>
    <script>

        var member = {
            'member_id': null,
            'member_name': null,
            'member_account': null,
            'member_email': null,
            'member_mobile': null,
            'member_password': null,
            'member_roles': []
        };

        var $data = {
            form: $.extend({}, member),
            error: {}
        };
        var $body = null;
        var app = new Vue({
            el: '#page-member-editor',
            mounted: function () {

                $body = $('body');
            },
            data: function () {

                return $data;
            },
            methods: {
                submit: function () {

                    $body.loading();

                    $do.post({
                        url: '/api/auth/member',
                        data: $data.form,
                        success: function (res) {
                            if (res.code === 200) {
                                $data.form = $.extend({}, member);
                                $do.success('用户添加成功');
                            } else if (res.code === 1000) {

                            }
                            $body.loading('stop');
                        },
                        error: function () {
                            $body.loading('stop');
                        }
                    })
                }
            }
        });
    </script>
@stop