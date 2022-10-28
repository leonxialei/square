<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\MerchantChannel;
use App\Models\UpstreamChannel;
use Illuminate\Http\Request;
use App\Models\Merchant;
use Illuminate\Support\Str;


class MerchantChannelController extends Controller
{
    public function status(Request $request) {
        if(!empty($request->get('id'))) {
            $merchantChannelModel = new MerchantChannel();
            $merchantChannel = $merchantChannelModel->where('id', $request->get('id'))->first();
            if(!empty($merchantChannel)) {
                $merchantChannelModel->where('id', $request->get('id'))->update([
                    'status' => $request->get('type'),
                ]);
                return [
                    'code' => 200,
                    'result' => true,
                    'msg' => '更新成功！'
                ];
            }
        }
    }

    public function weight(Request $request) {
        $merchantChannelModel = new MerchantChannel();
        $merchantChannel = $merchantChannelModel->where('id', $request->get('id'))->first();
        if(!empty($merchantChannel)) {
            $merchantChannelModel->where('id', $request->get('id'))->update([
                'weight' => $request->get('weight'),
            ]);
            return [
                'code' => 200,
                'result' => true,
                'msg' => '更新成功！'
            ];
        }
    }

    public function setStatus(Request $request) {
        $merchantChannelModel = new MerchantChannel();
        if($request->get('type') == 3) {
            $merchantChannelModel->where('status', 1)->update([
                'status' => 0,
            ]);
        }
        parse_str($request->get('params'),$params);
        foreach($params as $key => $value) {
            if(!empty($value)) {
                $merchantChannelModel = $merchantChannelModel->where($key, $value);
            }
        }
        $merchantChannelModel = $merchantChannelModel->where('created', '>', 0);
        if($request->get('type') == 1) {
            $merchantChannelModel->update([
                'status' => 1,
            ]);
        } elseif($request->get('type') == 2) {
            $merchantChannelModel->update([
                'status' => 0,
            ]);
        }
        return [
            'code' => 200,
            'result' => true,
            'msg' => '更新成功！'
        ];
    }

    public function rate(Request $request) {
        if(!empty($request->get('id'))) {
            $merchantChannelModel = new MerchantChannel();
            $merchantChannel = $merchantChannelModel->where('id', $request->get('id'))->first();
            if(!empty($merchantChannel)) {
                $merchantChannelModel->where('id', $request->get('id'))->update([
                    'rate' => $request->get('rate'),
                ]);
                return [
                    'code' => 200,
                    'result' => true,
                    'msg' => '更新成功！'
                ];
            }
        }
    }

    public function batchRate(Request $request) {
        $upstreamChannelModel = new UpstreamChannel();
        $channel = $upstreamChannelModel->where('id', $request->get('id'))->first();
        if(empty($channel)) {
            return [
                'code' => 3001,
                'result' => false,
                'msg' => '没用这个通道！'
            ];
        }
        $merchantChannelModel = new MerchantChannel();
        $merchantChannelModel->where('channel_id', $channel->id)->update([
            'rate' => $request->get('rate'),
        ]);
        return [
            'code' => 200,
            'result' => true,
            'msg' => '更新成功！'
        ];
    }

}
