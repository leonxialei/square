<?php  namespace App\Upstream;
use App\Help\Sign;
use App\Models\Order;
use Illuminate\Support\Facades\Http;

class Tiantian {
    private $_mchId = '20000002';
    private $_key = 'SKWYKEMXE7LUKX376VY7AMPKUO8ZSZ1YDLFEKHZZCKDEBSAWDSFQTC5UNBV2A3AEPHDDADYD15Y1X3HCLBSDHAOHCACEUCI6ZE2KXP5BRZNRGWPQGXLWR4OPOC5Y5V6Q';
    private $_url = 'http://103.194.185.154:56700/';
    public function order($request) {
        $parameters = [
            'mchId' => $this->_mchId,
            'appId' => '402c6e7a6cd14a6bbc5a2bd4e64a92ad',
            'productId' => $request['channelId'],
            'mchOrderNo' => 'YZ' . $request['orderNo'],
            'amount' => $request['amount'],
            'currency' => 'cny',
            'notifyUrl' => config('app.url').'/api/pay/tiantian/notify',
            'subject' => $request['subject'],
            'body' => $request['body'],
            'reqTime' => date('YmdHis'),
            'version' => '1.0',


//            'redirectUrl' => $request['redirectUrl'],

        ];
        if(!empty($request['redirectUrl'])) {
            $parameters['returnUrl'] = $request['redirectUrl'];
        } else {
            $parameters['returnUrl'] = config('app.url').'/api/pay/mu/notify';
        }

        $signObj = new Sign();
        $sign = $signObj->encode($parameters, $this->_key);
        $parameters['sign'] = $sign;
        $response = Http::asForm()->post($this->_url.'api/pay/create_order', $parameters);
        $response = $response->json();
        if($response['retCode'] == '0') {
            return [
                'code' => 1,
                'data' => [
                    'qrImgUrl' => $response['payJumpUrl'],
                    'payOrderId' => 'YZ' . $request['orderNo'],
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
