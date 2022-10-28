<?php  namespace App\Upstream;
use App\Help\Sign;
use App\Models\Order;
use Illuminate\Support\Facades\Http;

class Hademen {
    private $_mchId = '发财1';
    private $_key = 'f7d76e3adde02184fa1178d71988830dcf729639';
    private $_url = 'https://www.xiguapay.xyz';
    public function order($request) {
        $parameters = [
            'mch_id' => $this->_mchId,
            'ptype' => $request['channelId'],
            'order_sn' => 'YZ' . $request['orderNo'],
            'money' => sprintf("%.2f", $request['amount']/100),
            'format' => 'json',
            'goods_desc' => NULL,
            'client_ip' => NULL,
            'notify_url' => config('app.url').'/api/pay/hademen/notify',
            'time' => time(),


        ];

        $signObj = new Sign();
        $sign = $signObj->hademen($parameters, $this->_key);
        $parameters['sign'] = $sign;
        $response = Http::asForm()->post($this->_url.'/?c=Pay', $parameters);
        $response = $response->json();

        if($response['code'] == 1) {
            return [
                'code' => 1,
                'data' => [
                    'qrImgUrl' => $response['data']['qrcode'],
                    'payOrderId' => 'YZ' . $request['orderNo'],
                    'qrUrl' => $response['data']['qrcode'],
                    'payUrl' => $this->_url.$response['data']['action_url'],
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
        $sign = $signObj->hademen($req, $this->_key);
        if($request->get('sign') != $sign) {
            return [
                'status' => '30003',
                'msg' => 'Signature error'
            ];
        }
        $mchOrderNo = substr($request->get('sh_order'), 2);
        $orderModel = new Order();
        $order = $orderModel->where('OrderNo', $mchOrderNo)->first();
        if(empty($order)) {
            return false;
        }
        if($request->get('status') == 'success' && $order->status == 0) {
            if(empty($request->get('money'))) {
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
