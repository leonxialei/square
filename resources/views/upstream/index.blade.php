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
        <div style="margin: 10px 10px 10px 10px" class="layui-card-header"><i class="fa fa-warning icon"></i>分配通道</div>
        <fieldset class="table-search-fieldset">

            <div style="margin: 10px 10px 10px 10px">

                    <div class="layui-inline" style="margin-bottom: 10px">
                        <button onclick="set_status(1)" class="layui-btn layui-btn-normal"> 批量开启 </button>
                    </div>
                    <div class="layui-inline" style="margin-bottom: 10px">
                        <button onclick="set_status(2)" class="layui-btn layui-btn-warm"> 批量关闭 </button>
                    </div>
                    <div class="layui-inline" style="margin-bottom: 10px">
                        <button onclick="set_status(3)" class="layui-btn layui-btn-danger"> 关闭所有通道 </button>
                    </div>
                <form class="layui-form layui-form-pane" action="">
                    <div class="layui-inline">
                        <label class="layui-form-label">商户</label>
                        <div class="layui-input-inline">
                            <select type="text" name="merchant_id" class="layui-input">
                                <option value="">请选择</option>
                                @foreach($merchants as $merchant)
                                <option
                                    @if($merchant->id == $request->get('merchant_id'))
                                    selected="selected"
                                    @endif
                                    value="{{$merchant->id}}" >{{$merchant->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="layui-inline">
                        <label class="layui-form-label">通道</label>
                        <div class="layui-input-inline">
                            <select type="text" name="channel_id" class="layui-input">
                                <option value="">请选择</option>
                                @foreach($upstreams as $upstream)
                                    <option
                                        value="" >===={{$upstream->name}}====</option>
                                    <?php
                                        $upChannelModel = new \App\Models\UpstreamChannel();
                                        $upChannels = $upChannelModel->where('upstream_id', $upstream->id)
                                            ->where('status', 1)->get();
                                    ?>
                                    @foreach($upChannels as $channel)
                                    <option
                                        @if($channel->id == $request->get('channel_id'))
                                        selected="selected"
                                        @endif
                                        value="{{$channel->id}}" >{{$channel->name}}</option>
                                    @endforeach
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="layui-inline">
                            <label class="layui-form-label">通道类型</label>
                            <div class="layui-input-inline">
                                <select type="text" name="code" class="layui-input">
                                    <option value="">请选择</option>
                                    @foreach($codes as $code)
                                        <option
                                            @if($code->code == $request->get('code'))
                                            selected="selected"
                                            @endif
                                            value="{{$code->code}}" >{{$code->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    <div class="layui-inline">
                        <label class="layui-form-label">上游</label>
                        <div class="layui-input-inline">
                            <select type="text" name="upstream" class="layui-input">
                                <option value="">请选择</option>
                                @foreach($upstreams as $upstream)
                                    @if($request->get('upstream') == $upstream->id)
                                        <option
                                            selected="selected"
                                            value="{{$upstream->id}}" >{{$upstream->name}}</option>
                                    @else
                                        <option
                                            value="{{$upstream->id}}" >{{$upstream->name}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="layui-inline">
                                <label class="layui-form-label">状态</label>
                                <div class="layui-input-inline">
                                    <select type="text" name="status" class="layui-input">
                                        <option value="">请选择</option>
                                        <option
                                            <?php if($request->get('status') == 1) {?>
                                            selected="selected"
                                            <?php } ?>
                                            value="1" >开启</option>
                                        <option
                                            <?php if($request->get('status') != ''  && $request->get('status') == 0) {?>
                                            selected="selected"
                                            <?php } ?>
                                            value="0" >关闭</option>
                                    </select>
                                </div>
                            </div>
                    <div class="layui-inline">
                        <button type="submit" class="layui-btn layui-btn-primary" lay-submit ><i class="layui-icon"></i> 搜 索</button>
                    </div>
                    <div class="layui-inline">
                        <button class="layui-btn data-add-btn"> 分配新通道 </button>
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
                        <th>商户</th>
                        <th>通道</th>
                        <th>通道类型</th>
                        <th>权重</th>
                        <th>编码</th>
                        <th>代理费率</th>
                        <th>支付链接</th>
{{--                        <th>固定金额</th>--}}
                        <th>状态</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($merchantChannels as $merchantChannel)
                    <tr>
                        <th>{{$merchantChannel->merchant->name}}</th>
                        <th>{{empty($merchantChannel->channel)?'':$merchantChannel->channel->name}}</th>
                        <th>{{empty($merchantChannel->channel->channelCode)?'':$merchantChannel->channel->channelCode->name}}</th>
                        <th ondblclick="change_weight({{$merchantChannel->id}}, this)">{{$merchantChannel->weight}}</th>
                        <th>{{empty($merchantChannel->channel->channelCode)?'':$merchantChannel->channel->code}}</th>
                        <th>{{$merchantChannel->rate}}</th>
                        <th>


                            <button
                                data-clipboard-action="copy"
                                data-clipboard-text="{{secure_url('pay/testcreate/')}}/{{Illuminate\Support\Facades\Crypt::encryptString($merchantChannel->channel->id)}}"
                                type="button" class="layui-btn layui-btn-warm layui-btn-xs info_copy">复制测试链接</button>





                        </th>
{{--                        <th>{{$merchantChannel->amount}}</th>--}}
                        <th>
                            @if($merchantChannel->status == 1)
                            <button type="button" onclick="quick_change({{$merchantChannel->id}}, 0, this)" class="layui-btn layui-btn-xs">开启</button>
                            @else
                            <button type="button" onclick="quick_change({{$merchantChannel->id}}, 1, this)" class="layui-btn layui-btn-danger layui-btn-xs">关闭</button>
                            @endif

                        </th>
                        <th>
                            <button type="button" onclick="edit_code({{$merchantChannel->id}})" class="layui-btn layui-btn-warm layui-btn-xs">修改</button>
                            <button type="button" onclick="del_code({{$merchantChannel->id}})" class="layui-btn layui-btn-danger layui-btn-xs">删除</button>
                        </th>
                    </tr>
                    @endforeach
                    </tbody>
                </table>

                {{$merchantChannels->appends([
                    'merchant_id' => $request->get('merchant_id'),
                    'channel_id' => $request->get('channel_id'),
                    'code' => $request->get('code'),
                    'status' => $request->get('status'),
                    'upstream' => $request->get('upstream')
                ])->links()}}
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
        $(".data-add-btn").on("click", function () {
            var index = layer.open({
                title: '分配新通道',
                type: 2,
                shade: 0.2,
                maxmin:true,
                shadeClose: true,
                area: ['100%', '100%'],
                content: '{{secure_url('upstream/create')}}',
            });
            $(window).on("resize", function () {
                layer.full(index);
            });

            return false;
        });

        edit_code = function (id) {
            var index = layer.open({
                title: '修改已分配通道',
                type: 2,
                shade: 0.2,
                maxmin:true,
                shadeClose: true,
                area: ['100%', '100%'],
                content: '{{secure_url('upstream')}}/'+id+'/edit',

            });
            $(window).on("resize", function () {
                layer.full(index);
            });

            return false;
        };


        del_code = function (id) {
            if(confirm('您确定要删除此通道吗？')) {
                $.ajax({
                    type: "DELETE",
                    url: "{{secure_url('api/upstream/channel')}}/"+id,
                    async: false,
                    data: {
                        '_token': '{{csrf_token()}}'
                    },
                    success: function (data) {
                        if(data == false) {
                            layer.msg('非法操作！');
                        }else if(data == true) {
                            window.location.reload();
                        }
                    }
                })
            }
        };
        // 监听删除操作
        // $(".data-delete-btn").on("click", function () {
        //     var checkStatus = table.checkStatus('currentTableId')
        //         , data = checkStatus.data;
        //     layer.alert(JSON.stringify(data));
        // });

        quick_change = function (id, type, _this) {
            $.ajax({
                type: "POST",
                url: "{{secure_url('api/merchant/channel/status')}}",
                data: {
                    id: id,
                    type: type,
                    _token:'{{csrf_token()}}'
                },
                dataType:'json',
                async:false,
                success: function(msg){
                   if(msg.result == true) {
                       // $(_this).hide();
                       if(type == 1) {
                           var html = '<button type="button" onclick="quick_change({{!empty($merchantChannel)?$merchantChannel->id:''}}, 0, this)" class="layui-btn layui-btn-xs">开启</button>'
                       } else if(type == 0){
                           var html = '<button type="button" onclick="quick_change({{!empty($merchantChannel)?$merchantChannel->id:''}}, 1, this)" class="layui-btn layui-btn-danger layui-btn-xs">关闭</button>';
                       }
                       $(_this).parent().append(html);
                       $(_this).remove();
                   }
                }
            });
        };

        change_weight = function (id, _this) {
            var weight = $(_this).html();
            $(_this).empty();
            var html = '<input onblur="set_weight('+id+', this)" type="number" value="'+weight+'" />';
            $(_this).append(html);
            $(_this).find('input').focus();
        };
        set_weight = function (id, _this) {
            var weight = $(_this).val();
            var tdbox = $(_this).parent();
            $.ajax({
                type: "POST",
                url: "{{secure_url('api/merchant/channel/weight')}}",
                data: {
                    id: id,
                    weight: weight,
                    _token:'{{csrf_token()}}'
                },
                dataType:'json',
                async:false,
                success: function(msg){
                    if(msg.result == true) {
                        tdbox.empty();
                        tdbox.html(weight);
                    }
                }
            });
        };

        set_status = function(type) {
            var params = window.location.search.substring(1);
            $.ajax({
                type: "POST",
                url: "{{secure_url('api/merchant/channel/set/status')}}",
                data: {
                    params: params,
                    type: type,
                    _token:'{{csrf_token()}}'
                },
                dataType:'json',
                async:false,
                success: function(msg){
                    if(msg.result == true) {
                        window.location.href='{{secure_url('upstream')}}?'+params;
                    }
                }
            });
        };


        var clipboard = new ClipboardJS('.info_copy');

        clipboard.on('success', function (e) {
            layer.msg('复制成功！');
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
