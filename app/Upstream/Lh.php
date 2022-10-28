<?php  namespace App\Upstream;
use App\Help\Sign;
use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Lh {
    private $_mchId = 'FXNQ1653718356496';
    private $_key = '5e3689990607cf3c3bdaa66fe635238b';
    private $_url = 'http://8.218.109.166:8094/';
    public function order($request) {
        $parameters = [
            'merchantNum' => $this->_mchId,
            'merchantOrderNo' => 'YZ'.$request['orderNo'],
            'money' => $request['amount']/100,
            'payWay' => 'wx',
            'payWayId' => $request['channelId'],
            'return_url' => config('app.url').'/api/pay/lh/notify',
            'ip' => $request['clientIp'],

        ];

        $sign = md5($this->_mchId.$parameters['money'].$this->_key);
        $parameters['sign'] = $sign;
        $response = Http::asForm()->post($this->_url.'backend/order/optimalPay', $parameters);
        $response = $response->json();

        if($response['success'] == true) {
            return [
                'code' => 1,
                'data' => [
                    'qrImgUrl' => $response['url'],
                    'payOrderId' => $response['data'],
                    'qrUrl' => $response['url'],
                    'payUrl' => $response['url'],
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
        $sign = md5($request->get('orderNo').$request->get('merchantOrderNo').
            $request->get('actualAmount').$this->_key);
        if($request->get('sign') != $sign) {
            return [
                'status' => '30003',
                'msg' => 'Signature error'
            ];
        }
        $mchOrderNo = substr($request->get('merchantOrderNo'), 2);
        $orderModel = new Order();
        $order = $orderModel->where('OrderNo', $mchOrderNo)->first();
        if(empty($order)) {
            return false;
        }
        if($request['status'] == 'SuccessPay' && $order->status == 0) {
            if(empty($request->get('actualAmount')) || $request->get('actualAmount') == 0) {
                return false;
            }
            $orderModel->where('OrderNo', $mchOrderNo)->update([
                'status' => 1,
                'pay_amount' => $request->get('actualAmount')*100,
                'pay_time' => time(),
'created' => time()
            ]);
        }
        return $orderModel->where('id', $order->id)->first();
    }
}
