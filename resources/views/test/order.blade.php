<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>宇宙支付测试</title>
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
        <div style="margin: 10px 10px 10px 10px" class="layui-card-header"><i class="fa fa-warning icon"></i>支付测试</div>
        <fieldset class="table-search-fieldset">

            <div style="margin: 10px 10px 10px 10px">


                <form class="layui-form layui-form-pane" action="">

                    <div class="layui">
                        <label class="layui-form-label">通道</label>
                        <div class="layui-input-inline">
                            <select type="text" name="channel_id" class="layui-input">
                                <option value="">请选择</option>
                                @foreach($upstreamChannels as $channel)
                                    <option
                                        value="{{$channel->id}}" >{{$channel->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="layui-inline" style="margin-top: 30px;">
                        <label class="layui-form-label">支付金额</label>

                        <div class="layui-input-inline">
                            <input type="number" value="30" name="amount" class="layui-input" placeholder="输入10等于10元">
                        </div>
                        <p style="margin-top: 10px;color: #da4f49;">*输入10等于10元</p>
                    </div>

                    </div>






                    <div class="layui" style="margin-top: 30px;">
                        <button type="button" onclick="toPay()" class="layui-btn data-add-btn"> 发起支付 </button>
                    </div>
                </form>
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




        toPay = function() {
            var channel_id = $('select[name="channel_id"]').val();
            var amount = $('input[name="amount"]').val();

            if(amount == '' || amount == undefined || amount ==0) {
                layer.msg('请填入正确的金额！');
                return false;
            }
            $.ajax({
                type: "POST",
                url: "{{secure_url('api/pay/order/test')}}",
                data: {
                    channel_id:channel_id,
                    amount:amount,
                    _token:'{{csrf_token()}}'
                },
                dataType:'json',
                async:false,
                success: function(msg){
                    if(msg.status == 200) {
                        if(msg.payUrl != '') {
                            window.location.href=msg.payUrl;
                        } else if(msg.qrUrl != '') {
                            window.location.href=msg.qrUrl;
                        }

                    } else {
                        layer.msg(msg.msg);
                    }
                }
            });
        };
    });



</script>


<script>




</script>
</body>
</html>
