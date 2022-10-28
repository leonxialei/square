<?php  namespace App\Upstream;
use App\Help\Sign;
use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Maimaitong {
    private $_mchId = 'facai';
    private $_key = 'ba1a6e969078a5cf9c78445d126f3f3b';
    private $_url = 'http://g8g0p6hf.20mu.pw/';
    public function order($request) {
        $parameters = [
            'merchantNum' => $this->_mchId,
            'orderNo' => 'YZ'.$request['orderNo'],
            'amount' =>  sprintf("%.2f", $request['amount']/100),
            'payType' => $request['channelId'],
            'ip' => $request['clientIp'],
            'notifyUrl' => config('app.url').'/api/pay/maimaitong/notify',


        ];
        $sign = md5($this->_mchId.$parameters['orderNo'].(string)$parameters['amount'].$parameters['notifyUrl'].
            $this->_key);

        $parameters['sign'] = $sign;

        $response = Http::asForm()->post($this->_url.'api/startOrder', $parameters);
        $response = $response->json();
        if(strtoupper($response['code']) == 200) {
            return [
                'code' => 1,
                'data' => [
                    'qrImgUrl' => $response['data']['payUrl'],
                    'payOrderId' => 'YZ'.$request['orderNo'],
                    'qrUrl' => $response['data']['payUrl'],
                    'payUrl' => $response['data']['payUrl'],
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


        $sign = md5($request['state'].$this->_mchId.$request['orderNo'].$request['amount'].
            $this->_key);
        if($request->get('sign') != $sign) {
            return [
                'status' => '30003',
                'msg' => 'Signature error'
            ];
        }
        $mchOrderNo = substr($request->get('orderNo'), 2);
        $orderModel = new Order();
        $order = $orderModel->where('OrderNo', $mchOrderNo)->first();
        if(empty($order)) {
            return false;
        }
        if(strtoupper($request['state']) == 1 && $order->status == 0) {
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
