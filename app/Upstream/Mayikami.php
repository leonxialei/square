<?php  namespace App\Upstream;
use App\Help\Sign;
use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use App\Help\Methods;

class Mayikami {
    private $_mchId = '635376';
    private $_key = 'D98AE5C9D30F1FE1A8AB409929F56598';
    private $_url = 'http://103.231.173.50/';
    public function order($request) {
        $parameters = [
            'api_id' => $this->_mchId,
            'orderid' => 'YZ'.$request['orderNo'],
            'money' => $request['amount']/100,
            'notify_url' => config('app.url').'/api/pay/mayikami/notify',


        ];
        if(!empty($request['redirectUrl'])) {
            $parameters['return_url'] = $request['redirectUrl'];
        } else {
            $parameters['return_url'] = config('app.url').'/api/pay/mayikami/notify';
        }
        $signObj = new Sign();
        $sign = $signObj->encode($parameters, $this->_key);
        $parameters['sign'] = $sign;

        $parameters['ip'] = rand(1,200).'.'.rand(1,200).'.'.rand(1,200).'.'.rand(1,200);	//请求IP
        $parameters['gtype'] = $request['channelId'];	//通道代码


        $response = Http::asForm()->post($this->_url.'api/pay', $parameters);
        $response = $response->json();

        if($response['code'] == '0') {
            return [
                'code' => 1,
                'data' => [
                    'qrImgUrl' => $response['payUrl'],
                    'payOrderId' => 'YZ'.$request['orderNo'],
                    'qrUrl' => $response['payUrl'],
                    'payUrl' => $response['payUrl'],
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
        $mchOrderNo = substr($request->get('orderid'), 2);
        $orderModel = new Order();
        $order = $orderModel->where('OrderNo', $mchOrderNo)->first();
        if(empty($order)) {
            return false;
        }
        if($order->status == 0) {
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
