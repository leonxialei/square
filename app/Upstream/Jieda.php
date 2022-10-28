<?php  namespace App\Upstream;
use App\Help\Sign;
use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Jieda {
    private $_mchId = 'facai001';
    private $_key = 'd9faf482e98cda8bca3cbfc0583ca7a4';
    private $_url = 'http://mb.hongchenpay.com/';
    public function order($request) {
        $parameters = [
            'merchantNum' => $this->_mchId,
            'orderNo' => 'YZ'.$request['orderNo'],
            'amount' => sprintf("%.2f", $request['amount']/100),
            'notifyUrl' => config('app.url').'/api/pay/jieda/notify',
            'payType' => '1002',


//            'pay_type' => $request['channelId'],

        ];

        if(!empty($request['redirectUrl'])) {
            $parameters['returnUrl'] = $request['redirectUrl'];
        } else {
            $parameters['returnUrl'] = config('app.url').'/api/pay/jieda/notify';
        }


        $sign = md5($this->_mchId.$parameters['orderNo'].$parameters['amount'].
            $parameters['notifyUrl'].$this->_key);

        $parameters['sign'] = $sign;
        $response = Http::asForm()->post($this->_url.'api/startOrder', $parameters);
        $response = $response->json();
        if($response['code'] == 200) {
            return [
                'code' => 1,
                'data' => [
                    'qrImgUrl' => $response['data']['payUrl'],
                    'payOrderId' => $response['data']['id'],
                    'qrUrl' => $response['data']['payUrl'],
                    'payUrl' => $response['data']['payUrl'],
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


        $sign = md5($request['state'].$request['merchantNum'].$request['orderNo'].
            $request['amount'].$this->_key);

        if($request->get('sign') != $sign) {
            return [
                'status' => '30003',
                'msg' => 'Signature error'
            ];
        }
        $mchOrderNo = substr($request->get('orderNo'), 2);
        $orderModel = new Order();
        $order = $orderModel->where('OrderNo', $mchOrderNo)->first();
        if(empty($order)) {
            return false;
        }
        if(strtoupper($request['state']) == 1 && $order->status == 0) {
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
