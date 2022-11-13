<?php

namespace App\Help;
class Sign {
    public function encode($params, $key) {
        $sourceStr = $this->ASCII($params).'&key='.$key;
        return strtoupper(md5($sourceStr));
    }

    public function smencode($params, $key) {
        $sourceStr = $this->ASCII($params).'&key='.$key;
        return md5($sourceStr);
    }

    public function smencode1($params, $key) {
        $sourceStr = $this->ASCII($params).'&'.$key;
        return md5($sourceStr);
    }

    public function zhongyou($params, $key) {
        $sourceStr = $this->ASCII($params).$key;
        return md5($sourceStr);
    }

    public function brencode($params, $key) {
        $sourceStr = $this->ASCII($params).$key;
        return strtoupper(md5($sourceStr));
    }

    public function brcbencode($params, $key) {
        if(!empty($params)){
            $p =  ksort($params);
            if($p){
                $str = '';
                foreach ($params as $k=>$val){
                    $str .= $k .'=' . $val . '&';
                }
                $strs = rtrim($str, '&');

            }
        }
        dd($strs.$key);
        return strtoupper(md5($strs.$key));
    }

    public function pxencode($params, $key) {
        if(!empty($params)){
            $p =  ksort($params);
            if($p){
                $str = '';
                foreach ($params as $k=>$val){
                    if($val != ''){
                        $str .= $k .'=' . $val . '&';
                    }
                }
                $sourceStr = rtrim($str, '&');
            }
        }



        $sourceStr = $sourceStr.$key;
        return strtoupper(md5($sourceStr));
    }

    private function ASCII($params = []) {
        if(!empty($params)){
            $p =  ksort($params);
            if($p){
                $str = '';
                foreach ($params as $k=>$val){
                    if($val != ''){
                        $str .= $k .'=' . $val . '&';
                    }
                }
                $strs = rtrim($str, '&');
                return $strs;
            }
        }
        return false;
    }

    private function newASCII($params = []) {
        if(!empty($params)){
            $p =  ksort($params);
            if($p){
                $str = '';
                foreach ($params as $k=>$val){
                    $str .= $k .'=' . $val . '&';
                }
                $strs = rtrim($str, '&');
                return $strs;
            }
        }
        return false;
    }

    public function wuyouEncode($params, $key, $token) {
        $sourceStr = $this->wuyouASCII($params).$key.$token;
        return strtoupper(md5($sourceStr));
    }

    private function wuyouASCII($params = []) {
        if(!empty($params)){
            $p =  ksort($params);
            if($p){
                $str = '';
                foreach ($params as $k=>$val){
                    if(!empty($val)){
                        $str .= $k. $val;
                    }
                }
                $strs = rtrim($str, '&');
                return $strs;
            }
        }
        return false;
    }

    public function makeSign($data, $secret): string
    {
        $data = array_filter($data);
        ksort($data);
        $str1 = '';
        foreach ($data as $k => $v) {
            $str1 .= '&' . $k . "=" . $v;
        }
        $str = $str1 . $secret;
        $str = trim($str, '&');
        $sign = md5($str);
        return $sign;
    }




    public function testEncode($params, $key) {
        $sourceStr = $this->ASCII($params).'&key='.$key;
        echo $sourceStr.'<br>';
        echo strtoupper(md5($sourceStr));die;
    }

    public function newencode($params, $key) {
        $sourceStr = $this->newASCII($params).'&key='.$key;
        return strtoupper(md5($sourceStr));
    }

    public function huihui($params, $key) {
        $params['mch_secret'] = $key;

        $p =  ksort($params);
        if($p){
            $str = '';
            foreach ($params as $k=>$val){
                if(!empty($val)){
                    $str .= $k .'=' . urlencode($val). '&';
                }
            }
            $strs = rtrim($str, '&');
        }


        return strtoupper(md5($strs));
    }

    public function hademen($params, $key) {

        $p =  ksort($params);
        if($p){
            $str = '';
            foreach ($params as $k=>$val){
                $str .= $k .'=' . $val . '&';
            }
            $strs = rtrim($str, '&');
        }


        return md5($strs.'&key='.$key);
    }

    public function wusong($params, $key) {
        if(!empty($params)){
            $p =  ksort($params);
            if($p){
                $str = '';
                foreach ($params as $k=>$val){
                    if($val != ''){
                        $str .= $k .'=' . $val . '&';
                    }
                }
                $strs = rtrim($str, '&');
            }
        }




        $sourceStr = $strs.'&secretKey='.strtoupper($key);

        return strtoupper(md5($sourceStr));
    }
}
