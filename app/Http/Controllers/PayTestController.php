<?php

namespace App\Http\Controllers;

use App\Help\Sign;
use App\Http\Controllers\Controller;
use App\Models\Merchant;
use App\Models\MerchantChannel;
use App\Models\Order;
use App\Models\UpstreamChannel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use  Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Crypt;

class PayTestController extends Controller
{
    public function order(Request $request) {
//        $merchantChannelModel = new MerchantChannel();
//        $merchantChannel = $merchantChannelModel->where('merchant_id', 30)
//            ->

        $upstreamChannelModel = new UpstreamChannel();
        $upstreamChannels = $upstreamChannelModel
            ->where('status',1)
            ->where('is_disabled',0)
            ->get();


        $errors = [];
        $data = [
            'errors' => $errors,
            'upstreamChannels' => $upstreamChannels
        ];
        return View('test.order', $data);





    }

    public function create($merchant_id, $code) {

    }

    public function testcreate($param) {

        $id = Crypt::decryptString($param);

        $upstreamChannelModel = new UpstreamChannel();
        $upstreamChannel = $upstreamChannelModel->find($id);

        $data = [
            'upstreamChannel' => $upstreamChannel
        ];
        return View('test.ordertest', $data);
    }





}
