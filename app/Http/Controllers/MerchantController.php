<?php

namespace App\Http\Controllers;

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

}
