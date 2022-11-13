<?php  namespace App\Upstream;
use App\Help\Sign;
use App\Models\Order;
use Illuminate\Support\Facades\Http;

class Nike {
    private $_mchId = 'HQD6146';
    private $_key = 'DED0E8B87877C2E11D3DC4728F08EA66';
    private $_url = 'http://47.251.10.118:12580/';
    public function order($request) {
        $parameters = [
//            'amount'=> sprintf("%.2f", $request['amount']/100),
            'payment_type_code' => $request['channelId'],
            'merchant_order_no' => 'YZ' . $request['orderNo'],
            'product_name' => $request['subject'],
            'merchant_no' =>  $this->_mchId,
            'notify_url' => config('app.url').'/api/pay/nike/notify',



        ];
        $signObj = new Sign();
        $sign = $signObj->zhongyou($parameters, $this->_key);
        $parameters['sign'] = $sign;
        $response = Http::asForm()->post($this->_url.'api/cpi/order/anyamount_pay', $parameters);
        $response = $response->json();
        if($response['status'] == 200) {
            return [
                'code' => 1,
                'data' => [
                    'qrImgUrl' => $response['pay_link'],
                    'payOrderId' => $response['order_no'],
                    'qrUrl' => $response['pay_link'],
                    'payUrl' => $response['pay_link'],
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
        $sign = $signObj->zhongyou($req, $this->_key);
        if($request->get('sign') != $sign) {
            return [
                'status' => '30003',
                'msg' => 'Signature error'
            ];
        }
        $mchOrderNo = substr($request->get('merchant_order_no'), 2);
        $orderModel = new Order();
        $order = $orderModel->where('OrderNo', $mchOrderNo)->first();
        if(empty($order)) {
            return false;
        }
        if($request->get('status') == 1 && $order->status == 0) {
//            if(empty($request->get('actual_amount')) || $request->get('actual_amount') == 0) {
//                return false;
//            }
            $orderModel->where('OrderNo', $mchOrderNo)->update([
                'status' => 1,
                'pay_amount' => $request->get('actual_amount')*100,
                'pay_time' => time(),
'created' => time()
            ]);
        }
        return $orderModel->where('id', $order->id)->first();
    }
}
