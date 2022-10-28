<?php  namespace App\Upstream;
use App\Help\Sign;
use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Wubai2 {
    private $_mchId = 'RwnyzQZGxLuzDJMBnkzXS8LwVM';
    private $_key = '742lY7epeAiAwEPWazJQtXay30q7e3TQOQBJq3aQ';
    private $_url = 'https://merchant.500paypay.xyz/';
    public function order($request) {
        $parameters = [
            'Timestamp' => time(),
            'AccessKey' => $this->_mchId,
            'PayChannelId' => $request['channelId'],
            'OrderNo' => 'YZ'.$request['orderNo'],
            'Amount' => $request['amount']/100,
            'CallbackUrl' => config('app.url').'/api/pay/wubai2/notify',

        ];
        if(!empty($request['redirectUrl'])) {
            $parameters['ReturnUrl'] = $request['redirectUrl'];
        }
        $signObj = new Sign();

        $sign = $signObj->zhongyou($parameters, '&SecretKey='.$this->_key);
        $parameters['sign'] = $sign;
        $response = Http::post($this->_url.'api/PayV2/submit', $parameters);
        $response = $response->json();
        if($response['Code'] == '0') {
            return [
                'code' => 1,
                'data' => [
                    'qrImgUrl' => $response['Data']['PayeeInfo']['CashUrl'],
                    'payOrderId' => $response['Data']['OrderNo'],
                    'qrUrl' => $response['Data']['PayeeInfo']['CashUrl'],
                    'payUrl' => $response['Data']['PayeeInfo']['CashUrl'],
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
        $sign = $signObj->zhongyou($req, '&SecretKey='.$this->_key);
        if($request->get('sign') != $sign) {
            return [
                'status' => '30003',
                'msg' => 'Signature error'
            ];
        }
        $mchOrderNo = substr($request->get('OrderNo'), 2);
        $orderModel = new Order();
        $order = $orderModel->where('OrderNo', $mchOrderNo)->first();
        if(empty($order)) {
            return false;
        }
        if(strtoupper($request['Status']) == 4 && $order->status == 0) {
            if(empty($request->get('Amount')) || $request->get('Amount') == 0) {
                return false;
            }
            $orderModel->where('OrderNo', $mchOrderNo)->update([
                'status' => 1,
                'pay_amount' => $request->get('Amount')*100,
                'pay_time' => time(),
'created' => time()
            ]);

        }
        return $orderModel->where('id', $order->id)->first();
    }
}
