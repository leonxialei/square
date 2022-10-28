<?php  namespace App\Upstream;
use App\Help\Sign;
use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Xinhuafei {
    private $_mchId = '12445155';
    private $_key = '2457Bh37xpZxweHsMbTmPCaX4Ynas85B';
    private $_url = 'http://103.239.247.195:2020/';
    public function order($request) {
        $parameters = [
            'mchid' => $this->_mchId,
            'mch_order_id' => 'YZ'.$request['orderNo'],
            'price' => $request['amount']/100,
            'paytype' => $request['channelId'],
            'notify' => config('app.url').'/api/pay/xinhuafei/notify',
            'time' => time(),
            'rand' => rand(100000,999999),


        ];
        $sign = md5($parameters['mchid'].$parameters['mch_order_id'].$parameters['price'].
            $parameters['paytype'].$parameters['notify'].$parameters['time'].$parameters['rand'].$this->_key);
        $parameters['sign'] = $sign;
        $response = Http::asForm()->post($this->_url.'sakura/mchbapi/pay3.php', $parameters);
        $response = $response->json();
        if($response['code'] == '0') {
            return [
                'code' => 1,
                'data' => [
                    'qrImgUrl' => $response['data']['url'],
                    'payOrderId' => $response['data']['pt_order_id'],
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
        $sign = md5($req['mchid'].$req['mch_order_id'].$req['price'].
            $req['paytype'].$req['status'].$this->_key);
        if($request->get('sign') != $sign) {
            return [
                'status' => '30003',
                'msg' => 'Signature error'
            ];
        }
        $mchOrderNo = substr($request->get('mch_order_id'), 2);
        $orderModel = new Order();
        $order = $orderModel->where('OrderNo', $mchOrderNo)->first();
        if(empty($order)) {
            return false;
        }
        if(strtoupper($request['status']) == 1 && $order->status == 0) {
            if(empty($request->get('price')) || $request->get('price') == 0) {
                return false;
            }
            $orderModel->where('OrderNo', $mchOrderNo)->update([
                'status' => 1,
                'pay_amount' => $request->get('price')*100,
                'pay_time' => time(),
'created' => time()
            ]);
        }
        return $orderModel->where('id', $order->id)->first();
    }
}
