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


class AdvanceController extends Controller
{
    public function index(Request $request) {
        if(empty($request->get('start_date'))) {
            $start_date = date('Y-m-d');
        } else {
            $start_date = $request->get('start_date');
        }
        $end_date = $start_date;
        $start_time = strtotime($start_date.' 00:00:00');
        $end_time = strtotime($end_date.' 23:59:59');


        $upstreamModel = new Upstream();
        $upstreams = $upstreamModel->where('status', 1)->get();

        $orderModel = new Order();
        $orders = $orderModel->select('customer_id')->where('created', '>=', $start_time)
            ->where('created', '<=', $end_time)
            ->groupBy('customer_id')
            ->get();
        $ids = [];
        foreach($orders as $order) {
            $ids[] = $order->customer->id;
        }

        $merchantChannelModel = new MerchantChannel();
        $merchantChannels =  $merchantChannelModel->select('merchant_id')->whereIn('merchant_id', $ids)
            ->groupBy('merchant_id')->get();

        $data = [
            'merchantChannels' => $merchantChannels,
            'request' => $request,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'upstreams' => $upstreams
        ];
        if(Methods::is_mobile_request()) {
            return View('mobile/advance/index', $data);
        } else {
            return View('advance/index', $data);
        }
    }

    public function edit(Request $request, $id) {
        $merchantModel = new Merchant();
        $merchant = $merchantModel->where('id', $id)->first();
        $merchants = $merchantModel->where('status', 1)->get();
        $data = [
            'merchant' => $merchant,
            'merchants' => $merchants
        ];
        return View('advance/edit', $data);
    }

    public function update(Request $request, $id) {
        $userInfo = session('account');
        $merchantModel = new Merchant();
        $merchant = $merchantModel->where('id', $id)->first();
        if(empty($merchant)) {
            $js = <<<JS
            <script>
            parent.location.reload();
            </script>
            JS;
            return $js;
        }
        $advanceModel = new MerchantAdvance();
        $advanceModel->merchant_id = $id;
        $advanceModel->amount = $request->get('amount')*100;
        $advanceModel->user_id = $userInfo['user_id'];
        $advanceModel->type = $request->get('type');
        $advanceModel->recharge_time = strtotime($request->get('recharge_time'));
        if($request->get('type') == 1) {
            $advanceModel->balance = $merchant->balance + ($request->get('amount')*100);
        } elseif($request->get('type') == 2) {
            $advanceModel->balance = $merchant->balance - ($request->get('amount')*100);
        }

        $advanceModel->created = time();
        $advanceModel->save();

        if(!empty(Order::merchantAmount($merchant->account, date('Y-m-d'), date('Y-m-d')))){
            $merchantAmount = Order::merchantAmount($merchant->account, date('Y-m-d'), date('Y-m-d'))->merchant_amount;
        }else {
            $merchantAmount = 0;
        }
        $balance = ($merchant->advance($merchant->id, date('Y-m-d'), date('Y-m-d'))) - $merchantAmount;

        $orderLogModel = new OrderLog();
        $orderLogModel->merchant_id = $merchant->id;
        $orderLogModel->attribute = 2;
        $orderLogModel->type = $request->get('type');
        $orderLogModel->amount = $request->get('amount')*100;
        if($request->get('type') == 1) {
            $orderLogModel->before_balance = $balance + ($request->get('amount')*100);
        } elseif($request->get('type') == 2) {
            $orderLogModel->before_balance = $balance - ($request->get('amount')*100);
        }

        $orderLogModel->balance = $balance;
        $orderLogModel->note = 'web入账';
        $orderLogModel->created = time();
        $orderLogModel->save();


        $merchantModel->where('id', $id)->update([
            'balance' => $balance
        ]);
        $js = <<<JS
            <script>
            alert('修改成功！');
            parent.location.reload();
            </script>
            JS;
        return $js;
    }

    public function create () {
        $merchantModel = new Merchant();
        $merchants = $merchantModel->where('status', 1)->get();
        $data = [
            'merchants' => $merchants,
        ];
        return View('advance/create', $data);
    }

    public function store (Request $request) {
        $userInfo = session('account');
        $merchantModel = new Merchant();
        $merchant = $merchantModel->where('id', $request->get('merchant_id'))->first();
        if(empty($merchant)) {
            $js = <<<JS
            <script>
            parent.location.reload();
            </script>
            JS;
            return $js;
        }
        $advanceModel = new MerchantAdvance();
        $advanceModel->merchant_id = $request->get('merchant_id');
        $advanceModel->amount = $request->get('amount')*100;
        $advanceModel->user_id = $userInfo['user_id'];
        $advanceModel->type = $request->get('type');
        $advanceModel->recharge_time = strtotime($request->get('recharge_time'));
        if($request->get('type') == 1) {
            $advanceModel->balance = $merchant->balance + ($request->get('amount')*100);
        } elseif($request->get('type') == 2) {
            $advanceModel->balance = $merchant->balance - ($request->get('amount')*100);
        }

        $advanceModel->created = time();
        $advanceModel->save();



        if(!empty(Order::merchantAmount($merchant->account, date('Y-m-d'), date('Y-m-d')))){
            $merchantAmount = Order::merchantAmount($merchant->account, date('Y-m-d'), date('Y-m-d'))->merchant_amount;
        }else {
            $merchantAmount = 0;
        }
        $balance = ($merchant->advance($merchant->id, date('Y-m-d'), date('Y-m-d'))) - $merchantAmount;

        $orderLogModel = new OrderLog();
        $orderLogModel->merchant_id = $merchant->id;
        $orderLogModel->attribute = 2;
        $orderLogModel->type = $request->get('type');
        $orderLogModel->amount = $request->get('amount')*100;
        if($request->get('type') == 1) {
            $orderLogModel->before_balance = $balance - ($request->get('amount')*100);
        } elseif($request->get('type') == 2) {
            $orderLogModel->before_balance = $balance + ($request->get('amount')*100);
        }

        $orderLogModel->balance = $balance;
        $orderLogModel->note = 'web入账';
        $orderLogModel->created = time();
        $orderLogModel->save();

        $merchantModel->where('id', $request->get('merchant_id'))->update([
            'balance' => $balance
        ]);
        $js = <<<JS
            <script>
            alert('添加成功！');
            parent.location.reload();
            </script>
            JS;
        return $js;
    }

}
