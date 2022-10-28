<?php  namespace App\Upstream;
use App\Help\Sign;
use App\Models\Order;
use Illuminate\Support\Facades\Http;

class Tianlong {
    private $_mchId = '1100006';
    private $_key = '82c296688e32d564e81b0fd28001b02f';
    private $_url = 'http://103.13.229.1:8081/';
    public function order($request) {
        $parameters = [
            'mchId' => $this->_mchId,
            'mchOrderNo' => 'YZ' . $request['orderNo'],
            'amount' => $request['amount'],
            'channelId' => $request['channelId'],
            'notifyUrl' => config('app.url').'/api/pay/notify',
//            'redirectUrl' => $request['redirectUrl'],
            'subject' => $request['subject'],
            'body' => $request['body'],
            'clientIp' => $request['clientIp'],

        ];
        if(!empty($request['redirectUrl'])) {
            $parameters['redirectUrl'] = $request['redirectUrl'];
        } else {
            $parameters['redirectUrl'] = config('app.url').'/api/pay/notify';
        }

        $signObj = new Sign();
        $sign = $signObj->encode($parameters, $this->_key);
        $parameters['sign'] = $sign;
        $response = Http::post($this->_url.'api/pay/create_order', $parameters);
        $response = $response->json();

        if($response['status'] == 200) {
            return [
                'code' => 1,
                'data' => [
                    'qrImgUrl' => $response['qrImgUrl'],
                    'payOrderId' => $response['payOrderId'],
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
