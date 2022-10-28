<?php  namespace App\Upstream;
use App\Help\Sign;
use App\Models\Order;
use Illuminate\Support\Facades\Http;

class Gdshihua {
    private $_mchId = 'SH10015';
    private $_key = 'd0f27bb90fdacdbedf8358c0eec54045';
    private $_url = 'http://18.162.198.149:8100/';
    public function order($request) {
        $parameters = [
            'mchId' => $this->_mchId,
            'mchOrderNo' => 'YZ' . $request['orderNo'],
            'amount' => $request['amount'],
            'channelType' => $request['channelId'],
            'notifyUrl' => config('app.url').'/api/pay/notify',
//            'redirectUrl' => $request['redirectUrl'],
            'extra' => $request['subject'],
            'clientIp' => $request['clientIp'],

        ];

        $signObj = new Sign();
        $sign = $signObj->encode($parameters, $this->_key);
        $parameters['sign'] = $sign;
        $response = Http::post($this->_url.'api/pay/order/create', $parameters);
        $response = $response->json();

        if($response['code'] == 0) {
            return [
                'code' => 1,
                'data' => [
                    'qrImgUrl' => $response['data']['payInfo'],
                    'payOrderId' => $response['data']['payOrderId'],
                    'qrUrl' => $response['data']['payInfo'],
                    'payUrl' => $response['data']['payInfo'],
                    'channelId' => $request['data']['channelId'],
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
        $mchOrderNo = substr($request->get('mchOrderNo'), 2);
        $orderModel = new Order();
        $order = $orderModel->where('OrderNo', $mchOrderNo)->first();
        if(empty($order)) {
            return false;
        }
        if($request->get('status') == 2 && $order->status == 0) {
            if(empty($request->get('amount')) || $request->get('amount') == 0
                || $request->get('amount') < $order->original_amount) {
                return false;
            }
            $orderModel->where('OrderNo', $mchOrderNo)->update([
                'status' => 1,
                'pay_amount' => $request->get('amount'),
                'pay_time' => time(),
'created' => time()
            ]);
        }
        return $orderModel->where('id', $order->id)->first();
    }
}
