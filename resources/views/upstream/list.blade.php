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
                <form class="layui-form layui-form-pane" action="">
                    <div class="layui-inline">
                        <label class="layui-form-label">上游名称</label>
                        <div class="layui-input-inline">
                            <input type="text" value="{{$name}}" name="name" class="layui-input">
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
                        <th>机器人ID</th>
                        <th>上游名称</th>

                        <th>注册时间</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($upstreams as $upstream)
                    <tr>
                        <th>{{$upstream->id}}</th>
                        <th>{{$upstream->name}}</th>

                        <th>{{$upstream->created}}</th>
                        <th><button type="button" onclick="del_code({{$upstream->id}})" class="layui-btn layui-btn-danger layui-btn-xs">删除</button></th>

                    </tr>
                    @endforeach





                    </tbody>
                </table>
                {{$upstreams->appends([
                                  'name' => $name,
                              ])->links()}}
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


        del_code = function (id) {
            if(confirm('您确定要删除此通道吗？')) {
                $.ajax({
                    type: "PUT",
                    url: "{{secure_url('api/upstream')}}/"+id,
                    async: false,
                    data: {
                        '_token': '{{csrf_token()}}'
                    },
                    success: function (data) {
                        if(data.code != 200) {
                            layer.msg('非法操作！');
                        }else if(data.code == 200) {
                            window.location.reload();
                        }
                    }
                })
            }
        };

    });
</script>


<script>




</script>
</body>
</html>
