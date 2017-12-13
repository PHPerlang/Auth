@extends('auth::admin.profile')

@section('page-header')
    <div class="navbar-header">
        <a class="navbar-brand" href="#">登录日志</a>
    </div>
@endsection

@section('layout-page')
    <div id="page">

        <div id="layout-content">
            <nav id="layout-page-handles" class="navbar navbar-toolbar" role="navigation">
                <div class="btn-toolbar" role="toolbar">

                    <div class="btn-group">
                        <a href="#" class="pagination-count pull-right btn">第 1 - 20 条记录，共 {{ $count }} 条 </a>
                        {{--<a class="btn btn-default dropdown-toggle" data-toggle="dropdown" href="#">--}}
                        {{--筛选 <span class="caret"></span>--}}
                        {{--</a>--}}
                        {{--<ul class="dropdown-menu">--}}
                        {{--<div class="container-fluid">--}}
                        {{--<li>--}}
                        {{--<div class="checkbox">--}}
                        {{--<label>--}}
                        {{--<input type="checkbox" value="">--}}
                        {{--筛选条件--}}
                        {{--</label>--}}
                        {{--</div>--}}
                        {{--</li>--}}
                        {{--</div>--}}
                        {{--<li role="separator" class="divider"></li>--}}
                        {{--</ul>--}}
                    </div>

                    <div class="pull-right btn-group">
                        <a href="#" class="btn btn-default"><i class="glyphicon glyphicon-chevron-left"></i></a>
                        <a href="#" class="btn btn-default"><i class="glyphicon glyphicon-chevron-right"></i></a>
                    </div>
                </div>
            </nav>

            <table class="table hover">
                <thead>
                <tr>
                    <th>IP</th>
                    <th>登录地址</th>
                    <th>登录时间</th>
                </tr>
                </thead>
                <tbody>
                @foreach($logs as $log)
                    <tr>
                        <td>{{ $log->ip }}</td>
                        <td>{{ $log->address }}</td>
                        <td>{{ $log->created_at }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    </div>
@endsection

@section('style')
    <style>
        #layout-content {
            padding: 0 10px;
        }
    </style>
@stop
@section('script')
    <script>
        //        var form = {};
        //        var $data = {
        //            form: $.extend({}, form),
        //            roles: [],
        //            members: [],
        //            validate_pass: true,
        //            error: {}
        //        };
        //
        //        new Vue({
        //            el: '#page',
        //            data: function () {
        //                return $data;
        //            },
        //            mounted: function () {
        //            },
        //            methods: {}
        //        });
    </script>
@stop