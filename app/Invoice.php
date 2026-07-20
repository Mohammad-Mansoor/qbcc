<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
class Invoice extends Model
{

    protected $guarded = [];
    protected $appends = ['total_amount', 'paid_amount', 'remaining_balance', 'payment_status'];

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
            // FORENSIC RULE: Calculate Base USD dynamically since sales table lacks base_amount column
            return $this->sale->where('is_returned', 0)->sum(function($sale) {
                if ($sale->currency_code === 'USD' || !$sale->exchange_rate) {
                    return (float)$sale->sale_cost_total;
                }
                return (float)bcmul((string)$sale->sale_cost_total, (string)$sale->exchange_rate, 4);
            });
        } else {
            // FORENSIC RULE: Use the base_currency_amount from material_sales table, fallback to bcmul if missing for legacy
            return $this->material_sales->sum(function($sale) {
                if ($sale->base_currency_amount && $sale->base_currency_amount > 0) {
                    return (float)$sale->base_currency_amount;
                }
                
                $rate = $sale->exchange_rate ?? 1.0;
                $code = $sale->currency_code ?? 'USD';
                $amount = $sale->original_amount ?? $sale->total_price ?? 0;
                
                if ($code === 'USD' || !$rate) return (float)$amount;
                return (float)bcmul((string)$amount, (string)$rate, 4);
            });
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

    public function getPaymentStatusAttribute($value)
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
}
