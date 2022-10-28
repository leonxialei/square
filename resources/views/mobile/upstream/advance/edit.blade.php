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





        <form class="layui-form" method="post" action="{{secure_url('upstream/advance')}}/{{$upstream->id}}">
            <div class="layui-form-item">
                <div class="layui">
                    <label class="layui-form-label">日期</label>
                    <div class="layui-input-block">
                        <input value="{{date('Y-m-d')}}" type="text" name="weight"  autocomplete="off" class="layui-input layui-disabled">
                    </div>
                </div>

            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">上游</label>
                <div class="layui-input-block">
                    <input value="{{$upstream->name}}" type="text" name="weight"  autocomplete="off" class="layui-input layui-disabled" />

                </div>

            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">预付金额</label>
                <div class="layui-input-block">
                    <input value="{{empty($amount)?'0.00':$amount}}" type="text"  autocomplete="off" class="layui-input layui-disabled" />

                </div>

            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">资金类型</label>
                <div class="layui-input-block">
                    <input type="radio" name="type" value="1" title="加" checked="">
                    <input type="radio" name="type" value="2" title="减">
                </div>

            </div>



            <div class="layui-form-item">
                <div class="layui">
                    <label class="layui-form-label">变动金额</label>
                    <div class="layui-input-block">
                        <input  value="" type="number" name="amount" lay-verify="required|number" autocomplete="off" class="layui-input">
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
