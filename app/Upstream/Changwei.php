<?php  namespace App\Upstream;
use App\Help\Sign;
use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Changwei {
    private $_mchId = '6086';
    private $_key = '0e0511439f926c73a857bc68830af6b260713ed5';
    private $_url = 'http://cw.cwka8.pro/';
    public function order($request) {
        $parameters = [
            'id' => $this->_mchId,
            'out_order_sn' => 'YZ'.$request['orderNo'],
            'notify_url' => config('app.url').'/api/pay/changwei/notify',
            'name' => 'changwei',
            'total_fee' => sprintf("%.2f", $request['amount']/100),
            'channel' => $request['channelId'],

        ];

        $signObj = new Sign();
        $sign = $signObj->makeSign($parameters, $this->_key);
        $parameters['sign'] = $sign;
        $response = Http::post($this->_url.'api/order/create', $parameters);
        $response = $response->json();
        if($response['code'] == 1) {
            return [
                'code' => 1,
                'data' => [
                    'qrImgUrl' => $response['url'],
                    'payOrderId' => 'YZ'.$request['orderNo'],
                    'qrUrl' => $response['url'],
                    'payUrl' => $response['url'],
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
        $sign = $signObj->makeSign($req, $this->_key);


        if($request->get('sign') != $sign) {
            return [
                'status' => '30003',
                'msg' => 'Signature error'
            ];
        }
        $mchOrderNo = substr($request->get('out_order_sn'), 2);
        $orderModel = new Order();
        $order = $orderModel->where('OrderNo', $mchOrderNo)->first();
        if(empty($order)) {
            return false;
        }
        if(strtoupper($request['status']) == 1 && $order->status == 0) {
            if(empty($request->get('total_fee')) || $request->get('total_fee') == 0) {
                return false;
            }
            $orderModel->where('OrderNo', $mchOrderNo)->update([
                'status' => 1,
                'pay_amount' => $request->get('total_fee')*100,
                'pay_time' => time(),
'created' => time()
            ]);
        }
        return $orderModel->where('id', $order->id)->first();
    }
}
