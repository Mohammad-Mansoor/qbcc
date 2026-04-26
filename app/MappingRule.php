<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MappingRule extends Model
{
    protected $guarded = [];

    public function debitAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'debit_account_id');
    }

    public function creditAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'credit_account_id');
    }
}
