<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
class KachaeePayment extends Model
{
   
    protected $guarded = [];
    public function kachaee(){
        return $this->belongsTo(Kachaee::class,'team_id','id');
    }

    public function allocations()
    {
        return $this->hasMany(KachaeePaymentAllocation::class, 'kachaee_payment_id');
    }
}
