<?php  namespace App\Upstream;
use App\Help\Sign;
use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Yinfu {
    private $_mchId = '1040';
    private $_key = 'c5526157246e4ff290dd858b57c5fa99';
    private $_url = 'http://118.107.13.228:23335/';
    public function order($request) {
        $parameters = [
            'mch_id' => $this->_mchId,
            'pass_code' => $request['channelId'],
            'subject' => $request['subject'],
            'out_trade_no' => 'YZ'.$request['orderNo'],
            'amount' => $request['amount']/100,
            'client_ip' => $request['clientIp'],
            'notify_url' => config('app.url').'/api/pay/yinfu/notify',
            'timestamp' => date('Y-m-d H:i:s'),

        ];
        $signObj = new Sign();
        $sign = $signObj->brencode($parameters, $this->_key);
        $parameters['sign'] = $sign;
        $response = Http::post($this->_url.'api/unifiedorder', $parameters);
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
        $sign = $signObj->brencode($req, $this->_key);
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
