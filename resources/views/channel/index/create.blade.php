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





        <form class="layui-form" method="post" action="{{secure_url('channel')}}">
            <div class="layui-form-item">
                <div class="layui">
                    <label class="layui-form-label">通道名称</label>
                    <div class="layui-input-block">
                        <input type="text" name="name" lay-verify="required" autocomplete="off" class="layui-input">
                    </div>
                </div>

            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">所属上家</label>
                <div class="layui-input-block">
                    <select name="upstream"  lay-verify="required">
                        <option value="">请选择</option>
                        @foreach($upstreams as $upstream)
                        <option value="{{$upstream->id}}" >{{$upstream->name}}</option>
                        @endforeach
                    </select>
                </div>

            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">平台通道码</label>
                <div class="layui-input-block">
                    <select name="code"  lay-verify="required">
                        <option value="">请选择</option>
                        @foreach($codes as $code)
                        <option value="{{$code->code}}" >{{$code->name}}</option>
                        @endforeach
                    </select>
                </div>

            </div>

            <div class="layui-form-item">
                <div class="layui">
                    <label class="layui-form-label">上游通道码</label>
                    <div class="layui-input-block">
                        <input type="text" name="upstream_code" lay-verify="required" autocomplete="off" class="layui-input">
                    </div>
                </div>

            </div>

            <div class="layui-form-item">
                <div class="layui">
                    <label class="layui-form-label">接入费率</label>
                    <div class="layui-input-block">
                        <input type="number" placeholder="千分比" name="rate" lay-verify="required|number" autocomplete="off" class="layui-input">
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
                <label class="layui-form-label">是否固定金额</label>
                <div class="layui-input-block">
                    <select name="is_amount"  lay-verify="required">
                        <option value="">请选择</option>
                        <option value="1" >是</option>
                        <option value="0" >否</option>
                    </select>
                </div>

            </div>

            <div class="layui-form-item">
                <div class="layui">
                    <label class="layui-form-label">固定金额</label>
                    <div class="layui-input-block">
                        <input placeholder="以半角逗号分隔，或-号分隔" value="" type="text" name="amount" lay-verify="required" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-inline">

                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">通道状态</label>
                <div class="layui-input-block">
                    <select name="status"  lay-verify="required">
                        <option value="">请选择</option>
                        <option value="1" >开启</option>
                        <option value="0" >关闭</option>
                    </select>
                </div>

            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">开通商户</label>
                <div class="layui-input-block">
                    <input type="checkbox" lay-filter="merchant_check" lay-skin="primary" name="" value="" title="全选/全不选">
                    <br />
                    @foreach ($merchants as $merchant)
                    <input type="checkbox" lay-skin="primary" name="merchant[]" value="{{$merchant->id}}" title="{{$merchant->name}}">
                    @endforeach
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
            , laydate = layui.laydate
            , $ = layui.jquery;

        form.on('checkbox(merchant_check)', function(data){
            if(data.elem.checked == true) {
                $("input[name='merchant[]']").prop("checked", true);//true:选中 false:不选中
                layui.form.render();
            } else {
                $("input[name='merchant[]']").prop("checked", false);//true:选中 false:不选中
                layui.form.render();
            }
        });





    });
</script>

</body>
</html>
