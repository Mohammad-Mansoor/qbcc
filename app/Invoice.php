<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
class Invoice extends Model
{
   
    protected $guarded = [];

    public function customer(){
        return $this->belongsTo(Customer::class,'customer_id');
    }

    public function agent(){
        return $this->belongsTo(Agents::class,'agent_id','agent_id');
    }

    public function sale(){
        return $this->hasMany(Sale::class,'invoice_id');
    }

    public function material_sales()
    {
        return $this->hasMany(MaterialSale::class,'invoice_id');
    }

    public function payments()
    {
        return $this->hasMany(InvoicePayment::class, 'invoice_id');
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
