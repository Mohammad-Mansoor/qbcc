<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class SellerPayment extends Model
{

    protected $guarded = [];
    public function seller(){
        return $this->belongsTo(StringSeller::class,'seller_id','id');
    }

    public function allocations()
    {
        return $this->hasMany(SellerPaymentAllocation::class, 'seller_payment_id');
    }

    public function debitAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'override_debit_account_id');
    }

    public function creditAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'override_credit_account_id');
    }
}
