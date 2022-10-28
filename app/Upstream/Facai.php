<?php  namespace App\Upstream;
use App\Help\Sign;
use App\Models\Order;
use Illuminate\Support\Facades\Http;

class Facai {
    private $_mchId = '1036';
    private $_key = 'c086b5a9efe34a868778ee21234255b0';
    private $_url = 'http://137.220.176.49:1778/';
    public function order($request) {
        $parameters = [
            'mch_id' => $this->_mchId,
            'pass_code' => $request['channelId'],
            'subject' => $request['subject'],
            'out_trade_no' => 'YZ'.$request['orderNo'],
            'amount' => $request['amount']/100,
            'client_ip' => $request['clientIp'],
            'notify_url' => config('app.url') . '/api/pay/facai/notify',
            'timestamp' => date('Y-m-d H:i:s'),

        ];
        if($request['channelId'] == 105 && ($request['amount']/100 < 1 || $request['amount']/100 > 300)) {
            print_r([
                'status' => '30005',
                'msg' => 'Recharge amount is incorrect'
            ]);
            die;
        }
        $signObj = new Sign();
        $sign = $signObj->brencode($parameters, $this->_key);
        $parameters['sign'] = $sign;
        $response = Http::post($this->_url.'api/unifiedorder', $parameters);
        $response = $response->json();

        if($response['code'] == 0) {
            return [
                'code' => 1,
                'data' => [
                    'qrImgUrl' => isset($response['data']['qrcode_content'])?$response['data']['qrcode_content']:'',
                    'payOrderId' => $response['data']['trade_no'],
                    'qrUrl' => isset($response['data']['qrcode_url'])?$response['data']['qrcode_url']:'',
                    'payUrl' => isset($response['data']['pay_url'])?$response['data']['pay_url']:'',
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
        $sign = $signObj->brencode($req, $this->_key);
        if($request->get('sign') != $sign) {
            return [
                'status' => '30003',
                'msg' => 'Signature error'
            ];
        }
        $mchOrderNo = substr($request->get('out_trade_no'), 2);
        $orderModel = new Order();
        $order = $orderModel->where('OrderNo', $mchOrderNo)->first();
        if(empty($order)) {
            return false;
        }
        if($request->get('status') == 2 && $order->status == 0) {
            if(empty($request->get('money')) || $request->get('money') == 0
            || $request->get('money')*100 < $order->original_amount) {
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
