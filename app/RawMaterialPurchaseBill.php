<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RawMaterialPurchaseBill extends Model
{
    protected $guarded = [];

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
}
