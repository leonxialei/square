<?php  namespace App\Upstream;
use App\Help\Sign;
use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Suibianjiaoyige {
    private $_mchId = '10022';
    private $_key = '0DCNVRURC924H9CJH6XLFCPJN78VFAITCTWZZZF1KJQDGLCZBAXP7AI1RV0LCPOY0ULIGVP5YQGOBGV10V9DGSRFYFYXLMGS3SH5ZRFU3B87P5DWAJPQGXWRG1RRZPVI';
    private $_url = 'http://pay.jojof.club/';
    public function order($request) {
        $parameters = [
            'mchId' => $this->_mchId,
            'productId' => $request['channelId'],
            'mchOrderNo' => 'YZ'.$request['orderNo'],
            'amount' => $request['amount'],
            'notifyUrl' => config('app.url').'/api/pay/sbjyg/notify',


        ];
        if(!empty($request['redirectUrl'])) {
            $parameters['returnUrl'] = $request['redirectUrl'];
        }
        $signObj = new Sign();
        $sign = $signObj->encode($parameters, $this->_key);
        $parameters['sign'] = $sign;
        $response = Http::asForm()->post($this->_url.'api/pay/create_order', $parameters);
        $response = $response->json();

        if(strtoupper($response['retCode']) == 'SUCCESS') {
            return [
                'code' => 1,
                'data' => [
                    'qrImgUrl' => $response['payParams']['payUrl'],
                    'payOrderId' => $response['payOrderId'],
                    'qrUrl' => $response['payParams']['payUrl'],
                    'payUrl' => $response['payParams']['payUrl'],
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
        if(strtoupper($request['status']) == 2 && $order->status == 0) {
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
