<?php  namespace App\Upstream;
use App\Help\Sign;
use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Xjhb {
    private $_mchId = 'A220606105409034';
    private $_key = 'SPlHGXnHEWbaD9BZUSH5mTpEwASGnExO';
    private $_url = 'http://api.go-pays.com/';
    public function order($request) {

//签名排列，按键值字母排序升序
        function encryptMD5Str($param,$key)
        {
            //去除空字段
            $param= array_filter($param);
            //参数排序
            ksort($param);
            $param = urldecode(http_build_query($param)."&key=".$key);
            //dump($param);
            return md5($param);
        }

//AES加密排列，按键值字母排序
        function encryptAesStr($param)
        {
            //参数排序
            ksort($param);
            return json_encode($param,true);
        }

//AES-128-ECB加密
        function aes_encrypt($data, $key) {
            dd($data);
            $data =  openssl_encrypt($data, 'AES-128-ECB', $key, OPENSSL_RAW_DATA);
            return base64_encode($data);
        }

//AES-128-ECB解密
        function aes_decrypt($data, $key) {

            $encrypted = base64_decode($data);
            return openssl_decrypt($encrypted, 'AES-128-ECB', $key, OPENSSL_RAW_DATA);
        }


        $param['mno'] = $this->_mchId;
        $param['orderno'] = 'YZ'.$request['orderNo'];
        $param['amount'] = $request['amount'];
        $param['pt_id'] = $request['channelId'];
        $param['async_notify_url']  = config('app.url').'/api/pay/xjhb/notify';

//MD5加密
        $param['sign'] = encryptMD5Str($param,$this->_key);

//将所有请求参数转为json进行AES-128-ECB加密
        $encryptAesStr = encryptAesStr($param);
//var_dump($encryptAesStr);
//使用AES-128-ECB加密请求所有参数
        $content = aes_encrypt($encryptAesStr,"VuMQuajEwcr3hqCE");
//var_dump("AES加密：".$content);
//$content = $this->aes_decrypt($content,$key['aes_key']);
//var_dump("AES解密：".$content);
//exit;

        $parameters = [
            'mno' => $param['mno'],
            'content' => $content
        ];




        $response = Http::asForm()->post($this->_url, $parameters);
        $response = $response->json();
        if($response['code'] == 'success') {
            return [
                'code' => 1,
                'data' => [
                    'qrImgUrl' => $response['data']['payurl'],
                    'payOrderId' => 'YZ'.$request['orderNo'],
                    'qrUrl' => $response['data']['payurl'],
                    'payUrl' => $response['data']['payurl'],
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
        function encryptMD5Str($param,$key)
        {
            //去除空字段
            $param= array_filter($param);
            //参数排序
            ksort($param);
            $param = urldecode(http_build_query($param)."&key=".$key);
            //dump($param);
            return md5($param);
        }

//AES加密排列，按键值字母排序
        function encryptAesStr($param)
        {
            //参数排序
            ksort($param);
            return json_encode($param,true);
        }

//AES-128-ECB加密
        function aes_encrypt($data, $key) {
            $data =  openssl_encrypt($data, 'AES-128-ECB', $key, OPENSSL_RAW_DATA);
            return base64_encode($data);
        }

//AES-128-ECB解密
        function aes_decrypt($data, $key) {
            $encrypted = base64_decode($data);
            return openssl_decrypt($encrypted, 'AES-128-ECB', $key, OPENSSL_RAW_DATA);
        }

//商户号
        $mno = trim($_POST['mno']);
        $content = trim($_POST['content']);

//使用AES-128-ECB解密content
        $content = aes_decrypt($content,"VuMQuajEwcr3hqCE");
//var_dump("AES解密：".$content);
//AES-128-ECB解密得到请求数据
        $param = json_decode($content,true);

        if(empty($param))die("请求参数解析失败");

//服务端解密完的sign
        $sign = $param['sign'];
        unset($param['sign']);

//MD5加密
        $encrypted = encryptMD5Str($param,$this->_key);















        if($encrypted != $sign) {
            return [
                'status' => '30003',
                'msg' => 'Signature error'
            ];
        }
        $mchOrderNo = substr($param['orderno'], 2);
        $orderModel = new Order();
        $order = $orderModel->where('OrderNo', $mchOrderNo)->first();
        if(empty($order)) {
            return false;
        }
        if($param['status']=="1" && $order->status == 0) {
            if(empty($param['amount']) || $param['amount'] == 0) {
                return false;
            }
            $orderModel->where('OrderNo', $mchOrderNo)->update([
                'status' => 1,
                'pay_amount' => $param['amount'],
                'pay_time' => time(),
'created' => time()
            ]);

        }
        return $orderModel->where('id', $order->id)->first();
    }
}
