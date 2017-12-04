@extends('admin::layout')

@section('content')
    <div id="page">
        <div id="layout-page-header">
            <h1 class="pull-left">用户列表</h1>
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
                                    <button class="btn btn-default">限制访问</button>
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
                $data.members = JSON.parse($('#members-json').html());
            },
            methods: {

            }
        });
    </script>
@stop