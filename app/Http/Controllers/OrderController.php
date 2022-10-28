<?php

namespace App\Http\Controllers;
use App\Help\Methods;
use App\Models\Merchant;
use App\Models\MerchantChannel;
use App\Models\Order;
use App\Models\Upstream;
use Illuminate\Http\Request;
use App\Models\ChannelCode;
use App\Models\UpstreamChannel;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
   public function index(Request $request) {
       $merchantChannelModel = new MerchantChannel();
       $merchantChannels = $merchantChannelModel->whereIn('channel_id', [8])
           ->where('status', 1)->orderBy('weight', 'DESC')->get();




       if(empty($request->get('start_time'))) {
           $start_time = strtotime(date('Y-m-d').' 00:00:00');
       } else {
           $start_time = strtotime($request->get('start_time'));
       }
       if(empty($request->get('end_time'))) {
           $end_time = strtotime(date('Y-m-d').' 23:59:59');
       } else {
           $end_time = strtotime($request->get('end_time'));
       }

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
       if(!empty($request->get('status'))) {
           if($request->get('status') == 4) {
               $orderModel = $orderModel->where('status', 0);
           } else {
               $orderModel = $orderModel->where('status', $request->get('status'));
           }
       }
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
       if(!empty($request->get('OrderNo'))) {
           $orderModel = $orderModel->where('OrderNo',  ltrim(trim($request->get('OrderNo')), 'YZ'));
       }
       if(!empty($request->get('mchOrderNo'))) {
           $orderModel = $orderModel->where('mchOrderNo', trim($request->get('mchOrderNo')));
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
       $orders = $orderModel->orderBy('id', 'DESC')->paginate(25);

       $data = [
           'request' => $request,
           'orders' => $orders,
           'start_time' => $start_time,
           'end_time' => $end_time,
           'channelCodes' => $channelCodes,
           'merchants' => $merchants,
           'upstreams' => $upstreams,
           'count' => $count,


           'pay_amount' => $pay_amount,
           'done_pay_amount' => $done_pay_amount,
           'done_count' => $done_count,
           'fail_count' => $fail_count,
           'status' => $request->get('status')
       ];
       return View('order.index', $data);
   }

   public function edit(Request $request, $id) {
       $orderModel = new Order();
       $order = $orderModel->where('id', $id)->first();
       $upstreamModel = new Upstream();
       $upstreams = $upstreamModel->where('status', 1)->get();
       $data = [
           'order' => $order,
           'upstreams' => $upstreams
       ];
       return View('order.edit', $data);
   }

   public function update(Request $request, $id) {
       $orderModel = new Order();
       $order = $orderModel->where('id', $id)->first();
       if(!empty($order)) {
           $orderModel->where('id', $id)->update(
               [
                   'pay_amount' => $request->get('pay_amount'),
                   'status' => $request->get('status')
               ]
           );
       }
       $js = <<<JS
            <script>
            alert('修改成功！');
            parent.location.reload();
            </script>
            JS;
       return $js;
   }




    public function statistics(Request $request) {
        if(empty($request->get('start_time'))) {
            $start_time = strtotime(date('Y-m-d').' 00:00:00');
        } else {
            $start_time = strtotime($request->get('start_time'));
        }
        if(empty($request->get('end_time'))) {
            $end_time = strtotime(date('Y-m-d').' 23:59:59');
        } else {
            $end_time = strtotime($request->get('end_time'));
        }

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
        $data = [
            'request' => $request,
            'orders' => $orders,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'channelCodes' => $channelCodes,
            'merchants' => $merchants,
            'upstreams' => $upstreams,
            'count' => $count,

            'pay_amount' => $pay_amount,
            'done_pay_amount' => $done_pay_amount,
            'done_count' => $done_count,
            'fail_count' => $fail_count,
            'status' => $request->get('status')
        ];

        if(Methods::is_mobile_request()) {
            return View('mobile/order/statistics', $data);
        } else {
            return View('order.statistics', $data);
        }
    }





}
