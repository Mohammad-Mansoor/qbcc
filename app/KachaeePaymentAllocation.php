<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class KachaeePaymentAllocation extends Model
{
    protected $guarded = [];

    public function payment()
    {
        return $this->belongsTo(KachaeePayment::class, 'kachaee_payment_id');
    }

    public function allocatable()
    {
        return $this->morphTo();
    }
}
