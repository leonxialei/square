<?php  namespace App\Upstream;
use App\Help\Sign;
use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Hanyu {
    private $_mchId = '4';
    private $_key = 'b815293ce33e4fcdb6fbc25231e873ba';
    private $_url = 'http://106.3.84.58:8080/';
    public function order($request) {
        $parameters = [
            'mch_id' => $this->_mchId,
            'pass_code' => $request['channelId'],
            'subject' => $request['subject'],
            'out_trade_no' => 'YZ'.$request['orderNo'],
            'money' => $request['amount']/100,
            'client_ip' => $request['clientIp'],
            'notify_url' => config('app.url').'/api/pay/hanyu/notify',
            'timestamp' => date('Y-m-d H:i:s'),

        ];
        if(!empty($request['redirectUrl'])) {
            $parameters['return_url'] = $request['redirectUrl'];
        } else {
            $parameters['return_url'] = config('app.url').'/api/pay/hanyu/notify';
        }
        $signObj = new Sign();
        $sign = $signObj->encode($parameters, $this->_key);
        $parameters['sign'] = $sign;
        $response = Http::post($this->_url.'api/mch/unifiedorder', $parameters);
        $response = $response->json();

        if($response['code'] == '0') {
            return [
                'code' => 1,
                'data' => [
                    'qrImgUrl' => $response['data']['pay_url'],
                    'payOrderId' => $response['data']['trade_no'],
                    'qrUrl' => $response['data']['pay_url'],
                    'payUrl' => $response['data']['pay_url'],
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
        $mchOrderNo = substr($request->get('out_trade_no'), 2);
        $orderModel = new Order();
        $order = $orderModel->where('OrderNo', $mchOrderNo)->first();
        if(empty($order)) {
            return false;
        }
        if(strtoupper($request['state']) == 2 && $order->status == 0) {
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
