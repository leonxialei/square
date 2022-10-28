<?php  namespace App\Upstream;
use App\Help\Sign;
use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Heng {
    private $_mchId = '743864839167803392';
    private $_key = '7d1d8b32d3b44229871e84b8ef316638';
    private $_url = 'http://apid1d29.coco-co.cc/';
    public function order($request) {
        $parameters = [
            'version' => '3.0',
            'method' => 'Gt.online.interface',
            'partner' => $this->_mchId,
            'banktype' => $request['channelId'],
            'paymoney' => sprintf("%.2f", $request['amount']/100),
            'ordernumber' => 'YZ'.$request['orderNo'],
            'callbackurl' => config('app.url').'/api/pay/heng/notify',

            'notreturnpage' => true,





        ];

        if(!empty($request['redirectUrl'])) {
            $parameters['hrefbackurl'] = $request['redirectUrl'];
        } else {
            $parameters['hrefbackurl'] = config('app.url').'/api/pay/heng/notify';
        }


        $sign = md5('version=3.0&method=Gt.online.interface&partner='.$this->_mchId.
            '&banktype='.$parameters['banktype'].'&paymoney='.$parameters['paymoney'].
            '&ordernumber='.$parameters['ordernumber'].'&callbackurl='.$parameters['callbackurl'].$this->_key);
        $parameters['sign'] = $sign;

        $response = Http::asForm()->post($this->_url.'api/v1/getway', $parameters);
        $response = $response->json();
        if($response['code'] == 0) {
            return [
                'code' => 1,
                'data' => [
                    'qrImgUrl' => $response['data']['qrcodeContent'],
                    'payOrderId' => $response['data']['tradeNo'],
                    'qrUrl' => $response['data']['qrcodeUrl'],
                    'payUrl' => $response['data']['payUrl'],
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
        $ReturnArray = array( // 返回字段
            "partner" => $request->get('partner'), // 商户ID
            "ordernumber" =>  $request->get('ordernumber'),
            "orderstatus" =>  $request->get('orderstatus'), // 交易金额
            'paymoney' => $request->get('paymoney'),
        );
        $md5str = "";
        foreach ($ReturnArray as $key => $val) {
            if (!empty($val)) {
                $md5str = $md5str . $key . "=" . $val . "&";
            }
        }
        $md5str = rtrim($md5str,"&");
        $sign = md5($md5str .  $this->_key);
        if($request->get('sign') != $sign) {
            return [
                'status' => '30003',
                'msg' => 'Signature error'
            ];
        }
        $mchOrderNo = substr($request->get('ordernumber'), 2);
        $orderModel = new Order();
        $order = $orderModel->where('OrderNo', $mchOrderNo)->first();
        if(empty($order)) {
            return false;
        }
        if(strtoupper($request['orderstatus']) == 1 && $order->status == 0) {
            if(empty($request->get('paymoney')) || $request->get('paymoney') == 0) {
                return false;
            }
            $orderModel->where('OrderNo', $mchOrderNo)->update([
                'status' => 1,
                'pay_amount' => $request->get('paymoney')*100,
                'pay_time' => time(),
'created' => time()
            ]);
        }
        return $orderModel->where('id', $order->id)->first();
    }
}
