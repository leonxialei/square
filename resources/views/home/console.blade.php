
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>&#x4E3B;&#x9875;</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <link rel="stylesheet" href="{{secure_url('lib/layui-v2.5.5/css/layui.css')}}" media="all">
    <link rel="stylesheet" href="{{secure_url('css/layuimini.css')}}" media="all">
    <link rel="stylesheet" href="{{secure_url('css/template.css')}}" media="all">
    <link rel="stylesheet" href="{{secure_url('css/admin.css')}}" media="all">
    <link rel="stylesheet" href="{{secure_url('css/console.css')}}" media="all">
    <script src="https://cdn.staticfile.org/html5shiv/r29/html5.min.js"></script>
    <script src="https://cdn.staticfile.org/respond.js/1.4.2/respond.min.js"></script>

</head>
<body>

<div class="layui-fluid">
    <div class="layui-row layui-col-space15">




        <div class="layui-col-md8">
            <div class="layui-card">
                <div class="layui-card-header">快捷导航</div>
                <div class="layui-card-body layadmin-shortcut">
                    <ul class="layui-row layui-col-space10 layui-this">
                        <li class="layui-col-lg2 layui-col-xs3">
                            <a href="javascript:;" data-iframe-tab="merchant" data-title="商户管理" data-icon="fa fa-gears">
                                <i class="layui-icon layui-icon-group"></i>
                                <cite>商户管理</cite>
                            </a>
                        </li>
                        <li class="layui-col-lg2 layui-col-xs3">
                            <a href="javascript:;" data-iframe-tab="upstream" data-title="通道管理" data-icon="fa fa-gears">
                                <i class="layui-icon layui-icon-upload"></i>
                                <cite>通道管理</cite>
                            </a>
                        </li>
                        <li class="layui-col-lg2 layui-col-xs3">
                            <a href="javascript:;" data-iframe-tab="order" data-title="订单管理" data-icon="fa fa-gears">
                                <i class="layui-icon layui-icon-form"></i>
                                <cite>订单管理</cite>
                            </a>
                        </li>
                        <li class="layui-col-lg2 layui-col-xs3">
                            <a href="javascript:;" data-iframe-tab="home/total" data-title="商户提现" data-icon="fa fa-gears">
                                <i class="layui-icon layui-icon-dollar"></i>
                                <cite>商户提现</cite>
                            </a>
                        </li>
                        <li class="layui-col-lg2 layui-col-xs3">
                            <a id="export_but" lay-href="/page/help">
                                <i class="layui-icon layui-icon-tree"></i>
                                <cite>报表下载</cite>
                            </a>
                        </li>
                        <li class="layui-col-lg2 layui-col-xs3">
                            <a lay-href="/platformacctlog/index">
                                <i class="layui-icon layui-icon-rmb"></i>
                                <cite>系统积分</cite>
                            </a>
                        </li>
                    </ul>

                </div>
            </div>

            <div class="layui-card">
                <div class="layui-card-header">今日订单统计</div>
                <div class="layui-card-body">
                    <div class="layadmin-backlog">
                        <ul class="layui-row layui-col-space10">
                            <li class="layui-col-xs6 layui-col-md4 layui-col-lg4">
                                <a class="layadmin-backlog-body">
                                    <h3>总金额</h3>
                                    <p>
                                        <cite>@if(empty($pay_amount))
                                                0
                                            @else
                                                {{sprintf("%.2f",$pay_amount/100)}}
                                            @endif</cite>
                                    </p>
                                </a>
                            </li>

                            <li class="layui-col-xs6 layui-col-md4 layui-col-lg4">
                                <a class="layadmin-backlog-body">
                                    <h3>成交额</h3>
                                    <p>
                                        <cite>
                                            @if(empty($done_pay_amount))
                                                0
                                            @else
                                                {{sprintf("%.2f",$done_pay_amount/100)}}
                                            @endif
                                        </cite>
                                    </p>
                                </a>
                            </li>
                            <li class="layui-col-xs6 layui-col-md4 layui-col-lg2">
                                <a class="layadmin-backlog-body">
                                    <h3>订单量</h3>
                                    <p>
                                        <cite>
                                            {{$count}}
                                        </cite>
                                    </p>
                                </a>
                            </li>
                            <li class="layui-col-xs6 layui-col-md4 layui-col-lg2">
                                <a class="layadmin-backlog-body">
                                    <h3>成功订单量</h3>
                                    <p>
                                        <cite>{{$done_count}}</cite>
                                    </p>
                                </a>
                            </li>
                            <li class="layui-col-xs6 layui-col-md4 layui-col-lg2">
                                <a class="layadmin-backlog-body">
                                    <h3>成功率</h3>
                                    @if($count!=0)
                                    <p>
                                        <cite>{{sprintf("%.2f",($done_count/$count)*100)}}%</cite>
                                    </p>
                                    @else
                                        <p>
                                            <cite>0%</cite>
                                        </p>
                                    @endif
                                </a>
                            </li>

                        </ul>
                    </div>
                </div>
            </div>

            <div class="layui-card">
                <div class="layui-card-header">昨日订单统计</div>
                <div class="layui-card-body">
                    <div class="layadmin-backlog">
                        <ul class="layui-row layui-col-space10">
                            <li class="layui-col-xs6 layui-col-md4 layui-col-lg4">
                                <a class="layadmin-backlog-body">
                                    <h3>总金额</h3>
                                    <p>
                                        <cite>@if(empty($ypay_amount))
                                                0
                                            @else
                                                {{sprintf("%.2f",$ypay_amount/100)}}
                                            @endif</cite>
                                    </p>
                                </a>
                            </li>

                            <li class="layui-col-xs6 layui-col-md4 layui-col-lg4">
                                <a class="layadmin-backlog-body">
                                    <h3>成交额</h3>
                                    <p>
                                        <cite>
                                            @if(empty($ydone_pay_amount))
                                                0
                                            @else
                                                {{sprintf("%.2f",$ydone_pay_amount/100)}}
                                            @endif
                                        </cite>
                                    </p>
                                </a>
                            </li>
                            <li class="layui-col-xs6 layui-col-md4 layui-col-lg2">
                                <a class="layadmin-backlog-body">
                                    <h3>订单量</h3>
                                    <p>
                                        <cite>
                                            {{$ycount}}
                                        </cite>
                                    </p>
                                </a>
                            </li>
                            <li class="layui-col-xs6 layui-col-md4 layui-col-lg2">
                                <a class="layadmin-backlog-body">
                                    <h3>成功订单量</h3>
                                    <p>
                                        <cite>{{$ydone_count}}</cite>
                                    </p>
                                </a>
                            </li>
                            <li class="layui-col-xs6 layui-col-md4 layui-col-lg2">
                                <a class="layadmin-backlog-body">
                                    <h3>成功率</h3>
                                    @if($ycount!=0)
                                    <p>
                                        <cite>{{sprintf("%.2f",($ydone_count/$ycount)*100)}}%</cite>
                                    </p>
                                    @else
                                        <p>
                                            <cite>0%</cite>
                                        </p>
                                    @endif
                                </a>
                            </li>

                        </ul>
                    </div>
                </div>
            </div>

            <div class="layui-row layui-col-space15">
                <div class="layui-col-md6">
                    <div class="layui-card">
                        <div class="layui-card-header">商户总账</div>
                        <div class="layui-card-body">
                            <div class="layadmin-backlog">
                                <ul class="layui-row layui-col-space10">
                                    <li class="layui-col-md4">
                                        <a class="layadmin-backlog-body">
                                            <h3>保证金</h3>
                                            <p>
                                                <cite>1222</cite>
                                            </p>
                                        </a>
                                    </li>
                                    <li class="layui-col-md4">
                                        <a class="layadmin-backlog-body">
                                            <h3>余额</h3>
                                            <p>
                                                <cite>0</cite>
                                            </p>
                                        </a>
                                    </li>
                                    <li class="layui-col-md4">
                                        <a class="layadmin-backlog-body">
                                            <h3>结余</h3>
                                            <p>
                                                <cite>-1222</cite>
                                            </p>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <div class="layadmin-backlog">
                                <table id="datatable-payable" lay-filter="datatable-payable"></table>
                                <script type="text/html" id="col-mch-status">
                                启用
                                </script>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="layui-col-md6">
                    <div class="layui-card">
                        <div class="layui-card-header">通道总账</div>
                        <div class="layui-card-body">
                            <div class="layadmin-backlog">
                                <ul class="layui-row layui-col-space10">
                                    <li class="layui-col-md4">
                                        <a class="layadmin-backlog-body">
                                            <h3>保证金</h3>
                                            <p>
                                                <cite>20000</cite>
                                            </p>
                                        </a>
                                    </li>
                                    <li class="layui-col-md4">
                                        <a class="layadmin-backlog-body">
                                            <h3>余额</h3>
                                            <p>
                                                <cite>0</cite>
                                            </p>
                                        </a>
                                    </li>
                                    <li class="layui-col-md4">
                                        <a class="layadmin-backlog-body">
                                            <h3>结余</h3>
                                            <p>
                                                <cite>20000</cite>
                                            </p>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <div class="layadmin-backlog">
                                <table id="datatable-receivable"></table>
                                <script type="text/html" id="col-pass-status">
                                    启用

                                </script>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="layui-col-md4">
{{--            <div class="layui-card">--}}
{{--                <div class="layui-card-header">通道监控：今日/昨日</div>--}}
{{--                <div class="layui-card-body">--}}
{{--                    <div class="layui-carousel layadmin-carousel layadmin-shortcut" id="div-passage-jiankong-container">--}}

{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
            <div class="layui-card">
                <div class="layui-card-header">商户监控：今日/昨日</div>
                <div class="layui-card-body">
                    <div class="layui-carousel layadmin-carousel layadmin-shortcut"
                         id="div-merchant-jiankong-container">

                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
<iframe id="export" style="width: 1px;height:1px;display: none" src=""></iframe>

<script src="{{secure_url('lib/layui-v2.5.5/layui.js?v=1.0.4')}}" charset="utf-8"></script>
<script src="{{secure_url('js/lay-config.js?v=1.0.4')}}" charset="utf-8"></script>
<script>
    layui.use(['element', 'layer', 'layuimini'], function () {
        var $ = layui.jquery,
            element = layui.element,
            layer = layui.layer;
        $.ajax({
            type: "GET",
            url: "{{secure_url('api/dashboard/down')}}/",
            dataType:'json',
            data: {
                '_token': '{{csrf_token()}}'
            },
            success: function (data) {
                var html = '';
                data.forEach(function(item,index,self){
                    html += '<div class="monitor-box">'
                    html += '<div class="title">'+item.name+'</div>'
                    html += '<span class="success">成功率：'+item.rate+'%</span>'
                    html += '<span class="amount">成交额：'+item.succeed+'</span>'
                    html += '<div class="layui-progress">'
                    html += '<div class="layui-progress-bar layui-bg-blue" lay-showPercent="yes" style="width:'+item.rate+'%;"></div>'
                    html += '</div></div>';
                })
                $('#div-merchant-jiankong-container').append(html);
            }
        });


        $('#export_but').click(function(){
            $('#export').attr('src', '');

            var url = '<?php echo secure_url('export/today') ?>';
            $('#export').attr('src', url);
        });

    });

    switch_mark = true;
    var timestart = 25;
    var timestep = -1;
    var timeID;
    function timecount() {
        if(timestart < 0){
            timestart = 25;
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
