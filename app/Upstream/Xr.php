<?php  namespace App\Upstream;
use App\Help\Sign;
use App\Models\Order;
use Illuminate\Support\Facades\Http;

class Xr {
    private $_mchId = '20000037';
    private $_key = 'TQKPJMK7ZHWLNQ2KNFTNBAUXZBXZDVTUSUCCLJKTWUTAA8RF6NBPIZ0KFAHW3ZEW8RPNOR3VGPV5GJXFUM99NPTFIA4ELNZK8AFLRT69P3VHBPAZ1CHTS7ENEWUYBSGS';
    private $_url = 'http://mkn2z4.id970.site/';
    public function order($request) {
        $parameters = [
            'mchId' => $this->_mchId,
            'appId' => '00a0611977a848a8b6c8c83b77cc655e',
            'productId' => $request['channelId'],
            'mchOrderNo' => 'YZ' . $request['orderNo'],
            'amount' => $request['amount'],
            'currency' => 'cny',
            'notifyUrl' => config('app.url').'/api/pay/xr/notify',
//            'redirectUrl' => $request['redirectUrl'],
            'subject' => 'subject',
            'body' => 'bodybody',
            'reqTime' => date('YmdHis'),
            'version' => '1.0'

        ];
        if(!empty($request['redirectUrl'])) {
            $parameters['returnUrl'] = $request['redirectUrl'];
        } else {
            $parameters['returnUrl'] = config('app.url').'/api/pay/xr/notify';
        }

        $signObj = new Sign();
        $sign = $signObj->encode($parameters, $this->_key);
        $parameters['sign'] = $sign;
        $response = Http::asForm()->post($this->_url.'api/pay/startOrder', $parameters);
        $response = $response->json();
        if($response['retCode'] == '0') {
            return [
                'code' => 1,
                'data' => [
                    'qrImgUrl' => $response['payJumpUrl'],
                    'payOrderId' => $response['payOrderId'],
                    'qrUrl' => $response['payJumpUrl'],
                    'payUrl' => $response['payJumpUrl'],
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
