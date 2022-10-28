<?php
if(date('H:i:s') > '00:20:00' ) {
    die;
}

$dbms='mysql';     //数据库类型
$host='localhost'; //数据库主机名
$dbName='square';    //使用的数据库
$user='root';      //数据库连接用户名
$pass='fsdFGPOJR@!4234LGJd';          //对应的密码
$dsn="$dbms:host=$host;dbname=$dbName";


try {
    $dbh = new PDO($dsn, $user, $pass); //初始化一个PDO对象
    /*你还可以进行一次搜索操作

    */
    $dbh = null;
} catch (PDOException $e) {
    die ("Error!: " . $e->getMessage() . "<br/>");
}
//默认这个不是长连接，如果需要数据库长连接，需要最后加一个参数：array(PDO::ATTR_PERSISTENT => true) 变成这样：
$db = new PDO($dsn, $user, $pass);
$items = $db->query('select * from telegram_merchant where mark=1 Limit 0,20')->fetchAll();
if(empty($items)) {
    die;
}

foreach ($items as $item) {
    $chat_id = $item['id'];
    go(function () use($chat_id) {
        //这里使用 sleep 5 来模拟一个很长的命令
        co::exec("php /home/wwwroot/square/artisan issued ".$chat_id);
//            error_log(312,3,'/home/aa/'.$chat_id);
    });
}



