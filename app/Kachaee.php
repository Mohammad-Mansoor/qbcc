<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
class Kachaee extends Model 
{
   
    protected $guarded = [];
    public function carpet()
    {
        return $this->hasOne(Carpet::class,'kachee_id','id');
    }
    public function repair(){
        return $this->hasOne(CarpetRepair::class,'team_id','id');
    }

    public function payment(){
        return $this->hasMany(KachaeePayment::class,'team_id','id');
    }

    public function getTotalUsdBalanceAttribute()
    {
        $received = $this->payment()->where('type', 'رسید')->sum('amount');
        $sent = $this->payment()->where('type', 'گرفت')->sum('amount');
        return $received - $sent;
    }

    public function getTotalAfBalanceAttribute()
    {
        $received = $this->payment()->where('type', 'رسید')->sum('amount_af');
        $sent = $this->payment()->where('type', 'گرفت')->sum('amount_af');
        return $received - $sent;
    }
}
