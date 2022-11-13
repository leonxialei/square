<?php

namespace App\Http\Controllers;
use App\Models\Merchant;
use App\Models\MerchantChannel;
use App\Models\Upstream;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\ChannelCode;
use App\Models\UpstreamChannel;


class ChannelController extends Controller
{
   public function index(Request $request) {
       $codeModel = new ChannelCode();

       $upstreamModel = new Upstream();
       $upstreams = $upstreamModel->where('status', 1)->get();
       $upstreamChannelModel = new UpstreamChannel();
       if(!empty($request->get('upstream'))) {
           $upstreamChannelModel = $upstreamChannelModel->where('upstream_id', $request->get('upstream'));
       }
       if(!empty($request->get('code'))) {
           $upstreamChannelModel = $upstreamChannelModel->where('code', $request->get('code'));
       }
       $upstreamChannels = $upstreamChannelModel->where('is_disabled', 0)->paginate(15);
       $data = [
           'request' => $request,
           'code' => $request->get('code'),
           'upstreams' => $upstreams,
           'upstreamChannels' => $upstreamChannels
       ];
       return View('channel/index/index', $data);
   }

   public function create() {
       $upstreamModel = new Upstream();
       $upstreams = $upstreamModel->where('status', 1)->get();
       $codeModel = new ChannelCode();
       $codes = $codeModel->where('status', 1)->get();
       $merchantModel = new Merchant();
       $merchants = $merchantModel->where('status', 1)->get();
       $data = [
           'upstreams' => $upstreams,
           'codes' => $codes,
           'merchants' => $merchants
       ];
       return  View('channel/index/create', $data);
   }

   public function store(Request $request) {
       if(!empty($request->get('amount'))) {
           if(strpos($request->get('amount'), '，')) {
               $js = <<<JS
                <script>
                alert('金额请用英语半角的逗号！');
                parent.location.reload();
                </script>
                JS;
               return $js;
           }
       }
       $upstreamChannelModel = new UpstreamChannel();
       $upstreamChannelModel->name = $request->get('name');
       $upstreamChannelModel->upstream_id = $request->get('upstream');
       $upstreamChannelModel->upstream_code = $request->get('upstream_code');
       $upstreamChannelModel->code = $request->get('code');
       $upstreamChannelModel->rate = $request->get('rate');
       $upstreamChannelModel->is_amount = $request->get('is_amount');
       $upstreamChannelModel->amount = $request->get('amount');
       $upstreamChannelModel->status = $request->get('status');
       $upstreamChannelModel->created = time();
       $upstreamChannelModel->save();
       if(!empty($request->get('merchant'))) {
           foreach($request->get('merchant') as $merchant) {
               $merchantChannelModel = new MerchantChannel();
               $merchantChannelModel->merchant_id = $merchant;
               $merchantChannelModel->channel_id = $upstreamChannelModel->id;
               $merchantChannelModel->weight = 1;
               $merchantChannelModel->rate = 0;
               $merchantChannelModel->agent_rate = 0;
               $merchantChannelModel->is_amount = $upstreamChannelModel->is_amount;
               $merchantChannelModel->amount =$upstreamChannelModel->amount;
               $merchantChannelModel->status = 1;
               $merchantChannelModel->created = time();
               $merchantChannelModel->save();
           }
       }


       $js = <<<JS
        <script>
        alert('创建成功！');
        parent.location.reload();
        </script>
        JS;
       return $js;
   }

    public function edit(Request $request, $id) {
        $upstreamChannelModel = new UpstreamChannel();
        $channel = $upstreamChannelModel->where('id', $id)->first();
        $upstreamModel = new Upstream();
        $upstreams = $upstreamModel->where('status', 1)->get();
        $codeModel = new ChannelCode();
        $codes = $codeModel->where('status', 1)->get();
        $data = [
            'channel' => $channel,
            'upstreams' => $upstreams,
            'codes' => $codes
        ];
        return View('channel/index/edit', $data);
    }

   public function update(Request $request, $id) {
       if(!empty($request->get('amount'))) {
           if(strpos($request->get('amount'), '，')) {
               $js = <<<JS
                <script>
                alert('金额请用英语半角的逗号！');
                parent.location.reload();
                </script>
                JS;
               return $js;
           }
       }
       $upstreamChannelModel = new UpstreamChannel();
       $channel = $upstreamChannelModel->where('id', $id)->first();
       if(empty($channel)) {
           $js = <<<JS
            <script>
            parent.location.reload();
            </script>
            JS;
           return $js;
       }

       $input = [
           "name" => $request->get('name'),
           "upstream_id" => $request->get('upstream'),
           "code" => $request->get('code'),
           "upstream_code" => $request->get('upstream_code'),
           "rate" => $request->get('rate'),
           "is_amount" => $request->get('is_amount'),
           "amount" => $request->get('amount'),
           "status" => $request->get('status'),
       ];
       $upstreamChannelModel->where('id', $id)->update($input);
       $merchantChannelModel = new MerchantChannel();
       $merchantChannelModel->where('channel_id', $id)
           ->update([
               "is_amount" => $request->get('is_amount'),
               "amount" => $request->get('amount'),
           ]);
       $js = <<<JS
        <script>
        alert('修改成功！');
        parent.location.reload();
        </script>
        JS;
       return $js;
   }

   public function merchant($id) {
       $upstreamChannelModel = new UpstreamChannel();
       $channel = $upstreamChannelModel->where('id', $id)->first();

       $data = [
           'channel' => $channel
       ];
       return View('channel/index/merchant', $data);
   }
}
