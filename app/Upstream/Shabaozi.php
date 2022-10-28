<?php  namespace App\Upstream;
use App\Help\Sign;
use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Shabaozi {
    private $_mchId = '8035';
    private $_key = 'M8neh0JfZaV1Z1X3SaH5J8p8S1z9E52fNdT4gg4fMdT4Y159M1j9E636M6z6Y357';
    private $_url = 'http://api.huichen66.xyz/';
    public function order($request) {
        $parameters = [
            'number' => $this->_mchId,
//            'ip' => $request['clientIp'],
            'order' => 'YZ'.$request['orderNo'],
            'money' => sprintf("%.2f", $request['amount']/100),
            'otifyUrl' => config('app.url').'/api/pay/shabaozi/notify',
        ];


        if(!empty($request['redirectUrl'])) {
            $parameters['returnUrl'] = $request['redirectUrl'];
        } else {
            $parameters['returnUrl'] = config('app.url').'/api/pay/shabaozi/notify';
        }
//        echo 'number='.$parameters['number'].'&order='.$parameters['order'].'&money='.
//            $parameters['money'].'&otifyUrl='.$parameters['otifyUrl'].'&returnUrl='.
//            $parameters['returnUrl'].'&'.$this->_key.'@@';
        $sign = md5('number='.$parameters['number'].'&order='.$parameters['order'].'&money='.
            $parameters['money'].'&otifyUrl='.$parameters['otifyUrl'].'&returnUrl='.
            $parameters['returnUrl'].'&'.$this->_key);
//        echo $sign;
        $parameters['sign'] = $sign;

        $response = Http::asForm()->post($this->_url.'pay/index.php', $parameters);
        $response = $response->json();
//        print_r($response);die;
        if($response['success'] == '请求成功') {
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
//
//        echo 'number='.$this->_mchId.'&order='.$request->get('order').'&money='.
//            $request->get('money').'&otifyUrl='.$request->get('otifyUrl').'&returnUrl='.
//            $request->get('returnUrl').'&'.$this->_key;die;

        $sign = md5('number='.$this->_mchId.'&order='.$request->get('order').'&money='.
            $request->get('money').'&otifyUrl='.$request->get('otifyUrl').'&returnUrl='.
            $request->get('returnUrl').'&'.$this->_key);
        if($request->get('sign') != $sign) {
            return [
                'status' => '30003',
                'msg' => 'Signature error'
            ];
        }
        $mchOrderNo = substr($request->get('order'), 2);
        $orderModel = new Order();
        $order = $orderModel->where('OrderNo', $mchOrderNo)->first();
        if(empty($order)) {
            return false;
        }
        if($order->status == 0) {
            if(empty($request->get('money')) || $request->get('money') == 0) {
                return false;
            }
            $orderModel->where('OrderNo', $mchOrderNo)->update([
                'status' => 1,
                'pay_amount' => $request->get('money')*100,
                'pay_time' => time(),
'created' => time()
            ]);
        }
        return $orderModel->where('id', $order->id)->first();
    }
}
