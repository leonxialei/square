<?php  namespace App\Upstream;
use App\Help\Sign;
use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Fczf {
    private $_mchId = '9000025';
    private $_key = 'f24d8af89bd907a8e012b46116340e15';
    private $_url = 'http://222.211.72.25:5173/';
    public function order($request) {
        $parameters = [
            'mchid' => $this->_mchId,
            'mch_order_id' => 'YZ'.$request['orderNo'],
            'paytype' => $request['channelId'],
            'price' => $request['amount']/100,
            'notify' => config('app.url').'/api/pay/fczf/notify',
            'time' => time(),
            'rand' => rand(100000, 999999)


        ];

        $sign = md5($this->_mchId.$parameters['mch_order_id'].$parameters['paytype'].
            $request['price'].$parameters['notify'].$parameters['time'].$parameters['rand']
            .$this->_key);

        $parameters['sign'] = $sign;
        $response = Http::asForm()->post($this->_url.'api/pay', $parameters);
        $response = $response->json();

        if(strtoupper($response['code']) == 0) {
            return [
                'code' => 1,
                'data' => [
                    'qrImgUrl' => $response['data']['url'],
                    'payOrderId' => $response['data']['order_id'],
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


        $sign = md5($request['mchid'].$request['mch_order_id'].$request['pt_order_id'].
            $request['price'].$request['paytype'].$request['status'].$this->_key);

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
