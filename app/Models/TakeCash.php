<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class TakeCash extends Model
{
    protected $table = 'take_cash';
    public $timestamps = false;


    public function merchant() {
        return $this->hasOne('App\Models\Merchant', 'id', 'merchant_id');
    }
}
