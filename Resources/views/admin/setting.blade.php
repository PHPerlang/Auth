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
                        <label>原密码</label>
                        <input v-model="form.origin_password" type="password" class="form-control" placeholder="">
                    </div>

                    <div class="form-group">
                        <label>新密码</label>
                        <input v-model="form.new_password" type="password" class="form-control" placeholder="">
                    </div>

                    <div class="form-group">
                        <label>确认新密码</label>
                        <input v-model="form.confirm_password" type="password" class="form-control" placeholder="">
                    </div>
                </div>
                <div class="hr-line-dashed"></div>
                <div class="container">
                    <div class="form-group">
                        <button type="button" @click="submit_password()" class="btn btn-primary">保存</button>
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
        var form = {
            origin_password: null,
            new_password: null,
            confirm_password: null
        };
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
            methods: {
                submit_password: function () {

                    if ($data.form.new_password !== $data.form.confirm_password) {
                        $do.warning('两次新密码不一致');
                        return;
                    }

                    $do.put({
                        url: '/api/auth/password',
                        data: $data.form,
                        success: function (res) {
                            if (res.code === 200) {
                                $do.success('密码修改成功，请重新登录');
                                setTimeout(function () {
                                    location.href = '/admin/logout';
                                }, 2000);
                            } else {
                                $do.warning(res.message);
                            }
                        }
                    })
                }
            }
        });
    </script>
@stop