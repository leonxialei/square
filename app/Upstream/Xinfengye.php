<?php  namespace App\Upstream;
use App\Help\Sign;
use App\Models\Order;
use Illuminate\Support\Facades\Http;

class Xinfengye {
    private $_mchId = '300117';
    private $_key = '9D2F58E11210A5342F54D38514778359';
    private $_url = 'http://api.999zf.link/';
    public function order($request) {
        $parameters = [
            'notify_url' => config('app.url').'/api/pay/xinfengye/notify',
            'order_id' => 'YZ'.$request['orderNo'],
            'order_amount' => sprintf("%.2f", $request['amount']/100),
            'pay_type' => $request['channelId'],


        ];

        if(!empty($request['redirectUrl'])) {
            $parameters['return_url'] = $request['redirectUrl'];
        } else {
            $parameters['return_url'] = config('app.url').'/api/pay/xinfengye/notify';
        }
        $signObj = new Sign();
        $sign = $signObj->encode($parameters, $this->_key);
        $parameters['sign'] = $sign;
        $parameters['sign_type'] = 'MD5';
        $parameters['method'] = 'topay';
        $parameters['user_id'] = $this->_mchId;
        $parameters['client_ip'] = $request['clientIp'];
        $parameters['client_system'] = 'ios';

        $response = Http::asForm()->post($this->_url.'gateway', $parameters);
        $response = $response->json();
        if($response['code'] == '10000') {
            return [
                'code' => 1,
                'data' => [
                    'qrImgUrl' => $response['url'],
                    'payOrderId' => $response['trade_no'],
                    'qrUrl' => $response['url'],
                    'payUrl' => $response['url'],
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
        unset($req['user_id']);
        unset($req['trade_no']);
        unset($req['buyer_pay_amount']);
        unset($req['success_time']);
        unset($req['account_id']);
        unset($req['trade_status']);
        unset($req['sign']);
        $mchOrderNo = substr($request->get('order_id'), 2);
        $orderModel = new Order();
        $order = $orderModel->where('OrderNo', $mchOrderNo)->first();
        if(empty($order)) {
            return false;
        }
//        $req['return_url'] = config('app.url').'/api/pay/xinfengye/notify';
        $req['notify_url'] = config('app.url').'/api/pay/xinfengye/notify';
        $signObj = new sign();
        $sign = $signObj->encode($req, $this->_key);
        if($request->get('sign') != $sign) {
            return [
                'status' => '30003',
                'msg' => 'Signature error'
            ];
        }





        if($request->get('trade_status') == 'SUCCESS' && $order->status == 0) {
            if(empty($request->get('buyer_pay_amount')) || $request->get('buyer_pay_amount') == 0) {
                return false;
            }
            $orderModel->where('OrderNo', $mchOrderNo)->update([
                'status' => 1,
                'pay_amount' => $request->get('buyer_pay_amount')*100,
                'pay_time' => time(),
'created' => time()
            ]);
        }
        return $orderModel->where('id', $order->id)->first();
    }
}
