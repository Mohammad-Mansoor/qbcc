<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PurchaseInvoice extends Model
{
    protected $guarded = [];
    protected $appends = ['total_amount', 'paid_amount', 'remaining_balance', 'payment_status'];

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
        // Use static original purchase price instead of dynamic total_price
        return $this->carpets()->sum('carpet_price_us') ?? 0;
    }

    public function getPaidAmountAttribute()
    {
        return $this->allocations()->sum('base_allocated_amount') ?? 0;
    }

    public function getRemainingBalanceAttribute()
    {
        return max(0, $this->total_amount - $this->paid_amount);
    }

    public function getPaymentStatusAttribute($value)
    {
        $total = $this->total_amount;
        $paid = $this->paid_amount;
        
        if ($paid >= $total - 0.01) {
            return 'paid';
        }
        if ($paid <= 0.01) {
            return 'unpaid';
        }
        return 'partially_paid';
    }
}
