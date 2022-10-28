<?php  namespace App\Upstream;
use App\Help\Sign;
use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Yisheng {
    private $_mchId = '478';
    private $_key = '41898f31fd0479fc246cc1598564f09a';
    private $_url = 'http://abc.abc988.com/';
    public function order($request) {
        $parameters = [
            'mch_id' => $this->_mchId,
            'orderid' => 'YZ'.$request['orderNo'],
            'money' => $request['amount'],
            'type' => $request['channelId'],
            'notifyUrl' => config('app.url').'/api/pay/yisheng/notify',
            'applytime' => date('Y-m-d H:i:s'),

        ];

        $signObj = new Sign();
        $sign = $signObj->zhongyou($parameters, $this->_key);
        $parameters['sign'] = $sign;
        $parameters['attach'] = 'json';
        $response = Http::asForm()->post($this->_url.'pay', $parameters);
        $response = $response->json();
        if($response['status'] == 1) {
            return [
                'code' => 1,
                'data' => [
                    'qrImgUrl' => $response['payUrl'],
                    'payOrderId' => 'YZ'.$request['orderNo'],
                    'qrUrl' => $response['payUrl'],
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
        unset($req['attach']);
        $signObj = new sign();
        $sign = $signObj->zhongyou($req, $this->_key);
        if($request->get('sign') != $sign) {
            return [
                'status' => '30003',
                'msg' => 'Signature error'
            ];
        }
        $mchOrderNo = substr($request->get('orderid'), 2);
        $orderModel = new Order();
        $order = $orderModel->where('OrderNo', $mchOrderNo)->first();
        if(empty($order)) {
            return false;
        }
        if(($request['status'] == '2' || $request['status'] == '1') && $order->status == 0) {
            if(empty($request->get('money')) || $request->get('money') == 0) {
                return false;
            }
            $orderModel->where('OrderNo', $mchOrderNo)->update([
                'status' => 1,
                'pay_amount' => $request->get('money')*100,
                'pay_time' => time(),
'created' => time()
            ]);
        }
        return $orderModel->where('id', $order->id)->first();
    }
}
