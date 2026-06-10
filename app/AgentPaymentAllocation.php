<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AgentPaymentAllocation extends Model
{
    protected $guarded = [];

    public function agent_payment()
    {
        return $this->belongsTo(AgentPayment::class, 'agent_payment_id');
    }

    public function allocatable()
    {
        return $this->morphTo();
    }
}
