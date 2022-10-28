<?php

namespace App\Http\Controllers\Api;;
use App\Http\Controllers\Controller;
use App\Models\MerchantChannel;
use App\Models\Order;
use App\Models\Upstream;
use App\Models\UpstreamChannel;
use Illuminate\Http\Request;
use App\Models\Merchant;
use Illuminate\Support\Facades\DB;


class DashboardController extends Controller
{
    public function down(Request $request) {
        $start_time = strtotime(date('Y-m-d').' 00:00:00');
        $end_time = strtotime(date('Y-m-d').' 23:59:59');



        $start_time = strtotime('2022-10-06 00:00:00');
        $end_time = strtotime('2022-10-06 23:59:59');


        $orderModel = new Order();
        $orderModel = $orderModel->where('created', '>=', $start_time)
            ->where('created', '<=', $end_time);


//       if(!empty($request->get('mchOrderNo'))) {
//           $orderModel = $orderModel->where('mchOrderNo', $request->get('mchOrderNo'));

        $orders = $orderModel
            ->select(DB::Raw('`customer_id` ,`merchant_channel_id`, SUM(`original_amount`) as original_amount, SUM(`amount`) as amount
            , COUNT(`id`) as count, SUM(`merchant_amount`) as 	merchant_amount'))
            ->groupBy('merchant_channel_id', 'customer_id')->orderBy('customer_id')->get();

        $data = [];
        foreach ($orders as $k=>$order) {
            if(!empty($order->merchantChannel->channel)){
                $data[$k]['name'] = $order->customer->name.'--'.$order->merchantChannel->channel->name;
            } else {
                $data[$k]['name'] = $order->customer->name.'--##';
            }

            if(!empty(Order::succeed($order->merchant_channel_id, $start_time, $end_time))){
                $data[$k]['rate'] = sprintf("%.2f", (Order::succeed($order->merchant_channel_id, $start_time, $end_time)->count/$order->count)*100);
            } else {
                $data[$k]['rate'] = 0;
            }

            if(!empty(Order::succeed($order->merchant_channel_id, $start_time, $end_time))) {
                $data[$k]['succeed'] = sprintf("%.2f", Order::succeed($order->merchant_channel_id, $start_time, $end_time)->original_amount/100);
            } else {
                $data[$k]['succeed']  = '0.00';
            }



        }
        return $data;














    }


    public function rate(Request $request) {
        $start_time = strtotime(date('Y-m-d').' 00:00:00');
        $end_time = strtotime(date('Y-m-d').' 23:59:59');



        $start_time = strtotime('2022-01-06 00:00:00');
        $end_time = strtotime('2022-10-06 23:59:59');


        $orderModel = new Order();
        $orderModel = $orderModel->where('created', '>=', $start_time)
            ->where('created', '<=', $end_time);
        $orderModel = $orderModel
            ->select('code')

            ->where('upstream_id', 80)->groupBy('code');

//       if(!empty($request->get('mchOrderNo'))) {
//           $orderModel = $orderModel->where('mchOrderNo', $request->get('mchOrderNo'));

        $codes = $orderModel
         ->get();

        $data = [];
        foreach ($codes as $k=>$code) {
            $orderModel = new Order();
            $orderModel = $orderModel->where('created', '>=', $start_time)
                ->where('created', '<=', $end_time);
            $orderModel = $orderModel->where('upstream_id', 80)->where('code', $code);
            $orderModel->select(DB::raw('COUNT(1) as order_nu'));

            $order = $orderModel->where('status', 2)->first();

            dd($order);

        }
        return $data;














    }

}
