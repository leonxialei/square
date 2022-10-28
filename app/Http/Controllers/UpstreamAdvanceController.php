<?php

namespace App\Http\Controllers;
use App\Help\Methods;
use App\Models\AdvanceLog;
use App\Models\ChannelCode;
use App\Models\Merchant;
use App\Models\MerchantChannel;
use App\Models\Order;
use App\Models\Upstream;
use App\Models\UpstreamChannel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;


class UpstreamAdvanceController extends Controller
{
    public function index(Request $request) {
        if(empty($request->get('start_date'))) {
            $start_date = date('Y-m-d');
        } else {
            $start_date = $request->get('start_date');
        }

        $end_date = $start_date;
        $upstreamModel = new Upstream();
        $upstreams = $upstreamModel->where('status', 1)->get();






        $data = [
            'request' => $request,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'upstreams' => $upstreams,
        ];

        if(Methods::is_mobile_request()) {
            return View('mobile/upstream/advance/index', $data);
        } else {
            return View('upstream/advance/index', $data);
        }



    }

    public function edit(Request $request, $id) {
        $upstreamModel = new Upstream();
        $upstream = $upstreamModel->where('id', $id)->first();
        $amount = Order::log_change($id, date('Y-m-d'), date('Y-m-d'));
        $data = [
            'upstream' => $upstream,
            'amount' => $amount
        ];
        return View('upstream/advance/edit', $data);
    }

    public function update(Request $request, $id) {
        $upstreamModel = new Upstream();
        $upstream = $upstreamModel->where('id', $id)->first();
        if(empty($upstream)) {
            $js = <<<JS
            <script>
            parent.location.reload();
            </script>
            JS;
            return $js;
        }
        if($request->get('type') == 1) {
            $balance = $upstream->balance + ($request->get('amount')*100);
        } else if($request->get('type') == 2) {
            $balance = $upstream->balance - ($request->get('amount')*100);
        }

        $upstreamModel->where('id', $id)->update([
            'balance' => $balance
        ]);



        $userInfo = session('account');
        $logModel = new AdvanceLog();
        $logModel->upstream_id = $id;
        $logModel->user_id = $userInfo['user_id'];
        $logModel->amount = $request->get('amount')*100;
        $logModel->type = $request->get('type');
        $logModel->balance = 0;
        $logModel->created = time();
        $logModel->save();

        $js = <<<JS
            <script>
            alert('修改成功！');
            parent.location.reload();
            </script>
            JS;
        return $js;
    }


}
