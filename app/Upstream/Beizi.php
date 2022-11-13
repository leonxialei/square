<?php  namespace App\Upstream;
use App\Help\Sign;
use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Beizi {
    private $_mchId = '75319693';
    private $_key = '5a092f3b1868bf34f63a26d7fb98b984';
    private $_url = 'http://api.ogpayone.com/';
    public function order($request) {
        $parameters = [
            'merchant_no' => $this->_mchId,
            'pay_code' => $request['channelId'],
            'order_amount' => sprintf("%.2f", $request['amount']/100),
            'order_no' => 'YZ'.$request['orderNo'],
            'callback_url' => config('app.url').'/api/pay/beizi/notify',
            'ts' => $this->getMillisecond()


        ];

        $signObj = new Sign();
        $sign = $signObj->smencode($parameters, $this->_key);
        $parameters['sign'] = $sign;
        $response = Http::post($this->_url.'v1/order/create', $parameters);
        $response = $response->json();
        if($response['code'] == 200) {
            return [
                'code' => 1,
                'data' => [
                    'qrImgUrl' => $response['data']['pay_url'],
                    'payOrderId' => $response['data']['platform_no'],
                    'qrUrl' => $response['data']['qr_code'],
                    'payUrl' => $response['data']['pay_url'],
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
        unset($req['platform_no']);
        $signObj = new sign();
        $sign = $signObj->smencode($req, $this->_key);


        if($request->get('sign') != $sign) {
            return [
                'status' => '30003',
                'msg' => 'Signature error'
            ];
        }
        $mchOrderNo = substr($request->get('order_no'), 2);
        $orderModel = new Order();
        $order = $orderModel->where('OrderNo', $mchOrderNo)->first();
        if(empty($order)) {
            return false;
        }
        if(strtoupper($request['status']) == 6 && $order->status == 0) {
            if(empty($request->get('order_amount')) || $request->get('order_amount') == 0) {
                return false;
            }
            $orderModel->where('OrderNo', $mchOrderNo)->update([
                'status' => 1,
                'pay_amount' => $request->get('order_amount')*100,
                'pay_time' => time(),
                'created' => time()
            ]);
        }
        return $orderModel->where('id', $order->id)->first();
    }


    private function getMillisecond() {
        list($microsecond , $time) = explode(' ', microtime()); //' '中间是一个空格
        return (float)sprintf('%.0f',(floatval($microsecond)+floatval($time))*1000);
    }
}
