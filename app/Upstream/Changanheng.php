<?php  namespace App\Upstream;
use App\Help\Sign;
use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Changanheng {
    private $_mchId = '40-182';
    private $_key = 'dc6c3f82101f48ff9dffc464dc41fea6';
    private $_url = 'http://ca.ttpay.top:8085/';
    public function order($request) {
        $parameters = [
            'mchId' => $this->_mchId,
            'productId' => $request['channelId'],
            'merchantOrderId' => 'YZ'.$request['orderNo'],
            'amount' => sprintf("%.2f", $request['amount']/100),
            'timestamp' => time(),
            'notifyUrl' => config('app.url').'/api/pay/changanheng/notify',
            'nonce' => md5(time().rand(1,999999))


        ];

        if(!empty($request['redirectUrl'])) {
            $parameters['returnUrl'] = $request['redirectUrl'];
        } else {
            $parameters['returnUrl'] = config('app.url').'/api/pay/changanheng/notify';
        }

        $signObj = new Sign();
        $sign = $signObj->brencode($parameters, $this->_key);
        $parameters['sign'] = $sign;
        $response = Http::post($this->_url.'paymentOrder/create', $parameters);
        $response = $response->json();
        if($response['code'] == 200) {
            return [
                'code' => 1,
                'data' => [
                    'qrImgUrl' => $response['data'],
                    'payOrderId' => 'YZ'.$request['orderNo'],
                    'qrUrl' => $response['data'],
                    'payUrl' => $response['data'],
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
        $sign = $signObj->brencode($req, $this->_key);

        if($request->get('sign') != $sign) {
            return [
                'status' => '30003',
                'msg' => 'Signature error'
            ];
        }
        $mchOrderNo = substr($request->get('merchantOrderId'), 2);
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
