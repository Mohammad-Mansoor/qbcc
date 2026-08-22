<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
class KachaeePayment extends Model
{
   
    protected $guarded = [];
    public function kachaee(){
        return $this->belongsTo(Kachaee::class,'team_id','id');
    }

    public function allocations()
    {
        return $this->hasMany(KachaeePaymentAllocation::class, 'kachaee_payment_id');
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
