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
            background-color: #257cc5;
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
            background-color: #e55744;
            color: #FFF;
            font-size: 8px;
            white-space: nowrap;
            border-radius: 11px;
            text-align: center;
        }
    </style>
</head>
<body>
<div class="layuimini-container">
    <div class="layuimini-main">
        <div style="margin: 10px 10px 10px 10px" class="layui-card-header"><i class="fa fa-warning icon"></i>提现列表</div>
        <fieldset class="table-search-fieldset">

            <div style="margin: 10px 10px 10px 10px">

                <form class="layui-form layui-form-pane" action="">
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
                            <label class="layui-form-label">状态</label>
                            <div class="layui-input-inline">
                                <select type="text" name="status" class="layui-input">
                                    <option value="">请选择</option>
                                    <option @if($status == 0 && $status != null) selected @endif value="0">取消</option>
                                    <option @if($status == 1) selected @endif value="1">成功</option>
                                    <option @if($status == 2) selected @endif value="2">等待</option>
                                </select>
                            </div>
                        </div>

                        <div class="layui-inline">
                            <button type="submit" class="layui-btn layui-btn-normal" lay-submit ><i class="layui-icon"></i> 搜 索</button>
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

                            <th>姓名</th>
                            <th>卡号</th>
                            <th>所属银行</th>
                            <th>状态</th>
                            <th>提现时间</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($cashs as $cash)
                        <tr>

                            <th>{{$cash->name}}</th>
                            <th>{{$cash->bank_account}}</th>
                            <th>{{$cash->bank}}</th>
                            @if($cash->status == 0)
                                <th><button class="layui-btn layui-btn-sm layui-btn-danger"> 取消 </button></th>
                            @elseif($cash->status == 1)
                                <th> <button class="layui-btn layui-btn-sm"> 成功 </button></th>
                            @elseif($cash->status == 2)
                                <th> <button class="layui-btn layui-btn-sm layui-btn-warm"> 等待 </button></th>
                            @endif


                            <th>{{date('Y-m-d H:i:s',$cash->created)}}</th>

                            <th>
                                <button type="button" onclick="edit_cash({{$cash->id}})" class="layui-btn layui-btn-warm layui-btn-sm">详细</button>
                            </th>

                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                    {{ $cashs->appends([
                         'start_time' => date('Y-m-d H:i:s', $start_time),
                         'end_time' => date('Y-m-d H:i:s', $end_time),

                         'status' => $status
                     ])->links() }}
                </div>
        </fieldset>
    </div>



    </div>
</div>
<iframe id="export" style="width: 1px;height:1px;display: none;" src=""></iframe>
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
        edit_cash = function (id) {
            var index = layer.open({
                title: '提现详情',
                type: 2,
                shade: 0.2,
                maxmin:true,
                shadeClose: true,
                area: ['100%', '100%'],
                content: '{{secure_url('merchant/cash')}}/'+id,

            });
            $(window).on("resize", function () {
                layer.full(index);
            });

            return false;
        };
    });




</script>


<script>




</script>
</body>
</html>
