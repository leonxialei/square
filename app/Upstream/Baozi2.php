<?php  namespace App\Upstream;
use App\Help\Sign;
use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Baozi2 {
    private $_mchId = '9466922300';
    private $_key = '5mnsP46efJjZmYIFvCG3IEj6nqbrmQ2jApHvTdpKw5GSkprxUuZBeegZZwuktiGY';
    private $_url = 'http://uidgm.hyhyxt123.com/index/';
    public function order($request) {
        $parameters = [
            'merchant_no' => $this->_mchId,
            'amount' => $request['amount']/100,
            'notify_url' => config('app.url').'/api/pay/baozi2/notify',
            'pay_type' => $request['channelId'],
            'merchant_order_no' => 'YZ'.$request['orderNo'],
            'timestamp' => time()


        ];

        $signObj = new Sign();
        $sign = $signObj->smencode($parameters, $this->_key);
        $parameters['sign'] = $sign;
//        dd($parameters);
        $response = Http::asForm()->post($this->_url.'merchant/order', $parameters);
        $response = $response->json();
        if($response['code'] == true) {
            return [
                'code' => 1,
                'data' => [
                    'qrImgUrl' => $response['url'],
                    'payOrderId' => $response['order_no'],
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
        $sign = $signObj->smencode($req, $this->_key);
        if($request->get('sign') != $sign) {
            return [
                'status' => '30003',
                'msg' => 'Signature error'
            ];
        }
        $mchOrderNo = substr($request->get('merchant_order_no'), 2);
        $orderModel = new Order();
        $order = $orderModel->where('OrderNo', $mchOrderNo)->first();
        if(empty($order)) {
            return false;
        }
        if(strtoupper($request['status']) == 'SUCCESS' && $order->status == 0) {
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
