<?php  namespace App\Upstream;
use App\Help\Sign;
use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Cszhifu {
    private $_mchId = '530';
    private $_key = 'E6445F504A1D47F8734930E0A1EE5159';
    private $_url = 'http://35.74.164.96:3456/';
    public function order($request) {
        $parameters = [
            'uid' => $this->_mchId,
            'outtradeno' => 'YZ'.$request['orderNo'],
            'fee' => $request['amount'],
            'channel' => $request['channelId'],
            'returnUrl' => config('app.url').'/api/pay/cszhifu/notify',



            'subject' => $request['subject'],


            'client_ip' => $request['clientIp'],
            'timestamp' => date('Y-m-d H:i:s'),

        ];
        if(!empty($request['redirectUrl'])) {
            $parameters['selectUrl'] = $request['redirectUrl'];
        } else {
            $parameters['selectUrl'] = config('app.url').'/api/pay/cszhifu/notify';
        }
        $signObj = new Sign();
        $sign = $signObj->zhongyou($parameters, '&keyvalue='.$this->_key);
        $parameters['sign'] = $sign;
        $response = Http::asForm()->post($this->_url.'v1/payment', $parameters);
        $response = $response->json();

        if($response['code'] == '0000') {
            return [
                'code' => 1,
                'data' => [
                    'qrImgUrl' => $response['payurl'],
                    'payOrderId' => 'YZ'.$request['orderNo'],
                    'qrUrl' => $response['payurl'],
                    'payUrl' => $response['payurl'],
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
        $sign = $signObj->zhongyou($req, '&keyvalue='.$this->_key);
        if($request->get('sign') != $sign) {
            return [
                'status' => '30003',
                'msg' => 'Signature error'
            ];
        }
        $mchOrderNo = substr($request->get('outtradeno'), 2);
        $orderModel = new Order();
        $order = $orderModel->where('OrderNo', $mchOrderNo)->first();
        if(empty($order)) {
            return false;
        }
        if($request->get('code') == '0000' && $order->status == 0) {
            if(empty($request->get('fee')) || $request->get('fee') == 0) {
                return false;
            }
            $orderModel->where('OrderNo', $mchOrderNo)->update([
                'status' => 1,
                'upOrderNo' => $request->get('serial'),
                'pay_amount' => $request->get('fee'),
                'pay_time' => time(),
'created' => time()
            ]);
        }
        return $orderModel->where('id', $order->id)->first();
    }
}
