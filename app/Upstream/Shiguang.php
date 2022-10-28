<?php  namespace App\Upstream;
use App\Help\Sign;
use App\Models\Order;
use Illuminate\Support\Facades\Http;

class Shiguang {
    private $_mchId = '10035';
    private $_key = 'WOTGAK7EG5DSVPV4YYBJD76S85KNDO3BQFNOFICRGW09OBRYVMQQRDMRPDGRKPZVBVK2DDRRESP9KTGNSUXVOP5KLM2CKVL84ETTQ5IXTXTUG0GR0DOMPK3O53KNFEBC';
    private $_url = 'http://pay.sgzf.one/';
    public function order($request) {
        $parameters = [
            'mchId' => $this->_mchId,
            'mchOrderNo' => 'YZ'.$request['orderNo'],
            'amount' => $request['amount'],
            'productId' => $request['channelId'],
            'notifyUrl' => config('app.url').'/api/pay/shiguang/notify',

            'clientIp' => $request['clientIp'],

        ];

        $signObj = new Sign();
        $sign = $signObj->encode($parameters, $this->_key);
        $parameters['sign'] = $sign;
        $response = Http::asForm()->post($this->_url.'api/pay/create_order', $parameters);
        $response = $response->json();

        if($response['retCode'] == 'SUCCESS') {
            return [
                'code' => 1,
                'data' => [
                    'qrImgUrl' => $response['payParams']['payUrl'],
                    'payOrderId' => $request['payOrderId'],
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
                'status' => '600',
                'msg' => '签名错误'
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
