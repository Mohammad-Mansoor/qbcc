<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
class Invoice extends Model
{

    protected $guarded = [];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function agent()
    {
        return $this->belongsTo(Agents::class, 'agent_id', 'agent_id');
    }

    public function sale()
    {
        return $this->hasMany(Sale::class, 'invoice_id');
    }

    public function material_sales()
    {
        return $this->hasMany(MaterialSale::class, 'invoice_id');
    }

    public function payments()
    {
        return $this->hasMany(InvoicePayment::class, 'invoice_id');
    }

    public function allocations()
    {
        return $this->morphMany(AgentPaymentAllocation::class, 'allocatable');
    }

    public function getTotalAmountAttribute()
    {
        if ($this->type === 'carpet') {
            return $this->sale()->sum('sale_cost_total') ?? 0;
        } else {
            return $this->material_sales()->sum('total_price') ?? 0;
        }
    }

    public function getPaidAmountAttribute()
    {
        if ($this->type === 'carpet') {
            return $this->payments()->sum('amount_applied') ?? 0;
        }
        return $this->allocations()->sum('base_allocated_amount') ?? 0;
    }

    public function getRemainingBalanceAttribute()
    {
        return max(0, $this->total_amount - $this->paid_amount);
    }

    public static function generateNextInvoiceNo($year = null)
    {
        if (!$year) {
            $year = date('Y');
        }
        $prefix = 'SI-' . $year . '-';

        $latest = self::where('invoice_no', 'like', $prefix . '%')
            ->orderBy('invoice_no', 'desc')
            ->first();

        if ($latest) {
            $parts = explode('-', $latest->invoice_no);
            $lastNum = (int) end($parts);
            $nextNum = $lastNum + 1;
        } else {
            $nextNum = 1;
        }

        return $prefix . str_pad($nextNum, 4, '0', STR_PAD_LEFT);
    }
}
