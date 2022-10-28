<?php  namespace App\Upstream;
use App\Help\Sign;
use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Canglei {
    private $_mchId = '1562439611198795778';
    private $_key = 'cudx5p58f5hlidoaqmwiai3hv9e3bml4';
    private $_url = 'http://45.152.218.240:8888/';
    public function order($request) {
        $parameters = [
            'amount' => sprintf("%.2f", $request['amount']/100),
//            'body' => $request['body'],
//            'device' => 'ios',
            'ip' => $request['clientIp'],
            'merchantId' => $this->_mchId,
            'notifyUrl' => config('app.url').'/api/pay/canglei/notify',
            'outTradeNo' => 'YZ'.$request['orderNo'],
            'passageCode' => $request['channelId'],
            'subject' => $request['subject'],
            'timestamp' => date('Y-m-d H:i:s')
        ];
        if(!empty($request['redirectUrl'])) {
            $parameters['returnUrl'] = $request['redirectUrl'];
        } else {
            $parameters['returnUrl'] = config('app.url').'/api/pay/canglei/notify';
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
