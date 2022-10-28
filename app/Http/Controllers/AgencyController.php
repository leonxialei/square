<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Merchant;



class AgencyController extends Controller
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
        $MerchantModel = new Merchant();
        $MerchantModel = $MerchantModel->where('agency_id', '!=', 0)
            ->where('status', 1);
        if(!empty($request->get('merchant_id'))) {
            $MerchantModel = $MerchantModel->where('agency_id', $request->get('merchant_id'));
        }
        $agencys = $MerchantModel->get();
        $ids = [];
        foreach ($agencys as $agency) {
            $ids[] = $agency->agency_id;
        }
        $MerchantModel = new Merchant();
        $merchants = $MerchantModel->whereIn('id', $ids)->get();
        $data = [
            'agencys' => $agencys,
            'merchants' => $merchants,
            'start_date' => $start_date,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'end_date' => $end_date,
            'request' => $request
        ];


        return View('agency.index', $data);
    }




}
