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
            background-color: #0a6ec4;
            color: #FFF;
            font-size: 8px;
            white-space: nowrap;
        }
        .layui-table th span.status_done {
            display:block;
            padding: 0 5px;
            height: 22px;
            line-height: 22px;
            background-color: #178d10;
            color: #FFF;
            font-size: 8px;
            white-space: nowrap;
        }
        .layui-table th span.status_failing {
            display:block;
            padding: 0 5px;
            height: 22px;
            line-height: 8px;
            background-color: #e55744;
            color: #FFF;
            font-size: 8px;
            white-space: nowrap;
        }
    </style>
</head>
<body>
<div class="layuimini-container">
    <div class="layuimini-main">
        <div style="margin: 10px 10px 10px 10px" class="layui-card-header"><i class="fa fa-warning icon"></i>订单统计</div>
        <fieldset class="table-search-fieldset">

            <div style="margin: 10px 10px 10px 10px">
                <span style="display:block;margin-bottom: 10px;">当前查询条件下记录总数：{{$count}}，交易金额总和：
                    @if(empty($pay_amount))
                        0，
                    @else
                    {{sprintf("%.2f",$pay_amount/100)}}，
                    @endif
                    <br />
成功金额总和：
                    @if(empty($done_pay_amount))
                        0，
                    @else
                    {{sprintf("%.2f",$done_pay_amount/100)}}，
                    @endif
                    <br />
                    成功率：
                    @if(empty($done_count))
                        0/0 = 0%
                    @else
                    {{$done_count}}/{{$count}}={{sprintf("%.2f",($done_count/$count)*100)}}%，
                    @endif
                    <br />
                    创建失败率：
                    @if(empty($fail_count))
                       0/0 = 0%
                    @else
                    {{$fail_count}}/{{$count}}={{sprintf("%.2f",($fail_count/$count)*100)}}
                    @endif
                </span>
                <form class="layui-form layui-form-pane" action="">
                    <div class="layui-form layui-form-pane" action="">

                    <div class="layui">
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


                </div>
                    <div class="layui-form layui-form-pane" style="margin: 10px 0 10px 0">
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
                    <div class="layui-inline">
                        <button type="submit" class="layui-btn layui-btn-primary" lay-submit ><i class="layui-icon"></i> 搜 索</button>
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
                        <th>商户</th>
                        <th>通道</th>
                        <th>成功率</th>
                        <th>权重</th>

                    </tr>
                    </thead>
                    <tbody>
                    <?php
                        $original_amount = 0;
                    $succeed_original_count = 0;
                    $merchant_amount_total = 0;
                    $merchant_count_total = 0;
                    $succeed_merchant_count_total = 0;
                    $income = 0;
                    $settlement = 0;
                    ?>

                    @foreach($orders as $order)
                        <tr>
                            <th>{{$order->customer->name}}</th>
                            <th>{{$order->merchantChannel->channel->name}}</th>
                            <th>
                                @if(!empty(\App\Models\Order::succeed($order->merchant_channel_id, $start_time, $end_time)))
                                    {{sprintf("%.2f", \App\Models\Order::succeed($order->merchant_channel_id, $start_time, $end_time)->count/$order->count*100)}}%
                                @else
                                    0%
                                @endif
                            </th>
                            <th ondblclick="change_weight({{$order->merchantChannel->id}}, this)">{{$order->merchantChannel->weight}}</th>


                        </tr>
                    @endforeach
                    @if($merchant_count_total != 0)

                    @endif
                    </tbody>
                </table>
{{--                {{ $orders->appends([--}}
{{--                    'start_time' => date('Y-m-d H:i:s', $start_time),--}}
{{--                    'end_time' => date('Y-m-d H:i:s', $end_time),--}}
{{--                    'merchant_id' => $request->get('merchant_id'),--}}
{{--                    'channel_code' => $request->get('channel_code'),--}}
{{--                ])->links() }}--}}
            </div>
        </fieldset>





    </div>
</div>
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
        change_weight = function (id, _this) {
            var weight = $(_this).html();
            $(_this).empty();
            var html = '<select onblur="set_weight('+id+', this)">' +
                '<option value="1">1</option>' +
                '<option value="4">4</option>' +
                '<option value="9">9</option>' +
                '</select>';
            $(_this).append(html);
            $(_this).find('input').focus();
        };
        set_weight = function (id, _this) {
            var weight = $(_this).find('option:selected').val();
            var tdbox = $(_this).parent();
            $.ajax({
                type: "POST",
                url: "{{secure_url('api/merchant/channel/weight')}}",
                data: {
                    id: id,
                    weight: weight,
                    _token:'{{csrf_token()}}'
                },
                dataType:'json',
                async:false,
                success: function(msg){
                    if(msg.result == true) {
                        tdbox.empty();
                        tdbox.html(weight);
                    }
                }
            });
        };




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
