<?php  namespace App\Upstream;
use App\Help\Sign;
use App\Models\Order;
use Illuminate\Support\Facades\Http;

class Dongfeng1 {
    private $_mchId = '2022104';
    private $_key = 'MyWDwArIKeIvglufANkDlUCIFxUyUYAi';
    private $_url = 'http://8.210.128.136/';
    public function order($request) {
        $parameters = [
            'fxid' => $this->_mchId,
            'fxddh' => 'YZ'.$request['orderNo'],
            'fxdesc' => $request['subject'],
            'fxfee' => sprintf("%.2f", $request['amount']/100),
            'fxnotifyurl' => config('app.url').'/api/pay/dongfeng1/notify',
            'fxpay' => $request['channelId'],
            'fxip' => $request['clientIp']

        ];
        if(!empty($request['redirectUrl'])) {
            $parameters['fxbackurl'] = $request['redirectUrl'];
        } else {
            $parameters['fxbackurl'] = config('app.url').'/api/pay/notify';
        }



        $sign = md5($parameters['fxid'].$parameters['fxddh'].$parameters['fxfee']
            .$parameters['fxnotifyurl'].$this->_key);
        $parameters['fxsign'] = $sign;
        $response = Http::asForm()->post($this->_url.'Pay', $parameters);
        $response = $response->json();

        if($response['status'] == 1) {
            return [
                'code' => 1,
                'data' => [
                    'qrImgUrl' => $response['payurl'],
                    'payOrderId' => 'YZ'.$request['orderNo'],
                    'qrUrl' => $response['payurl'],
                    'payUrl' => $response['payurl'],
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
        $sign = md5($req['fxstatus'].$req['fxid'].$req['fxddh']
            .$req['fxfee'].$this->_key);
        if($request->get('fxsign') != $sign) {
            return [
                'status' => '30003',
                'msg' => 'Signature error'
            ];
        }
        $mchOrderNo = substr($request->get('fxddh'), 2);
        $orderModel = new Order();
        $order = $orderModel->where('OrderNo', $mchOrderNo)->first();
        if(empty($order)) {
            return false;
        }
        if($request->get('fxstatus') == 1 && $order->status == 0) {
            if(empty($request->get('fxfee')) || $request->get('fxfee') == 0) {
                return false;
            }
            $orderModel->where('OrderNo', $mchOrderNo)->update([
                'status' => 1,
                'pay_amount' => $request->get('fxfee')*100,
                'pay_time' => time(),
'created' => time()
            ]);
        }
        return $orderModel->where('id', $order->id)->first();
    }
}
