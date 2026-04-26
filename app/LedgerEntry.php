<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LedgerEntry extends Model
{
    protected $fillable = [
        'transaction_id', 
        'account_id', 
        'debit', 
        'credit', 
        'exchange_rate', 
        'base_currency_amount', 
        'party_type', 
        'party_id', 
        'cost_center_id'
    ];

    public function transaction()
    {
        return $this->belongsTo(LedgerTransaction::class, 'transaction_id');
    }

    public function account()
    {
        return $this->belongsTo(ChartOfAccount::class, 'account_id');
    }

    public function party()
    {
        return $this->morphTo();
    }

    public function costCenter()
    {
        return $this->belongsTo(CostCenter::class, 'cost_center_id');
    }
}
