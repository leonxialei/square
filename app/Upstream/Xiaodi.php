<?php  namespace App\Upstream;
use App\Help\Sign;
use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Xiaodi {
    private $_mchId = '441615215';
    private $_key = '3ED7907EAE0090E8A4FB7C5BB51C71FB';
    private $_url = 'http://api.gyqudong.cn/';
    public function order($request) {


        $parameters = [
            'merchantNo' => $this->_mchId,
            'businessType' => 'order',
            'timeStamp' => time(),
            'ipAddr' => rand(1,237).'.'.rand(1,237).'.'.rand(1,237).'.'.rand(1,237),
            'order_no' => 'YZ'.$request['orderNo'],
            'extend' => 'wu',
            'order_money' => $request['amount'],
            'channel' => $request['channelId'],
            'async_url' => config('app.url').'/api/pay/xiaodi/notify',


        ];
        $public =  [
            'merchantNo' => $this->_mchId,
            'businessType' => 'order',
            'timeStamp' => time(),
            'ipAddr' => rand(1,237).'.'.rand(1,237).'.'.rand(1,237).'.'.rand(1,237),
        ];
        if(!empty($request['redirectUrl'])) {
            $parameters['sync_url'] = $request['redirectUrl'];
        } else {
            $parameters['sync_url'] = config('app.url').'/api/pay/xiaodi/notify';
        }
        $business = [
            "order_no" => $parameters['order_no'],
            "async_url" => $parameters['async_url'],
            "extend" => $parameters['extend'],
            "sync_url" => $parameters['sync_url'],
            "channel" => $parameters['channel'],
            "order_money" => $parameters['order_money'],
        ];

        $public['data'] = base64_encode(json_encode($business));
        $signObj = new Sign();
        $sign = $signObj->encode($public, $this->_key);
        $public['sign'] = $sign;
        $response = Http::asForm()->post($this->_url.'api/v2/gateway', $public);
        $response = $response->json();

        if($response['code'] == '20000') {
            $data = json_decode(base64_decode($response['data']), true);
            return [
                'code' => 1,
                'data' => [
                    'qrImgUrl' => $data['payUrl'],
                    'payOrderId' => $data['orderNo'],
                    'qrUrl' => $data['payUrl'],
                    'payUrl' => $data['payUrl'],
                    'channelId' => $request['channelId'],
                ]
            ];
        } else {
            return [
                'code' => 0,
                'data' => json_encode($response),
                'para' => json_encode($parameters),
            ];
        }





    }

    public function notify($request) {
        $req = $request->all();
        unset($req['sign']);
        unset($req['extend']);
//        $business = [
//            "merchant_no" => $req['merchant_no'],
//            "order_no" => $req['order_no'],
//            "platform_order_no" => $req['platform_order_no'],
//            "order_money" => $req['order_money'],
//            "pay_time" => $req['pay_time'],
//            "pay_money" => $req['pay_money'],
//            "order_state" => $req['order_state'],
//            "extend" => $req['extend']
//        ];

//        $req['data'] = base64_encode(json_encode($business));



        $signObj = new sign();
        $sign = $signObj->encode($req, $this->_key);
        if($request->get('sign') != $sign) {
            return [
                'status' => '30003',
                'msg' => 'Signature error'
            ];
        }
        $mchOrderNo = substr($request->get('order_no'), 2);
        $orderModel = new Order();
        $order = $orderModel->where('OrderNo', $mchOrderNo)->first();
        if(empty($order)) {
            return false;
        }
        if(strtoupper($request['order_state']) == 82003 && $order->status == 0) {
            if(empty($request->get('pay_money')) || $request->get('pay_money') == 0) {
                return false;
            }
            $orderModel->where('OrderNo', $mchOrderNo)->update([
                'status' => 1,
                'pay_amount' => $request->get('pay_money'),
                'pay_time' => time(),
'created' => time()
            ]);
        }
        return $orderModel->where('id', $order->id)->first();
    }
}
