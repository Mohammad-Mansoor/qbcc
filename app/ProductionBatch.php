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

    protected $appends = ['total_amount', 'paid_amount', 'remaining_balance', 'payment_status', 'team_name'];

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

    public function allocations()
    {
        if ($this->type === 'wash') {
            return $this->morphMany(WashingPaymentAllocation::class, 'allocatable');
        } elseif ($this->type === 'finish') {
            return $this->morphMany(FinishingPaymentAllocation::class, 'allocatable');
        }
        return $this->morphMany(KachaeePaymentAllocation::class, 'allocatable');
    }

    public function getTotalAmountAttribute()
    {
        if ($this->type === 'kachaee') {
            return \DB::table('carpet_repairs')
                ->where('kachaee_number', $this->reference_number)
                ->sum('total_price') ?? 0.0;
        } elseif ($this->type === 'wash') {
            return \DB::table('carpet_washes')
                ->where('wash_number', $this->reference_number)
                ->sum('total_price') ?? 0.0;
        } elseif ($this->type === 'finish') {
            return \DB::table('finishing_works')
                ->where('finish_number', $this->reference_number)
                ->sum('price') ?? 0.0;
        }
        return 0.0;
    }

    public function getPaidAmountAttribute()
    {
        // Direct payments (legacy/direct entries where kachaee_number is stored directly on the payment)
        $directPaid = 0.0;
        if ($this->type === 'kachaee') {
            $directPaid = \DB::table('kachaee_payments')
                ->where('kachaee_number', $this->reference_number)
                ->where('status', 1)
                ->sum(\DB::raw("CASE WHEN type = 'گرفت' THEN original_amount ELSE -original_amount END")) ?? 0.0;
        } elseif ($this->type === 'wash') {
            $directPaid = \DB::table('washing_payments')
                ->where('wash_number', $this->reference_number)
                ->where('status', 1)
                ->sum(\DB::raw("CASE WHEN type = 'گرفت' THEN original_amount ELSE -original_amount END")) ?? 0.0;
        } elseif ($this->type === 'finish') {
            $directPaid = \DB::table('finishing_team_payments')
                ->where('finish_number', $this->reference_number)
                ->where('status', 1)
                ->sum(\DB::raw("CASE WHEN type = 'گرفت' THEN original_amount ELSE -original_amount END")) ?? 0.0;
        }

        // Plus allocated advance payments
        $allocatedPaid = $this->allocations()->sum('allocated_amount') ?? 0.0;

        return (float)($directPaid + $allocatedPaid);
    }

    public function getRemainingBalanceAttribute()
    {
        return max(0.0, $this->total_amount - $this->paid_amount);
    }

    public function getPaymentStatusAttribute()
    {
        $total = $this->total_amount;
        $paid = $this->paid_amount;
        
        if ($paid >= $total - 0.01) {
            return 'paid';
        }
        if ($paid <= 0.01) {
            return 'unpaid';
        }
        return 'partially_paid';
    }

    public function getTeamNameAttribute()
    {
        if (!$this->team_id) {
            return null;
        }
        
        if ($this->type === 'kachaee') {
            $team = \App\Kachaee::find($this->team_id);
            return $team ? $team->name : null;
        } elseif ($this->type === 'wash') {
            $team = \App\WashingTeam::find($this->team_id);
            return $team ? $team->name : null;
        } elseif ($this->type === 'finish') {
            $team = \App\FinishingTeam::find($this->team_id);
            return $team ? $team->name : null;
        }
        
        return null;
    }
}
