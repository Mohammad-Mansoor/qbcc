<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
class CustomerPayment extends Model 
{
   
    protected $guarded = [];
    public function agent(){
        return $this->belongsTo(Customer::class,'customer_id','id');
    }
}
