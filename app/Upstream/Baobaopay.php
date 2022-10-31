<?php  namespace App\Upstream;
use App\Help\Broadcast;
use App\Help\Sign;
use App\Models\Order;
use Illuminate\Support\Facades\Http;

class Baobaopay {
    private $_mchId = '221056427';
    private $_key = 'l4m2k6q92yjbkg4z2vyxfwkrnr8vwhzc';
    private $_url = 'https://bbpay.howersj.com/';
    public function order($request) {
        $parameters = [
            'pay_memberid' => $this->_mchId,
            'pay_orderid' => 'YZ'.$request['orderNo'],
            'pay_bankcode' => $request['channelId'],
            'pay_applydate' => date('Y-m-d Hi:s'),
            'pay_amount' => sprintf("%.2f", $request['amount']/100),
            'pay_notifyurl' => config('app.url').'/api/pay/baobaopay/notify',

        ];
        if(!empty($request['redirectUrl'])) {
            $parameters['pay_callbackurl'] = $request['redirectUrl'];
        } else {
            $parameters['pay_callbackurl'] = config('app.url').'/api/pay/baobaopay/notify';
        }

        $signObj = new Sign();
        $sign = $signObj->encode($parameters, $this->_key);
        $parameters['pay_md5sign'] = $sign;
        $response = Http::asForm()->post($this->_url.'Pay_index.html', $parameters);
        $response = $response->json();
        if($response['code'] == 200 ) {
            return [
                'code' => 1,
                'data' => [
                    'qrImgUrl' => $response['payurl'],
                    'payOrderId' => 'YZ'.$request['orderNo'],
                    'qrUrl' => $response['payurl'],
                    'payUrl' => $response['payurl'],
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
        if($request->get('returncode') == '00' && $order->status == 0) {
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
