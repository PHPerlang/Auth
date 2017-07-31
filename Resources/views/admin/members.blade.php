@extends('admin::layout')

@section('content')
    <div class="page">
        <div class="page-header">
            <div class="page-title">用户</div>
            <!-- 页面操作按钮 -->
            <div class="page-handles">
                <button class="button" type="button" data-toggle="example-dropdown-2">
                    <i class="more fa fa-ellipsis-v" aria-hidden="true"></i>
                </button>
                <div class="dropdown-pane" id="example-dropdown-2" data-dropdown data-hover="true"
                     data-hover-pane="true">
                    <ul class="list-group">
                        <li class="list-group-item">权限表</li>
                        <li class="list-group-item">用户日志</li>
                    </ul>
                </div>
            </div>
            <!-- 页面菜单导航 -->
            <div class="page-links">
                <div class="page-links">
                    <a class="active" href="#">列表</a>
                    <a href="#">数据</a>
                    <a href="#">统计</a>
                </div>
            </div>
        </div>
        <div class="page-content" style="padding: 15px 20px 0 20px;">
            <div class="data-handles">
                <div class="left">
                    <div class="data-handle">
                        <button class="button" type="button" data-toggle="example-dropdown-1">
                            <i class="fa fa-filter" aria-hidden="true"></i>
                        </button>
                        <div class="dropdown-pane" id="example-dropdown-1" data-close-on-click="true"
                             data-dropdown data-v-offset="5">
                            <label for="check-1"><input id="check-1" type="checkbox"> 字段一</label>
                            <label for="check-2"><input id="check-2" type="checkbox"> 字段二</label>
                            <label for="check-3"><input id="check-3" type="checkbox"> 字段三</label>
                        </div>
                    </div>
                    <div class="data-handle">
                        <button class="button" type="button">
                            <i class="fa fa-share" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>
                <div class="right">
                    <div class="data-handle number">
                        第
                        <button class="button" type="button" data-toggle="example-dropdown-4">
                            1 - 100
                        </button>
                        <div class="dropdown-pane" id="example-dropdown-4" data-dropdown
                             data-close-on-click="true" data-v-offset="5">
                            <a href="#">101 - 200</a>
                            <a href="#">201 - 300</a>
                            <a href="#">301 - 400</a>
                        </div>
                        位用户，共 10004 位
                    </div>
                    <a href="#" class="data-handle"><i class="fa fa-chevron-left" aria-hidden="true"></i></a>
                    <a href="#" class="data-handle"><i class="fa fa-chevron-right" aria-hidden="true"></i></a>
                </div>
            </div>
            <table class="hover">
                <thead>
                <tr>
                    <th width="150">用户名</th>
                    <th>邮箱</th>
                    <th width="150">手机号</th>
                    <th width="150">注册时间</th>
                    <th width="250">操作</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>Content Goes Here</td>
                    <td>This is longer content Donec id elit non mi porta gravida at eget metus.</td>
                    <td>Content Goes Here</td>
                    <td>Content Goes Here</td>
                    <td>
                        <a href="#">禁用</a>
                        <a href="#">操作日志</a>
                        <a href="#">查看详情</a>
                    </td>
                </tr>
                <tr>
                    <td>Content Goes Here</td>
                    <td>This is longer Content Goes Here Donec id elit non mi porta gravida at eget metus.
                    </td>
                    <td>Content Goes Here</td>
                    <td>Content Goes Here</td>
                    <td>
                        <a href="#">禁用</a>
                        <a href="#">操作日志</a>
                        <a href="#">查看详情</a>
                    </td>
                </tr>
                <tr>
                    <td>Content Goes Here</td>
                    <td>This is longer Content Goes Here Donec id elit non mi porta gravida at eget metus.
                    </td>
                    <td>Content Goes Here</td>
                    <td>Content Goes Here</td>
                    <td>
                        <a href="#">禁用</a>
                        <a href="#">操作日志</a>
                        <a href="#">查看详情</a>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('style')
    <style>

    </style>
@stop

@section('script')
    <script>

    </script>
@stop