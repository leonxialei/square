<?php  namespace App\Upstream;
use App\Help\Sign;
use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Xiaoniu {
    private $_mchId = 'facai';
    private $_key = 'a16a2fc1-f52c-4413-85b7-f71ed715c1fd';
    private $_url = 'http://47.243.176.160/';
    public function order($request) {
        $parameters = [
            'name' => $this->_mchId,
            'type' => $request['channelId'],
            'pass_no' => 'fjsh',
            'sys' => 'lZFEaAzL',
            'order_id' => 'YZ'.$request['orderNo'],
            'money' => $request['amount']/100,

            'notify_url' => config('app.url').'/api/pay/xiaoniu/notify',
            'form_type' => 'json',



            'remark' => rand(2222,999999999)
        ];

        if(!empty($request['redirectUrl'])) {
            $parameters['success_url'] = $request['redirectUrl'];
        } else {
            $parameters['success_url'] = config('app.url').'/api/pay/xiaoniu/notify';
        }
        $sign = md5(md5($parameters['form_type'].$parameters['money'].$parameters['name'].
                $parameters['notify_url']. $parameters['order_id'].$parameters['success_url'].
                $parameters['type']).$this->_key);
        $parameters['sign'] = $sign;
        $response = Http::asForm()->post($this->_url.'index.php', $parameters);
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
                'msg' => '签名错误'
            ];
        }
        $mchOrderNo = substr($request->get('order_id'), 2);
        $orderModel = new Order();
        $order = $orderModel->where('OrderNo', $mchOrderNo)->first();
        if(empty($order)) {
            return false;
        }
        if(strtoupper($request['status']) == 200 && $order->status == 0) {
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
