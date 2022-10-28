<?php

namespace App\Http\Controllers\Api;

use App\Help\Sign;
use App\Http\Controllers\Controller;
use App\Models\AdvanceLog;
use App\Models\Merchant;
use App\Models\MerchantAdvance;
use App\Models\MerchantChannel;
use App\Models\Order;
use App\Models\UpstreamChannel;
use App\Models\Upstream;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;
class NewNotifyController extends Controller
{

    public function jd(Request $request)
    {
        //        $upstreamModel = new Upstream();
        //        $upstream = $upstreamModel->where('ip', $_SERVER['REMOTE_ADDR'])->first();
        //        if(empty($upstream)) {
        //            return [
        //                'status' => '30009',
        //                'msg' => 'IP restrictions'
        //            ];
        //        }
        //        $modelName = 'App\Upstream'.'\\'.ucfirst($upstream->en_name);

        $modelName = 'App\Upstream' . '\\' . 'Jd';
        $model = new $modelName;
        $res = $model->notify($request);
        if (isset($res['status']) && $res['status'] == 30003) {
            return [
                'status' => '30003',
                'msg' => 'Signature error'
            ];
        }
        if ($res && $res->status != 2) {
            $balance = $res->upstream->balance - $res->amount;
            $upstreamModel = new Upstream();
            $upstreamModel->where('id', $res->upstream_id)->update([
            'balance' => $balance
        ]);


        $mbalance = $res->customer->balance - $res->merchant_amount;
        $merchantModel = new Merchant();
        $merchantModel->where('id', $res->customer->id)->update([
        'balance' => $mbalance
        ]);


        $data = json_encode([
        'number' => 5,
        'order_id' => $res->id
        ]);
        Redis::rpush('feedback_pool', $data);


        return 'ok';
        }
            if($res->status == 2) {
            return 'ok';
        }
    }

    public function jdkm(Request $request)
    {
        //        $upstreamModel = new Upstream();
        //        $upstream = $upstreamModel->where('ip', $_SERVER['REMOTE_ADDR'])->first();
        //        if(empty($upstream)) {
        //            return [
        //                'status' => '30009',
        //                'msg' => 'IP restrictions'
        //            ];
        //        }
        //        $modelName = 'App\Upstream'.'\\'.ucfirst($upstream->en_name);
        $modelName = 'App\Upstream' . '\\' . 'Jdkm';
        $model = new $modelName;
        $res = $model->notify($request);
        if (isset($res['status']) && $res['status'] == 30003) {
            return [
                'status' => '30003',
                'msg' => 'Signature error'
            ];
        }
        if ($res && $res->status != 2) {
            $balance = $res->upstream->balance - $res->amount;
            $upstreamModel = new Upstream();
            $upstreamModel->where('id', $res->upstream_id)->update([
                'balance' => $balance
            ]);


            $mbalance = $res->customer->balance - $res->merchant_amount;
            $merchantModel = new Merchant();
            $merchantModel->where('id', $res->customer->id)->update([
                'balance' => $mbalance
            ]);


            $data = json_encode([
                'number' => 5,
                'order_id' => $res->id
            ]);
            Redis::rpush('feedback_pool', $data);


            return 'success';
        }
        if($res->status == 2) {
            return 'success';
        }
    }

    public function sbjyg(Request $request)
    {
//        $upstreamModel = new Upstream();
//        $upstream = $upstreamModel->where('ip', $_SERVER['REMOTE_ADDR'])->first();
//        if(empty($upstream)) {
//            return [
//                'status' => '30009',
//                'msg' => 'IP restrictions'
//            ];
//        }
//        $modelName = 'App\Upstream'.'\\'.ucfirst($upstream->en_name);
//        error_log(print_r($request->all(),1),3,'hw.txt');
        $modelName = 'App\Upstream' . '\\' . 'Suibianjiaoyige';
        $model = new $modelName;
        $res = $model->notify($request);
        if (isset($res['status']) && $res['status'] == 30003) {
            return [
                'status' => '30003',
                'msg' => 'Signature error'
            ];
        }
        if ($res && $res->status != 2) {
            $balance = $res->upstream->balance - $res->amount;
            $upstreamModel = new Upstream();
            $upstreamModel->where('id', $res->upstream_id)->update([
                'balance' => $balance
            ]);


            $mbalance = $res->customer->balance - $res->merchant_amount;
            $merchantModel = new Merchant();
            $merchantModel->where('id', $res->customer->id)->update([
                'balance' => $mbalance
            ]);


            $data = json_encode([
                'number' => 5,
                'order_id' => $res->id
            ]);
            Redis::rpush('feedback_pool', $data);


            return 'success';
        }
    }

    public function yongheng2(Request $request)
    {
//        $upstreamModel = new Upstream();
//        $upstream = $upstreamModel->where('ip', $_SERVER['REMOTE_ADDR'])->first();
//        if(empty($upstream)) {
//            return [
//                'status' => '30009',
//                'msg' => 'IP restrictions'
//            ];
//        }
//        $modelName = 'App\Upstream'.'\\'.ucfirst($upstream->en_name);
        $modelName = 'App\Upstream' . '\\' . 'Yongheng2';
        $model = new $modelName;
        $res = $model->notify($request);
        if (isset($res['status']) && $res['status'] == 30003) {
            return [
                'status' => '30003',
                'msg' => 'Signature error'
            ];
        }
        if ($res && $res->status != 2) {
            $balance = $res->upstream->balance - $res->amount;
            $upstreamModel = new Upstream();
            $upstreamModel->where('id', $res->upstream_id)->update([
                'balance' => $balance
            ]);


            $mbalance = $res->customer->balance - $res->merchant_amount;
            $merchantModel = new Merchant();
            $merchantModel->where('id', $res->customer->id)->update([
                'balance' => $mbalance
            ]);


            $data = json_encode([
                'number' => 5,
                'order_id' => $res->id
            ]);
            Redis::rpush('feedback_pool', $data);


            return 'OK';
        }
    }

    public function canglei2(Request $request)
    {
//        $upstreamModel = new Upstream();
//        $upstream = $upstreamModel->where('ip', $_SERVER['REMOTE_ADDR'])->first();
//        if(empty($upstream)) {
//            return [
//                'status' => '30009',
//                'msg' => 'IP restrictions'
//            ];
//        }
//        $modelName = 'App\Upstream'.'\\'.ucfirst($upstream->en_name);
        $modelName = 'App\Upstream' . '\\' . 'Canglei2';
        $model = new $modelName;
        $res = $model->notify($request);
        if (isset($res['status']) && $res['status'] == 30003) {
            return [
                'status' => '30003',
                'msg' => 'Signature error'
            ];
        }
        if ($res && $res->status != 2) {
            $balance = $res->upstream->balance - $res->amount;
            $upstreamModel = new Upstream();
            $upstreamModel->where('id', $res->upstream_id)->update([
                'balance' => $balance
            ]);


            $mbalance = $res->customer->balance - $res->merchant_amount;
            $merchantModel = new Merchant();
            $merchantModel->where('id', $res->customer->id)->update([
                'balance' => $mbalance
            ]);


            $data = json_encode([
                'number' => 5,
                'order_id' => $res->id
            ]);
            Redis::rpush('feedback_pool', $data);


            return 'ok';
        }
    }

    public function xiaodi(Request $request)
    {
//        $upstreamModel = new Upstream();
//        $upstream = $upstreamModel->where('ip', $_SERVER['REMOTE_ADDR'])->first();
//        if(empty($upstream)) {
//            return [
//                'status' => '30009',
//                'msg' => 'IP restrictions'
//            ];
//        }
//        $modelName = 'App\Upstream'.'\\'.ucfirst($upstream->en_name);
        $modelName = 'App\Upstream' . '\\' . 'Xiaodi';
        $model = new $modelName;
        $res = $model->notify($request);
        if (isset($res['status']) && $res['status'] == 30003) {
            return [
                'status' => '30003',
                'msg' => 'Signature error'
            ];
        }
        if ($res && $res->status != 2) {
            $balance = $res->upstream->balance - $res->amount;
            $upstreamModel = new Upstream();
            $upstreamModel->where('id', $res->upstream_id)->update([
                'balance' => $balance
            ]);


            $mbalance = $res->customer->balance - $res->merchant_amount;
            $merchantModel = new Merchant();
            $merchantModel->where('id', $res->customer->id)->update([
                'balance' => $mbalance
            ]);


            $data = json_encode([
                'number' => 5,
                'order_id' => $res->id
            ]);
            Redis::rpush('feedback_pool', $data);


            return 'success';
        }
    }

    public function changanheng(Request $request)
    {
//        $upstreamModel = new Upstream();
//        $upstream = $upstreamModel->where('ip', $_SERVER['REMOTE_ADDR'])->first();
//        if(empty($upstream)) {
//            return [
//                'status' => '30009',
//                'msg' => 'IP restrictions'
//            ];
//        }
//        $modelName = 'App\Upstream'.'\\'.ucfirst($upstream->en_name);
//        error_log(print_r($request->all(),1),3,'changanheng.txt');
        $modelName = 'App\Upstream' . '\\' . 'Changanheng';
        $model = new $modelName;
        $res = $model->notify($request);
        if (isset($res['status']) && $res['status'] == 30003) {
            return [
                'status' => '30003',
                'msg' => 'Signature error'
            ];
        }
        if ($res && $res->status != 2) {
            $balance = $res->upstream->balance - $res->amount;
            $upstreamModel = new Upstream();
            $upstreamModel->where('id', $res->upstream_id)->update([
                'balance' => $balance
            ]);


            $mbalance = $res->customer->balance - $res->merchant_amount;
            $merchantModel = new Merchant();
            $merchantModel->where('id', $res->customer->id)->update([
                'balance' => $mbalance
            ]);


            $data = json_encode([
                'number' => 5,
                'order_id' => $res->id
            ]);
            Redis::rpush('feedback_pool', $data);


            return 'success';
        }
    }

    public function lufei(Request $request)
    {
//        $upstreamModel = new Upstream();
//        $upstream = $upstreamModel->where('ip', $_SERVER['REMOTE_ADDR'])->first();
//        if(empty($upstream)) {
//            return [
//                'status' => '30009',
//                'msg' => 'IP restrictions'
//            ];
//        }
//        $modelName = 'App\Upstream'.'\\'.ucfirst($upstream->en_name);
        $modelName = 'App\Upstream' . '\\' . 'Lufei';
        $model = new $modelName;
        $res = $model->notify($request);
        if (isset($res['status']) && $res['status'] == 30003) {
            return [
                'status' => '30003',
                'msg' => 'Signature error'
            ];
        }
        if ($res && $res->status != 2) {
            $balance = $res->upstream->balance - $res->amount;
            $upstreamModel = new Upstream();
            $upstreamModel->where('id', $res->upstream_id)->update([
                'balance' => $balance
            ]);


            $mbalance = $res->customer->balance - $res->merchant_amount;
            $merchantModel = new Merchant();
            $merchantModel->where('id', $res->customer->id)->update([
                'balance' => $mbalance
            ]);


            $data = json_encode([
                'number' => 5,
                'order_id' => $res->id
            ]);
            Redis::rpush('feedback_pool', $data);


            return 'OK';
        }
    }

    public function jdkami(Request $request)
    {
//        $upstreamModel = new Upstream();
//        $upstream = $upstreamModel->where('ip', $_SERVER['REMOTE_ADDR'])->first();
//        if(empty($upstream)) {
//            return [
//                'status' => '30009',
//                'msg' => 'IP restrictions'
//            ];
//        }
//        $modelName = 'App\Upstream'.'\\'.ucfirst($upstream->en_name);
        $modelName = 'App\Upstream' . '\\' . 'Jdkami';
        $model = new $modelName;
        $res = $model->notify($request);
        if (isset($res['status']) && $res['status'] == 30003) {
            return [
                'status' => '30003',
                'msg' => 'Signature error'
            ];
        }
        if ($res && $res->status != 2) {
            $balance = $res->upstream->balance - $res->amount;
            $upstreamModel = new Upstream();
            $upstreamModel->where('id', $res->upstream_id)->update([
                'balance' => $balance
            ]);


            $mbalance = $res->customer->balance - $res->merchant_amount;
            $merchantModel = new Merchant();
            $merchantModel->where('id', $res->customer->id)->update([
                'balance' => $mbalance
            ]);


            $data = json_encode([
                'number' => 5,
                'order_id' => $res->id
            ]);
            Redis::rpush('feedback_pool', $data);


            return 'success';
        }
    }


    public function baobaokm(Request $request)
    {
//        $upstreamModel = new Upstream();
//        $upstream = $upstreamModel->where('ip', $_SERVER['REMOTE_ADDR'])->first();
//        if(empty($upstream)) {
//            return [
//                'status' => '30009',
//                'msg' => 'IP restrictions'
//            ];
//        }
//        $modelName = 'App\Upstream'.'\\'.ucfirst($upstream->en_name);
//        error_log(print_r($request->all(),1),3,'baiyue.txt');
        $modelName = 'App\Upstream' . '\\' . 'Baobaokm';
        $model = new $modelName;
        $res = $model->notify($request);
        if (isset($res['status']) && $res['status'] == 30003) {
            return [
                'status' => '30003',
                'msg' => 'Signature error'
            ];
        }
        if ($res && $res->status != 2) {
            $balance = $res->upstream->balance - $res->amount;
            $upstreamModel = new Upstream();
            $upstreamModel->where('id', $res->upstream_id)->update([
                'balance' => $balance
            ]);


            $mbalance = $res->customer->balance - $res->merchant_amount;
            $merchantModel = new Merchant();
            $merchantModel->where('id', $res->customer->id)->update([
                'balance' => $mbalance
            ]);


            $data = json_encode([
                'number' => 5,
                'order_id' => $res->id
            ]);
            Redis::rpush('feedback_pool', $data);


            return 'success';
        } elseif($res->status == 2) {
            return 'success';
        }
    }

    public function tiantian(Request $request)
    {
//        $upstreamModel = new Upstream();
//        $upstream = $upstreamModel->where('ip', $_SERVER['REMOTE_ADDR'])->first();
//        if(empty($upstream)) {
//            return [
//                'status' => '30009',
//                'msg' => 'IP restrictions'
//            ];
//        }
//        $modelName = 'App\Upstream'.'\\'.ucfirst($upstream->en_name);
        $modelName = 'App\Upstream' . '\\' . 'Tiantian';
        $model = new $modelName;
        $res = $model->notify($request);
        if (isset($res['status']) && $res['status'] == 30003) {
            return [
                'status' => '30003',
                'msg' => 'Signature error'
            ];
        }
        if ($res && $res->status != 2) {
            $balance = $res->upstream->balance - $res->amount;
            $upstreamModel = new Upstream();
            $upstreamModel->where('id', $res->upstream_id)->update([
                'balance' => $balance
            ]);


            $mbalance = $res->customer->balance - $res->merchant_amount;
            $merchantModel = new Merchant();
            $merchantModel->where('id', $res->customer->id)->update([
                'balance' => $mbalance
            ]);


            $data = json_encode([
                'number' => 5,
                'order_id' => $res->id
            ]);
            Redis::rpush('feedback_pool', $data);


            return 'success';
        }
    }

    public function ost(Request $request)
    {
//        $upstreamModel = new Upstream();
//        $upstream = $upstreamModel->where('ip', $_SERVER['REMOTE_ADDR'])->first();
//        if(empty($upstream)) {
//            return [
//                'status' => '30009',
//                'msg' => 'IP restrictions'
//            ];
//        }
//        $modelName = 'App\Upstream'.'\\'.ucfirst($upstream->en_name);
        $modelName = 'App\Upstream' . '\\' . 'Ost';
        $model = new $modelName;
        $res = $model->notify($request);
        if (isset($res['status']) && $res['status'] == 30003) {
            return [
                'status' => '30003',
                'msg' => 'Signature error'
            ];
        }
        if ($res && $res->status != 2) {
            $balance = $res->upstream->balance - $res->amount;
            $upstreamModel = new Upstream();
            $upstreamModel->where('id', $res->upstream_id)->update([
                'balance' => $balance
            ]);


            $mbalance = $res->customer->balance - $res->merchant_amount;
            $merchantModel = new Merchant();
            $merchantModel->where('id', $res->customer->id)->update([
                'balance' => $mbalance
            ]);


            $data = json_encode([
                'number' => 5,
                'order_id' => $res->id
            ]);
            Redis::rpush('feedback_pool', $data);


            return 'success';
        }
    }

    public function boxin(Request $request)
    {
//        $upstreamModel = new Upstream();
//        $upstream = $upstreamModel->where('ip', $_SERVER['REMOTE_ADDR'])->first();
//        if(empty($upstream)) {
//            return [
//                'status' => '30009',
//                'msg' => 'IP restrictions'
//            ];
//        }
//        $modelName = 'App\Upstream'.'\\'.ucfirst($upstream->en_name);
        $modelName = 'App\Upstream' . '\\' . 'Boxin';
        $model = new $modelName;
        $res = $model->notify($request);
        if (isset($res['status']) && $res['status'] == 30003) {
            return [
                'status' => '30003',
                'msg' => 'Signature error'
            ];
        }
        if ($res && $res->status != 2) {
            $balance = $res->upstream->balance - $res->amount;
            $upstreamModel = new Upstream();
            $upstreamModel->where('id', $res->upstream_id)->update([
                'balance' => $balance
            ]);


            $mbalance = $res->customer->balance - $res->merchant_amount;
            $merchantModel = new Merchant();
            $merchantModel->where('id', $res->customer->id)->update([
                'balance' => $mbalance
            ]);


            $data = json_encode([
                'number' => 5,
                'order_id' => $res->id
            ]);
            Redis::rpush('feedback_pool', $data);


            return 'SUCCESS';
        }
    }

    public function jdb(Request $request)
    {
//        $upstreamModel = new Upstream();
//        $upstream = $upstreamModel->where('ip', $_SERVER['REMOTE_ADDR'])->first();
//        if(empty($upstream)) {
//            return [
//                'status' => '30009',
//                'msg' => 'IP restrictions'
//            ];
//        }
//        $modelName = 'App\Upstream'.'\\'.ucfirst($upstream->en_name);
        $modelName = 'App\Upstream' . '\\' . 'Jdb';
        $model = new $modelName;
        $res = $model->notify($request);
        if (isset($res['status']) && $res['status'] == 30003) {
            return [
                'status' => '30003',
                'msg' => 'Signature error'
            ];
        }
        if ($res && $res->status != 2) {
            $balance = $res->upstream->balance - $res->amount;
            $upstreamModel = new Upstream();
            $upstreamModel->where('id', $res->upstream_id)->update([
                'balance' => $balance
            ]);


            $mbalance = $res->customer->balance - $res->merchant_amount;
            $merchantModel = new Merchant();
            $merchantModel->where('id', $res->customer->id)->update([
                'balance' => $mbalance
            ]);


            $data = json_encode([
                'number' => 5,
                'order_id' => $res->id
            ]);
            Redis::rpush('feedback_pool', $data);


            return 'success';
        }
    }

    public function shiguang(Request $request)
    {
//        $upstreamModel = new Upstream();
//        $upstream = $upstreamModel->where('ip', $_SERVER['REMOTE_ADDR'])->first();
//        if(empty($upstream)) {
//            return [
//                'status' => '30009',
//                'msg' => 'IP restrictions'
//            ];
//        }
//        $modelName = 'App\Upstream'.'\\'.ucfirst($upstream->en_name);
        $modelName = 'App\Upstream' . '\\' . 'Shiguang';
        $model = new $modelName;
        $res = $model->notify($request);
        if (isset($res['status']) && $res['status'] == 30003) {
            return [
                'status' => '30003',
                'msg' => 'Signature error'
            ];
        }
        if ($res && $res->status != 2) {
            $balance = $res->upstream->balance - $res->amount;
            $upstreamModel = new Upstream();
            $upstreamModel->where('id', $res->upstream_id)->update([
                'balance' => $balance
            ]);


            $mbalance = $res->customer->balance - $res->merchant_amount;
            $merchantModel = new Merchant();
            $merchantModel->where('id', $res->customer->id)->update([
                'balance' => $mbalance
            ]);


            $data = json_encode([
                'number' => 5,
                'order_id' => $res->id
            ]);
            Redis::rpush('feedback_pool', $data);


            return 'success';
        }
    }

    public function baobaotb(Request $request)
    {
//        $upstreamModel = new Upstream();
//        $upstream = $upstreamModel->where('ip', $_SERVER['REMOTE_ADDR'])->first();
//        if(empty($upstream)) {
//            return [
//                'status' => '30009',
//                'msg' => 'IP restrictions'
//            ];
//        }
//        $modelName = 'App\Upstream'.'\\'.ucfirst($upstream->en_name);
        $modelName = 'App\Upstream' . '\\' . 'Baobaotb';
        $model = new $modelName;
        $res = $model->notify($request);
        if (isset($res['status']) && $res['status'] == 30003) {
            return [
                'status' => '30003',
                'msg' => 'Signature error'
            ];
        }
        if ($res && $res->status != 2) {
            $balance = $res->upstream->balance - $res->amount;
            $upstreamModel = new Upstream();
            $upstreamModel->where('id', $res->upstream_id)->update([
                'balance' => $balance
            ]);


            $mbalance = $res->customer->balance - $res->merchant_amount;
            $merchantModel = new Merchant();
            $merchantModel->where('id', $res->customer->id)->update([
                'balance' => $mbalance
            ]);


            $data = json_encode([
                'number' => 5,
                'order_id' => $res->id
            ]);
            Redis::rpush('feedback_pool', $data);


            return 'success';
        }
    }

    public function guangguang(Request $request)
    {
//        $upstreamModel = new Upstream();
//        $upstream = $upstreamModel->where('ip', $_SERVER['REMOTE_ADDR'])->first();
//        if(empty($upstream)) {
//            return [
//                'status' => '30009',
//                'msg' => 'IP restrictions'
//            ];
//        }
//        $modelName = 'App\Upstream'.'\\'.ucfirst($upstream->en_name);
        $modelName = 'App\Upstream' . '\\' . 'Guangguang';
        $model = new $modelName;
        $res = $model->notify($request);
        if (isset($res['status']) && $res['status'] == 30003) {
            return [
                'status' => '30003',
                'msg' => 'Signature error'
            ];
        }
        if ($res && $res->status != 2) {
            $balance = $res->upstream->balance - $res->amount;
            $upstreamModel = new Upstream();
            $upstreamModel->where('id', $res->upstream_id)->update([
                'balance' => $balance
            ]);


            $mbalance = $res->customer->balance - $res->merchant_amount;
            $merchantModel = new Merchant();
            $merchantModel->where('id', $res->customer->id)->update([
                'balance' => $mbalance
            ]);


            $data = json_encode([
                'number' => 5,
                'order_id' => $res->id
            ]);
            Redis::rpush('feedback_pool', $data);


            return 'success';
        }
    }

    public function huajie(Request $request)
    {
//        $upstreamModel = new Upstream();
//        $upstream = $upstreamModel->where('ip', $_SERVER['REMOTE_ADDR'])->first();
//        if(empty($upstream)) {
//            return [
//                'status' => '30009',
//                'msg' => 'IP restrictions'
//            ];
//        }
//        $modelName = 'App\Upstream'.'\\'.ucfirst($upstream->en_name);
        $modelName = 'App\Upstream' . '\\' . 'Huajie';
        $model = new $modelName;
        $res = $model->notify($request);
        if (isset($res['status']) && $res['status'] == 30003) {
            return [
                'status' => '30003',
                'msg' => 'Signature error'
            ];
        }
        if ($res && $res->status != 2) {



            $data = json_encode([
                'number' => 5,
                'order_id' => $res->id
            ]);
            Redis::rpush('feedback_pool', $data);


            return 'success';
        }
    }

    public function qiaopai(Request $request)
    {
//        $upstreamModel = new Upstream();
//        $upstream = $upstreamModel->where('ip', $_SERVER['REMOTE_ADDR'])->first();
//        if(empty($upstream)) {
//            return [
//                'status' => '30009',
//                'msg' => 'IP restrictions'
//            ];
//        }
//        $modelName = 'App\Upstream'.'\\'.ucfirst($upstream->en_name);
        $modelName = 'App\Upstream' . '\\' . 'Qiaopai';
        $model = new $modelName;
        $res = $model->notify($request);
        if (isset($res['status']) && $res['status'] == 30003) {
            return [
                'status' => '30003',
                'msg' => 'Signature error'
            ];
        }
        if ($res && $res->status != 2) {



            $data = json_encode([
                'number' => 5,
                'order_id' => $res->id
            ]);
            Redis::rpush('feedback_pool', $data);


            return 'ok';
        }
    }

    public function shagou(Request $request)
    {
//        $upstreamModel = new Upstream();
//        $upstream = $upstreamModel->where('ip', $_SERVER['REMOTE_ADDR'])->first();
//        if(empty($upstream)) {
//            return [
//                'status' => '30009',
//                'msg' => 'IP restrictions'
//            ];
//        }
//        $modelName = 'App\Upstream'.'\\'.ucfirst($upstream->en_name);
        $modelName = 'App\Upstream' . '\\' . 'Shagou';
        $model = new $modelName;
        $res = $model->notify($request);
        if (isset($res['status']) && $res['status'] == 30003) {
            return [
                'status' => '30003',
                'msg' => 'Signature error'
            ];
        }
        if ($res && $res->status != 2) {



            $data = json_encode([
                'number' => 5,
                'order_id' => $res->id
            ]);
            Redis::rpush('feedback_pool', $data);


            return 1;
        }
    }

    public function laozhanyou(Request $request)
    {
//        $upstreamModel = new Upstream();
//        $upstream = $upstreamModel->where('ip', $_SERVER['REMOTE_ADDR'])->first();
//        if(empty($upstream)) {
//            return [
//                'status' => '30009',
//                'msg' => 'IP restrictions'
//            ];
//        }
//        $modelName = 'App\Upstream'.'\\'.ucfirst($upstream->en_name);
        $modelName = 'App\Upstream' . '\\' . 'Laozhanyou';
        $model = new $modelName;
        $res = $model->notify($request);
        if (isset($res['status']) && $res['status'] == 30003) {
            return [
                'status' => '30003',
                'msg' => 'Signature error'
            ];
        }
        if ($res && $res->status != 2) {



            $data = json_encode([
                'number' => 5,
                'order_id' => $res->id
            ]);
            Redis::rpush('feedback_pool', $data);


            return 'ok';
        }
    }

    public function sbpay(Request $request)
    {
//        $upstreamModel = new Upstream();
//        $upstream = $upstreamModel->where('ip', $_SERVER['REMOTE_ADDR'])->first();
//        if(empty($upstream)) {
//            return [
//                'status' => '30009',
//                'msg' => 'IP restrictions'
//            ];
//        }
//        $modelName = 'App\Upstream'.'\\'.ucfirst($upstream->en_name);
        $modelName = 'App\Upstream' . '\\' . 'Sbpay';
        $model = new $modelName;
        $res = $model->notify($request);
        if (isset($res['status']) && $res['status'] == 30003) {
            return 'fail';
        }
        if ($res && $res->status != 2) {



            $data = json_encode([
                'number' => 5,
                'order_id' => $res->id
            ]);
            Redis::rpush('feedback_pool', $data);


            return 'success';
        }
    }

    public function mt(Request $request)
    {
//        $upstreamModel = new Upstream();
//        $upstream = $upstreamModel->where('ip', $_SERVER['REMOTE_ADDR'])->first();
//        if(empty($upstream)) {
//            return [
//                'status' => '30009',
//                'msg' => 'IP restrictions'
//            ];
//        }
//        $modelName = 'App\Upstream'.'\\'.ucfirst($upstream->en_name);
        $modelName = 'App\Upstream' . '\\' . 'Mt';
        $model = new $modelName;
        $res = $model->notify($request);
        if (isset($res['status']) && $res['status'] == 30003) {
            return [
                'status' => '30003',
                'msg' => 'Signature error'
            ];
        }
        if ($res && $res->status != 2) {



            $data = json_encode([
                'number' => 5,
                'order_id' => $res->id
            ]);
            Redis::rpush('feedback_pool', $data);


            return 'success';
        }
    }
}
