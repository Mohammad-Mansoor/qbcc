<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
class Invoice extends Model
{
   
    protected $guarded = [];

    public function customer(){
        return $this->belongsTo(Customer::class,'customer_id');
    }

    public function sale(){
        return $this->hasMany(Sale::class,'invoice_id');
    }

    public function payments()
    {
        return $this->hasMany(InvoicePayment::class, 'invoice_id');
    }
}
