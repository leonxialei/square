<?php  namespace App\Upstream;
use App\Help\Sign;
use App\Models\Order;
use Illuminate\Support\Facades\Http;

class Wuming {
    private $_mchId = '10000432';
    private $_key = '4df95a98533bebfba0e65592f2eeaa5d';
    private $_url = 'http://34.92.147.35:3020/';
    public function order($request) {
        $parameters = [
            'mchId' => $this->_mchId,
            'mchOrderNo' => 'YZ' . $request['orderNo'],
            'channelId' => $request['channelId'],
            'amount' => $request['amount'],
            'notifyUrl' => config('app.url').'/api/pay/wuming/notify',
            'subject' => $request['subject'],
            'body' => $request['body'],
            'clientIp' => rand(1,200).'.'.rand(1,200).'.'.rand(1,200).'.'.rand(1,200),



        ];
        if(!empty($request['redirectUrl'])) {
            $parameters['redirectUrl'] = $request['redirectUrl'];
        } else {
            $parameters['redirectUrl'] = config('app.url').'/api/pay/wuming/notify';
        }

        $signObj = new Sign();
        $sign = $signObj->encode($parameters, $this->_key);
        $parameters['sign'] = $sign;
        $response = Http::post($this->_url.'api/pay/create_order', $parameters);
        $response = $response->json();
        if($response['status'] == '200') {
            return [
                'code' => 1,
                'data' => [
                    'qrImgUrl' => $response['qrImgUrl'],
                    'payOrderId' => 'YZ' . $request['orderNo'],
                    'qrUrl' => $response['qrUrl'],
                    'payUrl' => $response['payUrl'],
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
            if(empty($request->get('amount')) || $request->get('amount') == 0) {
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
