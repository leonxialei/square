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





        <form class="layui-form" method="post" action="{{secure_url('advance')}}">
            <div class="layui-form-item">
                <div class="layui">
                    <label class="layui-form-label">通道名称</label>
                    <div class="layui-input-block">
                        <input type="text" value="{{date('Y-m-d')}}" name="recharge_time" id="recharge_time" lay-verify="date" placeholder="yyyy-MM-dd" autocomplete="off" class="layui-input">
                    </div>
                </div>

            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">商户</label>
                <div class="layui-input-block">
                    <select name="merchant_id"  lay-verify="required">
                        <option value="">请选择</option>
                        @foreach($merchants as $merchant)
                            <option value="{{$merchant->id}}">{{$merchant->name}}</option>
                        @endforeach
                    </select>
                </div>

            </div>



            <div class="layui-form-item">
                <div class="layui">
                    <label class="layui-form-label">预付金额（分）</label>
                    <div class="layui-input-block">
                        <input type="number" name="amount" lay-verify="required|number" autocomplete="off" class="layui-input">
                    </div>
                </div>

            </div>


{{--            <div class="layui-form-item">--}}
{{--                <div class="layui">--}}
{{--                    <label class="layui-form-label">固定金额</label>--}}
{{--                    <div class="layui-input-block">--}}
{{--                        <input type="text" placeholder="单位分注意：请用半角逗号分割！！" name="amount" lay-verify="required" autocomplete="off" class="layui-input">--}}
{{--                    </div>--}}
{{--                </div>--}}

{{--            </div>--}}

            <div class="layui-form-item">
                <label class="layui-form-label">资金类型</label>
                <div class="layui-input-block">
                    <input type="radio" name="type" value="1" title="加" checked="">
                    <input type="radio" name="type" value="2" title="减">
                </div>

            </div>


            <div class="layui-form-item">
                <div class="layui-input-block">
                    <input type="hidden" name="_token" value="{{csrf_token()}}" >
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



        laydate.render({
            elem: '#recharge_time'
        });



    });
</script>

</body>
</html>
