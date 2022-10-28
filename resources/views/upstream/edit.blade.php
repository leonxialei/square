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





        <form class="layui-form" method="post" action="{{secure_url('upstream')}}/{{$merchantChannel->id}}">
            <div class="layui-form-item">
                <label class="layui-form-label">商户</label>
                <div class="layui-input-block">
                    <select name="merchant_id"  lay-verify="required">
                        <option value="">请选择</option>
                        @foreach($merchants as $merchant)
                            <option
                                @if($merchantChannel->merchant_id == $merchant->id)
                                selected="selected"
                                @endif
                                value="{{$merchant->id}}" >{{$merchant->name}}</option>
                        @endforeach
                    </select>
                </div>

            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">通道</label>
                <div class="layui-input-block">
                    <select name="channel_id"  lay-verify="required">
                        <option value="">请选择</option>
                        @foreach($channels as $channel)
                            <option
                                @if($merchantChannel->channel_id == $channel->id)
                                selected="selected"
                                @endif
                                value="{{$channel->id}}" >{{$channel->name}}</option>
                        @endforeach
                    </select>
                </div>

            </div>


            <div class="layui-form-item">
                <div class="layui">
                    <label class="layui-form-label">权重</label>
                    <div class="layui-input-block">
                        <input value="{{$merchantChannel->weight}}" type="number" name="weight" lay-verify="required|number" autocomplete="off" class="layui-input">
                    </div>
                </div>

            </div>

            <div class="layui-form-item">
                <div class="layui">
                    <label class="layui-form-label">商户费率</label>
                    <div class="layui-input-block">
                        <input placeholder="千分比费率" value="{{$merchantChannel->rate}}" type="number" name="rate" lay-verify="required|number" autocomplete="off" class="layui-input">
                    </div>
                </div>

            </div>

            <div class="layui-form-item">
                <div class="layui">
                    <label class="layui-form-label">一级商户代理费率</label>
                    <div class="layui-input-block">
                        <input placeholder="千分比费率" value="{{$merchantChannel->agent_rate}}" type="number" name="agent_rate" lay-verify="required|number" autocomplete="off" class="layui-input">
                    </div>
                </div>

            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">是否固定金额</label>
                <div class="layui-input-block">
                    <select disabled class="layui-disabled" name="is_amount"  lay-verify="required">
                        <option value="">请选择</option>
                        <option
                            @if($merchantChannel->is_amount == 1)
                            selected="selected"
                            @endif
                            value="1" >是</option>
                        <option
                            @if($merchantChannel->is_amount == 0)
                            selected="selected"
                            @endif
                            value="0" >否</option>
                    </select>
                </div>

            </div>

            <div class="layui-form-item">
                <div class="layui">
                    <label class="layui-form-label">固定金额</label>
                    <div class="layui-input-block">
                        <input placeholder="以半角逗号分隔" value="{{$merchantChannel->amount}}" type="text" name="amount" lay-verify="required" autocomplete="off" class="layui-input layui-disabled">
                    </div>
                </div>
                <div class="layui-inline">

                </div>
            </div>




            <div class="layui-form-item">
                <label class="layui-form-label">状态</label>
                <div class="layui-input-block">
                    <select name="status"  lay-verify="required">
                        <option value="">请选择</option>
                        <option
                            @if($merchantChannel->status == 1)
                            selected="selected"
                            @endif
                            value="1" >开启</option>
                        <option
                            @if($merchantChannel->status == 0)
                            selected="selected"
                            @endif
                            value="0" >关闭</option>
                    </select>
                </div>

            </div>


            <div class="layui-form-item">
                <div class="layui-input-block">
                    <input type="hidden" name="_token" value="{{csrf_token()}}" >
                    <input type="hidden" name="_method" value="put" />
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
