<?php  namespace App\Upstream;
use App\Help\Sign;
use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Dami {
    private $_mchId = '220598410';
    private $_key = 'lye2z3nmcehwn920kd6xqn4gbq9o1ks0';
    private $_url = 'http://xinaweb.xamhjx.com/';
    public function order($request) {
        $parameters = [
            'mch_id' => $this->_mchId,
            'ordersn' => 'YZ'.$request['orderNo'],
            'date' => date('Y-m-d H:i:s'),
            'paytype' => 'WXPAY',
            'typecode' => $request['channelId'],
            'notifyurl' => config('app.url').'/api/pay/dami/notify',
            'amount' => $request['amount']/100,
            'is_form' => 2,

        ];

        if(!empty($request['redirectUrl'])) {
            $parameters['callbackurl'] = $request['redirectUrl'];
        } else {
            $parameters['callbackurl'] = config('app.url').'/api/pay/dami/notify';
        }

        $signObj = new Sign();
        $sign = $signObj->encode($parameters, $this->_key);
        $parameters['sign'] = $sign;
        $response = Http::post($this->_url.'Pay_Index_create.gt', $parameters);
        $response = $response->json();
        if($response['code'] == 1) {
            return [
                'code' => 1,
                'data' => [
                    'qrImgUrl' => $response['url'],
                    'payOrderId' => 'YZ'.$request['orderNo'],
                    'qrUrl' => $response['url'],
                    'payUrl' => $response['url'],
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
        $sign = $signObj->makeSign($req, $this->_key);


        if($request->get('sign') != $sign) {
            return [
                'status' => '30003',
                'msg' => 'Signature error'
            ];
        }
        $mchOrderNo = substr($request->get('out_order_sn'), 2);
        $orderModel = new Order();
        $order = $orderModel->where('OrderNo', $mchOrderNo)->first();
        if(empty($order)) {
            return false;
        }
        if(strtoupper($request['status']) == 1 && $order->status == 0) {
            if(empty($request->get('total_fee')) || $request->get('total_fee') == 0) {
                return false;
            }
            $orderModel->where('OrderNo', $mchOrderNo)->update([
                'status' => 1,
                'pay_amount' => $request->get('total_fee')*100,
                'pay_time' => time(),
'created' => time()
            ]);
        }
        return $orderModel->where('id', $order->id)->first();
    }
}
