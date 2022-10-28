<?php  namespace App\Upstream;
use App\Help\Sign;
use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Nanwei {
    private $_mchId = 'facai12312312311';
    private $_key = '93bab91c-8e3e-4847-ad84-6f220b6a93c5';
    private $_url = 'http://39.108.15.187:39123/';
    public function order($request) {
        $parameters = [
            'name' => $this->_mchId,
            'type' => $request['channelId'],
            'pass_no' => 'fjsh',
            'sys' => 'gLSxA4Uf',
            'order_id' => 'YZ'.$request['orderNo'],
            'money' => $request['amount']/100,
            'notify_url' => config('app.url').'/api/pay/nanwei/notify',
            'success_url' => config('app.url'),
            'form_type' => 'json',



        ];

        $sign = md5(md5($parameters['form_type'].$parameters['money'].$parameters['name'].$parameters['notify_url'].
            $parameters['order_id'].$parameters['success_url'].$parameters['type']).$this->_key);
        $parameters['sign'] = $sign;
//        dd($parameters);
        $response = Http::asForm()->post($this->_url.'api/index/new_pay', $parameters);
        $response = $response->json();
        if($response['code'] == 1) {
            return [
                'code' => 1,
                'data' => [
                    'qrImgUrl' => $response['data']['url'],
                    'payOrderId' => $response['data']['order_id'],
                    'qrUrl' => $response['data']['url'],
                    'payUrl' => $response['data']['url'],
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


        $sign = md5(md5($req['money'].$req['order_id']).$this->_key);
        if($request->get('sign') != $sign) {
            return [
                'status' => '30003',
                'msg' => 'Signature error'
            ];
        }
        $mchOrderNo = substr($request->get('order_id'), 2);
        $orderModel = new Order();
        $order = $orderModel->where('OrderNo', $mchOrderNo)->first();
        if(empty($order)) {
            return false;
        }
        if(strtoupper($request['status']) == '200' && $order->status == 0) {
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
