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





        <form class="layui-form" method="post" action="{{secure_url('merchant')}}/{{$merchant->id}}">
            <div class="layui-form-item">
                <div class="layui">
                    <label class="layui-form-label">登录账号</label>
                    <div class="layui-input-block">
                        <input value="{{$merchant->account}}" type="number" name="account" lay-verify="required|number" autocomplete="off" class="layui-input layui-disabled">
                    </div>
                </div>

            </div>
            <div class="layui-form-item">
                <div class="layui">
                    <label class="layui-form-label">名称</label>
                    <div class="layui-input-block">
                        <input value="{{$merchant->name}}" type="text" name="name" lay-verify="required" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-inline">

                </div>
            </div>

            <div class="layui-form-item">
                <div class="layui">
                    <label class="layui-form-label">登录密码</label>
                    <div class="layui-input-block">
                        <input type="text" value="{{$merchant->password}}" name="password" lay-verify="required" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-inline">

                </div>
            </div>

            <div class="layui-form-item">
                <div class="layui">
                    <label class="layui-form-label">接入密钥</label>
                    <div class="layui-input-block">
                        <input type="text" value="{{$merchant->token}}" name="token" lay-verify="required" autocomplete="off" class="layui-input layui-disabled">
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
                            @if($merchant->status == 1)
                            selected="selected"
                        @endif
                        value="1" >开启</option>
                        <option
                            @if($merchant->status == 0)
                            selected="selected"
                            @endif
                            value="0" >关闭</option>
                    </select>
                </div>

            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">上级代理</label>
                <div class="layui-input-block">
                    <select name="agency_id"  lay-verify="required">
                        <option value="0">没有上级</option>
                        @foreach($merchants as $val)
                            <option
                                @if($merchant->agency_id == $val->id)
                                selected="selected"
                                @endif
                                value="{{$val->id}}" >{{$val->name}}</option>
                        @endforeach
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


    <div class="layui-col-md12">
        <span style="margin: 20px; color: #333;">代理商户</span>
        <table class="layui-table">
            <colgroup>
                <col>
                <col>
                <col>
            </colgroup>
            <thead>
            <tr>
                <th>商户账号</th>
                <th>商户名</th>
                <th>状态</th>
                <th>创建日期</th>
            </tr>
            </thead>
            <tbody>
            @foreach($agencys as $agency)
            <tr>
                <td>{{$agency->account}}</td>
                <td>{{$agency->name}}</td>
                <td>
                    @if($agency->status == 1)
                        <button type="button" class="layui-btn layui-btn-xs">开启</button>
                    @elseif($agency->status == 0)
                        <button type="button" class="layui-btn layui-btn-danger layui-btn-xs">关闭</button>
                    @endif

                </td>
                <td>{{date('Y-m-d H:i:s',$agency->created)}}</td>
            </tr>
            @endforeach
            </tbody>
        </table>
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
