<?php

namespace App\Http\Controllers;

use App\Help\Sign;
use App\Http\Controllers\Controller;
use App\Models\ChannelCode;
use App\Models\Merchant;
use App\Models\MerchantChannel;
use App\Models\Order;
use App\Models\TelegramMerchant;
use App\Models\Upstream;
use App\Models\UpstreamChannel;
use Illuminate\Http\Request;
use App\Help\Methods;
use Illuminate\Support\Facades\Session;
use  Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;

class ExportController extends Controller
{
    public function order(Request $request) {
        if(empty($request->get('start_time'))) {
            $start_time = strtotime(date('Y-m-d').' 00:00:00');
        } else {
            $start_time = strtotime($request->get('start_time'));
        }
        if(empty($request->get('end_time'))) {
            $end_time = strtotime(date('Y-m-d').' 23:59:59');
        } else {
            $end_time = strtotime($request->get('end_time'));
        }

        $channelCodeModel = new ChannelCode();

        $channelModel = new UpstreamChannel();


        $orderModel = new Order();
        $orderModel = $orderModel->where('created', '>=', $start_time)
            ->where('created', '<=', $end_time);
        if(!empty($request->get('status'))) {
            if($request->get('status') == 4) {
                $orderModel = $orderModel->where('status', 0);
            } else {
                $orderModel = $orderModel->where('status', $request->get('status'));
            }
        }
        if(!empty($request->get('channel_code'))) {
            $channelCode = $channelCodeModel->where('code', $request->get('channel_code'))->first();
            $merchantChannelModel = new MerchantChannel();

            $channelId = [];
            foreach($channelCode->upstreamChannel as $val) {
                $channelId[] = $val->id;
            }
            $merchantChannels = $merchantChannelModel->whereIn('channel_id', $channelId)->get();

            $merchantChannelIds = [];
            foreach($merchantChannels as $merchantChannel) {
                $merchantChannelIds[] = $merchantChannel->id;
            }
            $orderModel = $orderModel->whereIn('merchant_channel_id', $merchantChannelIds);
        }

        if(!empty($request->get('channel_id'))) {
            $upstreamChannel = $channelModel->where('id', $request->get('channel_id'))->first();
            $merchantChannels = $upstreamChannel->merchantChannel;
            $merchantChannelIds = [];
            foreach($merchantChannels as $merchantChannel) {
                $merchantChannelIds[] = $merchantChannel->id;
            }
            $orderModel = $orderModel->whereIn('merchant_channel_id', $merchantChannelIds);
        }

        if(!empty($request->get('upstream_id'))) {
            $orderModel = $orderModel->where('upstream_id', $request->get('upstream_id'));
        }
        if(!empty($request->get('merchant_id'))) {
            $orderModel = $orderModel->where('customer_id', $request->get('merchant_id'));
        }
        if(!empty($request->get('OrderNo'))) {
            $orderModel = $orderModel->where('OrderNo',  ltrim(trim($request->get('OrderNo')), 'YZ'));
        }
        if(!empty($request->get('mchOrderNo'))) {
            $orderModel = $orderModel->where('mchOrderNo', trim($request->get('mchOrderNo')));
        }

        $orders = $orderModel->orderBy('id', 'DESC')->get();
        $subject = [
            '平台单号',
            '商户',
            '商户订单号',
            '订单金额',
            '实际支付',
            '状态',
            '订单创建时间',
            '订单支付时间'
        ];
        $data = [];
        foreach($orders as $key => $order) {
            $data[$key] = [
                'YZ'.$order->OrderNo,
                $order->customer->name,
                $order->mchOrderNo,
                sprintf("%.2f",$order->original_amount/100),
                sprintf("%.2f",$order->pay_amount/100),

            ];
            if($order->status == 1) {
                array_push($data[$key], '支付成功');
            } elseif ($order->status == 0) {
                array_push($data[$key], '订单生成');
            } elseif ($order->status == 2) {
                array_push($data[$key], '处理完成');
            } elseif ($order->status == 3) {
                array_push($data[$key], '创建失败');
            }
            array_push($data[$key], date('Y-m-d H:i:s', $order->created));
            if($order->pay_time != 0) {
                array_push($data[$key], date('Y-m-d H:i:s', $order->pay_time));
            } else {
                array_push($data[$key], '0');
            }

        }
        $name = date('Y-m-d', $start_time).'data_export';
        Methods::csv($name, $subject, $data);
        $js = <<<JS
            <script>
                window.open("about:blank","_self").close();
            </script>
            JS;
        return $js;
        die;
    }

    public function today() {
        $subject = [
            '名称',
            '类型',
            '剩余预付'
        ];
        $data = [];

        $tMerchantModel = new TelegramMerchant();
        $tMerchants = $tMerchantModel->orderBy('type')->get();
        foreach ($tMerchants as $key=>$tMerchant) {

            $name = '---';
            $type = '';
            if($tMerchant->type == 1) {
                $upModel = new Upstream();
                $upstream = $upModel->where('id', $tMerchant->customer_id)
                    ->first();

                if(empty($upstream)) {
                    continue;
                }
                $name = $upstream->name;
                $type = '上游';
            } else if($tMerchant->type == 2) {
                $meModel = new Merchant();
                $merchant = $meModel->where('id', $tMerchant->customer_id)
                    ->first();
                if(empty($merchant)) {
                    continue;
                }
                $name = $merchant->name;
                $type = '下游';

            }
            $data[$key] = [
                $name, $type, sprintf("%.2f", $tMerchant->remaining/100)
            ];
        }


        $name = date('Y-m-d H:i:s').'daily_report';
        Methods::csv($name, $subject, $data);
        $js = <<<JS
            <script>
                window.open("about:blank","_self").close();
            </script>
            JS;
        return $js;
        die;
    }





}
