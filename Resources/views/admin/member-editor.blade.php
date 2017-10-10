@extends('auth::admin.layout')

@section('page-title')
    <ol class="breadcrumb">
        <li class="active"><a href="#">用户管理</a></li>
        <li class="active">添加用户</li>
    </ol>
@stop
@section('page-content')
    <div id="page-member-editor">
        <div id="layout-page-header">
            <h1 class="pull-left">添加用户</h1>
            <div class="pull-right">
                <a @click="submit()" class="pull-right btn btn-primary" href="javascript:;">添加</a>
            </div>
        </div>
        {{--<nav id="layout-page-handles" class="navbar navbar-toolbar" role="navigation">--}}
        {{--<div class="btn-toolbar" role="toolbar">--}}
        {{--<div class="wrap btn-group">--}}
        {{--<a href="/admin/auth/members" class="btn btn-default">返回</a>--}}
        {{--</div>--}}
        {{--<a @click="submit()" class="pull-right btn btn-primary" href="javascript:;">添加</a>--}}
        {{--</div>--}}
        {{--</nav>--}}
        <div id="layout-content">

        </div>
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