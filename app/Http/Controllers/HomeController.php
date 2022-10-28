<?php

namespace App\Http\Controllers;



use App\Models\Order;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index() {
        return view('home.index');
    }

    public function console() {
        $start_time = strtotime(date('Y-m-d').' 00:00:00');
        $end_time = strtotime(date('Y-m-d').' 23:59:59');
        $orderModel = new Order();
        $orderModel = $orderModel->where('created', '>=', $start_time)
            ->where('created', '<=', $end_time);
        $count = $orderModel->count();
        $pay_amount = $orderModel->sum('original_amount');
        $done_pay_amount = $orderModel->whereIn('status', [1,2])->sum('original_amount');
        $done_count = $orderModel->whereIn('status', [1,2])->count();
        $fail_count = $orderModel->where('status', 3)->count();
        $orders = $orderModel
            ->select(DB::Raw('`customer_id` ,`merchant_channel_id`, SUM(`original_amount`) as original_amount, SUM(`amount`) as amount
            , COUNT(`id`) as count, SUM(`merchant_amount`) as 	merchant_amount'))
            ->groupBy('merchant_channel_id', 'customer_id')->orderBy('customer_id')->get();


        $start_time = strtotime(date('Y-m-d').' 00:00:00')-86400;
        $end_time = strtotime(date('Y-m-d').' 23:59:59')-86400;
        $orderModel = new Order();
        $orderModel = $orderModel->where('created', '>=', $start_time)
            ->where('created', '<=', $end_time);
        $ycount = $orderModel->count();
        $ypay_amount = $orderModel->sum('original_amount');
        $ydone_pay_amount = $orderModel->whereIn('status', [1,2])->sum('original_amount');
        $ydone_count = $orderModel->whereIn('status', [1,2])->count();
        $yfail_count = $orderModel->where('status', 3)->count();
        $data = [
            'count' => $count,
            'pay_amount' => $pay_amount,
            'done_pay_amount' => $done_pay_amount,
            'done_count' => $done_count,
            'fail_count' => $fail_count,
            'orders' => $orders,


            'ycount' => $ycount,
            'ypay_amount' => $ypay_amount,
            'ydone_pay_amount' => $ydone_pay_amount,
            'ydone_count' => $ydone_count,
            'yfail_count' => $yfail_count,

        ];
        return view('home.console', $data);
    }

    public function total() {
        return view('home.total');
    }
}
