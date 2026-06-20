<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RawMaterialPurchaseBill extends Model
{
    protected $guarded = [];

    protected $appends = ['total_amount', 'paid_amount', 'remaining_balance'];

    public function seller()
    {
        return $this->belongsTo(StringSeller::class, 'seller_id', 'id');
    }

    public function purchases()
    {
        return $this->hasMany(PurchaseMaterial::class, 'raw_material_purchase_bill_id', 'id');
    }

    public function allocations()
    {
        return $this->hasMany(SellerPaymentAllocation::class, 'raw_material_purchase_bill_id', 'id');
    }

    public function getTotalAmountAttribute()
    {
        return $this->purchases()->sum('total') ?? 0;
    }

    public function getPaidAmountAttribute()
    {
        return $this->allocations()->sum('base_allocated_amount') ?? 0;
    }

    public function getRemainingBalanceAttribute()
    {
        return max(0, $this->total_amount - $this->paid_amount);
    }

    public function recalculatePaymentStatus()
    {
        $total = $this->total_amount;
        $paid = $this->paid_amount;
        
        if ($paid >= $total - 0.01) {
            $this->payment_status = 'paid';
        } else if ($paid <= 0.01) {
            $this->payment_status = 'unpaid';
        } else {
            $this->payment_status = 'partially_paid';
        }
        $this->save();
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
