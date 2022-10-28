<?php

namespace App\Http\Controllers\Api;

use App\Help\Sign;
use App\Http\Controllers\Controller;
use App\Models\AdvanceLog;
use App\Models\Merchant;
use App\Models\MerchantAdvance;
use App\Models\MerchantChannel;
use App\Models\Order;
use App\Models\OrderLog;
use App\Models\UpstreamChannel;
use App\Models\Upstream;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;
use App\Models\Count;
class GetPayController extends Controller
{
    public function createOrder(Request $request) {

        set_time_limit(120);

        $request = $request->all();
        if(empty($request['partnerid']) || empty($request['out_trade_no']) || empty($request['payType']) ||
            empty($request['amount']) || empty($request['notifyUrl']) || empty($request['returnUrl']) ||
            empty($request['version']) || empty($request['sign'] )) {
            return [
                'status' => '10001',
                'msg' => '参数错误'
            ];
        }
        $request['amount'] = $request['amount']*100;
        $request['clientIp'] = rand(1,254).'.'.rand(1,254).'.'.rand(1,254).'.'.rand(1,254);
        $request['mchId'] = $request['partnerid'];
        $request['mchOrderNo'] = $request['out_trade_no'];
        $request['channelId'] = $request['payType'];
        $request['subject'] = 'subject';
        $request['body'] = 'body';

        $orderModel = new Order();
        $order = $orderModel->where('mchOrderNo', $request['mchOrderNo'])
            ->where('customer_id', $request['mchId'])->first();
        if(!empty($order)) {
            return [
                'status' => '10007',
                'msg' => '订单已经提交'
            ];
        }

        $cliSign = $request['sign'];
        foreach($request as $key => $value) {
            if(empty($value)) {
                unset($request[$key]);
            }
        }
        unset($request['sign']);
        $merchantModel = new Merchant();
        $merchant = $merchantModel->where('account', $request['mchId'])->first();
        if(empty($merchant)) {
            return [
                'status' => '10002',
                'msg' => '没有这个商户'
            ];
        }
        $signObj = new Sign();
        $sign = $signObj->encode($request, $merchant->token);

        if($sign != $cliSign) {
            return [
                'status' => '10003',
                'msg' => '签名错误'
            ];
        }









        $upstreamChannelModel = new UpstreamChannel();
        $upstreamChannels = $upstreamChannelModel->where('code', $request['channelId'])
            ->where('status', 1)->get();
        if($upstreamChannels->isEmpty()) {
            return [
                'status' => '10004',
                'msg' => '通道关闭了'
            ];
        }


//        $request['mchId']


        $ids = [];
        foreach($upstreamChannels as $upstreamChannel) {
            $ids[] = $upstreamChannel->id;
        }








        $merchantChannelModel = new MerchantChannel();
        $merchantChannels = $merchantChannelModel
            ->select('id','weight')
            ->whereIn('channel_id', $ids)
            ->where('merchant_id', $merchant->id)
            ->where('status', 1)
            ->where('is_disabled', 0)
            ->orderBy('weight', 'DESC')->get();
        if($merchantChannels->isEmpty()) {
            return [
                'status' => '10004',
                'msg' => '通道关闭了'
            ];
        }
        $totalWeight = 0;

        foreach($merchantChannels as $mc) {
            $mc->start = $totalWeight;
            $totalWeight = $mc->weight + $totalWeight;
            $mc->end = $totalWeight;
        }
        $rant = rand(0, $totalWeight);
//        $cids = [];
//        foreach($merchantChannels as $mc) {
//            if($mc->start <= $rant && $mc->end >= $rant) {
//                $cids[] = $mc->toArray();
//            }
//        }
//        foreach ($merchantChannels as $mc) {
//            if($cids[0]['id'] != $mc->id) {
//                $cids[] = $mc->toArray();
//            }
//        }



        $cid = 0;
        $backendCid = 0;
        foreach($merchantChannels as $mc) {
            if($mc->start <= $rant && $mc->end >= $rant) {
                $cid = $mc->id;
            } else {
                $backendCid = $mc->id;
            }
        }





        $merchantChannel = $merchantChannelModel->where('id',$cid)
            ->where('status', 1)->first();
        if(empty($merchantChannel)) {
            return [
                'status' => '10008',
                'msg' => '没有这个通道'
            ];
        }
        if($merchantChannel->is_amount) {
            if(!$this->check_price($request, $merchantChannel)) {
                if(empty($backendCid)) {
                    return [
                        'status' => '10005',
                        'msg' => '金额超出范围'
                    ];
                } else {
                    $merchantChannel = $merchantChannelModel->where('id',$backendCid)
                        ->where('status', 1)->first();
                    if(empty($merchantChannel)) {
                        return [
                            'status' => '10008',
                            'msg' => '没有这个通道'
                        ];
                    }
                    if(!$this->check_price($request, $merchantChannel)) {
                        return [
                            'status' => '10005',
                            'msg' => '金额超出范围'
                        ];
                    }
                }
            }




        }

        $count = Count::getCount();
        $orderNo = rand(1, 99).date('dsi').$count. rand(10, 99);
        Count::plus();

        $modelName = 'App\Upstream'.'\\'.ucfirst($merchantChannel->channel->upstream->en_name);
        $model = new $modelName;
        $request['channelId'] = $merchantChannel->channel->upstream_code;
        $request['orderNo'] = $orderNo;
        $result = $model->order($request);
        if($result['code'] == 1) {
            $orderModel = new Order();

            $orderModel->OrderNo = $orderNo;
            $orderModel->mchOrderNo = $request['mchOrderNo'];
            $orderModel->upOrderNo = $result['data']['payOrderId'];
            $orderModel->merchant_channel_id = $merchantChannel->id;
            $orderModel->upstream_id = $merchantChannel->channel->upstream->id;
            $orderModel->notifyUrl = $request['notifyUrl'];
            if(!empty($request['redirectUrl'])) {
                $orderModel->upCallbackUrl = $request['redirectUrl'];
            }
            $orderModel->original_amount = $request['amount'];
            $orderModel->subject = $request['subject'];
            $orderModel->body = $request['body'];
//                $orderModel->amount = $request['amount'] - ($request['amount']/1000*$merchantChannel->rate);
            $log = [
                'xiajia_rate' => $merchantChannel->rate,
                'shangjia_rate' => $merchantChannel->channel->rate,
                'shangjia_id' =>$merchantChannel->id,
                'xiajia_id' => $merchantChannel->channel->id
            ];
            $orderModel->amount = $request['amount'] - ($request['amount'] * ($merchantChannel->channel->rate / 1000));
            $orderModel->merchant_amount = $request['amount'] - ($request['amount'] * ($merchantChannel->rate / 1000));
            $orderModel->customer_id = $request['mchId'];
            $orderModel->clientIp = $request['clientIp'];
            if(!empty($request['uid'])) {
                $orderModel->uid = $request['uid'];
            }
            if(!empty($request['bankCode'])) {
                $orderModel->bankCode = $request['bankCode'];
            }
            if(!empty($request['cardId'])) {
                $orderModel->cardId = $request['cardId'];
            }
            if(!empty($request['bankCardNo'])) {
                $orderModel->bankCardNo = $request['bankCardNo'];
            }
            if(!empty($request['phone'])) {
                $orderModel->phone = $request['phone'];
            }
            if(!empty($request['bankCardName'])) {
                $orderModel->bankCardName = $request['bankCardName'];
            }
            $orderModel->status = 0;
            $orderModel->created = time();
            $orderModel->pay_time = 0;
            $orderModel->save();
            if(!empty($result['data']['payUrl']) || $result['data']['qrImgUrl'] || !empty($result['data']['qrUrl'])) {
                return [
                    "num" => 'YZ'.$orderNo,
                    "amount" => $request->get('amount')/100,
                    "code" => 200,
                    "out_trade_no" => $request['out_trade_no'],
                    "url" => $result['data']['payUrl'],
                    "qrcode" => $result['data']['qrImgUrl'],
                ];
            } else {
                return $result['data']['body'];
            }

        } else if($result['code'] == 0) {
            $error_data = [
                'data' => $result['data'],

                'channel_id' => $merchantChannel->channel->id
            ];
            if(isset( $result['para'])) {
                $error_data['para'] = $result['para'];
            }
            $error_data = json_encode($error_data);
            Redis::rpush('error_log', $error_data);
            return [
                'status' => '10006',
                'msg' => '通道异常请检查'
            ];
        }


    }

    public function check_price($request, $merchantChannel) {
        $is_amount_mark = true;
        if(strpos($merchantChannel->amount,',') !== false) {
            $amountType = explode(',', $merchantChannel->amount);
            if(!in_array($request['amount'], $amountType)) {
                $is_amount_mark = false;
            }
        } elseif(strpos($merchantChannel->amount,'-') !== false) {
            $amountType = explode('-', $merchantChannel->amount);
            if($request['amount'] < $amountType[0] || $request['amount'] > $amountType[1]) {
                $is_amount_mark = false;
            }
        }
        return $is_amount_mark;
    }

    public function apprise($id) {
        $orderModel = new Order();
        $order = $orderModel->where('id', $id)->first();
        if($order->status == 2) {
            return 2;
        }
        if($order->status == 0) {
            return 3;
        }
        $status = $order->status;
        if($order->status == 1) {
            $status = 2;
        }
        $key = $order->customer->token;
        $p = [
            'payOrderId' => 'YZ'.$order->OrderNo,
            'amount' => $order->original_amount,
            'mchOrderNo' => $order->mchOrderNo,
            'status' => $status,
        ];
        if(!empty($order->upCallbackUrl)) {
            $p['redirectUrl'] = $order->upCallbackUrl;
        }
        $signObj = new Sign();
        $sign = $signObj->encode($p, $key);
        $p['sign'] = $sign;

        $response = Http::get($order->notifyUrl, $p);
        if($order->customer_id == '1100017') {
            error_log(print_r($response->body(),1),3,'guooyi,hh');
        }

        if(strtoupper($response->body()) == 'SUCCESS') {
            if($status == 2) {
                if(!empty(Order::merchantAmount($order->customer->account, date('Y-m-d'), date('Y-m-d')))){
                    $merchantAmount = Order::merchantAmount($order->customer->account, date('Y-m-d'), date('Y-m-d'))->merchant_amount;
                }else {
                    $merchantAmount = 0;
                }
                $balance = $order->customer->advance($order->customer->id, date('Y-m-d'), date('Y-m-d')) - $merchantAmount;
                $orderLogModel = new OrderLog();
                $orderLogModel->order_id = $order->id;
                $orderLogModel->merchant_id = $order->customer->id;
                $orderLogModel->attribute = 1;
                $orderLogModel->type = 2;
                $orderLogModel->amount = $order->merchant_amount;
                $orderLogModel->before_balance = $balance;
                $orderLogModel->balance = $balance - $order->merchant_amount;
                $orderLogModel->note = '订单扣除';
                $orderLogModel->created = time();
                $orderLogModel->save();
            }



            $orderModel->where('id', $id)->update([
                'status' => $status,
                'is_notify' => 1
            ]);
            return true;
        } else {
            return false;
        }

    }







}
