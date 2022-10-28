<?php

namespace App\Help;
class Methods {


    public static function diversion($msg='', $metchod='') {
        $js = <<<JS
            <script src="../lib/layui-v2.5.5/layui.js" charset="utf-8"></script>
            <script>
            layui.use(['form', 'table'], function () {
                layer.msg('$msg', function () {
                    $metchod
                });
            });
            </script>
            JS;
        return $js;

    }

    public static function csv($name, $subject, $data)
    {
        set_time_limit(0);
        ini_set('memory_limit', '256M');
        //下载csv的文件名
        $fileName = $name.'.csv';
        header('Content-Description: File Transfer');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        //打开php数据输入缓冲区
        $fp = fopen('php://output', 'a');
        $heade = $subject;
        //将数据编码转换成GBK格式
        mb_convert_variables('GBK', 'UTF-8', $heade);
        //将数据格式化为CSV格式并写入到output流中
        fputcsv($fp, $heade);

        //如果在csv中输出一个空行，向句柄中写入一个空数组即可实现
        foreach ($data as $row) {
            //将数据编码转换成GBK格式
            mb_convert_variables('GBK', 'UTF-8', $row);
            fputcsv($fp, $row);
            //将已经存储到csv中的变量数据销毁，释放内存
            unset($row);
        }
        //关闭句柄
        fclose($fp);
        die;
    }

    public static function is_mobile_request()
    {

        $_SERVER['ALL_HTTP'] = isset($_SERVER['ALL_HTTP']) ? $_SERVER['ALL_HTTP'] : '';

        $mobile_browser = '0';

        if(preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|iphone|ipad|ipod|android|xoom)/i', strtolower($_SERVER['HTTP_USER_AGENT'])))

            $mobile_browser++;

        if((isset($_SERVER['HTTP_ACCEPT'])) and (strpos(strtolower($_SERVER['HTTP_ACCEPT']),'application/vnd.wap.xhtml+xml') !== false))

            $mobile_browser++;

        if(isset($_SERVER['HTTP_X_WAP_PROFILE']))

            $mobile_browser++;

        if(isset($_SERVER['HTTP_PROFILE']))

            $mobile_browser++;

        $mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'],0,4));

        $mobile_agents = array(

            'w3c ','acs-','alav','alca','amoi','audi','avan','benq','bird','blac',

            'blaz','brew','cell','cldc','cmd-','dang','doco','eric','hipt','inno',

            'ipaq','java','jigs','kddi','keji','leno','lg-c','lg-d','lg-g','lge-',

            'maui','maxo','midp','mits','mmef','mobi','mot-','moto','mwbp','nec-',

            'newt','noki','oper','palm','pana','pant','phil','play','port','prox',

            'qwap','sage','sams','sany','sch-','sec-','send','seri','sgh-','shar',

            'sie-','siem','smal','smar','sony','sph-','symb','t-mo','teli','tim-',

            'tosh','tsm-','upg1','upsi','vk-v','voda','wap-','wapa','wapi','wapp',

            'wapr','webc','winw','winw','xda','xda-'

        );

        if(in_array($mobile_ua, $mobile_agents))

            $mobile_browser++;

        if(strpos(strtolower($_SERVER['ALL_HTTP']), 'operamini') !== false)

            $mobile_browser++;

        // Pre-final check to reset everything if the user is on Windows

        if(strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows') !== false)

            $mobile_browser=0;

        // But WP7 is also Windows, with a slightly different characteristic

        if(strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows phone') !== false)

            $mobile_browser++;



        if($mobile_browser>0)

            return true;

        else

            return false;

    }

    public static function get_total_millisecond()

    {

        $time = explode (" ", microtime () );
        $time = $time [1] . ($time [0] * 1000);

        $time2 = explode ( ".", $time );

        $time = $time2 [0];

        return $time;

    }
}
