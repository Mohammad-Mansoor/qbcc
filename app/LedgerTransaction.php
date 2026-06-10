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

        static::creating(function ($model) {
            if (empty($model->journal_id)) {
                $year = date('Y', strtotime($model->date ?? 'now'));
                $prefix = "JV-{$year}-";
                
                // Get the maximum sequential number for this prefix
                $lastTransactions = self::where('journal_id', 'LIKE', "{$prefix}%")
                    ->lockForUpdate()
                    ->get();
                
                $maxNum = 0;
                foreach ($lastTransactions as $t) {
                    $numPart = str_replace($prefix, '', $t->journal_id);
                    if (is_numeric($numPart)) {
                        $maxNum = max($maxNum, intval($numPart));
                    }
                }
                
                $nextNum = $maxNum + 1;
                $model->journal_id = $prefix . sprintf('%05d', $nextNum);
            }
        });

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
