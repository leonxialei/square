<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\InitController;
use App\Http\Controllers\Api\MerchantChannelController;
use App\Http\Controllers\Api\PayController;
use App\Http\Controllers\Api\PayTestController;
use App\Http\Controllers\Api\NotifyController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ChannelController;
use App\Http\Controllers\Api\NewNotifyController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\UpstreamController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('pay/apprise/{id}', [PayController::class, 'apprise']);
Route::post('pay/create_order', [PayController::class, 'createOrder']);
Route::post('pay/test/create_order', [PayTestController::class, 'createOrder']);
Route::get('pay/test/pay', [PayTestController::class, 'test']);
Route::get('pay/test/redis', [PayTestController::class, 'redis']);
Route::get('pay/test/notify', [PayTestController::class, 'notify']);
Route::get('pay/test/bot', [PayTestController::class, 'bot']);
Route::get('pay/notify', [PayController::class, 'notify']);
Route::get('pay/notify/test', [PayController::class, 'notifyTest']);
Route::post('pay/notify/post', [PayController::class, 'notifyPost']);
Route::post('pay/ytnotify', [PayController::class, 'ytNotify']);
Route::post('pay/wuyou/notify', [PayController::class, 'wuyouNotify']);
Route::post('pay/pangxie/notify', [PayController::class, 'PxNotify']);
Route::post('pay/wanguan/notify', [PayController::class, 'WgNotify']);
Route::post('pay/jindun/notify', [PayController::class, 'jdNotify']);
Route::post('pay/daxing/notify', [NotifyController::class, 'dx']);
Route::post('pay/bangrui/notify', [NotifyController::class, 'br']);
Route::post('pay/facai/notify', [NotifyController::class, 'facai']);
Route::post('pay/zhongyou/notify', [NotifyController::class, 'zhongyou']);
Route::post('pay/xingxingtiyu/notify', [NotifyController::class, 'xingxingtiyu']);
Route::post('pay/baiyue/notify', [NotifyController::class, 'baiyue']);
Route::post('pay/laojin/notify', [NotifyController::class, 'laojin']);
Route::post('pay/jinniu/notify', [NotifyController::class, 'jinniu']);
Route::post('pay/bh/notify', [NotifyController::class, 'bh']);
Route::post('pay/xinzhongyou/notify', [NotifyController::class, 'xinzhongyou']);
Route::post('pay/xm/notify', [NotifyController::class, 'xm']);
Route::post('pay/sh/notify', [NotifyController::class, 'sh']);
Route::post('pay/hw/notify', [NotifyController::class, 'hw']);
Route::post('pay/yufang/notify', [NotifyController::class, 'yufang']);
Route::post('pay/dingding/notify', [NotifyController::class, 'dingding']);
Route::get('pay/daji/notify', [NotifyController::class, 'daji']);
Route::post('pay/xy/notify', [NotifyController::class, 'xy']);
Route::post('pay/hf/notify', [NotifyController::class, 'hf']);
Route::post('pay/bufan/notify', [NotifyController::class, 'bufan']);
Route::get('pay/bufan/notify', [NotifyController::class, 'bufanaa']);
Route::post('pay/wubai/notify', [NotifyController::class, 'wubai']);
Route::post('pay/ak/notify', [NotifyController::class, 'ak']);
Route::post('pay/guoguo/notify', [NotifyController::class, 'guoguo']);
Route::post('pay/lanyangyang/notify', [NotifyController::class, 'lanyangyang']);
Route::post('pay/lh/notify', [NotifyController::class, 'lh']);
Route::post('pay/lanbojini/notify', [NotifyController::class, 'lanbojini']);
Route::post('pay/xiyou1/notify', [NotifyController::class, 'xiyou1']);
Route::post('pay/hanyu/notify', [NotifyController::class, 'hanyu']);
Route::get('pay/jieda/notify', [NotifyController::class, 'jieda']);
Route::post('pay/xiyou2/notify', [NotifyController::class, 'xiyou2']);
Route::post('pay/changwei/notify', [NotifyController::class, 'changwei']);
Route::post('pay/xiyou3/notify', [NotifyController::class, 'xiyou3']);
Route::get('pay/xinfubao/notify', [NotifyController::class, 'xinfubao']);
Route::post('pay/xmzf/notify', [NotifyController::class, 'xmzf']);
Route::post('pay/fengzi/notify', [NotifyController::class, 'fengzi']);
Route::post('pay/aodi/notify', [NotifyController::class, 'aodi']);
Route::post('pay/konglong/notify', [NotifyController::class, 'konglong']);
Route::post('pay/jiale/notify', [NotifyController::class, 'jiale']);
Route::post('pay/dashaoye/notify', [NotifyController::class, 'dashaoye']);
Route::post('pay/dashaoye2/notify', [NotifyController::class, 'dashaoye2']);
Route::post('pay/huiceng/notify', [NotifyController::class, 'huiceng']);
Route::post('pay/hcdq/notify', [NotifyController::class, 'hcdq']);
Route::post('pay/yinfu/notify', [NotifyController::class, 'yinfu']);
Route::post('pay/tm/notify', [NotifyController::class, 'tm']);
Route::post('pay/xjhb/notify', [NotifyController::class, 'xjhb']);
Route::post('pay/hademen/notify', [NotifyController::class, 'hademen']);
Route::get('pay/baozi/notify', [NotifyController::class, 'baozi']);
Route::post('pay/huixin/notify', [NotifyController::class, 'huixin']);
Route::post('pay/huihui/notify', [NotifyController::class, 'huihui']);
Route::post('pay/huashun/notify', [NotifyController::class, 'huashun']);
Route::get('pay/heng/notify', [NotifyController::class, 'heng']);
Route::post('pay/fanggou/notify', [NotifyController::class, 'fanggou']);
Route::post('pay/yongheng/notify', [NotifyController::class, 'yongheng']);
Route::get('pay/fanggou/notify', [NotifyController::class, 'fanggou']);
Route::post('pay/order/test', [OrderController::class, 'createOrder']);
Route::post('pay/order/query', [OrderController::class, 'query']);
Route::post('pay/hui/notify', [NotifyController::class, 'hui']);
Route::get('pay/cszhifu/notify', [NotifyController::class, 'cszhifu']);
Route::post('pay/huiwxnf/notify', [NotifyController::class, 'huiwxnf']);
Route::post('pay/qingmu/notify', [NotifyController::class, 'qingmu']);
Route::post('pay/vzhifu/notify', [NotifyController::class, 'vzhifu']);
Route::match(['get', 'post'],'pay/huiwx99/notify', [NotifyController::class, 'huiwx99']);
Route::post('pay/ldpay/notify', [NotifyController::class, 'ldpay']);
Route::post('pay/baozi2/notify', [NotifyController::class, 'baozi2']);
Route::post('pay/huoyan/notify', [NotifyController::class, 'huoyan']);
Route::post('pay/shabaozi/notify', [NotifyController::class, 'shabaozi']);
Route::post('pay/sanqian/notify', [NotifyController::class, 'sanqian']);
Route::post('pay/shabaozi99/notify', [NotifyController::class, 'shabaozi99']);
Route::post('pay/changweipagou/notify', [NotifyController::class, 'changweipagou']);
Route::post('pay/wukong/notify', [NotifyController::class, 'wukong']);
Route::post('pay/tianshibaobao/notify', [NotifyController::class, 'tianshibaobao']);
Route::get('pay/maimaitong/notify', [NotifyController::class, 'maimaitong']);
Route::post('pay/wubai2/notify', [NotifyController::class, 'wubai2']);
Route::post('pay/ran/notify', [NotifyController::class, 'ran']);
Route::post('pay/canglei/notify', [NotifyController::class, 'canglei']);
Route::post('pay/mu/notify', [NotifyController::class, 'mu']);
Route::post('pay/xr/notify', [NotifyController::class, 'xr']);
Route::post('pay/xinfengye/notify', [NotifyController::class, 'xinfengye']);
Route::post('pay/yisheng/notify', [NotifyController::class, 'yisheng']);
Route::post('pay/taizi/notify', [NotifyController::class, 'taizi']);
Route::post('pay/lingdang/notify', [NotifyController::class, 'lingdang']);
Route::post('pay/xinhuafei/notify', [NotifyController::class, 'xinhuafei']);
Route::post('pay/dongfeng1/notify', [NotifyController::class, 'dongfeng1']);
Route::post('pay/xiaoyu/notify', [NotifyController::class, 'xiaoyu']);
Route::post('pay/mu22/notify', [NotifyController::class, 'mu22']);
Route::post('pay/nanwei/notify', [NotifyController::class, 'nanwei']);
Route::post('pay/shandian/notify', [NotifyController::class, 'shandian']);
Route::post('pay/bangde/notify', [NotifyController::class, 'bangde']);
Route::post('pay/changanxg/notify', [NotifyController::class, 'changanxg']);
Route::post('pay/abc/notify', [NotifyController::class, 'abc']);




Route::get('pay/jd/notify', [NewNotifyController::class, 'jd']);
Route::post('pay/jdkm/notify', [NewNotifyController::class, 'jdkm']);
Route::post('pay/sbjyg/notify', [NewNotifyController::class, 'sbjyg']);
Route::post('pay/yongheng2/notify', [NewNotifyController::class, 'yongheng2']);
Route::post('pay/canglei2/notify', [NewNotifyController::class, 'canglei2']);
Route::post('pay/xiaodi/notify', [NewNotifyController::class, 'xiaodi']);
Route::post('pay/changanheng/notify', [NewNotifyController::class, 'changanheng']);
Route::post('pay/lufei/notify', [NewNotifyController::class, 'lufei']);
Route::post('pay/jdkami/notify', [NewNotifyController::class, 'jdkami']);
Route::post('pay/baobaokm/notify', [NewNotifyController::class, 'baobaokm']);
Route::post('pay/tiantian/notify', [NewNotifyController::class, 'tiantian']);
Route::post('pay/ost/notify', [NewNotifyController::class, 'ost']);
Route::get('pay/boxin/notify', [NewNotifyController::class, 'boxin']);
Route::post('pay/jdb/notify', [NewNotifyController::class, 'jdb']);

Route::any('pay/shiguang/notify', [NewNotifyController::class, 'shiguang']);
Route::post('pay/baobaotb/notify', [NewNotifyController::class, 'baobaotb']);
Route::post('pay/guangguang/notify', [NewNotifyController::class, 'guangguang']);
Route::post('pay/huajie/notify', [NewNotifyController::class, 'huajie']);
Route::post('pay/qiaopai/notify', [NewNotifyController::class, 'qiaopai']);
Route::any('pay/shagou/notify', [NewNotifyController::class, 'shagou']);
Route::post('pay/laozhanyou/notify', [NewNotifyController::class, 'laozhanyou']);
Route::post('pay/sbpay/notify', [NewNotifyController::class, 'sbpay']);
Route::post('pay/mt/notify', [NewNotifyController::class, 'mt']);
Route::post('pay/xiaoniu/notify', [NewNotifyController::class, 'xiaoniu']);
Route::post('pay/baobaopay/notify', [NewNotifyController::class, 'baobaopay']);





Route::post('upstream/collection', [UpstreamController::class, 'collection']);

Route::resources([

    'user' => UserController::class,
    'init' => InitController::class,
    'merchant/channel' => MerchantChannelController::class,
    'pay' => PayController::class,
    'channel' => ChannelController::class,
    'upstream' => UpstreamController::class
]);

Route::post('merchant/channel/batch/rate', [MerchantChannelController::class, 'batchRate']);
Route::post('merchant/channel/rate', [MerchantChannelController::class, 'rate']);
Route::post('merchant/channel/status', [MerchantChannelController::class, 'status']);
Route::post('merchant/channel/set/status', [MerchantChannelController::class, 'setStatus']);
Route::post('merchant/channel/weight', [MerchantChannelController::class, 'weight']);
Route::get('pay/test/bot', [PayTestController::class, 'bot']);
Route::get('pay/test/bot1', [PayTestController::class, 'bot1']);
Route::get('dashboard/rate', [DashboardController::class, 'rate']);
Route::get('dashboard/down', [DashboardController::class, 'down']);
