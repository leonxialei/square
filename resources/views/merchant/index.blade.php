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
        <div style="margin: 10px 10px 10px 10px" class="layui-card-header"><i class="fa fa-warning icon"></i>商户管理</div>
        <fieldset class="table-search-fieldset">
            <div style="margin: 10px 10px 10px 10px">
                <form class="layui-form layui-form-pane" action="">
                    <div class="layui-form-item">
                        <div class="layui-inline">
                            <label class="layui-form-label">登录账号</label>
                            <div class="layui-input-inline">
                                <input value="{{$request->get('account')}}" type="text" name="account" autocomplete="off" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-inline">
                            <label class="layui-form-label">商户名称</label>
                            <div class="layui-input-inline">
                                <input value="{{$request->get('name')}}" type="text" name="name" autocomplete="off" class="layui-input">
                            </div>
                        </div>

                        <div class="layui-inline">
                            <label class="layui-form-label">状态</label>
                            <div class="layui-input-inline">
                                <select type="text" name="status" class="layui-input">
                                    <option value="">请选择</option>
                                    <option
                                        <?php if($request->get('status') == 1) {?>
                                        selected="selected"
                                        <?php } ?>
                                        value="1" >开启</option>
                                    <option
                                        <?php if($request->has('status') && $request->get('status') == 0) {?>
                                        selected="selected"
                                        <?php } ?>
                                        value="0" >关闭</option>
                                </select>
                            </div>
                        </div>

                        <div class="layui-inline">
                            <button type="submit" class="layui-btn layui-btn-primary" lay-submit ><i class="layui-icon"></i> 搜 索</button>
                        </div>

                        <div class="layui-inline">
                            <button class="layui-btn data-add-btn"> 添加新商户 </button>
                        </div>

                        <div class="layui-inline">
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
                        <th>机器人ID</th>
                        <th>登录账号</th>
                        <th>商户名称</th>
                        <th>状态</th>
                        <th>创建时间</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                        @foreach($merchants as $merchant)
                            <tr>
                                <td>{{$merchant->id}}</td>
                                <td>{{$merchant->account}}</td>
                                <td>{{$merchant->name}}</td>
                                <td>
                                    @if($merchant->status == 1)
                                        <button type="button" class="layui-btn layui-btn-xs">开启</button>
                                    @elseif($merchant->status == 0)
                                        <button type="button" class="layui-btn layui-btn-danger layui-btn-xs">关闭</button>
                                    @endif
                                </td>
                                <td>{{date('Y-m-d H:i:s', $merchant->created)}}</td>
                                <td>
                                    <button type="button" onclick="edit_code({{$merchant->id}})" class="layui-btn layui-btn-warm layui-btn-xs">修改</button>
                                    <button
                                        data-clipboard-action="copy"
                                        data-clipboard-text="复制开户信息
后台地址:http://78.142.245.26:2089/
登录账号:{{$merchant->account}}
密码:{{$merchant->password}}
商户ID:{{$merchant->account}}
商户密钥:{{$merchant->token}}

下单地址:http://78.142.245.26:2088/api/pay/create_order
商户号:{{$merchant->account}}
密钥:登录后绑定谷歌验证码后查看
注：首次登录无需验证码。ios、android下载Authenticator APP进行谷歌验证码绑定
请求下单对接文档：https://www.showdoc.com.cn/1592147104280258/9368878050714704

文档密码：UHv4q7Dj

回调IP: 78.142.245.26"
                                        type="button" class="layui-btn layui-btn-warm layui-btn-xs info_copy">复制开户信息</button>



                                </td>
                            </tr>

                        @endforeach

                    </tbody>
                </table>
                {{$merchants->links()}}
            </div>
        </fieldset>





    </div>
</div>
<script src="{{secure_url('lib/layui-v2.5.5/layui.js')}}" charset="utf-8"></script>
<script src="{{secure_url('lib/dist/clipboard.min.js')}}" charset="utf-8"></script>
<script>
    layui.use(['form', 'table'], function () {
        var $ = layui.jquery,
            form = layui.form,
            table = layui.table,
            layuimini = layui.layuimini;



        // 监听搜索操作


        // 监听添加操作
        $(".data-add-btn").on("click", function () {
            var index = layer.open({
                title: '添加商户',
                type: 2,
                shade: 0.2,
                maxmin:true,
                shadeClose: true,
                area: ['100%', '100%'],
                content: '{{secure_url('merchant/create')}}',
            });
            $(window).on("resize", function () {
                layer.full(index);
            });

            return false;
        });

        edit_code = function (id) {
            var index = layer.open({
                title: '编辑商户',
                type: 2,
                shade: 0.2,
                maxmin:true,
                shadeClose: true,
                area: ['100%', '100%'],
                content: '{{secure_url('merchant')}}/'+id+'/edit',

            });
            $(window).on("resize", function () {
                layer.full(index);
            });

            return false;
        };

        // 监听删除操作
        // $(".data-delete-btn").on("click", function () {
        //     var checkStatus = table.checkStatus('currentTableId')
        //         , data = checkStatus.data;
        //     layer.alert(JSON.stringify(data));
        // });

        var clipboard = new ClipboardJS('.info_copy');

        clipboard.on('success', function (e) {
            layer.msg('复制成功！');
            console.log(e);
        });

        clipboard.on('error', function (e) {
            console.log(e);
        });

    });


</script>


<script>




</script>
</body>
</html>
