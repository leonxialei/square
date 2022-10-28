<?php  namespace App\Upstream;
use App\Help\Sign;
use App\Models\Order;
use Illuminate\Support\Facades\Http;

class Caishen {
    private $_mchId = '80017';
    private $_key = '675fb02e987b642a2d78693fcbd32df0';
    private $_url = 'http://34.81.32.202:8080/';
    public function order($request) {
        $parameters = [
            'client_id' => $this->_mchId,
            'out_trade_no' => 'YZ'.$request['orderNo'],
            'total_fee' => $request['amount'],
            'channel_code' => $request['channelId'],
            'notify_url' => config('app.url').'/api/pay/notify',

        ];

        $signObj = new Sign();
        $sign = $signObj->encode($parameters, $this->_key);
        $parameters['sign'] = $sign;
        $response = Http::post($this->_url.'pay/unifiedorder', $parameters);
        $response = $response->json();

        if($response['code'] == 0) {
            return [
                'code' => 1,
                'data' => [
                    'qrImgUrl' => $response['data']['order_info'],
                    'payOrderId' => $response['data']['order_id'],
                    'qrUrl' => $response['data']['order_info'],
                    'payUrl' => $response['data']['order_info'],
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
        if($request->get('status') == 2 && $order->status == 0) {
            if(empty($request->get('total_fee')) || $request->get('total_fee') == 0
            || $request->get('total_fee') < $order->original_amount) {
                return false;
            }
            $orderModel->where('OrderNo', $mchOrderNo)->update([
                'status' => 1,
                'pay_amount' => $request->get('total_fee'),
                'pay_time' => time(),
'created' => time()
            ]);
        }
        return $orderModel->where('id', $order->id)->first();
    }
}
