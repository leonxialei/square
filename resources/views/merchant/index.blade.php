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
                    <div class="layui-form-item">
                        <div class="layui-inline">
                            <label class="layui-form-label">????????????</label>
                            <div class="layui-input-inline">
                                <input value="{{$request->get('account')}}" type="text" name="account" autocomplete="off" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-inline">
                            <label class="layui-form-label">????????????</label>
                            <div class="layui-input-inline">
                                <input value="{{$request->get('name')}}" type="text" name="name" autocomplete="off" class="layui-input">
                            </div>
                        </div>

                        <div class="layui-inline">
                            <label class="layui-form-label">??????</label>
                            <div class="layui-input-inline">
                                <select type="text" name="status" class="layui-input">
                                    <option value="">?????????</option>
                                    <option
                                        <?php if($request->get('status') == 1) {?>
                                        selected="selected"
                                        <?php } ?>
                                        value="1" >??????</option>
                                    <option
                                        <?php if($request->has('status') && $request->get('status') == 0) {?>
                                        selected="selected"
                                        <?php } ?>
                                        value="0" >??????</option>
                                </select>
                            </div>
                        </div>

                        <div class="layui-inline">
                            <button type="submit" class="layui-btn layui-btn-primary" lay-submit ><i class="layui-icon">???</i> ??? ???</button>
                        </div>

                        <div class="layui-inline">
                            <button class="layui-btn data-add-btn"> ??????????????? </button>
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
                        <th>?????????ID</th>
                        <th>????????????</th>
                        <th>????????????</th>
                        <th>??????</th>
                        <th>????????????</th>
                        <th>??????</th>
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
                                        <button type="button" class="layui-btn layui-btn-xs">??????</button>
                                    @elseif($merchant->status == 0)
                                        <button type="button" class="layui-btn layui-btn-danger layui-btn-xs">??????</button>
                                    @endif
                                </td>
                                <td>{{date('Y-m-d H:i:s', $merchant->created)}}</td>
                                <td>
                                    <button type="button" onclick="edit_code({{$merchant->id}})" class="layui-btn layui-btn-warm layui-btn-xs">??????</button>
                                    <button
                                        data-clipboard-action="copy"
                                        data-clipboard-text="??????????????????
????????????:http://78.142.245.26:2089/
????????????:{{$merchant->account}}
??????:{{$merchant->password}}
??????ID:{{$merchant->account}}
????????????:{{$merchant->token}}

????????????:http://78.142.245.26:2088/api/pay/create_order
?????????:{{$merchant->account}}
??????:???????????????????????????????????????
????????????????????????????????????ios???android??????Authenticator APP???????????????????????????
???????????????????????????https://www.showdoc.com.cn/1592147104280258/9368878050714704

???????????????UHv4q7Dj

??????IP: 78.142.245.26"
                                        type="button" class="layui-btn layui-btn-warm layui-btn-xs info_copy">??????????????????</button>



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



        // ??????????????????


        // ??????????????????
        $(".data-add-btn").on("click", function () {
            var index = layer.open({
                title: '????????????',
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
                title: '????????????',
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

        // ??????????????????
        // $(".data-delete-btn").on("click", function () {
        //     var checkStatus = table.checkStatus('currentTableId')
        //         , data = checkStatus.data;
        //     layer.alert(JSON.stringify(data));
        // });

        var clipboard = new ClipboardJS('.info_copy');

        clipboard.on('success', function (e) {
            layer.msg('???????????????');
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
