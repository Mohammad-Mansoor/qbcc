<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AjnasAccountDetails extends Model
{
    protected $primaryKey = 'aad_id';
    protected  $guarded = [];

    public function debitAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'override_debit_account_id');
    }

    public function creditAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'override_credit_account_id');
    }
}
