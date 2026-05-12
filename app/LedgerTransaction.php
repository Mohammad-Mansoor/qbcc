<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LedgerTransaction extends Model
{
    protected $fillable = [
        'journal_id', 
        'date', 
        'reference', 
        'description', 
        'source_type', 
        'source_id', 
        'status', 
        'reversed_transaction_id', 
        'mapping_key',
        'journal_type', 
        'posted_at'
    ];

    public function entries()
    {
        return $this->hasMany(LedgerEntry::class, 'transaction_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::updating(function ($model) {
            if ($model->status === 'posted' && $model->isDirty('status') === false) {
                throw new \Exception("Posted transactions are immutable. Use reversals for corrections.");
            }
        });

        static::deleting(function ($model) {
            if ($model->status === 'posted') {
                throw new \Exception("Cannot delete posted transactions. Use reversals instead.");
            }
        });
    }
}
