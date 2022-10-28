<?php

namespace App\Http\Controllers;
use App\Help\Methods;
use App\Models\AdvanceLog;
use App\Models\ChannelCode;
use App\Models\Merchant;
use App\Models\MerchantAdvance;
use App\Models\MerchantChannel;
use App\Models\Order;
use App\Models\OrderLog;
use App\Models\Upstream;
use App\Models\UpstreamChannel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class AnalyzeController {
    public function index(Request $request) {
        $date = $request->get('start_date');
        $ids = $this->ids($date);
        if(empty($ids)) {
            return '今日无订单';
        }
        return $this->show($ids[0], $request);

    }
    public function show($id, Request $request) {
        $date = $request->get('start_date');
        if(empty($date)) {
            $start_date = date('Y-m-d');
        } else {
            $start_date = $date;
        }
        $ids = $this->ids($start_date);
        $merchantModel = new Merchant();
        $merchants = $merchantModel->whereIn('id', $ids)->get();
        $nowMerchant =  $merchantModel->find($id);
        $times = [];
        $i = 0;
        for($i=0; $i<=23; $i++) {
            $times[] = sprintf("%02d",$i);
        }


        $data = [
            'merchants' => $merchants,
            'nowMerchant' => $nowMerchant,
            'times' => $times,
            'start_date' => $start_date,
            'id' => $id
        ];
        return View('analyze/index', $data);
    }

    private function ids($date) {
        if(empty($date)) {
            $start_date = date('Y-m-d');
        } else {
            $start_date = $date;
        }

        $end_date = $start_date;
        $start_time = strtotime($start_date.' 00:00:00');
        $end_time = strtotime($end_date.' 23:59:59');



        $orderModel = new Order();
        $orders = $orderModel->select('customer_id')->where('created', '>=', $start_time)
            ->where('created', '<=', $end_time)
            ->groupBy('customer_id')
            ->get();
        $ids = [];
        foreach($orders as $order) {
            $ids[] = $order->customer->id;
        }
        return $ids;
    }
}
