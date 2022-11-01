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
        .layui-table th span.status_pay {
            display:block;
            padding: 0 5px;
            height: 22px;
            line-height: 22px;
            background-color: #5d9432;
            color: #FFF;
            font-size: 8px;
            white-space: nowrap;
            border-radius: 11px;
            text-align: center;
        }
        .layui-table th span.status_done {
            display:block;
            padding: 0 5px;
            height: 22px;
            line-height: 22px;
            background-color: #0a75af;
            color: #FFF;
            font-size: 8px;
            white-space: nowrap;
            border-radius: 11px;
            text-align: center;
        }
        .layui-table th span.status_failing {
            display:block;
            padding: 0 5px;
            height: 22px;
            line-height: 22px;
            background-color: #cb4703;
            color: #FFF;
            font-size: 8px;
            white-space: nowrap;
            border-radius: 11px;
            text-align: center;
        }
        .layui-card-header {
            position: relative;
        }
        .layui-card-heade-such {
            position: absolute;
            right: 30px;
        }
    </style>
</head>
<body>
<div class="layuimini-container">
    <div class="layuimini-main">
        <div style="margin: 10px 10px 10px 10px" class="layui-card-header"><i class="fa fa-warning icon"></i>订单查询
        </div>
        <fieldset class="table-search-fieldset">

            <div  style="margin: 10px 10px 10px 10px;">
                <span style="display:block;margin-bottom: 10px;">当前查询条件下记录总数：{{$count}}，交易金额总和：
                    @if(empty($pay_amount))
                        0，
                    @else
                    {{sprintf("%.2f",$pay_amount/100)}}，
                    @endif
成功金额总和：
                    @if(empty($done_pay_amount))
                        0，
                    @else
                    {{sprintf("%.2f",$done_pay_amount/100)}}，
                    @endif
                    成功率：
                    @if(empty($done_count))
                        0/0 = 0%
                    @else
                    {{$done_count}}/{{$count}}={{sprintf("%.2f",($done_count/$count)*100)}}%，
                    @endif

                    创建失败率：
                    @if(empty($fail_count))
                       0/0 = 0%
                    @else
                    {{$fail_count}}/{{$count}}={{sprintf("%.2f",($fail_count/$count)*100)}}
                    @endif
                </span>
                <form id="such-box" class="layui-form layui-form-pane" action="">
                    <div class="layui-form layui-form-pane" action="">
                    <div class="layui-inline">
                        <label class="layui-form-label">起始时间</label>
                        <div class="layui-input-inline">
                            <input type="text" value="{{date('Y-m-d H:i:s', $start_time)}}" name="start_time" id="start-date" lay-verify="datetime" placeholder="yyyy-MM-dd HH:ii:ss" autocomplete="off" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label class="layui-form-label">截止时间</label>
                        <div class="layui-input-inline">
                            <input type="text" value="{{date('Y-m-d H:i:s', $end_time)}}" name="end_time" id="end-date" lay-verify="datetime" placeholder="yyyy-MM-dd HH:ii:ss" autocomplete="off" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label class="layui-form-label">商户</label>
                        <div class="layui-input-inline">
                            <select type="text" name="merchant_id" class="layui-input">
                                <option value="">请选择</option>
                                @foreach($merchants as $merchant)
                                    <option
                                        @if($request->get('merchant_id') == $merchant->account)
                                        selected="selected"
                                        @endif
                                        value="{{$merchant->account}}" >{{$merchant->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                        <div class="layui-form layui-form-pane" style="margin: 10px 0 10px 0">
                    <div class="layui-inline">
                            <label class="layui-form-label">通道</label>
                            <div class="layui-input-inline">
                                <select type="text" name="channel_id" class="layui-input">
                                    <option value="">请选择</option>
                                    @foreach($upstreams as $upstream)
                                        <option
                                            value="" >===={{$upstream->name}}====</option>
                                        <?php
                                        $upChannelModel = new \App\Models\UpstreamChannel();
                                        $upChannels = $upChannelModel->where('upstream_id', $upstream->id)
                                            ->where('status', 1)->get();
                                        ?>
                                        @foreach($upChannels as $channel)
                                            <option
                                                @if($channel->id == $request->get('channel_id'))
                                                selected="selected"
                                                @endif
                                                value="{{$channel->id}}" >{{$channel->name}}</option>
                                        @endforeach
                                    @endforeach
                                </select>
                            </div>
                        </div>

                    <div class="layui-inline">
                        <label class="layui-form-label">状态</label>
                        <div class="layui-input-inline">
                            <select type="text" name="status" class="layui-input">
                                <option value="">请选择</option>
                                <option @if($status == 4) selected @endif value="4">订单生成</option>
                                <option @if($status == 1) selected @endif value="1">支付成功</option>
                                <option @if($status == 2) selected @endif value="2">处理完成</option>
                                <option @if($status == 3) selected @endif value="3">创建失败</option>
                            </select>
                        </div>
                    </div>
                            <div class="layui-inline">
                                <label class="layui-form-label">通道类型</label>
                                <div class="layui-input-inline">
                                    <select type="text" name="channel_code" class="layui-input">
                                        <option value="">请选择</option>
                                        @foreach($channelCodes as $channelCode)
                                            <option
                                                @if($request->get('channel_code') == $channelCode->code)
                                                selected="selected"
                                                @endif
                                                value="{{$channelCode->code}}" >{{$channelCode->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                </div>
                    <div class="layui-form layui-form-pane" style="margin: 10px 0 10px 0">


                    <div class="layui-inline">
                        <label class="layui-form-label">平台订单号</label>
                        <div class="layui-input-inline">
                            <input type="text" value="{{$request->get('OrderNo')}}" name="OrderNo"  autocomplete="off" class="layui-input">
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label class="layui-form-label">商户订单号</label>
                        <div class="layui-input-inline">
                            <input type="text" value="{{$request->get('mchOrderNo')}}" name="mchOrderNo"  autocomplete="off" class="layui-input">
                        </div>
                    </div>

                        <div class="layui-inline">
                            <button type="submit" class="layui-btn layui-btn-primary" lay-submit ><i class="layui-icon"></i> 搜 索</button>
                        </div>

                        <div class="layui-inline" pane="">
                            <label class="layui-form-label">自动刷新开关</label>
                            <div class="layui-input-block">
                                <input type="checkbox" checked="" name="open" lay-skin="switch" lay-filter="switchTest" title="开关">
                                <div class="layui-unselect layui-form-switch layui-form-onswitch" lay-skin="_switch"><em></em><i></i></div>
                            </div>
                        </div>
                        <div class="layui-inline" style="margin-top: 5px;">
                            <button type="button" id="export_but" class="layui-btn" lay-submit >导出CSV</button>
                        </div>


            </div>
                </form>

            <div class="layui-col-md12">
                <table class="layui-table">
                    <colgroup>
                        <col>
                        <col>
                        <col>
                    </colgroup>
                    <thead>
                    <tr>
                        <th>平台订单号</th>
                        <th>商户</th>
                        <th>商户订单号</th>
                        <th>通道</th>
                        <th>金额</th>

                        <th>创建时间</th>
                        <th>支付时间</th>
                        <th>状态</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($orders as $order)
                        <tr>
                            <th>YZ{{$order->OrderNo}}</th>
                            <th>{{$order->customer->name}}</th>
                            <th>{{$order->mchOrderNo}}</th>
                            <th>{{$order->merchantChannel->channel->name}}</th>

                            <th>{{sprintf("%.2f",$order->original_amount/100)}}</th>


                            <th>{{date('Y-m-d H:i:s', $order->order_time)}}</th>
                            @if(empty($order->pay_time))
                            <th>还未支付</th>
                            @else
                            <th>{{date('Y-m-d H:i:s', $order->pay_time)}}</th>
                            @endif
                            <th>
                                @if($order->status == 0)
                                    <span>订单生成</span>
                                @elseif($order->status == 1)
                                    <span class="status_pay">支付成功</span>
                                @elseif($order->status == 2)
                                    <span class="status_done">处理完成</span>
                                @elseif($order->status == 3)
                                    <span class="status_failing">创建失败</span>
                                @endif
                            </th>
                            <th>
                                <button
                                    type="button" onclick="inform({{$order->id}})" class="layui-btn layui-btn-danger layui-btn-xs info_copy">补发通知</button>
                                <button onclick="edit_code({{$order->id}})" type="button" class="layui-btn layui-btn-danger layui-btn-xs">编辑</button>
                            </th>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                {{ $orders->appends([
                    'start_time' => date('Y-m-d H:i:s', $start_time),
                    'end_time' => date('Y-m-d H:i:s', $end_time),
                    'merchant_id' => $request->get('merchant_id'),
                    'channel_id' => $request->get('channel_id'),
                    'status' => $status,
                    'channel_code' => $request->get('channel_code'),
                    'OrderNo' => $request->get('OrderNo'),
                    'mchOrderNo' => $request->get('mchOrderNo')
                ])->links() }}
            </div>
        </fieldset>




    </div>
</div>
<iframe id="export" style="width: 1px;height:1px;" src=""></iframe>
<script src="{{secure_url('lib/layui-v2.5.5/layui.js')}}" charset="utf-8"></script>
<script src="{{secure_url('lib/dist/clipboard.min.js')}}" charset="utf-8"></script>
<script>
    layui.use(['form', 'table', 'laydate'], function () {
        var $ = layui.jquery,
            form = layui.form,
            table = layui.table,
            laydate = layui.laydate,
            layuimini = layui.layuimini;

        laydate.render({
            elem: '#start-date',
            type: 'datetime',
        });
        laydate.render({
            elem: '#end-date',
            type: 'datetime',
        });
        // 监听搜索操作
        form.on('switch(switchTest)', function (data) {
            layer.msg('自动刷新：' + (this.checked ? '打开' : '关闭'), {
                offset: '6px'
            });
            // layer.tips('温馨提示：请注意开关状态的文字可以随意定义，而不仅仅是ON|OFF', data.othis)
            switch_mark = this.checked;

        });

        inform = function(id) {
            $.ajax({
                type: "GET",
                url: "{{secure_url('api/pay/apprise/')}}/"+id,
                async: false,
                data: {
                    '_token': '{{csrf_token()}}'
                },
                success: function (data) {
                   if(data == 2) {
                       layer.msg('此订单已完结，无需补发！');
                   }else if(data == 1) {
                       layer.msg('补发成功！');
                   } else if(data == 0) {
                       layer.msg('补发失败！');
                   } else if(data == 3) {
                       layer.msg('先更改状态为已支付才能发送！');
                   }
                }
            })
        }

        edit_code = function (id) {
            var index = layer.open({
                title: '修改订单',
                type: 2,
                shade: 0.2,
                maxmin:true,
                shadeClose: true,
                area: ['100%', '100%'],
                content: '{{secure_url('order')}}/'+id+'/edit',

            });
            $(window).on("resize", function () {
                layer.full(index);
            });

            return false;
        };

        var clipboard = new ClipboardJS('.info_copy');

        clipboard.on('success', function (e) {
            layer.msg('结算成功！');
            console.log(e);
        });

        clipboard.on('error', function (e) {
            console.log(e);
        });

        $('#export_but').click(function(){
            $('#export').attr('src', '');
            var start_time = '?start_time='+$('#start-date').val();
            var end_time = '&end_time='+$('#end-date').val();
            var merchant_id = '&merchant_id='+$('select[name="merchant_id"]').val();
            var channel_id = '&channel_id='+$('select[name="channel_id"]').val();
            var status = '&status='+$('select[name="status"]').val();
            var channel_code = '&channel_code='+$('select[name="channel_code"]').val();
            var OrderNo = '&OrderNo='+$('input[name="OrderNo"]').val();
            var mchOrderNo = '&mchOrderNo='+$('input[name="mchOrderNo"]').val();
            var url = '<?php echo secure_url('export/order') ?>'+start_time+end_time+merchant_id
                +channel_id+status+channel_code+OrderNo+OrderNo+mchOrderNo;
            $('#export').attr('src', url);
        });


    });


    switch_mark = true;
    var timestart = 30;
    var timestep = -1;
    var timeID;
    function timecount() {
        if(timestart < 0){
            timestart = 30;
            if(switch_mark == true) {
                location.reload();
            }

        }else {
            timestart = timestart - 1;
        }
        timeID=setTimeout("timecount()",1000);
    }
    timecount();


</script>


<script>




</script>
</body>
</html>
