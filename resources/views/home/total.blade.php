
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
    <link rel="stylesheet" href="{{secure_url('lib/font-awesome-4.7.0/css/font-awesome.min.css')}}" media="all">


</head>
<body>

<div class="layui-fluid">
    <div class="layui-card">
        <div class="layui-card-body">
            <form class="layui-form layui-form-pane">
                <div class="layui-form-item">
                    <div class="layui-input-inline" style="width:120px">
                        <select name="state">
                            <option value="">请选择状态</option>
                            <option value='1'>待审核</option><option value='2'>已取消</option><option value='3'>已完成</option>
                        </select>
                    </div>
                    <div class="layui-input-inline">
                        <select name="mchid" lay-search>
                            <option value="">全部商户</option>
                            <option value='10'>1</option><option value='2'>22</option><option value='3'>ABC</option><option value='8'>hhh</option><option value='9'>pppppp</option><option value='5'>XF1234</option><option value='7'>万博</option><option value='1'>下游名称</option><option value='4'>五行</option><option value='6'>张三</option>
                        </select>
                    </div>
                    <div class="layui-input-inline">
                        <select name="daifuid" lay-search>
                            <option value="">全部代付</option>
                            <option value='1'>11</option>
                        </select>
                    </div>
                    <div class="layui-input-inline" style="width:120px">
                        <select name="xiafatype">
                            <option value="">下发类型</option>
                            <option value='1'>自主下发</option><option value='2'>指派通道</option><option value='3'>指派代付</option>
                        </select>
                    </div>
                    <div class="layui-input-inline">
                        <input type="text" name="begin" id="begin" placeholder="开始时间" class="layui-input" autocomplete="off">
                    </div>
                    <div class="layui-input-inline">
                        <input type="text" name="end" id="end" placeholder="结束时间" class="layui-input" autocomplete="off">
                    </div>
                    <div class="layui-input-inline" style="width: 60px">
                        <button class="layui-btn" lay-submit="" lay-filter="form_submit">搜索</button>
                    </div>
                    <div class="layui-input-inline">
                        <button class="layui-btn" lay-submit="" lay-filter="form_export">导出结果</button>
                    </div>
                </div>
            </form>
            <table id="dataTable" lay-filter="dataTable"></table>
            <script type="text/html" id="coltpl_state">

                <button class="layui-btn layui-btn-xs layui-btn-normal">已完成</button>
            </script>
            <script type="text/html" id="rowtool">

                <button class="layui-btn layui-btn-xs" lay-event="details">查看</button>
            </script>
        </div>
    </div>
</div>


<script src="/layuiadmin/layui/layui.js"></script>

<script>
    layui.config({ base: '/layuiadmin/' }).extend({ index: 'lib/index' }).use(["index", "admin", "carousel", "console", "table"], function () {
        var $ = layui.$, table = layui.table, admin = layui.admin;


        table.render({
            elem: '#datatable-payable',
            title: '商户余额',
            url: '/home/getmaccts',
            page: false,
            limit: 20,
            cellMinWidth: 80,
            toolbar: '#toolbar',
            autoSort: true,
            totalRow: true,
            cols: [[
                { field: 'Id', title: '商户ID', sort: true },
                { field: 'Name', title: '商户名称' },
                { field: 'State', title: '状态', sort: true, templet: '#col-mch-status', totalRowText: '合计：' },
                { field: 'Balance', title: '余额', sort: true, totalRow: true }
            ]],
            parseData: function (data) {
                return {
                    "code": 0,
                    "count": data.length,
                    "data": data
                }
            }
        });
        table.render({
            elem: '#datatable-receivable',
            title: '渠道余额',
            url: '/home/getpaccts',
            page: false,
            cellMinWidth: 80,
            toolbar: '#toolbar',
            autoSort: true,
            totalRow: true,
            cols: [[
                { field: 'Name', title: '通道名称' },
                { field: 'State', title: '状态', sort: true, templet: '#col-pass-status', totalRowText: '合计：' },
                { field: 'Rate', title: '费率' },
                { field: 'Balance', title: '余额', sort: true, totalRow: true }
            ]],
            parseData: function (data) {
                return {
                    "code": 0,
                    "count": data.length,
                    "data": data
                }
            }
        });

        $(".btn_google_auth").click(function () {
            parent.layui.index.openTabsPage("/admin/bindgoogleauth", '谷歌验证');
        });

        $("#div-passage-jiankong-container").height($("#div-passage-jiankong").height());
        $("#div-merchant-jiankong-container").height($("#div-merchant-jiankong").height());
    });
</script>

</body>
</html>
