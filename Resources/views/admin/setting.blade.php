@extends('auth::admin.profile')

@section('page-header')
    <div class="navbar-header">
        <a class="navbar-brand" href="#">账户设置</a>
    </div>
@endsection


@section('layout-page')
    <div id="page">
        <div id="layout-content">
            <form>
                <br>
                <div class="container">
                    <div class="form-block"><i></i> 账户</div>
                </div>
                <div class="hr-line-dashed"></div>
                <div class="container">

                    @if($member->member_account)
                        <div class="form-group">
                            <label for="exampleInputEmail1">用户名</label>
                            <input disabled value="{{ $member->member_account }}" type="text" class="form-control"
                                   id="exampleInputEmail1" placeholder="">
                        </div>
                    @endif

                    @if($member->member_mobile)
                        <div class="form-group">
                            <label for="exampleInputEmail1">手机号</label>
                            <input disabled value="{{ $member->member_mobile }}" type="number" class="form-control"
                                   id="exampleInputEmail1" placeholder="">
                        </div>
                    @endif

                    @if($member->member_eamil)
                        <div class="form-group">
                            <label for="exampleInputEmail1">邮箱</label>
                            <input disabled value="{{ $member->member_eamil }}" type="text" class="form-control"
                                   id="exampleInputEmail1" placeholder="">
                        </div>
                    @endif

                </div>

                <div class="hr-line-dashed"></div>
                <div class="container">
                    <div class="form-block"><i></i> 密码</div>
                </div>
                <div class="hr-line-dashed"></div>
                <div class="container">

                    <div class="form-group">
                        <label for="exampleInputEmail1">原密码</label>
                        <input type="text" class="form-control" id="exampleInputEmail1" placeholder="">
                    </div>

                    <div class="form-group">
                        <label for="exampleInputEmail1">新密码</label>
                        <input type="text" class="form-control" id="exampleInputEmail1" placeholder="">
                    </div>

                    <div class="form-group">
                        <label for="exampleInputEmail1">确认新密码</label>
                        <input type="text" class="form-control" id="exampleInputEmail1" placeholder="">
                    </div>
                </div>
                <div class="hr-line-dashed"></div>
                <div class="container">
                    <div class="form-group">
                        <button class="btn btn-primary">保存</button>
                    </div>
                </div>
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