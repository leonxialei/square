<?php  namespace App\Upstream;
use App\Help\Sign;
use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Yongheng2 {
    private $_mchId = '10248';
    private $_key = '633568d4e4b07cc259cc5a86';
    private $_url = 'http://yh.666go.cc/';
    public function order($request) {
        $parameters = [
            'busId' => $this->_mchId,
            'orderNo' => 'YZ'.$request['orderNo'],
            'channelProductId' => $request['channelId'],
            'orderAmount' => $request['amount']/100,
            'notifyUrl' => config('app.url').'/api/pay/yongheng2/notify',


        ];
        if(!empty($request['redirectUrl'])) {
            $parameters['callbackUrl'] = $request['redirectUrl'];
        } else {
            $parameters['callbackUrl'] = config('app.url').'/api/pay/yongheng/notify';
        }
        $signObj = new Sign();
        $sign = $signObj->smencode($parameters, $this->_key);
        $parameters['sign'] = $sign;
        $response = Http::asForm()->post($this->_url.'manager/bus/order/create', $parameters);
        $response = $response->json();
        if($response['code'] == '0') {
            return [
                'code' => 1,
                'data' => [
                    'qrImgUrl' => $response['data'],
                    'payOrderId' => 'YZ'.$request['orderNo'],
                    'qrUrl' => $response['data'],
                    'payUrl' => $response['data'],
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
        $mchOrderNo = substr($request->get('orderNo'), 2);
        $orderModel = new Order();
        $order = $orderModel->where('OrderNo', $mchOrderNo)->first();
        if(empty($order)) {
            return false;
        }
        if($order->status == 0) {
            if(empty($request->get('orderAmount')) || $request->get('orderAmount') == 0) {
                return false;
            }
            $orderModel->where('OrderNo', $mchOrderNo)->update([
                'status' => 1,
                'pay_amount' => $request->get('orderAmount')*100,
                'pay_time' => time(),
'created' => time()
            ]);
        }
        return $orderModel->where('id', $order->id)->first();
    }
}
