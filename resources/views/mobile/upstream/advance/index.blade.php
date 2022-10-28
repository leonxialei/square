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
        <div style="margin: 10px 10px 10px 10px" class="layui-card-header"><i class="fa fa-warning icon"></i>上游预付</div>
        <fieldset class="table-search-fieldset">

            <div style="margin: 10px 10px 10px 10px">
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
                        <th>上游名称</th>
                        <th>跑量金额</th>
                        <th>预付剩余</th>
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
                                <th>
                                    <button onclick="edit_code({{$upstream->id}})" type="button" class="layui-btn layui-btn-warm layui-btn-xs">修改预付</button>
                                </th>
                            </tr>
                        @endforeach

                        <tr>
                            <th>总计：</th>
                            <th>{{sprintf("%.2f",$total_original_amount)}}</th>
                            <th>{{sprintf("%.2f",$total_balance)}}</th>
                            <th></th>
                        </tr>



                    </tbody>
                </table>

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
