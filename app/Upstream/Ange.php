<?php  namespace App\Upstream;
use App\Help\Sign;
use App\Models\Order;
use Illuminate\Support\Facades\Http;

class Ange {
    private $_mchId = '20000384';
    private $_key = 'UVYS4FADMC4YGRPPIRJDXBIKPVIGVLK5A6OTSTMSWKWYKFR7RATUMK95GWKM0NYIGC3IMKBWEOMLGHPJ0URYAKAFAZ7Z9CPKBO7XNNIVHOQJMYGDAWCRAMUW8VCB0ITF';
    private $_url = 'http://111.68.7.34:9911/';
    public function order($request) {
        $parameters = [
            'mchId' => $this->_mchId,
            'productId' => $request['channelId'],
            'mchOrderNo' => 'YZ' . $request['orderNo'],
            'amount' => $request['amount'],
            'currency' => 'cny',
            'notifyUrl' => config('app.url').'/api/pay/ange/notify',
//            'redirectUrl' => $request['redirectUrl'],
            'subject' => $request['subject'],
            'body' => $request['body'],
            'reqTime' => date('YmdHis'),
            'version' => '1.0'

        ];
        if(!empty($request['redirectUrl'])) {
            $parameters['returnUrl'] = $request['redirectUrl'];
        } else {
            $parameters['returnUrl'] = config('app.url').'/api/pay/ange/notify';
        }

        $signObj = new Sign();
        $sign = $signObj->encode($parameters, $this->_key);
        $parameters['sign'] = $sign;
        $response = Http::asForm()->post($this->_url.'api/pay/create_order', $parameters);
        $response = $response->json();
        if($response['retCode'] == '0') {
            return [
                'code' => 1,
                'data' => [
                    'qrImgUrl' => $response['payUrl'],
                    'payOrderId' => 'YZ' . $request['orderNo'],
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
        $mchOrderNo = substr($request->get('mchOrderNo'), 2);
        $orderModel = new Order();
        $order = $orderModel->where('OrderNo', $mchOrderNo)->first();
        if(empty($order)) {
            return false;
        }
        if($request->get('status') == 2 && $order->status == 0) {
            if(empty($request->get('amount')) || $request->get('amount') == 0) {
                return false;
            }
            $orderModel->where('OrderNo', $mchOrderNo)->update([
                'status' => 1,
                'pay_amount' => $request->get('amount'),
                'pay_time' => time(),
                'created' => time()
            ]);
        }
        return $orderModel->where('id', $order->id)->first();
    }
}
