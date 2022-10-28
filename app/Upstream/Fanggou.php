<?php  namespace App\Upstream;
use App\Help\Sign;
use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Fanggou {
    private $_mchId = '2206246963';
    private $_key = '5b94a7ce5b6ac5ddfdd6aea3a5954075';
    private $_url = 'http://2206246963.pp.jubaola.cn/';
    public function order($request) {
        $parameters = [
            'appkey' => $this->_mchId,
            'number' => 'YZ'.$request['orderNo'],
            'money' => $request['amount']/100,
            'type' => $request['channelId'],
            'return_url' => config('app.url').'/api/pay/fanggou/notify',

        ];


        if(!empty($request['redirectUrl'])) {
            $parameters['notify_url'] = $request['redirectUrl'];
        } else {
            $parameters['notify_url'] = config('app.url').'/api/pay/fanggou/notify';
        }
        $sign = md5($parameters['appkey'].'&'.$parameters['number'].'&'.$parameters['money'].'&'.
            $parameters['type'].'&'.$this->_key);

        $parameters['key'] = $sign;
        $data = [];
        foreach ($parameters as $key=>$value) {
            $data[$key] = urlencode($value);
        }
        $response = Http::asForm()->get($this->_url.'api/createOrder', $data);
        $response = $response->json();

        if(strtoupper($response['code']) == 1000) {
            return [
                'code' => 1,
                'data' => [
                    'qrImgUrl' => $response['data']['qrcode'],
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


        $sign = md5($request['number'].'&'.$request['time'].'&'.$request['money'].'&'.
            $request['status'].'&'.$this->_key);
        if($request->get('key') != $sign) {
            return [
                'status' => '30003',
                'msg' => 'Signature error'
            ];
        }
        $mchOrderNo = substr($request->get('number'), 2);
        $orderModel = new Order();
        $order = $orderModel->where('OrderNo', $mchOrderNo)->first();
        if(empty($order)) {
            return false;
        }
        if(strtoupper($request['status']) == 'success' && $order->status == 0) {
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
