<?php  namespace App\Upstream;
use App\Help\Sign;
use App\Models\Order;
use Illuminate\Support\Facades\Http;

class Jd {
    private $_mchId = '30360';
    private $_key = 'l698jxyf';
    private $_url = 'http://103.171.34.25:10001/';
    public function order($request) {
        $parameters = [
            'od' => 'YZ' . $request['orderNo'],
            'submid' => $this->_mchId,
            'money' => sprintf("%.2f", $request['amount']/100),
            'td' => $request['channelId'],
            'miaoshu' => 'subject',
            'notifyurl' => config('app.url').'/api/pay/jd/notify',


        ];
        $sign = md5($parameters['od'].$parameters['submid'].$parameters['money']
            .$parameters['td']. $parameters['miaoshu'].$parameters['notifyurl'].$this->_key);
        $parameters['sign'] = $sign;
        $response = Http::asForm()->get($this->_url.'api/ordersubmit', $parameters);
        $response = $response->json();
        if($response['s'] == 1) {
            return [
                'code' => 1,
                'data' => [
                    'qrImgUrl' => $response['msg'],
                    'payOrderId' => 'YZ' . $request['orderNo'],
                    'qrUrl' => $response['msg'],
                    'payUrl' => $response['msg'],
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
        $sign = md5($request['submid'].$request['od'].$request['status']
            .$request['money'].$this->_key);
        if($request->get('sign') != $sign) {
            return [
                'status' => '30003',
                'msg' => 'Signature error'
            ];
        }
        $mchOrderNo = substr($request->get('od'), 2);
        $orderModel = new Order();
        $order = $orderModel->where('OrderNo', $mchOrderNo)->first();
        if(empty($order)) {
            return false;
        }
        if($request->get('status') == 1 && $order->status == 0) {
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
