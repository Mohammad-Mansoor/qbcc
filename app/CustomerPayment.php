<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
class CustomerPayment extends Model
{

    protected $guarded = [];
    public function agent()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    public function order()
    {
        return $this->belongsTo(CustomerOrder::class, 'order_id', 'co_id');
    }

    public function allocations()
    {
        return $this->hasMany(InvoicePayment::class, 'payment_id');
    }
}
