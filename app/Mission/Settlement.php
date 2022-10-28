<?php

namespace App\Mission;
use App\Http\Controllers\Api\PayController;
use App\Models\AdvanceLog;
use App\Models\Merchant;
use App\Models\MerchantAdvance;
use App\Models\MerchantChannel;
use App\Models\Order;
use App\Models\OrderLog;
use App\Models\TelegramBookkeeping;
use App\Models\TelegramMerchant;
use App\Models\Upstream;
use App\Models\UpstreamChannel;
use  Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;

class Settlement {
    protected  $token = '';

    public function __construct()
    {
        $this->token = config('services.telegram-bot-api.newtoken');
    }


    public function index($chat_id) {
        $tMerchantModel = new TelegramMerchant();
        $tMerchantModel->where('id', $chat_id)->update([
            'mark'=>1
        ]);
        $this->doit($chat_id);
        die;



    }




    public function doit($chat_id) {
        $url = 'https://api.telegram.org/bot'.$this->token.'/sendMessage';
        $tMerchantModel = new TelegramMerchant();
        $tMerchant = $tMerchantModel->where('id', $chat_id)
            ->first();
        $chat_id = $tMerchant->chat_id;
        if($tMerchant->type == 1) {
            $text = $this->_up($tMerchant->customer_id);
        } elseif($tMerchant->type == 2) {
            $text = $this->_down($tMerchant->customer_id);
        }


        if(empty($text)){
            $parameters = [
                'chat_id' => $chat_id,
                'text' => '没有结算信息'
            ];
            Http::post($url, $parameters);


        } else {
            $tMerchantModel->where('id', $tMerchant->id)->update([
                'remaining' => $text['amount']
            ]);
            $parameters = [
                'chat_id' => $chat_id,
                'text' => $text['text']
            ];
            $res = Http::post($url, $parameters);
            $res = $res->json();

            $parameters = [
                'chat_id' => $chat_id,
                'message_id' =>  $res['result']['message_id']
            ];
            $url = 'https://api.telegram.org/bot'.$this->token.'/pinChatMessage';
            Http::post($url, $parameters);

//            $this->issued($chat_id, $text['amount']);
        }
    }

    private function _up($id) {
        $start_date = date('Y-m-d');
        $end_date = $start_date;
        $upstreamModel = new Upstream();
        $upstream = $upstreamModel->where('id', $id)->first();

        $text = '';
        $text .= $end_date.'账单明细:'."\n".
            '上游:'.$upstream->name."\n";
        $total_pay_amount = 0;
        foreach(Order::detail($upstream->id, $start_date, $end_date) as $item) {
            $text .= UpstreamChannel::obj($item->channel_id)->name."\n".
                '跑量:'.sprintf("%.2f",$item->original_amount/100)."\n";
            $rate = UpstreamChannel::obj($item->channel_id)->rate;
            $text .= '费率:'.($rate/10).'%'."\n";
            $pay_amount = ($item->original_amount - $item->original_amount*($rate/1000))/100;
            $total_pay_amount = $total_pay_amount + $pay_amount;
            $text .= '应结算:'.sprintf("%.2f",$pay_amount)."\n";
        }
        $amount = Order::log_change($upstream->id, $start_date, $end_date)/100;
        $amount = empty($amount)?'0.00':$amount;
        $text .= '跑量合计:'.sprintf("%.2f",Order::upstreamDetail($upstream->id, $start_date, $end_date)->original_amount/100)."\n".
            '应结算合计:'.sprintf("%.2f",$total_pay_amount)."\n".
            '已预付:'.sprintf("%.2f",$amount)."\n".
            '剩余应结算:'.sprintf("%.2f",$amount).' - '.
            sprintf("%.2f",$total_pay_amount).' = '.sprintf("%.2f",$amount - $total_pay_amount)."\n"."\n";



        $new = $amount - $total_pay_amount;

        return [
            'text'=>$text,
            'amount' => $new*100
        ];

    }

    private function _down($id) {
        $start_date = date('Y-m-d');
        $end_date = $start_date;
        $merchantChannelModel = new MerchantChannel();
        $merchantChannel =  $merchantChannelModel->where('merchant_id', $id)
            ->first();

        if(!empty(Order::merchantOriginalAmount($merchantChannel->merchant->account, $start_date, $end_date))) {
            $originalAmount = sprintf("%.2f", Order::merchantOriginalAmount($merchantChannel->merchant->account, $start_date, $end_date)->amount/100);
        } else {
            $originalAmount = '0.00';
        }
        $pay_amount_total = 0;
        $text = '商户:'.$merchantChannel->merchant->name."\n"."\n";
        foreach(Order::merchantDetail($merchantChannel->merchant->account, $start_date, $end_date) as $item) {
            $text .= $item->merchantChannel->channel->name."\n";
            $text .= '跑量:'.sprintf("%.2f", $item->original_amount/100)."\n";
            $text .= '费率:'.($item->merchantChannel->rate/10).'%'."\n";
            $pay_amount = ($item->original_amount - ($item->original_amount*($item->merchantChannel->rate/1000)))/100;
            $pay_amount_total = $pay_amount_total + $pay_amount;
            $text .= '应结算:'.sprintf("%.2f", $pay_amount)."\n";
        }
        $advance = $merchantChannel->merchant->advance($merchantChannel->merchant->id, $start_date, $end_date);
        $text .= '跑量合计:'.$originalAmount."\n";
        $text .= '应结算合计:'.$pay_amount_total."\n";
        $text .= '已预付:'.sprintf("%.2f", $advance/100)."\n";
        $text .= '剩余应结算:'.sprintf("%.2f", $advance/100).
            ' - '. $pay_amount_total. ' = '.
            sprintf("%.2f", $advance/100 - $pay_amount_total);



        $new = $advance - ($pay_amount_total*100);
        return [
            'text'=>$text,
            'amount' => $new
        ];

    }


    public function issued($chat_id) {
        $name = '宇宙机器人';
        $tMerchantModel = new TelegramMerchant();
        $tMerchantModel->where('id', $chat_id)->update([
            'mark'=>2
        ]);
        $tMerchant = $tMerchantModel->where('id', $chat_id)
            ->first();
        if(empty($tMerchant)){
            return ;
        }
        $bookkeepingModel = new TelegramBookkeeping();

        $bookkeepingModel->chat_id = $tMerchant->chat_id;
        $bookkeepingModel->customer_id = $tMerchant->customer_id;
        $bookkeepingModel->type = $tMerchant->type;
        $bookkeepingModel->genre = 1;
        $bookkeepingModel->amount = $tMerchant->remaining;
        $bookkeepingModel->name = $name;

        $bookkeepingModel->note = '机器人初始化结余预付';
        $bookkeepingModel->created = time();
        $bookkeepingModel->save();

        $text_c = '';
        if($tMerchant->type == 1) {
            $text_c = $this->upUpdate($tMerchant->remaining/100, $tMerchant->customer_id);
        } elseif($tMerchant->type == 2) {
            $text_c = $this->dowmUpdate($tMerchant->remaining/100, $tMerchant->customer_id);
        }

        $text = $this->bookkeeping($tMerchant->chat_id);
        $text = $text."\n"."\n".$text_c;

        $url = 'https://api.telegram.org/bot'.$this->token.'/sendMessage';
        $parameters = [
            'chat_id' => $tMerchant->chat_id,
            'text' => $text
        ];
        $res = Http::post($url, $parameters);


        $res = $res->json();

        $parameters = [
            'chat_id' => $tMerchant->chat_id,
            'message_id' =>  $res['result']['message_id']
        ];
        $url = 'https://api.telegram.org/bot'.$this->token.'/pinChatMessage';
        Http::post($url, $parameters);


        $parameters = [
            'chat_id' => $tMerchant->chat_id,
            'voice' =>  'AwACAgUAAxkBAAEBUu9jV9cT2DynUUTQfE86xW7Kqrx3MQAC4gYAAo7OuFZwYoq0OpSg-yoE',
            'caption' => '【發財--通道通知】'."\n".'通道已打开，请加大并发，'."\n".'请加大并发，请加大并发，请加大并发！！！！',
            'protect_content' => true
        ];

        $url = 'https://api.telegram.org/bot'.$this->token.'/sendVoice';
        Http::post($url, $parameters);


    }


    private function upUpdate($row, $id) {
        $start_date = date('Y-m-d',strtotime('-1 day'));

        $end_date = $start_date;

        $upstreamModel = new Upstream();
        $upstream = $upstreamModel->where('id', $id)->first();
        if(empty($upstream)) {
            return false;
        }


        $logModel = new AdvanceLog();
        $logModel->upstream_id = $id;
        $logModel->user_id = 1;
        $logModel->amount = abs($row) * 100;
        $type = 1;
        if(strpos($row,'+') !== false) {
            $type = 1;
        } elseif(strpos($row,'-') !== false) {
            $type = 2;
        }
        $logModel->type = $type;
        $logModel->balance = 0;
        $logModel->created = time();
        $logModel->save();
        $amount = Order::log_change($upstream->id, $start_date, $end_date)/100;
        $amount = empty($amount)?'0.00':$amount;
        $balance = sprintf("%.2f",$amount - (Order::upstreamDetail($upstream->id, $start_date, $end_date)->amount/100));
        $text = '['.$upstream->name.']预付剩余:'.$balance;

        return $text;
    }

    private function dowmUpdate($row, $id) {
        $start_date = date('Y-m-d',strtotime('-1 day'));

        $end_date = $start_date;



        $merchantModel = new Merchant();
        $merchant = $merchantModel->where('id', $id)->first();
        if(empty($merchant)) {
            return false;
        }
        $advanceModel = new MerchantAdvance();
        $advanceModel->merchant_id = $merchant->id;
        $advanceModel->amount = abs($row)*100;
        $advanceModel->user_id = 1;
        $type = 1;
        if(strpos($row,'+') !== false) {
            $type = 1;
        } elseif(strpos($row,'-') !== false) {
            $type = 2;
        }
        $advanceModel->type = $type;
        $advanceModel->recharge_time = time();
        $advanceModel->balance = ($merchant->balance/100 + $row)*100;

        $advanceModel->created = time();
        $advanceModel->save();



        if(!empty(Order::merchantAmount($merchant->account, $start_date, $end_date))){
            $merchantAmount = Order::merchantAmount($merchant->account, $start_date, $end_date)->merchant_amount/100;
        }else {
            $merchantAmount = 0;
        }

        $balance = ($merchant->advance($merchant->id, $start_date, $end_date)/100) - $merchantAmount;




        $orderLogModel = new OrderLog();
        $orderLogModel->merchant_id = $merchant->id;
        $orderLogModel->attribute = 2;
        $orderLogModel->type = 1;
        $orderLogModel->amount = $row*100;
        $orderLogModel->before_balance = ($balance - $row) * 100;
        $orderLogModel->balance = $balance * 100;
        $orderLogModel->note = '宇宙机器人入账';
        $orderLogModel->created = time();
        $orderLogModel->save();
        $merchantModel->where('id', $merchant->id)->update([
            'balance' => $balance
        ]);


        $balance = sprintf("%.2f", $balance);
        $text = '['.$merchant->name.']预付剩余:'.$balance;

        return $text;
    }


    private function bookkeeping($chat_id) {
        $start_date = date('Y-m-d');
        $end_date = $start_date;
        $start_time = strtotime($start_date.' 00:00:00');
        $end_time = strtotime($end_date.' 23:59:59');
        $bookkeepingModel = new TelegramBookkeeping();
        $bookkeepings = $bookkeepingModel->where('chat_id', $chat_id)
            ->where('genre', 1)
            ->where('created', '>=', $start_time)
            ->where('created', '<=', $end_time)
            ->get();
        $total = 0;
        $tmp = '';
        foreach ($bookkeepings as $bookkeeping) {
            $tmp .= '['.date('H:i',$bookkeeping->created ).'] '.sprintf("%.2f", $bookkeeping->amount/100)
                .'  '.$bookkeeping->name;
            if(!empty($bookkeeping->note)) {
                $tmp .= '    '.$bookkeeping->note."\n";
            } else {
                $tmp .= "\n";
            }
            $total = $total + sprintf("%.2f", $bookkeeping->amount/100);
        }
        $text = '下发共计：'.sprintf("%.2f", $total).'        '."\n";
        $text .= $tmp."\n"."\n";



        $bookkeepings = $bookkeepingModel->where('chat_id', $chat_id)
            ->where('genre', 2)
            ->where('created', '>=', $start_time)
            ->where('created', '<=', $end_time)
            ->get();
        $tmp = '';
        $total = 0;
        foreach ($bookkeepings as $bookkeeping) {
            $tmp .= '['.date('H:i',$bookkeeping->created ).'] '.sprintf("%.2f", $bookkeeping->amount/100)
                .'  '.$bookkeeping->name;
            if(!empty($bookkeeping->note)) {
                $tmp .= '    '.$bookkeeping->note."\n";
            } else {
                $tmp .= "\n";
            }
            $total = $total + sprintf("%.2f", $bookkeeping->amount/100);
        }
        $text .= '记账共计：'.sprintf("%.2f", $total).'        '."\n";
        $text .= $tmp."\n";
        return $text;
    }
}
