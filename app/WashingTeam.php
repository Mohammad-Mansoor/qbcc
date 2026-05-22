<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WashingTeam extends Model
{
   
    protected $guarded = [];


    
    public function carpet_wash(){
        return $this->hasMany(CarpetWash::class,'team_id');
    }
    
    public function carpet(){
        return $this->hasMany(Carpet::class,'washing_id');
    }

    public function payment(){
        return $this->hasMany(WashingPayment::class,'team_id','id');
    }

    public function getNormalizedBalanceAttribute()
    {
        $receipts = $this->payment()->where('type', 'رسید')->sum('base_amount');
        $payouts = $this->payment()->where('type', 'گرفت')->sum('base_amount');
        return $receipts - $payouts;
    }

    public function getUsdBalanceAttribute()
    {
        $receipts = $this->payment()->where('type', 'رسید')->sum('amount');
        $payouts = $this->payment()->where('type', 'گرفت')->sum('amount');
        return $receipts - $payouts;
    }

    public function getAfBalanceAttribute()
    {
        $receipts = $this->payment()->where('type', 'رسید')->sum('amount_af');
        $payouts = $this->payment()->where('type', 'گرفت')->sum('amount_af');
        return $receipts - $payouts;
    }
}
