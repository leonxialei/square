<?php  namespace App\Upstream;
use App\Help\Sign;
use App\Models\Order;
use Illuminate\Support\Facades\Http;

class Acpay {
    private $_mchId = '880021';
    private $_key = '7a3c960c6a4a214570048613cf221bdd';
    private $_url = 'http://v.acbili.cn/';
    public function order($request) {
        $parameters = [
            'mch_id' => $this->_mchId,
            'orderid' => 'YZ'.$request['orderNo'],
            'money' => $request['amount'],
            'type' => $request['channelId'],
            'notifyUrl' => config('app.url').'/api/pay/acpay/notify',
            'applytime' => date('Y-m-d H:i:s'),


        ];
        $signObj = new Sign();
        $sign = $signObj->zhongyou($parameters, $this->_key);
        $parameters['sign'] = $sign;
        $parameters['attach'] = 'json';
        $response = Http::asForm()->post($this->_url.'pay', $parameters);
        $response = $response->json();
        if($response['status'] == 1 || $response['status'] == 2) {
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
        if(isset($req['attach'])) {
            unset($req['attach']);
        }
        $signObj = new sign();
        $sign = $signObj->zhongyou($req, $this->_key);
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
        if(($request->get('status') == 1 ||$request->get('status') == 2)&& $order->status == 0) {
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
