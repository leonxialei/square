<?php  namespace App\Upstream;
use App\Help\Sign;
use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Jdkami {
    private $_mchId = '7764142';
    private $_key = 'BB817CC680E2BF43C0B963B7EF9252F8';
    private $_url = 'http://18.162.114.247:9981/';
    public function order($request) {
        $parameters = [
            'api_id' => $this->_mchId,
            'orderid' => 'YZ'.$request['orderNo'],
            'money' => $request['amount']/100,
            'notify_url' => config('app.url').'/api/pay/jdkami/notify',


        ];
        if(!empty($request['redirectUrl'])) {
            $parameters['return_url'] = $request['redirectUrl'];
        } else {
            $parameters['return_url'] = config('app.url').'/api/pay/jdkami/notify';
        }
        $signObj = new Sign();
        $sign = $signObj->encode($parameters, $this->_key);
        $parameters['sign'] = $sign;
        $parameters['ip'] = $request['clientIp'];
//        $parameters['mid'] = $request['channelId'];
        $parameters['gtype'] = 'wpt';
        $response = Http::post($this->_url.'api/pay', $parameters);
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
        if(isset($req['attch'])) {
            unset($req['attch']);
        }
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
