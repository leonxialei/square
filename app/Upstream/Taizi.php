<?php  namespace App\Upstream;
use App\Help\Sign;
use App\Models\Order;
use Illuminate\Support\Facades\Http;

class Taizi {
    private $_mchId = '20000077';
    private $_key = 'TOVOULDFAZ1WE3ZNX9VOHENYLFA4E5F7A65VE93PDOI6FJUIYYYW7X50F3TNFKPDSHPNNL0JJ1681KGA4KXNCC8IQV2RXPLMJDOWN9DXEQWCFFX14BPYIA6S10VIORHR';
    private $_url = 'http://api.qy001.cc/';
    public function order($request) {
        $parameters = [
            'mchId' => $this->_mchId,
            'appId' => '54425836c9c842f3a0c2c9775d9868a9',
            'productId' => $request['channelId'],
            'mchOrderNo' => 'YZ' . $request['orderNo'],
            'amount' => $request['amount'],
            'currency' => 'cny',
            'notifyUrl' => config('app.url').'/api/pay/taizi/notify',
//            'redirectUrl' => $request['redirectUrl'],
            'subject' => 'subject',
            'body' => 'bodybody',
            'extra' => '{"openId":"54425836c9c842f3a0c2c9775d9868a9"}'

        ];
        if(!empty($request['redirectUrl'])) {
            $parameters['returnUrl'] = $request['redirectUrl'];
        } else {
            $parameters['returnUrl'] = config('app.url').'/api/pay/taizi/notify';
        }

        $signObj = new Sign();
        $sign = $signObj->encode($parameters, $this->_key);
        $parameters['sign'] = $sign;
        $response = Http::asForm()->post($this->_url.'api/pay/create_order', $parameters);
        $response = $response->json();
        if($response['retCode'] == 'SUCCESS') {
            return [
                'code' => 1,
                'data' => [
                    'qrImgUrl' => $response['payParams']['payUrl'],
                    'payOrderId' => $response['payOrderId'],
                    'qrUrl' => $response['payParams']['payUrl'],
                    'payUrl' => $response['payParams']['payUrl'],
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
        $mchOrderNo = substr($request->get('mchOrderNo'), 2);
        $orderModel = new Order();
        $order = $orderModel->where('OrderNo', $mchOrderNo)->first();
        if(empty($order)) {
            return false;
        }
        if($request->get('status') == 2 && $order->status == 0) {
            if(empty($request->get('amount')) || $request->get('amount') == 0
                || $request->get('amount') < $order->original_amount) {
                return false;
            }
            $orderModel->where('OrderNo', $mchOrderNo)->update([
                'status' => 1,
                'pay_amount' => $request->get('amount'),
                'pay_time' => time(),
'created' => time()
            ]);
        }
        return $orderModel->where('id', $order->id)->first();
    }
}
