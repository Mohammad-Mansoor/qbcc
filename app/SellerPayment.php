<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class SellerPayment extends Model
{

    protected $guarded = [];
    public function seller(){
        return $this->belongsTo(StringSeller::class,'seller_id','id');
    }

    public function allocations()
    {
        return $this->hasMany(SellerPaymentAllocation::class, 'seller_payment_id');
    }
}
