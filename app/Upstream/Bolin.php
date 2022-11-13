<?php  namespace App\Upstream;
use App\Help\Broadcast;
use App\Help\Sign;
use App\Models\Order;
use Illuminate\Support\Facades\Http;

class Bolin {
    private $_mchId = '221200696';
    private $_key = 'SxhXc6x26QicS7Vb5h8ASPlmY7gnLpXH';
    private $_url = 'http://api.bolinpay.cc/';
    private $_privateKey = 'MIIEvwIBADANBgkqhkiG9w0BAQEFAASCBKkwggSlAgEAAoIBAQCJ59hnji7FUv3on2lFFk68D+c4VufSt5RwvbSDsz3Bp+Bsc/olzTmVSJOGyMyK4eCwBMkwHtQaBsi0pNSzHvZVpHJ0PoSDM63ZPeCqp6xMlPOoKPEY2H8hxxGSngPd8CE35pKtDmCQSPIQ/el4sqWiXSE177LhYieeHpPstwtd032KaXs0Y1rMdhlWLdyvGJSNhPAH7+nYdsWfcKonRvG+s7rYBG5BXU2gzc1Yll47k1MXXeDkTrsTPeMGeBaT2EclhVuyH1c7SDhKNYMgqKibJuD1ws4vHOsTwLQ+IZgyIuWK3bJM/VotiXYRVIuBjaea2R8raoBUjiYQBxPz0+uRAgMBAAECggEAaZa10BxCXV19AZbE4FwDLuUyUaXg3CspoeTuiQQ9XcBvCjsGpdjrpQyrwECQtCncgok3jfucxMy68e1e2kLzs4E1DSItDdQM4VAKbD/zobNOmEu4xiBFvxQPtWX9afoJRSbq2UwvzWm8xhvTnlGSlq2d1xFPslgYI24gLte8ATrkfD846js0i6vqXm3jJYBxlGuvdRWSH7p8MDmfEOnXVK6ousgeS++lWx2zPPNzvPW1PhGDnCI70TEvs5f5LTx+OjL8ukXpj/iRAEtPdYH9aDqRLOoQGy16mB3OKAhQUat8R3WY24ax0DawGgD/kNaxcRdAS/UmDdte9Equqhl5nQKBgQDCFurQvw119etH4F/I0wLGfXjUAbV0jTg0FI4bNP7u78j0ZImPdRB5yj7xHb2dWBsRaZmkMgj5n7hUyYh4T//72NfAVb3cqRoC6dNMXJNmaOfOQAG0RiWqw1wqyuyKJBqabukTi9vjnKU5T3ymIBcSq0r4vBCj2l5QzeeLxMpGUwKBgQC15QfCRVuyGheMpB9R3T2Bl+zy3V0Bje5ijUn0Ks8ai0IHMJtwe6G6pZ81o7rRbSCrPrp/4Ll+cBKqQgTc7wUgNjq7QW4d25a2bPU01qLBcqAUnZYOyXmU0j10yz7ZM28o8sEq4hcYPY6nc401dArbcV7Zl3AtGZk+/lVKB8jCCwKBgQCPlu7P0ph9zZrsSRXz+BBUkp0ik8UP3i5XcWGUxUdZs/JCxGJ0zJGM2NBqB6uxTW5xGmP9crrrGnv/1j1tbRof01QCyiw1pLFGuICHPPIb0L61+uqH2WGijPeR/SC0LnO0DxvGRzE9mjUuSh0YtiL7v9guXswcvMAMHdkQsV1IWwKBgQCNuFLhRJT7IWUzyTQ13oDrlemiGGM8sM8Jrjfuq+QNNG9PlsmlTE5oVF7Ftjn6rmIDyq4YsIkWQE/qo+GSGhEOVfJQr9wSG2ND44TxlEHfI2Yiad3ey4+VKnGDrE/dfzue1fU3akzAMEYTpe5htXY4IA7czicynH1QHW1qbsI2nQKBgQCPVH5gSAKp/HQon11Wb/RESTmhKVC28tpAa4AjewoSMYpQybeuA7ZqKfwpp2pTtLd78stZikxtFUaf1IDCKrCZMvTw+hnnAFm9jOxCxWO3wvVeESPQn6Gl0BezHPKRaMA/Gu1tZMjFfkyZjyBOlyG6k55wjbju1UjK+jhNONylUQ==';
    private $_publicKey = 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAnVcZhlDHBYXgB2exj+9VpoxzqG/NnxGxwToyUmYmbWshyOc0eCLQA+5WfN0pzZyQGfjEpHD7SPvDuRqqJPeNd/vVKMSIeM6mU5vpyQdrQvUq8buuSskGOO0qcd/Uiww6ToTkHJdfXTFO/FpI/MiuvYsIDW132dnageXu/l2iI9PbA2fkrnDxl6/loC2UiPnHJfyWbD3h/Bwj0uNkBrVp56BEQFDrxhbn7rAC/4Kg6lpexEJ1VbA1z/5MFhvDWTQ/Pw2QW4NfDP834kT4AuH2kfyYLG5gHBpcbGuyyJjt7rgwWSDMip0S6J2kgkKDdVmG+B3VNQs3yxeRzOWXoiNNowIDAQAB';
    public function order($request) {
        $parameters = [
            'pay_memberid' => $this->_mchId,
            'pay_orderid' => 'YZ'.$request['orderNo'],
            'pay_bankcode' => $request['channelId'],
            'pay_applydate' => date('Y-m-d Hi:s'),
            'pay_amount' => sprintf("%.2f", $request['amount']/100),
            'pay_notifyurl' => config('app.url').'/api/pay/bolin/notify',

        ];
        if(!empty($request['redirectUrl'])) {
            $parameters['pay_callbackurl'] = $request['redirectUrl'];
        } else {
            $parameters['pay_callbackurl'] = config('app.url').'/api/pay/bolin/notify';
        }

        $signObj = new Sign();
        $signValue = $signObj->encode($parameters, $this->_key);



        $privateKey = $this->_privateKey;//商户私钥
        $private = "-----BEGIN PRIVATE KEY-----\n" . wordwrap($privateKey, 64, "\n", true). "\n-----END PRIVATE KEY-----";
        $key = openssl_pkey_get_private($private); //解析商户私钥
        openssl_sign($signValue, $sign, $key, OPENSSL_ALGO_SHA256); //签名
        openssl_free_key($key); //释放私钥
        $sign = base64_encode($sign);






        $parameters['pay_md5sign'] = $sign;
        $parameters['type'] = 'json';
        $response = Http::asForm()->post($this->_url.'pay', $parameters);
        $response = $response->json();
        if($response['status'] == 200 ) {
            return [
                'code' => 1,
                'data' => [
                    'qrImgUrl' => $response['data'],
                    'payOrderId' => 'YZ'.$request['orderNo'],
                    'qrUrl' => $response['data'],
                    'payUrl' => $response['data'],
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
        $signValue = $signObj->encode($req, $this->_key);


        //平台公钥，通道管理->对接参数中查看
        $publicKey   = $this->_publicKey;
        $pay_md5sign = $request->get('sign');
        $public = "-----BEGIN PUBLIC KEY-----\n". wordwrap($publicKey, 64, "\n", true)."\n-----END PUBLIC KEY-----";

        $result = openssl_verify($signValue,
            base64_decode($pay_md5sign),
            $public,OPENSSL_ALGO_SHA256);



        if($result != 1) {
            return [
                'status' => '30003',
                'msg' => 'Signature error'
            ];
        }
        $mchOrderNo = substr($request->get('orderid'), 2);
        $orderModel = new Order();
        $order = $orderModel->where('OrderNo', $mchOrderNo)->first();
        if(empty($order)) {
            return false;
        }
        if($request->get('returncode') == '00' && $order->status == 0) {
            if(empty($request->get('amount')) || $request->get('amount') == 0) {
                return false;
            }
            $orderModel->where('OrderNo', $mchOrderNo)->update([
                'status' => 1,
                'pay_amount' => $request->get('amount')*100,
                'pay_time' => time(),
                'created' => time()
            ]);
        }
        return $orderModel->where('id', $order->id)->first();
    }
}
