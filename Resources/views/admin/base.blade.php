@extends('auth::admin.profile')

@section('layout-page')
    <div id="page">
        <div id="layout-page-header">
            <h1 class="pull-left">基本资料</h1>
            <div class="pull-right">
                <a @click="open_editor()" href="#" class="btn btn-primary">保存</a>
            </div>
        </div>
        <div id="layout-content">
            <form>
                <br>
                <div class="container">

                    <div class="row">
                        <div class="col-xs-6 col-md-3 col-xs-offset-3 col-md-offset-4">
                            <a href="#" class="thumbnail">
                                <img src="{{ asset('/modules/admin/avatar.png') }}" alt="avatar">
                            </a>
                        </div>
                    </div>
                </div>
                <div class="hr-line-dashed"></div>
                <div class="container">
                    <div class="form-block"><i></i> 基本信息</div>
                </div>
                <div class="hr-line-dashed"></div>
                <div class="container">

                    <div class="form-group">
                        <label for="exampleInputEmail1">姓名</label>
                        <input type="text" class="form-control" id="exampleInputEmail1" placeholder="">
                    </div>

                    <div class="form-group">
                        <label for="exampleInputEmail1">性别</label>
                        <input type="text" class="form-control" id="exampleInputEmail1" placeholder="">
                    </div>

                    <div class="form-group">
                        <label for="exampleInputEmail1">职务</label>
                        <input type="text" class="form-control" id="exampleInputEmail1" placeholder="">
                    </div>

                    <div class="form-group">
                        <label for="exampleInputEmail1">编号</label>
                        <input type="text" class="form-control" id="exampleInputEmail1" placeholder="">
                    </div>

                    <div class="form-group">
                        <label for="exampleInputEmail1">生日</label>
                        <input type="text" class="form-control" id="exampleInputEmail1" placeholder="">
                    </div>
                </div>

                {{--<div class="hr-line-dashed"></div>--}}
                {{--<div class="container">--}}
                {{--<div class="form-block"><i></i> 简介</div>--}}
                {{--</div>--}}
                {{--<div class="hr-line-dashed"></div>--}}
                {{--<div class="container">--}}
                {{--<div class="form-group">--}}
                {{--<textarea name="" id="" cols="30" rows="10" class="form-control"></textarea>--}}
                {{--</div>--}}
                {{--</div>--}}
            </form>
        </div>
    </div>
@endsection

@section('style')

@stop
@section('script')
    <script src="{{ asset('/modules/admin/vue/vue.min.js') }}"></script>
    <script>
        var form = {};
        var $data = {
            form: $.extend({}, form),
            roles: [],
            members: [],
            validate_pass: true,
            error: {}
        };

        new Vue({
            el: '#page',
            data: function () {
                return $data;
            },
            mounted: function () {
            },
            methods: {}
        });
    </script>
@stop