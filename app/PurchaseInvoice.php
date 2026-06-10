<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PurchaseInvoice extends Model
{
    protected $guarded = [];

    public function agent()
    {
        return $this->belongsTo(Agents::class, 'agent_id', 'agent_id');
    }

    public function carpets()
    {
        return $this->hasMany(Carpet::class, 'purchase_invoice_id', 'id');
    }

    public function allocations()
    {
        return $this->morphMany(AgentPaymentAllocation::class, 'allocatable');
    }

    public function getTotalAmountAttribute()
    {
        return $this->carpets()->sum('total_price') ?? 0;
    }

    public function getPaidAmountAttribute()
    {
        return $this->allocations()->sum('base_allocated_amount') ?? 0;
    }

    public function getRemainingBalanceAttribute()
    {
        return max(0, $this->total_amount - $this->paid_amount);
    }
}
