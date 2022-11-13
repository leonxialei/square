<?php  namespace App\Upstream;
use App\Help\Sign;
use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Tianyi {
    private $_mchId = '81016';
    private $_key = '00QCUB4OAU07HILM0GW7AS8EO0JCC0INMIGT22QF0CDJ21N5ODW9Q83FA9KKTV78QD0NAKHF45BE1S7RQ3B9LMA0O1QL1XWARG8RAANVSNCCF30VYIIZVIR3JVFKG11S';
    private $_url = 'http://www.tianyizhifupay.com/';
    public function order($request) {
        $parameters = [
            'requestNo' => 'YZ'.$request['orderNo'],
            'amount' =>  sprintf("%.2f", $request['amount']/100),
            'callBackURL' =>  config('app.url').'/api/pay/tianyi/notify',
            'redirectUrl' =>  config('app.url').'/api/pay/tianyi/notify',
            'userId' => $this->_mchId,
            'type' => $request['channelId'],

        ];

        $signObj = new Sign();
        $sign = $signObj->zhongyou($parameters, $this->_key);
        $parameters['sign'] = $sign;
        $parameters['return_type'] = 'json';
        $response = Http::asForm()->post($this->_url.'api/pay1/zfadd', $parameters);
        $response = $response->json();
        if($response['status'] == '0000') {
            return [
                'code' => 1,
                'data' => [
                    'qrImgUrl' => $response['data']['url'],
                    'payOrderId' => 'YZ'.$request['orderNo'],
                    'qrUrl' => $response['data']['url'],
                    'payUrl' => $response['data']['url'],
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
        if(isset($req['body'])) {
            unset($req['body']);
        }

        $signObj = new sign();
        $sign = $signObj->zhongyou($req, $this->_key);


        if($request->get('sign') != $sign) {
            return [
                'status' => '30003',
                'msg' => 'Signature error'
            ];
        }
        $mchOrderNo = substr($request->get('requestNo'), 2);
        $orderModel = new Order();
        $order = $orderModel->where('OrderNo', $mchOrderNo)->first();
        if(empty($order)) {
            return false;
        }
        if(strtoupper($request['status']) == '0000' && $order->status == 0) {
            if(empty($request->get('amount')) || $request->get('amount') == 0) {
                return false;
            }
            $orderModel->where('OrderNo', $mchOrderNo)->update([
                'status' => 1,
                'pay_amount' => $request->get('amount')*100,
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
