<?php  namespace App\Upstream;
use App\Help\Sign;
use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use App\Help\Methods;

class Dashaoye {
    private $_mchId = '737024227923722240';
    private $_key = 'ac7af5adb3284752bca04d7e56ef1883';
    private $_url = 'http://156.253.15.245:8090/';
    public function order($request) {
        $parameters = [
            'uid' => $this->_mchId,
            'money' => sprintf("%.2f", $request['amount']/100),
            'channel' => $request['channelId'],
            'outTradeNo' => 'YZ'.$request['orderNo'],
            'notifyUrl' => config('app.url').'/api/pay/dashaoye/notify',
            'timestamp' => Methods::get_total_millisecond(),
            'token' => $this->_key,

        ];
        if(!empty($request['redirectUrl'])) {
            $parameters['returnUrl'] = $request['redirectUrl'];
        } else {
            $parameters['returnUrl'] = config('app.url').'/api/pay/dashaoye/notify';
        }
        $signObj = new Sign();
        $sign = $signObj->brencode($parameters, '');
        $parameters['sign'] = $sign;
        unset($parameters['token']);
        $response = Http::asForm()->post($this->_url.'api/v1/charges', $parameters);
        $response = $response->json();

        if($response['code'] == '0') {
            return [
                'code' => 1,
                'data' => [
                    'qrImgUrl' => $response['data']['qrcodeContent'],
                    'payOrderId' => $response['data']['tradeNo'],
                    'qrUrl' => $response['data']['qrcodeUrl'],
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
        $req['token'] = $this->_key;
        $signObj = new sign();
        $sign = $signObj->brencode($req, '');
        if($request->get('sign') != $sign) {
            return [
                'status' => '30003',
                'msg' => 'Signature error'
            ];
        }
        $mchOrderNo = substr($request->get('outTradeNo'), 2);
        $orderModel = new Order();
        $order = $orderModel->where('OrderNo', $mchOrderNo)->first();
        if(empty($order)) {
            return false;
        }
        if($order->status == 0) {
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
