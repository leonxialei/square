<?php  namespace App\Upstream;
use App\Help\Sign;
use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Fengzi {
    private $_mchId = '37e7122b83f3d55f';
    private $_key = '6c6b4f0c4226052e4ef7f55777d85d89';
    private $_url = 'http://pay.fengy3pay.com';
    public function order($request) {



        $paykey = $this->_key;
        $url = $this->_url;
        $parameter = array(
            "api_id" => $this->_mchId,
            "record" => 'YZ' . $request['orderNo'],
            "money" => sprintf("%.2f",$request['amount']/100),

        );
        function md5_sign($data, $key){//签名加密
            ksort($data);
            $str1 = '';
            foreach ($data as $k => $v) {
                $str1 .= '&' . $k . "=" . $v;
            }
            $sign = md5(trim($str1) . $key);
            return $sign;
        }

        $sign= md5_sign($parameter,$this->_key);

        $parameter['sign'] = $sign;//md5加密

        $parameter['refer'] = config('app.url').'/api/pay/fengzi/notify';
        $parameter['notify_url'] = config('app.url').'/api/pay/fengzi/notify';
        $parameter['typec'] = $request['channelId'];
        $parameter['json_ret'] = 1;
        //post请求
        $header =  array('Accept-Language: zh-cn');


        function curl($sUrl, $aHeader, $aData) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_URL, $sUrl);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $aHeader);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 );// 不可去掉 否则拉起慢
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($aData));
            $sResult = curl_exec($ch);
            if ($sError = curl_error($ch)) {
                die($sError);
            }
            curl_close($ch);
            return $sResult;
        }

        $result = curl($url,$header,$parameter);
        $result = json_decode($result,1);

        if($result['code'] == '200') {
            return [
                'code' => 1,
                'data' => [
                    'qrImgUrl' => $result['data']['pay_url'],
                    'payOrderId' => $result['data']['order_id'],
                    'qrUrl' => $result['data']['pay_url'],
                    'payUrl' => $result['data']['pay_url'],
                    'channelId' => $request['channelId'],
                ]
            ];
        } else {
            return [
                'code' => 0,
                'data' => $parameter,
                'para' => $result,
            ];
        }





    }

    public function notify($request) {
        $money = $_REQUEST['money'];//金额（注意：php使用 sprintf("%.2f",金额)  接收此参数）
        $order = $_REQUEST['order'];//本系统订单号
        $record = $_REQUEST['record'];//附加参数（发起支付传递的您网站的订单号或用户名等唯一参数）

        $data = array(
            'api_id' => $this->_mchId,
            'record' => strval($record),
            'money' => sprintf("%.2f", $money),
        );

        function md5_sign($data, $key){//签名加密
            ksort($data);
            $str1 = '';
            foreach ($data as $k => $v) {
                $str1 .= '&' . $k . "=" . $v;
            }
            $sign = md5(trim($str1) . $key);
            return $sign;
        }

        $sign_ok = md5_sign($data,$this->_key);
        if($request->get('sign') != $sign_ok) {
            return [
                'status' => '30003',
                'msg' => 'Signature error'
            ];
        }
        $mchOrderNo = substr($request->get('record'), 2);
        $orderModel = new Order();
        $order = $orderModel->where('OrderNo', $mchOrderNo)->first();
        if(empty($order)) {
            return false;
        }
        if(empty($request->get('money')) || $request->get('money') == 0) {
            return false;
        }
        $orderModel->where('OrderNo', $mchOrderNo)->update([
            'status' => 1,
            'pay_amount' => $request->get('money')*100,
            'pay_time' => time(),
'created' => time()
        ]);
        return $orderModel->where('id', $order->id)->first();
    }
}
