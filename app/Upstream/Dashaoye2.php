<?php  namespace App\Upstream;
use App\Help\Sign;
use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use App\Help\Methods;

class Dashaoye2 {
    private $_mchId = 'FXNQ1653718356496';
    private $_key = '5e3689990607cf3c3bdaa66fe63523';
    private $_url = 'http://39.108.123.156/';
    public function order($request) {
        $parameters = [
            'user_id' => $this->_mchId,
            'money' => sprintf("%.2f", $request['amount']/100),
            'notify_url' => config('app.url').'/api/pay/dashaoye2/notify',
            'type' => 1,
            'timestamp' => time(),
            'user_order_no' => 'YZ'.$request['orderNo'],

        ];
//        if(!empty($request['redirectUrl'])) {
//            $parameters['returnUrl'] = $request['redirectUrl'];
//        } else {
//            $parameters['returnUrl'] = config('app.url').'/api/pay/dashaoye/notify';
//        }
        $signObj = new Sign();
        $sign = $signObj->encode($parameters, $this->_key);
        $parameters['sign'] = $sign;
        unset($parameters['token']);
        $response = Http::asForm()->post($this->_url.'index/api/order', $parameters);
        $response = $response->json();

        if($response['code'] == '1') {
            return [
                'code' => 1,
                'data' => [
                    'qrImgUrl' => $response['pay_url'],
                    'payOrderId' => $response['order_no'],
                    'qrUrl' => $response['pay_url'],
                    'payUrl' => $response['pay_url'],
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
        $mchOrderNo = substr($request->get('user_order_no'), 2);
        $orderModel = new Order();
        $order = $orderModel->where('OrderNo', $mchOrderNo)->first();
        if(empty($order)) {
            return false;
        }
        if(strtoupper($request['trade_status']) == 'SUCCESS' && $order->status == 0) {
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
