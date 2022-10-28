<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Upstream;
use Illuminate\Http\Request;


class UpstreamController extends Controller
{
    public function collection(Request $request) {
        if(!empty($request->get('id'))) {
            $upstreamModel = new Upstream();
            $upstream = $upstreamModel->where('id', $request->get('id'))->first();
            if(!empty($upstream)) {
                $upstreamModel->where('id', $request->get('id'))->update([
                    'collection' => $request->get('type'),
                ]);
                return [
                    'code' => 200,
                    'result' => true,
                    'msg' => '更新成功！'
                ];
            }
        }
    }

    public function update(Request $request, $id) {
        $upstreamModel = new Upstream();
        $upstream = $upstreamModel->where('id', $id)->first();
        if(!empty($upstream)) {
            $upstreamModel->where('id', $id)->update([
                'status' => 0,
            ]);
            return [
                'code' => 200,
                'result' => true,
                'msg' => '更新成功！'
            ];
        }
    }
}
