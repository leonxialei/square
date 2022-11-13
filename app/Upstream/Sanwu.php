<?php  namespace App\Upstream;
use App\Help\Sign;
use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Sanwu {
    private $_mchId = '19';
    private $_key = 'd2735afe37964f1c9967a5ed4fd26e03';
    private $_url = 'http://gateway.ckpay888.xyz/';
    public function order($request) {
        $parameters = [
            'merchantId' => $this->_mchId,
            'merchantTradeNo' => 'YZ'.$request['orderNo'],
            'payment' => 2,
            'channelId' => $request['channelId'],
            'amount' => $request['amount']/100,
            'notifyUrl' => config('app.url').'/api/pay/sanwu/notify',


        ];

//        MD5()
        $sign = md5('amount='.$parameters['amount'].'&merchantId='.$this->_mchId.
            '&merchantTradeNo='.$parameters['merchantTradeNo'].'&payment='.$parameters['payment'].
            '&secretKey='.$this->_key);
        $parameters['sign'] = $sign;
        $response = Http::post($this->_url.'gateway/createTrade', $parameters);
        $response = $response->json();
        if($response['code'] == 200) {
            return [
                'code' => 1,
                'data' => [
                    'qrImgUrl' => $response['data']['payUrl'],
                    'payOrderId' => $response['data']['tradeNo'],
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
        $sign = md5('amount='.$req['amount'].'&merchantId='.$req['merchantId'].
            '&merchantTradeNo='.$req['merchantTradeNo'].'&secretKey='.$this->_key);
        if($request->get('sign') != $sign) {
            return [
                'status' => '30003',
                'msg' => 'Signature error'
            ];
        }
        $mchOrderNo = substr($request->get('merchantTradeNo'), 2);
        $orderModel = new Order();
        $order = $orderModel->where('OrderNo', $mchOrderNo)->first();
        if(empty($order)) {
            return false;
        }
        if($order->status == 0) {
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
