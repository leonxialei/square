<?php  namespace App\Upstream;
use App\Help\Sign;
use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Shengshi {
    private $_mchId = '63816930';
    private $_key = 'd1fb17ac381183da1836f8a0d14f725d';
    private $_url = 'http://api.shengshihuihuang.top/';
    public function order($request) {
        $parameters = [
            'key' => $this->_mchId.$this->_key,
            'pay_code' => $request['channelId'],
            'order_amount' => sprintf("%.2f", $request['amount']/100),
            'order_no' => 'YZ'.$request['orderNo'],
            'ts' => $this->getMillisecond()


        ];
        if(!empty($parameters)){
            $p =  ksort($parameters);
            if($p){
                $str = '';
                foreach ($parameters as $k=>$val){
                    if($val != ''){
                        $str .= $k .'=' . $val . '&';
                    }
                }
                $strs = rtrim($str, '&');
            }
        }

        $sign = md5($strs);
        $parameters['sign'] = $sign;
        $parameters['merchant_no'] = $this->_mchId;
        $parameters['callback_url'] = config('app.url').'/api/pay/shengshi/notify';
        $parameters['callback_type'] = 'POST';
        $response = Http::post($this->_url.'v1/order/create', $parameters);
        $response = $response->json();
        if($response['code'] == 200) {
            return [
                'code' => 1,
                'data' => [
                    'qrImgUrl' => $response['data']['pay_url'],
                    'payOrderId' => $response['data']['order_no'],
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
        $parameters = [
            'key' => $this->_mchId.$this->_key,
            'order_amount' =>$request->get('order_amount'),
            'order_no' =>$request->get('order_no'),
            'pay_code' => $request->get('pay_code')
        ];
        if(!empty($parameters)){
            $p =  ksort($parameters);
            if($p){
                $str = '';
                foreach ($parameters as $k=>$val){
                    if($val != ''){
                        $str .= $k .'=' . $val . '&';
                    }
                }
                $strs = rtrim($str, '&');
            }
        }

        $sign = md5($strs);
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
        if($order->status == 0) {
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
