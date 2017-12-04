@extends('auth::admin.profile')

@section('layout-page')
    <div id="page">
        <div id="layout-page-header">
            <h1 class="pull-left">登录日志</h1>

            <div class="pull-right btn-group">
                <a href="#" class="btn btn-default"><i class="glyphicon glyphicon-chevron-left"></i></a>
                <a href="#" class="btn btn-default"><i class="glyphicon glyphicon-chevron-right"></i></a>
            </div>

            <a href="#" class="pagination-count pull-right btn">第 1 - 5 条记录，共 105 条 </a>
        </div>
        <div id="layout-content">

            <table class="table hover">
                <thead>
                <tr>
                    <th>登录时间</th>
                    <th>IP 地址</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>2017-12-25</td>
                    <td>127.0.0.1</td>
                </tr>
                </tbody>
            </table>
        </div>
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