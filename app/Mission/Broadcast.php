<?php

namespace App\Mission;
use App\Http\Controllers\Api\PayController;
use App\Models\AdvanceLog;
use App\Models\Order;
use App\Models\TelegramBookkeeping;
use App\Models\TelegramMerchant;
use App\Models\Upstream;
use App\Models\UpstreamChannel;
use Illuminate\Support\Facades\DB;
use  Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;

class Broadcast {
    public function apprise() {
//        $total = Redis::llen('feedback_pool');
        $total = 200;
        for($i = 1; $i <= $total; $i++) {
            $data = Redis::blpop('feedback_pool', 1);
            if(empty($data)) {
//                sleep(2);
                continue;
            }
            $data = json_decode($data[1]);

            if(isset($data->time)) {
                if((time() - $data->time) < 10) {
                    Redis::rpush('feedback_pool',json_encode([
                        'number' => 10,
                        'time' => $data->time,
                        'order_id' => $data->order_id
                    ]));
                    continue;
                }

            }

            $number = $data->number - 1;
            $PayController = new PayController();
            if($PayController->apprise($data->order_id) != true) {
                if($number > 0) {
                    Redis::rpush('feedback_pool',json_encode([
                        'number' => $number,
                        'time' => time(),
                        'order_id' => $data->order_id
                    ]));
                }

            }
        }

    }

    public function supplement() {
        $orderModel = new Order();
//        $date = time()-10;
        $orders = $orderModel->where('status', 1)
//            ->where('pay_time','<',$date)
            ->get();
        foreach ($orders as $order) {
            $PayController = new PayController();
            $PayController->apprise($order->id);
        }
    }

    public function bot() {
        for($i = 0; $i <= 30; $i++) {
            $data = Redis::blpop('error_log', 1);
            if(isset($data)) {
                $data = json_decode($data[1], true);
                if(isset($data['para'])) {
                    $para = json_decode($data['para'],1);
                } else {
                    $para = '';
                }

                $error = json_decode($data['data'],1);
                $channel_id = $data['channel_id'];
                $channelModel = new UpstreamChannel();
                $channel = $channelModel->find($channel_id);
                $word = '########「'.$channel->name.'」########'."\n";
                if(!empty($para)){
                    $word = $word.'请求参数:'.json_encode($para,JSON_UNESCAPED_UNICODE)."\n";
                }
                $word = $word.'响应结果:'.json_encode($error,JSON_UNESCAPED_UNICODE)."\n";
                $token = config('services.telegram-bot-api.token');
                $url = 'https://api.telegram.org/bot'.$token.'/sendMessage';
                $parameters = [
                    'chat_id' => '-777614738',
                    'text' => $word
                ];
                Http::post($url, $parameters);
            }
            sleep(2);
        }


    }

    public function debt()
    {
        $start_date = date('Y-m-d');
        $end_date = $start_date;
        $upstreamModel = new Upstream();
        $upstreams = $upstreamModel->where('status', 1)->where('collection', 1)->get();

        foreach($upstreams as $upstream) {
            if (Order::upstreamDetail($upstream->id, $start_date, $end_date)->original_amount == 0 &&
                Order::upstreamDetail($upstream->id, $start_date, $end_date)->amount == 0 &&
                empty(Order::log_change($upstream->id, $start_date, $end_date))
            ) {
                continue;
            }

            $amount = Order::log_change($upstream->id, $start_date, $end_date);
            $amount = empty($amount) ? '0' : $amount;
            $tMerchantModel = new TelegramMerchant();
            $tMerchant = $tMerchantModel->where('customer_id', $upstream->id)
                ->where('type', 1)->first();

            $remaining = $amount - Order::upstreamDetail($upstream->id, $start_date, $end_date)->amount;

            if ($remaining <= 500000 && $remaining > 0) {

                $word = '尊敬的「' . $upstream->name . '」您好，您现在预付金低于5000.00元';
                $word .= '现有预付金' . sprintf("%.2f", $remaining / 100) . '元，为了防止被系统自动切停请及时下发！！！';
                $token = config('services.telegram-bot-api.newtoken');
                $url = 'https://api.telegram.org/bot' . $token . '/sendMessage';
                $parameters = [
                    'chat_id' => $tMerchant->chat_id,
                    'text' => $word
                ];
                Http::post($url, $parameters);
            }
        }
    }

    public function channelRefresh()
    {
        $start_date = date('Y-m-d');
        $end_date = $start_date;
        $upstreamModel = new Upstream();
        $upstreams = $upstreamModel->where('status', 1)->where('collection', 1)->get();

        foreach($upstreams as $upstream) {
            if (Order::upstreamDetail($upstream->id, $start_date, $end_date)->original_amount == 0 &&
                Order::upstreamDetail($upstream->id, $start_date, $end_date)->amount == 0 &&
                empty(Order::log_change($upstream->id, $start_date, $end_date))
            ) {
                continue;
            }

            $amount = Order::log_change($upstream->id, $start_date, $end_date);
            $amount = empty($amount) ? '0' : $amount;


            $remaining = $amount - Order::upstreamDetail($upstream->id, $start_date, $end_date)->amount;

            if ($remaining < 0) {
                $upChanelModel = new UpstreamChannel();
                $upChanelModel->where('upstream_id', $upstream->id)->update([
                    'status' => 0
                ]);

            }
        }
    }

    public function BotRefresh() {
        for ($i=0; $i<3; $i++) {
            $token = config('services.telegram-bot-api.token');
            $url = 'https://api.telegram.org/bot'.$token.'/setWebhook';
            $parameters = [
                'url' => 'https://bot.facaila2022.com/api/service/order',
                'drop_pending_updates' => 1
            ];
//        Http::post($url, $parameters);

            $newtoken = config('services.telegram-bot-api.newtoken');
            $url = 'https://api.telegram.org/bot'.$newtoken.'/setWebhook';
            $parameters = [
                'url' => 'https://bot.facaila2022.com/api/advance',
                'drop_pending_updates' => 1
            ];
            Http::get($url, $parameters);
            sleep(20);
            $i++;
        }





    }

    public function chatRefresh() {
        $tMerchantModel = new TelegramMerchant();
        $tMerchantModel->where('id','>',0)->update([
            'mark' => 0,
            'remaining' => 0
        ]);
        $start_date = strtotime(date('Y-m-d',strtotime('-2 day')));
        $bookkeepingModel = new TelegramBookkeeping();
        $bookkeepingModel->where('created', '<=', $start_date)
            ->delete();




    }

    public function redisRefresh()
    {
        $keys = Redis::keys('query*');
        foreach ($keys as $key) {
            $k = explode('laravel_database_', $key);
//            echo $k[1]."\n";
            Redis::del($k[1]);
        }



    }



}
