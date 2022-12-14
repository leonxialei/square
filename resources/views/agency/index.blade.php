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
        <div style="margin: 10px 10px 10px 10px" class="layui-card-header"><i class="fa fa-warning icon"></i>????????????</div>
        <fieldset class="table-search-fieldset">

            <div style="margin: 10px 10px 10px 10px">
                <form class="layui-form layui-form-pane" action="">
                    <div class="layui-inline">
                        <label class="layui-form-label">????????????</label>
                        <div class="layui-input-inline">
                            <input type="text" value="{{$start_date}}" name="start_date" id="start-date" lay-verify="date" placeholder="yyyy-MM-dd" autocomplete="off" class="layui-input">
                        </div>
                    </div>

                    <div class="layui-inline">
                            <label class="layui-form-label">????????????</label>
                            <div class="layui-input-inline">
                                <select type="text" name="merchant_id" class="layui-input">
                                    <option value="">?????????</option>
                                    @foreach($merchants as $merchant)
                                    <option
                                        @if($request->get('merchant_id') == $merchant->id)
                                        selected="selected"
                                        @endif
                                        value="{{$merchant->id}}" >{{$merchant->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>




                        <div class="layui-inline">
                            <button type="submit" class="layui-btn layui-btn-primary" lay-submit ><i class="layui-icon">???</i> ??? ???</button>
                        </div>



                    <div class="layui-inline" pane="">
                        <label class="layui-form-label">??????????????????</label>
                        <div class="layui-input-block">
                            <input type="checkbox" checked="" name="open" lay-skin="switch" lay-filter="switchTest" title="??????">
                            <div class="layui-unselect layui-form-switch layui-form-onswitch" lay-skin="_switch"><em></em><i></i></div>
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
                        <th>??????</th>
                        <th>????????????</th>
                        <th>????????????</th>
                        <th>????????????</th>
                        <th>?????????????????????</th>
                        <th>?????????</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $total_amount = 0.00;
                    $total_close = 0.00;
                    $total_advance = 0.00;
                    $total_residue = 0.00;
                    ?>
                    @foreach($agencys as $agency)
                    <tr>
                        <th>{{$agency->name}}</th>
                        <th>{{$agency->agency->name}}</th>
                        <th>
                            @if(!empty(\App\Models\Order::merchantOrder($agency->account, $start_date, $end_date, [0,1,2,3])))
                                <?php $merchant_count = count(\App\Models\Order::merchantOrder($agency->account, $start_date, $end_date, [0,1,2,3])); ?>
                            @else
                                <?php $merchant_count = 0;?>
                            @endif
                            {{$merchant_count}}
                        </th>
                        <th>
                            @if(!empty(\App\Models\Order::merchantOrder($agency->account, $start_date, $end_date, 2)))
                                <?php $succeed_merchant_count = count(\App\Models\Order::merchantOrder($agency->account, $start_date, $end_date, 2)); ?>
                            @else
                                <?php $succeed_merchant_count = 0;?>
                            @endif
                            {{$succeed_merchant_count}}

                        </th>
                        <th>
                            <?php
                            if(!empty(\App\Models\Order::merchantOriginalAmount($agency->account, $start_date, $end_date))) {
                                $originalAmount = sprintf("%.2f", \App\Models\Order::merchantOriginalAmount($agency->account, $start_date, $end_date)->amount/100);
                            } else {
                                $originalAmount = '0.00';
                            }
                            $total_amount = $total_amount + $originalAmount;
                            ?>
                            {{$originalAmount}}
                        </th>
                        <th>
                            <?php
                                if($merchant_count != 0) {
                                    echo (sprintf("%.2f", $succeed_merchant_count/$merchant_count * 100)).'%';
                                } else {
                                    echo '0%';
                                }
                            ?>
                        </th>
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
        // ??????????????????

        form.on('switch(switchTest)', function (data) {
            layer.msg('???????????????' + (this.checked ? '??????' : '??????'), {
                offset: '6px'
            });
            // layer.tips('?????????????????????????????????????????????????????????????????????????????????ON|OFF', data.othis)
            switch_mark = this.checked;

        });

        $(".data-add-btn").on("click", function () {
            var index = layer.open({
                title: '??????',
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
                title: '????????????',
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
            layer.msg('???????????????');
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
