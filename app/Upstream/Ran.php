<?php  namespace App\Upstream;
use App\Help\Sign;
use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Ran {
    private $_mchId = '10013';
    private $_key = '44a47dcd93be32808b440f05c6c10ac7';
    private $_url = 'http://211.149.151.131:8585/';
    public function order($request) {
        $parameters = [
            'merchant_no' => $this->_mchId,
            'out_order_no' => 'YZ'.$request['orderNo'],
            'amount' => $request['amount']/100,
            'pay_type' => $request['channelId'],
            'notify_url' => config('app.url').'/api/pay/ran/notify',



        ];

        $sign = md5($this->_mchId.$parameters['out_order_no'].$parameters['amount'].
            $request['channelId'].$parameters['notify_url'].$this->_key);

        $parameters['sign'] = $sign;
        $response = Http::asForm()->post($this->_url.'api/pay', $parameters);
        $response = $response->json();

        if(strtoupper($response['code']) == 1) {
            return [
                'code' => 1,
                'data' => [
                    'qrImgUrl' => $response['data']['pay_url'],
                    'payOrderId' => $response['data']['order_no'],
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


        $sign = md5($request['order_no'].$request['merchant_no'].$request['out_order_no'].
            $request['amount'].$request['pay_type'].$request['code'].$this->_key);

        if($request->get('sign') != $sign) {
            return [
                'status' => '30003',
                'msg' => 'Signature error'
            ];
        }
        $mchOrderNo = substr($request->get('out_order_no'), 2);
        $orderModel = new Order();
        $order = $orderModel->where('OrderNo', $mchOrderNo)->first();
        if(empty($order)) {
            return false;
        }
        if(strtoupper($request['code']) == 1 && $order->status == 0) {
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
