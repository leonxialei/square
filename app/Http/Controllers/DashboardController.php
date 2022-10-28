<?php

namespace App\Http\Controllers;
use App\Models\ChannelCode;
use App\Models\MerchantChannel;
use App\Models\Order;
use App\Models\Upstream;
use App\Models\UpstreamChannel;
use Illuminate\Http\Request;
use App\Models\Merchant;
use Illuminate\Support\Facades\DB;


class DashboardController extends Controller
{
    public function index(Request $request) {
        $start_time = strtotime(date('Y-m-d').' 00:00:00');
        $end_time = strtotime(date('Y-m-d').' 23:59:59');
        $start_date = date('Y-m-d');
        $end_date = date('Y-m-d');



        $start_date = '2022-06-06';
        $end_date = '2022-06-06';

        $start_time = strtotime('2022-06-06 00:00:00');
        $end_time = strtotime('2022-06-06 23:59:59');
        $channelCodeModel = new ChannelCode();
        $channelCodes = $channelCodeModel->where('status', 1)->get();
        $merchantModel = new Merchant();
        $merchants = $merchantModel->where('status', 1)->get();
        $channelModel = new UpstreamChannel();
        $upstreamModel = new Upstream();
        $upstreams = $upstreamModel->where('status', 1)->orderBy('id', 'DESC')->get();

        $orderModel = new Order();
        $orderModel = $orderModel->where('created', '>=', $start_time)
            ->where('created', '<=', $end_time);

        if(!empty($request->get('channel_code'))) {
            $channelCode = $channelCodeModel->where('code', $request->get('channel_code'))->first();
            $merchantChannelModel = new MerchantChannel();

            $channelId = [];
            foreach($channelCode->upstreamChannel as $val) {
                $channelId[] = $val->id;
            }
            $merchantChannels = $merchantChannelModel->whereIn('channel_id', $channelId)->get();

            $merchantChannelIds = [];
            foreach($merchantChannels as $merchantChannel) {
                $merchantChannelIds[] = $merchantChannel->id;
            }
            $orderModel = $orderModel->whereIn('merchant_channel_id', $merchantChannelIds);
        }

        if(!empty($request->get('channel_id'))) {
            $upstreamChannel = $channelModel->where('id', $request->get('channel_id'))->first();
            $merchantChannels = $upstreamChannel->merchantChannel;
            $merchantChannelIds = [];
            foreach($merchantChannels as $merchantChannel) {
                $merchantChannelIds[] = $merchantChannel->id;
            }
            $orderModel = $orderModel->whereIn('merchant_channel_id', $merchantChannelIds);
        }

        if(!empty($request->get('upstream_id'))) {
            $orderModel = $orderModel->where('upstream_id', $request->get('upstream_id'));
        }
        if(!empty($request->get('merchant_id'))) {
            $orderModel = $orderModel->where('customer_id', $request->get('merchant_id'));
        }

//       if(!empty($request->get('mchOrderNo'))) {
//           $orderModel = $orderModel->where('mchOrderNo', $request->get('mchOrderNo'));
//       }
        $cloneModel = $orderModel->clone();
        $count = $cloneModel->count();
        $pay_amount = $cloneModel->sum('original_amount');
        $done_pay_amount = $cloneModel->whereIn('status', [1,2])->sum('original_amount');
        $done_count = $cloneModel->whereIn('status', [1,2])->count();
        $fail_count = $cloneModel->where('status', 3)->count();
        $orders = $orderModel
            ->select(DB::Raw('`customer_id` ,`merchant_channel_id`, SUM(`original_amount`) as original_amount, SUM(`amount`) as amount
            , COUNT(`id`) as count, SUM(`merchant_amount`) as 	merchant_amount'))
            ->groupBy('merchant_channel_id', 'customer_id')->orderBy('customer_id')->get();



        $neworders = $orderModel->select('customer_id')->where('created', '>=', $start_time)
            ->where('created', '<=', $end_time)
            ->groupBy('customer_id')
            ->get();
        $ids = [];
        foreach($neworders as $neworder) {
            $ids[] = $neworder->customer->id;
        }

        $merchantChannelModel = new MerchantChannel();
        $merchantChannels =  $merchantChannelModel->select('merchant_id')->whereIn('merchant_id', $ids)
            ->groupBy('merchant_id')->get();
        $data = [
            'request' => $request,
            'orders' => $orders,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'channelCodes' => $channelCodes,
            'merchants' => $merchants,
            'upstreams' => $upstreams,
            'count' => $count,
            'merchantChannels' => $merchantChannels,
            'pay_amount' => $pay_amount,
            'done_pay_amount' => $done_pay_amount,
            'done_count' => $done_count,
            'fail_count' => $fail_count,
            'status' => $request->get('status')
        ];

        return View('dashboard.index', $data);
    }




}
