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

class DailyCut {
    protected  $token = '';

    public function __construct()
    {
        $this->token = config('services.telegram-bot-api.newtoken');
    }

    public function message() {
        $url = 'https://api.telegram.org/bot'.$this->token.'/sendVideo';
        $this->_pushChannelData();

        $merchantChannelModel = new MerchantChannel();
        $merchantChannelModel->where('id', '!=', 0)->update([
            'status' => 0
        ]);
        $tMerchantModel = new TelegramMerchant();
        $tMerchants = $tMerchantModel->get();
        foreach ($tMerchants as $tMerchant) {
            $parameters = [
                'chat_id' => $tMerchant->chat_id,
                'video' => 'BAACAgUAAxkBAALVlWNIGje0Zf9HPJFUbWw2Pa4OJFBEAAKtCAAC_pSRVcMV32UhRVYvKgQ',
                'caption' => '所有通道日切'."\n".'感谢各位老板美好的一天陪伴'."\n".'明天会越来越好❤️❤️❤️'
            ];
            Http::post($url, $parameters);
        }
        die;
    }

    public function openChannel() {
        $merchantData = Redis::get('merchantData');
//        dd($merchantData);
        if(empty($merchantData)) {
            die;
        }
        $merchantData = json_decode($merchantData, true);
        foreach ($merchantData as $row) {
            $merchantChannelModel = new MerchantChannel();
            $merchantChannelModel->where('id', $row['id'])->update([
                'status' => $row['status']
            ]);
        }
        Redis::del('merchantData');
    }



    private function _pushChannelData() {

        $merchantChannelModel = new MerchantChannel();
        $merchantChannels = $merchantChannelModel->get();
        $merchantData = [];
        foreach ($merchantChannels as $key => $channel) {
            $merchantData[$key]['id'] = $channel->id;
            $merchantData[$key]['status'] = $channel->status;
        }
        $merchantData = json_encode($merchantData);
        Redis::del('merchantData');
        Redis::set('merchantData', $merchantData);
    }
}
