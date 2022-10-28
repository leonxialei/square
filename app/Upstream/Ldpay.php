<?php  namespace App\Upstream;
use App\Help\Sign;
use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Ldpay {
    private $_mchId = '1548405346417045505';
    private $_key = '421kki90qo6srggw85x96uvccpthiprk';
    private $_url = 'http://38.55.20.53:8888/';
    public function order($request) {
        $parameters = [
            'amount' => sprintf("%.2f", $request['amount']/100),
//            'body' => $request['body'],
//            'device' => 'ios',
            'ip' => $request['clientIp'],
            'merchantId' => $this->_mchId,
            'notifyUrl' => config('app.url').'/api/pay/ldpay/notify',
            'outTradeNo' => 'YZ'.$request['orderNo'],
            'passageCode' => $request['channelId'],
            'subject' => $request['subject'],
            'timestamp' => date('Y-m-d H:i:s')
        ];
        if(!empty($request['redirectUrl'])) {
            $parameters['returnUrl'] = $request['redirectUrl'];
        } else {
            $parameters['returnUrl'] = config('app.url').'/api/pay/ldpay/notify';
        }
        $signObj = new Sign();
        $sign = $signObj->zhongyou($parameters, $this->_key);
        $parameters['sign'] = $sign;
        $response = Http::post($this->_url.'api/unifiedorder', $parameters);
        $response = $response->json();

        if($response['code'] == '200') {
            return [
                'code' => 1,
                'data' => [
                    'qrImgUrl' => $response['data']['payUrl'],
                    'payOrderId' => $response['data']['tradeNo'],
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
        $sign = $signObj->zhongyou($req, $this->_key);
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
        if(strtoupper($request['status']) == 2 && $order->status == 0) {
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
