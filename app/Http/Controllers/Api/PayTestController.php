<?php

namespace App\Http\Controllers\Api;

use App\Help\Broadcast;
use App\Help\Sign;
use App\Http\Controllers\Controller;
use App\Models\AdvanceLog;
use App\Models\Count;
use App\Models\Merchant;
use App\Models\MerchantChannel;
use App\Models\Order;
use App\Models\Upstream;
use App\Models\UpstreamChannel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use  Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;
use NotificationChannels\Telegram\TelegramUpdates;
use NotificationChannels\Telegram\TelegramMessage;
use NotificationChannels\Telegram\TelegramPoll;
use Illuminate\Support\Str;
use function Symfony\Component\String\s;

class PayTestController extends Controller
{
    public function createOrder(Request $request) {



        $request = $request->all();
        if(empty($request['mchId']) || empty($request['mchOrderNo']) || empty($request['channelId']) ||
            empty($request['amount'])) {
            return [
                'status' => '30001',
                'msg' => '参数错误'
            ];
        }
        $orderModel = new Order();
        $order = $orderModel->where('mchOrderNo', $request['mchOrderNo'])
            ->where('customer_id', $request['mchId'])->first();


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
                'status' => '30002',
                'msg' => '无此商户'
            ];
        }








        $upstreamChannelModel = new UpstreamChannel();
        $upstreamChannels = $upstreamChannelModel->where('code', $request['channelId'])
            ->where('status', 1)
            ->where('is_disabled', 0)
            ->get();
        if($upstreamChannels->isEmpty()) {
            return [
                'status' => '30004',
                'msg' => '通道关闭'
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
                'status' => '30004',
                'msg' => '通道关闭'
            ];
        }
        $totalWeight = 0;

        foreach($merchantChannels as $mc) {

            $mc->start = $totalWeight;
            $totalWeight = $mc->weight + $totalWeight;
            $mc->end = $totalWeight-1;
        }

        $rant = rand(0, $totalWeight-1);
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
            ->first();
        if(empty($merchantChannel)) {
            return [
                'status' => '30008',
                'msg' => '没有此通道'.$cid
            ];
        }
        if($merchantChannel->is_amount) {
            if(!$this->check_price($request, $merchantChannel)) {
                if(empty($backendCid)) {
                    return [
                        'status' => '30005',
                        'msg' => '充值金额不正确'
                    ];
                } else {
                    $merchantChannel = $merchantChannelModel->where('id',$backendCid)
                        ->first();
                    if(empty($merchantChannel)) {
                        return [
                            'status' => '30008',
                            'msg' => '没有此通道'
                        ];
                    }
                    if($merchantChannel->is_amount) {
                        if (!$this->check_price($request, $merchantChannel)) {
                            return [
                                'status' => '30005',
                                'msg' => '充值金额不正确'
                            ];
                        }
                    }
                }
            }

        }
        $count = Count::getCount();
        $orderNo = rand(1, 99).date('dsi').$count. rand(10, 99);
        Count::plus();
        dd($merchantChannel->channel->upstream->name);
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
            $orderModel->code = $merchantChannel->channel->code;
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
                    'status' => '200',
                    'msg' => 'SUCCESS',
                    "payOrderId" => 'YZ'.$orderNo,
                    "out_trade_no" => $request['mchOrderNo'],
                    "channelId" => $result['data']['channelId'],
                    "payUrl" => $result['data']['payUrl'],
                    "qrUrl" => $result['data']['qrUrl'],
                    "qrImgUrl" => $result['data']['qrImgUrl'],
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
                'status' => '30006',
                'msg' => '通道异常'
            ];
        }


    }

    public function test() {
        $response = Http::get('http://103.13.229.1:8081/api/pay/notify', [
            'aa' => 1232,
            'bb' => 123555,
        ])->body();
        echo $response;
        die;
        $response = Http::post('http://103.13.229.1:8081/api/pay/create_order', [
            'mchId' => 1100030,
            'mchOrderNo' => 202137424342,
            'amount' => 3000,
            'channelId' => 1218,
            'notifyUrl' => 'http://square.test/api/pay/notify',
            'subject' => 'weixinios',
            'body' => 'weixinios',
            'clientIp' => '223.204.246.162',
            'sign' => 'A10DC901C98F9C6CE352C5528166B958',
        ]);
        $aa = $response->json();
        dd($aa);
    }


    public function redis() {
        return 'SUCCESS';
//        $a = Redis::rpush('feedback_pool',json_encode([
//            'bb' => 1234
//        ]));
//        $a = Redis::blpop('feedback_pool', 1);
//        dd(json_decode($a[1])->bb);
        dd(Redis::llen('feedback_pool'));
    }

    public function notify(Request $request) {
        $beginToday=mktime(0,0,0,date('m'),date('d'),date('Y'));
        $endToday=mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;
        echo $beginToday.'@'.$endToday;
        die;
        $mchOrderNo = $request->get('mchOrderNo');
        $orderModel = new Order();
        $order = $orderModel->where('mchOrderNo', $mchOrderNo)->first();
        if(empty($order)) {
            return false;
        }
        if($request->get('status') == 2 && $order->status == 0) {
            $orderModel->where('mchOrderNo', $mchOrderNo)->update([
                'status' => 1,
                'pay_amount' => $request->get('total_fee'),
                'pay_time' => time()
            ]);
        }
        return 'SUCCESS';
    }

    public function bot1()
    {
        $data = Redis::blpop('feedback_pool111', 1);
        $data = json_decode($data[1]);
        dd(isset($data->time));
    }
    public function bot()
    {
        $keys = Redis::keys('query*');
        foreach ($keys as $key) {
            $k = explode('laravel_database_', $key);
            echo $k[1]."\n";
            Redis::del($k[1]);
        }



    }

}
