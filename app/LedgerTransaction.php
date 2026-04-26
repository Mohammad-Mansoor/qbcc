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
        'journal_type', 
        'posted_at'
    ];

    public function entries()
    {
        return $this->hasMany(LedgerEntry::class, 'transaction_id');
    }
}
