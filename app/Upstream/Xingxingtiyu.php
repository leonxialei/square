<?php  namespace App\Upstream;
use App\Help\Methods;
use App\Help\Sign;
use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Xingxingtiyu {
    private $_mchId = '201577';
    private $_key = '292F6B46EFC14046BAEC3AD0632A5B57';
    private $_url = 'https://moon888.store/';
    public function order($request) {
        $parameters = [
            'merchant_id' => $this->_mchId,
            'app_id' => '-',
            'version' => 'V2.0',
            'pay_type' => $request['channelId'],
            'device_type' => 'wap',
            'request_time' => date('YmdHis'),
            'nonce_str' => Str::random(20),
            'pay_ip' => $request['clientIp'],
            'out_trade_no' => 'YZ'.$request['orderNo'],
            'amount' => sprintf("%.2f", $request['amount']/100),
            'notify_url' => config('app.url').'/api/pay/xingxingtiyu/notify',


        ];

        $signObj = new Sign();
        $sign = $signObj->brencode($parameters, $this->_key);
        $parameters['sign'] = $sign;
        $response = Http::post($this->_url.'gateway/dopay', $parameters);
        $response = $response->json();

        if(strtoupper($response['status']) == 'SUCCESS') {
            return [
                'code' => 1,
                'data' => [
                    'qrImgUrl' => $response['pay_url'],
                    'payOrderId' => $response['out_trade_no'],
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
        $sign = $signObj->brencode($req, $this->_key);
        if($request->get('sign') != $sign) {
            return [
                'status' => '30003',
                'msg' => 'Signature error'
            ];
        }
        $json = [
            'id'=>$request->get('out_trade_no'),
            'date' => Methods::get_total_millisecond()
        ];
        $json = json_encode($json);
        $mchOrderNo = substr($request->get('out_trade_no'), 2);
        $orderModel = new Order();
        $order = $orderModel->where('OrderNo', $mchOrderNo)->first();
        if(empty($order)) {
            return false;
        }
        if(strtoupper($request['status']) == 'SUCCESS' && $order->status == 0) {
            if(empty($request->get('pay_amount')) || $request->get('pay_amount') == 0) {
                return false;
            }
            $orderModel->where('OrderNo', $mchOrderNo)->update([
                'status' => 1,
                'pay_amount' => $request->get('pay_amount')*100,
                'pay_time' => time(),
'created' => time()
            ]);
        }
        return $orderModel->where('id', $order->id)->first();
    }
}
