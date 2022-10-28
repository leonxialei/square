<?php  namespace App\Upstream;
use App\Help\Sign;
use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Abc {
    private $_mchId = '100003';
    private $_key = 'CDtdvrdxYcxZzpMyEFYtUCZWDqkC97pP8AZKKBhrUvgsMeSZzKR3hcEl6DiOzmCZ';
    private $_url = 'https://www.acpay.co/';
    public function order($request) {
        $parameters = [
            'mch_id' => $this->_mchId,
            'out_trade_no' => 'YZ'.$request['orderNo'],
            'total' => sprintf("%.2f",$request['amount']/100),
            'request_token' => 'GI8y1O1NN4g7AX0Uw7q4Ok4BpiC4xJYV',
            'timestamp' => time(),
            'type' => $request['channelId'],
            'notify_url' => config('app.url').'/api/pay/abc/notify',



        ];

        if(!empty($request['redirectUrl'])) {
            $parameters['return_url'] = $request['redirectUrl'];
        } else {
            $parameters['return_url'] = config('app.url').'/api/pay/ak/notify';
        }
        $signObj = new Sign();
        $sign = $signObj->encode($parameters, $this->_key);
        $parameters['sign'] = $sign;
        $parameters['attach'] = 'json';
        $response = Http::asForm()->post($this->_url.'api/order', $parameters);
//        $response = $response->json();
        dd($response->body());
        if($response['status'] == 10000) {
            return [
                'code' => 1,
                'data' => [
                    'qrImgUrl' => $response['data']['h5_url'],
                    'payOrderId' => 'YZ'.$request['orderNo'],
                    'qrUrl' => $response['data']['h5_url'],
                    'payUrl' => $response['data']['h5_url'],
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
        $mchOrderNo = substr($request->get('out_trade_no'), 2);
        $orderModel = new Order();
        $order = $orderModel->where('OrderNo', $mchOrderNo)->first();
        if(empty($order)) {
            return false;
        }
        if(($request['callbacks'] == 'CODE_SUCCESS') && $order->status == 0) {
            if(empty($request->get('total')) || $request->get('total') == 0) {
                return false;
            }
            $orderModel->where('OrderNo', $mchOrderNo)->update([
                'status' => 1,
                'pay_amount' => $request->get('total')*100,
                'pay_time' => time(),
'created' => time()
            ]);
        }
        return $orderModel->where('id', $order->id)->first();
    }
}
