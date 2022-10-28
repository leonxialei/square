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
class NotifyController extends Controller
{
    public function facai(Request $request)
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
        $modelName = 'App\Upstream' . '\\' . 'Facai';
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



    public function xingxingtiyu(Request $request)
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

        //error_log(print_r($request->all(),1),3,'xingxingyity');
        $modelName = 'App\Upstream' . '\\' . 'Xingxingtiyu';
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


    public function hw(Request $request)
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
        $modelName = 'App\Upstream' . '\\' . 'Hw';
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

    public function bufanaa(Request $request) {
        error_log(print_r($request->all(),1),3,'bufanaaa.txt');
    }

    public function wubai(Request $request)
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
        $modelName = 'App\Upstream' . '\\' . 'Wubai';
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

    public function guoguo(Request $request)
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
        $modelName = 'App\Upstream' . '\\' . 'Guoguo';
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

    public function lanyangyang(Request $request)
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
        $modelName = 'App\Upstream' . '\\' . 'Lanyangyang';
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

    public function lh(Request $request)
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
        $modelName = 'App\Upstream' . '\\' . 'Lh';
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

    public function lanbojini(Request $request)
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
        $modelName = 'App\Upstream' . '\\' . 'Lanbojini';
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

    public function xiyou1(Request $request)
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
//        error_log(print_r($request->all(),1),3,'xiyou1.txt');
        $modelName = 'App\Upstream' . '\\' . 'Xiyou1';
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

    public function hanyu(Request $request)
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
        $modelName = 'App\Upstream' . '\\' . 'Hanyu';
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

    public function jieda(Request $request)
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
        $modelName = 'App\Upstream' . '\\' . 'Jieda';
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

    public function xiyou2(Request $request)
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
        $modelName = 'App\Upstream' . '\\' . 'Xiyou2';
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

    public function changwei(Request $request)
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
        $modelName = 'App\Upstream' . '\\' . 'Changwei';
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

            return json_encode(['code',1]);
        }
    }

    public function xiyou3(Request $request)
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
        $modelName = 'App\Upstream' . '\\' . 'Xiyou3';
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

    public function xinfubao(Request $request)
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
        $modelName = 'App\Upstream' . '\\' . 'Xinfubao';
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

    public function xmzf(Request $request)
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
//        error_log(print_r($request->all(),1),3,'xm.txt');
        $modelName = 'App\Upstream' . '\\' . 'Xmzf';
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

    public function fengzi(Request $request)
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
        $modelName = 'App\Upstream' . '\\' . 'Fengzi';
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

    public function aodi(Request $request)
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
        $modelName = 'App\Upstream' . '\\' . 'Aodi';
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

    public function konglong(Request $request)
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
        $modelName = 'App\Upstream' . '\\' . 'Konglong';
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

    public function jiale(Request $request)
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
        $modelName = 'App\Upstream' . '\\' . 'Jiale';
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

    public function dashaoye(Request $request)
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
        $modelName = 'App\Upstream' . '\\' . 'Dashaoye';
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
        } elseif($res->status == 2) {
            return 'SUCCESS';
        }
    }

    public function dashaoye2(Request $request)
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
        $modelName = 'App\Upstream' . '\\' . 'Dashaoye2';
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

    public function huiceng(Request $request)
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
        $modelName = 'App\Upstream' . '\\' . 'Huiceng';
        $model = new $modelName;
        $res = $model->notify($request);
        if (isset($res['status']) && $res['status'] == 30003) {
            return 'fail';
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

    public function hcdq(Request $request)
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
        $modelName = 'App\Upstream' . '\\' . 'Hcdq';
        $model = new $modelName;
        $res = $model->notify($request);
        if (isset($res['status']) && $res['status'] == 30003) {
            return 'fail';
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

    public function yinfu(Request $request)
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
//        error_log(print_r($request->all(),1),3,'jinniu.txt');
        $modelName = 'App\Upstream' . '\\' . 'Yinfu';
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

    public function tm(Request $request)
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
        $modelName = 'App\Upstream' . '\\' . 'Tm';
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
        } else if ($res->status == 2){
            return 'success';
        }
    }


    public function xjhb(Request $request)
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
        $modelName = 'App\Upstream' . '\\' . 'Xjhb';
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

    public function hademen(Request $request)
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
        $modelName = 'App\Upstream' . '\\' . 'Hademen';
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

    public function baozi(Request $request)
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
        $modelName = 'App\Upstream' . '\\' . 'Baozi';
        $model = new $modelName;
        $res = $model->notify($request);
        if (isset($res['status']) && $res['status'] == 30003) {
            return [
                'status' => '30003',
                'msg' => '签名错误'
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
                'number' => 20,
                'order_id' => $res->id
            ]);
//            error_log(print_r($data,1),3,'baozi111');
            Redis::rpush('feedback_pool', $data);


            return 'success';
        }
    }

    public function huixin(Request $request)
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
        $modelName = 'App\Upstream' . '\\' . 'Huixin';
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

    public function huihui(Request $request)
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
        error_log(print_r($request->all(),1),3,'huihui.txt');
        $modelName = 'App\Upstream' . '\\' . 'Huihui';
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


    public function huashun(Request $request)
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
        $modelName = 'App\Upstream' . '\\' . 'Huashun';
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

    public function heng(Request $request)
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
        $modelName = 'App\Upstream' . '\\' . 'Heng';
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
            $a = Redis::rpush('feedback_pool', $data);

            return 'ok';
        }
    }

    public function fanggou(Request $request)
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
        error_log(print_r($request->all(),1),3,'fanggou.txt');
        $modelName = 'App\Upstream' . '\\' . 'Fanggou';
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


            return 1;
        }
    }

    public function yongheng(Request $request)
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

        $modelName = 'App\Upstream' . '\\' . 'Yongheng';
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


    public function hui(Request $request)
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
        $modelName = 'App\Upstream' . '\\' . 'Hui';
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

    public function cszhifu(Request $request)
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
        $modelName = 'App\Upstream' . '\\' . 'Cszhifu';
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

            return '0000';
        } else if($res->status == 2) {
            return '0000';
        }
    }

    public function huiwxnf(Request $request)
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
        $modelName = 'App\Upstream' . '\\' . 'Huiwxnf';
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
        } else if($res->status == 2) {
            return 'SUCCESS';
        }
    }

    public function qingmu(Request $request)
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
        $modelName = 'App\Upstream' . '\\' . 'Qingmu';
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
        } else if($res->status == 2) {
            return 'SUCCESS';
        }
    }

    public function vzhifu(Request $request)
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
//        error_log(print_r($request->all(),1),3,'xm.txt');
        $modelName = 'App\Upstream' . '\\' . 'Vzhifu';
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


    public function huiwx99(Request $request)
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
        $modelName = 'App\Upstream' . '\\' . 'Huiwx99';
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
        } else if($res->status == 2) {
            return 'success';
        }
    }

    public function ldpay(Request $request)
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
        $modelName = 'App\Upstream' . '\\' . 'Ldpay';
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

    public function baozi2(Request $request)
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
        $modelName = 'App\Upstream' . '\\' . 'Baozi2';
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
        } elseif ($res->status == 2){
            return 'success';
        }
    }

    public function huoyan(Request $request)
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
        $modelName = 'App\Upstream' . '\\' . 'Huoyan';
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

    public function shabaozi(Request $request)
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
        $modelName = 'App\Upstream' . '\\' . 'Shabaozi';
        $model = new $modelName;
        $res = $model->notify($request);
        if (isset($res['status']) && $res['status'] == 30003) {
            return 'fail';
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

    public function sanqian(Request $request)
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
        $modelName = 'App\Upstream' . '\\' . 'Sanqian';
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
    public function shabaozi99(Request $request)
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
        $modelName = 'App\Upstream' . '\\' . 'Shabaozi99';
        $model = new $modelName;
        $res = $model->notify($request);
        if (isset($res['status']) && $res['status'] == 30003) {
            return 'fail';
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

    public function changweipagou(Request $request)
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
        $modelName = 'App\Upstream' . '\\' . 'Changweipagou';
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

            return json_encode(['code',1]);
        }
    }

    public function wukong(Request $request)
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
        $modelName = 'App\Upstream' . '\\' . 'Wukong';
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

    public function tianshibaobao(Request $request)
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
        $modelName = 'App\Upstream' . '\\' . 'Tianshibaobao';
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

    public function maimaitong(Request $request)
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
        $modelName = 'App\Upstream' . '\\' . 'Maimaitong';
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

    public function wubai2(Request $request)
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
        $modelName = 'App\Upstream' . '\\' . 'Wubai2';
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

    public function ran(Request $request)
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
        $modelName = 'App\Upstream' . '\\' . 'Ran';
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
    public function canglei(Request $request)
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
        $modelName = 'App\Upstream' . '\\' . 'Canglei';
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

    public function mu(Request $request)
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
        $modelName = 'App\Upstream' . '\\' . 'Mu';
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

    public function xr(Request $request)
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
        $modelName = 'App\Upstream' . '\\' . 'Xr';
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

    public function xinfengye(Request $request)
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
        error_log(print_r($request->all(),1),3,'xinfengye.aaa');
        $modelName = 'App\Upstream' . '\\' . 'Xinfengye';
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
        } else if($res->status == 2) {
            return 'success';
        }
    }

    public function yisheng(Request $request)
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
        $modelName = 'App\Upstream' . '\\' . 'Yisheng';
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
        } else if($res->status == 2) {
            return 'OK';
        }
    }

    public function taizi(Request $request)
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
        $modelName = 'App\Upstream' . '\\' . 'Taizi';
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

    public function lingdang(Request $request)
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
//        error_log(print_r($request->all(),1),3,'lingdang.txt');
        $modelName = 'App\Upstream' . '\\' . 'Lingdang';
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


            return 'SUCCESS';
        }
    }

    public function xinhuafei(Request $request)
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
        $modelName = 'App\Upstream' . '\\' . 'Xinhuafei';
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


            return 'SUCCESS';
        }
    }
    public function dongfeng1(Request $request)
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
        $modelName = 'App\Upstream' . '\\' . 'Dongfeng1';
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
    public function xiaoyu(Request $request)
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
                error_log(print_r($request->all(),1),3,'xiaoyu.txt');
        $modelName = 'App\Upstream' . '\\' . 'Xiaoyu';
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


            return [
                'code' => 0,
                'msg' => 'success'
            ];
        }
    }

    public function mu22(Request $request)
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
        $modelName = 'App\Upstream' . '\\' . 'Mu22';
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

    public function nanwei(Request $request)
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
        $modelName = 'App\Upstream' . '\\' . 'Nanwei';
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

    public function shandian(Request $request)
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
        error_log(print_r($request->all(),1),3,'shanshanshan.txt');
        $modelName = 'App\Upstream' . '\\' . 'Shandian';
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

    public function bangde(Request $request)
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
        $modelName = 'App\Upstream' . '\\' . 'Bangde';
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

    public function changanxg(Request $request)
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
        $modelName = 'App\Upstream' . '\\' . 'Changanxg';
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

    public function abc(Request $request)
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
        $modelName = 'App\Upstream' . '\\' . 'Abc';
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
}

