<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SellerPaymentAllocation extends Model
{
    protected $guarded = [];

    public function seller_payment()
    {
        return $this->belongsTo(SellerPayment::class, 'seller_payment_id');
    }

    public function purchase_bill()
    {
        return $this->belongsTo(RawMaterialPurchaseBill::class, 'raw_material_purchase_bill_id');
    }
}
