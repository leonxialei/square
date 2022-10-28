<?php  namespace App\Upstream;
use App\Help\Sign;
use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Boxin {
    private $_mchId = 'MER20221017194406855654';
    private $_key = '3faa430bc93c4e008fba252a4173484b';
    private $_url = 'http://api.boxinpower.com/';
    public function order($request) {
        $parameters = [
            'p1_merchantno' => $this->_mchId,
            'p2_amount' => sprintf("%.2f", $request['amount']/100),
            'p3_orderno' => 'YZ'.$request['orderNo'],
            'p4_paytype' => $request['channelId'],
            'p5_reqtime' => date('YmdHis'),
            'p6_goodsname' => $request['subject'],
            'p9_callbackurl' => config('app.url').'/api/pay/boxin/notify',



        ];

        if(!empty($request['redirectUrl'])) {
            $parameters['p8_returnurl'] = $request['redirectUrl'];
        } else {
            $parameters['p8_returnurl'] = config('app.url').'/api/pay/boxin/notify';
        }

        $signObj = new Sign();
        $sign = $signObj->encode($parameters, $this->_key);

        $parameters['sign'] = $sign;
        $response = Http::asForm()->post($this->_url.'pay', $parameters);
        $response = $response->json();

        if(strtoupper($response['rspcode']) == 'A0') {
            return [
                'code' => 1,
                'data' => [
                    'qrImgUrl' => $response['data'],
                    'payOrderId' =>'YZ'.$request['orderNo'],
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
        $sign = $signObj->encode($req, $this->_key);
        if($request->get('sign') != $sign) {
            return [
                'status' => '30003',
                'msg' => 'Signature error'
            ];
        }
        $mchOrderNo = substr($request->get('p3_orderno'), 2);
        $orderModel = new Order();
        $order = $orderModel->where('OrderNo', $mchOrderNo)->first();
        if(empty($order)) {
            return false;
        }
        if(strtoupper($request['p4_status']) == 2 && $order->status == 0) {
            if(empty($request->get('p2_amount')) || $request->get('p2_amount') == 0) {
                return false;
            }
            $orderModel->where('OrderNo', $mchOrderNo)->update([
                'status' => 1,
                'pay_amount' => $request->get('p2_amount')*100,
                'pay_time' => time(),
                'pay_time' => time()
            ]);
        }
        return $orderModel->where('id', $order->id)->first();
    }
}
