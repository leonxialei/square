<?php

namespace App\Http\Controllers;
use App\Help\Methods;
use App\Models\ChannelCode;
use App\Models\Merchant;
use App\Models\MerchantChannel;
use App\Models\Upstream;
use App\Models\UpstreamChannel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;


class UpstreamController extends Controller
{
    public function index(Request $request){
        $merchantChannelModel = new MerchantChannel();
        $codeModel = new ChannelCode();
        if(!empty($request->get('merchant_id'))) {
            $merchantChannelModel = $merchantChannelModel->where('merchant_id', $request->get('merchant_id'));
        }
        if(!empty($request->get('channel_id'))) {
            $merchantChannelModel = $merchantChannelModel->where('channel_id', $request->get('channel_id'));
        }
        if(!empty($request->get('upstream'))) {
            $upChannelModel = new UpstreamChannel();
            $upChannels = $upChannelModel->select('id')->where('upstream_id', $request->get('upstream'))
                ->get();
            $upIds = [];
            foreach ($upChannels as $upChannel) {
                $upIds[] = $upChannel->id;
            }
            $merchantChannelModel = $merchantChannelModel->whereIn('channel_id', $upIds);
        }
        if(!empty($request->get('code'))) {
            $code = $codeModel->where('code', $request->get('code'))->first();
            $channelId = [];

            foreach($code->upstreamChannel as $val) {
                $channelId[] = $val->id;
            }
            if(!empty($channelId)) {
                $merchantChannelModel = $merchantChannelModel->whereIn('channel_id', $channelId);
            } else {
                $merchantChannelModel = $merchantChannelModel->where('channel_id', 'aaa');
            }

        }
        if($request->get('status') != '') {
            $merchantChannelModel = $merchantChannelModel->where('status', $request->get('status'));
        }
        $merchantChannels = $merchantChannelModel->where('is_disabled', 0)->orderBy('id', 'DESC')->paginate(15);

        $merchantModel = new Merchant();
        $merchants = $merchantModel->where('status', 1)->get();
        $upstreamModel = new Upstream();
        $upstreams = $upstreamModel->where('status', 1)->orderBy('id', 'DESC')->get();

        $codes = $codeModel->where('status', 1)->get();
        $data = [
            'request' => $request,
            'merchantChannels' => $merchantChannels,
            'merchants' => $merchants,
            'upstreams' => $upstreams,
            'codes' => $codes
        ];
        if(Methods::is_mobile_request()) {
            return View('mobile/upstream/index', $data);
        } else {
            return View('upstream/index', $data);
        }

    }

    public function create(Request $request){
        $merchantModel = new Merchant();
        $merchants = $merchantModel->where('status', 1)->get();
        $channelModel = new UpstreamChannel();
        $channels = $channelModel->where('is_disabled', 0)->where('status', 1)->get();
        $data = [
            'merchants' => $merchants,
            'channels' => $channels
        ];
        return View('upstream/create', $data);
    }

    public function store(Request $request){
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
        $upstreamChannel = $upstreamChannelModel->where('id', $request->get('channel_id'))
            ->first();
        if(empty($upstreamChannel)) {
            $js = <<<JS
                <script>
                alert('无此通道！');
                parent.location.reload();
                </script>
                JS;
            return $js;
        }
        $merchantChannelModel = new MerchantChannel();
        $merchantChannelModel->merchant_id = $request->get('merchant_id');
        $merchantChannelModel->channel_id = $request->get('channel_id');
        $merchantChannelModel->weight = $request->get('weight');
        $merchantChannelModel->rate = $request->get('rate');
        $merchantChannelModel->agent_rate = $request->get('agent_rate');
        if(!empty($upstreamChannel->is_amount)) {
            $merchantChannelModel->is_amount = $upstreamChannel->is_amount;
        } else {
            $merchantChannelModel->is_amount = 0;
        }
        $merchantChannelModel->amount =$upstreamChannel->amount;
        $merchantChannelModel->status = $request->get('status');
        $merchantChannelModel->created = time();
        if($merchantChannelModel->save()) {
            $js = <<<JS
            <script>
            alert('创建成功！');
            parent.location.reload();
            </script>
            JS;
            return $js;
        }
    }

    public function edit($id) {
        $merchantModel = new Merchant();
        $merchants = $merchantModel->where('status', 1)->get();
        $channelModel = new UpstreamChannel();
        $channels = $channelModel->where('is_disabled', 0)->where('status', 1)->get();
        $merchantChannelModel = new MerchantChannel();
        $merchantChannel = $merchantChannelModel->where('id', $id)->first();
        $data = [
            'merchants' => $merchants,
            'channels' => $channels,
            'merchantChannel' => $merchantChannel
        ];
        return View('upstream/edit', $data);
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
        $merchantChannelModel = new MerchantChannel();
        $merchantChannel = $merchantChannelModel->where('id', $id)->first();
        if(empty($merchantChannel)) {
            $js = <<<JS
            <script>
            parent.location.reload();
            </script>
            JS;
            return $js;
        }
        $input = [
            'merchant_id' => $request->get('merchant_id'),
            'channel_id' => $request->get('channel_id'),
            'weight' => $request->get('weight'),
            'rate' => $request->get('rate'),
            'agent_rate' => $request->get('agent_rate'),
//            'is_amount' => $request->get('is_amount'),
//            'amount' => $request->get('amount'),
            'status' => $request->get('status')
        ];
        $merchantChannelModel->where('id', $id)->update($input);
        $js = <<<JS
            <script>
            alert('更改成功！');
            parent.location.reload();
            </script>
            JS;
        return $js;
    }

    public function list(Request $request) {
        $upstreamModel = new Upstream();
        if(!empty($request->get('name'))) {
            $upstreamModel = $upstreamModel->where('name', 'like', '%'.$request->get('name').'%');
        }
        $upstreams = $upstreamModel
            ->where('status', 1)
            ->orderBy('id', 'DESC')->paginate(15);
        $data = [
            'upstreams' => $upstreams,
            'name' => $request->get('name')
        ];
        return View('upstream/list', $data);
    }


}
