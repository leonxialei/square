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





        <form class="layui-form" method="post" action="{{secure_url('merchant/cash')}}/{{$cash->id}}">
            <div class="layui-form-item">
                <div class="layui">
                    <label class="layui-form-label">商户名</label>
                    <div class="layui-input-block">
                        <input value="{{$cash->merchant->name}}" type="text" name="name" lay-verify="required" disabled="true" class="layui-input layui-disabled">
                    </div>
                </div>

            </div>
            <div class="layui-form-item">
                <div class="layui">
                    <label class="layui-form-label">姓名</label>
                    <div class="layui-input-block">
                        <input value="{{$cash->name}}" type="text" name="name" lay-verify="required" disabled="true" class="layui-input layui-disabled">
                    </div>
                </div>

            </div>

            <div class="layui-form-item">
                <div class="layui">
                    <label class="layui-form-label">银行账号</label>
                    <div class="layui-input-block">
                        <input value="{{$cash->bank_account}}" type="text" name="account" lay-verify="required"  disabled="true" class="layui-input layui-disabled">
                    </div>

                </div>

            </div>
            <div class="layui-form-item">
                <div class="layui">
                    <label class="layui-form-label">所属银行</label>
                    <div class="layui-input-block">
                        <input value="{{$cash->bank}}" type="text" name="bank"   disabled="true" class="layui-input layui-disabled">
                    </div>

                </div>

            </div>

            <div class="layui-form-item">
                <div class="layui">
                    <label class="layui-form-label">开户行</label>
                    <div class="layui-input-block">
                        <input value="{{$cash->bank_detail}}" type="text" name="bank_detail"  disabled="true" class="layui-input layui-disabled">
                    </div>

                </div>

            </div>

            <div class="layui-form-item">
                <div class="layui">
                    <label class="layui-form-label">提现金额</label>
                    <div class="layui-input-block">
                        <input value="{{sprintf("%.2f",$cash->amount/100)}}" type="number" name="amount" lay-verify="required|number"  disabled="true" class="layui-input layui-disabled">
                    </div>

                </div>

            </div>

            <div class="layui-form-item">
                <div class="layui">
                    <label class="layui-form-label">备注</label>
                    <div class="layui-input-block">
                        <input value="{{$cash->note}}" type="text" name="note" autocomplete="off"  disabled="true" class="layui-input layui-disabled">
                    </div>
                </div>

            </div>

            <div class="layui-form-item">
                <div class="layui">
                    <label class="layui-form-label">确认金额</label>
                    <div class="layui-input-block">
                        <input value="" type="number" name="take_amount" lay-verify="required|number" autocomplete="off" class="layui-input">
                    </div>

                    <label class="layui-form-label"></label>
                    <div class="layui-form-mid layui-word-aux">确认提现金额（必须与申请保持一致）</div>

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







    });
</script>

</body>
</html>
