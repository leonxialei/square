<?php  namespace App\Upstream;
use App\Help\Sign;
use App\Models\Order;
use Illuminate\Support\Facades\Http;

class Xgdsg {
    private $_mchId = '200057';
    private $_key = 'A7E437DA242741B6931351DDF2BECC1E';
    private $_url = 'http://sanf.njyidou.top/pay-api/';
    public function order($request) {
        $parameters = [
            'mchId' => $this->_mchId,
            'projectId' => $request['channelId'],
            'mchOrderNo' => 'YZ' . $request['orderNo'],
            'timestamp' => $this->getMillisecond(),


            'amount' => $request['amount'],

            'notifyUrl' => config('app.url').'/api/pay/xgdsg/notify',
//            'redirectUrl' => $request['redirectUrl'],
            'subject' => $request['subject'],
            'signType' => 'MD5'

        ];
        if(!empty($request['redirectUrl'])) {
            $parameters['returnUrl'] = $request['redirectUrl'];
        } else {
            $parameters['returnUrl'] = config('app.url').'/api/pay/xgdsg/notify';
        }

        $signObj = new Sign();
        $sign = $signObj->encode($parameters, $this->_key);
        $parameters['sign'] = $sign;
        $response = Http::asForm()->post($this->_url.'api/pay/create_order', $parameters);
        $response = $response->json();
        if($response['retCode'] == 'SUCCESS') {
            return [
                'code' => 1,
                'data' => [
                    'qrImgUrl' => $response['data'],
                    'payOrderId' => 'YZ' . $request['orderNo'],
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

    private function getMillisecond() {
        list($microsecond , $time) = explode(' ', microtime()); //' '中间是一个空格
        return (float)sprintf('%.0f',(floatval($microsecond)+floatval($time))*1000);
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
        if($request->get('status') == 1 && $order->status == 0) {
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
