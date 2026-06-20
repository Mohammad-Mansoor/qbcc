<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WashingPaymentAllocation extends Model
{
    protected $guarded = [];

    public function payment()
    {
        return $this->belongsTo(WashingPayment::class, 'washing_payment_id');
    }

    public function allocatable()
    {
        return $this->morphTo();
    }
}
