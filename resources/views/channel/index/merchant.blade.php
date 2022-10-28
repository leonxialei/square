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
            <div class="layui-form-item">
                <div class="layui">
                    <label class="layui-form-label">通道名称</label>
                    <div class="layui-input-block">
                        <input disabled="true" type="text" value="{{$channel->name}}" class="layui-input layui-disabled">
                    </div>
                </div>

            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">所属上家</label>
                <div class="layui-input-block">
                    <input disabled="true" type="text" value="{{$channel->upstream->name}}" class="layui-input layui-disabled">
                </div>

            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">平台通道码</label>
                <div class="layui-input-block">
                    <input disabled="true" type="number" value="{{$channel->code}}" class="layui-input layui-disabled">
                </div>

            </div>

            <div class="layui-form-item">
                <div class="layui">
                    <label class="layui-form-label">上游通道码</label>
                    <div class="layui-input-block">
                        <input disabled="true" type="text" value="{{$channel->upstream_code}}" class="layui-input layui-disabled">
                    </div>
                </div>

            </div>

            <div class="layui-form-item">
                <div class="layui">
                    <label class="layui-form-label">接入费率</label>
                    <div class="layui-input-block">
                        <input disabled="true" type="number" value="{{$channel->rate}}" class="layui-input layui-disabled">
                    </div>
                </div>

            </div>

        <div class="layui-form-item">
            <div class="layui">
                <label class="layui-form-label">批改费率</label>
                <div class="layui-input-inline">
                    <input type="number" value="{{$channel->rate+25}}" class="layui-input">
                </div>
                <div class="layui-input-inline">
                    <button type="button"  cid="{{$channel->id}}" class="layui-btn batch-rate-but layui-btn-sm">提交</button>
                </div>
            </div>

        </div>


            <div class="layui-form-item">
                <form class="layui-form" action="">
                    <table class="layui-table">
                    <colgroup>
                        <col>
                        <col>
                        <col>
                    </colgroup>
                    <thead>

                    <tr>
                        <th>开启状态</th>
                        <th>实际费率</th>
                        <th>商户名</th>
                        <th>商户编号</th>



                    </tr>
                    </thead>
                    <tbody>
                    @foreach($channel->merchantChannel as $merchantChannel)
                    <th>
                        <div class="">
                            <input type="checkbox" mcid="{{$merchantChannel->id}}"
                               @if($merchantChannel->status == 1)
                                   checked="checked"
                               @elseif($merchantChannel->status == 0)
                               @endif
                                   lay-filter="switchStatus" name="status" lay-text="开启|关闭" lay-skin="switch">
                        </div>

                    </th>
                    <th>
                        <div class="layui-input-inline">
                            <input type="number" name="rate" value="{{$merchantChannel->rate}}"  class="layui-input" />
                        </div>
                        <div class="layui-input-inline">
                            <button type="button"  mcid="{{$merchantChannel->id}}" class="layui-btn rate-but layui-btn-sm">提交</button>
                        </div>

                    </th>
                    <th>{{$merchantChannel->merchant->name}}</th>
                    <th>{{$merchantChannel->merchant->account}}</th>


                    </tbody>
                    @endforeach
                </table>
                </form>
            </div>
        </fieldset>




        </div>
    </div>
</div>
<script src="{{secure_url('lib/layui-v2.5.5/layui.js')}}" charset="utf-8"></script>
<script>
    layui.use(['form', 'table'], function () {
        var $ = layui.jquery,
            form = layui.form,
            table = layui.table,
            layuimini = layui.layuimini;


        form.on('switch(switchStatus)', function(data) {
            var status = this.checked;
            var mcid = $(this).attr('mcid');
            if(status) {
                status = 1;
            } else {
                status = 0;
            }
            $.ajax({
                type: "POST",
                url: "{{secure_url('api/merchant/channel/status')}}",
                async: false,
                data: {
                    'id' :mcid,
                    'type': status,
                    '_token': '{{csrf_token()}}'
                },
                success: function (data) {
                    if(data.result == false) {
                        layer.msg('非法操作！');
                    }
                }
            })
        });

        $('.rate-but').click(function(){
            var mcid = $(this).attr('mcid');
            var rate = $(this).parent().parent().find('input').val();
            if(rate == '') {
                rate = 0;
            }
            $.ajax({
                type: "POST",
                url: "{{secure_url('api/merchant/channel/rate')}}",
                async: false,
                data: {
                    'id' :mcid,
                    'rate': rate,
                    '_token': '{{csrf_token()}}'
                },
                success: function (data) {
                    if(data.result == true) {
                        layer.msg('更新成功！');
                    }
                }
            })
        });

        $('.batch-rate-but').click(function(){
            var cid = $(this).attr('cid');
            var rate = $(this).parent().parent().find('input').val();
            if(rate == '') {
                rate = 0;
            }

            $.ajax({
                type: "POST",
                url: "{{secure_url('api/merchant/channel/batch/rate')}}",
                async: false,
                data: {
                    'id' : cid,
                    'rate': rate,
                    '_token': '{{csrf_token()}}'
                },
                success: function (data) {
                    if(data.result == true) {
                        $("input[name=rate]").val(rate);
                        layer.msg('更新成功！');
                    }
                }
            })
        });

    });
</script>
<script>

</script>

</body>
</html>
