<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UpstreamChannel;
use App\Models\MerchantChannel;


class ChannelController extends Controller
{
    public function destroy($id) {
        $upstreamChannelModel = new UpstreamChannel();
        $upstreamChanne = $upstreamChannelModel->where('id', $id)->first();
        if(empty($upstreamChanne)) {
            return false;
        }
        $upstreamChannelModel->where('id', $id)->update([
            'status' => 0,
            'is_disabled' => 1
        ]);
        $merchantChannelModel = new MerchantChannel();
        $merchantChannelModel->where('channel_id', $id)->update([
            'status' => 0,
            'is_disabled' => 1
        ]);
        return true;
    }
    public function upstream($id) {
        $merchantChannelModel = new MerchantChannel();
        $merchantChannel = $merchantChannelModel->where('id', $id)->first();
        if(empty($merchantChannel)) {
            return false;
        }

        $merchantChannelModel->where('id', $id)->update([
            'status' => 0,
            'is_disabled' => 1
        ]);
        return true;
    }

}
