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
        <div style="margin: 10px 10px 10px 10px" class="layui-card-header"><i class="fa fa-warning icon"></i>下游预付</div>
        <fieldset class="table-search-fieldset">




            <div class="layui-col-md12">
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


                        <th>预付剩余</th>
                        <th>操作</th>
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
                            <?php if(!empty(\App\Models\Order::merchantAmount($merchantChannel->merchant->account, $start_date, $end_date))) { ?>
                            <?php $merchantAmount = sprintf("%.2f", \App\Models\Order::merchantAmount($merchantChannel->merchant->account, $start_date, $end_date)->merchant_amount/100) ?>
                            <?php } else { ?>
                            <?php
                                $merchantAmount = '0.00'; }
                                $total_close = $total_close + $merchantAmount;
                            ?>
                            <?php
                                $advance = sprintf("%.2f", $merchantChannel->merchant->advance($merchantChannel->merchant->id, $start_date, $end_date)/100);
                                $total_advance = $total_advance + $advance;
                            ?>
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
                        <th>
                            <button type="button" onclick="edit_code({{$merchantChannel->merchant->id}})" class="layui-btn layui-btn-warm layui-btn-xs">编辑</button>




                        </th>
                    </tr>
                    @endforeach



                    <tr>
                        <th>总计：</th>
                        <th>{{sprintf("%.2f",$total_amount)}}</th>
                        <th>{{sprintf("%.2f",$total_residue)}}</th>
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

        $(".data-add-btn").on("click", function () {
            var index = layer.open({
                title: '添加',
                type: 2,
                shade: 0.2,
                maxmin:true,
                shadeClose: true,
                area: ['100%', '100%'],
                content: '{{secure_url('advance/create')}}',
            });
            $(window).on("resize", function () {
                layer.full(index);
            });

            return false;
        });


        edit_code = function (id) {
            var index = layer.open({
                title: '编辑预付',
                type: 2,
                shade: 0.2,
                maxmin:true,
                shadeClose: true,
                area: ['100%', '100%'],
                content: '{{secure_url('advance')}}/'+id+'/edit',

            });
            $(window).on("resize", function () {
                layer.full(index);
            });

            return false;
        };

        var clipboard = new ClipboardJS('.info_copy');

        clipboard.on('success', function (e) {
            layer.msg('复制成功！');
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
