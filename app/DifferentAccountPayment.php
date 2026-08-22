<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DifferentAccountPayment extends Model
{
    protected $guarded = [];
    public function account(){
        return $this->belongsTo(DifferentAccount::class,'account_id','id');
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
