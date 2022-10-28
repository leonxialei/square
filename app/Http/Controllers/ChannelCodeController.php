<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\ChannelCode;


class ChannelCodeController extends Controller
{
    public function index(Request $request) {
        $codeModel = new ChannelCode();
        if(!empty($request->get('name'))) {
            $codeModel = $codeModel->where('name', $request->get('name'));
        }
        if(!empty($request->get('code'))) {
            $codeModel = $codeModel->where('code', $request->get('code'));
        }
        if($request->get('status') != '') {
            $codeModel = $codeModel->where('status', $request->get('status'));
        }
        $codeModel = $codeModel;
        $code = $codeModel->orderBy('id', 'DESC')->paginate(15);
        $code = $code->appends($request->all());
        return View('channel/code/index', ['code' => $code
        , 'request' => $request]);
    }

    public function create() {
        return View('channel/code/create');
    }

    public function store(Request $request) {
        $codeModel = new ChannelCode();
        $code = $codeModel->where('code', $request->get('code'))->first();
        if(!empty($code)) {
            $js = <<<JS
            <script>
            alert('编码重复！');
            history.go(-1);
            </script>
            JS;
            return $js;
        }
        $codeModel->name = $request->get('name');
        $codeModel->code = $request->get('code');
        $codeModel->status = $request->get('status');
        if($codeModel->save()) {
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
        $codeModel = new ChannelCode();
        $code = $codeModel->where('id', $id)->first();
        return View('channel/code/edit', ['code' => $code]);
    }

    public function update(Request $request, $id) {
        $codeModel = new ChannelCode();
        $code = $codeModel->where('id', $id)->first();
        if(!empty($code)) {
            $codeModel->where('id', $id)->update(
                [
                    'name' => $request->get('name'),
                    'code' => $request->get('code'),
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


}
