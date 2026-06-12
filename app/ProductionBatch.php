<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductionBatch extends Model
{
    protected $table = 'production_batches';

    protected $fillable = [
        'reference_number',
        'team_id',
        'type',
        'status',
    ];

    /**
     * Scope to filter by type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope to get active/open batches.
     */
    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    /**
     * Generate the next sequential batch number in series (e.g. Wash-2026-0001) for a given type.
     * Starts from 0001 when a new calendar year begins.
     *
     * @param string $type ('wash', 'kachaee', 'finish')
     * @return string
     */
    public static function generateNextNumber($type)
    {
        $currentYear = date('Y');
        
        // Map types to prefix
        $prefixes = [
            'wash' => 'Wash',
            'kachaee' => 'Kachayee',
            'finish' => 'Tayaari'
        ];
        
        $prefix = isset($prefixes[$type]) ? $prefixes[$type] : ucfirst($type);
        
        // Find latest batch for this type and year
        $latestBatch = self::where('type', $type)
            ->where('reference_number', 'like', "{$prefix}-{$currentYear}-%")
            ->orderBy('id', 'desc')
            ->first();
            
        $nextSeqNum = 1;
        if ($latestBatch) {
            // Extract the sequence suffix (the digits following the last hyphen)
            $parts = explode('-', $latestBatch->reference_number);
            $lastSeq = (int) end($parts);
            $nextSeqNum = $lastSeq + 1;
        }
        
        return sprintf("%s-%s-%04d", $prefix, $currentYear, $nextSeqNum);
    }
}
