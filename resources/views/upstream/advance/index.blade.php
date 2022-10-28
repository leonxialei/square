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
        <div style="margin: 10px 10px 10px 10px" class="layui-card-header"><i class="fa fa-warning icon"></i>上游预付</div>
        <fieldset class="table-search-fieldset">

            <div style="margin: 10px 10px 10px 10px">
                <form class="layui-form layui-form-pane" action="">
                    <div class="layui-inline">
                        <label class="layui-form-label">搜索日期</label>
                        <div class="layui-input-inline">
                            <input type="text" value="{{$start_date}}" name="start_date" id="start-date" lay-verify="date" placeholder="yyyy-MM-dd" autocomplete="off" class="layui-input">
                        </div>
                    </div>


                    <div class="layui-inline">
                            <label class="layui-form-label">上游</label>
                            <div class="layui-input-inline">
                                <select type="text" name="upstream_id" class="layui-input">
                                    <option value="">请选择</option>
                                    @foreach($upstreams as $upstream)
                                    <option
                                        @if($request->get('upstream_id') == $upstream->id)
                                        selected="selected"
                                        @endif
                                        value="{{$upstream->id}}" >{{$upstream->name}}</option>
                                    @endforeach
                                </select>
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


                </form>
            </div>


            <form class="layui-form" action="">
                <table class="layui-table">
                    <colgroup>
                        <col>
                        <col>
                       <col>
                    </colgroup>
                    <thead>
                    <tr>
                        <th>日期</th>
                        <th>上游名称</th>
                        <th>跑量金额</th>
                        <th>结算金额</th>
                        <th>预付金额</th>
                        <th>预付剩余</th>
                        <th>开关催债</th>
                        <th>操作</th>

                    </tr>
                    </thead>
                    <tbody>
                    <?php
                        $total_original_amount = 0;
                        $total_quantity = 0;
                        $total_amount = 0;
                        $total_balance = 0;
                    ?>
                        @foreach($upstreams as $upstream)
                            <?php
                                if(\App\Models\Order::upstreamDetail($upstream->id, $start_date, $end_date)->original_amount == 0 &&
                                    \App\Models\Order::upstreamDetail($upstream->id, $start_date, $end_date)->amount == 0 &&
                                    empty(App\Models\Order::log_change($upstream->id, $start_date, $end_date))
                                ) {
                                    continue;
                                }
                            ?>
                            <tr>
                                <th>{{$end_date}}</th>
                                <th>{{$upstream->name}}</th>
                                <th>
                                    <?php $total_original_amount = $total_original_amount + sprintf("%.2f",\App\Models\Order::upstreamDetail($upstream->id, $start_date, $end_date)->original_amount/100); ?>
                                    {{sprintf("%.2f",\App\Models\Order::upstreamDetail($upstream->id, $start_date, $end_date)->original_amount/100)}}
                                </th>
                                <th>
                                    <?php $total_quantity = $total_quantity + sprintf("%.2f",\App\Models\Order::upstreamDetail($upstream->id, $start_date, $end_date)->amount/100); ?>
                                    {{sprintf("%.2f",\App\Models\Order::upstreamDetail($upstream->id, $start_date, $end_date)->amount/100)}}
                                </th>
                                <?php
                                    $amount = App\Models\Order::log_change($upstream->id, $start_date, $end_date)/100;
                                    $amount = empty($amount)?'0.00':$amount;
                                $total_amount = $total_amount + $amount;
                                ?>
                                <th>{{sprintf("%.2f",$amount)}}</th>
                                <th>
                                    <?php $total_balance = $total_balance + sprintf("%.2f",($amount - \App\Models\Order::upstreamDetail($upstream->id, $start_date, $end_date)->amount/100)); ?>
                                    @if(($amount - \App\Models\Order::upstreamDetail($upstream->id, $start_date, $end_date)->amount/100) >= 0)
                                    <button type="button" class="layui-btn layui-btn-xs layui-btn-radius">
                                        {{sprintf("%.2f",$amount - \App\Models\Order::upstreamDetail($upstream->id, $start_date, $end_date)->amount/100)}}</button>
                                    @elseif(($amount - \App\Models\Order::upstreamDetail($upstream->id, $start_date, $end_date)->amount/100) < 0)
                                    <button type="button" class="layui-btn layui-btn-xs layui-btn-danger layui-btn-radius">
                                        {{sprintf("%.2f",$amount - \App\Models\Order::upstreamDetail($upstream->id, $start_date, $end_date)->amount/100)}}</button>
                                    @endif
                                </th>

                                 <th>
                                     <input type="checkbox" uid="{{$upstream->id}}"
                                            @if($upstream->collection == 1)
                                                checked="checked"
                                            @elseif($upstream->collection == 0)
                                            @endif
                                            lay-filter="switchStatus" name="status" lay-text="开启|关闭" lay-skin="switch">
                                </th>



                                <th>

                                    <button
                                        data-clipboard-action="copy"
                                        data-clipboard-text="{{$end_date}}账单明细:

上游:{{$upstream->name}}
<?php $total_pay_amount = 0; ?>
@foreach(App\Models\Order::detail($upstream->id, $start_date, $end_date) as $item)
{{App\Models\UpstreamChannel::obj($item->channel_id)->name}}
跑量:{{sprintf("%.2f",$item->original_amount/100)}}
<?php $rate = App\Models\UpstreamChannel::obj($item->channel_id)->rate; ?>
费率:{{$rate/10}}%
<?php
    $pay_amount = ($item->original_amount - $item->original_amount*($rate/1000))/100;
    $total_pay_amount = $total_pay_amount + $pay_amount;
?>
应结算:{{sprintf("%.2f",$pay_amount)}}

@endforeach
跑量合计:{{sprintf("%.2f",\App\Models\Order::upstreamDetail($upstream->id, $start_date, $end_date)->original_amount/100)}}
应结算合计:{{sprintf("%.2f",$total_pay_amount)}}
已预付:{{sprintf("%.2f",$amount)}}
剩余应结算:{{sprintf("%.2f",$amount)}}-{{sprintf("%.2f",$total_pay_amount)}}={{sprintf("%.2f",$amount - $total_pay_amount)}}"
                                        type="button" class="layui-btn layui-btn-warm layui-btn-xs info_copy">结算信息</button>
                                    <button onclick="edit_code({{$upstream->id}})" type="button" class="layui-btn layui-btn-warm layui-btn-xs">修改预付</button>
                                </th>




                            </tr>
                        @endforeach

                        <tr>
                            <th>总计：</th>
                            <th>---</th>
                            <th>{{sprintf("%.2f",$total_original_amount)}}</th>
                            <th>{{sprintf("%.2f",$total_quantity)}}</th>
                            <th>{{sprintf("%.2f",$total_amount)}}</th>
                            <th>{{sprintf("%.2f",$total_balance)}}</th>
                            <th></th>
                            <th></th>
                        </tr>
                    </tbody>


                    </tbody>
                </table>

            </form>
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


        form.on('switch(switchStatus)', function(data) {
            var status = this.checked;
            var uid = $(this).attr('uid');
            if(status) {
                status = 1;
            } else {
                status = 0;
            }
            $.ajax({
                type: "POST",
                url: "{{secure_url('api/upstream/collection')}}",
                async: false,
                data: {
                    'id' :uid,
                    'type': status,
                    '_token': '{{csrf_token()}}'
                },
                success: function (data) {
                    if(data.result == false) {
                        layer.msg('非法操作！');
                    }
                }
            })
        });

        laydate.render({
            elem: '#start-date'
        });
        laydate.render({
            elem: '#end-date'
        });
        // 监听搜索操作

        form.on('switch(switchTest)', function (data) {
            layer.msg('自动刷新：' + (this.checked ? '打开' : '关闭'), {
                offset: '6px'
            });
            // layer.tips('温馨提示：请注意开关状态的文字可以随意定义，而不仅仅是ON|OFF', data.othis)
            switch_mark = this.checked;

        });

        edit_code = function (id) {
            var index = layer.open({
                title: '修改预付',
                type: 2,
                shade: 0.2,
                maxmin:true,
                shadeClose: true,
                area: ['100%', '100%'],
                content: '{{secure_url('upstream/advance')}}/'+id+'/edit',

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
