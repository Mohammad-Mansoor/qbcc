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
        'currency_code',
        'original_amount',
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

    protected static function boot()
    {
        parent::boot();

        static::updating(function ($model) {
            if ($model->transaction->status === 'posted') {
                throw new \Exception("Linked ledger entries are immutable. Use reversals for corrections.");
            }
        });

        static::deleting(function ($model) {
            if ($model->transaction->status === 'posted') {
                throw new \Exception("Cannot delete ledger entries of a posted transaction.");
            }
        });
    }
}
