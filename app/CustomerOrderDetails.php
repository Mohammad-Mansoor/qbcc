<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomerOrderDetails extends Model
{
    protected $primaryKey = 'cod_id';
    protected  $guarded = [];

    public function order()
    {
        return $this->belongsTo(CustomerOrder::class, 'customer_order_id', 'co_id');
    }

    public function carpet()
    {
        return $this->belongsTo(Carpet::class, 'carpet_id', 'carpet_id');
    }
}
