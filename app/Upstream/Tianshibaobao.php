<?php  namespace App\Upstream;
use App\Help\Sign;
use App\Models\Order;
use Illuminate\Support\Facades\Http;

class Tianshibaobao {
    private $_mchId = '62f384c5e4b04241d796ecbf';
    private $_key = '31e7tbh7ktjh21sxrtc2nsynbkayc0kod6fi5ezh3veko78dow82ewmz78vft1tsf1qeimfmiwy7r466k7ddd40vbarxwc9i2x1p4jdc2x6q3ctd520gsyis2tqctbxv';
    private $_url = 'http://154.38.118.89/';
    public function order($request) {
        $parameters = [
            'mchNo' => 'M1660126405',
            'appId' => $this->_mchId,
            'client_id' => $this->_mchId,
            'mchOrderNo' => 'YZ'.$request['orderNo'],
            'wayCode' => $request['channelId'],
            'amount' => $request['amount'],
            'currency' => 'cny',
            'subject' => 'subject'.time(),
            'body' => 'product body',
            'notifyUrl' => config('app.url').'/api/pay/tianshibaobao/notify',
            'reqTime' => time(),
            'version' => '1.0'



        ];
        if(!empty($request['redirectUrl'])) {
            $parameters['redirectUrl'] = $request['redirectUrl'];
        } else {
            $parameters['redirectUrl'] = config('app.url').'/api/pay/tianshibaobao/notify';
        }
        $signObj = new Sign();
        $sign = $signObj->encode($parameters, $this->_key);
        $parameters['sign'] = $sign;
        $response = Http::asForm()->post($this->_url.'api/pay/unifiedOrder', $parameters);
        $response = $response->json();

        if($response['code'] == 0) {
            return [
                'code' => 1,
                'data' => [
                    'qrImgUrl' => $response['data']['payData'],
                    'payOrderId' => $response['data']['payOrderId'],
                    'qrUrl' => $response['data']['payData'],
                    'payUrl' => $response['data']['payData'],
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
        if($request->get('state') == 2 && $order->status == 0) {
            if(empty($request->get('amount')) || $request->get('amount') == 0) {
                return false;
            }
            $orderModel->where('OrderNo', $mchOrderNo)->update([
                'status' => 1,
                'pay_amount' => $request->get('total_fee'),
                'pay_time' => time(),
'created' => time()
            ]);
        }
        return $orderModel->where('id', $order->id)->first();
    }
}
