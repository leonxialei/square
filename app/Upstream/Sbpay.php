<?php  namespace App\Upstream;
use App\Help\Sign;
use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Sbpay {
    private $_mchId = '6719';
    private $_key = 'RdzQBmCNNsUmtzt8ZZnNBzpjThzmEm2eNdjaUU4FMRjUIMz0MNTZkz3KMEjXM22f';
    private $_url = 'http://110.42.1.238:5050/';
    public function order($request) {
        $parameters = [
            'number' => $this->_mchId,
            'ip' => $request['clientIp'],
            'order' => 'YZ'.$request['orderNo'],
            'money' => sprintf("%.2f", $request['amount']/100),
            'otifyUrl' => config('app.url').'/api/pay/sbpay/notify',
            'returnUrl' => config('app.url').'/api/pay/sbpay/notify'

        ];

        $sign = md5('number='.$parameters['number'].'&order='.$parameters['order'].
            '&money='.$parameters['money'].'&otifyUrl='.$parameters['otifyUrl'].
            '&returnUrl='.$parameters['returnUrl'].'&'.$this->_key);

        $parameters['sign'] = $sign;
        $response = Http::asForm()->post($this->_url.'pay/index.php', $parameters);
        $response = $response->json();
        if($response['success']== '请求成功') {
            return [
                'code' => 1,
                'data' => [
                    'qrImgUrl' => $response['url'],
                    'payOrderId' => 'YZ'.$request['orderNo'],
                    'qrUrl' => $response['url'],
                    'payUrl' => $response['url'],
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
        $sign = md5('number='.$this->_mchId.'&order='.$req['order'].
            '&money='.$req['money'].'&otifyUrl='.config('app.url').'/api/pay/sbpay/notify'.
            '&returnUrl='.config('app.url').'/api/pay/sbpay/notify'.'&'.$this->_key);
        if($request->get('sign') != $sign) {
            return [
                'status' => '30003',
                'msg' => '签名错误'
            ];
        }
        $mchOrderNo = substr($request->get('order'), 2);
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
