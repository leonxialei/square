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
        <div style="margin: 10px 10px 10px 10px" class="layui-card-header"><i class="fa fa-warning icon"></i>下游跑量分析</div>
        <fieldset class="table-search-fieldset">

            <div style="margin: 10px 10px 10px 10px">


                @foreach($merchants as $merchant)
                    @if($nowMerchant->id == $merchant->id)
                    <div class="layui-inline" style="margin: 10px;">
                        <a href="{{secure_url('analyze')}}/{{$merchant->id}}?start_date={{$start_date}}" class="layui-btn layui-btn-normal data-add-btn"> {{$merchant->name}} </a>
                    </div>
                    @else
                    <div class="layui-inline" style="margin: 10px;">
                        <a href="{{secure_url('analyze')}}/{{$merchant->id}}?start_date={{$start_date}}" class="layui-btn data-add-btn"> {{$merchant->name}} </a>
                    </div>
                    @endif
                @endforeach
            </div>
            @if(!\App\Help\Methods::is_mobile_request())
            <div class="layui-col-md12">
                <div id="container" style="height: 500px"></div>

            </div>
            @endif
            <div style="margin: 10px 10px 10px 10px">
                <form class="layui-form layui-form-pane" action="{{secure_url('analyze')}}/{{$id}}">
                    <div class="layui-inline">
                        <label class="layui-form-label">搜索日期</label>
                        <div class="layui-input-inline">
                            <input type="text" value="{{$start_date}}" name="start_date" id="start-date" lay-verify="date" placeholder="yyyy-MM-dd" autocomplete="off" class="layui-input">
                        </div>
                    </div>



                    <div class="layui-inline">
                        <button type="submit" class="layui-btn layui-btn-primary" lay-submit ><i class="layui-icon"></i> 搜 索</button>
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
                        <th>时间</th>
                        <th>订单量</th>
                        <th>成功金额</th>
                        <th>商户结算</th>
                    </tr>
                    </thead>
                    <tbody>

                    @foreach(\App\Models\Order::analyze($nowMerchant->account,$start_date,$times) as $key=>$order)
                        @if(date('H') == $times[$key])
                        <tr style="background: #d6d9d9">
                            <th>{{$times[$key]}} （现在时间）</th>
                        @else
                        <tr>
                            <th>{{$times[$key]}}</th>
                        @endif
                            <th>{{$order->quantity}}</th>
                            <th>{{sprintf("%.2f",$order->amount/100)}}</th>
                            <th>{{sprintf("%.2f",$order->merchant_amount/100)}}</th>


                        </tr>
                    @endforeach




                    </tbody>
                </table>

            </div>


        </fieldset>





    </div>
</div>
<script src="{{secure_url('lib/layui-v2.5.5/layui.js')}}" charset="utf-8"></script>
<script src="{{secure_url('lib/dist/clipboard.min.js')}}" charset="utf-8"></script>
<script type="text/javascript" src="https://fastly.jsdelivr.net/npm/echarts@5.3.3/dist/echarts.min.js"></script>
<script>
    layui.use(['form', 'table', 'laydate'], function () {
        var $ = layui.jquery,
            form = layui.form,
            table = layui.table,
            laydate = layui.laydate,
            layuimini = layui.layuimini;

        laydate.render({
            elem: '#start-date'
        });
        laydate.render({
            elem: '#end-date'
        });
        // 监听搜索操作







    });




</script>


<script type="text/javascript">
    var dom = document.getElementById('container');
    var myChart = echarts.init(dom, null, {
        renderer: 'canvas',
        useDirtyRect: false
    });
    var app = {};

    var option;

    option = {
        xAxis: {
            type: 'category',
            data: {!!json_encode($times)!!}
        },
        legend: {
            data: ['跑量金额', '订单量']
        },
        tooltip: {
            trigger: 'axis'
        },
        yAxis: {
            type: 'value'
        },
        <?php
            $amounts = \App\Models\Order::analyze($nowMerchant->account,$start_date,$times);
        ?>
        series: [
            {
                name: '跑量金额',
                stack: '元',
                data: [
                    @foreach($amounts as $amount)
                        {{sprintf("%.2f",$amount->amount/100)}},
                    @endforeach
                ],
                type: 'line'
            },
            {
                name: '订单量',
                stack: '单',
                data: [
                    @foreach($amounts as $amount)
                        {{$amount->quantity}},
                    @endforeach
                ],
                type: 'line'
            }
        ]
    };

    if (option && typeof option === 'object') {
        myChart.setOption(option);
    }

    window.addEventListener('resize', myChart.resize);
</script>
</body>
</html>
