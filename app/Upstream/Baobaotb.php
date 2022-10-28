<?php  namespace App\Upstream;
use App\Help\Sign;
use App\Models\Order;
use Illuminate\Support\Facades\Http;

class Baobaotb {
    private $_mchId = '2088031216502326073648811';
    private $_key = 'cU9RNHV0NkQ0OWN2dm9CVnUxNDZPTUlOZ0t6KzNwZmRtR1M0ejkzVWJLdFNiRHRnK21kK3YyMThWUFUrSDNhaA==';
    private $_url = 'http://129.226.191.213:22666/';
    public function order($request) {
        $parameters = [
            'mchid' => $this->_mchId,
            'paytype' => $request['channelId'],
            'amount' => $request['amount'],
            'orderid' => 'YZ'.$request['orderNo'],
            'ordertime' => date('YmdHis'),
            'notifyurl' => config('app.url').'/api/pay/baobaotb/notify',
            'createip' => $request['clientIp'],

        ];

        $sign = md5('mchid='.$parameters['mchid'].'&paytype='.$parameters['paytype'].
            '&amount='.$parameters['amount'].'&orderid='.$parameters['orderid'].
            '&ordertime='.$parameters['ordertime'].'&'.$this->_key);
        $parameters['sign'] = $sign;


        $response = Http::get($this->_url.'pay/gateway', $parameters);
        $response = $response->json();
        if($response['code'] == '200') {
            return [
                'code' => 1,
                'data' => [
                    'qrImgUrl' => $response['result']['payinfo'],
                    'payOrderId' => 'YZ'.$request['orderNo'],
                    'qrUrl' => $response['result']['payinfo'],
                    'payUrl' => $response['result']['payinfo'],
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
        $sign = md5('mchid='.$req['mchid'].'&paytype='.$req['paytype'].
            '&amount='.$req['amount'].'&ordertime='.$req['ordertime'].
            '&orderiscomplete='.$req['orderiscomplete'].'&orderno='.$req['orderno'].
            '&mchorderno='.$req['mchorderno'].'&'.$this->_key);
        if($request->get('sign') != $sign) {
            return [
                'status' => '30003',
                'msg' => '签名错误'
            ];
        }
        $mchOrderNo = substr($request->get('mchorderno'), 2);
        $orderModel = new Order();
        $order = $orderModel->where('OrderNo', $mchOrderNo)->first();
        if(empty($order)) {
            return false;
        }
        if($request->get('orderiscomplete') == 1 && $order->status == 0) {
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
