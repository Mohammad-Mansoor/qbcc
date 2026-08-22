<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PurchaseMaterial extends Model
{
 
    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();

        static::saved(function ($purchase) {
            if ($purchase->raw_material_purchase_bill_id) {
                $bill = $purchase->purchaseBill;
                if ($bill) {
                    $bill->recalculatePaymentStatus();
                }
            }
            if ($purchase->isDirty('raw_material_purchase_bill_id')) {
                $originalId = $purchase->getOriginal('raw_material_purchase_bill_id');
                if ($originalId) {
                    $originalBill = \App\RawMaterialPurchaseBill::find($originalId);
                    if ($originalBill) {
                        $originalBill->recalculatePaymentStatus();
                    }
                }
            }
        });

        static::deleted(function ($purchase) {
            if ($purchase->raw_material_purchase_bill_id) {
                $bill = $purchase->purchaseBill;
                if ($bill) {
                    $bill->recalculatePaymentStatus();
                }
            }
        });
    }

    public function materialType() {
        return $this->belongsTo(MaterialType::class , 'material_type' , 'material_type_id');
    }

    public function materialCategory() {
        return $this->belongsTo(MaterialCategory::class , 'material_category' , 'material_category_id');
    }

    public function seller() {
        return $this->belongsTo(StringSeller::class , 'seller_id' , 'id');
    }

    public function warehouse() {
        return $this->belongsTo(\App\Warehouse::class, 'warehouse_id', 'id');
    }

    public function purchaseBill() {
        return $this->belongsTo(\App\RawMaterialPurchaseBill::class, 'raw_material_purchase_bill_id', 'id');
    }

    public function debitAccount() {
        return $this->belongsTo(ChartOfAccount::class, 'override_debit_account_id');
    }

    public function creditAccount() {
        return $this->belongsTo(ChartOfAccount::class, 'override_credit_account_id');
    }
}
