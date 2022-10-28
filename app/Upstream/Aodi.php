<?php  namespace App\Upstream;
use App\Help\Sign;
use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Aodi {
    private $_mchId = '220585088';
    private $_key = 'smos3ibx2bstmzyvuaxfce5eshsznhyh';
    private $_url = 'http://47.242.112.103:808/';
    public function order($request) {
        $parameters = [
            'mid' => $this->_mchId,
            'orderid' => 'YZ'.$request['orderNo'],
            'amount' => sprintf("%.2f", $request['amount']/100),
            'timestamp' => time(),
            'paytype' => $request['channelId'],
            'notifyurl' => config('app.url').'/api/pay/aodi/notify',
            'callbackurl' => config('app.url').'/api/pay/aodi/notify',


        ];

        ksort($parameters);
        $md5str = "";
        foreach ($parameters as $key => $val) {
            if (!empty($val)) {
                $md5str = $md5str . $key . "=" . $val . "&";
            }
        }
        $sign = md5($md5str . "key=" . $this->_key);
        $parameters['sign'] = $sign;
        $parameters['remark'] = 'JSON';
        $parameters['pay_isJson'] = 1;
        $response = Http::asForm()->post($this->_url.'Order/Index/add', $parameters);
        $response = $response->json();
        if($response['code'] == 200) {
            return [
                'code' => 1,
                'data' => [
                    'qrImgUrl' => $response['pay_url'],
                    'payOrderId' => 'YZ'.$request['orderNo'],
                    'qrUrl' => $response['pay_url'],
                    'payUrl' => $response['pay_url'],
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
        $ReturnArray = array( // 返回字段
            "mid" => $request->get('mid'), // 商户ID
            "orderid" =>  $request->get('orderid'),
            "amount" =>  $request->get('amount'), // 交易金额
            'ordernumber' => $request->get('ordernumber'),
            "datetime" =>  $request->get('datetime'),
            "code" => $request->get('code'),
        );
        ksort($ReturnArray);
        $md5str = "";
        foreach ($ReturnArray as $key => $val) {
            if (!empty($val)) {
                $md5str = $md5str . $key . "=" . $val . "&";
            }
        }
        $sign = md5($md5str . "key=" . $this->_key);

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
        if(strtoupper($request['code']) == 1 && $order->status == 0) {
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
}
