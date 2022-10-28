<?php  namespace App\Upstream;
use App\Help\Sign;
use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Qiaopai {
    private $_mchId = '10037';
    private $_key = '7aa2f3184ae4a3cb62d75bf14fad5c67';
    private $_url = 'http://888.999vip.me/';
    public function order($request) {
        $parameters = [
            'merchantId' => $this->_mchId,
            'orderId' => 'YZ'.$request['orderNo'],
            'orderAmount' => sprintf("%.2f", $request['amount']/100),
            'channelType' => $request['channelId'],
            'notifyUrl' => config('app.url').'/api/pay/qiaopai/notify',

        ];

        $signObj = new Sign();
        $sign = $signObj->smencode($parameters, $this->_key);
        $parameters['sign'] = $sign;
        $response = Http::post($this->_url.'api/newOrder', $parameters);
        $response = $response->json();

        if($response['code'] == '200') {
            return [
                'code' => 1,
                'data' => [
                    'qrImgUrl' => $response['data']['payUrl'],
                    'payOrderId' => 'YZ'.$request['orderNo'],
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
        $signObj = new sign();
        $sign = $signObj->smencode($req, $this->_key);
        if($request->get('sign') != $sign) {
            return [
                'status' => '30003',
                'msg' => 'Signature error'
            ];
        }
        $mchOrderNo = substr($request->get('orderId'), 2);
        $orderModel = new Order();
        $order = $orderModel->where('OrderNo', $mchOrderNo)->first();

        if(empty($order)) {
            return false;
        }
        if(strtoupper($request['status']) == 'OK' && $order->status == 0) {
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
