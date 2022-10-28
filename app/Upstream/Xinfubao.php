<?php  namespace App\Upstream;
use App\Help\Sign;
use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Xinfubao {
    private $_mchId = 'ouran';
    private $_key = '93f010ed09ff4f3b89fa90dc59dd4b65';
    private $_url = 'http://wlj555.com/';
    public function order($request) {
        $parameters = [
            'clientOrderId' => 'YZ'.$request['orderNo'],
            'account' => $this->_mchId,
            'subject' => 'products',
            'money' => $request['amount'],
            'clientUserId' => 88,
            'clientUserIp' => $request['clientIp'],
            'callback' => config('app.url').'/api/pay/xinfubao/notify',
            'payType' => $request['channelId'],

        ];

        $signObj = new Sign();
        $sign = $signObj->brencode($parameters, '&publicKey='.$this->_key);
        $parameters['secretKey'] = $sign;
        $parameters['phoneType'] = 2;
        $response = Http::post($this->_url.'api/payDefray/send', $parameters);
        $response = $response->json();
        if($response['code'] == 0) {
            return [
                'code' => 1,
                'data' => [
                    'qrImgUrl' => $response['data']['payUrl'],
                    'payOrderId' => $response['data']['orderId'],
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
        unset($req['secretKey']);
        $signObj = new sign();
        $sign = $signObj->brencode($req, '&publicKey='.$this->_key);


        if($request->get('secretKey') != $sign) {
            return [
                'status' => '30003',
                'msg' => 'Signature error'
            ];
        }
        $mchOrderNo = substr($request->get('clientOrderId'), 2);
        $orderModel = new Order();
        $order = $orderModel->where('OrderNo', $mchOrderNo)->first();
        if(empty($order)) {
            return false;
        }
        if(strtoupper($request['payStatus']) == 1 && $order->status == 0) {
            if(empty($request->get('money')) || $request->get('money') == 0) {
                return false;
            }
            $orderModel->where('OrderNo', $mchOrderNo)->update([
                'status' => 1,
                'pay_amount' => $request->get('money'),
                'pay_time' => time(),
'created' => time()
            ]);
        }
        return $orderModel->where('id', $order->id)->first();
    }
}
