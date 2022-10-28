<?php  namespace App\Upstream;
use App\Help\Sign;
use App\Models\Order;
use Illuminate\Support\Facades\Http;

class Shandian {
    private $_mchId = '1082';
    private $_key = 'BUUESLE1EVZ2I5ROFQFV2GJB1LTGBN5ECCZCN15FM47S3RLIB4ODADIIAF3PQZQTPZM3JUCRIPIW1Z3HYJEXVLXB0UFVGY0HC4OSGIPXM4DU3AUDZABWDJNHI1EDBHE0';
    private $_url = 'https://api.shanpropay.com/';
    public function order($request) {
        $parameters = [
            'mchId' => $this->_mchId,
            'appId' => '25778672556f42e386437c5f958a9967',
            'productId' => $request['channelId'],
            'mchOrderNo' => 'YZ' . $request['orderNo'],
            'amount' => $request['amount'],
            'notifyUrl' => config('app.url').'/api/pay/shandian/notify',
//            'redirectUrl' => $request['redirectUrl'],

        ];

        $signObj = new Sign();
        $sign = $signObj->encode($parameters, $this->_key);
        $parameters['sign'] = $sign;
        error_log(print_r($parameters,1),3,'shandian.txt');
        $response = Http::asForm()->post($this->_url.'api/pay/create_order', $parameters);
        $response = $response->json();
        if($response['retCode'] == 0) {
            return [
                'code' => 1,
                'data' => [
                    'qrImgUrl' => $response['payJumpUrl'],
                    'payOrderId' => $response['payOrderId'],
                    'qrUrl' => $response['payJumpUrl'],
                    'payUrl' => $response['payJumpUrl'],
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
                'status' => '30003',
                'msg' => 'Signature error'
            ];
        }
        $mchOrderNo = substr($request->get('mchOrderNo'), 2);
        $orderModel = new Order();
        $order = $orderModel->where('OrderNo', $mchOrderNo)->first();
        if(empty($order)) {
            return false;
        }
        if($request->get('status') == 2 && $order->status == 0) {
            if(empty($request->get('amount')) || $request->get('amount') == 0) {
                return false;
            }
            $orderModel->where('OrderNo', $mchOrderNo)->update([
                'status' => 1,
                'pay_amount' => $request->get('amount'),
                'pay_time' => time(),
'created' => time()
            ]);
        }
        return $orderModel->where('id', $order->id)->first();
    }
}
