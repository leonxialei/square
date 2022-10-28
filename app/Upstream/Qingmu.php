<?php  namespace App\Upstream;
use App\Help\Sign;
use App\Models\Order;
use Illuminate\Support\Facades\Http;

class Qingmu{
    private $_mchId = '2200249';
    private $_key = 'E61E91372802BC45CF5FC78AF3E64413';
    private $_url = 'https://v2.qmapi.net/';
    public function order($request) {
        $parameters = [
            'app_id' => $this->_mchId,
            'out_trade_no' => 'YZ'.$request['orderNo'],
            'subject' => '随便写吧',
            'amount' => sprintf("%.2f", $request['amount']/100),
            'channel' => $request['channelId'],
            'client_ip' => $request['clientIp'],
            'notify_url' => config('app.url').'/api/pay/qingmu/notify',
            'return_url' => config('app.url').'/api/pay/qingmu/notify',



        ];

        $signObj = new Sign();
        $sign = $signObj->encode($parameters, $this->_key);
        $parameters['sign'] = $sign;
        $response = Http::asForm()->post($this->_url.'apply', $parameters);
        $response = $response->json();

        if($response['return_code'] == 'SUCCESS') {
            return [
                'code' => 1,
                'data' => [
                    'qrImgUrl' => $response['credential'],
                    'payOrderId' => $response['trade_no'],
                    'qrUrl' => $response['credential'],
                    'payUrl' => $response['credential'],
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
        $signObj = new sign();
        $sign = $signObj->encode($req, $this->_key);
        if($request->get('sign') != $sign) {
            return [
                'status' => '30003',
                'msg' => 'Signature error'
            ];
        }
        $mchOrderNo = substr($request->get('out_trade_no'), 2);
        $orderModel = new Order();
        $order = $orderModel->where('OrderNo', $mchOrderNo)->first();
        if(empty($order)) {
            return false;
        }
        if($request->get('return_code') == 'SUCCESS' && $order->status == 0) {
            if(empty($request->get('amount')) || $request->get('amount') == 0) {
                return false;
            }
            $orderModel->where('OrderNo', $mchOrderNo)->update([
                'status' => 1,
                'pay_amount' => $request->get('amount')*100,
                'pay_time' => time(),
'created' => time()
            ]);
        }
        return $orderModel->where('id', $order->id)->first();
    }
}
