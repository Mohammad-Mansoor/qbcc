<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChartOfAccount extends Model
{
    protected $fillable = [
        'account_code', 
        'account_name', 
        'account_type', 
        'report_group', 
        'currency', 
        'is_cash_account', 
        'normal_balance'
    ];

    public function entries()
    {
        return $this->hasMany(LedgerEntry::class, 'account_id');
    }
}
