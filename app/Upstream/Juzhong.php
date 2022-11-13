<?php  namespace App\Upstream;
use App\Help\Sign;
use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Juzhong {
    private $_mchId = 'pgswc7yu';
    private $_key = '9e39062d23914926';
    private $_url = 'http://115.126.98.205:12345/';
    public function order($request) {
        $a = rand(1,200);
        $b = rand(1,200);
        $c = rand(1,200);
        $d = rand(1,200);
        $parameters = [
            'version' => '1.0',
            'partnerid' => $this->_mchId,
            'orderid' => 'YZ'.$request['orderNo'],
            'payamount' => $request['amount'],
            'payip' => $a.'.'.$b.'.'.$c.'.'.$d,
            'notifyurl' => config('app.url').'/api/pay/juzhong/notify',
            'paytype' => $request['channelId'],



        ];
        if(!empty($request['redirectUrl'])) {
            $parameters['returnurl'] = $request['redirectUrl'];
        } else {
            $parameters['returnurl'] = config('app.url').'/api/pay/juzhong/notify';
        }
        $signObj = new Sign();
        $sign = $signObj->smencode($parameters, $this->_key);
        $parameters['sign'] = $sign;
        $response = Http::asForm()->post($this->_url.'trade/pay', $parameters);
        $response = $response->json();
        if($response['code'] == '0') {
            return [
                'code' => 1,
                'data' => [
                    'qrImgUrl' => $response['payurl'],
                    'payOrderId' => $response['orderid'],
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
        $sign = $signObj->smencode($req, $this->_key);
        if($request->get('sign') != $sign) {
            return [
                'status' => '30003',
                'msg' => 'Signature error'
            ];
        }
        $mchOrderNo = substr($request->get('partnerorderid'), 2);
        $orderModel = new Order();
        $order = $orderModel->where('OrderNo', $mchOrderNo)->first();
        if(empty($order)) {
            return false;
        }
        if((strtoupper($request['orderstatus']) == 1 || strtoupper($request['orderstatus']) == 4) && $order->status == 0) {
            if(empty($request->get('payamount')) || $request->get('payamount') == 0) {
                return false;
            }
            $orderModel->where('OrderNo', $mchOrderNo)->update([
                'status' => 1,
                'pay_amount' => $request->get('payamount'),
                'pay_time' => time(),
                'created' => time()
            ]);
        }
        return $orderModel->where('id', $order->id)->first();
    }
}
