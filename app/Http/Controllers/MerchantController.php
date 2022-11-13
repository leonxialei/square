<?php

namespace App\Http\Controllers;

use App\Models\MerchantAdvance;
use App\Models\OrderLog;
use App\Models\TakeCash;
use App\Models\TelegramBookkeeping;
use App\Models\TelegramMerchant;
use Illuminate\Http\Request;
use App\Models\Merchant;
use Illuminate\Support\Str;


class MerchantController extends Controller
{
    public function index(Request $request) {
        $merchantModel = new Merchant();
        if(!empty($request->get('name'))) {
            $merchantModel = $merchantModel->where('name', 'LIKE','%'.$request->get('name').'%');
        }
        if(!empty($request->get('account'))) {
            $merchantModel = $merchantModel->where('account', 'LIKE','%'.$request->get('account').'%');
        }
        if($request->get('status') != '') {
            $merchantModel = $merchantModel->where('status', $request->get('status'));
        }
        $merchants = $merchantModel->orderBy('id', 'DESC')->paginate(15);
        $data = [
            'request' => $request,
            'merchants' => $merchants
        ];
        return View('merchant/index', $data);
    }

    public function create() {
        $merchantModel = new Merchant();
        $number = 1;
        $merchant = $merchantModel->orderBy('id', 'DESC')->first();
        $merchants = $merchantModel->orderBy('id', 'DESC')->get();
        $basics = 1100000;
        if(!empty($merchant)) {
            $number = $merchant->id + 1;
        }
        $basics = $basics + $number;
        $token = md5($basics.'BAICHI');
        $data = [
            'merchants' => $merchants,
            'basics' => $basics,
            'token' => $token,
        ];
        return View('merchant/create', $data);
    }

    public function store(Request $request) {
        $merchantModel = new Merchant();
        $merchant = $merchantModel->where('account', $request->get('account'))->first();
        if(!empty($merchant)) {
            return redirect("merchant");
        }
        $merchantModel->account = $request->get('account');
        $merchantModel->name = $request->get('name');
        $merchantModel->password = $request->get('password');
        $merchantModel->token = $request->get('token');
        $merchantModel->agency_id = $request->get('agency_id');
        $merchantModel->status = $request->get('status');
        $merchantModel->created = time();
        if($merchantModel->save()) {
            $js = <<<JS
            <script>
            alert('创建成功！');
            parent.location.reload();
            </script>
            JS;
            return $js;
        }
    }

    public function edit(Request $request, $id) {
        $merchantModel = new Merchant();
        $merchant = $merchantModel->where('id', $id)->first();
        $merchants = $merchantModel->orderBy('id', 'DESC')->get();
        $agencys = $merchantModel->where('agency_id', $id)->get();




        return View('merchant/edit', [
            'merchant' => $merchant,
            'merchants' => $merchants,
            'agencys' => $agencys
        ]);
    }

    public function update(Request $request, $id) {
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
        $merchantModel->where('id', $id)->update([
            'name' => $request->get('name'),
            'password' => $request->get('password'),
            'status' => $request->get('status'),
            'agency_id' => $request->get('agency_id')
        ]);
        $js = <<<JS
        <script>
        alert('修改成功！');
        parent.location.reload();
        </script>
        JS;
        return $js;
    }

    public function cash(Request $request) {
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
        $cashModel = new TakeCash();
        $cashModel = $cashModel
            ->where('created', '>=', $start_time)
            ->where('created', '<=', $end_time);
        $status = $request->get('status');
        if($status != '') {
            $cashModel->where('status', $status);
        } else {
            $status = 2;
        }
        $cashs = $cashModel->paginate(25);
        $data = [
            'start_time' => $start_time,
            'end_time' => $end_time,
            'cashs' => $cashs,
            'status' => $status
        ];
        return View('merchant/cash', $data);
    }

    public function detail($id) {
        $cashModel = new TakeCash();

        $cash = $cashModel->where('id', $id)->first();
        $data = [

            'cash' => $cash
        ];
        return View('merchant/cashDetail', $data);
    }

    public function store_cash(Request $request, $id) {
        $cashModel = new TakeCash();

        $cash = $cashModel->where('id', $id)->first();
        if(empty($cash)) {
            $js = <<<JS
                <script>
                alert('非法操作！');
                parent.location.reload();
                </script>
                JS;
            return $js;
        }
        if($cash->amount != $request->get('take_amount')*100) {
            $js = <<<JS
                <script>
                alert('提现金额不一致！');
                parent.location.reload();
                </script>
                JS;
            return $js;
        }
        $merchantModel = new Merchant();
        $merchant = $merchantModel->where('id', $cash->merchant_id)->first();
        $tMerchantModel = new TelegramMerchant();
        $tMerchant = $tMerchantModel->where('customer_id', $cash->merchant_id)
            ->where('type', 2)->first();


        if(!empty($tMerchant)) {
            $bookkeepingModel = new TelegramBookkeeping();

            $bookkeepingModel->chat_id = $tMerchant->chat_id;
            $bookkeepingModel->customer_id = $tMerchant->customer_id;
            $bookkeepingModel->type = $tMerchant->type;
            $bookkeepingModel->genre = 1;
            $bookkeepingModel->amount = $cash->amount;
            $bookkeepingModel->name = '提现';

            $bookkeepingModel->note = '用户提现';
            $bookkeepingModel->created = time();
            $bookkeepingModel->save();
        }

        $start_time = date('Y-m-d');
        $balance = $merchant->advance($merchant->id, $start_time, $start_time);





        $advanceModel = new MerchantAdvance();
        $advanceModel->merchant_id = $merchant->id;
        $advanceModel->amount = abs($cash->amount);
        $advanceModel->user_id = 1;
        $type = 1;
        if(strpos($cash->amount,'+') !== false) {
            $type = 1;
        } elseif(strpos($cash->amount,'-') !== false) {
            $type = 2;
        }
        $advanceModel->type = $type;
        $advanceModel->recharge_time = time();
        $advanceModel->balance = ($merchant->balance + $cash->amount);

        $advanceModel->created = time();
        $advanceModel->save();




        $orderLogModel = new OrderLog();
        $orderLogModel->merchant_id = $cash->merchant_id;
        $orderLogModel->attribute = 2;
        $orderLogModel->type = $type;
        $orderLogModel->amount = $cash->amount;
        $orderLogModel->before_balance = $balance - $cash->amount;
        $orderLogModel->balance = $balance;
        $orderLogModel->note = '用户提现';
        $orderLogModel->created = time();
        $orderLogModel->save();
        $cashModel->where('id', $id)->update([
            'take_amount' => $request->get('take_amount')*100,
            'status' => 1,
            'allow_time' => time()
        ]);
        $js = <<<JS
                <script>
                alert('批复成功！');
                parent.location.reload();
                </script>
                JS;
        return $js;
    }

}
