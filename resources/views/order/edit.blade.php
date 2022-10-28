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
</head>
<body>
<div class="layuimini-container">
    <div class="layuimini-main">





        <form class="layui-form" method="post" action="{{secure_url('order')}}/{{$order->id}}">
            <div class="layui-form-item">
                <div class="layui">
                    <label class="layui-form-label">订单号</label>
                    <div class="layui-input-block">
                        <input value="YZ{{$order->OrderNo}}" type="text" name="OrderNo"  autocomplete="off" class="layui-input layui-disabled">
                    </div>
                </div>

            </div>
            <div class="layui-form-item">
                <div class="layui">
                    <label class="layui-form-label">商户ID</label>
                    <div class="layui-input-block">
                        <input value="{{$order->customer_id}}" type="text" name="customer_id"  autocomplete="off" class="layui-input ">
                    </div>
                </div>

            </div>
            <div class="layui-form-item">
                <div class="layui">
                    <label class="layui-form-label">商户订单号</label>
                    <div class="layui-input-block">
                        <input value="{{$order->mchOrderNo}}"  lay-verify="required" type="text" name="mchOrderNo"  autocomplete="off" class="layui-input layui-disabled">
                    </div>
                </div>

            </div>

            <div class="layui-form-item">
                <div class="layui">
                    <label class="layui-form-label">订单金额（元）</label>
                    <div class="layui-input-block">
                        <input value="{{sprintf("%.2f",$order->original_amount/100)}}" type="text" name="original_amount"  autocomplete="off" class="layui-input layui-disabled">
                    </div>
                </div>

            </div>

            <div class="layui-form-item">
                <div class="layui">
                    <label class="layui-form-label">实际支付（分）</label>
                    <div class="layui-input-block">
                        <input value="{{$order->pay_amount}}" lay-verify="required|number" type="number" name="pay_amount"  autocomplete="off" class="layui-input">
                    </div>
                </div>

            </div>

            <div class="layui-form-item">
                <div class="layui">
                    <label class="layui-form-label">创建时间</label>
                    <div class="layui-input-block">
                        <input value="{{date('Y-m-d H:i:s', $order->created)}}" type="text" name="created"  autocomplete="off" class="layui-input layui-disabled">
                    </div>
                </div>

            </div>
            <div class="layui-form-item">
                <div class="layui">
                    <label class="layui-form-label">支付成功时间</label>
                    <div class="layui-input-block">
                        <input value="" type="text" name="OrderNo"  autocomplete="off" class="layui-input">
                    </div>
                </div>

            </div>

            <div class="layui-form-item">
                <div class="layui">
                    <label class="layui-form-label">最后回调时间</label>
                    <div class="layui-input-block">
                        <input value="" type="text" name="OrderNo"  autocomplete="off" class="layui-input">
                    </div>
                </div>

            </div>

            <div class="layui-form-item">
                <div class="layui">
                    <label class="layui-form-label">回调次数</label>
                    <div class="layui-input-block">
                        <input value="" type="text" name="OrderNo"  autocomplete="off" class="layui-input">
                    </div>
                </div>

            </div>

            <div class="layui-form-item">
                <div class="layui">
                    <label class="layui-form-label">回调地址</label>
                    <div class="layui-input-block">
                        <input value="{{$order->notifyUrl}}" type="text" name="OrderNo"  autocomplete="off" class="layui-input layui-disabled">
                    </div>
                </div>

            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">通道</label>
                <div class="layui-input-block">
                    <select name="is_amount" disabled="disabled"  lay-verify="required" class="layui-disabled">
                        <option value="">请选择</option>
                        @foreach($upstreams as $upstream)
                        <option
                            @if($upstream->id == $order->upstream_id)
                            selected="selected"
                            @endif
                            value="1" >{{$upstream->name}}</option>
                        @endforeach
                    </select>
                </div>

            </div>

            <div class="layui-form-item">
                <div class="layui">
                    <label class="layui-form-label">上游订单号</label>
                    <div class="layui-input-block">
                        <input value="{{$order->upOrderNo}}" type="text" name="OrderNo"  autocomplete="off" class="layui-input layui-disabled">
                    </div>
                </div>

            </div>


            <div class="layui-form-item">
                <label class="layui-form-label">订单状态</label>
                <div class="layui-input-block">
                    <select name="status"  lay-verify="required">
                        <option value="">请选择</option>
                        <option
                            @if($order->status == 3)
                            selected="selected"
                            @endif
                            value="3" >创建失败</option>
                        <option
                            @if($order->status == 0)
                            selected="selected"
                            @endif
                            value="0" >订单生成</option>
                        <option
                            @if($order->status == 100)
                            selected="selected"
                            @endif
                            value="100" >支付中</option>
                        <option
                            @if($order->status == 1)
                            selected="selected"
                            @endif
                            value="1" >支付成功</option>
                        <option
                            @if($order->status == 2)
                            selected="selected"
                            @endif
                            value="2" >处理完成</option>

                    </select>
                </div>

            </div>

            <div class="layui-form-item">
                <div class="layui">
                    <label class="layui-form-label">上游结果</label>
                    <div class="layui-input-block">
                        <input value="{{$order->upCallbackUrl}}" type="text" name="OrderNo"  autocomplete="off" class="layui-input layui-disabled">
                    </div>
                </div>

            </div>









            <div class="layui-form-item">
                <div class="layui-input-block">
                    <input type="hidden" name="_token" value="{{csrf_token()}}" >
                    <input type="hidden" name="_method" value="put" >
                    <button class="layui-btn" lay-submit="" lay-filter="demo1">立即提交</button>
                    <button type="reset" class="layui-btn layui-btn-primary">重置</button>
                </div>
            </div>
        </form>


    </div>
</div>

<script src="{{secure_url('lib/layui-v2.5.5/layui.js')}}" charset="utf-8"></script>
<!-- 注意：如果你直接复制所有代码到本地，上述js路径需要改成你本地的 -->
<script>
    layui.use(['form', 'layedit', 'laydate'], function () {
        var form = layui.form
            , layer = layui.layer
            , layedit = layui.layedit
            , laydate = layui.laydate;







    });
</script>

</body>
</html>
