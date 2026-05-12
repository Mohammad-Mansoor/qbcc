<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomerOrder extends Model
{
    protected $primaryKey = 'co_id';
    protected  $guarded = [];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function details()
    {
        return $this->hasMany(CustomerOrderDetails::class, 'customer_order_id', 'co_id');
    }
}
