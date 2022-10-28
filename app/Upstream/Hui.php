<?php  namespace App\Upstream;
use App\Help\Sign;
use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Hui {
    private $_mchId = '2329930';
    private $_key = 'B9DF71CF71C85C8397BD5EE0540E0055';
    private $_url = 'http://139.155.11.150/';
    public function order($request) {



        $paykey = $this->_key;
        $url = 'http://139.155.11.150/api/pay';
        $parameter = array(
            "api_id" => $this->_mchId,
            "orderid" => 'YZ' . $request['orderNo'],
            "money" => $request['amount']/100,
            "notify_url" =>config('app.url').'/api/pay/hui/notify',
            "return_url" =>config('app.url').'/api/pay/hui/notify',
        );
        ksort($parameter);
        reset($parameter);
        $fieldString = [];
        foreach ($parameter as $key => $value) {
            if(!empty($value)){
                $fieldString[] = $key . "=" . $value . "";
            }
        }
        $fieldString = implode('&', $fieldString);

        $parameter['sign'] = strtoupper(md5($fieldString."&key=".$paykey));//md5加密
        $parameter['ip'] = '8.8.8.8';
//        $parameter['type'] = '支付编码';//可不填
//        $parameter['gtype'] = '业务编码';//可不填
        //post请求
        function send_post($url, $data) {

            $postdata = http_build_query($data);
            $options = array(
                'http' => array(
                    'method' => 'POST',
                    'header' => 'Content-type:application/x-www-form-urlencoded',
                    'content' => $postdata,
                    'timeout' => 15 * 60 // 超时时间（单位:s）
                )
            );
            $context = stream_context_create($options);
            $result = file_get_contents($url, false, $context);

            return $result;
        }
        $html = send_post($url,$parameter);
        $result = json_decode($html, true);




        if($result['code'] == '0') {
            return [
                'code' => 1,
                'data' => [
                    'qrImgUrl' => $result['payUrl'],
                    'payOrderId' => 'YZ'.$request['orderNo'],
                    'qrUrl' => $result['payUrl'],
                    'payUrl' => $result['payUrl'],
                    'channelId' => $request['channelId'],
                ]
            ];
        } else {
            return [
                'code' => 0,
                'data' => $parameter,
                'para' => $result,
            ];
        }





    }

    public function notify($request) {
        $parameter = [
            'api_id' => $_REQUEST['api_id'],//商户ID
            'orderid' => $_REQUEST["orderid"],//商户订单
            'api_orderid' => $_REQUEST["api_orderid"],//上游订单
            'money' => $_REQUEST["money"],//金额
            'notify_url' => $_REQUEST["notify_url"],//异步
            'return_url' => $_REQUEST["return_url"],//同步
        ];
        ksort($parameter);
        reset($parameter);
        $fieldString = [];
        foreach ($parameter as $key => $value) {
            if(!empty($value)){
                $fieldString[] = $key . "=" . $value . "";
            }
        }
        $fieldString = implode('&', $fieldString);
        $sign = strtoupper(md5($fieldString."&key=".$this->_key));
        if($request->get('sign') != $sign) {
            return [
                'status' => '30003',
                'msg' => 'Signature error'
            ];
        }
        $mchOrderNo = substr($request->get('orderid'), 2);
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
