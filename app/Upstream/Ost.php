<?php  namespace App\Upstream;
use App\Help\Sign;
use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Ost {
    private $_mchId = '620';
    private $_key = 'TjL2tZR6cVwlyUbHaGKmQnAhJzXkYds9';
    private $_url = 'http://154.23.183.43:8787/';
    public function order($request) {
        $parameters = [
            'channel_merchant_code' => $this->_mchId,
            'amount' => $request['amount'],
            'product_id' => $request['channelId'],
            'order_number' => 'YZ'.$request['orderNo'],
            'notify_url' => config('app.url').'/api/pay/ost/notify',
        ];
        $signObj = new Sign();
        $sign = $signObj->encode($parameters, $this->_key);
        $parameters['sign'] = $sign;
        $response = Http::post($this->_url.'v1/create', $parameters);
        $response = $response->json();
        if($response['code'] == '0') {
            return [
                'code' => 1,
                'data' => [
                    'qrImgUrl' => $response['data']['pay_url'],
                    'payOrderId' => 'YZ'.$request['orderNo'],
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
                'msg' => '签名错误'
            ];
        }
        $mchOrderNo = substr($request->get('merchant_number'), 2);
        $orderModel = new Order();
        $order = $orderModel->where('OrderNo', $mchOrderNo)->first();
        if(empty($order)) {
            return false;
        }
        if(strtoupper($request['status']) == 1 && $order->status == 0) {
            if(empty($request->get('real_pay_amount')) || $request->get('real_pay_amount') == 0) {
                return false;
            }
            $orderModel->where('OrderNo', $mchOrderNo)->update([
                'status' => 1,
                'pay_amount' => $request->get('real_pay_amount'),
                'pay_time' => time(),
'created' => time()
            ]);
        }
        return $orderModel->where('id', $order->id)->first();
    }
}
