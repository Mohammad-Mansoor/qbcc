<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FinishingPaymentAllocation extends Model
{
    protected $guarded = [];

    public function payment()
    {
        return $this->belongsTo(FinishingTeamPayment::class, 'finishing_team_payment_id');
    }

    public function allocatable()
    {
        return $this->morphTo();
    }
}
