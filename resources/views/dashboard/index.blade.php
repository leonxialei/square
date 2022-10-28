
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>主页一</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="stylesheet" href="{{secure_url('lib/layui-v2.5.5/css/layui.css')}}" media="all">
    <link rel="stylesheet" href="{{secure_url('lib/font-awesome-4.7.0/css/font-awesome.min.css')}}" media="all">
    <link rel="stylesheet" href="{{secure_url('css/public.css')}}" media="all">
</head>
<style>
    .layui-top-box {padding:40px 20px 20px 20px;color:#fff}
    .panel {margin-bottom:17px;background-color:#fff;border:1px solid transparent;border-radius:3px;-webkit-box-shadow:0 1px 1px rgba(0,0,0,.05);box-shadow:0 1px 1px rgba(0,0,0,.05)}
    .panel-body {padding:15px}
    .panel-title {margin-top:0;margin-bottom:0;font-size:14px;color:inherit}
    .label {display:inline;padding:.2em .6em .3em;font-size:75%;font-weight:700;line-height:1;color:#fff;text-align:center;white-space:nowrap;vertical-align:baseline;border-radius:.25em;margin-top: .3em;}
    .layui-red {color:red}
    .main_btn > p {height:40px;}
</style>
<body>
<div class="layuimini-container">
    <div class="layuimini-main layui-top-box">
        <div style="margin: 10px 10px 10px 10px">
            <form class="layui-form layui-form-pane" action="">





                <div class="layui-inline" pane="">
                        <input type="checkbox" checked="" name="open" lay-skin="switch" lay-filter="switchTest" title="开关">
                </div>


            </form>
        </div>



        <div class="layui-row layui-col-space10">

            <div class="layui-col-md3">
                <div class="col-xs-6 col-md-3">
                    <div class="panel layui-bg-cyan">
                        <div class="panel-body">
                            <div class="panel-title">
                                <span class="label pull-right layui-bg-blue">实时</span>
                                <h5>成功总订单数：</h5>
                            </div>
                            <div class="panel-content">
                                <h1 class="no-margins">{{$done_count}}</h1>
                                <small>记录时间{{date('Y-m-d H:i:s')}}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="layui-col-md3">
                <div class="col-xs-6 col-md-3">
                    <div class="panel layui-bg-blue">
                        <div class="panel-body">
                            <div class="panel-title">
                                <span class="label pull-right layui-bg-cyan">实时</span>
                                <h5>交易总金额</h5>
                            </div>
                            <div class="panel-content">
                                <h1 class="no-margins">
                                    @if(empty($pay_amount))
                                        0.00
                                    @else
                                        {{sprintf("%.2f",$pay_amount/100)}}
                                    @endif
                                </h1>
                                <small>记录时间{{date('Y-m-d H:i:s')}}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="layui-col-md3">
                <div class="col-xs-6 col-md-3">
                    <div class="panel layui-bg-green">
                        <div class="panel-body">
                            <div class="panel-title">
                                <span class="label pull-right layui-bg-orange">实时</span>
                                <h5>成功总金额</h5>
                            </div>
                            <div class="panel-content">
                                <h1 class="no-margins">
                                    @if(empty($done_pay_amount))
                                        0.00
                                    @else
                                        {{sprintf("%.2f",$done_pay_amount/100)}}
                                    @endif
                                </h1>
                                <small>记录时间{{date('Y-m-d H:i:s')}}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="layui-col-md3">
                <div class="col-xs-6 col-md-3">
                    <div class="panel layui-bg-orange">
                        <div class="panel-body">
                            <div class="panel-title">
                                <span class="label pull-right layui-bg-green">实时</span>
                                <h5>总成功率</h5>
                            </div>
                            <div class="panel-content">
                                <h1 class="no-margins">
                                    @if(empty($done_count) || empty($count))
                                        0%
                                    @else
                                        {{sprintf("%.2f",($done_count/$count)*100)}}%
                                    @endif

                                </h1>
                                <small>记录时间{{date('Y-m-d H:i:s')}}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>





            <div class="layui-col-md6">
                <div style="" class="layui-card-header">订单统计</div>
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
                        <th>成功金额（元）</th>

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
                            <th>
                                @if(!empty($order->merchantChannel->channel))
                                    {{$order->merchantChannel->channel->name}}
                                @else
                                    ##
                                @endif
                            </th>

                            <th>
                                @if(!empty(\App\Models\Order::succeed($order->merchant_channel_id, $start_time, $end_time)))
                                    {{sprintf("%.2f", \App\Models\Order::succeed($order->merchant_channel_id, $start_time, $end_time)->count/$order->count*100)}}%
                                @else
                                    0%
                                @endif
                            </th>
                            <th ondblclick="change_weight({{$order->merchantChannel->id}}, this)">{{$order->merchantChannel->weight}}</th>

                            {{--                            订单金额（元）--}}
                            <th>
                                @if(!empty(\App\Models\Order::succeed($order->merchant_channel_id, $start_time, $end_time)))
                                    <?php $succeed_original_amount = sprintf("%.2f", \App\Models\Order::succeed($order->merchant_channel_id, $start_time, $end_time)->original_amount/100); ?>
                                @else
                                    <?php $succeed_original_amount = 0.00; ?>
                                @endif
                                <?php $succeed_original_count = $succeed_original_count + $succeed_original_amount; ?>
                                {{$succeed_original_amount}}
                                {{--                                    成功金额（元）--}}
                            </th>


                        </tr>
                    @endforeach

                    </tbody>
                </table>

            </div>





            <div class="layui-col-md6">
                <div style="" class="layui-card-header">上游预付</div>
                <table class="layui-table">
                    <colgroup>
                        <col>
                        <col>
                        <col>
                    </colgroup>
                    <thead>
                    <tr>
                        <th>上游名称</th>
                        <th>跑量金额</th>
                        <th>预付金额</th>
                        <th>预付剩余</th>
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
                            <th>{{$upstream->name}}</th>
                            <th>
                                <?php $total_original_amount = $total_original_amount + sprintf("%.2f",\App\Models\Order::upstreamDetail($upstream->id, $start_date, $end_date)->original_amount/100); ?>
                                {{sprintf("%.2f",\App\Models\Order::upstreamDetail($upstream->id, $start_date, $end_date)->original_amount/100)}}
                            </th>
                                <?php $total_quantity = $total_quantity + sprintf("%.2f",\App\Models\Order::upstreamDetail($upstream->id, $start_date, $end_date)->amount/100); ?>
                            <?php
                            $amount = App\Models\Order::log_change($upstream->id, $start_date, $end_date)/100;
                            $amount = empty($amount)?'0.00':$amount;
                            $total_amount = $total_amount + $amount;
                            ?>
                            <th>{{sprintf("%.2f",$amount)}}</th>
                            <th>
                                <?php $total_balance = $total_balance + sprintf("%.2f",($amount - \App\Models\Order::upstreamDetail($upstream->id, $start_date, $end_date)->amount/100)); ?>
                                @if(($amount - \App\Models\Order::upstreamDetail($upstream->id, $start_date, $end_date)->amount/100) >= 0)
                                    <button type="button" class="layui-btn layui-btn-xs layui-btn-normal layui-btn-radius">
                                        {{sprintf("%.2f",$amount - \App\Models\Order::upstreamDetail($upstream->id, $start_date, $end_date)->amount/100)}}</button>
                                @elseif(($amount - \App\Models\Order::upstreamDetail($upstream->id, $start_date, $end_date)->amount/100) < 0)
                                    <button type="button" class="layui-btn layui-btn-xs layui-btn-danger layui-btn-radius">
                                        {{sprintf("%.2f",$amount - \App\Models\Order::upstreamDetail($upstream->id, $start_date, $end_date)->amount/100)}}</button>
                                @endif
                            </th>
                        </tr>
                    @endforeach




                    </tbody>
                </table>

            </div>


            <div class="layui-col-md12">
                <div style="" class="layui-card-header">下游预付</div>
                <table class="layui-table">
                    <colgroup>
                        <col>
                        <col>
                        <col>
                    </colgroup>
                    <thead>
                    <tr>
                        <th>商户号</th>
                        <th>成功金额</th>
                        <th>商户结算</th>
                        <th>预付金额</th>
                        <th>预付剩余</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $total_amount = 0.00;
                    $total_close = 0.00;
                    $total_advance = 0.00;
                    $total_residue = 0.00;
                    ?>
                    @foreach($merchantChannels as $merchantChannel)
                        <?php
                        if(empty(\App\Models\Order::merchantOriginalAmount($merchantChannel->merchant->account, $start_date, $end_date)) &&
                            empty(\App\Models\Order::merchantAmount($merchantChannel->merchant->account, $start_date, $end_date)) &&
                            empty($merchantChannel->merchant->advance($merchantChannel->merchant->id, $start_date, $end_date))
                        ) {
                            continue;
                        }
                        ?>
                        <tr>
                            <th>{{$merchantChannel->merchant->name}}</th>
                            <th>
                                <?php
                                if(!empty(\App\Models\Order::merchantOriginalAmount($merchantChannel->merchant->account, $start_date, $end_date))) {
                                    $originalAmount = sprintf("%.2f", \App\Models\Order::merchantOriginalAmount($merchantChannel->merchant->account, $start_date, $end_date)->amount/100);
                                } else {
                                    $originalAmount = '0.00';
                                }
                                $total_amount = $total_amount + $originalAmount;
                                ?>
                                {{$originalAmount}}
                            </th>
                            <th>
                                <?php if(!empty(\App\Models\Order::merchantAmount($merchantChannel->merchant->account, $start_date, $end_date))) { ?>
                            <?php $merchantAmount = sprintf("%.2f", \App\Models\Order::merchantAmount($merchantChannel->merchant->account, $start_date, $end_date)->merchant_amount/100) ?>
                            <?php } else { ?>
                            <?php
                                    $merchantAmount = '0.00'; }
                                $total_close = $total_close + $merchantAmount;
                                ?>
                                {{$merchantAmount}}
                            </th>
                            <th>
                                <?php
                                $advance = sprintf("%.2f", $merchantChannel->merchant->advance($merchantChannel->merchant->id, $start_date, $end_date)/100);
                                $total_advance = $total_advance + $advance;
                                ?>
                                {{$advance}}
                            </th>
                            <th>
                                <?php
                                $balance = sprintf("%.2f", ($merchantChannel->merchant->advance($merchantChannel->merchant->id, $start_date, $end_date)/100) - $merchantAmount);
                                $total_residue = $total_residue + $balance;
                                ?>
                                @if($balance >= 0)
                                    <button type="button" class="layui-btn layui-btn-xs layui-btn-normal layui-btn-radius">{{$balance}}</button>
                                @else
                                    <button type="button" class="layui-btn layui-btn-xs layui-btn-danger layui-btn-radius">{{$balance}}</button>
                                @endif

                            </th>
                        </tr>
                    @endforeach





                    </tbody>
                </table>

            </div>


        </div>









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


        // 监听搜索操作
        form.on('switch(switchTest)', function (data) {
            layer.msg('自动刷新：' + (this.checked ? '打开' : '关闭'), {
                offset: '6px'
            });
            // layer.tips('温馨提示：请注意开关状态的文字可以随意定义，而不仅仅是ON|OFF', data.othis)
            switch_mark = this.checked;

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


</body>



</html>

