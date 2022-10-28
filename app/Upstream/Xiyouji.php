<?php  namespace App\Upstream;
use App\Help\Sign;
use App\Models\Order;
use Illuminate\Support\Facades\Http;

class Xiyouji {
    private $_mchId = '10000035';
    private $_key = '6dd0ebf69e001f558a165173b19aa308';
    private $_url = 'http://34.92.147.35:3020/';
    public function order($request) {
        $parameters = [
            'mchId' => $this->_mchId,
            'mchOrderNo' => 'YZ'.$request['orderNo'],
            'amount' => $request['amount'],
            'channelId' => $request['channelId'],
            'notifyUrl' => config('app.url').'/api/pay/notify',
            'subject' => $request['subject'],
            'body' => $request['body'],
            'clientIp' => $request['clientIp'],

        ];

        $signObj = new Sign();
        $sign = $signObj->encode($parameters, $this->_key);
        $parameters['sign'] = $sign;
        $response = Http::post($this->_url.'api/pay/create_order', $parameters);
        $response = $response->json();

        if($response['status'] == 200) {
            return [
                'code' => 1,
                'data' => [
                    'qrImgUrl' => $response['qrImgUrl'],
                    'payOrderId' => $request['mchOrderNo'],
                    'qrUrl' => $response['qrUrl'],
                    'payUrl' => $response['payUrl'],
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
                'status' => '600',
                'msg' => '签名错误'
            ];
        }
        $mchOrderNo = $request->get('mchOrderNo');
        $orderModel = new Order();
        $order = $orderModel->where('mchOrderNo', $mchOrderNo)->first();
        if(empty($order)) {
            return false;
        }
        if($request->get('status') == 2 && $order->status == 0) {
            $orderModel->where('mchOrderNo', $mchOrderNo)->update([
                'status' => 1,
                'pay_amount' => $request->get('total_fee'),
                'pay_time' => time(),
'created' => time()
            ]);
        }
        return $orderModel->where('id', $order->id)->first();
    }
}
