<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>layui</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="stylesheet" href="{{secure_url('lib/layui-v2.5.5/css/layui.css')}}" media="all">
    <link rel="stylesheet" href="{{secure_url('css/public.css')}}" media="all">
    <style>
        #pull_right{
            text-align:center;
        }
        .pull-right {
            /*float: left!important;*/
        }
        .pagination {
            display: inline-block;
            padding-left: 0;
            margin: 20px 0;
            border-radius: 4px;
        }
        .pagination > li {
            display: inline;
        }
        .pagination > li > a,
        .pagination > li > span {
            position: relative;
            float: left;
            padding: 6px 12px;
            margin-left: -1px;
            line-height: 1.42857143;
            color: #428bca;
            text-decoration: none;
            background-color: #fff;
            border: 1px solid #ddd;
        }
        .pagination > li:first-child > a,
        .pagination > li:first-child > span {
            margin-left: 0;
            border-top-left-radius: 4px;
            border-bottom-left-radius: 4px;
        }
        .pagination > li:last-child > a,
        .pagination > li:last-child > span {
            border-top-right-radius: 4px;
            border-bottom-right-radius: 4px;
        }
        .pagination > li > a:hover,
        .pagination > li > span:hover,
        .pagination > li > a:focus,
        .pagination > li > span:focus {
            color: #2a6496;
            background-color: #eee;
            border-color: #ddd;
        }
        .pagination > .active > a,
        .pagination > .active > span,
        .pagination > .active > a:hover,
        .pagination > .active > span:hover,
        .pagination > .active > a:focus,
        .pagination > .active > span:focus {
            z-index: 2;
            color: #fff;
            cursor: default;
            background-color: #428bca;
            border-color: #428bca;
        }
        .pagination > .disabled > span,
        .pagination > .disabled > span:hover,
        .pagination > .disabled > span:focus,
        .pagination > .disabled > a,
        .pagination > .disabled > a:hover,
        .pagination > .disabled > a:focus {
            color: #777;
            cursor: not-allowed;
            background-color: #fff;
            border-color: #ddd;
        }
        .clear{
            clear: both;
        }
    </style>
</head>
<body>
<div class="layuimini-container">
    <div class="layuimini-main">
        <div style="margin: 10px 10px 10px 10px" class="layui-card-header"><i class="fa fa-warning icon"></i>通道管理</div>
        <fieldset class="table-search-fieldset">
            <div style="margin: 10px 10px 10px 10px">
                <form class="layui-form layui-form-pane" action="">
                    <div class="layui-form-item">

                        <div class="layui-inline">
                            <label class="layui-form-label">上游</label>
                            <div class="layui-input-inline">
                                <select type="text" name="upstream" class="layui-input">
                                    <option value="">请选择</option>
                                    @foreach($upstreams as $upstream)
                                        @if($request->get('upstream') == $upstream->id)
                                        <option
                                            selected="selected"
                                            value="{{$upstream->id}}" >{{$upstream->name}}</option>
                                        @else
                                        <option
                                            value="{{$upstream->id}}" >{{$upstream->name}}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="layui-inline">
                            <label class="layui-form-label">通道</label>
                            <div class="layui-input-inline">
                                <select type="text" name="code" class="layui-input">
                                    <option value="">请选择</option>
                                    @foreach($codes as $code)
                                        @if($request->get('code') == $code->code)
                                    <option
                                        selected="selected"
                                        value="{{$code->code}}" >{{$code->name}}</option>
                                        @else
                                    <option
                                        value="{{$code->code}}" >{{$code->name}}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="layui-inline">
                            <button type="submit" class="layui-btn layui-btn-primary" lay-submit ><i class="layui-icon"></i> 搜 索</button>
                        </div>

                        <div class="layui-inline">
                            <button class="layui-btn data-add-btn"> 新增通道 </button>
                        </div>

                    </div>
                </form>
            </div>


            <div class="layui-col-md12">
                <table class="layui-table">
                    <colgroup>
                        <col>
                        <col>
                        <col>
                    </colgroup>
                    <thead>
                    <tr>
                        <th>通道名称</th>
                        <th>平台通道编码</th>
                        <th>所属上家</th>
                        <th>接入费率</th>
                        <th>固定金额</th>
                        <th>绑定商户</th>
                        <th>状态</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($upstreamChannels as $channel)
                    <tr>
                        <td>{{$channel->name}}</td>
                        <td>{{$channel->code}}</td>
                        <td>{{$channel->upstream->name}}</td>
                        <td>{{$channel->rate}}</td>
                        <td>{{$channel->amount}}</td>
                        <th><button type="button" onclick="bind_merchant({{$channel->id}})" class="merchant_management layui-btn layui-btn-xs">绑定商户</button></th>
                        <td>
                            @if($channel->status == 1)
                            <button type="button" class="layui-btn layui-btn-xs">开启</button>
                            @elseif($channel->status == 0)
                            <button type="button" class="layui-btn layui-btn-danger layui-btn-xs">关闭</button>
                            @endif
                        </td>
                        <td>
                            <button type="button" onclick="edit_code({{$channel->id}})" class="layui-btn layui-btn-warm layui-btn-xs">修改</button>
                            <button type="button" onclick="del_code({{$channel->id}})" class="layui-btn layui-btn-danger layui-btn-xs channel-del">删除</button>
                        </td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
                {{ $upstreamChannels->links() }}
            </div>
        </fieldset>





    </div>
</div>
<script src="{{secure_url('lib/layui-v2.5.5/layui.js')}}" charset="utf-8"></script>
<script>
    layui.use(['form', 'table'], function () {
        var $ = layui.jquery,
            form = layui.form,
            table = layui.table,
            layuimini = layui.layuimini;



        // 监听搜索操作


        // 监听添加操作
        $(".data-add-btn").on("click", function () {
            var index = layer.open({
                title: '添加通道',
                type: 2,
                shade: 0.2,
                maxmin:true,
                shadeClose: true,
                area: ['100%', '100%'],
                content: '{{secure_url('channel/create')}}',
            });
            $(window).on("resize", function () {
                layer.full(index);
            });

            return false;
        });



        bind_merchant = function (id) {
            var index = layer.open({
                title: '绑定商户',
                type: 2,
                shade: 0.2,
                maxmin:true,
                shadeClose: true,
                area: ['100%', '100%'],
                content: '{{secure_url('channel/merchant')}}/'+id,

            });
            $(window).on("resize", function () {
                layer.full(index);
            });

            return false;
        };


        del_code = function(id) {
            var check = confirm('您确定要删除此通道吗？');
            if(check) {
                $.ajax({
                    type: "DELETE",
                    url: "{{secure_url('api/channel/')}}/"+id,
                    async: false,
                    data: {
                        '_token': '{{csrf_token()}}'
                    },
                    success: function (data) {
                        if(data == false) {
                            layer.msg('非法操作！');
                        }else if(data == true) {
                            window.location.reload();
                        }
                    }
                })
            }




            {{--$.ajax({--}}
            {{--    type: "DELETE",--}}
            {{--    url: "{{secure_url('api/channel/')}}/"+id,--}}
            {{--    async: false,--}}
            {{--    data: {--}}
            {{--        '_token': '{{csrf_token()}}'--}}
            {{--    },--}}
            {{--    success: function (data) {--}}
            {{--        if(data == false) {--}}
            {{--            layer.msg('非法操作！');--}}
            {{--        }else if(data == true) {--}}
            {{--            window.location.reload();--}}
            {{--        }--}}
            {{--    }--}}
            {{--})--}}
        }





        edit_code = function (id) {
            var index = layer.open({
                title: '编辑通道',
                type: 2,
                shade: 0.2,
                maxmin:true,
                shadeClose: true,
                area: ['100%', '100%'],
                content: '{{secure_url('channel')}}/'+id+'/edit',

            });
            $(window).on("resize", function () {
                layer.full(index);
            });

            return false;
        };

        // 监听删除操作
        // $(".data-delete-btn").on("click", function () {
        //     var checkStatus = table.checkStatus('currentTableId')
        //         , data = checkStatus.data;
        //     layer.alert(JSON.stringify(data));
        // });



    });
</script>
<script>

</script>

</body>
</html>
