<?php  namespace App\Upstream;
use App\Help\Broadcast;
use App\Help\Sign;
use App\Models\Order;
use Illuminate\Support\Facades\Http;

class Lufei {
    private $_mchId = '221053842';
    private $_key = 'YbDdfukSRxhRNzZx8ts9GWEC0d6hP78s';
    private $_url = 'https://tuochao.top/';
    public function order($request) {
        $parameters = [
            'pay_memberid' => $this->_mchId,
            'pay_orderid' => 'YZ'.$request['orderNo'],
            'pay_bankcode' => $request['channelId'],
            'pay_amount' => sprintf("%.2f", $request['amount']/100),
            'pay_notifyurl' => config('app.url').'/api/pay/lufei/notify',

        ];
        if(!empty($request['redirectUrl'])) {
            $parameters['pay_callbackurl'] = $request['redirectUrl'];
        } else {
            $parameters['pay_callbackurl'] = config('app.url').'/api/pay/lufei/notify';
        }

        $signObj = new Sign();
        $sign = $signObj->encode($parameters, $this->_key);
        $parameters['pay_md5sign'] = $sign;
        $response = Http::asForm()->post($this->_url.'Pay', $parameters);
        $response = $response->json();
        if($response['status'] == 200 && $response['type'] == 'url'  ) {
            return [
                'code' => 1,
                'data' => [
                    'qrImgUrl' => $response['data'],
                    'payOrderId' => 'YZ'.$request['orderNo'],
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
        $mchOrderNo = substr($request->get('orderid'), 2);
        $orderModel = new Order();
        $order = $orderModel->where('OrderNo', $mchOrderNo)->first();
        if(empty($order)) {
            return false;
        }
        if($request->get('returncode') == '00' && $order->status == 0) {
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
