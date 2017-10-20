@extends('auth::admin.layout')

@section('page-title')
    <ol class="breadcrumb">
        <li class="active"><a href="#">用户管理</a></li>
        <li class="active">用户列表</li>
    </ol>
@stop
@section('layout-page')
    <div id="page">
        <div id="layout-page-header">
            <h1 class="pull-left">用户列表</h1>
            <div class="pull-right">
                <a @click="open_editor()" href="#" class="btn btn-primary">添加用户</a>
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
                                <a href="#">无效用户</a>
                            </li>
                        </ul>
                    </div>


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
                    {{--<th>用户名</th>--}}
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
                <template v-for="(member,index) in members">
                    <tr>
                        <td>@{{ member.member_id }}</td>
                        <td>@{{ member.member_name ? member.member_name : '-'}}</td>
                        {{--<td>@{{ member.account ? member.account : '-'}}</td>--}}
                        <td>@{{ member.member_email ? member.member_email : '-'}}</td>
                        <td>@{{ member.member_mobile ? member.member_mobile : '-'}}</td>
                        <td>管理员<br>会员</td>
                        <td>@{{ member.register_channel ? member.register_channel : '-'}}</td>
                        <td>@{{ member.register_source ? member.register_source : '-'}}</td>
                        <td>@{{ member.last_login ? member.last_login : '-'}}</td>
                        <td>@{{ member.created_at }}</td>
                        <td>
                            <div class="dropdown">
                                <div class="btn-group">
                                    <button class="btn btn-default">查看</button>
                                    <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                        <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenu1">
                                        <li><a href="#">重置密码</a></li>
                                        <li><a href="#">限制访问</a></li>
                                        <li><a href="#">删除用户</a></li>
                                    </ul>
                                </div>
                            </div>
                        </td>
                    </tr>
                </template>
                </tbody>
            </table>
        </div>

        <div id="member-editor" class="modal fade" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">添加用户</h4>
                    </div>
                    <div class="modal-body">
                        <br>
                        <form>
                            <div class="container">
                                <div class="form-block"><i></i> 用户信息</div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group" v-bind:class="{'has-error':error.member_name}">
                                <label for="">姓名</label>
                                <input v-model="form.member_name" type="text" class="form-control" id="" placeholder="">
                                <p class="help-block has-error"
                                   v-show="error.member_name && typeof error.member_name.required !== 'undefined'">
                                    姓名不能为空
                                </p>
                            </div>
                            <div class="form-group" v-bind:class="{'has-error':error.member_email}">
                                <label for="">用户邮箱</label>
                                <input v-model="form.member_email" type="text" class="form-control" id=""
                                       placeholder="">
                                <p class="help-block has-error"
                                   v-show="error.member_email && typeof error.member_email.required !== 'undefined'">
                                    用户邮箱不能为空
                                </p>
                            </div>
                            <div class="form-group" v-bind:class="{'has-error':error.member_mobile}">
                                <label for="">电话号码</label>
                                <input v-model="form.member_mobile" type="text" class="form-control" id=""
                                       placeholder="">
                                <p class="help-block has-error"
                                   v-show="error.member_mobile && typeof error.member_mobile.required !== 'undefined'">
                                    电话号码不能为空
                                </p>
                            </div>
                            <div class="form-group" v-bind:class="{'has-error':error.member_password}">
                                <label for="">密码</label>
                                <input v-model="form.member_password" type="password" class="form-control" id=""
                                       placeholder="">
                                <p class="help-block has-error"
                                   v-show="error.member_password && typeof error.member_password.required !== 'undefined'">
                                    密码不能为空
                                </p>
                            </div>
                            <div class="form-group">
                                <div class="checkbox">
                                    <label>
                                        <input v-model="form.reset_password" type="checkbox"> 登录后重置密码
                                    </label>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="container">
                                <div class="form-block"><i></i> 权限信息</div>
                            </div>
                            <div class="hr-line-dashed" style="border-top: 1px solid #e7eaec;"></div>
                            <table class="table hover">
                                <thead>
                                <tr>
                                    <th width="5%"><input type="checkbox"></th>
                                    <th width="20%">角色名</th>
                                    <th>角色描述</th>
                                    <th class="text-center" width="8%">角色时效</th>
                                    <th class="text-center" width="15%">过期时间</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="role in roles">
                                    <td><input v-model="role.checked" type="checkbox"></td>
                                    <td>@{{ role.role_name }}</td>
                                    <td>@{{ role.role_desc ? role.role_desc : '-' }}</td>
                                    <td class="text-center">
                                        <if v-if="role.role_type===1">
                                            长期
                                        </if>
                                        <if v-if="role.role_type===2">
                                            临时
                                        </if>
                                    </td>
                                    <td class="text-center">@{{ role.expired_at ? role.expired_at : '-' }}</td>
                                </tr>
                                </tbody>
                            </table>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                        <button @click="submit()" type="button" class="btn btn-primary">添加</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('style')
    <style>
        #member-editor table {
            margin-top: -15px;
            border-bottom: none;
        }

        #member-editor .modal-body {
            padding: 0;
        }

        #member-editor .form-group {
            padding: 0 15px;
        }

        #member-editor .form-block {
            margin-left: 15px;
        }
    </style>
@stop
@section('script')
    <script src="{{ asset('/modules/admin/vue/vue.min.js') }}"></script>
    <script id="members-json" type="application/json">{!! json_encode($members) !!}</script>
    <script id="roles-json" type="application/json">{!! json_encode($roles) !!}</script>
    <script>
        var form = {
            'member_id': null,
            'member_name': null,
            'member_email': null,
            'member_mobile': null,
            'member_password': null,
            'reset_password': false,
            'member_roles': []
        };
        var $data = {
            form: $.extend({}, form),
            roles: [],
            members: [],
            validate_pass: true,
            error: {
                member_name: null,
                member_email: null,
                member_mobile: null,
                member_password: null
            }
        };
        var $member_editor = null;
        var $body = null;

        new Vue({
            el: '#page',
            data: function () {
                return $data;
            },
            mounted: function () {
                $body = $('body');
                $member_editor = $('#member-editor');
                $data.roles = JSON.parse($('#roles-json').html());
                $data.members = JSON.parse($('#members-json').html());
            },
            methods: {
                submit: function () {

                    for (var i in $data.roles) {
                        if ($data.roles[i].checked) {
                            $data.form.member_roles.push($data.roles[i].role_id);
                        }
                    }

                    $data.validate_pass = true;

                    this.validate_member_name();
                    this.validate_member_email();
                    this.validate_member_mobile();
                    this.validate_member_password();

                    if ($data.validate_pass) {

                        $member_editor.modal('hide');
                        $body.loading();

                        $do.post({
                            url: '/api/auth/member',
                            data: $data.form,
                            success: function (res) {
                                if (res.code === 200) {
                                    $do.success('用户添加成功');
                                    $data.members.push(res.data);
                                    $member_editor.modal('hide');
                                } else if (res.code === 1000) {
                                    $data.error = $.extend($data.error, res.data);
                                    $member_editor.modal();
                                } else {
                                    $member_editor.modal();
                                }
                                $body.loading('stop');
                            },
                            error: function () {
                                $member_editor.modal();
                                $body.loading('stop');
                            }
                        });
                    }
                },
                open_editor: function () {
                    $data.form = $.extend({}, form);
                    for (var i in $data.roles) {
                        $data.roles[i].checked = false;
                    }
                    $member_editor.modal();
                },
                validate_member_name: function () {
                    if ($.trim($data.form.member_name) === '') {
                        $data.error.member_name = {};
                        $data.error.member_name.required = true;
                        $data.validate_pass = false;
                    } else {
                        $data.error.member_name = null;
                    }
                },
                validate_member_email: function () {
                    if ($.trim($data.form.member_email) === '') {
                        $data.error.member_email = {};
                        $data.error.member_email.required = true;
                        $data.validate_pass = false;
                    } else {
                        $data.error.member_email = null;
                    }
                },
                validate_member_mobile: function () {
                    if ($.trim($data.form.member_mobile) === '') {
                        $data.error.member_mobile = {};
                        $data.error.member_mobile.required = true;
                        $data.validate_pass = false;
                    } else {
                        $data.error.member_mobile = null;
                    }
                },
                validate_member_password: function () {
                    if ($.trim($data.form.member_password) === '') {
                        $data.error.member_password = {};
                        $data.error.member_password.required = true;
                        $data.validate_pass = false;
                    } else {
                        $data.error.member_password = null;
                    }
                }
            },
            watch: {
                'form.member_name': function () {
                    this.validate_member_name();
                },
                'form.member_email': function () {
                    this.validate_member_email();
                },
                'form.member_mobile': function () {
                    this.validate_member_mobile();
                },
                'form.member_password': function () {
                    this.validate_member_password();
                }
            }
        });
    </script>
@stop